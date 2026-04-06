<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stats — {{ $quiz->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        body { font-family:'Outfit',sans-serif; background:#0f1f14; color:#e0ebe2; }
        .sidebar { width:260px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;display:flex;flex-direction:column;background:linear-gradient(180deg,#081409,#0a1a0f);border-right:1px solid rgba(37,194,110,0.08); }
        .main-content { margin-left:260px;min-height:100vh; }
        .nav-item { display:flex;align-items:center;gap:12px;padding:10px 20px;border-radius:12px;color:rgba(255,255,255,0.45);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all 0.2s;margin:2px 12px; }
        .nav-item:hover { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.8); }
        .nav-item.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .nav-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;background:rgba(255,255,255,0.04); }
        .nav-item.active .nav-icon { background:rgba(37,194,110,0.18); }

        .glass  { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:18px; }
        .glass2 { background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:14px; }

        /* ── Stat cards ── */
        .kpi { padding:20px;border-radius:16px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);transition:all 0.25s; }
        .kpi:hover { border-color:rgba(37,194,110,0.2);transform:translateY(-2px); }
        .kpi-val  { font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;line-height:1; }
        .kpi-lbl  { font-size:0.72rem;color:rgba(255,255,255,0.38);margin-top:6px;text-transform:uppercase;letter-spacing:.06rem; }
        .kpi-sub  { font-size:0.75rem;margin-top:4px; }

        /* ── Bars distribution ── */
        .dist-bar-wrap { display:flex;align-items:flex-end;gap:6px;height:100px; }
        .dist-bar { border-radius:4px 4px 0 0;min-width:18px;transition:all 0.6s ease;position:relative;flex:1; }
        .dist-bar:hover .dist-tooltip { opacity:1; }
        .dist-tooltip { position:absolute;bottom:calc(100%+4px);left:50%;transform:translateX(-50%);background:#0d1f13;border:1px solid rgba(255,255,255,0.1);border-radius:7px;padding:3px 8px;font-size:0.68rem;white-space:nowrap;opacity:0;transition:opacity 0.2s;pointer-events:none; }

        /* ── Question difficulty bar ── */
        .diff-bar { height:8px;border-radius:4px;background:rgba(255,255,255,0.07);overflow:hidden;flex:1; }
        .diff-fill { height:100%;border-radius:4px;transition:width 1s ease; }

        /* ── Score badge ── */
        .score-badge { display:inline-flex;align-items:center;justify-content:center;min-width:44px;padding:3px 8px;border-radius:100px;font-size:0.75rem;font-weight:700; }
        .score-pass   { background:rgba(37,194,110,0.12);color:#25c26e;border:1px solid rgba(37,194,110,0.25); }
        .score-fail   { background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2); }
        .score-mid    { background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }

        /* ── Réponse bar ── */
        .ans-bar { height:6px;border-radius:3px;background:rgba(255,255,255,0.06);overflow:hidden;flex:1; }
        .ans-fill { height:100%;border-radius:3px;transition:width 1s ease; }

        /* ── Table apprenants ── */
        .student-row { display:flex;align-items:center;gap:14px;padding:12px 16px;border-bottom:1px solid rgba(255,255,255,0.04);transition:background 0.15s; }
        .student-row:hover { background:rgba(255,255,255,0.02); }
        .student-row:last-child { border-bottom:none; }

        /* ── Tabs ── */
        .tab-btn { padding:8px 18px;border-radius:10px;font-size:0.85rem;font-weight:500;cursor:pointer;border:none;background:transparent;transition:all 0.2s;font-family:'Outfit',sans-serif;color:rgba(255,255,255,0.45); }
        .tab-btn.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .tab-btn:hover:not(.active) { color:rgba(255,255,255,0.75); }

        /* ── Btn ── */
        .btn-ghost { display:inline-flex;align-items:center;gap:7px;padding:8px 15px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:9px;color:rgba(255,255,255,0.6);font-size:0.82rem;font-weight:500;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .btn-ghost:hover { background:rgba(255,255,255,0.08);color:#fff; }
        .btn-primary { display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:linear-gradient(135deg,#1a8a47,#25c26e);border-radius:10px;color:#fff;font-size:0.85rem;font-weight:600;border:none;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .btn-primary:hover { transform:translateY(-1px);box-shadow:0 6px 18px rgba(37,194,110,0.3); }

        /* ── Section title ── */
        .section-title { font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;margin-bottom:4px; }
        .section-sub   { font-size:0.75rem;color:rgba(255,255,255,0.35);margin-bottom:16px; }

        /* ── Gauge arc ── */
        .gauge-wrap { position:relative;display:flex;justify-content:center; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .anim-1{animation-delay:.05s}.anim-2{animation-delay:.1s}.anim-3{animation-delay:.15s}
        .anim-4{animation-delay:.2s}.anim-5{animation-delay:.25s}.anim-6{animation-delay:.3s}

        /* ── Progress ring ── */
        .ring { transform:rotate(-90deg); }
        .ring-track { fill:none;stroke:rgba(255,255,255,0.06);stroke-width:10; }
        .ring-fill  { fill:none;stroke-width:10;stroke-linecap:round;transition:stroke-dashoffset 1.2s ease; }
    </style>
</head>
<body x-data="quizStats()">

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>

    {{-- Résumé quiz --}}
    <div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.05);">
        <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:.08rem;color:rgba(255,255,255,0.2);margin-bottom:8px;">Quiz analysé</div>
        <div style="font-size:0.85rem;font-weight:600;color:#fff;margin-bottom:6px;line-height:1.3;">{{ Str::limit($quiz->title, 32) }}</div>
        <div style="font-size:0.72rem;color:rgba(255,255,255,0.35);">{{ $quiz->course->title }}</div>
    </div>

    <nav style="flex:1;padding:16px 0;overflow-y:auto;">
        <a href="{{ route('teacher.dashboard') }}" class="nav-item"><span class="nav-icon">🏠</span>Dashboard</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="nav-icon">📚</span>Mes cours</a>
        <a href="{{ route('teacher.quizzes.index') }}" class="nav-item active"><span class="nav-icon">📝</span>Quiz & Exercices</a>
        <a href="{{ route('teacher.students.index') }}" class="nav-item"><span class="nav-icon">👥</span>Apprenants</a>
        <a href="#" class="nav-item"><span class="nav-icon">💰</span>Revenus</a>
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
    <header style="position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;padding:12px 32px;border-bottom:1px solid rgba(37,194,110,0.08);background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);">
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="{{ route('teacher.quizzes.index') }}" class="btn-ghost" style="padding:6px 12px;">← Retour</a>
            <div>
                <div style="font-size:0.9rem;font-weight:600;color:#fff;">{{ Str::limit($quiz->title, 45) }}</div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.35);">Statistiques · {{ $globalStats['total_attempts'] }} tentative(s)</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn-ghost" style="font-size:0.82rem;">✏️ Éditer le quiz</a>
        </div>
    </header>

    <div style="padding:28px 32px;">

        @if($globalStats['total_attempts'] === 0)
        {{-- ── ÉTAT VIDE ── --}}
        <div class="glass anim" style="padding:60px 20px;text-align:center;">
            <div style="font-size:4rem;margin-bottom:14px;">📊</div>
            <div style="font-size:1rem;font-weight:600;color:rgba(255,255,255,0.55);margin-bottom:6px;">Aucune tentative pour l'instant</div>
            <div style="font-size:0.85rem;color:rgba(255,255,255,0.3);margin-bottom:6px;">Ce quiz n'a pas encore été passé par des apprenants.</div>
            <div style="font-size:0.8rem;color:rgba(255,255,255,0.2);">
                Ce quiz comporte {{ $quiz->questions->count() }} question(s) · Score requis : {{ $quiz->passing_score }}%
            </div>
        </div>
        @else

        {{-- ── KPI GLOBAUX ── --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">

            {{-- Taux de réussite (avec anneau) --}}
            <div class="kpi anim anim-1" style="grid-column:span 1;">
                <div style="display:flex;align-items:center;gap:14px;">
                    {{-- Ring SVG --}}
                    <div style="position:relative;width:70px;height:70px;flex-shrink:0;">
                        <svg width="70" height="70" viewBox="0 0 70 70">
                            <circle class="ring-track" cx="35" cy="35" r="28"/>
                            @php
                                $r   = 28;
                                $circ = 2 * M_PI * $r;
                                $pct  = $globalStats['pass_rate'];
                                $offset = $circ * (1 - $pct / 100);
                                $color = $pct >= 70 ? '#25c26e' : ($pct >= 40 ? '#e8b84b' : '#f87171');
                            @endphp
                            <circle class="ring-fill ring" cx="35" cy="35" r="28"
                                    stroke="{{ $color }}"
                                    stroke-dasharray="{{ $circ }}"
                                    stroke-dashoffset="{{ $offset }}"/>
                        </svg>
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;color:{{ $color }};">
                            {{ $globalStats['pass_rate'] }}%
                        </div>
                    </div>
                    <div>
                        <div class="kpi-val" style="font-size:1.5rem;color:{{ $color }};">{{ $globalStats['passed'] }}/{{ $globalStats['total_attempts'] }}</div>
                        <div class="kpi-lbl">Taux de réussite</div>
                        <div class="kpi-sub" style="color:rgba(255,255,255,0.3);">{{ $globalStats['failed'] }} échec(s)</div>
                    </div>
                </div>
            </div>

            @foreach([
                ['📊', $globalStats['avg_score'].'%', 'Score moyen', ($globalStats['avg_score'] >= $quiz->passing_score ? '#25c26e' : '#f87171'), 'Seuil : '.$quiz->passing_score.'%'],
                ['🏆', $globalStats['best_score'].'%', 'Meilleur score', '#e8b84b', 'Pire : '.$globalStats['worst_score'].'%'],
                ['👥', $globalStats['unique_students'], 'Apprenants', '#3b82f6', $globalStats['total_attempts'].' tentative(s) total'],
            ] as [$icon, $val, $label, $color, $sub])
            <div class="kpi anim anim-{{ $loop->index+2 }}">
                <div style="width:36px;height:36px;border-radius:10px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;font-size:1rem;margin-bottom:10px;">{{ $icon }}</div>
                <div class="kpi-val" style="color:{{ $color }};">{{ $val }}</div>
                <div class="kpi-lbl">{{ $label }}</div>
                <div class="kpi-sub" style="color:rgba(255,255,255,0.3);">{{ $sub }}</div>
            </div>
            @endforeach
        </div>

        {{-- ── TABS ── --}}
        <div style="display:flex;gap:4px;margin-bottom:22px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:5px;width:fit-content;" class="anim anim-3">
            <button class="tab-btn" :class="tab==='overview'?'active':''"   x-on:click="tab='overview'">📊 Vue d'ensemble</button>
            <button class="tab-btn" :class="tab==='questions'?'active':''"  x-on:click="tab='questions'">❓ Analyse questions</button>
            <button class="tab-btn" :class="tab==='students'?'active':''"   x-on:click="tab='students'">👥 Par apprenant</button>
            <button class="tab-btn" :class="tab==='attempts'?'active':''"   x-on:click="tab='attempts'">📋 Tentatives récentes</button>
        </div>

        {{-- ═══════════════════════════════════════
            TAB : VUE D'ENSEMBLE
        ═══════════════════════════════════════ --}}
        <div x-show="tab === 'overview'" x-transition>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">

                {{-- Distribution des scores --}}
                <div class="glass" style="padding:22px;">
                    <div class="section-title">Distribution des scores</div>
                    <div class="section-sub">Répartition des {{ $globalStats['total_attempts'] }} tentative(s) par tranche</div>

                    @php $maxCount = $distribution->max('count') ?: 1; @endphp
                    <div class="dist-bar-wrap" style="margin-bottom:8px;">
                        @foreach($distribution as $d)
                        @php
                            $h     = max(4, round($d['count'] / $maxCount * 100));
                            $isPass = $d['min'] >= $quiz->passing_score;
                            $barColor = $isPass ? '#25c26e' : ($d['min'] >= 50 ? '#e8b84b' : '#f87171');
                        @endphp
                        <div class="dist-bar" style="height:{{ $h }}%;background:{{ $barColor }}{{ $d['count'] > 0 ? '' : '30' }};">
                            <div class="dist-tooltip">
                                {{ $d['range'] }}<br>{{ $d['count'] }} tentative(s)
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:0.65rem;color:rgba(255,255,255,0.25);">
                        <span>0%</span><span>50%</span><span>100%</span>
                    </div>

                    {{-- Légende seuil --}}
                    <div style="margin-top:12px;display:flex;align-items:center;gap:8px;font-size:0.75rem;color:rgba(255,255,255,0.4);">
                        <div style="width:10px;height:10px;border-radius:2px;background:#25c26e;flex-shrink:0;"></div>
                        <span>≥ {{ $quiz->passing_score }}% = réussi</span>
                        <div style="width:10px;height:10px;border-radius:2px;background:#f87171;flex-shrink:0;margin-left:8px;"></div>
                        <span>< {{ $quiz->passing_score }}% = échoué</span>
                    </div>

                    {{-- Tableau distribution --}}
                    <div style="margin-top:14px;display:flex;flex-direction:column;gap:6px;">
                        @foreach($distribution->where('count', '>', 0) as $d)
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span style="font-size:0.72rem;color:rgba(255,255,255,0.4);width:60px;flex-shrink:0;">{{ $d['range'] }}</span>
                            <div class="diff-bar">
                                <div class="diff-fill" style="width:{{ $d['pct'] }}%;background:{{ $d['min'] >= $quiz->passing_score ? '#25c26e' : '#f87171' }};"></div>
                            </div>
                            <span style="font-size:0.72rem;font-weight:600;color:rgba(255,255,255,0.7);width:32px;text-align:right;">{{ $d['count'] }}</span>
                            <span style="font-size:0.68rem;color:rgba(255,255,255,0.25);width:32px;">{{ $d['pct'] }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Graphe évolution temporelle (Chart.js) --}}
                <div class="glass" style="padding:22px;">
                    <div class="section-title">Évolution des scores</div>
                    <div class="section-sub">Score moyen par jour (30 derniers jours)</div>

                    @if($scoreEvolution->count() > 0)
                    <div style="position:relative;height:200px;">
                        <canvas id="scoreChart"></canvas>
                    </div>
                    @else
                    <div style="height:200px;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;">
                        <div style="font-size:2rem;">📈</div>
                        <div style="font-size:0.82rem;color:rgba(255,255,255,0.3);">Pas encore assez de données</div>
                    </div>
                    @endif

                    {{-- Infos temps moyen --}}
                    @if($globalStats['avg_time_seconds'] > 0)
                    <div style="margin-top:14px;padding:10px 12px;border-radius:10px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:0.78rem;color:rgba(255,255,255,0.4);">⏱ Temps moyen par tentative</span>
                        <span style="font-size:0.85rem;font-weight:600;color:#fff;">
                            @php
                                $sec = $globalStats['avg_time_seconds'];
                                echo $sec < 60 ? $sec.'s' : intdiv($sec,60).'min '.($sec%60).'s';
                            @endphp
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Réussite par tentative --}}
                <div class="glass" style="padding:22px;">
                    <div class="section-title">Réussite par tentative</div>
                    <div class="section-sub">Les apprenants réussissent-ils dès le 1er essai ?</div>

                    @php
                        $byAttemptNum = $quiz->attempts->groupBy('attempt_number')->map(function($group) {
                            return ['total' => $group->count(), 'passed' => $group->where('passed',true)->count()];
                        })->sortKeys();
                    @endphp

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach($byAttemptNum->take(5) as $num => $data)
                        @php $rate = $data['total'] > 0 ? round($data['passed']/$data['total']*100) : 0; @endphp
                        <div>
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                                <span style="font-size:0.78rem;color:rgba(255,255,255,0.55);">
                                    {{ $num === 1 ? '1ʳᵉ tentative' : $num.'ᵉ tentative' }}
                                </span>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="font-size:0.72rem;color:rgba(255,255,255,0.3);">{{ $data['total'] }} essai(s)</span>
                                    <span class="score-badge {{ $rate >= $quiz->passing_score ? 'score-pass' : 'score-fail' }}">{{ $rate }}%</span>
                                </div>
                            </div>
                            <div class="diff-bar">
                                <div class="diff-fill" style="width:{{ $rate }}%;background:{{ $rate >= $quiz->passing_score ? '#25c26e' : '#f87171' }};"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Info paramètres quiz --}}
                <div class="glass" style="padding:22px;">
                    <div class="section-title">Paramètres du quiz</div>
                    <div class="section-sub">Configuration actuelle</div>

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach([
                            ['Score de passage','{{ $quiz->passing_score }}%','#25c26e'],
                            ['Tentatives max','{{ $quiz->max_attempts }}','#3b82f6'],
                            ['Durée','{{ $quiz->duration_minutes ? $quiz->duration_minutes."min" : "Illimitée" }}','#a78bfa'],
                            ['Questions','{{ $quiz->questions->count() }}','#e8b84b'],
                            ['Points total','{{ $quiz->questions->sum("points") }} pts','#f97316'],
                            ['Afficher corrections','{{ $quiz->show_answers ? "Oui" : "Non" }}','rgba(255,255,255,0.5)'],
                        ] as [$label,$val,$color])
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-radius:9px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);">
                            <span style="font-size:0.82rem;color:rgba(255,255,255,0.5);">{{ $label }}</span>
                            <span style="font-size:0.85rem;font-weight:600;color:{{ $color }};">{{ $val }}</span>
                        </div>
                        @endforeach
                    </div>

                    <a href="{{ route('teacher.quizzes.edit', $quiz) }}?tab=settings"
                       class="btn-ghost" style="width:100%;justify-content:center;margin-top:14px;font-size:0.8rem;">
                        ⚙️ Modifier les paramètres
                    </a>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════
            TAB : ANALYSE QUESTIONS
        ═══════════════════════════════════════ --}}
        <div x-show="tab === 'questions'" x-transition>

            <div class="glass" style="padding:18px 20px;margin-bottom:14px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-size:1.2rem;">💡</span>
                    <span style="font-size:0.82rem;color:rgba(255,255,255,0.5);line-height:1.5;">
                        Les questions sont triées du plus difficile au plus facile (taux de bonne réponse croissant).
                        Les barres rouges indiquent les questions posant problème à la majorité des apprenants.
                    </span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px;">
                @foreach($questionStats as $qi => $qs)
                @php
                    $rate  = $qs['correct_rate'];
                    $color = $rate >= 70 ? '#25c26e' : ($rate >= 40 ? '#e8b84b' : '#f87171');
                    $diff  = $rate < 40 ? '🔴 Difficile' : ($rate < 70 ? '🟡 Moyen' : '🟢 Facile');
                @endphp
                <div class="glass" style="overflow:hidden;">

                    {{-- En-tête question --}}
                    <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 20px;cursor:pointer;"
                         x-on:click="toggleQ({{ $qs['id'] }})">
                        <div style="width:28px;height:28px;border-radius:8px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:{{ $color }};flex-shrink:0;margin-top:1px;">
                            {{ $qi + 1 }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.9rem;font-weight:500;color:#fff;margin-bottom:6px;line-height:1.4;">{{ $qs['question'] }}</div>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                {{-- Barre de difficulté --}}
                                <div style="display:flex;align-items:center;gap:8px;flex:1;min-width:160px;">
                                    <div class="diff-bar" style="height:7px;">
                                        <div class="diff-fill" style="width:{{ $rate }}%;background:{{ $color }};"></div>
                                    </div>
                                    <span style="font-size:0.8rem;font-weight:700;color:{{ $color }};white-space:nowrap;">{{ $rate }}%</span>
                                </div>
                                <span style="font-size:0.72rem;padding:2px 8px;border-radius:100px;background:{{ $color }}15;border:1px solid {{ $color }}30;color:{{ $color }};">{{ $diff }}</span>
                                <span style="font-size:0.72rem;color:rgba(255,255,255,0.3);">{{ $qs['attempted'] }} tenté(s)</span>
                                <span style="font-size:0.72rem;padding:2px 8px;border-radius:100px;background:rgba(232,184,75,0.1);border:1px solid rgba(232,184,75,0.2);color:#e8b84b;">{{ $qs['points'] }} pt(s)</span>
                            </div>
                        </div>
                        <div style="font-size:0.85rem;color:rgba(255,255,255,0.3);flex-shrink:0;" x-text="openQuestions.includes({{ $qs['id'] }}) ? '▲' : '▼'"></div>
                    </div>

                    {{-- Détail réponses (expand) --}}
                    <div x-show="openQuestions.includes({{ $qs['id'] }})" x-transition
                         style="border-top:1px solid rgba(255,255,255,0.06);padding:14px 20px;">
                        <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.3);margin-bottom:10px;font-weight:600;">
                            Distribution des réponses ({{ $qs['attempted'] }} tentative(s))
                        </div>
                        <div style="display:flex;flex-direction:column;gap:7px;">
                            @foreach($qs['answers'] as $ans)
                            <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:9px;background:{{ $ans['is_correct'] ? 'rgba(37,194,110,0.06)' : 'rgba(255,255,255,0.02)' }};border:1px solid {{ $ans['is_correct'] ? 'rgba(37,194,110,0.15)' : 'rgba(255,255,255,0.05)' }};">
                                <div style="width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.62rem;font-weight:700;flex-shrink:0;{{ $ans['is_correct'] ? 'background:rgba(37,194,110,0.15);color:#25c26e' : 'background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.2)' }}">
                                    {{ $ans['is_correct'] ? '✓' : '✗' }}
                                </div>
                                <span style="flex:1;font-size:0.82rem;{{ $ans['is_correct'] ? 'color:#25c26e;font-weight:500' : 'color:rgba(255,255,255,0.55)' }};">{{ $ans['text'] }}</span>
                                <div class="ans-bar">
                                    <div class="ans-fill" style="width:{{ $ans['pct'] }}%;background:{{ $ans['is_correct'] ? '#25c26e' : 'rgba(255,255,255,0.15)' }};"></div>
                                </div>
                                <span style="font-size:0.72rem;font-weight:600;width:30px;text-align:right;color:{{ $ans['is_correct'] ? '#25c26e' : 'rgba(255,255,255,0.4)' }};">{{ $ans['count'] }}</span>
                                <span style="font-size:0.68rem;color:rgba(255,255,255,0.25);width:34px;text-align:right;">{{ $ans['pct'] }}%</span>
                            </div>
                            @endforeach
                        </div>
                        @if($qs['correct_rate'] < 40)
                        <div style="margin-top:10px;padding:8px 12px;border-radius:9px;background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.15);font-size:0.78rem;color:rgba(248,113,113,0.8);">
                            ⚠ Cette question est ratée par {{ 100 - $qs['correct_rate'] }}% des apprenants. Envisagez de la reformuler ou d'ajouter une explication.
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ═══════════════════════════════════════
            TAB : PAR APPRENANT
        ═══════════════════════════════════════ --}}
        <div x-show="tab === 'students'" x-transition>

            {{-- Barre de recherche --}}
            <div style="margin-bottom:16px;">
                <div style="position:relative;">
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:0.9rem;color:rgba(255,255,255,0.3);">🔍</span>
                    <input type="text" x-model="studentSearch" placeholder="Rechercher un apprenant..."
                           style="width:100%;max-width:320px;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);border-radius:10px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.88rem;padding:9px 14px 9px 40px;outline:none;">
                </div>
            </div>

            <div class="glass" style="overflow:hidden;">

                {{-- En-tête tableau --}}
                <div style="display:flex;align-items:center;gap:14px;padding:10px 16px;border-bottom:1px solid rgba(255,255,255,0.07);font-size:0.65rem;text-transform:uppercase;letter-spacing:.07rem;font-weight:700;color:rgba(255,255,255,0.25);">
                    <div style="flex:1;">Apprenant</div>
                    <div style="width:90px;text-align:center;">Tentatives</div>
                    <div style="width:90px;text-align:center;">Meilleur</div>
                    <div style="width:90px;text-align:center;">Score moy.</div>
                    <div style="width:80px;text-align:center;">Statut</div>
                    <div style="width:100px;text-align:right;">Dernière tent.</div>
                </div>

                @php $colors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e']; @endphp
                @forelse($studentStats as $i => $s)
                @php $c = $colors[$i % count($colors)]; @endphp
                <div class="student-row"
                     x-show="!studentSearch || '{{ strtolower($s['user']->full_name) }}'.includes(studentSearch.toLowerCase())">
                    <div style="flex:1;display:flex;align-items:center;gap:10px;min-width:0;">
                        <div style="width:36px;height:36px;border-radius:50%;background:{{ $c }};display:flex;align-items:center;justify-content:center;font-size:0.82rem;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ $s['user']->initials }}
                        </div>
                        <div style="min-width:0;">
                            <div style="font-size:0.88rem;font-weight:500;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s['user']->full_name }}</div>
                            <div style="font-size:0.72rem;color:rgba(255,255,255,0.35);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s['user']->email }}</div>
                        </div>
                    </div>
                    <div style="width:90px;text-align:center;font-size:0.88rem;font-weight:600;color:rgba(255,255,255,0.7);">
                        {{ $s['attempts'] }} / {{ $quiz->max_attempts }}
                    </div>
                    <div style="width:90px;text-align:center;">
                        <span class="score-badge {{ $s['best_score'] >= $quiz->passing_score ? 'score-pass' : 'score-fail' }}">
                            {{ $s['best_score'] }}%
                        </span>
                    </div>
                    <div style="width:90px;text-align:center;font-size:0.82rem;color:rgba(255,255,255,0.55);">
                        {{ $s['avg_score'] }}%
                    </div>
                    <div style="width:80px;text-align:center;">
                        @if($s['passed'])
                        <span style="font-size:0.72rem;padding:3px 9px;border-radius:100px;background:rgba(37,194,110,0.1);border:1px solid rgba(37,194,110,0.2);color:#25c26e;font-weight:700;">✓ Réussi</span>
                        @else
                        <span style="font-size:0.72rem;padding:3px 9px;border-radius:100px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);color:#f87171;font-weight:700;">✗ Échoué</span>
                        @endif
                    </div>
                    <div style="width:100px;text-align:right;font-size:0.72rem;color:rgba(255,255,255,0.3);">
                        {{ $s['last_attempt']->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div style="padding:40px;text-align:center;font-size:0.88rem;color:rgba(255,255,255,0.35);">
                    Aucun apprenant
                </div>
                @endforelse
            </div>

            {{-- Légende --}}
            <div style="margin-top:14px;display:flex;align-items:center;gap:16px;font-size:0.75rem;color:rgba(255,255,255,0.3);">
                <div style="display:flex;align-items:center;gap:5px;">
                    <div style="width:8px;height:8px;border-radius:2px;background:#25c26e;"></div>
                    Score ≥ {{ $quiz->passing_score }}% = réussi
                </div>
                <div style="display:flex;align-items:center;gap:5px;">
                    <div style="width:8px;height:8px;border-radius:2px;background:#f87171;"></div>
                    Score < {{ $quiz->passing_score }}% = échoué
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
            TAB : TENTATIVES RÉCENTES
        ═══════════════════════════════════════ --}}
        <div x-show="tab === 'attempts'" x-transition>
            <div class="glass" style="overflow:hidden;">

                <div style="display:flex;align-items:center;gap:14px;padding:10px 16px;border-bottom:1px solid rgba(255,255,255,0.07);font-size:0.65rem;text-transform:uppercase;letter-spacing:.07rem;font-weight:700;color:rgba(255,255,255,0.25);">
                    <div style="flex:1;">Apprenant</div>
                    <div style="width:80px;text-align:center;">Tentative</div>
                    <div style="width:80px;text-align:center;">Score</div>
                    <div style="width:80px;text-align:center;">Pts obtenus</div>
                    <div style="width:80px;text-align:center;">Statut</div>
                    <div style="width:80px;text-align:center;">Durée</div>
                    <div style="width:110px;text-align:right;">Date</div>
                </div>

                @php $colors2 = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e']; @endphp
                @forelse($recentAttempts as $i => $attempt)
                @php $c = $colors2[$i % count($colors2)]; @endphp
                <div class="student-row">
                    <div style="flex:1;display:flex;align-items:center;gap:10px;min-width:0;">
                        <div style="width:32px;height:32px;border-radius:50%;background:{{ $c }};display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ $attempt->user->initials ?? '?' }}
                        </div>
                        <div style="min-width:0;">
                            <div style="font-size:0.85rem;font-weight:500;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $attempt->user->full_name ?? 'Inconnu' }}</div>
                        </div>
                    </div>
                    <div style="width:80px;text-align:center;font-size:0.82rem;color:rgba(255,255,255,0.5);">
                        #{{ $attempt->attempt_number }}
                    </div>
                    <div style="width:80px;text-align:center;">
                        <span class="score-badge {{ $attempt->score >= $quiz->passing_score ? 'score-pass' : 'score-fail' }}">
                            {{ $attempt->score }}%
                        </span>
                    </div>
                    <div style="width:80px;text-align:center;font-size:0.82rem;color:rgba(255,255,255,0.5);">
                        {{ $attempt->earned_points }}/{{ $attempt->total_points }}
                    </div>
                    <div style="width:80px;text-align:center;">
                        @if($attempt->passed)
                        <span style="font-size:0.7rem;color:#25c26e;font-weight:700;">✓ Réussi</span>
                        @else
                        <span style="font-size:0.7rem;color:#f87171;font-weight:700;">✗ Échoué</span>
                        @endif
                    </div>
                    <div style="width:80px;text-align:center;font-size:0.75rem;color:rgba(255,255,255,0.4);">
                        @if($attempt->time_spent)
                        @php $sec = $attempt->time_spent; echo $sec < 60 ? $sec.'s' : intdiv($sec,60).'min '.($sec%60).'s'; @endphp
                        @else —
                        @endif
                    </div>
                    <div style="width:110px;text-align:right;font-size:0.72rem;color:rgba(255,255,255,0.3);">
                        {{ $attempt->finished_at ? $attempt->finished_at->format('d/m/y H:i') : $attempt->created_at->format('d/m/y H:i') }}
                    </div>
                </div>
                @empty
                <div style="padding:40px;text-align:center;font-size:0.88rem;color:rgba(255,255,255,0.35);">
                    Aucune tentative récente
                </div>
                @endforelse
            </div>
        </div>

        @endif {{-- end has attempts --}}
    </div>
</div>

{{-- Chart.js init --}}
@if($globalStats['total_attempts'] > 0 && $scoreEvolution->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('scoreChart');
    if (!ctx) return;

    const data = @json($scoreEvolution);
    const passing = {{ $quiz->passing_score }};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Score moyen',
                data: data.map(d => d.avg_score),
                borderColor: '#25c26e',
                backgroundColor: 'rgba(37,194,110,0.08)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#25c26e',
                pointRadius: 4,
                pointHoverRadius: 6,
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
                    titleColor: 'rgba(255,255,255,0.8)',
                    bodyColor: '#25c26e',
                    callbacks: {
                        label: ctx => `Score moyen : ${ctx.parsed.y}%`,
                    },
                },
                annotation: {
                    annotations: {
                        passLine: {
                            type: 'line',
                            yMin: passing, yMax: passing,
                            borderColor: 'rgba(232,184,75,0.5)',
                            borderWidth: 1.5,
                            borderDash: [4, 4],
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: { color: 'rgba(255,255,255,0.3)', font: { size: 11 } },
                },
                y: {
                    min: 0, max: 100,
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: 'rgba(255,255,255,0.3)',
                        font: { size: 11 },
                        callback: v => v + '%',
                    },
                },
            },
        },
    });
});
</script>
@endif

<script>
function quizStats() {
    return {
        tab: 'overview',
        studentSearch: '',
        openQuestions: [],

        toggleQ(id) {
            const idx = this.openQuestions.indexOf(id);
            idx === -1 ? this.openQuestions.push(id) : this.openQuestions.splice(idx, 1);
        },

        init() {
            // Ouvrir les questions les plus difficiles par défaut
            const hardQIds = @json($questionStats->take(3)->pluck('id')->values());
            this.openQuestions = hardQIds;
        }
    }
}
</script>
</body>
</html>