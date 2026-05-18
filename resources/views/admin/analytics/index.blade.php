@extends('admin.layouts.app')

@section('title', 'Analytiques')
@section('page-title', 'Analytiques')
@section('page-subtitle', 'Vue complète des performances de la plateforme')

@section('topbar-actions')
{{-- Sélecteur de période --}}
<div x-data="{ period: '{{ request('period', '30') }}' }">
    <select x-model="period"
            @change="window.location = '{{ route('admin.analytics') }}?period=' + period"
            class="text-xs border rounded-xl px-3 py-2 outline-none cursor-pointer font-medium"
            style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1);color:rgba(255,255,255,0.7)">
        <option value="7"  style="background:#040a05">7 derniers jours</option>
        <option value="30" style="background:#040a05">30 derniers jours</option>
        <option value="90" style="background:#040a05">90 derniers jours</option>
        <option value="365" style="background:#040a05">Cette année</option>
    </select>
</div>
@endsection

@push('styles')
<style>
    .kpi-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 18px; padding: 20px;
        transition: all .25s; position: relative; overflow: hidden;
    }
    .kpi-card:hover { transform: translateY(-2px); border-color: rgba(255,255,255,0.1); }
    .kpi-card::before {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
        border-radius: 0 0 18px 18px;
    }

    .chart-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 18px; overflow: hidden;
    }
    .chart-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 22px; border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .chart-body { padding: 20px 22px; }

    .tab-period {
        padding: 5px 12px; border-radius: 8px; font-size: .72rem; font-weight: 600;
        cursor: pointer; transition: all .2s; border: none; background: transparent;
        color: rgba(255,255,255,0.35);
    }
    .tab-period.active { background: rgba(232,184,75,0.15); color: #e8b84b; }
    .tab-period:hover:not(.active) { color: rgba(255,255,255,0.65); }

    .country-row {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .country-row:last-child { border-bottom: none; }

    .donut-legend-item {
        display: flex; align-items: center; gap: 8px;
        font-size: .78rem; color: rgba(255,255,255,0.6);
    }
    .donut-legend-dot {
        width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0;
    }

    .metric-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .metric-row:last-child { border-bottom: none; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation: fadeUp .45s ease both; }
    .d1{animation-delay:.05s}.d2{animation-delay:.10s}.d3{animation-delay:.15s}
    .d4{animation-delay:.20s}.d5{animation-delay:.25s}.d6{animation-delay:.30s}
</style>
@endpush

@section('content')

{{-- ── KPI CARDS ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        [
            'icon'    => '👥',
            'label'   => 'Nouveaux utilisateurs',
            'value'   => number_format($kpis['new_users']),
            'sub'     => ($kpis['users_growth'] >= 0 ? '↑' : '↓') . ' ' . abs($kpis['users_growth']) . '% vs période préc.',
            'color'   => '#25c26e',
            'border'  => 'linear-gradient(90deg,#1a8a47,#25c26e)',
        ],
        [
            'icon'    => '📚',
            'label'   => 'Nouvelles inscriptions',
            'value'   => number_format($kpis['new_enrollments']),
            'sub'     => ($kpis['enrollments_growth'] >= 0 ? '↑' : '↓') . ' ' . abs($kpis['enrollments_growth']) . '%',
            'color'   => '#3b82f6',
            'border'  => 'linear-gradient(90deg,#1d4ed8,#3b82f6)',
        ],
        [
            'icon'    => '💰',
            'label'   => 'Revenus générés',
            'value'   => number_format($kpis['revenue']) . ' XAF',
            'sub'     => ($kpis['revenue_growth'] >= 0 ? '↑' : '↓') . ' ' . abs($kpis['revenue_growth']) . '%',
            'color'   => '#e8b84b',
            'border'  => 'linear-gradient(90deg,#b8860b,#e8b84b)',
        ],
        [
            'icon'    => '📊',
            'label'   => 'Taux de complétion',
            'value'   => $kpis['completion_rate'] . '%',
            'sub'     => 'Cours terminés / commencés',
            'color'   => '#a78bfa',
            'border'  => 'linear-gradient(90deg,#7c3aed,#a78bfa)',
        ],
    ] as $kpi)
    <div class="kpi-card anim d{{ $loop->iteration }}">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                 style="background:{{ $kpi['color'] }}18">{{ $kpi['icon'] }}</div>
            <span class="text-xs font-medium px-2 py-1 rounded-full"
                  style="background:{{ $kpi['color'] }}12;color:{{ $kpi['color'] }}">
                {{ $kpi['sub'] }}
            </span>
        </div>
        <div class="text-2xl font-bold mb-1" style="font-family:'Playfair Display',serif;color:{{ $kpi['color'] }}">
            {{ $kpi['value'] }}
        </div>
        <div class="text-xs" style="color:rgba(255,255,255,0.4)">{{ $kpi['label'] }}</div>
        <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-2xl"
             style="background:{{ $kpi['border'] }}"></div>
    </div>
    @endforeach
</div>

{{-- ── GRAPHES PRINCIPAUX ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Évolution des inscriptions --}}
    <div class="chart-card anim d2">
        <div class="chart-header">
            <div>
                <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                    Évolution des inscriptions
                </h3>
                <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">
                    Nouveaux inscrits par jour
                </p>
            </div>
            <div class="text-right">
                <div class="text-xl font-bold" style="color:#25c26e;font-family:'Playfair Display',serif">
                    {{ number_format($kpis['new_enrollments']) }}
                </div>
                <div class="text-xs" style="color:rgba(255,255,255,0.3)">total période</div>
            </div>
        </div>
        <div class="chart-body">
            <canvas id="enrollmentChart" height="200"></canvas>
        </div>
    </div>

    {{-- Revenus mensuels --}}
    <div class="chart-card anim d3">
        <div class="chart-header">
            <div>
                <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                    Revenus mensuels
                </h3>
                <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">
                    {{ now()->year }} — en XAF
                </p>
            </div>
            <div class="text-right">
                <div class="text-xl font-bold" style="color:#e8b84b;font-family:'Playfair Display',serif">
                    {{ number_format($kpis['revenue'] / 1000, 0) }}K
                </div>
                <div class="text-xs" style="color:rgba(255,255,255,0.3)">XAF cette période</div>
            </div>
        </div>
        <div class="chart-body">
            <canvas id="revenueChart" height="200"></canvas>
        </div>
    </div>
</div>

{{-- ── LIGNE 2 ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Répartition par catégorie --}}
    <div class="chart-card anim d3">
        <div class="chart-header">
            <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                Cours par catégorie
            </h3>
        </div>
        <div class="chart-body">
            <div class="flex justify-center mb-5">
                <canvas id="categoryChart" width="180" height="180" style="max-width:180px"></canvas>
            </div>
            <div class="space-y-2">
                @php
                    $catColors = ['#25c26e','#3b82f6','#e8b84b','#a78bfa','#f97316','#ec4899'];
                @endphp
                @foreach($coursesByCategory->take(6) as $i => $cat)
                <div class="donut-legend-item">
                    <div class="donut-legend-dot" style="background:{{ $catColors[$i % count($catColors)] }}"></div>
                    <span class="flex-1 truncate">{{ $cat['category'] ?: 'Non catégorisé' }}</span>
                    <span class="font-bold text-white">{{ $cat['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Métriques d'engagement ── --}}
    <div class="chart-card anim d4">
        <div class="chart-header">
            <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                Engagement
            </h3>
        </div>
        <div class="chart-body">
            <div class="space-y-1">
                @foreach([
                    ['Taux d\'activation',     $engagementMetrics['activation_rate'].'%',      '#25c26e', $engagementMetrics['activation_rate']],
                    ['Taux de rétention',      $engagementMetrics['retention_rate'].'%',        '#3b82f6', $engagementMetrics['retention_rate']],
                    ['Taux de complétion',     $engagementMetrics['completion_rate'].'%',       '#e8b84b', $engagementMetrics['completion_rate']],
                    ['Score moyen quiz',       $engagementMetrics['avg_quiz_score'].'%',        '#a78bfa', $engagementMetrics['avg_quiz_score']],
                    ['Satisfaction moyenne',   $engagementMetrics['avg_rating'].'/5',           '#f97316', $engagementMetrics['avg_rating'] * 20],
                    ['Taux de réponse forum',  $engagementMetrics['forum_response_rate'].'%',  '#ec4899', $engagementMetrics['forum_response_rate']],
                ] as [$label, $value, $color, $pct])
                <div class="metric-row">
                    <div class="flex-1 min-w-0 pr-4">
                        <div class="flex justify-between text-xs mb-1.5">
                            <span style="color:rgba(255,255,255,0.55)">{{ $label }}</span>
                            <span class="font-bold" style="color:{{ $color }}">{{ $value }}</span>
                        </div>
                        <div class="prog-bar">
                            <div class="prog-fill" style="width:{{ min(100, $pct) }}%;background:{{ $color }}"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Répartition géographique ── --}}
    <div class="chart-card anim d5">
        <div class="chart-header">
            <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                Pays des apprenants
            </h3>
        </div>
        <div class="chart-body">
            @php $maxCountry = $usersByCountry->max('total') ?: 1; @endphp
            <div class="space-y-3">
                @foreach($usersByCountry->take(8) as $country)
                @php
                    $flags = ['CM'=>'🇨🇲','SN'=>'🇸🇳','CI'=>'🇨🇮','GH'=>'🇬🇭','NG'=>'🇳🇬','BJ'=>'🇧🇯','TG'=>'🇹🇬','ML'=>'🇲🇱','BF'=>'🇧🇫','CD'=>'🇨🇩'];
                    $flag  = $flags[$country['country']] ?? '🌍';
                    $pct   = round($country['total'] / $maxCountry * 100);
                @endphp
                <div class="country-row">
                    <span class="text-lg shrink-0">{{ $flag }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between text-xs mb-1">
                            <span style="color:rgba(255,255,255,0.65)">{{ $country['country'] ?: 'Inconnu' }}</span>
                            <span class="font-bold text-white">{{ $country['total'] }}</span>
                        </div>
                        <div class="prog-bar">
                            <div class="prog-fill" style="width:{{ $pct }}%;background:linear-gradient(90deg,#1a8a47,#25c26e)"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── LIGNE 3 : Top cours + Formateurs ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Top cours --}}
    <div class="chart-card anim d4">
        <div class="chart-header">
            <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">🏆 Top cours</h3>
            <span class="text-xs" style="color:rgba(255,255,255,0.3)">par inscriptions</span>
        </div>
        <div class="divide-y divide-white/5">
            @forelse($topCourses as $course)
            @php $maxEnroll = $topCourses->max('enrollments_count') ?: 1; @endphp
            <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-white/2 transition-colors">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black shrink-0"
                     style="background:rgba(232,184,75,0.1);color:#e8b84b">
                    {{ $loop->iteration }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-white truncate mb-1">{{ $course->title }}</div>
                    <div class="prog-bar">
                        <div class="prog-fill" style="width:{{ round($course->enrollments_count / $maxEnroll * 100) }}%;background:linear-gradient(90deg,#1a8a47,#25c26e)"></div>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-sm font-bold" style="color:#25c26e">{{ $course->enrollments_count }}</div>
                    <div class="text-[10px]" style="color:rgba(255,255,255,0.3)">inscrits</div>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-xs" style="color:rgba(255,255,255,0.3)">Aucun cours publié.</div>
            @endforelse
        </div>
    </div>

    {{-- Top formateurs --}}
    <div class="chart-card anim d5">
        <div class="chart-header">
            <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">👨‍🏫 Top formateurs</h3>
            <span class="text-xs" style="color:rgba(255,255,255,0.3)">par apprenants</span>
        </div>
        @php
            $avatarColors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46'];
            $maxT = $topTeachers->max('total_enrollments') ?: 1;
        @endphp
        <div class="divide-y divide-white/5">
            @forelse($topTeachers as $teacher)
            <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-white/2 transition-colors">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                     style="background:{{ $avatarColors[$loop->index % 5] }}">
                    {{ $teacher->initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-white truncate mb-1">{{ $teacher->full_name }}</div>
                    <div class="prog-bar">
                        <div class="prog-fill" style="width:{{ round($teacher->total_enrollments / $maxT * 100) }}%;background:{{ $avatarColors[$loop->index % 5] }}"></div>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-sm font-bold text-white">{{ $teacher->total_enrollments }}</div>
                    <div class="text-[10px]" style="color:rgba(255,255,255,0.3)">apprenants</div>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-xs" style="color:rgba(255,255,255,0.3)">Aucun formateur.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── TABLEAU INSCRIPTIONS DÉTAILLÉES ── --}}
<div class="chart-card anim d6">
    <div class="chart-header">
        <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
            📋 Inscriptions récentes
        </h3>
        <a href="#" class="text-xs font-semibold" style="color:#e8b84b">
            Voir tout →
        </a>
    </div>
    {{-- En-tête --}}
    <div class="flex items-center gap-4 px-6 py-2 border-b border-white/5"
         style="color:rgba(255,255,255,0.2);font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08rem">
        <span class="flex-1">Apprenant</span>
        <span class="w-40 hidden md:block">Cours</span>
        <span class="w-28 hidden lg:block">Formateur</span>
        <span class="w-20 text-right">Date</span>
    </div>
    <div class="divide-y divide-white/4">
        @forelse($recentEnrollments as $enrollment)
        <div class="flex items-center gap-4 px-6 py-3 hover:bg-white/2 transition-colors">
            <div class="flex-1 flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                     style="background:{{ $avatarColors[$loop->index % 5] }}">
                    {{ $enrollment->user->initials }}
                </div>
                <span class="text-sm font-medium text-white truncate">{{ $enrollment->user->full_name }}</span>
            </div>
            <span class="w-40 text-xs truncate hidden md:block" style="color:rgba(255,255,255,0.4)">
                {{ $enrollment->course->title }}
            </span>
            <span class="w-28 text-xs truncate hidden lg:block" style="color:rgba(255,255,255,0.35)">
                {{ $enrollment->course->teacher->full_name }}
            </span>
            <span class="w-20 text-right text-[10px]" style="color:rgba(255,255,255,0.25)">
                {{ $enrollment->enrolled_at->diffForHumans() }}
            </span>
        </div>
        @empty
        <div class="px-6 py-10 text-center text-xs" style="color:rgba(255,255,255,0.3)">
            Aucune inscription récente.
        </div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
// ── Configuration globale Chart.js ─────────────────────────────────────────
Chart.defaults.color = 'rgba(255,255,255,0.4)';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
Chart.defaults.font.family = 'Outfit, sans-serif';
Chart.defaults.font.size = 11;

const chartData = @json($chartData);

// ── 1. Graphe inscriptions ─────────────────────────────────────────────────
const enrollCtx = document.getElementById('enrollmentChart');
if (enrollCtx) {
    new Chart(enrollCtx, {
        type: 'line',
        data: {
            labels: chartData.enrollments.labels,
            datasets: [{
                label: 'Inscriptions',
                data: chartData.enrollments.data,
                borderColor: '#25c26e',
                backgroundColor: 'rgba(37,194,110,0.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#25c26e',
                tension: .4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: { maxTicksLimit: 8, color: 'rgba(255,255,255,0.3)' }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: { color: 'rgba(255,255,255,0.3)', precision: 0 },
                    beginAtZero: true,
                }
            }
        }
    });
}

// ── 2. Graphe revenus ──────────────────────────────────────────────────────
const revCtx = document.getElementById('revenueChart');
if (revCtx) {
    new Chart(revCtx, {
        type: 'bar',
        data: {
            labels: chartData.revenues.labels,
            datasets: [{
                label: 'Revenus (XAF)',
                data: chartData.revenues.data,
                backgroundColor: chartData.revenues.labels.map((_, i) =>
                    i === new Date().getMonth() ? '#e8b84b' : 'rgba(232,184,75,0.2)'
                ),
                borderColor: chartData.revenues.labels.map((_, i) =>
                    i === new Date().getMonth() ? '#e8b84b' : 'rgba(232,184,75,0.3)'
                ),
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(255,255,255,0.3)' }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: 'rgba(255,255,255,0.3)',
                        callback: v => v >= 1000 ? (v/1000)+'K' : v
                    },
                    beginAtZero: true,
                }
            }
        }
    });
}

// ── 3. Donut catégories ────────────────────────────────────────────────────
const catCtx = document.getElementById('categoryChart');
if (catCtx) {
    const catColors = ['#25c26e','#3b82f6','#e8b84b','#a78bfa','#f97316','#ec4899'];
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: chartData.categories.labels,
            datasets: [{
                data: chartData.categories.data,
                backgroundColor: catColors.slice(0, chartData.categories.data.length),
                borderColor: '#040a05',
                borderWidth: 3,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} cours`
                    }
                }
            }
        }
    });
}
</script>
@endpush