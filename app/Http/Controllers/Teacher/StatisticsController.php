<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\RevenueRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(Request $request): View
    {
        $teacher   = Auth::user();
        $courseIds = Course::byTeacher($teacher->id)->pluck('id');

        // ── Filtres ───────────────────────────────────────────────────────────
        $period       = $request->get('period', '30');   // 7 | 30 | 90 | 365
        $courseFilter = $request->get('course', 'all');
        $since        = now()->subDays((int) $period);

        $filteredCourseIds = $courseFilter !== 'all'
            ? collect([$courseFilter])
            : $courseIds;

        // ── KPIs globaux ──────────────────────────────────────────────────────
        $totalEnrollments  = Enrollment::whereIn('course_id', $filteredCourseIds)->count();
        $completedCount    = Enrollment::whereIn('course_id', $filteredCourseIds)->whereNotNull('completed_at')->count();
        $completionRate    = $totalEnrollments > 0 ? round($completedCount / $totalEnrollments * 100) : 0;
        $avgProgress       = (int) Enrollment::whereIn('course_id', $filteredCourseIds)->avg('progress_percent');

        $periodEnrollments = Enrollment::whereIn('course_id', $filteredCourseIds)->where('created_at', '>=', $since)->count();
        $periodCompleted   = Enrollment::whereIn('course_id', $filteredCourseIds)->whereNotNull('completed_at')->where('completed_at', '>=', $since)->count();

        $lessonsCompleted  = LessonProgress::whereIn('lesson_id',
                \App\Models\Lesson::whereHas('chapter', fn($q) => $q->whereIn('course_id', $filteredCourseIds))->pluck('id')
            )->where('is_completed', true)->count();

        $quizPassRate = $this->quizPassRate($filteredCourseIds);
        $avgQuizScore = round(QuizAttempt::whereHas('quiz', fn($q) => $q->whereIn('course_id', $filteredCourseIds))->avg('score') ?? 0, 1);

        $forumPosts   = ForumThread::whereIn('course_id', $filteredCourseIds)->count()
                      + ForumReply::whereHas('thread', fn($q) => $q->whereIn('course_id', $filteredCourseIds))->count();

        // ── Évolution inscriptions (période sélectionnée, par jour/semaine) ──
        $groupBy     = $period <= 30 ? 'DATE(created_at)' : 'YEARWEEK(created_at, 1)';
        $enrollChart = Enrollment::whereIn('course_id', $filteredCourseIds)
            ->where('created_at', '>=', $since)
            ->selectRaw("$groupBy as period_key, COUNT(*) as count")
            ->groupBy('period_key')
            ->orderBy('period_key')
            ->get()
            ->map(fn($r) => [
                'label' => $period <= 30
                    ? \Carbon\Carbon::parse($r->period_key)->translatedFormat('d M')
                    : 'Sem. ' . substr($r->period_key, 4),
                'count' => $r->count,
            ]);

        // ── Taux de complétion par cours ──────────────────────────────────────
        $completionByCourse = Course::byTeacher($teacher->id)
            ->whereIn('id', $filteredCourseIds)
            ->withCount([
                'enrollments',
                'enrollments as completed_count' => fn($q) => $q->whereNotNull('completed_at'),
            ])
            ->with('chapters')
            ->get()
            ->map(fn($c) => [
                'title'       => $c->title,
                'enrollments' => $c->enrollments_count,
                'completed'   => $c->completed_count,
                'rate'        => $c->enrollments_count > 0
                    ? round($c->completed_count / $c->enrollments_count * 100) : 0,
                'avg_progress'=> (int) Enrollment::where('course_id', $c->id)->avg('progress_percent'),
                'chapters'    => $c->chapters->count(),
            ])
            ->sortByDesc('enrollments')
            ->values();

        // ── Progression moyenne par chapitre (1er cours publié) ───────────────
        $topCourse = Course::byTeacher($teacher->id)->published()
            ->whereIn('id', $filteredCourseIds)
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->with('chapters.lessons')
            ->first();

        $chapterDropoff = null;
        if ($topCourse) {
            $enrolledIds = Enrollment::where('course_id', $topCourse->id)->pluck('user_id');
            $chapterDropoff = $topCourse->chapters->map(function ($ch) use ($enrolledIds) {
                $lessonIds  = $ch->lessons->pluck('id');
                $totalUsers = $enrolledIds->count() ?: 1;
                $completed  = LessonProgress::whereIn('user_id', $enrolledIds)
                    ->whereIn('lesson_id', $lessonIds)
                    ->where('is_completed', true)
                    ->distinct('user_id')
                    ->count('user_id');
                return [
                    'title' => $ch->title,
                    'rate'  => round($completed / $totalUsers * 100),
                    'lessons' => $lessonIds->count(),
                ];
            });
        }

        // ── Engagement : activité hebdomadaire ────────────────────────────────
        $weeklyActivity = collect(range(11, 0))->map(function ($weeksAgo) use ($filteredCourseIds) {
            $start = now()->startOfWeek()->subWeeks($weeksAgo);
            $end   = $start->copy()->endOfWeek();
            $label = $start->translatedFormat('d M');

            $lessons = LessonProgress::whereIn('lesson_id',
                \App\Models\Lesson::whereHas('chapter', fn($q) => $q->whereIn('course_id', $filteredCourseIds))->pluck('id')
            )->where('is_completed', true)
             ->whereBetween('completed_at', [$start, $end])
             ->count();

            $quizzes = QuizAttempt::whereHas('quiz', fn($q) => $q->whereIn('course_id', $filteredCourseIds))
             ->whereBetween('created_at', [$start, $end])
             ->count();

            $forum = ForumThread::whereIn('course_id', $filteredCourseIds)
             ->whereBetween('created_at', [$start, $end])
             ->count()
             + ForumReply::whereHas('thread', fn($q) => $q->whereIn('course_id', $filteredCourseIds))
             ->whereBetween('created_at', [$start, $end])
             ->count();

            return compact('label', 'lessons', 'quizzes', 'forum');
        });

        // ── Heures d'activité (distribution par heure du jour) ────────────────
        $activityByHour = LessonProgress::whereIn('lesson_id',
                \App\Models\Lesson::whereHas('chapter', fn($q) => $q->whereIn('course_id', $filteredCourseIds))->pluck('id')
            )
            ->whereNotNull('completed_at')
            ->selectRaw('HOUR(completed_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $hourlyChart = collect(range(0, 23))->map(fn($h) => [
            'hour'  => $h,
            'label' => str_pad($h, 2, '0', STR_PAD_LEFT) . 'h',
            'count' => $activityByHour->get($h)?->count ?? 0,
        ]);

        // ── Top apprenants (les plus actifs) ──────────────────────────────────
        $topStudents = Enrollment::with('user')
            ->whereIn('course_id', $filteredCourseIds)
            ->orderByDesc('progress_percent')
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'user'      => $e->user,
                'progress'  => $e->progress_percent,
                'completed' => !is_null($e->completed_at),
                'course'    => $e->course->title ?? '—',
            ]);

        // ── Quiz : distribution scores ────────────────────────────────────────
        $quizScoreDistribution = collect(range(0, 9))->map(function ($i) use ($filteredCourseIds) {
            $min = $i * 10; $max = $min + 9;
            return [
                'range' => $min . '–' . $max,
                'count' => QuizAttempt::whereHas('quiz', fn($q) => $q->whereIn('course_id', $filteredCourseIds))
                    ->where('score', '>=', $min)->where('score', '<=', $max)->count(),
            ];
        });

        // ── Courses pour filtre ────────────────────────────────────────────────
        $courses = Course::byTeacher($teacher->id)->select('id', 'title')->get();

        // ── Comparaison période précédente ────────────────────────────────────
        $prevSince = now()->subDays((int) $period * 2);
        $prevEnrollments = Enrollment::whereIn('course_id', $filteredCourseIds)
            ->whereBetween('created_at', [$prevSince, $since])->count();

        $enrollGrowth = $prevEnrollments > 0
            ? round(($periodEnrollments - $prevEnrollments) / $prevEnrollments * 100, 1) : 0;

        return view('teacher.statistics.index', compact(
            'totalEnrollments', 'completedCount', 'completionRate', 'avgProgress',
            'periodEnrollments', 'periodCompleted', 'lessonsCompleted',
            'quizPassRate', 'avgQuizScore', 'forumPosts',
            'enrollChart', 'completionByCourse', 'chapterDropoff', 'topCourse',
            'weeklyActivity', 'hourlyChart', 'topStudents',
            'quizScoreDistribution',
            'courses', 'period', 'courseFilter',
            'enrollGrowth', 'prevEnrollments'
        ));
    }

    private function quizPassRate($courseIds): int
    {
        $total  = QuizAttempt::whereHas('quiz', fn($q) => $q->whereIn('course_id', $courseIds))->count();
        $passed = QuizAttempt::whereHas('quiz', fn($q) => $q->whereIn('course_id', $courseIds))->where('passed', true)->count();
        return $total > 0 ? round($passed / $total * 100) : 0;
    }
}