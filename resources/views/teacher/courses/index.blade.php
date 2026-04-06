<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mes cours — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        :root {
            --green-deep:#0d5c2e; --green-mid:#1a8a47; --green-bright:#25c26e;
            --gold:#e8b84b; --dark:#0a1a0f; --bg:#0f1f14;
        }
        body { font-family:'Outfit',sans-serif; background:var(--bg); color:#e0ebe2; margin:0; }

        /* ── Sidebar ── */
        .sidebar { width:260px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;
                   display:flex;flex-direction:column;
                   background:linear-gradient(180deg,#081409 0%,#0a1a0f 100%);
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

        /* ── Stats cards ── */
        .stat-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);
                     border-radius:16px;padding:18px 20px;transition:all 0.25s; }
        .stat-card:hover { border-color:rgba(37,194,110,0.2);transform:translateY(-2px); }

        /* ── Filtres ── */
        .filter-btn { padding:7px 16px;border-radius:100px;font-size:0.8rem;font-weight:500;
                      cursor:pointer;border:1px solid rgba(255,255,255,0.1);background:transparent;
                      color:rgba(255,255,255,0.45);transition:all 0.2s;font-family:'Outfit',sans-serif; }
        .filter-btn.active { background:rgba(37,194,110,0.12);border-color:#25c26e;color:#25c26e; }
        .filter-btn:hover:not(.active) { border-color:rgba(255,255,255,0.25);color:rgba(255,255,255,0.75); }

        /* ── Course cards ── */
        .course-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);
                       border-radius:20px;overflow:hidden;transition:all 0.3s;display:flex;flex-direction:column; }
        .course-card:hover { border-color:rgba(37,194,110,0.25);transform:translateY(-3px);
                             box-shadow:0 16px 40px rgba(0,0,0,0.3); }
        .course-thumb { height:160px;display:flex;align-items:center;justify-content:center;
                        font-size:3.5rem;position:relative;overflow:hidden; }
        .course-thumb-overlay { position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(0,0,0,0.5)); }

        /* ── Status pills ── */
        .pill { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                border-radius:100px;font-size:0.7rem;font-weight:700; }
        .pill-dot { width:6px;height:6px;border-radius:50%;flex-shrink:0; }
        .pill-published { background:rgba(37,194,110,0.12);color:#25c26e;border:1px solid rgba(37,194,110,0.25); }
        .pill-draft     { background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.45);border:1px solid rgba(255,255,255,0.1); }
        .pill-pending   { background:rgba(232,184,75,0.12);color:#e8b84b;border:1px solid rgba(232,184,75,0.25); }
        .pill-rejected  { background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2); }

        /* ── Micro stats ── */
        .micro-stat { display:flex;flex-direction:column;align-items:center;gap:2px;
                      padding:8px 12px;border-radius:10px;background:rgba(255,255,255,0.03);
                      border:1px solid rgba(255,255,255,0.06);flex:1; }
        .micro-stat-val { font-size:1rem;font-weight:700;color:#fff;line-height:1; }
        .micro-stat-lbl { font-size:0.62rem;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:.04rem; }

        /* ── Progress bar ── */
        .prog-bar { height:4px;border-radius:2px;background:rgba(255,255,255,0.07);overflow:hidden; }
        .prog-fill { height:100%;border-radius:2px;background:linear-gradient(90deg,#1a8a47,#25c26e); }

        /* ── Actions ── */
        .action-btn { display:inline-flex;align-items:center;gap:6px;padding:7px 13px;
                      border-radius:9px;font-size:0.78rem;font-weight:500;cursor:pointer;
                      border:none;transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .action-edit  { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
        .action-edit:hover  { background:rgba(37,194,110,0.2); }
        .action-stats { background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2); }
        .action-stats:hover { background:rgba(59,130,246,0.2); }
        .action-submit{ background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }
        .action-submit:hover { background:rgba(232,184,75,0.2); }
        .action-del   { background:rgba(239,68,68,0.08);color:#f87171;border:1px solid rgba(239,68,68,0.15); }
        .action-del:hover   { background:rgba(239,68,68,0.15); }
        .action-primary { background:linear-gradient(135deg,#1a8a47,#25c26e);color:#fff; }
        .action-primary:hover { transform:translateY(-1px);box-shadow:0 6px 18px rgba(37,194,110,0.3); }

        /* ── Search ── */
        .search-input { background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);
                        border-radius:12px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.88rem;
                        padding:10px 16px 10px 42px;outline:none;transition:all 0.25s;width:280px; }
        .search-input::placeholder { color:rgba(255,255,255,0.25); }
        .search-input:focus { border-color:#25c26e;background:rgba(37,194,110,0.05); }

        /* ── Empty ── */
        .empty-state { text-align:center;padding:60px 20px; }

        /* ── Animations ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.45s ease both; }
        .anim-1{animation-delay:.05s} .anim-2{animation-delay:.1s} .anim-3{animation-delay:.15s}
        .anim-4{animation-delay:.2s}  .anim-5{animation-delay:.25s}

        /* ── Dropdown menu ── */
        .dropdown { position:relative; }
        .dropdown-menu { position:absolute;right:0;top:calc(100% + 6px);z-index:50;
                         min-width:180px;background:#0d1f13;border:1px solid rgba(255,255,255,0.1);
                         border-radius:12px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.5); }
        .dropdown-item { display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:0.82rem;
                         color:rgba(255,255,255,0.65);cursor:pointer;transition:background 0.15s;
                         text-decoration:none;font-family:'Outfit',sans-serif;border:none;width:100%;background:transparent; }
        .dropdown-item:hover { background:rgba(255,255,255,0.05);color:#fff; }
        .dropdown-item.danger { color:#f87171; }
        .dropdown-item.danger:hover { background:rgba(239,68,68,0.08); }
        .dropdown-sep { height:1px;background:rgba(255,255,255,0.06);margin:4px 0; }

        /* ── Tooltips ── */
        [data-tip] { position:relative; }
        [data-tip]:hover::after { content:attr(data-tip);position:absolute;bottom:calc(100%+6px);left:50%;
            transform:translateX(-50%);background:#0d1f13;border:1px solid rgba(255,255,255,0.1);
            color:rgba(255,255,255,0.8);font-size:0.72rem;padding:4px 10px;border-radius:7px;
            white-space:nowrap;pointer-events:none;z-index:100; }

        /* ── Grid ── */
        .courses-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:18px; }
        @media(max-width:1280px) { .courses-grid { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:900px)  { .courses-grid { grid-template-columns:1fr; } }
    </style>
</head>
<body x-data="coursesPage()">

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>

    {{-- Profil --}}
    <div class="px-6 py-4 border-b border-white/5">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#7a3b1e,#c4682d);display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;color:#fff;flex-shrink:0;">
                {{ auth()->user()->initials }}
            </div>
            <div style="min-width:0;">
                <div style="font-size:0.85rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->full_name }}</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);">Formateur vérifié</div>
            </div>
        </div>
    </div>

    <nav style="flex:1;padding:16px 0;overflow-y:auto;">
        <div style="padding:0 24px 8px;font-size:0.62rem;text-transform:uppercase;letter-spacing:.08rem;font-weight:700;color:rgba(255,255,255,0.2);">Principal</div>
        <a href="{{ route('teacher.dashboard') }}" class="nav-item"><span class="nav-icon">🏠</span> Tableau de bord</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item active"><span class="nav-icon">📚</span> Mes cours</a>
        <a href="{{ route('teacher.courses.create') }}" class="nav-item"><span class="nav-icon">➕</span> Créer un cours</a>
        <a href="{{ route('teacher.quizzes.index') }}" class="nav-item"><span class="nav-icon">📝</span> Quiz & Exercices</a>
        <div style="padding:16px 24px 8px;font-size:0.62rem;text-transform:uppercase;letter-spacing:.08rem;font-weight:700;color:rgba(255,255,255,0.2);">Apprenants</div>
        <a href="{{route ('teacher.students.index')}}" class="nav-item"><span class="nav-icon">👥</span> Mes apprenants</a>
        <a href="#" class="nav-item"><span class="nav-icon">💬</span> Forum
            @if(isset($unreadForumCount) && $unreadForumCount > 0)
            <span style="margin-left:auto;background:#ef4444;color:#fff;font-size:0.65rem;font-weight:700;border-radius:100px;padding:1px 6px;">{{ $unreadForumCount }}</span>
            @endif
        </a>
        <div style="padding:16px 24px 8px;font-size:0.62rem;text-transform:uppercase;letter-spacing:.08rem;font-weight:700;color:rgba(255,255,255,0.2);">Finances</div>
        <a href="#" class="nav-item"><span class="nav-icon">💰</span> Revenus</a>
        <a href="#" class="nav-item"><span class="nav-icon">📊</span> Statistiques</a>
    </nav>

    <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,0.05);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:rgba(239,68,68,0.07);color:rgba(239,68,68,0.75);border:none;cursor:pointer;">
                <span class="nav-icon" style="background:rgba(239,68,68,0.08);">🚪</span> Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ═══ MAIN ═══ --}}
<div class="main-content">

    {{-- Topbar --}}
    <header style="position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;padding:14px 32px;border-bottom:1px solid rgba(37,194,110,0.08);background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);">
        <div>
            <h1 style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:#fff;">Mes cours</h1>
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:2px;">
                {{ $globalStats['total'] }} cours au total · {{ $globalStats['published'] }} publiés
            </p>
        </div>
        <a href="{{ route('teacher.courses.create') }}" class="action-btn action-primary" style="font-size:0.88rem;padding:10px 20px;">
            ➕ Nouveau cours
        </a>
    </header>

    <div style="padding:28px 32px;">

        {{-- Alerts --}}
        @if(session('success'))
        <div class="anim" style="margin-bottom:20px;display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:14px;background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2);">
            <span>🎉</span><span style="font-size:0.88rem;color:#25c26e;">{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="anim" style="margin-bottom:20px;display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:14px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);">
            <span>⚠</span><span style="font-size:0.88rem;color:#f87171;">{{ session('error') }}</span>
        </div>
        @endif

        {{-- ── STATS GLOBALES ── --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;">
            @foreach([
                ['👥', $globalStats['students'],         'Apprenants',    '#25c26e'],
                ['📚', $globalStats['total'],            'Cours total',   '#3b82f6'],
                ['💰', number_format($globalStats['revenue'],0,',',' ').' XAF', 'Revenus nets', '#e8b84b'],
                ['⭐', $globalStats['avg_rating'].' / 5','Note moyenne',  '#a78bfa'],
            ] as [$icon, $val, $label, $color])
            <div class="stat-card anim anim-{{ $loop->index+1 }}">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <div style="width:38px;height:38px;border-radius:10px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">{{ $icon }}</div>
                </div>
                <div style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:{{ $color }};line-height:1;">{{ $val }}</div>
                <div style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:4px;">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        {{-- ── FILTRES & RECHERCHE ── --}}
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:22px;flex-wrap:wrap;" class="anim anim-3">

            {{-- Filtres statut --}}
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                @foreach([
                    ['all',       'Tous',          $globalStats['total']],
                    ['published', 'Publiés',       $globalStats['published']],
                    ['draft',     'Brouillons',    $globalStats['draft']],
                    ['pending',   'En révision',   $globalStats['pending']],
                    ['rejected',  'Refusés',       $globalStats['rejected']],
                ] as [$val, $label, $count])
                @if($count > 0 || $val === 'all')
                <a href="{{ route('teacher.courses.index', array_merge(request()->query(), ['status' => $val])) }}"
                   class="filter-btn {{ $statusFilter === $val ? 'active' : '' }}">
                    {{ $label }}
                    @if($count > 0)
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;font-size:0.65rem;font-weight:700;margin-left:4px;{{ $statusFilter === $val ? 'background:rgba(37,194,110,0.2);color:#25c26e' : 'background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.4)' }}">{{ $count }}</span>
                    @endif
                </a>
                @endif
                @endforeach
            </div>

            {{-- Recherche --}}
            <form method="GET" action="{{ route('teacher.courses.index') }}" style="position:relative;">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:0.9rem;color:rgba(255,255,255,0.3);">🔍</span>
                <input type="text" name="search" value="{{ $search }}"
                       class="search-input" placeholder="Rechercher un cours..."
                       x-on:keydown.escape="$el.value=''; $el.form.submit()">
            </form>
        </div>

        {{-- ── GRILLE DES COURS ── --}}
        @if($courses->isEmpty())
        <div class="empty-state anim anim-3" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:20px;">
            @if($search || $statusFilter !== 'all')
            <div style="font-size:3.5rem;margin-bottom:14px;">🔍</div>
            <div style="font-size:1rem;font-weight:600;color:rgba(255,255,255,0.6);margin-bottom:6px;">Aucun cours trouvé</div>
            <div style="font-size:0.85rem;color:rgba(255,255,255,0.3);margin-bottom:20px;">Essayez d'autres filtres ou termes de recherche.</div>
            <a href="{{ route('teacher.courses.index') }}" class="action-btn action-edit" style="display:inline-flex;">Réinitialiser les filtres</a>
            @else
            <div style="font-size:4rem;margin-bottom:14px;">📚</div>
            <div style="font-size:1rem;font-weight:600;color:rgba(255,255,255,0.6);margin-bottom:6px;">Aucun cours pour l'instant</div>
            <div style="font-size:0.85rem;color:rgba(255,255,255,0.3);margin-bottom:24px;">Créez votre premier cours et commencez à partager votre expertise.</div>
            <a href="{{ route('teacher.courses.create') }}" class="action-btn action-primary" style="display:inline-flex;padding:12px 24px;font-size:0.9rem;">
                ➕ Créer mon premier cours
            </a>
            @endif
        </div>

        @else
        <div class="courses-grid">
            @php
                $gradients = [
                    'from-[#0d5c2e] to-[#1a8a47]',
                    'from-[#7a3b1e] to-[#c4682d]',
                    'from-[#1a2a6c] to-[#4a4aad]',
                    'from-[#5b21b6] to-[#7c3aed]',
                    'from-[#065f46] to-[#059669]',
                    'from-[#92400e] to-[#b45309]',
                ];
                $icons = ['💻','📊','🎨','🤖','📱','🔒','📈','🌐','🔧'];
            @endphp

            @foreach($courses as $course)
            @php
                $gi = $loop->index % count($gradients);
                $revenue = $revenuesByCourse[$course->id] ?? 0;
                $avgRating = round($course->reviews_avg_rating ?? 0, 1);
                $totalLessons = $course->chapters->sum('lessons_count');
            @endphp
            <div class="course-card anim anim-{{ ($loop->index % 3) + 1 }}"
                 x-data="{ menuOpen: false }">

                {{-- Thumbnail --}}
                <div class="course-thumb bg-gradient-to-br {{ $gradients[$gi] }}"
                     style="position:relative;">
                    @if($course->thumbnail)
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                         style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;">
                    @else
                    <span style="font-size:3.5rem;position:relative;z-index:1;">{{ $icons[$loop->index % count($icons)] }}</span>
                    @endif
                    <div class="course-thumb-overlay"></div>

                    {{-- Status badge (sur la thumb) --}}
                    <div style="position:absolute;top:10px;left:10px;z-index:2;">
                        <span class="pill pill-{{ $course->status }}">
                            <span class="pill-dot" style="background:currentColor;opacity:0.7;"></span>
                            {{ $course->status_label }}
                        </span>
                    </div>

                    {{-- Menu 3 points --}}
                    <div class="dropdown" style="position:absolute;top:8px;right:8px;z-index:2;">
                        <button type="button" x-on:click.stop="menuOpen = !menuOpen"
                                style="width:30px;height:30px;border-radius:8px;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.15);color:#fff;cursor:pointer;font-size:0.9rem;display:flex;align-items:center;justify-content:center;">
                            ⋮
                        </button>
                        <div class="dropdown-menu" x-show="menuOpen" x-on:click.outside="menuOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100">
                            <a href="{{ route('teacher.courses.edit', $course) }}" class="dropdown-item">
                                ✏️ Modifier le cours
                            </a>
                            <a href="{{ route('teacher.courses.edit', $course) }}?tab=content" class="dropdown-item">
                                📚 Gérer les chapitres
                            </a>
                            <a href="{{ route('teacher.quizzes.index') }}" class="dropdown-item">
                                📝 Voir les quiz
                            </a>
                            <div class="dropdown-sep"></div>
                            @if($course->status === 'draft')
                            <form method="POST" action="{{ route('teacher.courses.submit', $course) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="dropdown-item" style="color:#e8b84b;">
                                    🚀 Soumettre pour validation
                                </button>
                            </form>
                            @endif
                            @if(in_array($course->status, ['draft', 'rejected']))
                            <div class="dropdown-sep"></div>
                            <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}"
                                  x-on:submit.prevent="confirm('Supprimer « {{ addslashes($course->title) }} » ?') && $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item danger">🗑 Supprimer</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div style="padding:18px;flex:1;display:flex;flex-direction:column;gap:12px;">

                    {{-- Titre + Catégorie --}}
                    <div>
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:4px;">
                            <h3 style="font-size:0.92rem;font-weight:600;color:#fff;line-height:1.3;flex:1;">
                                {{ Str::limit($course->title, 50) }}
                            </h3>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            @if($course->category)
                            <span style="font-size:0.68rem;padding:2px 8px;border-radius:100px;background:rgba(37,194,110,0.1);border:1px solid rgba(37,194,110,0.2);color:#25c26e;">{{ $course->category }}</span>
                            @endif
                            <span style="font-size:0.68rem;color:rgba(255,255,255,0.3);">{{ ['beginner'=>'Débutant','intermediate'=>'Intermédiaire','advanced'=>'Avancé'][$course->level] ?? $course->level }}</span>
                        </div>
                    </div>

                    {{-- Micro-stats --}}
                    <div style="display:flex;gap:6px;">
                        <div class="micro-stat" data-tip="Apprenants inscrits">
                            <div class="micro-stat-val">{{ $course->enrollments_count }}</div>
                            <div class="micro-stat-lbl">Inscrits</div>
                        </div>
                        <div class="micro-stat" data-tip="Leçons au total">
                            <div class="micro-stat-val">{{ $totalLessons }}</div>
                            <div class="micro-stat-lbl">Leçons</div>
                        </div>
                        <div class="micro-stat" data-tip="Note moyenne">
                            <div class="micro-stat-val" style="color:#e8b84b;">{{ $avgRating > 0 ? $avgRating : '—' }}</div>
                            <div class="micro-stat-lbl">Note</div>
                        </div>
                        <div class="micro-stat" data-tip="Revenus nets générés">
                            <div class="micro-stat-val" style="font-size:0.78rem;color:#25c26e;">
                                {{ $revenue > 0 ? number_format($revenue/1000, 0).'K' : '0' }}
                            </div>
                            <div class="micro-stat-lbl">XAF nets</div>
                        </div>
                    </div>

                    {{-- Chapitres progress --}}
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:0.72rem;color:rgba(255,255,255,0.35);margin-bottom:5px;">
                            <span>Chapitres</span>
                            <span>{{ $course->chapters->count() }}</span>
                        </div>
                        @php $chapterPct = min(100, $course->chapters->count() * 20); @endphp
                        <div class="prog-bar">
                            <div class="prog-fill" style="width:{{ $chapterPct }}%"></div>
                        </div>
                        @if($course->chapters->count() > 0)
                        <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:4px;">
                            @foreach($course->chapters->take(4) as $ch)
                            <span style="font-size:0.65rem;padding:2px 7px;border-radius:6px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);color:rgba(255,255,255,0.4);">
                                {{ Str::limit($ch->title, 16) }}
                                <span style="color:rgba(255,255,255,0.25);">· {{ $ch->lessons_count }}</span>
                            </span>
                            @endforeach
                            @if($course->chapters->count() > 4)
                            <span style="font-size:0.65rem;padding:2px 7px;border-radius:6px;background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.3);">
                                +{{ $course->chapters->count() - 4 }} autres
                            </span>
                            @endif
                        </div>
                        @endif
                    </div>

                    {{-- Prix --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            @if($course->is_free)
                            <span style="font-size:0.9rem;font-weight:700;color:#25c26e;">Gratuit</span>
                            @else
                            <span style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#e8b84b;">
                                {{ number_format($course->price, 0, ',', ' ') }} XAF
                            </span>
                            @endif
                        </div>
                        <div style="font-size:0.7rem;color:rgba(255,255,255,0.3);">
                            {{ $course->duration_formatted }}
                        </div>
                    </div>

                    {{-- Alerte cours rejeté --}}
                    @if($course->status === 'rejected')
                    <div style="padding:8px 12px;border-radius:9px;background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.15);">
                        <div style="font-size:0.75rem;color:#f87171;font-weight:500;">⚠ Cours refusé</div>
                        <div style="font-size:0.7rem;color:rgba(239,68,68,0.6);margin-top:2px;">Corrigez les problèmes et soumettez à nouveau.</div>
                    </div>
                    @endif

                    {{-- CTA bas de card --}}
                    <div style="display:flex;gap:7px;margin-top:auto;padding-top:4px;border-top:1px solid rgba(255,255,255,0.05);">
                        <a href="{{ route('teacher.courses.edit', $course) }}"
                           class="action-btn action-edit" style="flex:1;justify-content:center;">
                            ✏️ Éditer
                        </a>

                        @if($course->status === 'published')
                        <a href="{{ route('teacher.students.index') }}?course={{ $course->id }}"
                           class="action-btn action-stats" data-tip="Voir les apprenants">
                            👥
                        </a>
                        @endif

                        @if($course->status === 'draft')
                        <form method="POST" action="{{ route('teacher.courses.submit', $course) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="action-btn action-submit" data-tip="Soumettre pour validation">
                                🚀
                            </button>
                        </form>
                        @endif

                        @if(in_array($course->status, ['draft', 'rejected']))
                        <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}"
                              x-on:submit.prevent="confirm('Supprimer « {{ addslashes($course->title) }} » définitivement ?') && $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn action-del" data-tip="Supprimer">🗑</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ── PAGINATION ── --}}
        @if($courses->hasPages())
        <div style="margin-top:28px;display:flex;align-items:center;justify-content:center;gap:6px;">
            {{-- Précédent --}}
            @if($courses->onFirstPage())
            <span style="padding:8px 14px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);color:rgba(255,255,255,0.2);font-size:0.82rem;">← Préc.</span>
            @else
            <a href="{{ $courses->previousPageUrl() }}"
               style="padding:8px 14px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.65);font-size:0.82rem;text-decoration:none;transition:all 0.2s;"
               onmouseover="this.style.borderColor='#25c26e';this.style.color='#25c26e'"
               onmouseout="this.style.borderColor='rgba(255,255,255,0.1)';this.style.color='rgba(255,255,255,0.65)'">
                ← Préc.
            </a>
            @endif

            {{-- Pages --}}
            @foreach($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
            @if($page === $courses->currentPage())
            <span style="padding:8px 14px;border-radius:10px;background:rgba(37,194,110,0.12);border:1px solid #25c26e;color:#25c26e;font-size:0.82rem;font-weight:600;">{{ $page }}</span>
            @elseif(abs($page - $courses->currentPage()) <= 2)
            <a href="{{ $url }}"
               style="padding:8px 14px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.55);font-size:0.82rem;text-decoration:none;transition:all 0.2s;"
               onmouseover="this.style.borderColor='#25c26e';this.style.color='#25c26e'"
               onmouseout="this.style.borderColor='rgba(255,255,255,0.1)';this.style.color='rgba(255,255,255,0.55)'">{{ $page }}</a>
            @endif
            @endforeach

            {{-- Suivant --}}
            @if($courses->hasMorePages())
            <a href="{{ $courses->nextPageUrl() }}"
               style="padding:8px 14px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.65);font-size:0.82rem;text-decoration:none;transition:all 0.2s;"
               onmouseover="this.style.borderColor='#25c26e';this.style.color='#25c26e'"
               onmouseout="this.style.borderColor='rgba(255,255,255,0.1)';this.style.color='rgba(255,255,255,0.65)'">
                Suiv. →
            </a>
            @else
            <span style="padding:8px 14px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);color:rgba(255,255,255,0.2);font-size:0.82rem;">Suiv. →</span>
            @endif
        </div>
        <div style="text-align:center;margin-top:10px;font-size:0.75rem;color:rgba(255,255,255,0.25);">
            {{ $courses->firstItem() }}–{{ $courses->lastItem() }} sur {{ $courses->total() }} cours
        </div>
        @endif

        @endif {{-- end $courses->isEmpty() --}}
    </div>
</div>

<script>
function coursesPage() {
    return {
        init() {
            // Fermer les dropdowns en cliquant ailleurs
            document.addEventListener('click', () => {
                this.$el.querySelectorAll('[x-data]').forEach(el => {
                    if (el._x_dataStack) el._x_dataStack[0].menuOpen = false;
                });
            });
        }
    }
}
</script>
</body>
</html>