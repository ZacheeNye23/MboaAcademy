@extends('admin.layouts.app')
 
@section('title', 'Reversements · '.$teacher->full_name)
@section('page-title', 'Reversements · '.$teacher->full_name)
@section('page-subtitle', $summary['total_sales'].' ventes · '.$teacher->email)
 
@section('topbar-actions')
<a href="{{ route('admin.payouts.index') }}"
   class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold"
   style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
    ← Reversements
</a>
@endsection
 
@section('content')
 
{{-- Stats du formateur --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-7">
    @foreach([
        ['💰','Total gagné',      number_format($summary['total_earned'],0,',',' ').' XAF', '#25c26e'],
        ['⏳','En attente',        number_format($summary['pending_payout'],0,',',' ').' XAF','#e8b84b'],
        ['📊','Total ventes',     $summary['total_sales'],                                   '#60a5fa'],
        ['↩️', 'Remboursements',   $summary['refunded'],                                      '#f87171'],
    ] as [$icon,$label,$val,$color])
    <div class="glass p-5 anim d{{ $loop->iteration }}">
        <div class="text-2xl mb-2">{{ $icon }}</div>
        <div class="text-xl font-bold" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
        <div class="text-xs mt-1" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>
 
{{-- Action payer --}}
@if($summary['pending_payout'] > 0)
<div class="mb-6 flex items-center justify-between gap-4 px-5 py-4 rounded-2xl anim d2"
     style="background:rgba(232,184,75,0.06);border:1px solid rgba(232,184,75,0.2)">
    <div>
        <p class="text-sm font-bold" style="color:#e8b84b">
            {{ number_format($summary['pending_payout'],0,',',' ') }} XAF en attente de reversement
        </p>
        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45)">
            Cliquez sur "Payer tout" pour marquer toutes les transactions en attente comme payées.
        </p>
    </div>
    <form method="POST" action="{{ route('admin.payouts.markPaid', $teacher) }}"
          onsubmit="return confirm('Confirmer le paiement de {{ number_format($summary[\'pending_payout\'],0,\',\',\' \') }} XAF à {{ addslashes($teacher->full_name) }} ?')">
        @csrf @method('PATCH')
        <button type="submit"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap border-none cursor-pointer"
                style="background:linear-gradient(135deg,#1a8a47,#25c26e);color:#fff">
            💸 Payer tout
        </button>
    </form>
</div>
@endif
 
{{-- Liste des transactions --}}
<div class="glass overflow-hidden anim d3">
    <div class="flex items-center gap-3 px-5 py-3 border-b border-white/5"
         style="color:rgba(255,255,255,0.22);font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.07rem">
        <div class="flex-1">Cours</div>
        <div class="w-32 hidden md:block">Apprenant</div>
        <div class="w-24 text-right hidden lg:block">Montant</div>
        <div class="w-24 text-right hidden lg:block">Commission</div>
        <div class="w-24 text-right">Net</div>
        <div class="w-24 text-center hidden md:block">Statut</div>
        <div class="w-28 text-right hidden md:block">Date</div>
    </div>
 
    @forelse($records as $record)
    @php
        $statusPill  = match($record->status) { 'completed'=>'pill-green','pending'=>'pill-gold',default=>'pill-red' };
        $statusLabel = match($record->status) { 'completed'=>'✅ Payé','pending'=>'⏳ En attente',default=>'↩️ Remboursé' };
    @endphp
    <div style="display:flex;align-items:center;gap:12px;padding:13px 22px;border-bottom:1px solid rgba(255,255,255,0.04)">
        <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-white truncate">
                {{ $record->course?->title ?? 'Cours supprimé' }}
            </div>
        </div>
        <div class="w-32 hidden md:block">
            <div class="text-xs text-white truncate">{{ $record->student?->full_name ?? '—' }}</div>
        </div>
        <div class="w-24 text-right hidden lg:block">
            <span class="text-xs text-white">{{ number_format($record->amount,0,',',' ') }}</span>
        </div>
        <div class="w-24 text-right hidden lg:block">
            <span class="text-xs" style="color:#a78bfa">{{ number_format($record->commission,0,',',' ') }}</span>
        </div>
        <div class="w-24 text-right">
            <span class="text-sm font-bold" style="color:#25c26e">{{ number_format($record->net_amount,0,',',' ') }}</span>
        </div>
        <div class="w-24 text-center hidden md:block">
            <span class="pill {{ $statusPill }}" style="font-size:.6rem">{{ $statusLabel }}</span>
        </div>
        <div class="w-28 text-right hidden md:block">
            <span class="text-xs" style="color:rgba(255,255,255,0.3)">
                {{ $record->paid_at ? \Carbon\Carbon::parse($record->paid_at)->format('d/m/Y') : '—' }}
            </span>
        </div>
    </div>
    @empty
    <div class="py-12 text-center">
        <p class="text-sm" style="color:rgba(255,255,255,0.3)">Aucune transaction pour ce formateur.</p>
    </div>
    @endforelse
</div>
 
<div class="mt-4">{{ $records->withQueryString()->links() }}</div>
 
@endsection
 