<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RevenueRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        // Revenus par formateur (complétés, non encore reversés = pending)
        $query = User::where('role', 'teacher')
            ->withSum(['revenueRecords as total_earned' => fn($q) =>
                $q->where('status', 'completed')
            ], 'net_amount')
            ->withSum(['revenueRecords as pending_payout' => fn($q) =>
                $q->where('status', 'pending')
            ], 'net_amount')
            ->withCount(['revenueRecords as total_sales' => fn($q) =>
                $q->where('status', 'completed')
            ])
            ->having('total_earned', '>', 0)
            ->orderByDesc('total_earned');

        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('first_name', 'like', '%'.$request->search.'%')
                  ->orWhere('last_name',  'like', '%'.$request->search.'%')
                  ->orWhere('email',      'like', '%'.$request->search.'%')
            );
        }

        $teachers = $query->paginate(15)->withQueryString();

        // Stats globales reversements
        $stats = [
            'total_to_pay'   => RevenueRecord::where('status', 'pending')->sum('net_amount'),
            'total_paid'     => RevenueRecord::where('status', 'completed')->sum('net_amount'),
            'teachers_count' => User::where('role', 'teacher')
                ->whereHas('revenueRecords', fn($q) => $q->where('status', 'pending'))
                ->count(),
            'commission_rate' => setting('platform_commission', 30),
            'teacher_rate'    => setting('teacher_commission', 70),
        ];

        // Historique des reversements récents (completed avec paid_at ce mois)
        $recentPayouts = RevenueRecord::with(['teacher', 'course'])
            ->where('status', 'completed')
            ->whereNotNull('paid_at')
            ->orderByDesc('paid_at')
            ->take(8)
            ->get();

        return view('admin.finance.payouts', compact('teachers', 'stats', 'recentPayouts'));
    }

    // ── Marquer les reversements d'un formateur comme payés ──────────────────
    public function markPaid(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'teacher', 403);

        $updated = RevenueRecord::where('teacher_id', $teacher->id)
            ->where('status', 'pending')
            ->update([
                'status'  => 'completed',
                'paid_at' => now(),
            ]);

        if ($updated === 0) {
            return back()->with('error', 'Aucun reversement en attente pour ce formateur.');
        }

        return back()->with('success', "✅ {$updated} reversement(s) marqué(s) comme payé(s) pour {$teacher->full_name}.");
    }

    // ── Détail des transactions d'un formateur ────────────────────────────────
    public function show(Request $request, User $teacher): View
    {
        abort_unless($teacher->role === 'teacher', 403);

        $records = RevenueRecord::with(['course', 'student'])
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('paid_at')
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'total_earned'   => RevenueRecord::where('teacher_id', $teacher->id)->where('status', 'completed')->sum('net_amount'),
            'pending_payout' => RevenueRecord::where('teacher_id', $teacher->id)->where('status', 'pending')->sum('net_amount'),
            'total_sales'    => RevenueRecord::where('teacher_id', $teacher->id)->count(),
            'refunded'       => RevenueRecord::where('teacher_id', $teacher->id)->where('status', 'refunded')->count(),
        ];

        return view('admin.finance.payout_show', compact('teacher', 'records', 'summary'));
    }
}