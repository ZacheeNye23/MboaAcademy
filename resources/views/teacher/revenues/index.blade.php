<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Revenus — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        body { font-family:'Outfit',sans-serif; background:#0f1f14; color:#e0ebe2; }

        /* ── Sidebar ── */
        .sidebar { width:260px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;
                   display:flex;flex-direction:column;
                   background:linear-gradient(180deg,#081409,#0a1a0f);
                   border-right:1px solid rgba(37,194,110,0.08); }
        .main-content { margin-left:260px;min-height:100vh; }
        .nav-item { display:flex;align-items:center;gap:12px;padding:10px 20px;border-radius:12px;
                    color:rgba(255,255,255,0.45);font-size:0.875rem;font-weight:500;
                    text-decoration:none;transition:all 0.2s;margin:2px 12px; }
        .nav-item:hover { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.8); }
        .nav-item.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .nav-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;
                    justify-content:center;font-size:1rem;flex-shrink:0;background:rgba(255,255,255,0.04); }
        .nav-item.active .nav-icon { background:rgba(37,194,110,0.18); }

        /* ── Surfaces ── */
        .glass  { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:18px; }
        .glass2 { background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:12px; }
        .gold-card { background:rgba(232,184,75,0.05);border:1px solid rgba(232,184,75,0.15);border-radius:16px; }

        /* ── KPI cards ── */
        .kpi { padding:20px;border-radius:16px;background:rgba(255,255,255,0.03);
               border:1px solid rgba(255,255,255,0.07);transition:all 0.25s; }
        .kpi:hover { border-color:rgba(37,194,110,0.2);transform:translateY(-2px); }
        .kpi-val { font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;line-height:1; }
        .kpi-lbl { font-size:0.72rem;color:rgba(255,255,255,0.35);margin-top:5px;
                   text-transform:uppercase;letter-spacing:.06rem; }
        .kpi-sub { font-size:0.75rem;margin-top:4px; }

        /* ── Progress bars ── */
        .prog-bar  { height:5px;border-radius:3px;background:rgba(255,255,255,0.07);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px;transition:width 0.8s ease; }

        /* ── Tabs ── */
        .tab-btn { padding:8px 18px;border-radius:10px;font-size:0.85rem;font-weight:500;
                   cursor:pointer;border:none;background:transparent;transition:all 0.2s;
                   font-family:'Outfit',sans-serif; }
        .tab-btn.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .tab-btn:not(.active) { color:rgba(255,255,255,0.45); }
        .tab-btn:not(.active):hover { color:rgba(255,255,255,0.75); }

        /* ── Table transactions ── */
        .tx-header { display:flex;align-items:center;padding:10px 18px;
                     border-bottom:1px solid rgba(255,255,255,0.07);
                     font-size:0.64rem;text-transform:uppercase;letter-spacing:.07rem;
                     font-weight:700;color:rgba(255,255,255,0.22); }
        .tx-row { display:flex;align-items:center;padding:13px 18px;
                  border-bottom:1px solid rgba(255,255,255,0.04);
                  transition:background 0.15s; }
        .tx-row:hover { background:rgba(37,194,110,0.03); }
        .tx-row:last-child { border-bottom:none; }

        /* Col widths */
        .c-date    { width:90px;flex-shrink:0; }
        .c-student { flex:1;min-width:0; }
        .c-course  { width:170px;flex-shrink:0; }
        .c-gross   { width:110px;flex-shrink:0;text-align:right; }
        .c-comm    { width:90px;flex-shrink:0;text-align:right; }
        .c-net     { width:120px;flex-shrink:0;text-align:right; }

        /* ── Btns ── */
        .btn-ghost { display:inline-flex;align-items:center;gap:7px;padding:8px 15px;
                     background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);
                     border-radius:9px;color:rgba(255,255,255,0.6);font-size:0.82rem;font-weight:500;
                     cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .btn-ghost:hover { background:rgba(255,255,255,0.08);color:#fff; }
        .btn-export { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
                      background:rgba(37,194,110,0.1);border:1px solid rgba(37,194,110,0.25);
                      border-radius:10px;color:#25c26e;font-size:0.85rem;font-weight:600;
                      cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .btn-export:hover { background:rgba(37,194,110,0.18);transform:translateY(-1px); }

        /* ── Selects & inputs ── */
        .field-select { background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);
                        border-radius:10px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.85rem;
                        padding:8px 12px;outline:none;cursor:pointer;transition:border-color 0.2s; }
        .field-select:focus { border-color:#25c26e; }
        .field-select option { background:#0f1f14; }

        /* ── Pagination ── */
        .pg-btn { padding:7px 13px;border-radius:9px;background:rgba(255,255,255,0.04);
                  border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.55);
                  font-size:0.82rem;text-decoration:none;transition:all 0.2s;display:inline-flex; }
        .pg-btn:hover { border-color:#25c26e;color:#25c26e; }
        .pg-btn.cur { background:rgba(37,194,110,0.12);border-color:#25c26e;color:#25c26e;font-weight:600; }
        .pg-btn.dis { opacity:0.3;pointer-events:none; }

        /* ── Chart container ── */
        .chart-wrap { position:relative;width:100%; }

        /* ── Trend indicator ── */
        .trend-up   { color:#25c26e;font-size:0.78rem;font-weight:700; }
        .trend-down { color:#f87171;font-size:0.78rem;font-weight:700; }
        .trend-flat { color:rgba(255,255,255,0.35);font-size:0.78rem;font-weight:700; }

        /* ── Bar chart ── */
        .bar-chart { display:flex;align-items:flex-end;gap:4px;height:120px; }
        .bar { border-radius:4px 4px 0 0;min-width:8px;flex:1;position:relative;
               transition:all 0.6s ease;cursor:pointer; }
        .bar:hover { opacity:0.85; }
        .bar-tip { position:absolute;bottom:calc(100%+4px);left:50%;transform:translateX(-50%);
                   background:#0d1f13;border:1px solid rgba(255,255,255,0.1);border-radius:7px;
                   padding:3px 9px;font-size:0.68rem;white-space:nowrap;
                   opacity:0;transition:opacity 0.15s;pointer-events:none;z-index:10; }
        .bar:hover .bar-tip { opacity:1; }

        /* ── Section separator ── */
        .sec-head { font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;
                    color:#fff;margin-bottom:3px; }
        .sec-sub  { font-size:0.75rem;color:rgba(255,255,255,0.35);margin-bottom:16px; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .anim-1{animation-delay:.04s}.anim-2{animation-delay:.08s}
        .anim-3{animation-delay:.12s}.anim-4{animation-delay:.16s}.anim-5{animation-delay:.2s}
    </style>
</head>
<body x-data="revenuePage()">

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>

    {{-- Total sidebar --}}
    <div style="padding:14px 20px;border-bottom:1px solid rgba(255,255,255,0.05);">
        <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:.08rem;color:rgba(255,255,255,0.2);margin-bottom:6px;">Total cumulé</div>
        <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:#e8b84b;">
            {{ number_format($totalRevenue, 0, ',', ' ') }}
        </div>
        <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);">XAF nets reçus</div>
    </div>

    <nav style="flex:1;padding:16px 0;overflow-y:auto;">
        <a href="{{ route('teacher.dashboard') }}"    class="nav-item"><span class="nav-icon">🏠</span>Dashboard</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="nav-icon">📚</span>Mes cours</a>
        <a href="{{ route('teacher.quizzes.index') }}" class="nav-item"><span class="nav-icon">📝</span>Quiz</a>
        <a href="{{ route('teacher.students.index') }}" class="nav-item"><span class="nav-icon">👥</span>Apprenants</a>
        <a href="{{ route('teacher.revenues.index') }}" class="nav-item active"><span class="nav-icon">💰</span>Revenus</a>
    </nav>

    <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,0.05);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:rgba(239,68,68,0.07);color:rgba(239,68,68,0.75);border:none;cursor:pointer;">
                <span class="nav-icon" style="background:rgba(239,68,68,0.08);">🚪</span>Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ═══ MAIN ═══ --}}
<div class="main-content">

    {{-- Topbar --}}
    <header style="position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;padding:14px 32px;border-bottom:1px solid rgba(37,194,110,0.08);background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);">
        <div>
            <h1 style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:#fff;">Revenus</h1>
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:2px;">
                {{ $totalTx }} transaction(s) · {{ $totalStudents }} apprenant(s) payant(s)
            </p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            {{-- Filtre année --}}
            <form method="GET" action="{{ route('teacher.revenues.index') }}" style="display:flex;gap:8px;align-items:center;">
                <select name="year" class="field-select" onchange="this.form.submit()">
                    @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <select name="course" class="field-select" onchange="this.form.submit()">
                    <option value="all" {{ $courseFilter==='all'?'selected':'' }}>Tous les cours</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ $courseFilter==$c->id?'selected':'' }}>{{ Str::limit($c->title,28) }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('teacher.revenues.export', array_filter(['year'=>$year,'course'=>$courseFilter!=='all'?$courseFilter:null])) }}"
               class="btn-export">⬇ CSV</a>
        </div>
    </header>

    <div style="padding:28px 32px;">

        {{-- ── KPIs ── --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">

            {{-- Net ce mois --}}
            <div class="kpi anim anim-1">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(232,184,75,0.15);display:flex;align-items:center;justify-content:center;font-size:1rem;">💰</div>
                    <span class="{{ $variation >= 0 ? 'trend-up' : 'trend-down' }}">
                        {{ $variation >= 0 ? '↑' : '↓' }} {{ abs($variation) }}%
                    </span>
                </div>
                <div class="kpi-val" style="color:#e8b84b;">{{ number_format($thisMonth,0,',',' ') }}</div>
                <div class="kpi-lbl">XAF nets — ce mois</div>
                <div class="kpi-sub" style="color:rgba(255,255,255,0.3);">vs {{ number_format($lastMonth,0,',',' ') }} XAF mois dernier</div>
            </div>

            {{-- Total cumulé --}}
            <div class="kpi anim anim-2">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(37,194,110,0.15);display:flex;align-items:center;justify-content:center;font-size:1rem;margin-bottom:12px;">📈</div>
                <div class="kpi-val" style="color:#25c26e;">{{ number_format($totalRevenue,0,',',' ') }}</div>
                <div class="kpi-lbl">XAF nets — total</div>
                <div class="kpi-sub" style="color:rgba(255,255,255,0.3);">Brut : {{ number_format($totalGross,0,',',' ') }} XAF</div>
            </div>

            {{-- Transactions --}}
            <div class="kpi anim anim-3">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(59,130,246,0.15);display:flex;align-items:center;justify-content:center;font-size:1rem;margin-bottom:12px;">🔢</div>
                <div class="kpi-val" style="color:#3b82f6;">{{ $totalTx }}</div>
                <div class="kpi-lbl">Transactions</div>
                <div class="kpi-sub" style="color:rgba(255,255,255,0.3);">
                    Moy. : {{ $totalTx > 0 ? number_format($totalRevenue / $totalTx, 0, ',', ' ') : 0 }} XAF
                </div>
            </div>

            {{-- Apprenants payants --}}
            <div class="kpi anim anim-4">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(167,139,250,0.15);display:flex;align-items:center;justify-content:center;font-size:1rem;margin-bottom:12px;">👥</div>
                <div class="kpi-val" style="color:#a78bfa;">{{ $totalStudents }}</div>
                <div class="kpi-lbl">Apprenants payants</div>
                <div class="kpi-sub" style="color:rgba(255,255,255,0.3);">
                    Valeur moy. : {{ $totalStudents > 0 ? number_format($totalRevenue / $totalStudents, 0, ',', ' ') : 0 }} XAF
                </div>
            </div>
        </div>

        {{-- ── GRAPHIQUE + RÉPARTITION ── --}}
        <div style="display:grid;grid-template-columns:1fr 320px;gap:18px;margin-bottom:24px;">

            {{-- Graphique mensuel / trimestriel --}}
            <div class="glass anim anim-2" style="padding:22px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                    <div>
                        <div class="sec-head">Évolution {{ $year }}</div>
                        <div class="sec-sub" style="margin:0;">Revenus nets en XAF</div>
                    </div>
                    <div style="display:flex;gap:4px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:4px;">
                        <button type="button"
                                :class="period==='monthly'?'active':''"
                                x-on:click="period='monthly'"
                                class="tab-btn" style="padding:5px 12px;font-size:0.78rem;">Mensuel</button>
                        <button type="button"
                                :class="period==='quarterly'?'active':''"
                                x-on:click="period='quarterly'"
                                class="tab-btn" style="padding:5px 12px;font-size:0.78rem;">Trimestriel</button>
                    </div>
                </div>

                {{-- Canvas Chart.js --}}
                <div class="chart-wrap" style="height:220px;">
                    <canvas id="revenueChart"></canvas>
                </div>

                {{-- Légende bas --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.3);">
                        Meilleur mois :
                        <strong style="color:#e8b84b;">{{ $bestMonth['label'] }} — {{ number_format($bestMonth['total'],0,',',' ') }} XAF</strong>
                    </div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.3);">
                        Total {{ $year }} :
                        <strong style="color:#25c26e;">{{ number_format($monthlyChart->sum('total'),0,',',' ') }} XAF</strong>
                    </div>
                </div>
            </div>

            {{-- Répartition par cours --}}
            <div class="glass anim anim-3" style="padding:22px;">
                <div class="sec-head">Par cours</div>
                <div class="sec-sub">Répartition des revenus nets</div>

                @php
                    $barColors = ['#25c26e','#3b82f6','#e8b84b','#a78bfa','#f97316','#ec4899','#14b8a6'];
                @endphp

                @forelse($byCourse as $i => $bc)
                @php
                    $pct   = $totalRevenue > 0 ? round($bc->total / $totalRevenue * 100) : 0;
                    $color = $barColors[$i % count($barColors)];
                @endphp
                <div style="margin-bottom:14px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:0.78rem;color:rgba(255,255,255,0.65);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:170px;" title="{{ $bc->course->title }}">
                            {{ Str::limit($bc->course->title, 22) }}
                        </span>
                        <span style="font-size:0.78rem;font-weight:700;color:{{ $color }};flex-shrink:0;margin-left:6px;">{{ $pct }}%</span>
                    </div>
                    <div class="prog-bar">
                        <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:3px;">
                        <span style="font-size:0.67rem;color:rgba(255,255,255,0.25);">{{ $bc->students }} apprenant(s)</span>
                        <span style="font-size:0.7rem;font-weight:600;color:{{ $color }};">{{ number_format($bc->total,0,',',' ') }} XAF</span>
                    </div>
                </div>
                @empty
                <div style="padding:30px 0;text-align:center;">
                    <div style="font-size:2.5rem;margin-bottom:8px;opacity:0.4;">💰</div>
                    <div style="font-size:0.85rem;color:rgba(255,255,255,0.35);">Aucun revenu enregistré</div>
                </div>
                @endforelse

                {{-- Total --}}
                @if($byCourse->count() > 0)
                <div style="margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.06);">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:0.78rem;color:rgba(255,255,255,0.4);">Total net</span>
                        <span style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#e8b84b;">
                            {{ number_format($totalRevenue,0,',',' ') }} XAF
                        </span>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:4px;">
                        <span style="font-size:0.72rem;color:rgba(255,255,255,0.3);">Commission plateforme (20%)</span>
                        <span style="font-size:0.78rem;color:rgba(255,255,255,0.35);">
                            {{ number_format($totalGross - $totalRevenue,0,',',' ') }} XAF
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── TABS ── --}}
        <div style="display:flex;gap:4px;margin-bottom:20px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:5px;width:fit-content;" class="anim anim-3">
            <button class="tab-btn" :class="tab==='transactions'?'active':''" x-on:click="tab='transactions'">
                📋 Transactions ({{ $transactions->total() }})
            </button>
            <button class="tab-btn" :class="tab==='summary'?'active':''" x-on:click="tab='summary'">
                📊 Récapitulatif mensuel
            </button>
        </div>

        {{-- ═══ TAB : TRANSACTIONS ═══ --}}
        <div x-show="tab === 'transactions'" x-transition>
            <div class="glass anim anim-4" style="overflow:hidden;">

                {{-- En-tête table --}}
                <div class="tx-header">
                    <div class="c-date">Date</div>
                    <div class="c-student">Apprenant</div>
                    <div class="c-course">Cours</div>
                    <div class="c-gross">Montant brut</div>
                    <div class="c-comm">Commission</div>
                    <div class="c-net">Net reçu</div>
                </div>

                @php
                    $avatarColors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e'];
                @endphp

                @forelse($transactions as $i => $tx)
                @php $ac = $avatarColors[$i % count($avatarColors)]; @endphp
                <div class="tx-row">
                    {{-- Date --}}
                    <div class="c-date">
                        <div style="font-size:0.82rem;color:#fff;font-weight:500;">
                            {{ $tx->paid_at?->format('d/m/Y') ?? '—' }}
                        </div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.25);">
                            {{ $tx->paid_at?->diffForHumans() ?? '' }}
                        </div>
                    </div>

                    {{-- Apprenant --}}
                    <div class="c-student" style="display:flex;align-items:center;gap:9px;min-width:0;">
                        <div style="width:32px;height:32px;border-radius:50%;background:{{ $ac }};display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ $tx->student->initials ?? '?' }}
                        </div>
                        <div style="min-width:0;">
                            <div style="font-size:0.82rem;font-weight:500;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $tx->student->full_name ?? '—' }}
                            </div>
                            <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $tx->student->email ?? '' }}
                            </div>
                        </div>
                    </div>

                    {{-- Cours --}}
                    <div class="c-course" style="min-width:0;">
                        <div style="font-size:0.78rem;color:rgba(255,255,255,0.7);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ Str::limit($tx->course->title ?? '—', 22) }}
                        </div>
                    </div>

                    {{-- Montant brut --}}
                    <div class="c-gross">
                        <div style="font-size:0.82rem;color:rgba(255,255,255,0.55);">
                            {{ number_format($tx->amount,0,',',' ') }}
                            <span style="font-size:0.65rem;color:rgba(255,255,255,0.25);">XAF</span>
                        </div>
                    </div>

                    {{-- Commission --}}
                    <div class="c-comm">
                        <div style="font-size:0.78rem;color:rgba(239,68,68,0.6);">
                            -{{ number_format($tx->commission,0,',',' ') }}
                            <span style="font-size:0.62rem;">XAF</span>
                        </div>
                    </div>

                    {{-- Net --}}
                    <div class="c-net">
                        <div style="font-size:0.88rem;font-weight:700;color:#e8b84b;">
                            {{ number_format($tx->net_amount,0,',',' ') }}
                            <span style="font-size:0.65rem;font-weight:400;color:rgba(255,255,255,0.3);">XAF</span>
                        </div>
                        <div style="font-size:0.62rem;color:rgba(255,255,255,0.2);">{{ $tx->currency }}</div>
                    </div>
                </div>
                @empty
                <div style="padding:60px 20px;text-align:center;">
                    <div style="font-size:4rem;margin-bottom:14px;">💰</div>
                    <div style="font-size:0.95rem;font-weight:600;color:rgba(255,255,255,0.45);margin-bottom:6px;">Aucune transaction</div>
                    <div style="font-size:0.82rem;color:rgba(255,255,255,0.28);">Les ventes de vos cours apparaîtront ici.</div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($transactions->hasPages())
            <div style="margin-top:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <div style="font-size:0.78rem;color:rgba(255,255,255,0.3);">
                    {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} sur {{ $transactions->total() }}
                </div>
                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                    @if($transactions->onFirstPage())
                    <span class="pg-btn dis">← Préc.</span>
                    @else
                    <a href="{{ $transactions->previousPageUrl() }}" class="pg-btn">← Préc.</a>
                    @endif

                    @foreach($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                    @if($page === $transactions->currentPage())
                    <span class="pg-btn cur">{{ $page }}</span>
                    @elseif(abs($page - $transactions->currentPage()) <= 2 || $page === 1 || $page === $transactions->lastPage())
                    <a href="{{ $url }}" class="pg-btn">{{ $page }}</a>
                    @elseif(abs($page - $transactions->currentPage()) === 3)
                    <span style="padding:7px 4px;color:rgba(255,255,255,0.2);font-size:0.82rem;">…</span>
                    @endif
                    @endforeach

                    @if($transactions->hasMorePages())
                    <a href="{{ $transactions->nextPageUrl() }}" class="pg-btn">Suiv. →</a>
                    @else
                    <span class="pg-btn dis">Suiv. →</span>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- ═══ TAB : RÉCAPITULATIF MENSUEL ═══ --}}
        <div x-show="tab === 'summary'" x-transition>
            <div class="glass anim anim-4" style="overflow:hidden;">

                {{-- En-tête --}}
                <div style="display:flex;align-items:center;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,0.07);
                            font-size:0.64rem;text-transform:uppercase;letter-spacing:.07rem;font-weight:700;color:rgba(255,255,255,0.22);">
                    <div style="width:80px;">Mois</div>
                    <div style="flex:1;">Progression</div>
                    <div style="width:80px;text-align:center;">Tx</div>
                    <div style="width:130px;text-align:right;">Montant net</div>
                    <div style="width:80px;text-align:right;">Variation</div>
                </div>

                @php $maxMonthly = $monthlyChart->max('total') ?: 1; $prevTotal = 0; @endphp
                @foreach($monthlyChart as $mData)
                @php
                    $isCurrentMonth = ($mData['month'] == now()->month && $year == now()->year);
                    $variation = $prevTotal > 0 ? round(($mData['total'] - $prevTotal) / $prevTotal * 100, 1) : null;
                    $barW = $maxMonthly > 0 ? max(2, round($mData['total'] / $maxMonthly * 100)) : 2;
                @endphp
                <div style="display:flex;align-items:center;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,0.04);transition:background 0.15s;{{ $isCurrentMonth ? 'background:rgba(37,194,110,0.03)' : '' }}"
                     onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='{{ $isCurrentMonth ? 'rgba(37,194,110,0.03)' : '' }}'">

                    {{-- Mois --}}
                    <div style="width:80px;">
                        <div style="font-size:0.85rem;font-weight:{{ $isCurrentMonth ? '600' : '400' }};color:{{ $isCurrentMonth ? '#fff' : 'rgba(255,255,255,0.6)' }};">{{ $mData['label'] }}</div>
                        @if($isCurrentMonth)
                        <div style="font-size:0.62rem;color:#25c26e;">En cours</div>
                        @endif
                    </div>

                    {{-- Barre --}}
                    <div style="flex:1;padding-right:16px;">
                        @if($mData['total'] > 0)
                        <div class="prog-bar" style="height:6px;">
                            <div class="prog-fill" style="width:{{ $barW }}%;background:{{ $isCurrentMonth ? '#e8b84b' : '#25c26e' }};opacity:{{ $mData['total'] > 0 ? '1' : '0.3' }};"></div>
                        </div>
                        @else
                        <div style="font-size:0.72rem;color:rgba(255,255,255,0.18);">—</div>
                        @endif
                    </div>

                    {{-- Transactions --}}
                    <div style="width:80px;text-align:center;font-size:0.78rem;color:{{ $mData['tx'] > 0 ? 'rgba(255,255,255,0.6)' : 'rgba(255,255,255,0.2)' }};">
                        {{ $mData['tx'] > 0 ? $mData['tx'] : '—' }}
                    </div>

                    {{-- Montant --}}
                    <div style="width:130px;text-align:right;">
                        @if($mData['total'] > 0)
                        <span style="font-size:0.88rem;font-weight:600;color:{{ $isCurrentMonth ? '#e8b84b' : '#fff' }};">
                            {{ number_format($mData['total'],0,',',' ') }}
                        </span>
                        <span style="font-size:0.62rem;color:rgba(255,255,255,0.25);">XAF</span>
                        @else
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.18);">0 XAF</span>
                        @endif
                    </div>

                    {{-- Variation --}}
                    <div style="width:80px;text-align:right;font-size:0.75rem;font-weight:700;">
                        @if($variation !== null && $mData['total'] > 0)
                        <span class="{{ $variation >= 0 ? 'trend-up' : 'trend-down' }}">
                            {{ $variation >= 0 ? '↑' : '↓' }} {{ abs($variation) }}%
                        </span>
                        @else
                        <span style="color:rgba(255,255,255,0.2);">—</span>
                        @endif
                    </div>
                </div>
                @php $prevTotal = $mData['total']; @endphp
                @endforeach

                {{-- Total annuel --}}
                <div style="display:flex;align-items:center;padding:14px 20px;background:rgba(232,184,75,0.05);border-top:1px solid rgba(232,184,75,0.1);">
                    <div style="width:80px;font-size:0.85rem;font-weight:700;color:#e8b84b;">Total {{ $year }}</div>
                    <div style="flex:1;"></div>
                    <div style="width:80px;text-align:center;font-size:0.82rem;font-weight:600;color:rgba(255,255,255,0.6);">
                        {{ $monthlyChart->sum('tx') }}
                    </div>
                    <div style="width:130px;text-align:right;">
                        <span style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#e8b84b;">
                            {{ number_format($monthlyChart->sum('total'),0,',',' ') }}
                        </span>
                        <span style="font-size:0.65rem;color:rgba(255,255,255,0.3);">XAF</span>
                    </div>
                    <div style="width:80px;"></div>
                </div>
            </div>
        </div>

    </div>{{-- end padding --}}
</div>

{{-- ═══ Chart.js ═══ --}}
<script>
const monthlyData    = @json($monthlyChart->values());
const quarterlyData  = @json($quarterlyChart->values());
const PASSING        = {{ $totalRevenue > 0 ? round($totalRevenue / 12) : 0 }};

let chart = null;

function buildChart(period) {
    const ctx  = document.getElementById('revenueChart');
    if (!ctx) return;

    const data   = period === 'quarterly' ? quarterlyData : monthlyData;
    const labels = data.map(d => d.label || d.month);
    const totals = data.map(d => d.total);
    const maxVal = Math.max(...totals, 1);
    const curMonth = {{ now()->month }};
    const selYear  = {{ $year }};
    const nowYear  = {{ now()->year }};

    const bgColors = data.map((d, i) => {
        const isActive = period === 'monthly'
            ? (d.month === curMonth && selYear === nowYear)
            : false;
        return isActive ? 'rgba(232,184,75,0.8)' : 'rgba(37,194,110,0.55)';
    });

    const borderColors = bgColors.map(c => c.replace('0.55','0.9').replace('0.8','1'));

    if (chart) chart.destroy();

    chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Revenus nets (XAF)',
                data: totals,
                backgroundColor: bgColors,
                borderColor: borderColors,
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0d1f13',
                    borderColor: 'rgba(37,194,110,0.3)',
                    borderWidth: 1,
                    titleColor: 'rgba(255,255,255,0.7)',
                    bodyColor: '#e8b84b',
                    callbacks: {
                        label: ctx => {
                            const v = ctx.parsed.y;
                            return ' ' + new Intl.NumberFormat('fr-FR').format(v) + ' XAF';
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(255,255,255,0.35)', font: { size: 11, family: 'Outfit' } },
                    border: { display: false },
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    border: { display: false },
                    ticks: {
                        color: 'rgba(255,255,255,0.3)',
                        font: { size: 11, family: 'Outfit' },
                        callback: v => new Intl.NumberFormat('fr-FR', { notation:'compact' }).format(v),
                    },
                },
            },
        },
    });
}

function revenuePage() {
    return {
        tab:    'transactions',
        period: '{{ $period }}',

        init() { this.$nextTick(() => buildChart(this.period)); },

        get monthlyData()    { return monthlyData; },
        get quarterlyData()  { return quarterlyData; },
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('revenuePage', revenuePage);
});

// Rebuild chart quand period change
document.addEventListener('alpine:initialized', () => {
    const el = document.querySelector('[x-data="revenuePage()"]');
    if (!el) return;
    const observer = new MutationObserver(() => {});

    // Watch period changes via custom event
    el.addEventListener('period-changed', e => buildChart(e.detail));
});

// Écouter le changement de period via Alpine
document.addEventListener('DOMContentLoaded', () => {
    buildChart('{{ $period }}');

    // Refaire le chart quand on clique sur Mensuel/Trimestriel
    document.querySelectorAll('[x-on\\:click*="period="]').forEach(btn => {
        btn.addEventListener('click', () => {
            const p = btn.getAttribute('x-on:click').includes('monthly') ? 'monthly' : 'quarterly';
            setTimeout(() => buildChart(p), 50);
        });
    });
});
</script>
</body>
</html>