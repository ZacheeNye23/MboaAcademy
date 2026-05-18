@extends('admin.layouts.app')
 
@section('title', 'Reversements')
@section('page-title', 'Reversements formateurs')
@section('page-subtitle', number_format($stats['total_to_pay'],0,',',' ').' XAF en attente · '.$stats['teachers_count'].' formateur(s)')
 
@section('topbar-actions')
<a href="{{ route('admin.revenues.index') }}"
   class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold"
   style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
    ← Revenus globaux
</a>
@endsection
 
@push('styles')
<style>
    .teacher-row { display:flex;align-items:center;gap:12px;padding:16px 22px;border-bottom:1px solid rgba(255,255,255,0.04);transition:background .2s; }
    .teacher-row:hover { background:rgba(255,255,255,0.02); }
    .teacher-row:last-child { border-bottom:none; }
    .prog-bar { height:4px;border-radius:2px;background:rgba(255,255,255,0.06);overflow:hidden; }
    .prog-fill { height:100%;border-radius:2px; }
    .act-btn { display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:10px;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .18s;text-decoration:none;border:none;font-family:'Outfit',sans-serif; }
    .act-pay  { background:rgba(37,194,110,0.12);color:#25c26e; }
    .act-pay:hover  { background:rgba(37,194,110,0.22); }
    .act-view { background:rgba(232,184,75,0.1);color:#e8b84b; }
    .act-view:hover { background:rgba(232,184,75,0.2); }
    .search-input { background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);border-radius:12px;padding:8px 14px 8px 36px;color:#fff;font-family:'Outfit',sans-serif;font-size:.85rem;outline:none;transition:all .2s;width:260px; }
    .search-input::placeholder { color:rgba(255,255,255,0.22); }
    .search-input:focus { border-color:rgba(232,184,75,0.3); }
</style>
@endpush
 
@section('content')
 
{{-- Stats globales --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['⏳','À reverser',          number_format($stats['total_to_pay'],0,',',' ').' XAF', '#e8b84b'],
        ['✅','Total reversé',        number_format($stats['total_paid'],0,',',' ').' XAF',   '#25c26e'],
        ['👨‍🏫','Formateurs en attente', $stats['teachers_count'],                              '#f87171'],
        ['📊','Part formateurs',      $stats['teacher_rate'].'%',                             '#a78bfa'],
    ] as [$icon,$label,$val,$color])
    <div class="glass p-5 anim d{{ $loop->iteration }}">
        <div class="text-2xl mb-2">{{ $icon }}</div>
        <div class="text-xl font-bold" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
        <div class="text-xs mt-1" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>
 
{{-- Alerte si des reversements sont en attente --}}
@if($stats['teachers_count'] > 0)
<div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl anim d2"
     style="background:rgba(232,184,75,0.06);border:1px solid rgba(232,184,75,0.2)">
    <span class="text-2xl">⚠️</span>
    <div class="flex-1">
        <p class="text-sm font-bold" style="color:#e8b84b">
            {{ $stats['teachers_count'] }} formateur(s) ont des reversements en attente
        </p>
        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45)">
            Total à payer : {{ number_format($stats['total_to_pay'],0,',',' ') }} XAF
        </p>
    </div>
</div>
@endif
 
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
 
    {{-- ── TABLE FORMATEURS ──────────────────────────────────────────────── --}}
    <div class="lg:col-span-2">
 
        {{-- Recherche --}}
        <div class="glass p-4 mb-4 anim d2">
            <form method="GET" class="flex items-center gap-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs" style="color:rgba(255,255,255,0.3)">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="search-input" placeholder="Nom, email du formateur...">
                </div>
                <button type="submit" class="act-btn act-view">Rechercher</button>
            </form>
        </div>
 
        <div class="glass overflow-hidden anim d3">
            {{-- Entête --}}
            <div class="flex items-center gap-3 px-5 py-3 border-b border-white/5"
                 style="color:rgba(255,255,255,0.22);font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.07rem">
                <div class="flex-1">Formateur</div>
                <div class="w-28 text-right hidden lg:block">Total gagné</div>
                <div class="w-28 text-right hidden md:block">En attente</div>
                <div class="w-20 text-center hidden lg:block">Ventes</div>
                <div class="w-36 text-right">Actions</div>
            </div>
 
            @forelse($teachers as $teacher)
            @php
                $avatarColors = ['#e8b84b','#25c26e','#3b82f6','#a78bfa','#f87171'];
                $bg = $avatarColors[$teacher->id % 5];
                $hasPending = ($teacher->pending_payout ?? 0) > 0;
            @endphp
            <div class="teacher-row">
                {{-- Avatar + nom --}}
                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                         style="background:{{ $bg }}">{{ $teacher->initials }}</div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-white truncate">{{ $teacher->full_name }}</span>
                            @if($hasPending)
                            <span class="w-2 h-2 rounded-full shrink-0" style="background:#e8b84b"></span>
                            @endif
                        </div>
                        <div class="text-xs truncate" style="color:rgba(255,255,255,0.35)">{{ $teacher->email }}</div>
                    </div>
                </div>
 
                {{-- Total gagné --}}
                <div class="w-28 text-right hidden lg:block">
                    <span class="text-sm font-bold text-white">
                        {{ number_format(($teacher->total_earned ?? 0)/1000, 1) }}K
                    </span>
                    <span class="text-xs" style="color:rgba(255,255,255,0.3)"> XAF</span>
                </div>
 
                {{-- En attente --}}
                <div class="w-28 text-right hidden md:block">
                    @if($hasPending)
                    <span class="text-sm font-bold" style="color:#e8b84b">
                        {{ number_format(($teacher->pending_payout ?? 0)/1000, 1) }}K
                    </span>
                    <span class="text-xs" style="color:rgba(232,184,75,0.6)"> XAF</span>
                    @else
                    <span class="text-xs" style="color:rgba(255,255,255,0.25)">—</span>
                    @endif
                </div>
 
                {{-- Ventes --}}
                <div class="w-20 text-center hidden lg:block">
                    <span class="text-sm text-white">{{ $teacher->total_sales ?? 0 }}</span>
                </div>
 
                {{-- Actions --}}
                <div class="w-36 flex items-center justify-end gap-1.5 shrink-0">
                    <a href="{{ route('admin.payouts.show', $teacher) }}" class="act-btn act-view">👁 Détail</a>
                    @if($hasPending)
                    <form method="POST" action="{{ route('admin.payouts.markPaid', $teacher) }}"
                          onsubmit="return confirm('Marquer tous les reversements de {{ addslashes($teacher->full_name) }} comme payés ?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="act-btn act-pay">💸 Payer</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="text-5xl mb-4">👨‍🏫</div>
                <h3 class="font-bold text-white mb-2" style="font-family:'Playfair Display',serif">
                    Aucun formateur
                </h3>
                <p class="text-sm" style="color:rgba(255,255,255,0.3)">
                    Aucun formateur avec des ventes enregistrées.
                </p>
            </div>
            @endforelse
        </div>
 
        <div class="mt-4">{{ $teachers->withQueryString()->links() }}</div>
    </div>
 
    {{-- ── SIDEBAR : REVERSEMENTS RÉCENTS ────────────────────────────────── --}}
    <div class="space-y-4 anim d4">
 
        {{-- Info commission --}}
        <div class="glass p-5" style="background:rgba(37,194,110,0.04);border-color:rgba(37,194,110,0.12)">
            <h3 class="font-bold text-white text-sm mb-4" style="font-family:'Playfair Display',serif">
                💹 Règle de commission
            </h3>
            <div class="space-y-3">
                @foreach([
                    ['👨‍🏫','Part formateur', $stats['teacher_rate'].'%','#25c26e'],
                    ['🏢','Part plateforme', $stats['commission_rate'].'%','#e8b84b'],
                ] as [$icon,$label,$val,$color])
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm" style="color:rgba(255,255,255,0.55)">
                        <span>{{ $icon }}</span><span>{{ $label }}</span>
                    </div>
                    <span class="text-sm font-bold" style="color:{{ $color }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('admin.settings.index') }}"
               class="mt-4 flex items-center justify-center gap-1 text-xs font-semibold py-2 rounded-xl transition-colors hover:bg-white/5"
               style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:rgba(255,255,255,0.4)">
                ⚙️ Modifier la commission
            </a>
        </div>
 
        {{-- Derniers reversements --}}
        <div class="glass overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5">
                <h3 class="font-bold text-white text-sm" style="font-family:'Playfair Display',serif">
                    🕐 Derniers reversements
                </h3>
            </div>
            @forelse($recentPayouts as $payout)
            @php $bg = ['#e8b84b','#25c26e','#3b82f6','#a78bfa','#f87171'][($payout->teacher?->id ?? 0) % 5]; @endphp
            <div class="flex items-center gap-3 px-5 py-3 border-b border-white/4 last:border-0">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold text-white shrink-0"
                     style="background:{{ $bg }}">{{ $payout->teacher?->initials }}</div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-medium text-white truncate">{{ $payout->teacher?->full_name }}</div>
                    <div class="text-[10px] truncate" style="color:rgba(255,255,255,0.35)">
                        {{ $payout->course?->title }}
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-xs font-bold" style="color:#25c26e">
                        +{{ number_format($payout->net_amount/1000,1) }}K
                    </div>
                    <div class="text-[10px]" style="color:rgba(255,255,255,0.25)">
                        {{ \Carbon\Carbon::parse($payout->paid_at)->format('d/m') }}
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center">
                <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucun reversement récent.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
 
@endsection