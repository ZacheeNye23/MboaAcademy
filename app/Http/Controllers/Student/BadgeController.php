<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserStreak;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BadgeController extends Controller
{
    // ── Page principale badges ──────────────────────────────────────────────
    public function index(): View
    {
        $user = Auth::user();

        // Tous les badges disponibles
        $allBadges = Badge::orderBy('type')->get();

        // Badges obtenus par l'utilisateur (keyBy badge_id)
        $earnedBadges = UserBadge::with('badge')
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('badge_id');

        // Streak
        $streak = UserStreak::firstOrCreate(
            ['user_id' => $user->id],
            ['current_streak' => 0, 'longest_streak' => 0]
        );

        // Stats pour le calcul de progression
        $stats = [
            'total_enrolled'    => Enrollment::where('user_id', $user->id)->count(),
            'completed_courses' => Enrollment::where('user_id', $user->id)->whereNotNull('completed_at')->count(),
            'lessons_completed' => LessonProgress::where('user_id', $user->id)->where('is_completed', true)->count(),
            'avg_quiz_score'    => (int) QuizAttempt::where('user_id', $user->id)->avg('score'),
        ];

        // Mini leaderboard (top 5)
        $leaderboard = $this->buildLeaderboard()->take(5);

        return view('student.badges.index', compact(
            'allBadges', 'earnedBadges', 'streak', 'stats', 'leaderboard'
        ));
    }

    // ── Page classement complet ─────────────────────────────────────────────
    public function leaderboard(): View
    {
        $fullLeaderboard = $this->buildLeaderboard();

        return view('student.badges.leaderboard', compact('fullLeaderboard'));
    }

    // ── Construction du leaderboard ─────────────────────────────────────────
    private function buildLeaderboard()
    {
        return User::where('role', 'student')
            ->where('is_active', true)
            ->withCount('badges as badge_count')
            ->orderByDesc('badge_count')
            ->take(50)
            ->get()
            ->map(function ($user) {
                $recentBadges = UserBadge::with('badge')
                    ->where('user_id', $user->id)
                    ->latest('earned_at')
                    ->take(3)
                    ->get()
                    ->pluck('badge.icon')
                    ->toArray();

                return [
                    'user_id'       => $user->id,
                    'name'          => $user->full_name,
                    'initials'      => $user->initials,
                    'badges'        => $user->badge_count,
                    'xp'            => $user->badge_count * 150,
                    'recent_badges' => $recentBadges,
                ];
            });
    }
}