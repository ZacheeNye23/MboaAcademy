<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\RevenueRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(Request $request): View
    {
        $teacher   = Auth::user();
        $courseIds = Course::byTeacher($teacher->id)->pluck('id');

        // ── Filtres ───────────────────────────────────────────────────────────
        $year        = (int) $request->get('year', now()->year);
        $courseFilter = $request->get('course', 'all');
        $period      = $request->get('period', 'monthly'); // monthly | quarterly

        // ── Requête de base ───────────────────────────────────────────────────
        $baseQuery = RevenueRecord::where('teacher_id', $teacher->id)->completed();
        if ($courseFilter !== 'all') {
            $baseQuery->where('course_id', $courseFilter);
        }

        // ── KPIs globaux (tous temps) ─────────────────────────────────────────
        $totalRevenue   = (clone $baseQuery)->sum('net_amount');
        $totalGross     = (clone $baseQuery)->sum('amount');
        $totalStudents  = (clone $baseQuery)->distinct('student_id')->count('student_id');
        $totalTx        = (clone $baseQuery)->count();

        // Ce mois
        $thisMonthQ = (clone $baseQuery)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year);
        $thisMonth  = $thisMonthQ->sum('net_amount');

        // Mois précédent
        $lastMonthQ = (clone $baseQuery)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at',  now()->subMonth()->year);
        $lastMonth  = $lastMonthQ->sum('net_amount');

        $variation = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100 : 0);

        // ── Revenus mensuels (année sélectionnée) ─────────────────────────────
        $monthlyData = (clone $baseQuery)
            ->whereYear('paid_at', $year)
            ->selectRaw('MONTH(paid_at) as month, SUM(net_amount) as total, COUNT(*) as tx')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Remplir les 12 mois (0 si aucune donnée)
        $monthlyChart = collect(range(1, 12))->map(fn($m) => [
            'month'  => $m,
            'label'  => now()->setMonth($m)->translatedFormat('M'),
            'total'  => round($monthlyData->get($m)?->total ?? 0),
            'tx'     => $monthlyData->get($m)?->tx ?? 0,
        ]);

        // ── Revenus trimestriels ──────────────────────────────────────────────
        $quarterlyChart = $monthlyChart->chunk(3)->values()->map(function ($months, $i) {
            return [
                'label' => 'T' . ($i + 1),
                'total' => $months->sum('total'),
                'tx'    => $months->sum('tx'),
            ];
        });

        // ── Répartition par cours ─────────────────────────────────────────────
        $byCourse = (clone $baseQuery)
            ->select('course_id',
                DB::raw('SUM(net_amount) as total'),
                DB::raw('SUM(amount) as gross'),
                DB::raw('COUNT(*) as tx'),
                DB::raw('COUNT(DISTINCT student_id) as students')
            )
            ->groupBy('course_id')
            ->orderByDesc('total')
            ->with('course:id,title,thumbnail,price')
            ->get();

        $totalForPct = $byCourse->sum('total') ?: 1;

        // ── Historique transactions ───────────────────────────────────────────
        $txQuery = RevenueRecord::where('teacher_id', $teacher->id)
            ->completed()
            ->with(['course:id,title', 'student:id,first_name,last_name,email'])
            ->latest('paid_at');

        if ($courseFilter !== 'all') $txQuery->where('course_id', $courseFilter);

        $transactions = $txQuery->paginate(15)->withQueryString();

        // ── Années disponibles ────────────────────────────────────────────────
        $availableYears = RevenueRecord::where('teacher_id', $teacher->id)
            ->completed()
            ->selectRaw('YEAR(paid_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        // ── Cours pour filtre ─────────────────────────────────────────────────
        $courses = Course::byTeacher($teacher->id)
            ->select('id', 'title')
            ->whereIn('id', RevenueRecord::where('teacher_id', $teacher->id)->pluck('course_id')->unique())
            ->get();

        // ── Meilleur mois ─────────────────────────────────────────────────────
        $bestMonth = $monthlyChart->sortByDesc('total')->first();

        return view('teacher.revenues.index', compact(
            'totalRevenue', 'totalGross', 'totalStudents', 'totalTx',
            'thisMonth', 'lastMonth', 'variation',
            'monthlyChart', 'quarterlyChart', 'byCourse', 'totalForPct',
            'transactions', 'availableYears', 'courses',
            'year', 'courseFilter', 'period', 'bestMonth'
        ));
    }

    // ── Export CSV ────────────────────────────────────────────────────────────
    public function export(Request $request): Response
    {
        $teacher      = Auth::user();
        $courseFilter = $request->get('course', 'all');
        $year         = $request->get('year');

        $query = RevenueRecord::where('teacher_id', $teacher->id)
            ->completed()
            ->with(['course:id,title', 'student:id,first_name,last_name,email'])
            ->latest('paid_at');

        if ($courseFilter !== 'all') $query->where('course_id', $courseFilter);
        if ($year) $query->whereYear('paid_at', $year);

        $records = $query->get();

        $csv  = "\xEF\xBB\xBF"; // BOM UTF-8
        $csv .= implode(';', ['"Date"','"Apprenant"','"Email"','"Cours"','"Montant brut"','"Commission"','"Net reçu"','"Statut"']) . "\r\n";

        foreach ($records as $r) {
            $csv .= implode(';', [
                '"' . ($r->paid_at?->format('d/m/Y') ?? '') . '"',
                '"' . ($r->student->full_name ?? '') . '"',
                '"' . ($r->student->email ?? '') . '"',
                '"' . ($r->course->title ?? '') . '"',
                '"' . number_format($r->amount, 0, ',', ' ') . ' XAF"',
                '"' . number_format($r->commission, 0, ',', ' ') . ' XAF"',
                '"' . number_format($r->net_amount, 0, ',', ' ') . ' XAF"',
                '"Complété"',
            ]) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="revenus_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}