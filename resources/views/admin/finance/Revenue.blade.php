@extends('admin.layouts.app')

@section('title', 'Revenus globaux')
@section('page-title', 'Revenus globaux')
@section('page-subtitle', 'Vue d\'ensemble financière de la plateforme')

@section('topbar-actions')
<a href="{{ route('admin.payments.index') }}"
   class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold"
   style="background:rgba(37,194,110,0.1);color:#25c26e">
    💳 Voir les paiements
</a>
@endsection

@push('styles')
<style>
    .chart-bar {
        flex:1; border-radius:6px 6px 0 0;
        min-width:0; transition:all .3s; cursor:pointer; position:relative;
    }
    .chart-bar:hover { filter:brightness(1.2); }
    .chart-bar:hover .bar-tooltip {
        opacity:1; transform:translateY(0);
    }
    .bar-tooltip {
        position:absolute; bottom:calc(100% + 6px); left:50%; transform:translateX(-50%) translateY(4px);
        background:#0d1f10; border:1px solid rgba(255,255,255,0.1);
        border-radius:8px; padding:5px 10px; white-space:nowrap;
        font-size:.68rem; color:#fff; opacity:0; transition:all .2s;
        pointer-events:none; z-index:10;
    }
    .stat-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:18px; }
    .rank-bar { height:6px;border-radius:3px;background:rgba(255,255,255,0.06);overflow:hidden;margin-top:6px; }
    .rank-fill { height:100%;border-radius:3px; }
</style>
@endpush

@section('content')

{{-- ── KPI CARDS ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['💰', 'Revenus totaux',        number_format($totalRevenue,0,',',' ').' XAF',     'Tous paiements complétés',    '#e8b84b'],
        ['🏢', 'Commission plateforme', number_format($totalCommission,0,',',' ').' XAF',   setting('platform_commission',30).'% sur ventes', '#25c26e'],
        ['👨‍🏫','Reversé formateurs',   number_format($totalNetTeachers,0,',',' ').' XAF',  setting('teacher_commission',70).'% aux formateurs','#a78bfa'],
        ['↩️', 'Remboursements',        number_format($totalRefunded,0,',',' ').' XAF',     'Total remboursé',             '#f87171'],
    ] as [$icon,$label,$val,$sub,$color])
    <div class="stat-card p-5 anim d{{ $loop->iteration }}">
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl">{{ $icon }}</span>
            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                  style="background:{{ $color }}15;color:{{ $color }}">{{ $sub }}</span>
        </div>
        <div class="text-xl font-bold text-white mb-0.5" style="font-family:'Playfair Display',serif;color:{{ $color }}">
            {{ $val }}
        </div>
        <div class="text-xs" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- ── REVENUS CE MOIS ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">

    {{-- Mois en cours --}}
    <div class="stat-card p-6 anim d2"
         style="background:linear-gradient(135deg,rgba(232,184,75,0.08),rgba(232,184,75,0.03))">
        <div class="text-xs uppercase tracking-widest font-bold mb-3" style="color:rgba(232,184,75,0.6)">
            Ce mois · {{ now()->translatedFormat('F Y') }}
        </div>
        <div class="text-3xl font-bold mb-2" style="font-family:'Playfair Display',serif;color:#e8b84b">
            {{ number_format($revenueThisMonth / 1000, 1) }}K XAF
        </div>
        <div class="flex items-center gap-2 mb-4">
            <span class="text-sm font-bold {{ $growthPct >= 0 ? 'text-green-400' : 'text-red-400' }}">
                {{ $growthPct >= 0 ? '↑' : '↓' }} {{ abs($growthPct) }}%
            </span>
            <span class="text-xs" style="color:rgba(255,255,255,0.35)">vs mois dernier</span>
        </div>
        <div class="text-xs" style="color:rgba(255,255,255,0.4)">
            {{ $transactionsThisMonth }} transaction(s) ce mois
        </div>
        <div class="mt-4 pt-4 border-t border-white/5">
            <div class="flex justify-between text-xs mb-1" style="color:rgba(255,255,255,0.35)">
                <span>Mois dernier</span>
                <span>{{ number_format($revenueLastMonth / 1000, 1) }}K XAF</span>
            </div>
        </div>
    </div>

    {{-- Répartition statuts --}}
    <div class="stat-card p-6 anim d3">
        <h3 class="font-bold text-white text-sm mb-5" style="font-family:'Playfair Display',serif">
            📊 Répartition des transactions
        </h3>
        @foreach([
            ['completed','✅ Complétés', '#25c26e'],
            ['pending',  '⏳ En attente','#e8b84b'],
            ['refunded', '↩️ Remboursés', '#f87171'],
        ] as [$status,$label,$color])
        @php
            $item  = $statusBreakdown[$status] ?? null;
            $count = $item?->count ?? 0;
            $total = $statusBreakdown->sum('count') ?: 1;
            $pct   = round($count / $total * 100);
        @endphp
        <div class="mb-4">
            <div class="flex justify-between text-xs mb-1.5">
                <span style="color:rgba(255,255,255,0.6)">{{ $label }}</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-white">{{ $count }}</span>
                    <span style="color:{{ $color }}">{{ $pct }}%</span>
                </div>
            </div>
            <div class="prog-bar">
                <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Taux commission --}}
    <div class="stat-card p-6 anim d4">
        <h3 class="font-bold text-white text-sm mb-5" style="font-family:'Playfair Display',serif">
            💹 Répartition commission
        </h3>
        @php
            $teacherRate  = setting('teacher_commission', 70);
            $platformRate = setting('platform_commission', 30);
        @endphp
        <div class="flex items-center justify-center mb-5">
            <div class="relative w-28 h-28">
                @php $c = 2 * M_PI * 40; @endphp
                <svg width="112" height="112" viewBox="0 0 112 112">
                    <circle cx="56" cy="56" r="40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="14"/>
                    <circle cx="56" cy="56" r="40" fill="none" stroke="#25c26e" stroke-width="14"
                            stroke-dasharray="{{ $c * $teacherRate/100 }} {{ $c }}"
                            stroke-dashoffset="{{ $c * 0.25 }}" stroke-linecap="butt"/>
                    <circle cx="56" cy="56" r="40" fill="none" stroke="#e8b84b" stroke-width="14"
                            stroke-dasharray="{{ $c * $platformRate/100 }} {{ $c }}"
                            stroke-dashoffset="{{ $c * (0.25 - $teacherRate/100) }}" stroke-linecap="butt"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-xs font-bold text-white">100%</span>
                </div>
            </div>
        </div>
        <div class="space-y-2">
            @foreach([['#25c26e','Formateurs',$teacherRate],['#e8b84b','Plateforme',$platformRate]] as [$color,$label,$rate])
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-sm" style="background:{{ $color }}"></div>
                    <span class="text-xs" style="color:rgba(255,255,255,0.55)">{{ $label }}</span>
                </div>
                <span class="text-sm font-bold" style="color:{{ $color }}">{{ $rate }}%</span>
            </div>
            @endforeach
        </div>
        <a href="{{ route('admin.settings.index') }}"
           class="mt-4 flex items-center justify-center gap-1 text-xs font-semibold transition-colors"
           style="color:rgba(255,255,255,0.3)">
            ⚙️ Modifier →
        </a>
    </div>
</div>

{{-- ── GRAPHE 12 MOIS ───────────────────────────────────────────────────── --}}
<div class="stat-card p-6 mb-8 anim d3">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="font-bold text-white" style="font-family:'Playfair Display',serif">
                📈 Évolution des revenus (12 mois)
            </h3>
            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">
                Total · Commission plateforme · Reversements formateurs
            </p>
        </div>
        <div class="flex items-center gap-4 text-xs">
            @foreach([['#e8b84b','Total'],['#25c26e','Formateurs'],['#a78bfa','Plateforme']] as [$c,$l])
            <div class="flex items-center gap-1.5">
                <div class="w-2.5 h-2.5 rounded-sm" style="background:{{ $c }}"></div>
                <span style="color:rgba(255,255,255,0.45)">{{ $l }}</span>
            </div>
            @endforeach
        </div>
    </div>

    @php $maxVal = $chartData->max('total') ?: 1; @endphp

    {{-- Barres --}}
    <div class="flex items-end gap-1.5 h-40 mb-2">
        @foreach($chartData as $month)
        @php
            $hTotal    = max(4, round($month['total']    / $maxVal * 100));
            $hTeacher  = max(2, round($month['teachers'] / $maxVal * 100));
            $hPlatform = max(2, round($month['platform'] / $maxVal * 100));
        @endphp
        <div class="flex-1 flex flex-col items-center gap-0.5 h-full justify-end relative group">
            {{-- Tooltip --}}
            <div class="bar-tooltip">
                <div class="font-bold">{{ $month['label'] }}</div>
                <div style="color:#e8b84b">{{ number_format($month['total']/1000,1) }}K XAF</div>
                <div style="color:#25c26e">Form: {{ number_format($month['teachers']/1000,1) }}K</div>
                <div style="color:#a78bfa">Plat: {{ number_format($month['platform']/1000,1) }}K</div>
            </div>
            {{-- Barre total --}}
            <div class="w-full rounded-t-md relative"
                 style="height:{{ $hTotal }}%;background:linear-gradient(to top,rgba(232,184,75,0.3),rgba(232,184,75,0.5))">
            </div>
        </div>
        @endforeach
    </div>

    {{-- Labels mois --}}
    <div class="flex gap-1.5">
        @foreach($chartData as $month)
        <div class="flex-1 text-center text-[9px]" style="color:rgba(255,255,255,0.25)">
            {{ $month['label'] }}
        </div>
        @endforeach
    </div>
</div>

{{-- ── TOP COURS + TOP FORMATEURS ──────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 anim d4">

    {{-- Top cours --}}
    <div class="stat-card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
            <h3 class="font-bold text-white text-sm" style="font-family:'Playfair Display',serif">
                🏆 Top 5 cours par revenus
            </h3>
        </div>
        @php $maxCourse = $topCourses->max('total_revenue') ?: 1; @endphp
        @forelse($topCourses as $item)
        <div class="px-5 py-3.5 border-b border-white/4 last:border-0 hover:bg-white/2 transition-colors">
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-xs font-bold w-4 shrink-0" style="color:#e8b84b">{{ $loop->iteration }}</span>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-white truncate">
                            {{ $item->course?->title ?? 'Cours supprimé' }}
                        </div>
                        <div class="text-xs" style="color:rgba(255,255,255,0.35)">
                            {{ $item->sales_count }} vente(s) · {{ $item->course?->teacher?->full_name }}
                        </div>
                    </div>
                </div>
                <span class="text-sm font-bold shrink-0 ml-3" style="color:#e8b84b">
                    {{ number_format($item->total_revenue / 1000, 1) }}K
                </span>
            </div>
            <div class="rank-bar ml-6">
                <div class="rank-fill"
                     style="width:{{ round($item->total_revenue / $maxCourse * 100) }}%;background:linear-gradient(90deg,#e8b84b,#f0d070)">
                </div>
            </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center">
            <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucune vente enregistrée.</p>
        </div>
        @endforelse
    </div>

    {{-- Top formateurs --}}
    <div class="stat-card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
            <h3 class="font-bold text-white text-sm" style="font-family:'Playfair Display',serif">
                👨‍🏫 Top 5 formateurs par revenus
            </h3>
            <a href="{{ route('admin.payouts.index') }}"
               class="text-xs font-semibold" style="color:#e8b84b">
                Voir reversements →
            </a>
        </div>
        @php
            $maxTeacher   = $topTeachers->max('total_earned') ?: 1;
            $avatarColors = ['#e8b84b','#25c26e','#3b82f6','#a78bfa','#f87171'];
        @endphp
        @forelse($topTeachers as $item)
        @php $bg = $avatarColors[$loop->index % 5]; @endphp
        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-white/4 last:border-0 hover:bg-white/2 transition-colors">
            <span class="text-xs font-bold w-4 shrink-0 text-center" style="color:{{ $bg }}">{{ $loop->iteration }}</span>
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                 style="background:{{ $bg }}">{{ $item->teacher?->initials }}</div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-white truncate">{{ $item->teacher?->full_name }}</div>
                <div class="rank-bar">
                    <div class="rank-fill" style="width:{{ round($item->total_earned/$maxTeacher*100) }}%;background:{{ $bg }}"></div>
                </div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-sm font-bold" style="color:{{ $bg }}">
                    {{ number_format($item->total_earned / 1000, 1) }}K
                </div>
                <div class="text-[10px]" style="color:rgba(255,255,255,0.3)">{{ $item->sales_count }} ventes</div>
            </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center">
            <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucun formateur avec des ventes.</p>
        </div>
        @endforelse
    </div>
</div>

@endsection