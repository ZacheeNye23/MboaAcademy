<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Statistiques — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        body { font-family:'Outfit',sans-serif; background:#0f1f14; color:#e0ebe2; }
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

        /* ── KPI ── */
        .kpi { padding:18px 20px;border-radius:16px;background:rgba(255,255,255,0.03);
               border:1px solid rgba(255,255,255,0.07);transition:all 0.25s;position:relative;overflow:hidden; }
        .kpi:hover { border-color:rgba(37,194,110,0.2);transform:translateY(-2px); }
        .kpi-glow { position:absolute;top:-20px;right:-20px;width:80px;height:80px;
                    border-radius:50%;opacity:0.06;pointer-events:none; }

        /* ── Progress ── */
        .prog-bar  { height:5px;border-radius:3px;background:rgba(255,255,255,0.07);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px;transition:width 0.8s ease; }
        .prog-thin { height:4px; }

        /* ── Trend ── */
        .trend-up   { color:#25c26e;font-size:0.75rem;font-weight:700; }
        .trend-down { color:#f87171;font-size:0.75rem;font-weight:700; }
        .trend-flat { color:rgba(255,255,255,0.3);font-size:0.75rem;font-weight:700; }

        /* ── Filter pills ── */
        .period-btn { padding:6px 14px;border-radius:100px;font-size:0.78rem;font-weight:500;
                      cursor:pointer;border:1px solid rgba(255,255,255,0.1);background:transparent;
                      color:rgba(255,255,255,0.45);transition:all 0.2s;font-family:'Outfit',sans-serif;
                      text-decoration:none;display:inline-block; }
        .period-btn.active { background:rgba(37,194,110,0.12);border-color:#25c26e;color:#25c26e; }
        .period-btn:hover:not(.active) { border-color:rgba(255,255,255,0.25);color:rgba(255,255,255,0.75); }

        /* ── Heat bar (heure) ── */
        .heat-bar { border-radius:3px 3px 0 0;min-width:6px;flex:1;cursor:default;position:relative; }
        .heat-bar:hover::after { content:attr(data-tip);position:absolute;bottom:calc(100%+4px);
                                  left:50%;transform:translateX(-50%);background:#0d1f13;
                                  border:1px solid rgba(255,255,255,0.1);border-radius:7px;
                                  padding:3px 8px;font-size:0.67rem;white-space:nowrap;z-index:10; }

        /* ── Dropoff bar ── */
        .dropoff-bar { display:flex;align-items:center;gap:10px;margin-bottom:10px; }

        /* ── Btn ── */
        .field-select { background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);
                        border-radius:10px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.85rem;
                        padding:8px 12px;outline:none;cursor:pointer;transition:border-color 0.2s; }
        .field-select:focus { border-color:#25c26e; }
        .field-select option { background:#0f1f14; }

        /* ── Section ── */
        .sec-title { font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff; }
        .sec-sub   { font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:2px;margin-bottom:14px; }

        /* ── Tabs ── */
        .tab-btn { padding:7px 16px;border-radius:9px;font-size:0.83rem;font-weight:500;cursor:pointer;
                   border:none;background:transparent;transition:all 0.2s;font-family:'Outfit',sans-serif; }
        .tab-btn.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .tab-btn:not(.active) { color:rgba(255,255,255,0.45); }
        .tab-btn:not(.active):hover { color:rgba(255,255,255,0.75); }

        /* ── Ring ── */
        .ring-svg { transform:rotate(-90deg); }

        @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .anim-1{animation-delay:.04s}.anim-2{animation-delay:.08s}.anim-3{animation-delay:.12s}
        .anim-4{animation-delay:.16s}.anim-5{animation-delay:.2s}.anim-6{animation-delay:.24s}

        /* ── Grid helpers ── */
        .grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:18px; }
        .grid-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px; }
        .grid-4 { display:grid;grid-template-columns:repeat(4,1fr);gap:14px; }
        @media(max-width:1100px){ .grid-4{grid-template-columns:1fr 1fr} }
    </style>
</head>
<body x-data="statsPage()">

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>

    <nav style="flex:1;padding:16px 0;overflow-y:auto;">
        <a href="{{ route('teacher.dashboard') }}"      class="nav-item"><span class="nav-icon">🏠</span>Dashboard</a>
        <a href="{{ route('teacher.courses.index') }}"  class="nav-item"><span class="nav-icon">📚</span>Mes cours</a>
        <a href="{{ route('teacher.quizzes.index') }}"  class="nav-item"><span class="nav-icon">📝</span>Quiz</a>
        <a href="{{ route('teacher.students.index') }}" class="nav-item"><span class="nav-icon">👥</span>Apprenants</a>
        <a href="{{ route('teacher.revenues.index') }}" class="nav-item"><span class="nav-icon">💰</span>Revenus</a>
        <a href="{{ route('teacher.statistics.index') }}" class="nav-item active"><span class="nav-icon">📊</span>Statistiques</a>
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
            <h1 style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:#fff;">Statistiques générales</h1>
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:2px;">
                Vue d'ensemble de l'engagement et de la progression de vos apprenants
            </p>
        </div>

        {{-- Filtres --}}
        <form method="GET" action="{{ route('teacher.statistics.index') }}" style="display:flex;align-items:center;gap:10px;">
            {{-- Période --}}
            <div style="display:flex;gap:4px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:4px;">
                @foreach(['7'=>'7j','30'=>'30j','90'=>'3m','365'=>'1an'] as $val => $lbl)
                <button type="submit" name="period" value="{{ $val }}"
                        class="period-btn {{ $period === $val ? 'active' : '' }}"
                        style="padding:5px 10px;">
                    {{ $lbl }}
                </button>
                @endforeach
            </div>
            {{-- Cours --}}
            <select name="course" class="field-select" onchange="this.form.submit()">
                <option value="all" {{ $courseFilter==='all'?'selected':'' }}>Tous les cours</option>
                @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ $courseFilter==$c->id?'selected':'' }}>{{ Str::limit($c->title,28) }}</option>
                @endforeach
            </select>
            <input type="hidden" name="period" value="{{ $period }}">
        </form>
    </header>

    <div style="padding:26px 32px;">

        {{-- ── KPIs (8 cartes) ── --}}
        <div class="grid-4 anim anim-1" style="margin-bottom:22px;">

            {{-- Inscriptions période --}}
            <div class="kpi">
                <div class="kpi-glow" style="background:#25c26e;"></div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <div style="width:34px;height:34px;border-radius:9px;background:rgba(37,194,110,0.15);display:flex;align-items:center;justify-content:center;font-size:0.9rem;">📈</div>
                    <span class="{{ $enrollGrowth >= 0 ? 'trend-up' : 'trend-down' }}">
                        {{ $enrollGrowth >= 0 ? '↑' : '↓' }} {{ abs($enrollGrowth) }}%
                    </span>
                </div>
                <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#25c26e;line-height:1;">{{ $periodEnrollments }}</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);margin-top:4px;text-transform:uppercase;letter-spacing:.05rem;">Inscriptions ({{ $period }}j)</div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.25);margin-top:3px;">Total : {{ $totalEnrollments }}</div>
            </div>

            {{-- Taux complétion --}}
            <div class="kpi">
                <div class="kpi-glow" style="background:#3b82f6;"></div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <div style="width:34px;height:34px;border-radius:9px;background:rgba(59,130,246,0.15);display:flex;align-items:center;justify-content:center;font-size:0.9rem;">✅</div>
                    <span style="font-size:0.72rem;padding:2px 8px;border-radius:100px;background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2);">{{ $completedCount }} finis</span>
                </div>
                <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#3b82f6;line-height:1;">{{ $completionRate }}%</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);margin-top:4px;text-transform:uppercase;letter-spacing:.05rem;">Taux complétion</div>
                <div class="prog-bar prog-thin" style="margin-top:8px;">
                    <div class="prog-fill" style="width:{{ $completionRate }}%;background:#3b82f6;"></div>
                </div>
            </div>

            {{-- Progression moyenne --}}
            <div class="kpi">
                <div class="kpi-glow" style="background:#a78bfa;"></div>
                <div style="width:34px;height:34px;border-radius:9px;background:rgba(167,139,250,0.15);display:flex;align-items:center;justify-content:center;font-size:0.9rem;margin-bottom:10px;">📊</div>
                <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#a78bfa;line-height:1;">{{ $avgProgress }}%</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);margin-top:4px;text-transform:uppercase;letter-spacing:.05rem;">Progression moy.</div>
                <div class="prog-bar prog-thin" style="margin-top:8px;">
                    <div class="prog-fill" style="width:{{ $avgProgress }}%;background:#a78bfa;"></div>
                </div>
            </div>

            {{-- Leçons complétées --}}
            <div class="kpi">
                <div class="kpi-glow" style="background:#e8b84b;"></div>
                <div style="width:34px;height:34px;border-radius:9px;background:rgba(232,184,75,0.15);display:flex;align-items:center;justify-content:center;font-size:0.9rem;margin-bottom:10px;">🎓</div>
                <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#e8b84b;line-height:1;">{{ number_format($lessonsCompleted) }}</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);margin-top:4px;text-transform:uppercase;letter-spacing:.05rem;">Leçons terminées</div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.25);margin-top:3px;">par {{ $totalEnrollments }} apprenants</div>
            </div>

            {{-- Taux réussite quiz --}}
            <div class="kpi">
                <div style="width:34px;height:34px;border-radius:9px;background:rgba(37,194,110,0.15);display:flex;align-items:center;justify-content:center;font-size:0.9rem;margin-bottom:10px;">🏆</div>
                <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:{{ $quizPassRate >= 60 ? '#25c26e' : '#f87171' }};line-height:1;">{{ $quizPassRate }}%</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);margin-top:4px;text-transform:uppercase;letter-spacing:.05rem;">Réussite quiz</div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.25);margin-top:3px;">Score moy. : {{ $avgQuizScore }}%</div>
            </div>

            {{-- Engagement forum --}}
            <div class="kpi">
                <div style="width:34px;height:34px;border-radius:9px;background:rgba(167,139,250,0.15);display:flex;align-items:center;justify-content:center;font-size:0.9rem;margin-bottom:10px;">💬</div>
                <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#a78bfa;line-height:1;">{{ $forumPosts }}</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);margin-top:4px;text-transform:uppercase;letter-spacing:.05rem;">Posts forum</div>
            </div>

            {{-- Cours terminés période --}}
            <div class="kpi">
                <div style="width:34px;height:34px;border-radius:9px;background:rgba(59,130,246,0.15);display:flex;align-items:center;justify-content:center;font-size:0.9rem;margin-bottom:10px;">🎯</div>
                <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#60a5fa;line-height:1;">{{ $periodCompleted }}</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);margin-top:4px;text-transform:uppercase;letter-spacing:.05rem;">Cours terminés ({{ $period }}j)</div>
            </div>

            {{-- Score moyen quiz --}}
            <div class="kpi">
                <div style="width:34px;height:34px;border-radius:9px;background:rgba(249,115,22,0.15);display:flex;align-items:center;justify-content:center;font-size:0.9rem;margin-bottom:10px;">📝</div>
                <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#fb923c;line-height:1;">{{ $avgQuizScore }}%</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);margin-top:4px;text-transform:uppercase;letter-spacing:.05rem;">Score moyen quiz</div>
            </div>
        </div>

        {{-- ── LIGNE 1 : Évolution inscriptions + Activité hebdo ── --}}
        <div class="grid-2 anim anim-2" style="margin-bottom:20px;">

            {{-- Graphique inscriptions --}}
            <div class="glass" style="padding:22px;">
                <div class="sec-title">Évolution des inscriptions</div>
                <div class="sec-sub">{{ $period }} derniers jours — {{ $periodEnrollments }} nouvelle(s)</div>
                @if($enrollChart->count() > 0)
                <div style="position:relative;height:180px;">
                    <canvas id="enrollChart"></canvas>
                </div>
                @else
                <div style="height:180px;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;">
                    <div style="font-size:2.5rem;opacity:0.4;">📈</div>
                    <div style="font-size:0.82rem;color:rgba(255,255,255,0.3);">Aucune inscription sur la période</div>
                </div>
                @endif
                {{-- Comparaison --}}
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:0.75rem;color:rgba(255,255,255,0.35);">Période précédente : {{ $prevEnrollments }}</span>
                    <span class="{{ $enrollGrowth >= 0 ? 'trend-up' : ($enrollGrowth < 0 ? 'trend-down' : 'trend-flat') }}">
                        {{ $enrollGrowth > 0 ? '↑ +' : ($enrollGrowth < 0 ? '↓ ' : '') }}{{ $enrollGrowth }}%
                    </span>
                </div>
            </div>

            {{-- Activité hebdomadaire empilée --}}
            <div class="glass" style="padding:22px;">
                <div class="sec-title">Engagement hebdomadaire</div>
                <div class="sec-sub">Leçons / Quiz / Forum — 12 dernières semaines</div>
                <div style="position:relative;height:180px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
                {{-- Légende --}}
                <div style="display:flex;gap:14px;margin-top:10px;">
                    @foreach([['#25c26e','Leçons'],['#3b82f6','Quiz'],['#a78bfa','Forum']] as [$c,$l])
                    <div style="display:flex;align-items:center;gap:5px;">
                        <div style="width:10px;height:10px;border-radius:2px;background:{{ $c }};"></div>
                        <span style="font-size:0.72rem;color:rgba(255,255,255,0.45);">{{ $l }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── LIGNE 2 : Taux complétion par cours + Drop-off chapitres ── --}}
        <div class="grid-2 anim anim-3" style="margin-bottom:20px;">

            {{-- Complétion par cours --}}
            <div class="glass" style="padding:22px;">
                <div class="sec-title">Taux de complétion par cours</div>
                <div class="sec-sub">{{ $completionByCourse->count() }} cours · triés par inscriptions</div>

                @forelse($completionByCourse as $i => $cc)
                @php
                    $colors = ['#25c26e','#3b82f6','#e8b84b','#a78bfa','#f97316','#ec4899'];
                    $c = $colors[$i % count($colors)];
                @endphp
                <div style="margin-bottom:14px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:0.8rem;color:rgba(255,255,255,0.7);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $cc['title'] }}">
                            {{ Str::limit($cc['title'], 24) }}
                        </span>
                        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                            <span style="font-size:0.7rem;color:rgba(255,255,255,0.3);">{{ $cc['enrollments'] }} inscrits</span>
                            <span style="font-size:0.78rem;font-weight:700;color:{{ $c }};">{{ $cc['rate'] }}%</span>
                        </div>
                    </div>
                    {{-- Double barre (progression moy + taux complétion) --}}
                    <div style="position:relative;">
                        <div class="prog-bar" style="height:8px;border-radius:4px;">
                            <div class="prog-fill" style="width:{{ $cc['avg_progress'] }}%;background:{{ $c }};opacity:0.35;border-radius:4px;"></div>
                        </div>
                        <div class="prog-bar" style="height:8px;border-radius:4px;margin-top:2px;">
                            <div class="prog-fill" style="width:{{ $cc['rate'] }}%;background:{{ $c }};border-radius:4px;"></div>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:3px;">
                        <span style="font-size:0.62rem;color:rgba(255,255,255,0.22);">Progression moy. {{ $cc['avg_progress'] }}%</span>
                        <span style="font-size:0.62rem;color:{{ $c }};">{{ $cc['completed'] }} terminés</span>
                    </div>
                </div>
                @empty
                <div style="padding:30px;text-align:center;font-size:0.85rem;color:rgba(255,255,255,0.3);">Aucun cours</div>
                @endforelse
            </div>

            {{-- Drop-off par chapitre --}}
            <div class="glass" style="padding:22px;">
                <div class="sec-title">Rétention par chapitre</div>
                <div class="sec-sub">
                    {{ $topCourse ? Str::limit($topCourse->title, 35) : 'Cours le plus populaire' }}
                    — % apprenants ayant complété chaque chapitre
                </div>

                @if($chapterDropoff && $chapterDropoff->count() > 0)
                @php $maxRate = $chapterDropoff->max('rate') ?: 1; @endphp
                @foreach($chapterDropoff as $i => $ch)
                @php
                    $drop = $i > 0 ? $chapterDropoff[$i-1]['rate'] - $ch['rate'] : 0;
                    $color = $ch['rate'] >= 70 ? '#25c26e' : ($ch['rate'] >= 40 ? '#e8b84b' : '#f87171');
                @endphp
                <div style="margin-bottom:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <div style="display:flex;align-items:center;gap:7px;min-width:0;">
                            <div style="width:20px;height:20px;border-radius:6px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;color:{{ $color }};flex-shrink:0;">{{ $i+1 }}</div>
                            <span style="font-size:0.78rem;color:rgba(255,255,255,0.65);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;">{{ Str::limit($ch['title'], 22) }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                            @if($drop > 5)
                            <span style="font-size:0.62rem;color:#f87171;">↓ {{ $drop }}%</span>
                            @endif
                            <span style="font-size:0.78rem;font-weight:700;color:{{ $color }};">{{ $ch['rate'] }}%</span>
                        </div>
                    </div>
                    <div class="prog-bar" style="height:6px;">
                        <div class="prog-fill" style="width:{{ $ch['rate'] }}%;background:{{ $color }};"></div>
                    </div>
                    <div style="font-size:0.62rem;color:rgba(255,255,255,0.2);margin-top:2px;">{{ $ch['lessons'] }} leçon(s)</div>
                </div>
                @endforeach
                @else
                <div style="padding:30px;text-align:center;font-size:0.85rem;color:rgba(255,255,255,0.3);">Données insuffisantes</div>
                @endif
            </div>
        </div>

        {{-- ── LIGNE 3 : Heure d'activité + Quiz distribution + Top apprenants ── --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 300px;gap:18px;margin-bottom:20px;" class="anim anim-4">

            {{-- Heure d'activité (heatmap barres) --}}
            <div class="glass" style="padding:22px;">
                <div class="sec-title">Heures d'activité</div>
                <div class="sec-sub">Distribution des leçons complétées par heure</div>

                @php $maxHour = $hourlyChart->max('count') ?: 1; @endphp
                <div style="display:flex;align-items:flex-end;gap:3px;height:100px;margin-bottom:6px;">
                    @foreach($hourlyChart as $h)
                    @php
                        $ht = $maxHour > 0 ? max(4, round($h['count'] / $maxHour * 100)) : 4;
                        $isPeak = $h['count'] >= $maxHour * 0.7;
                        $isWork = $h['hour'] >= 8 && $h['hour'] <= 18;
                        $hColor = $isPeak ? '#e8b84b' : ($isWork ? '#25c26e' : 'rgba(37,194,110,0.25)');
                    @endphp
                    <div class="heat-bar" style="height:{{ $ht }}%;background:{{ $hColor }};border-radius:3px 3px 0 0;flex:1;"
                         data-tip="{{ $h['label'] }} : {{ $h['count'] }}"></div>
                    @endforeach
                </div>
                {{-- Labels heures --}}
                <div style="display:flex;justify-content:space-between;font-size:0.6rem;color:rgba(255,255,255,0.22);">
                    <span>0h</span><span>6h</span><span>12h</span><span>18h</span><span>23h</span>
                </div>
                {{-- Légende --}}
                <div style="display:flex;gap:12px;margin-top:10px;">
                    <div style="display:flex;align-items:center;gap:4px;font-size:0.7rem;color:rgba(255,255,255,0.35);">
                        <div style="width:8px;height:8px;border-radius:2px;background:#e8b84b;"></div> Pic
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;font-size:0.7rem;color:rgba(255,255,255,0.35);">
                        <div style="width:8px;height:8px;border-radius:2px;background:#25c26e;"></div> Journée
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;font-size:0.7rem;color:rgba(255,255,255,0.35);">
                        <div style="width:8px;height:8px;border-radius:2px;background:rgba(37,194,110,0.25);"></div> Nuit
                    </div>
                </div>

                {{-- Heure de pointe --}}
                @php
                    $peakHour = $hourlyChart->sortByDesc('count')->first();
                @endphp
                @if($peakHour && $peakHour['count'] > 0)
                <div style="margin-top:12px;padding:8px 12px;border-radius:9px;background:rgba(232,184,75,0.06);border:1px solid rgba(232,184,75,0.12);">
                    <span style="font-size:0.75rem;color:rgba(255,255,255,0.45);">🕐 Heure de pointe :</span>
                    <strong style="font-size:0.8rem;color:#e8b84b;margin-left:6px;">{{ $peakHour['label'] }} ({{ $peakHour['count'] }} leçons)</strong>
                </div>
                @endif
            </div>

            {{-- Distribution scores quiz --}}
            <div class="glass" style="padding:22px;">
                <div class="sec-title">Distribution des scores quiz</div>
                <div class="sec-sub">Répartition par tranche de 10%</div>

                @php $maxQDist = $quizScoreDistribution->max('count') ?: 1; @endphp
                <div style="display:flex;flex-direction:column;gap:6px;">
                    @foreach($quizScoreDistribution as $qd)
                    @php
                        $barW  = max(2, round($qd['count'] / $maxQDist * 100));
                        $qColor = (int)explode('–',$qd['range'])[0] >= 70 ? '#25c26e' : ((int)explode('–',$qd['range'])[0] >= 40 ? '#e8b84b' : '#f87171');
                    @endphp
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:0.68rem;color:rgba(255,255,255,0.35);width:46px;flex-shrink:0;">{{ $qd['range'] }}%</span>
                        <div class="prog-bar" style="flex:1;height:7px;">
                            <div class="prog-fill" style="width:{{ $barW }}%;background:{{ $qColor }};"></div>
                        </div>
                        <span style="font-size:0.72rem;font-weight:600;width:22px;text-align:right;color:{{ $qd['count'] > 0 ? 'rgba(255,255,255,0.7)' : 'rgba(255,255,255,0.2)' }};">{{ $qd['count'] }}</span>
                    </div>
                    @endforeach
                </div>

                <div style="margin-top:14px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.35);">Taux de réussite global</span>
                        <span style="font-size:0.88rem;font-weight:700;color:{{ $quizPassRate >= 60 ? '#25c26e' : '#f87171' }};">{{ $quizPassRate }}%</span>
                    </div>
                    <div class="prog-bar" style="margin-top:6px;height:6px;">
                        <div class="prog-fill" style="width:{{ $quizPassRate }}%;background:{{ $quizPassRate >= 60 ? '#25c26e' : '#f87171' }};"></div>
                    </div>
                </div>
            </div>

            {{-- Top apprenants --}}
            <div class="glass" style="padding:20px;">
                <div class="sec-title">Top apprenants</div>
                <div class="sec-sub">Les plus avancés</div>

                @php $avColors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46']; @endphp
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @forelse($topStudents as $i => $s)
                    @php $ac = $avColors[$i % count($avColors)]; @endphp
                    <div style="display:flex;align-items:center;gap:10px;">
                        {{-- Rang --}}
                        <div style="width:20px;text-align:center;font-size:0.72rem;font-weight:700;color:{{ $i === 0 ? '#e8b84b' : 'rgba(255,255,255,0.25)' }};flex-shrink:0;">
                            {{ ['🥇','🥈','🥉','4','5'][$i] }}
                        </div>
                        {{-- Avatar --}}
                        <div style="width:32px;height:32px;border-radius:50%;background:{{ $ac }};display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ $s['user']->initials ?? '?' }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.78rem;font-weight:500;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s['user']->full_name }}</div>
                            <div class="prog-bar" style="height:3px;margin-top:4px;">
                                <div class="prog-fill" style="width:{{ $s['progress'] }}%;background:{{ $s['completed'] ? '#25c26e' : '#3b82f6' }};"></div>
                            </div>
                        </div>
                        <div style="flex-shrink:0;font-size:0.78rem;font-weight:700;color:{{ $s['completed'] ? '#25c26e' : '#60a5fa' }};">
                            {{ $s['progress'] }}%
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:20px 0;font-size:0.85rem;color:rgba(255,255,255,0.3);">Aucun apprenant</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══ CHART.JS ═══ --}}
<script>
const enrollData  = @json($enrollChart->values());
const weeklyData  = @json($weeklyActivity->values());

const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#0d1f13',
            borderColor: 'rgba(37,194,110,0.3)',
            borderWidth: 1,
            titleColor: 'rgba(255,255,255,0.7)',
            bodyColor: '#e0ebe2',
        },
    },
    scales: {
        x: {
            grid: { display: false },
            border: { display: false },
            ticks: { color: 'rgba(255,255,255,0.3)', font: { size: 11, family: 'Outfit' } },
        },
        y: {
            grid: { color: 'rgba(255,255,255,0.04)' },
            border: { display: false },
            ticks: { color: 'rgba(255,255,255,0.3)', font: { size: 11, family: 'Outfit' } },
        },
    },
};

// ── Graphique inscriptions ──────────────────────────────────────────────────
(function() {
    const ctx = document.getElementById('enrollChart');
    if (!ctx || !enrollData.length) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: enrollData.map(d => d.label),
            datasets: [{
                data:            enrollData.map(d => d.count),
                borderColor:     '#25c26e',
                backgroundColor: 'rgba(37,194,110,0.08)',
                borderWidth: 2,
                tension:     0.4,
                fill:        true,
                pointBackgroundColor: '#25c26e',
                pointRadius: 3,
                pointHoverRadius: 5,
            }],
        },
        options: {
            ...chartDefaults,
            plugins: { ...chartDefaults.plugins,
                tooltip: { ...chartDefaults.plugins.tooltip,
                    callbacks: { label: ctx => ` ${ctx.parsed.y} inscription(s)` }
                }
            },
            scales: { ...chartDefaults.scales,
                y: { ...chartDefaults.scales.y,
                    ticks: { ...chartDefaults.scales.y.ticks, stepSize: 1 }
                }
            },
        },
    });
})();

// ── Graphique engagement hebdomadaire (barres empilées) ─────────────────────
(function() {
    const ctx = document.getElementById('weeklyChart');
    if (!ctx || !weeklyData.length) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: weeklyData.map(d => d.label),
            datasets: [
                {
                    label: 'Leçons',
                    data:  weeklyData.map(d => d.lessons),
                    backgroundColor: 'rgba(37,194,110,0.65)',
                    borderRadius: { topLeft: 4, topRight: 4 },
                    borderSkipped: false,
                },
                {
                    label: 'Quiz',
                    data:  weeklyData.map(d => d.quizzes),
                    backgroundColor: 'rgba(59,130,246,0.65)',
                },
                {
                    label: 'Forum',
                    data:  weeklyData.map(d => d.forum),
                    backgroundColor: 'rgba(167,139,250,0.65)',
                },
            ],
        },
        options: {
            ...chartDefaults,
            plugins: { ...chartDefaults.plugins,
                legend: { display: false },
                tooltip: { ...chartDefaults.plugins.tooltip, mode: 'index', intersect: false },
            },
            scales: { ...chartDefaults.scales,
                x: { ...chartDefaults.scales.x, stacked: true },
                y: { ...chartDefaults.scales.y, stacked: true,
                     ticks: { ...chartDefaults.scales.y.ticks, stepSize: 1 }
                },
            },
        },
    });
})();

function statsPage() { return {}; }
</script>
</body>
</html>