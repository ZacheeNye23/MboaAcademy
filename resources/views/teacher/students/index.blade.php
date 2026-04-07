<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mes apprenants — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
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

        /* ── Cards ── */
        .glass  { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:18px; }
        .kpi    { padding:18px 20px;border-radius:16px;background:rgba(255,255,255,0.03);
                  border:1px solid rgba(255,255,255,0.07);transition:all 0.25s; }
        .kpi:hover { border-color:rgba(37,194,110,0.2);transform:translateY(-2px); }

        /* ── Filtres ── */
        .filter-btn { padding:7px 15px;border-radius:100px;font-size:0.8rem;font-weight:500;
                      cursor:pointer;border:1px solid rgba(255,255,255,0.1);background:transparent;
                      color:rgba(255,255,255,0.45);transition:all 0.2s;font-family:'Outfit',sans-serif;
                      text-decoration:none;display:inline-block; }
        .filter-btn.active { background:rgba(37,194,110,0.12);border-color:#25c26e;color:#25c26e; }
        .filter-btn:hover:not(.active) { border-color:rgba(255,255,255,0.25);color:rgba(255,255,255,0.75); }

        /* ── Table ── */
        .tbl-header { display:flex;align-items:center;gap:0;padding:10px 16px;
                      border-bottom:1px solid rgba(255,255,255,0.07);
                      font-size:0.65rem;text-transform:uppercase;letter-spacing:.07rem;
                      font-weight:700;color:rgba(255,255,255,0.25); }
        .tbl-row { display:flex;align-items:center;padding:14px 16px;
                   border-bottom:1px solid rgba(255,255,255,0.04);
                   transition:background 0.15s;cursor:pointer;text-decoration:none; }
        .tbl-row:hover { background:rgba(37,194,110,0.04); }
        .tbl-row:last-child { border-bottom:none; }

        /* ── Col widths ── */
        .col-student { flex:1;min-width:0; }
        .col-course  { width:180px;flex-shrink:0; }
        .col-prog    { width:140px;flex-shrink:0; }
        .col-status  { width:110px;flex-shrink:0;text-align:center; }
        .col-date    { width:100px;flex-shrink:0;text-align:right; }
        .col-action  { width:80px;flex-shrink:0;text-align:right; }

        /* ── Progress bar ── */
        .prog-bar  { height:5px;border-radius:3px;background:rgba(255,255,255,0.07);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px;background:linear-gradient(90deg,#1a8a47,#25c26e);transition:width 0.8s ease; }

        /* ── Status pills ── */
        .pill { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:100px;font-size:0.7rem;font-weight:700; }
        .pill-done  { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
        .pill-prog  { background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2); }
        .pill-new   { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.4);border:1px solid rgba(255,255,255,0.1); }

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

        /* ── Sort indicator ── */
        .sort-btn { display:inline-flex;align-items:center;gap:4px;cursor:pointer;
                    font-size:0.65rem;text-transform:uppercase;letter-spacing:.07rem;font-weight:700;
                    color:rgba(255,255,255,0.25);text-decoration:none;transition:color 0.2s;font-family:'Outfit',sans-serif; }
        .sort-btn:hover { color:rgba(255,255,255,0.6); }
        .sort-btn.sorted { color:#25c26e; }

        /* ── Avatar ── */
        .avatar { width:38px;height:38px;border-radius:50%;display:flex;align-items:center;
                  justify-content:center;font-size:0.82rem;font-weight:700;color:#fff;flex-shrink:0; }

        /* ── Search ── */
        .search-wrap { position:relative; }
        .search-icon { position:absolute;left:14px;top:50%;transform:translateY(-50%);
                       font-size:0.9rem;color:rgba(255,255,255,0.3);pointer-events:none; }
        .search-input { background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);
                        border-radius:12px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.88rem;
                        padding:9px 14px 9px 40px;outline:none;transition:all 0.25s;width:260px; }
        .search-input::placeholder { color:rgba(255,255,255,0.25); }
        .search-input:focus { border-color:#25c26e;background:rgba(37,194,110,0.05); }

        /* ── Select ── */
        .field-select { background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);
                        border-radius:10px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.85rem;
                        padding:8px 12px;outline:none;cursor:pointer;transition:border-color 0.2s; }
        .field-select:focus { border-color:#25c26e; }
        .field-select option { background:#0f1f14; }

        /* ── Pagination ── */
        .pg-btn { padding:7px 13px;border-radius:9px;background:rgba(255,255,255,0.04);
                  border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.55);font-size:0.82rem;
                  text-decoration:none;transition:all 0.2s; }
        .pg-btn:hover { border-color:#25c26e;color:#25c26e; }
        .pg-btn.current { background:rgba(37,194,110,0.12);border-color:#25c26e;color:#25c26e;font-weight:600; }
        .pg-btn.disabled { opacity:0.3;pointer-events:none; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .anim-1{animation-delay:.05s}.anim-2{animation-delay:.1s}
        .anim-3{animation-delay:.15s}.anim-4{animation-delay:.2s}
    </style>
</head>
<body>

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>

    {{-- Résumé rapide --}}
    <div style="padding:14px 20px;border-bottom:1px solid rgba(255,255,255,0.05);">
        <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:.08rem;color:rgba(255,255,255,0.2);margin-bottom:8px;">Aperçu</div>
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:0.75rem;color:rgba(255,255,255,0.45);">Total inscrits</span>
            <span style="font-size:0.8rem;font-weight:700;color:#25c26e;">{{ $globalStats['total'] }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;">
            <span style="font-size:0.75rem;color:rgba(255,255,255,0.45);">Taux complétion</span>
            <span style="font-size:0.8rem;font-weight:700;color:#e8b84b;">
                {{ $globalStats['total'] > 0 ? round($globalStats['completed']/$globalStats['total']*100) : 0 }}%
            </span>
        </div>
    </div>

    <nav style="flex:1;padding:16px 0;overflow-y:auto;">
        <a href="{{ route('teacher.dashboard') }}" class="nav-item"><span class="nav-icon">🏠</span>Dashboard</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="nav-icon">📚</span>Mes cours</a>
        <a href="{{ route('teacher.courses.create') }}" class="nav-item"><span class="nav-icon">➕</span>Créer un cours</a>
        <a href="{{ route('teacher.quizzes.index') }}" class="nav-item"><span class="nav-icon">📝</span>Quiz & Exercices</a>
        <a href="{{ route('teacher.students.index') }}" class="nav-item active"><span class="nav-icon">👥</span>Mes apprenants</a>
        <a href="#" class="nav-item"><span class="nav-icon">💬</span>Forum</a>
        <a href="{{route ('teacher.revenues.index')}}" class="nav-item"><span class="nav-icon">💰</span>Revenus</a>
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
            <h1 style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:#fff;">Mes apprenants</h1>
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:2px;">
                {{ $globalStats['unique'] }} apprenant(s) unique(s) · {{ $globalStats['total'] }} inscription(s)
            </p>
        </div>
        {{-- Export CSV --}}
        <a href="{{ route('teacher.students.export', array_filter(['course' => $courseFilter !== 'all' ? $courseFilter : null, 'status' => $statusFilter !== 'all' ? $statusFilter : null])) }}"
           class="btn-export">
            ⬇ Exporter CSV
        </a>
    </header>

    <div style="padding:28px 32px;">

        @if(session('success'))
        <div class="anim" style="margin-bottom:20px;padding:14px 18px;border-radius:14px;background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2);display:flex;align-items:center;gap:10px;">
            <span>🎉</span><span style="font-size:0.88rem;color:#25c26e;">{{ session('success') }}</span>
        </div>
        @endif

        {{-- ── KPI GLOBAUX ── --}}
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px;">
            @foreach([
                ['👥', $globalStats['unique'],       'Apprenants uniques', '#25c26e',  null],
                ['📋', $globalStats['total'],        'Inscriptions total',  '#3b82f6',  null],
                ['✅', $globalStats['completed'],    'Cours terminés',      '#25c26e',  $globalStats['total'] > 0 ? round($globalStats['completed']/$globalStats['total']*100).'%' : '0%'],
                ['⏳', $globalStats['in_progress'],  'En cours',            '#e8b84b',  null],
                ['📊', $globalStats['avg_progress'].'%','Progression moy.', '#a78bfa',  null],
            ] as [$icon, $val, $label, $color, $badge])
            <div class="kpi anim anim-{{ $loop->index+1 }}">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px;">
                    <div style="width:34px;height:34px;border-radius:9px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;font-size:0.9rem;">{{ $icon }}</div>
                    @if($badge)
                    <span style="font-size:0.68rem;padding:2px 7px;border-radius:100px;background:{{ $color }}15;color:{{ $color }};font-weight:700;">{{ $badge }}</span>
                    @endif
                </div>
                <div style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:{{ $color }};line-height:1;">{{ $val }}</div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.35);margin-top:5px;">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        {{-- ── BARRE DE FILTRES ── --}}
        <form method="GET" action="{{ route('teacher.students.index') }}" id="filterForm">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;flex-wrap:wrap;" class="anim anim-3">

                {{-- Recherche --}}
                <div class="search-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" value="{{ $search }}"
                           class="search-input" placeholder="Nom, prénom, email..."
                           x-data x-on:keydown.escape="$el.value=''; $el.form.submit()">
                </div>

                {{-- Filtre cours --}}
                <select name="course" class="field-select" onchange="this.form.submit()">
                    <option value="all" {{ $courseFilter==='all'?'selected':'' }}>Tous les cours</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ $courseFilter==$c->id?'selected':'' }}>
                        {{ Str::limit($c->title, 30) }} ({{ $c->enrollments_count }})
                    </option>
                    @endforeach
                </select>

                {{-- Filtre statut --}}
                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                    @foreach([
                        ['all',         'Tous',           $globalStats['total']],
                        ['completed',   'Terminés',       $globalStats['completed']],
                        ['in_progress', 'En cours',       $globalStats['in_progress']],
                        ['not_started', 'Non commencés',  $globalStats['not_started']],
                    ] as [$val, $label, $count])
                    <a href="{{ route('teacher.students.index', array_merge(request()->except('status','page'), ['status' => $val])) }}"
                       class="filter-btn {{ $statusFilter===$val?'active':'' }}">
                        {{ $label }}
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:17px;height:17px;border-radius:50%;font-size:0.62rem;font-weight:700;margin-left:3px;{{ $statusFilter===$val ? 'background:rgba(37,194,110,0.2);color:#25c26e' : 'background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.35)' }}">{{ $count }}</span>
                    </a>
                    @endforeach
                </div>

                {{-- Bouton recherche --}}
                <button type="submit" class="btn-ghost" style="padding:8px 16px;font-size:0.82rem;">Filtrer</button>

                {{-- Reset --}}
                @if($search || $courseFilter !== 'all' || $statusFilter !== 'all')
                <a href="{{ route('teacher.students.index') }}" class="btn-ghost" style="padding:8px 14px;font-size:0.8rem;color:rgba(255,255,255,0.35);">✕ Réinitialiser</a>
                @endif

                {{-- Hidden fields --}}
                <input type="hidden" name="sort" value="{{ $sortBy }}">
                <input type="hidden" name="dir"  value="{{ $sortDir }}">
            </div>
        </form>

        {{-- ── TABLEAU ── --}}
        <div class="glass anim anim-4" style="overflow:hidden;">

            {{-- En-tête avec tri --}}
            <div class="tbl-header">
                <div class="col-student">
                    Apprenant
                </div>
                <div class="col-course">Cours</div>
                <div class="col-prog">
                    @php
                        $progDir = ($sortBy === 'progress_percent' && $sortDir === 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a href="{{ route('teacher.students.index', array_merge(request()->query(), ['sort'=>'progress_percent','dir'=>$progDir])) }}"
                       class="sort-btn {{ $sortBy==='progress_percent'?'sorted':'' }}">
                        Progression {{ $sortBy==='progress_percent' ? ($sortDir==='asc'?'↑':'↓') : '↕' }}
                    </a>
                </div>
                <div class="col-status">Statut</div>
                <div class="col-date">
                    @php $dateDir = ($sortBy === 'enrolled_at' && $sortDir === 'asc') ? 'desc' : 'asc'; @endphp
                    <a href="{{ route('teacher.students.index', array_merge(request()->query(), ['sort'=>'enrolled_at','dir'=>$dateDir])) }}"
                       class="sort-btn {{ $sortBy==='enrolled_at'?'sorted':'' }}" style="justify-content:flex-end;">
                        Inscrit {{ $sortBy==='enrolled_at' ? ($sortDir==='asc'?'↑':'↓') : '↕' }}
                    </a>
                </div>
                <div class="col-action"></div>
            </div>

            {{-- Lignes --}}
            @php
                $avatarColors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e','#831843','#134e4a'];
            @endphp

            @forelse($enrollments as $i => $enrollment)
            @php
                $user    = $enrollment->user;
                $course  = $enrollment->course;
                $pct     = $enrollment->progress_percent;
                $done    = !is_null($enrollment->completed_at);
                $started = $pct > 0;
                $ac      = $avatarColors[$i % count($avatarColors)];
            @endphp
            <a href="{{ route('teacher.students.show', $enrollment) }}" class="tbl-row">

                {{-- Apprenant --}}
                <div class="col-student" style="display:flex;align-items:center;gap:10px;min-width:0;">
                    <div class="avatar" style="background:{{ $ac }};">{{ $user->initials }}</div>
                    <div style="min-width:0;">
                        <div style="font-size:0.88rem;font-weight:500;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $user->full_name }}
                        </div>
                        <div style="font-size:0.72rem;color:rgba(255,255,255,0.35);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $user->email }}
                        </div>
                    </div>
                </div>

                {{-- Cours --}}
                <div class="col-course" style="min-width:0;">
                    <div style="font-size:0.8rem;font-weight:500;color:rgba(255,255,255,0.75);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ Str::limit($course->title, 24) }}
                    </div>
                    <div style="font-size:0.68rem;color:rgba(255,255,255,0.3);margin-top:1px;">
                        {{ ['beginner'=>'Débutant','intermediate'=>'Intermédiaire','advanced'=>'Avancé'][$course->level] ?? '' }}
                    </div>
                </div>

                {{-- Progression --}}
                <div class="col-prog">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:0.72rem;font-weight:600;color:{{ $done ? '#25c26e' : ($pct > 0 ? '#60a5fa' : 'rgba(255,255,255,0.3)') }};">
                            {{ $pct }}%
                        </span>
                        @if($done)
                        <span style="font-size:0.62rem;color:#25c26e;">✓ Fini</span>
                        @endif
                    </div>
                    <div class="prog-bar">
                        <div class="prog-fill" style="width:{{ $pct }}%;{{ $done ? 'background:#25c26e' : '' }}"></div>
                    </div>
                </div>

                {{-- Statut --}}
                <div class="col-status">
                    @if($done)
                    <span class="pill pill-done">✓ Terminé</span>
                    @elseif($started)
                    <span class="pill pill-prog">⏳ En cours</span>
                    @else
                    <span class="pill pill-new">○ Nouveau</span>
                    @endif
                </div>

                {{-- Date --}}
                <div class="col-date" style="font-size:0.72rem;color:rgba(255,255,255,0.3);">
                    {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d/m/Y') : '—' }}
                </div>

                {{-- Action --}}
                <div class="col-action" style="display:flex;justify-content:flex-end;">
                    <span style="font-size:0.75rem;padding:5px 10px;border-radius:8px;background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.15);color:#25c26e;font-weight:500;">
                        Voir →
                    </span>
                </div>
            </a>
            @empty
            <div style="padding:60px 20px;text-align:center;">
                <div style="font-size:3.5rem;margin-bottom:12px;">👥</div>
                @if($search || $courseFilter !== 'all' || $statusFilter !== 'all')
                <div style="font-size:0.95rem;font-weight:600;color:rgba(255,255,255,0.5);margin-bottom:6px;">Aucun résultat</div>
                <div style="font-size:0.82rem;color:rgba(255,255,255,0.3);margin-bottom:18px;">Essayez d'autres filtres.</div>
                <a href="{{ route('teacher.students.index') }}" class="btn-ghost" style="display:inline-flex;">Réinitialiser</a>
                @else
                <div style="font-size:0.95rem;font-weight:600;color:rgba(255,255,255,0.5);margin-bottom:6px;">Aucun apprenant</div>
                <div style="font-size:0.82rem;color:rgba(255,255,255,0.3);">Vos apprenants apparaîtront ici dès qu'ils s'inscrivent à vos cours.</div>
                @endif
            </div>
            @endforelse
        </div>

        {{-- ── PAGINATION ── --}}
        @if($enrollments->hasPages())
        <div style="margin-top:22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="font-size:0.78rem;color:rgba(255,255,255,0.3);">
                {{ $enrollments->firstItem() }}–{{ $enrollments->lastItem() }} sur {{ $enrollments->total() }} inscription(s)
            </div>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                {{-- Précédent --}}
                @if($enrollments->onFirstPage())
                <span class="pg-btn disabled">← Préc.</span>
                @else
                <a href="{{ $enrollments->previousPageUrl() }}" class="pg-btn">← Préc.</a>
                @endif

                {{-- Pages --}}
                @foreach($enrollments->getUrlRange(1, $enrollments->lastPage()) as $page => $url)
                @if($page === $enrollments->currentPage())
                <span class="pg-btn current">{{ $page }}</span>
                @elseif(abs($page - $enrollments->currentPage()) <= 2 || $page === 1 || $page === $enrollments->lastPage())
                <a href="{{ $url }}" class="pg-btn">{{ $page }}</a>
                @elseif(abs($page - $enrollments->currentPage()) === 3)
                <span style="padding:7px 4px;color:rgba(255,255,255,0.2);font-size:0.82rem;">…</span>
                @endif
                @endforeach

                {{-- Suivant --}}
                @if($enrollments->hasMorePages())
                <a href="{{ $enrollments->nextPageUrl() }}" class="pg-btn">Suiv. →</a>
                @else
                <span class="pg-btn disabled">Suiv. →</span>
                @endif
            </div>
        </div>
        @endif

        {{-- ── NOTE EXPORT ── --}}
        <div style="margin-top:16px;padding:12px 16px;border-radius:12px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);display:flex;align-items:center;gap:10px;">
            <span style="font-size:1rem;">💡</span>
            <span style="font-size:0.78rem;color:rgba(255,255,255,0.35);">
                L'export CSV respecte les filtres actifs (cours et statut). Idéal pour envoyer des relances ou analyser la progression dans Excel.
            </span>
        </div>

    </div>
</div>

</body>
</html>