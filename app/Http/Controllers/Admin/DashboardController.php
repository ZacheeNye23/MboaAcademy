<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\RevenueRecord;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Compteurs globaux ────────────────────────────────────────────────
        $totalUsers      = User::count();
        $totalStudents   = User::where('role', 'student')->count();
        $totalTeachers   = User::where('role', 'teacher')->count();
        $totalAdmins     = User::where('role', 'admin')->count();

        $totalCourses    = Course::count();
        $pendingCourses  = Course::where('status', 'pending')->count();
        $publishedCourses = Course::where('status', 'published')->count();

        $totalEnrollments = Enrollment::count();
        $totalThreads     = ForumThread::count();

        // ── Nouveaux utilisateurs ce mois ───────────────────────────────────
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $newUsersLastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $userGrowthPct = $newUsersLastMonth > 0
            ? round((($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100)
            : 0;

        // ── Revenus ──────────────────────────────────────────────────────────
        // Adapte le modèle Payment si nécessaire (ou Enrollment si tu stockes le prix là)
        $revenueThisMonth = RevenueRecord::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('amount');

        $revenueLastMonth = RevenueRecord::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('status', 'completed')
            ->sum('amount');

        $revenueGrowthPct = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100)
            : 0;

        $totalTransactionsThisMonth = RevenueRecord::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->count();

        $revenueGoal = 4_000_000; // XAF — modifiable
        $revenueGoalPct = $revenueGoal > 0
            ? min(100, round(($revenueThisMonth / $revenueGoal) * 100))
            : 0;

        // ── Répartition utilisateurs (pour le donut) ────────────────────────
        $circumference = 2 * M_PI * 52;
        $studentPct  = $totalUsers > 0 ? $totalStudents  / $totalUsers : 0;
        $teacherPct  = $totalUsers > 0 ? $totalTeachers  / $totalUsers : 0;
        $adminPct    = $totalUsers > 0 ? $totalAdmins    / $totalUsers : 0;

        // Offsets SVG pour le donut (arc cumulatif)
        $donut = [
            'students' => [
                'dash'   => $circumference * $studentPct,
                'offset' => $circumference * 0.25,
                'color'  => '#25c26e',
                'pct'    => round($studentPct * 100),
            ],
            'teachers' => [
                'dash'   => $circumference * $teacherPct,
                'offset' => $circumference * (0.25 - $studentPct),
                'color'  => '#e8b84b',
                'pct'    => round($teacherPct * 100),
            ],
            'admins' => [
                'dash'   => $circumference * $adminPct,
                'offset' => $circumference * (0.25 - $studentPct - $teacherPct),
                'color'  => '#a78bfa',
                'pct'    => round($adminPct * 100),
            ],
        ];

        // ── Cours en attente de validation ──────────────────────────────────
        $coursesAwaitingValidation = Course::with('teacher')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // ── Activité récente globale ─────────────────────────────────────────
        // Fusionne les inscriptions, paiements et threads récents
        $recentEnrollments = Enrollment::with('user', 'course')
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($e) => [
                'icon'   => '🆕',
                'action' => 'Nouvelle inscription',
                'detail' => "{$e->user->full_name} — {$e->course->title}",
                'time'   => $e->created_at,
                'dot'    => 'dot-green',
            ]);

        $recentPayments = RevenueRecord::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($p) => [
                'icon'   => '💰',
                'action' => 'Paiement reçu',
                'detail' => number_format($p->amount, 0, ',', ' ')." XAF — {$p->user->full_name}",
                'time'   => $p->created_at,
                'dot'    => 'dot-green',
            ]);

        $recentThreads = ForumThread::with('author')
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($t) => [
                'icon'   => '💬',
                'action' => 'Nouvelle discussion forum',
                'detail' => "{$t->title} — {$t->author->full_name}",
                'time'   => $t->created_at,
                'dot'    => 'dot-gold',
            ]);

        $recentActivity = $recentEnrollments
            ->concat($recentPayments)
            ->concat($recentThreads)
            ->sortByDesc('time')
            ->take(8)
            ->values()
            ->map(fn($item) => array_merge($item, [
                'time_human' => $item['time']->diffForHumans(),
            ]));

        // ── Top formateurs (par revenus ou inscriptions) ─────────────────────
        $topTeachers = User::where('role', 'teacher')
            ->withCount(['courses as total_enrollments' => fn($q) =>
                $q->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ])
            ->orderByDesc('total_enrollments')
            ->take(5)
            ->get();

        // ── Inscriptions des 7 derniers jours (sparkline) ───────────────────
        $enrollmentsByDay = Enrollment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Remplir les jours manquants avec 0
        $sparkline = collect();
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $sparkline->put($day, $enrollmentsByDay->get($day, 0));
        }

        return view('admin.dashboard', compact(
            'totalUsers', 'totalStudents', 'totalTeachers', 'totalAdmins',
            'totalCourses', 'pendingCourses', 'publishedCourses',
            'totalEnrollments', 'totalThreads',
            'newUsersThisMonth', 'userGrowthPct',
            'revenueThisMonth', 'revenueLastMonth', 'revenueGrowthPct',
            'totalTransactionsThisMonth', 'revenueGoal', 'revenueGoalPct',
            'donut', 'totalUsers',
            'coursesAwaitingValidation',
            'recentActivity',
            'topTeachers',
            'sparkline'
        ));
    }
}