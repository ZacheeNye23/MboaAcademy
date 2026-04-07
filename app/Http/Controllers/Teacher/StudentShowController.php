<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentShowController extends Controller
{
    public function __invoke(Enrollment $enrollment): View
    {
        // Vérifier que ce cours appartient au formateur
        $courseIds = Course::byTeacher(Auth::id())->pluck('id');
        abort_unless($courseIds->contains($enrollment->course_id), 403);

        $enrollment->load([
            'user',
            'course.chapters' => fn($q) => $q->orderBy('order')->with([
                'lessons' => fn($q) => $q->orderBy('order')->with('resources'),
            ]),
        ]);

        $user   = $enrollment->user;
        $course = $enrollment->course;

        // ── Progression leçons ────────────────────────────────────────────────
        $allLessons = $course->chapters->flatMap->lessons;

        $lessonProgressMap = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $allLessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $completedCount = $lessonProgressMap->where('is_completed', true)->count();
        $totalLessons   = $allLessons->count();
        $progressPct    = $enrollment->progress_percent;

        // ── Stats par chapitre ────────────────────────────────────────────────
        $chapterStats = $course->chapters->map(function ($chapter) use ($lessonProgressMap) {
            $lessonIds  = $chapter->lessons->pluck('id');
            $total      = $lessonIds->count();
            $completed  = $lessonProgressMap->whereIn('lesson_id', $lessonIds)->where('is_completed', true)->count();
            $watchTime  = $lessonProgressMap->whereIn('lesson_id', $lessonIds)->sum('watch_time');

            return [
                'chapter'    => $chapter,
                'total'      => $total,
                'completed'  => $completed,
                'pct'        => $total > 0 ? round($completed / $total * 100) : 0,
                'watch_time' => $watchTime,
            ];
        });

        // ── Quiz ──────────────────────────────────────────────────────────────
        $quizAttempts = QuizAttempt::with('quiz.questions')
            ->where('user_id', $user->id)
            ->whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
            ->latest()
            ->get();

        // Résumé quiz par quiz (meilleur score, nb tentatives, statut)
        $quizSummary = $quizAttempts->groupBy('quiz_id')->map(function ($attempts) {
            $quiz = $attempts->first()->quiz;
            return [
                'quiz'         => $quiz,
                'attempts'     => $attempts->count(),
                'best_score'   => $attempts->max('score'),
                'avg_score'    => round($attempts->avg('score'), 1),
                'passed'       => $attempts->where('passed', true)->count() > 0,
                'last_attempt' => $attempts->first()->created_at,
                'all_attempts' => $attempts,
            ];
        })->values();

        // ── Activité récente (toutes actions) ─────────────────────────────────
        $recentActivity = $this->buildActivityFeed($user->id, $course->id, $lessonProgressMap, $quizAttempts);

        // ── Certificat ────────────────────────────────────────────────────────
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        // ── Temps total passé ─────────────────────────────────────────────────
        $totalWatchSeconds = $lessonProgressMap->sum('watch_time');
        $totalQuizTime     = $quizAttempts->whereNotNull('time_spent')->sum('time_spent');

        // ── Autres cours du même apprenant ────────────────────────────────────
        $otherEnrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->where('id', '!=', $enrollment->id)
            ->get();

        // ── Forum du cours ────────────────────────────────────────────────────
        $forumActivity = ForumThread::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->withCount('replies')
            ->latest()
            ->take(3)
            ->get();

        $forumRepliesCount = ForumReply::whereHas('thread', fn($q) => $q->where('course_id', $course->id))
            ->where('user_id', $user->id)
            ->count();

        return view('teacher.students.show', compact(
            'enrollment', 'user', 'course',
            'allLessons', 'lessonProgressMap', 'completedCount', 'totalLessons', 'progressPct',
            'chapterStats',
            'quizAttempts', 'quizSummary',
            'recentActivity',
            'certificate',
            'totalWatchSeconds', 'totalQuizTime',
            'otherEnrollments',
            'forumActivity', 'forumRepliesCount'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPER : fil d'activité chronologique
    // ─────────────────────────────────────────────────────────────────────────
    private function buildActivityFeed(int $userId, int $courseId, $lessonProgressMap, $quizAttempts): array
    {
        $feed = [];

        // Leçons complétées
        foreach ($lessonProgressMap->where('is_completed', true)->sortByDesc('completed_at')->take(10) as $lp) {
            $feed[] = [
                'type'   => 'lesson',
                'icon'   => '✅',
                'color'  => '#25c26e',
                'title'  => 'Leçon complétée',
                'detail' => $lp->lesson->title ?? '—',
                'time'   => $lp->completed_at,
                'meta'   => $lp->watch_time > 0 ? $this->formatTime($lp->watch_time) : null,
            ];
        }

        // Tentatives quiz
        foreach ($quizAttempts->take(10) as $attempt) {
            $feed[] = [
                'type'   => 'quiz',
                'icon'   => $attempt->passed ? '🏆' : '📝',
                'color'  => $attempt->passed ? '#e8b84b' : '#3b82f6',
                'title'  => 'Quiz ' . ($attempt->passed ? 'réussi' : 'passé') . ' — ' . $attempt->score . '%',
                'detail' => $attempt->quiz->title ?? '—',
                'time'   => $attempt->finished_at ?? $attempt->created_at,
                'meta'   => 'Tentative #' . $attempt->attempt_number,
            ];
        }

        // Forum
        $threads = ForumThread::where('course_id', $courseId)->where('user_id', $userId)->latest()->take(5)->get();
        foreach ($threads as $thread) {
            $feed[] = [
                'type'   => 'forum',
                'icon'   => '💬',
                'color'  => '#a78bfa',
                'title'  => 'Discussion créée',
                'detail' => $thread->title,
                'time'   => $thread->created_at,
                'meta'   => null,
            ];
        }

        // Inscription
        $enrollment = Enrollment::where('user_id', $userId)->where('course_id', $courseId)->first();
        if ($enrollment) {
            $feed[] = [
                'type'   => 'enrollment',
                'icon'   => '🎓',
                'color'  => '#25c26e',
                'title'  => 'Inscription au cours',
                'detail' => $enrollment->course->title ?? '—',
                'time'   => $enrollment->enrolled_at ?? $enrollment->created_at,
                'meta'   => null,
            ];
        }

        // Trier par date décroissante
        usort($feed, fn($a, $b) => ($b['time'] ?? now()) <=> ($a['time'] ?? now()));

        return array_slice($feed, 0, 12);
    }

    private function formatTime(int $seconds): string
    {
        if ($seconds < 60) return $seconds . 's';
        $m = intdiv($seconds, 60);
        $s = $seconds % 60;
        return $s > 0 ? "{$m}min {$s}s" : "{$m}min";
    }
}