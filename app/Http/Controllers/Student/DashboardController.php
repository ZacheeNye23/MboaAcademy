<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\UserBadge;
use App\Models\UserStreak;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Cours en cours
        $enrollments = Enrollment::with(['course.chapters.lessons', 'course.teacher'])
            ->where('user_id', $user->id)
            ->whereNull('completed_at')
            ->latest()
            ->take(5)
            ->get();

        // Stats globales
        $stats = [
            'total_enrolled'    => Enrollment::where('user_id', $user->id)->count(),
            'completed_courses' => Enrollment::where('user_id', $user->id)->whereNotNull('completed_at')->count(),
            'lessons_completed' => LessonProgress::where('user_id', $user->id)->where('is_completed', true)->count(),
            'badges_count'      => UserBadge::where('user_id', $user->id)->count(),
            'avg_quiz_score'    => (int) QuizAttempt::where('user_id', $user->id)->avg('score'),
        ];

        //Quiz disponibles 
        $pendingQuizzes = \App\Models\Quiz::whereHas('course.enrollments', fn($q) =>
                $q->where('user_id', $user->id))
            ->with('course')
            ->get()
            ->filter(fn($quiz) => $quiz->canAttempt($user->id))
            ->take(4);

        // Badges 
        $allBadges    = \App\Models\Badge::all();
        $earnedBadges = UserBadge::where('user_id', $user->id)->pluck('badge_id');

        //Streak
        $streak = UserStreak::firstOrCreate(
            ['user_id' => $user->id],
            ['current_streak' => 0, 'longest_streak' => 0]
        );

        //Progression globale 
        $avgProgress = (int) Enrollment::where('user_id', $user->id)->avg('progress_percent');

        //Activité récente
        $recentActivity = $this->getRecentActivity($user->id);

        //Cours recommandés
        $enrolledIds = Enrollment::where('user_id', $user->id)->pluck('course_id');
        $recommended = \App\Models\Course::published()
            ->whereNotIn('id', $enrolledIds)
            ->with('teacher')
            ->inRandomOrder()
            ->take(3)
            ->get();

        //Certificats
        $certificates = \App\Models\Certificate::with('course')
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->take(3)
            ->get();

        return view('student.dashboard', compact(
            'user', 'enrollments', 'stats', 'pendingQuizzes',
            'allBadges', 'earnedBadges', 'streak', 'avgProgress',
            'recentActivity', 'recommended', 'certificates'
        ));
    }

    private function getRecentActivity(int $userId): array
    {
        $activities = [];

        LessonProgress::with('lesson')->where('user_id', $userId)
            ->where('is_completed', true)->latest('completed_at')->take(5)->get()
            ->each(fn($p) => $activities[] = [
                'icon' => '✅', 'action' => 'Leçon complétée',
                'detail' => $p->lesson->title ?? '—',
                'time'   => $p->completed_at,   'color' => '#25c26e',
            ]);

        QuizAttempt::with('quiz')->where('user_id', $userId)->latest()->take(3)->get()
            ->each(fn($a) => $activities[] = [
                'icon' => '📝', 'action' => 'Quiz — Score : ' . $a->score . '%',
                'detail' => $a->quiz->title ?? '—',
                'time'   => $a->created_at,     'color' => '#3b82f6',
            ]);

        UserBadge::with('badge')->where('user_id', $userId)->latest('earned_at')->take(2)->get()
            ->each(fn($ub) => $activities[] = [
                'icon' => '🏆', 'action' => 'Badge obtenu',
                'detail' => $ub->badge->name ?? '—',
                'time'   => $ub->earned_at,     'color' => '#e8b84b',
            ]);

        usort($activities, fn($a, $b) => $b['time'] <=> $a['time']);
        return array_slice($activities, 0, 8);
    }
}