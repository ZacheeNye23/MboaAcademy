<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\RevenueRecord;
use App\Models\User;
use App\Models\CourseReview;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $period    = (int) $request->get('period', 30);
        $startDate = now()->subDays($period);
        $prevStart = now()->subDays($period * 2);
        $prevEnd   = now()->subDays($period);

        // ── KPIs ────────────────────────────────────────────────────────────
        $newUsers       = User::where('created_at', '>=', $startDate)->count();
        $prevUsers      = User::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $usersGrowth    = $prevUsers > 0 ? round(($newUsers - $prevUsers) / $prevUsers * 100, 1) : 0;

        $newEnrollments     = Enrollment::where('created_at', '>=', $startDate)->count();
        $prevEnrollments    = Enrollment::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $enrollmentsGrowth  = $prevEnrollments > 0 ? round(($newEnrollments - $prevEnrollments) / $prevEnrollments * 100, 1) : 0;

        $revenue        = (int) RevenueRecord::where('created_at', '>=', $startDate)->completed()->sum('net_amount');
        $prevRevenue    = (int) RevenueRecord::whereBetween('created_at', [$prevStart, $prevEnd])->completed()->sum('net_amount');
        $revenueGrowth  = $prevRevenue > 0 ? round(($revenue - $prevRevenue) / $prevRevenue * 100, 1) : 0;

        $totalStarted   = Enrollment::where('created_at', '>=', $startDate)->count();
        $totalCompleted = Enrollment::where('created_at', '>=', $startDate)->whereNotNull('completed_at')->count();
        $completionRate = $totalStarted > 0 ? round($totalCompleted / $totalStarted * 100) : 0;

        $kpis = [
            'new_users'          => $newUsers,
            'users_growth'       => $usersGrowth,
            'new_enrollments'    => $newEnrollments,
            'enrollments_growth' => $enrollmentsGrowth,
            'revenue'            => $revenue,
            'revenue_growth'     => $revenueGrowth,
            'completion_rate'    => $completionRate,
        ];

        // ── Métriques d'engagement ──────────────────────────────────────────
        $totalUsers     = User::where('role', 'student')->count();
        $activeUsers    = Enrollment::whereNotNull('enrolled_at')->distinct('user_id')->count('user_id');
        $activationRate = $totalUsers > 0 ? round($activeUsers / $totalUsers * 100) : 0;

        $usersWithMultiple  = Enrollment::select('user_id')->groupBy('user_id')->havingRaw('COUNT(*) > 1')->count();
        $retentionRate      = $totalUsers > 0 ? round($usersWithMultiple / max(1, $activeUsers) * 100) : 0;

        $avgQuizScore       = (int) QuizAttempt::avg('score');

        $avgRating          = round(CourseReview::avg('rating') ?? 0, 1);

        $threadsWithReplies = ForumThread::has('replies')->count();
        $totalThreads       = ForumThread::count();
        $forumResponseRate  = $totalThreads > 0 ? round($threadsWithReplies / $totalThreads * 100) : 0;

        $engagementMetrics = [
            'activation_rate'     => $activationRate,
            'retention_rate'      => min(100, $retentionRate),
            'completion_rate'     => $completionRate,
            'avg_quiz_score'      => $avgQuizScore,
            'avg_rating'          => $avgRating,
            'forum_response_rate' => $forumResponseRate,
        ];

        // ── Données pour Chart.js ───────────────────────────────────────────
        // 1. Inscriptions par jour
        $enrollmentsByDay = Enrollment::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $enrollLabels = [];
        $enrollData   = [];
        for ($i = $period; $i >= 0; $i--) {
            $date           = now()->subDays($i)->toDateString();
            $enrollLabels[] = now()->subDays($i)->format('d/m');
            $enrollData[]   = $enrollmentsByDay[$date]->count ?? 0;
        }

        // 2. Revenus mensuels (année en cours)
        $revenueByMonth = RevenueRecord::completed()
            ->whereYear('paid_at', now()->year)
            ->selectRaw('MONTH(paid_at) as month, SUM(net_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
        $revenueData = [];
        foreach (range(1, 12) as $m) {
            $revenueData[] = (int) ($revenueByMonth[$m]->total ?? 0);
        }

        // 3. Cours par catégorie
        $catData = Course::published()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        $chartData = [
            'enrollments' => [
                'labels' => $enrollLabels,
                'data'   => $enrollData,
            ],
            'revenues' => [
                'labels' => $months,
                'data'   => $revenueData,
            ],
            'categories' => [
                'labels' => $catData->pluck('category')->map(fn($c) => $c ?: 'Autre')->toArray(),
                'data'   => $catData->pluck('count')->toArray(),
            ],
        ];

        // ── Répartition géographique ────────────────────────────────────────
        $usersByCountry = User::where('role', 'student')
            ->selectRaw('country, COUNT(*) as total')
            ->groupBy('country')
            ->orderByDesc('total')
            ->get()
            ->map(fn($r) => ['country' => $r->country, 'total' => $r->total]);

        // ── Cours par catégorie ─────────────────────────────────────────────
        $coursesByCategory = Course::published()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get()
            ->map(fn($r) => ['category' => $r->category, 'count' => $r->count]);

        // ── Top cours ───────────────────────────────────────────────────────
        $topCourses = Course::published()
            ->withCount('enrollments')
            ->with('teacher')
            ->orderByDesc('enrollments_count')
            ->take(8)
            ->get();

        // ── Top formateurs ──────────────────────────────────────────────────
        $topTeachers = User::where('role', 'teacher')
            ->withCount(['courses as total_enrollments' => fn($q) =>
                $q->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
                  ->select(DB::raw('COUNT(enrollments.id)'))
            ])
            ->orderByDesc('total_enrollments')
            ->take(6)
            ->get();

        // ── Inscriptions récentes ───────────────────────────────────────────
        $recentEnrollments = Enrollment::with(['user', 'course.teacher'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.analytics.index', compact(
            'kpis', 'engagementMetrics', 'chartData',
            'usersByCountry', 'coursesByCategory',
            'topCourses', 'topTeachers', 'recentEnrollments',
            'period'
        ));
    }
}