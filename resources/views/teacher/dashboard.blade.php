{{-- resources/views/teacher/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Espace Formateur — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { font-family:'Outfit',sans-serif; background:#0f1f14; color:#e0ebe2; }
        .sidebar { width:260px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;display:flex;flex-direction:column;
                   background:linear-gradient(180deg,#081409 0%,#0a1a0f 100%);border-right:1px solid rgba(37,194,110,0.08); }
        .main-content { margin-left:260px;min-height:100vh; }
        .nav-item { display:flex;align-items:center;gap:12px;padding:10px 20px;border-radius:12px;color:rgba(255,255,255,0.45);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all 0.2s;margin:2px 12px; }
        .nav-item:hover { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.8); }
        .nav-item.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .nav-item .icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
        .nav-item.active .icon { background:rgba(37,194,110,0.18); }
        .nav-item:not(.active) .icon { background:rgba(255,255,255,0.04); }
        .glass { background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:20px; }
        .prog-bar { height:5px;border-radius:3px;background:rgba(255,255,255,0.07);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px;background:linear-gradient(90deg,#1a8a47,#25c26e); }
        .card-hover { transition:all 0.25s; }
        .card-hover:hover { transform:translateY(-2px); }
        @keyframes fadeUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.5s ease both; }
        .anim-1{animation-delay:.05s}.anim-2{animation-delay:.1s}.anim-3{animation-delay:.15s}
        .anim-4{animation-delay:.2s}.anim-5{animation-delay:.25s}.anim-6{animation-delay:.3s}
        .pill { display:inline-flex;align-items:center;padding:3px 10px;border-radius:100px;font-size:0.7rem;font-weight:700; }
        .mini-chart { display:flex;align-items:flex-end;gap:3px;height:48px; }
        .mini-bar { flex:1;border-radius:3px 3px 0 0;min-width:6px;transition:background 0.2s; }
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

    <div class="px-6 py-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-white shrink-0"
                 style="background:linear-gradient(135deg,#7a3b1e,#c4682d)">{{ $teacher->initials }}</div>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">{{ $teacher->full_name }}</div>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 inline-block"></span>
                    <span class="text-xs" style="color:rgba(255,255,255,0.4)">Formateur</span>
                </div>
            </div>
        </div>
    </div>

    <nav class="flex-1 py-4 overflow-y-auto">
        <div class="px-6 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.2)">Principal</div>
        <a href="{{ route('teacher.dashboard') }}" class="nav-item active"><span class="icon">🏠</span> Tableau de bord</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="icon">📚</span> Mes cours</a>
        <a href="{{ route('teacher.courses.create') }}" class="nav-item"><span class="icon">➕</span> Créer un cours</a>
        <a href="{{ route('teacher.quizzes.index') }}" class="nav-item"><span class="icon">📝</span> Quiz & Exercices</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.2)">Apprenants</div>
        <a href="{{route('teacher.students.index')}}" class="nav-item"><span class="icon">👥</span> Mes apprenants</a>
        <a href="#" class="nav-item">
            <span class="icon">💬</span> Forum
            @if($unreadForumCount > 0)
                <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5">{{ $unreadForumCount }}</span>
            @endif
        </a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.2)">Finances</div>
        <a href="{{route ('teacher.revenues.index')}}" class="nav-item"><span class="icon">💰</span> Revenus</a>
        <a href="{{route ('teacher.statistics.index')}}" class="nav-item"><span class="icon">📊</span> Statistiques</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.2)">Compte</div>
        <a href="#" class="nav-item"><span class="icon">👤</span> Profil public</a>
        <a href="#" class="nav-item"><span class="icon">⚙️</span> Paramètres</a>
    </nav>

    <div class="p-4 border-t border-white/5">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-left" style="background:rgba(239,68,68,0.07);color:rgba(239,68,68,0.75);">
                <span class="icon" style="background:rgba(239,68,68,0.08);">🚪</span> Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ═══ MAIN ═══ --}}
<div class="main-content">

    {{-- Topbar --}}
    <header class="sticky top-0 z-30 flex items-center justify-between px-8 py-4 border-b"
            style="background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);border-color:rgba(37,194,110,0.08)">
        <div>
            <h1 style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:#fff;">
                Tableau de bord Formateur
            </h1>
            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">{{ now()->translatedFormat('l d F Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.courses.create') }}"
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
               style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 4px 15px rgba(37,194,110,0.3)">
                ➕ Nouveau cours
            </a>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs text-white"
                 style="background:linear-gradient(135deg,#7a3b1e,#c4682d)">{{ $teacher->initials }}</div>
        </div>
    </header>

    <div class="p-8">

        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl anim"
             style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2)">
            <span class="text-xl">🎉</span>
            <p class="text-sm font-medium" style="color:#25c26e">{{ session('success') }}</p>
        </div>
        @endif

        {{-- ── STATS ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach([
                ['👥','Apprenants inscrits', $stats['total_students'], $stats['total_courses'].' cours','#25c26e'],
                ['📚','Cours publiés',       $stats['published'],      $stats['drafts'].' brouillon(s)','#3b82f6'],
                ['💰','Revenus ce mois',     number_format($revenues['this_month'],0,',',' ').' XAF',
                    ($revenues['variation'] >= 0 ? '↑' : '↓').' '.$revenues['variation'].'%','#e8b84b'],
                ['⭐','Note moyenne',        $stats['avg_rating'].' / 5', $stats['total_reviews'].' avis','#a78bfa'],
            ] as [$icon,$label,$val,$sub,$color])
            <div class="card-hover glass p-5 anim anim-{{ $loop->index+1 }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                         style="background:{{ $color }}18">{{ $icon }}</div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                          style="background:{{ $color }}15;color:{{ $color }}">{{ $sub }}</span>
                </div>
                <div class="text-2xl font-bold mb-1" style="font-family:'Playfair Display',serif;color:{{ $color }}">
                    {{ $val }}
                </div>
                <div class="text-xs font-medium" style="color:rgba(255,255,255,0.4)">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- ── MES COURS ── --}}
            <div class="lg:col-span-2 glass overflow-hidden anim anim-3">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                    <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#fff;">Mes cours</h2>
                    <a href="{{ route('teacher.courses.index') }}" class="text-xs font-semibold" style="color:#25c26e">Gérer tout →</a>
                </div>

                @forelse($courses as $course)
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-white/2 transition-colors border-b border-white/4 last:border-0">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0"
                         style="background:rgba(37,194,110,0.08)">
                        {{ ['💻','📊','🎨','🤖','📱','🔒'][$loop->index % 6] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-semibold text-white truncate">{{ $course->title }}</span>
                            <span class="pill shrink-0"
                                  style="background:{{ $course->status_color }}18;color:{{ $course->status_color }};border:1px solid {{ $course->status_color }}30">
                                {{ $course->status_label }}
                            </span>
                        </div>
                        <div class="text-xs" style="color:rgba(255,255,255,0.3)">
                            {{ $course->enrollments_count }} inscrits
                            @if($course->average_rating > 0) · ⭐ {{ $course->average_rating }} @endif
                            · {{ $course->total_lessons }} leçons
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <a href="{{ route('teacher.courses.edit', $course) }}"
                           class="text-xs px-3 py-1.5 rounded-lg font-medium transition-all hover:scale-105"
                           style="background:rgba(37,194,110,0.1);color:#25c26e">Éditer</a>
                        @if($course->status === 'draft')
                        <form method="POST" action="{{ route('teacher.courses.submit', $course) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg font-medium transition-all hover:scale-105"
                                    style="background:rgba(232,184,75,0.1);color:#e8b84b">Soumettre</button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <div class="text-4xl mb-3">📚</div>
                    <p class="text-sm mb-4" style="color:rgba(255,255,255,0.4)">Vous n'avez pas encore créé de cours.</p>
                    <a href="{{ route('teacher.courses.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
                       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                        ➕ Créer mon premier cours
                    </a>
                </div>
                @endforelse

                <div class="px-6 py-4 border-t border-white/5">
                    <a href="{{ route('teacher.courses.create') }}"
                       class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
                       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                        ➕ Créer un nouveau cours
                    </a>
                </div>
            </div>

            {{-- ── REVENUS ── --}}
            <div class="glass p-6 anim anim-4">
                <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#fff;" class="mb-1">Revenus</h2>
                <p class="text-xs mb-5" style="color:rgba(255,255,255,0.35)">{{ now()->translatedFormat('F Y') }}</p>

                <div class="mb-1">
                    <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#e8b84b;">
                        {{ number_format($revenues['this_month'], 0, ',', ' ') }}
                    </div>
                    <div class="text-sm" style="color:rgba(255,255,255,0.4)">XAF ce mois</div>
                </div>
                <div class="flex items-center gap-1.5 mb-6 text-xs font-semibold"
                     style="color:{{ $revenues['variation'] >= 0 ? '#25c26e' : '#f87171' }}">
                    {{ $revenues['variation'] >= 0 ? '↑' : '↓' }} {{ abs($revenues['variation']) }}% vs mois dernier
                </div>

                {{-- Mini graphe mensuel --}}
                <div class="mini-chart mb-2">
                    @php $maxRev = max(array_values($revenues['monthly']) ?: [1]); @endphp
                    @foreach(range(1, 12) as $month)
                    @php
                        $val = $revenues['monthly'][$month] ?? 0;
                        $h   = $maxRev > 0 ? max(8, round(($val / $maxRev) * 100)) : 8;
                        $isCurrentMonth = $month === now()->month;
                    @endphp
                    <div class="mini-bar"
                         style="height:{{ $h }}%;background:{{ $isCurrentMonth ? '#25c26e' : 'rgba(37,194,110,0.2)' }}"
                         title="{{ $val }} XAF"></div>
                    @endforeach
                </div>
                <div class="flex justify-between text-[10px]" style="color:rgba(255,255,255,0.2)">
                    <span>Jan</span><span>Avr</span><span>Juil</span><span>Oct</span><span>Déc</span>
                </div>

                {{-- Total --}}
                <div class="mt-5 p-3 rounded-xl" style="background:rgba(232,184,75,0.06);border:1px solid rgba(232,184,75,0.12)">
                    <div class="text-xs font-semibold mb-1" style="color:#e8b84b">Total cumulé</div>
                    <div class="text-lg font-bold" style="color:#fff;font-family:'Playfair Display',serif;">
                        {{ number_format($revenues['total'], 0, ',', ' ') }} XAF
                    </div>
                </div>

                {{-- Répartition par cours --}}
                @if($topCourses->count() > 0)
                <div class="mt-5 space-y-3">
                    @foreach($topCourses as $course)
                    @php $pct = $stats['total_students'] > 0 ? round(($course->enrollments_count / $stats['total_students']) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="truncate max-w-[130px]" style="color:rgba(255,255,255,0.5)">{{ Str::limit($course->title, 20) }}</span>
                            <span class="font-bold" style="color:#25c26e">{{ $pct }}%</span>
                        </div>
                        <div class="prog-bar"><div class="prog-fill" style="width:{{ $pct }}%"></div></div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── APPRENANTS RÉCENTS ── --}}
            <div class="lg:col-span-2 glass overflow-hidden anim anim-5">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                    <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#fff;">Apprenants récents</h2>
                    <a href="{{route ('teacher.students.index')}}" class="text-xs font-semibold" style="color:#25c26e">Voir tout →</a>
                </div>

                {{-- En-tête tableau --}}
                <div class="flex items-center gap-4 px-6 py-2 border-b border-white/5"
                     style="color:rgba(255,255,255,0.2);font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:.08rem;">
                    <span class="flex-1">Apprenant</span>
                    <span class="w-36 hidden md:block">Cours</span>
                    <span class="w-24 text-center">Progression</span>
                    <span class="w-20 text-right hidden lg:block">Inscrit</span>
                </div>

                @forelse($recentStudents as $enrollment)
                @php
                    $colors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e','#1e40af'];
                    $c = $colors[$loop->index % count($colors)];
                @endphp
                <div class="flex items-center gap-4 px-6 py-3.5 border-b border-white/4 hover:bg-white/2 transition-colors last:border-0">
                    <div class="flex-1 flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                             style="background:{{ $c }}">{{ $enrollment->user->initials }}</div>
                        <span class="text-sm font-medium text-white truncate">{{ $enrollment->user->full_name }}</span>
                    </div>
                    <span class="w-36 text-xs truncate hidden md:block" style="color:rgba(255,255,255,0.4)">
                        {{ Str::limit($enrollment->course->title, 20) }}
                    </span>
                    <div class="w-24">
                        <div class="prog-bar"><div class="prog-fill" style="width:{{ $enrollment->progress_percent }}%"></div></div>
                        <div class="text-[10px] mt-0.5 text-center" style="color:#25c26e">{{ $enrollment->progress_percent }}%</div>
                    </div>
                    <span class="w-20 text-right text-[10px] hidden lg:block" style="color:rgba(255,255,255,0.25)">
                        {{ $enrollment->enrolled_at->diffForHumans() }}
                    </span>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <div class="text-4xl mb-3">👥</div>
                    <p class="text-sm" style="color:rgba(255,255,255,0.35)">Aucun apprenant pour l'instant.</p>
                </div>
                @endforelse
            </div>

            {{-- ── FORUM ── --}}
            <div class="glass overflow-hidden anim anim-6">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                    <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#fff;">Forum</h2>
                    @if($unreadForumCount > 0)
                    <span class="pill" style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2)">
                        {{ $unreadForumCount }} non lu(s)
                    </span>
                    @endif
                </div>

                <div class="divide-y divide-white/5">
                    @forelse($forumThreads as $thread)
                    @php $hasNew = $thread->replies->where('user_id', '!=', $teacher->id)->count() > 0; @endphp
                    <div class="flex items-start gap-3 px-6 py-4 hover:bg-white/2 transition-colors {{ $hasNew ? '' : 'opacity-60' }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0 mt-0.5"
                             style="background:#1a8a47">{{ $thread->author->initials }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="text-xs font-semibold text-white">{{ $thread->author->first_name }}</span>
                                @if($hasNew)<span class="w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span>@endif
                            </div>
                            <div class="text-xs truncate mb-0.5" style="color:rgba(255,255,255,0.4)">{{ $thread->title }}</div>
                            <div class="text-[10px]" style="color:rgba(255,255,255,0.2)">
                                {{ $thread->replies_count }} réponse(s) · {{ $thread->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <a href="{{ route('teacher.forum.show', [$thread->course->slug, $thread]) }}"
                           class="shrink-0 text-xs px-2 py-1.5 rounded-lg font-medium"
                           style="background:rgba(37,194,110,0.1);color:#25c26e">↩ Répondre</a>
                    </div>
                    @empty
                    <div class="px-6 py-10 text-center">
                        <div class="text-4xl mb-3">💬</div>
                        <p class="text-sm" style="color:rgba(255,255,255,0.35)">Aucune discussion pour l'instant.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── ACTIVITÉ RÉCENTE ── --}}
        @if(count($recentActivity) > 0)
        <div class="glass overflow-hidden anim anim-6 mt-6">
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#fff;">Activité récente</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-white/5">
                @foreach(array_chunk($recentActivity, (int) ceil(count($recentActivity) / 2)) as $chunk)
                <div class="divide-y divide-white/5">
                    @foreach($chunk as $activity)
                    <div class="flex items-center gap-3 px-6 py-3.5 hover:bg-white/2 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0"
                             style="background:{{ $activity['color'] }}18">{{ $activity['icon'] }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-semibold text-white">{{ $activity['action'] }}</div>
                            <div class="text-xs truncate" style="color:rgba(255,255,255,0.35)">{{ $activity['detail'] }}</div>
                        </div>
                        <span class="text-[10px] shrink-0" style="color:rgba(255,255,255,0.2)">
                            {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
</body>
</html>