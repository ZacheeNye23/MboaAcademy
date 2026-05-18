@extends('admin.layouts.app')

@section('title', 'Paiements')
@section('page-title', 'Paiements')
@section('page-subtitle', number_format($stats['volume'],0,',',' ').' XAF · '.$stats['total'].' transactions')

@section('topbar-actions')
<a href="{{ route('admin.revenues.index') }}"
   class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold"
   style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
    ← Revenus globaux
</a>
@endsection

@push('styles')
<style>
    .pay-row { display:flex;align-items:center;gap:12px;padding:14px 22px;border-bottom:1px solid rgba(255,255,255,0.04);transition:background .2s; }
    .pay-row:hover { background:rgba(255,255,255,0.02); }
    .pay-row:last-child { border-bottom:none; }
    .filter-btn { padding:6px 13px;border-radius:100px;font-size:.74rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;border:none;font-family:'Outfit',sans-serif; }
    .filter-btn.on  { background:#e8b84b;color:#0a1a0f; }
    .filter-btn.off { background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.4);border:1px solid rgba(255,255,255,0.07); }
    .filter-btn.off:hover { border-color:rgba(232,184,75,0.3);color:#e8b84b; }
    .search-input { background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);border-radius:12px;padding:8px 14px 8px 36px;color:#fff;font-family:'Outfit',sans-serif;font-size:.85rem;outline:none;transition:all .2s;width:240px; }
    .search-input::placeholder { color:rgba(255,255,255,0.22); }
    .search-input:focus { border-color:rgba(232,184,75,0.3); }
    .act-btn { display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:8px;font-size:.7rem;font-weight:600;cursor:pointer;transition:all .18s;text-decoration:none;border:none;font-family:'Outfit',sans-serif; }
    .act-complete { background:rgba(37,194,110,0.1);color:#25c26e; }
    .act-complete:hover { background:rgba(37,194,110,0.2); }
    .act-refund   { background:rgba(239,68,68,0.08);color:#f87171; }
    .act-refund:hover { background:rgba(239,68,68,0.15); }
</style>
@endpush

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-7">
    @foreach([
        ['💰','Volume total',  number_format($stats['volume'],0,',',' ').' XAF', '#e8b84b'],
        ['✅','Complétés',     $stats['completed'],  '#25c26e'],
        ['⏳','En attente',    $stats['pending'],    '#e8b84b'],
        ['↩️', 'Remboursés',   $stats['refunded'],   '#f87171'],
        ['📊','Total',         $stats['total'],      '#60a5fa'],
    ] as [$icon,$label,$val,$color])
    <div class="glass p-4 anim d{{ $loop->iteration }}">
        <div class="text-xl mb-2">{{ $icon }}</div>
        <div class="text-xl font-bold" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
        <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Filtres --}}
<div class="glass p-4 mb-5 anim d2">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs" style="color:rgba(255,255,255,0.3)">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="search-input" placeholder="Apprenant, cours...">
        </div>

        {{-- Statut --}}
        <div class="flex gap-2 flex-wrap">
            @foreach(['all'=>'🗂 Tous','completed'=>'✅ Complétés','pending'=>'⏳ En attente','refunded'=>'↩️ Remboursés'] as $val=>$label)
            <button type="submit" name="status" value="{{ $val }}"
                    class="filter-btn {{ request('status','all')===$val?'on':'off' }}">{{ $label }}</button>
            @endforeach
        </div>

        {{-- Période --}}
        <div class="flex gap-2 flex-wrap ml-auto">
            @foreach(['all'=>'Tout','today'=>'Auj.','week'=>'7j','month'=>'Ce mois','last_month'=>'Mois dernier'] as $val=>$label)
            <button type="submit" name="period" value="{{ $val }}"
                    class="filter-btn {{ request('period','all')===$val?'on':'off' }}">{{ $label }}</button>
            @endforeach
        </div>
    </form>
</div>

{{-- Table --}}
<div class="glass overflow-hidden anim d3">

    {{-- Entête --}}
    <div class="flex items-center gap-3 px-5 py-3 border-b border-white/5"
         style="color:rgba(255,255,255,0.22);font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.07rem">
        <div class="flex-1">Transaction</div>
        <div class="w-32 hidden md:block">Formateur</div>
        <div class="w-28 text-right hidden lg:block">Montant</div>
        <div class="w-24 text-right hidden lg:block">Commission</div>
        <div class="w-24 text-right hidden lg:block">Net</div>
        <div class="w-24 text-center hidden md:block">Statut</div>
        <div class="w-28 text-right hidden md:block">Date</div>
        <div class="w-24 text-right">Actions</div>
    </div>

    @forelse($payments as $payment)
    @php
        $statusPill  = match($payment->status) { 'completed'=>'pill-green','pending'=>'pill-gold',default=>'pill-red' };
        $statusLabel = match($payment->status) { 'completed'=>'✅','pending'=>'⏳',default=>'↩️' };
        $avatarColors = ['#1a8a47','#e8b84b','#3b82f6','#a78bfa','#f87171'];
        $bg = $avatarColors[($payment->student?->id ?? 0) % 5];
    @endphp
    <div class="pay-row">

        {{-- Apprenant + cours --}}
        <div class="flex items-center gap-2.5 flex-1 min-w-0">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                 style="background:{{ $bg }}">
                {{ $payment->student?->initials ?? '?' }}
            </div>
            <div class="min-w-0">
                <div class="text-sm font-medium text-white truncate">
                    {{ $payment->student?->full_name ?? 'Inconnu' }}
                </div>
                <div class="text-xs truncate" style="color:rgba(255,255,255,0.35)">
                    {{ $payment->course?->title ?? 'Cours supprimé' }}
                </div>
            </div>
        </div>

        {{-- Formateur --}}
        <div class="w-32 hidden md:block">
            <div class="text-xs text-white truncate">{{ $payment->teacher?->full_name ?? '—' }}</div>
        </div>

        {{-- Montant --}}
        <div class="w-28 text-right hidden lg:block">
            <span class="text-sm font-bold text-white">
                {{ number_format($payment->amount, 0, ',', ' ') }}
            </span>
            <span class="text-xs" style="color:rgba(255,255,255,0.4)"> {{ $payment->currency ?? 'XAF' }}</span>
        </div>

        {{-- Commission --}}
        <div class="w-24 text-right hidden lg:block">
            <span class="text-xs" style="color:#a78bfa">
                {{ number_format($payment->commission, 0, ',', ' ') }}
            </span>
        </div>

        {{-- Net --}}
        <div class="w-24 text-right hidden lg:block">
            <span class="text-xs font-semibold" style="color:#25c26e">
                {{ number_format($payment->net_amount, 0, ',', ' ') }}
            </span>
        </div>

        {{-- Statut --}}
        <div class="w-24 text-center hidden md:block">
            <span class="pill {{ $statusPill }}" style="font-size:.62rem">
                {{ $statusLabel }} {{ ucfirst($payment->status) }}
            </span>
        </div>

        {{-- Date --}}
        <div class="w-28 text-right hidden md:block">
            <div class="text-xs" style="color:rgba(255,255,255,0.3)">
                {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') : '—' }}
            </div>
            <div class="text-[10px]" style="color:rgba(255,255,255,0.2)">
                {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('H:i') : '' }}
            </div>
        </div>

        {{-- Actions --}}
        <div class="w-24 flex items-center justify-end gap-1.5 shrink-0">
            @if($payment->status === 'pending')
            <form method="POST" action="{{ route('admin.payments.complete', $payment) }}">
                @csrf @method('PATCH')
                <button type="submit" class="act-btn act-complete" title="Marquer complété">✅</button>
            </form>
            @endif
            @if($payment->status === 'completed')
            <form method="POST" action="{{ route('admin.payments.refund', $payment) }}"
                  onsubmit="return confirm('Rembourser ce paiement ?')">
                @csrf @method('PATCH')
                <button type="submit" class="act-btn act-refund" title="Rembourser">↩️</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="text-5xl mb-4">💳</div>
        <h3 class="font-bold text-white mb-2" style="font-family:'Playfair Display',serif">Aucun paiement</h3>
        <p class="text-sm" style="color:rgba(255,255,255,0.3)">Aucun paiement ne correspond à vos filtres.</p>
    </div>
    @endforelse
</div>

<div class="mt-5">{{ $payments->withQueryString()->links() }}</div>

@endsection