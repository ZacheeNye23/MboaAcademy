<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RevenueRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(): View
    {
        // ── KPIs globaux ─────────────────────────────────────────────────────
        $totalRevenue      = RevenueRecord::where('status', 'completed')->sum('amount');
        $totalCommission   = RevenueRecord::where('status', 'completed')->sum('commission');
        $totalNetTeachers  = RevenueRecord::where('status', 'completed')->sum('net_amount');
        $totalRefunded     = RevenueRecord::where('status', 'refunded')->sum('amount');

        $revenueThisMonth  = RevenueRecord::where('status', 'completed')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at',  now()->year)
            ->sum('amount');

        $revenueLastMonth  = RevenueRecord::where('status', 'completed')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at',  now()->subMonth()->year)
            ->sum('amount');

        $growthPct = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100)
            : 0;

        $transactionsThisMonth = RevenueRecord::where('status', 'completed')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at',  now()->year)
            ->count();

        // ── Revenus 12 derniers mois (graphe) ────────────────────────────────
        $monthlyRevenue = RevenueRecord::where('status', 'completed')
            ->where('paid_at', '>=', now()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('YEAR(paid_at) as year'),
                DB::raw('MONTH(paid_at) as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('SUM(commission) as platform'),
                DB::raw('SUM(net_amount) as teachers')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        // Remplir les mois manquants
        $chartData = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $found = $monthlyRevenue->first(fn($r) =>
                $r->year == $date->year && $r->month == $date->month
            );
            $chartData->push([
                'label'    => $date->translatedFormat('M y'),
                'total'    => $found?->total    ?? 0,
                'platform' => $found?->platform ?? 0,
                'teachers' => $found?->teachers ?? 0,
            ]);
        }

        // ── Top cours par revenus ─────────────────────────────────────────────
        $topCourses = RevenueRecord::where('status', 'completed')
            ->with('course.teacher')
            ->select('course_id',
                DB::raw('SUM(amount) as total_revenue'),
                DB::raw('COUNT(*) as sales_count')
            )
            ->groupBy('course_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        // ── Top formateurs par revenus ────────────────────────────────────────
        $topTeachers = RevenueRecord::where('status', 'completed')
            ->with('teacher')
            ->select('teacher_id',
                DB::raw('SUM(net_amount) as total_earned'),
                DB::raw('COUNT(*) as sales_count')
            )
            ->groupBy('teacher_id')
            ->orderByDesc('total_earned')
            ->take(5)
            ->get();

        // ── Répartition statuts ───────────────────────────────────────────────
        $statusBreakdown = RevenueRecord::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('admin.finance.revenue', compact(
            'totalRevenue', 'totalCommission', 'totalNetTeachers', 'totalRefunded',
            'revenueThisMonth', 'revenueLastMonth', 'growthPct', 'transactionsThisMonth',
            'chartData', 'topCourses', 'topTeachers', 'statusBreakdown'
        ));
    }
}