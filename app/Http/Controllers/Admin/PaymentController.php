<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RevenueRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = RevenueRecord::with(['course', 'teacher', 'student'])
            ->orderByDesc('paid_at');

        // Filtre statut
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filtre période
        match($request->get('period', 'all')) {
            'today'     => $query->whereDate('paid_at', today()),
            'week'      => $query->where('paid_at', '>=', now()->subDays(7)),
            'month'     => $query->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year),
            'last_month'=> $query->whereMonth('paid_at', now()->subMonth()->month)->whereYear('paid_at', now()->subMonth()->year),
            default     => null,
        };

        // Recherche
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->whereHas('student', fn($s) =>
                    $s->where('first_name', 'like', '%'.$request->search.'%')
                      ->orWhere('last_name',  'like', '%'.$request->search.'%')
                      ->orWhere('email',      'like', '%'.$request->search.'%')
                )
                ->orWhereHas('course', fn($c) =>
                    $c->where('title', 'like', '%'.$request->search.'%')
                )
            );
        }

        $payments = $query->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total'     => RevenueRecord::count(),
            'completed' => RevenueRecord::where('status', 'completed')->count(),
            'pending'   => RevenueRecord::where('status', 'pending')->count(),
            'refunded'  => RevenueRecord::where('status', 'refunded')->count(),
            'volume'    => RevenueRecord::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.finance.payments', compact('payments', 'stats'));
    }

    // ── Marquer un paiement comme complété ───────────────────────────────────
    public function markCompleted(RevenueRecord $payment): RedirectResponse
    {
        abort_unless($payment->status === 'pending', 422);

        $payment->update([
            'status'  => 'completed',
            'paid_at' => now(),
        ]);

        return back()->with('success', '✅ Paiement marqué comme complété.');
    }

    // ── Rembourser ───────────────────────────────────────────────────────────
    public function refund(RevenueRecord $payment): RedirectResponse
    {
        abort_unless($payment->status === 'completed', 422);

        $payment->update(['status' => 'refunded']);

        return back()->with('success', '↩️ Paiement remboursé.');
    }
}


