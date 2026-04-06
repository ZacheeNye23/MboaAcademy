{{-- resources/views/student/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mon Espace — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { font-family:'Outfit',sans-serif; background:#f4f7f4; color:#1c2b1f; }
        .sidebar { width:260px;min-height:100vh;background:#0a1a0f;position:fixed;left:0;top:0;bottom:0;z-index:40;display:flex;flex-direction:column; }
        .main-content { margin-left:260px;min-height:100vh; }
        .nav-item { display:flex;align-items:center;gap:12px;padding:10px 20px;border-radius:12px;color:rgba(255,255,255,0.5);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all 0.2s;margin:2px 12px; }
        .nav-item:hover { background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.85); }
        .nav-item.active { background:rgba(37,194,110,0.15);color:#25c26e; }
        .nav-item .icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
        .nav-item.active .icon { background:rgba(37,194,110,0.2); }
        .nav-item:not(.active) .icon { background:rgba(255,255,255,0.05); }
        .prog-bar { height:6px;border-radius:3px;background:rgba(0,0,0,0.08);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px;background:linear-gradient(90deg,#1a8a47,#25c26e); }
        .card-hover { transition:all 0.25s; }
        .card-hover:hover { transform:translateY(-3px);box-shadow:0 12px 30px rgba(0,0,0,0.10); }
        @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.5s ease both; }
        .anim-1{animation-delay:.05s}.anim-2{animation-delay:.1s}.anim-3{animation-delay:.15s}
        .anim-4{animation-delay:.2s}.anim-5{animation-delay:.25s}.anim-6{animation-delay:.3s}
        @keyframes flicker { 0%,100%{transform:scale(1) rotate(-2deg)} 50%{transform:scale(1.15) rotate(2deg)} }
        .flame { animation:flicker 1.5s ease-in-out infinite;display:inline-block; }
    </style>
</head>
<body>

{{-- ═══════ SIDEBAR ═══════ --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" class="font-playfair text-xl font-black text-white" style="font-family:'Playfair Display',serif">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div class="mt-1 text-xs font-semibold uppercase tracking-widest" style="color:#25c26e;">Espace Apprenant</div>
    </div>

    <div class="px-6 py-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-white shrink-0"
                 style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                {{ $user->initials }}
            </div>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">{{ $user->full_name }}</div>
                <div class="text-xs" style="color:rgba(255,255,255,0.4)">Apprenant</div>
            </div>
        </div>
    </div>

    <nav class="flex-1 py-4 overflow-y-auto">
        <div class="px-6 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.2)">Principal</div>
        <a href="{{ route('student.dashboard') }}" class="nav-item active"><span class="icon">🏠</span> Tableau de bord</a>
        <a href="{{ route('student.courses.mine') }}" class="nav-item"><span class="icon">📚</span> Mes cours</a>
        <a href="{{ route('student.courses.index') }}" class="nav-item"><span class="icon">🔍</span> Explorer</a>
        <a href="{{ route('student.quizzes.index') }}" class="nav-item">
            <span class="icon">📝</span> Mes quiz
            @if($pendingQuizzes->count() > 0)
                <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5">{{ $pendingQuizzes->count() }}</span>
            @endif
        </a>
        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.2)">Communauté</div>
        <a href="#" class="nav-item"><span class="icon">💬</span> Forum</a>
        <a href="{{ route('student.badges.index') }}" class="nav-item"><span class="icon">🏆</span> Badges</a>
        <a href="{{ route('student.certificates.index') }}" class="nav-item"><span class="icon">🎓</span> Certificats</a>
        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.2)">Compte</div>
        <a href="#" class="nav-item"><span class="icon">👤</span> Profil</a>
        <a href="#" class="nav-item"><span class="icon">⚙️</span> Paramètres</a>
    </nav>

    <div class="p-4 border-t border-white/5">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-left" style="background:rgba(239,68,68,0.08);color:rgba(239,68,68,0.8);">
                <span class="icon" style="background:rgba(239,68,68,0.1);">🚪</span> Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ═══════ MAIN ═══════ --}}
<div class="main-content">
    <header class="sticky top-0 z-30 flex items-center justify-between px-8 py-4 bg-white/80 backdrop-blur border-b border-black/5">
        <div>
            <h1 class="text-xl font-bold text-gray-900" style="font-family:'Playfair Display',serif">
                Bonjour, {{ $user->first_name }} 👋
            </h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ now()->translatedFormat('l d F Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($streak->current_streak > 0)
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl" style="background:rgba(232,184,75,0.1);border:1px solid rgba(232,184,75,0.2)">
                <span class="flame text-base">🔥</span>
                <span class="text-sm font-bold" style="color:#e8b84b">{{ $streak->current_streak }} jour{{ $streak->current_streak > 1 ? 's' : '' }}</span>
            </div>
            @endif
            <a href="{{ route('student.dashboard') }}" class="relative w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
                🔔
                @if($user->unreadNotifications->count() > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-[9px] font-bold text-white flex items-center justify-center">{{ $user->unreadNotifications->count() }}</span>
                @endif
            </a>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs text-white"
                 style="background:linear-gradient(135deg,#1a8a47,#25c26e)">{{ $user->initials }}</div>
        </div>
    </header>

    <div class="p-8">

        {{-- Succès --}}
        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl anim" style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2)">
            <span class="text-xl">🎉</span>
            <p class="text-sm font-medium" style="color:#1a8a47">{{ session('success') }}</p>
        </div>
        @endif

        {{-- ── STATS ──────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach([
                ['📚','Cours inscrits',       $stats['total_enrolled'],    $stats['completed_courses'].' terminé(s)','#25c26e'],
                ['✅','Leçons complétées',    $stats['lessons_completed'], 'au total','#3b82f6'],
                ['🏆','Badges obtenus',       $stats['badges_count'],      'sur '.($allBadges->count()).' disponibles','#e8b84b'],
                ['📊','Score moyen quiz',      $stats['avg_quiz_score'].'%','sur tous les quiz','#a78bfa'],
            ] as [$icon,$label,$val,$sub,$color])
            <div class="card-hover anim anim-{{ $loop->index+1 }} bg-white rounded-2xl p-5 border border-black/5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-2xl">{{ $icon }}</span>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background:{{ $color }}18;color:{{ $color }}">{{ $sub }}</span>
                </div>
                <div class="text-3xl font-bold mb-1" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
                <div class="text-xs text-gray-400 font-medium">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- ── MES COURS EN COURS ──────────────────────── --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-black/5 overflow-hidden anim anim-3">
                <div class="flex items-center justify-between px-6 py-4 border-b border-black/5">
                    <h2 class="text-lg font-bold" style="font-family:'Playfair Display',serif">Mes cours en cours</h2>
                    <a href="{{ route('student.courses.mine') }}" class="text-xs font-semibold hover:underline" style="color:#25c26e">Voir tout →</a>
                </div>

                @forelse($enrollments as $enrollment)
                @php
                    $course     = $enrollment->course;
                    $totalLessons = $course->lessons->count();
                    $pct        = $enrollment->progress_percent;
                    $icons      = ['💻','📊','🎨','🤖','📱','🔒'];
                    $icon       = $icons[$loop->index % count($icons)];
                @endphp
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition-colors border-b border-black/5 last:border-0">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
                         style="background:rgba(37,194,110,0.1)">{{ $icon }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-800 truncate mb-1">{{ $course->title }}</div>
                        <div class="prog-bar mb-1"><div class="prog-fill" style="width:{{ $pct }}%"></div></div>
                        <div class="flex justify-between text-xs text-gray-400">
                            <span>{{ $totalLessons }} leçons</span>
                            <span class="font-semibold" style="color:#25c26e">{{ $pct }}%</span>
                        </div>
                    </div>
                    <a href="{{ route('student.courses.learn', $course->slug) }}"
                       class="shrink-0 w-9 h-9 rounded-xl flex items-center justify-center text-sm transition-all hover:scale-110 text-white"
                       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">▶</a>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <div class="text-4xl mb-3">📚</div>
                    <p class="text-gray-400 text-sm mb-4">Vous n'êtes inscrit à aucun cours pour l'instant.</p>
                    <a href="{{ route('student.courses.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
                       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                        🔍 Explorer les cours
                    </a>
                </div>
                @endforelse

                @if($enrollments->count() > 0)
                <div class="px-6 py-4 border-t border-black/5">
                    <a href="{{ route('student.courses.index') }}"
                       class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5"
                       style="background:rgba(37,194,110,0.08);color:#1a8a47;border:1px solid rgba(37,194,110,0.2)">
                        + Explorer plus de cours
                    </a>
                </div>
                @endif
            </div>

            {{-- ── PROGRESSION GLOBALE ──────────────────────── --}}
            <div class="bg-white rounded-2xl border border-black/5 p-6 anim anim-4">
                <h2 class="text-lg font-bold mb-6" style="font-family:'Playfair Display',serif">Progression globale</h2>

                {{-- Anneau SVG --}}
                <div class="flex justify-center mb-6 relative">
                    @php $circumference = 2 * M_PI * 48; $offset = $circumference * (1 - $avgProgress / 100); @endphp
                    <svg width="120" height="120" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="48" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="10"/>
                        <circle cx="60" cy="60" r="48" fill="none" stroke="#25c26e" stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $offset }}"
                                style="transform:rotate(-90deg);transform-origin:center"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-bold" style="font-family:'Playfair Display',serif;color:#1a8a47">{{ $avgProgress }}%</span>
                        <span class="text-xs text-gray-400">complété</span>
                    </div>
                </div>

                {{-- Par cours --}}
                <div class="space-y-3">
                    @forelse($enrollments->take(3) as $enrollment)
                    <div>
                        <div class="flex justify-between text-xs font-medium mb-1">
                            <span class="text-gray-600 truncate max-w-[140px]">{{ Str::limit($enrollment->course->title, 22) }}</span>
                            <span style="color:#25c26e">{{ $enrollment->progress_percent }}%</span>
                        </div>
                        <div class="prog-bar"><div class="prog-fill" style="width:{{ $enrollment->progress_percent }}%"></div></div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 text-center py-4">Aucun cours en cours</p>
                    @endforelse
                </div>

                {{-- Streak --}}
                @if($streak->current_streak > 0)
                <div class="mt-5 p-3 rounded-xl flex items-center gap-3" style="background:rgba(232,184,75,0.08);border:1px solid rgba(232,184,75,0.2)">
                    <span class="flame text-2xl">🔥</span>
                    <div>
                        <div class="text-sm font-bold" style="color:#e8b84b">{{ $streak->current_streak }} jours de suite !</div>
                        <div class="text-xs text-gray-400">Record : {{ $streak->longest_streak }} jours</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- ── QUIZ À FAIRE ────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-black/5 overflow-hidden anim anim-5">
                <div class="flex items-center justify-between px-6 py-4 border-b border-black/5">
                    <h2 class="text-lg font-bold" style="font-family:'Playfair Display',serif">Quiz à faire</h2>
                    @if($pendingQuizzes->count() > 0)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:rgba(59,130,246,0.1);color:#3b82f6;border:1px solid rgba(59,130,246,0.2)">
                        {{ $pendingQuizzes->count() }} en attente
                    </span>
                    @endif
                </div>
                <div class="divide-y divide-black/5">
                    @forelse($pendingQuizzes as $data)
                    @php $quiz = $data['quiz'] ?? $data; @endphp
                    <div class="flex items-center gap-3 px-6 py-3.5 hover:bg-gray-50 transition-colors">
                        <span class="text-xl">📝</span>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-800 truncate">{{ $quiz->title }}</div>
                            <div class="text-xs text-gray-400">{{ $quiz->questions->count() }} questions · {{ $quiz->passing_score }}% requis</div>
                        </div>
                        <a href="{{ route('student.quizzes.show', $quiz) }}"
                           class="shrink-0 text-xs font-bold px-3 py-1.5 rounded-lg transition-all hover:scale-105"
                           style="background:rgba(37,194,110,0.1);color:#1a8a47">Start</a>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center">
                        <div class="text-3xl mb-2">✅</div>
                        <p class="text-sm text-gray-400">Aucun quiz en attente</p>
                    </div>
                    @endforelse
                </div>
                <div class="px-6 py-3 border-t border-black/5">
                    <a href="{{ route('student.quizzes.index') }}" class="block text-center text-xs font-semibold py-2 rounded-xl" style="background:rgba(37,194,110,0.08);color:#1a8a47">Voir tous les quiz →</a>
                </div>
            </div>

            {{-- ── BADGES ──────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-black/5 p-6 anim anim-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold" style="font-family:'Playfair Display',serif">Mes badges</h2>
                    <span class="text-xs text-gray-400">{{ $earnedBadges->count() }} / {{ $allBadges->count() }}</span>
                </div>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    @foreach($allBadges->take(6) as $badge)
                    @php $earned = $earnedBadges->contains($badge->id); @endphp
                    <div class="flex flex-col items-center gap-1" title="{{ $badge->name }}">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl border-2 transition-all hover:scale-110"
                             style="background:{{ $earned ? $badge->color.'18' : 'rgba(0,0,0,0.03)' }};
                                    border-color:{{ $earned ? $badge->color : 'rgba(0,0,0,0.08)' }};
                                    opacity:{{ $earned ? '1' : '0.35' }}">
                            {{ $badge->icon }}
                        </div>
                        <span class="text-[10px] text-center text-gray-500 leading-tight">{{ Str::limit($badge->name, 12) }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('student.badges.index') }}" class="block text-center text-xs font-semibold py-2 rounded-xl" style="background:rgba(37,194,110,0.08);color:#1a8a47">Voir tous les badges</a>
            </div>

            {{-- ── ACTIVITÉ RÉCENTE ────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-black/5 overflow-hidden anim anim-6">
                <div class="px-6 py-4 border-b border-black/5">
                    <h2 class="text-lg font-bold" style="font-family:'Playfair Display',serif">Activité récente</h2>
                </div>
                <div class="divide-y divide-black/5">
                    @forelse($recentActivity as $activity)
                    <div class="flex items-center gap-3 px-6 py-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0"
                             style="background:{{ $activity['color'] }}18">{{ $activity['icon'] }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-semibold text-gray-700">{{ $activity['action'] }}</div>
                            <div class="text-xs text-gray-400 truncate">{{ $activity['detail'] }}</div>
                        </div>
                        <span class="text-[10px] text-gray-300 shrink-0">
                            {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                        </span>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center">
                        <div class="text-3xl mb-2">🌱</div>
                        <p class="text-sm text-gray-400">Commencez à apprendre !</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── COURS RECOMMANDÉS ──────────────────────────── --}}
        @if($recommended->count() > 0)
        <div class="bg-white rounded-2xl border border-black/5 overflow-hidden anim anim-6">
            <div class="flex items-center justify-between px-6 py-4 border-b border-black/5">
                <h2 class="text-lg font-bold" style="font-family:'Playfair Display',serif">Cours recommandés pour vous</h2>
                <a href="{{ route('student.courses.index') }}" class="text-xs font-semibold hover:underline" style="color:#25c26e">Tout voir →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 divide-x divide-black/5">
                @foreach($recommended as $course)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                         style="background:rgba(37,194,110,0.08)">📚</div>
                    <div class="text-sm font-bold text-gray-800 mb-1">{{ $course->title }}</div>
                    <div class="text-xs text-gray-400 mb-3">Par {{ $course->teacher->full_name }}</div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold" style="color:{{ $course->is_free ? '#25c26e' : '#e8b84b' }}">
                            {{ $course->is_free ? 'Gratuit' : number_format($course->price, 0, ',', ' ').' XAF' }}
                        </span>
                        <a href="{{ route('student.courses.show', $course->slug) }}"
                           class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white"
                           style="background:linear-gradient(135deg,#1a8a47,#25c26e)">Voir →</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
</body>
</html>