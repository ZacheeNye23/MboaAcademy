<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { font-family:'Outfit',sans-serif; background:#070d09; color:#e0ebe2; }
        .font-playfair { font-family:'Playfair Display',serif; }
        .sidebar { width:270px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;display:flex;flex-direction:column; background:#040a05;border-right:1px solid rgba(232,184,75,0.12); }
        .main-content { margin-left:270px;min-height:100vh; }
        .glass-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:18px; }
        .gold-card   { background:rgba(232,184,75,0.05);border:1px solid rgba(232,184,75,0.15);border-radius:18px; }
        .nav-item { display:flex;align-items:center;gap:12px;padding:10px 20px;border-radius:12px;color:rgba(255,255,255,0.4);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all 0.2s;margin:2px 12px; }
        .nav-item:hover { background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.75); }
        .nav-item.active { background:rgba(232,184,75,0.1);color:#e8b84b; }
        .nav-item .icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
        .nav-item.active .icon { background:rgba(232,184,75,0.15); }
        .nav-item:not(.active) .icon { background:rgba(255,255,255,0.04); }
        .card-hover { transition:all 0.25s; }
        .card-hover:hover { transform:translateY(-2px); }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.5s ease both; }
        .anim-1{animation-delay:.05s}.anim-2{animation-delay:.1s}.anim-3{animation-delay:.15s}
        .anim-4{animation-delay:.2s}.anim-5{animation-delay:.25s}.anim-6{animation-delay:.3s}
        .prog-bar { height:5px;border-radius:3px;background:rgba(255,255,255,0.06);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px; }
        .pill { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:100px;font-size:0.7rem;font-weight:700; }
        .pill-green { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
        .pill-gold  { background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }
        .pill-red   { background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2); }
        .pill-gray  { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.35);border:1px solid rgba(255,255,255,0.08); }
        .dot-green { width:8px;height:8px;border-radius:50%;background:#25c26e;flex-shrink:0; }
        .dot-gold  { width:8px;height:8px;border-radius:50%;background:#e8b84b;flex-shrink:0; }
        .dot-red   { width:8px;height:8px;border-radius:50%;background:#f87171;flex-shrink:0; }
        .trend-up   { color:#25c26e;font-size:0.7rem;font-weight:700; }
        .trend-down { color:#f87171;font-size:0.7rem;font-weight:700; }
        ::-webkit-scrollbar { width:4px; }
        ::-webkit-scrollbar-track { background:#040a05; }
        ::-webkit-scrollbar-thumb { background:#1a8a47;border-radius:2px; }
    </style>
</head>
<body>

{{-- ═══════ SIDEBAR ADMIN ═══════ --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" class="font-playfair text-xl font-black text-white">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div class="mt-1 text-xs font-semibold uppercase tracking-widest" style="color:#e8b84b;">Administration</div>
    </div>

    <div class="px-6 py-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                 style="background:linear-gradient(135deg,#e8b84b,#f0d070);color:#0a1a0f">
                {{ auth()->user()->initials }}
            </div>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name }}</div>
                <div class="text-xs px-2 py-0.5 rounded-full w-fit" style="background:rgba(232,184,75,0.12);color:#e8b84b">Administrateur</div>
            </div>
        </div>
    </div>

    <nav class="flex-1 py-4 overflow-y-auto">
        <div class="px-6 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Vue générale</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item active"><span class="icon">📊</span> Tableau de bord</a>
        <a href="{{ route('admin.analytics') }}" class="nav-item"><span class="icon">📈</span> Analytiques</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Utilisateurs</div>
        <a href="{{ route('admin.users.index') }}" class="nav-item"><span class="icon">👥</span> Tous les utilisateurs</a>
        <a href="{{ route('admin.users.index', ['role' => 'student']) }}" class="nav-item"><span class="icon">🎓</span> Apprenants</a>
        <a href="{{ route('admin.users.index', ['role' => 'teacher']) }}" class="nav-item"><span class="icon">📖</span> Formateurs</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Contenu</div>
        <a href="{{ route('admin.courses.index') }}" class="nav-item">
            <span class="icon">📚</span> Cours
            @if($pendingCourses > 0)
            <span class="ml-auto pill pill-gold">{{ $pendingCourses }} en attente</span>
            @endif
        </a>
        <a href="{{ route('admin.quizzes.index') }}" class="nav-item"><span class="icon">📝</span> Quiz & Exercices</a>
        <a href="{{ route('admin.forum.overview') }}" class="nav-item"><span class="icon">💬</span> Forum</a>
        <a href="{{ route('admin.certificates.index') }}" class="nav-item"><span class="icon">🏆</span> Certificats</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Finances</div>
        <a href="{{ route('admin.revenues.index') }}" class="nav-item"><span class="icon">💰</span> Revenus globaux</a>
        <a href="{{ route('admin.payments.index') }}" class="nav-item"><span class="icon">💳</span> Paiements</a>
        <a href="{{ route('admin.payouts.index') }}" class="nav-item"><span class="icon">🔄</span> Reversements</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Système</div>
        <a href="{{ route('admin.settings.index') }}" class="nav-item"><span class="icon">⚙️</span> Paramètres</a>
        <a href="{{ route('admin.notifications.index') }}" class="nav-item"><span class="icon">🔔</span> Notifications</a>
        <a href="{{ route('admin.security.index') }}" class="nav-item"><span class="icon">🛡️</span> Sécurité & Logs</a>
    </nav>

    <div class="p-4 border-t border-white/5">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-left" style="background:rgba(239,68,68,0.07);color:rgba(239,68,68,0.7);">
                <span class="icon" style="background:rgba(239,68,68,0.08)">🚪</span> Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ═══════ MAIN CONTENT ═══════ --}}
<div class="main-content">

    {{-- Topbar --}}
    <header class="sticky top-0 z-30 flex items-center justify-between px-8 py-4 border-b"
            style="background:rgba(7,13,9,0.97);backdrop-filter:blur(12px);border-color:rgba(232,184,75,0.08)">
        <div>
            <h1 class="font-playfair text-xl font-bold text-white">Tableau de bord Admin</h1>
            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">
                {{ now()->translatedFormat('l d F Y') }} ·
                @if($pendingCourses > 0)
                    <span style="color:#e8b84b">{{ $pendingCourses }} cours en attente 🟡</span>
                @else
                    <span style="color:#25c26e">Tout va bien 🟢</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($pendingCourses > 0)
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl" style="background:rgba(232,184,75,0.08);border:1px solid rgba(232,184,75,0.15)">
                <span class="text-sm">⏳</span>
                <span class="text-sm font-bold" style="color:#e8b84b">{{ $pendingCourses }} en attente</span>
            </div>
            @endif
            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0"
                 style="background:linear-gradient(135deg,#e8b84b,#f0d070);color:#0a1a0f">
                {{ auth()->user()->initials }}
            </div>
        </div>
    </header>

    <div class="p-8">

        {{-- ── KPI STATS ──────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach([
                ['👥', 'Utilisateurs',    $totalUsers,        '+'.$newUsersThisMonth.' ce mois',  $userGrowthPct >= 0 ? 'trend-up' : 'trend-down', ($userGrowthPct >= 0 ? '+' : '').$userGrowthPct.'%'],
                ['📚', 'Cours publiés',   $publishedCourses,  $pendingCourses.' en attente',      'trend-up', ''],
                ['🎓', 'Inscriptions',    $totalEnrollments,  'total',                             'trend-up', ''],
                ['💰', 'Revenus (mois)',  number_format($revenueThisMonth, 0, ',', ' ').' XAF', $totalTransactionsThisMonth.' transactions', $revenueGrowthPct >= 0 ? 'trend-up' : 'trend-down', ($revenueGrowthPct >= 0 ? '+' : '').$revenueGrowthPct.'%'],
            ] as [$icon, $label, $val, $sub, $trendClass, $trend])
            <div class="card-hover glass-card p-5 anim anim-{{ $loop->iteration }}">
                <div class="flex items-start justify-between mb-3">
                    <span class="text-2xl">{{ $icon }}</span>
                    @if($trend)
                    <span class="{{ $trendClass }}">{{ $trend }}</span>
                    @endif
                </div>
                <div class="font-playfair text-2xl font-bold text-white mb-1">{{ $val }}</div>
                <div class="text-xs" style="color:rgba(255,255,255,0.35)">{{ $label }} · {{ $sub }}</div>
            </div>
            @endforeach
        </div>

        {{-- ── SPARKLINE + COURS EN ATTENTE ───────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            {{-- Sparkline inscriptions 7 jours --}}
            <div class="lg:col-span-2 glass-card p-6 anim anim-3">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="font-playfair text-lg font-bold text-white">Inscriptions (7 derniers jours)</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">Nouvelles inscriptions par jour</p>
                    </div>
                    <span class="pill pill-green">{{ $sparkline->sum() }} total</span>
                </div>
                @php
                    $maxVal = $sparkline->max() ?: 1;
                    $days   = $sparkline->keys()->map(fn($d) => \Carbon\Carbon::parse($d)->translatedFormat('D'));
                @endphp
                <div class="flex items-end gap-2 h-24">
                    @foreach($sparkline as $date => $count)
                    @php $height = max(8, round(($count / $maxVal) * 100)); @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-[10px] font-bold" style="color:{{ $count === $sparkline->max() ? '#25c26e' : 'rgba(255,255,255,0.3)' }}">
                            {{ $count > 0 ? $count : '' }}
                        </span>
                        <div class="w-full rounded-t-md transition-all"
                             style="height:{{ $height }}%;background:{{ $count === $sparkline->max() ? 'linear-gradient(to top,#1a8a47,#25c26e)' : 'rgba(37,194,110,0.2)' }}">
                        </div>
                        <span class="text-[10px]" style="color:rgba(255,255,255,0.25)">
                            {{ \Carbon\Carbon::parse($date)->translatedFormat('D') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Cours en attente --}}
            <div class="glass-card overflow-hidden anim anim-4">
                <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
                    <h2 class="font-playfair text-base font-bold text-white">À valider</h2>
                    @if($pendingCourses > 0)
                    <span class="pill pill-gold">{{ $pendingCourses }}</span>
                    @endif
                </div>
                @forelse($coursesAwaitingValidation as $course)
                <div class="flex items-center gap-3 px-5 py-3.5 border-b border-white/4 hover:bg-white/2 transition-colors last:border-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0"
                         style="background:rgba(232,184,75,0.1)">📚</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ Str::limit($course->title, 28) }}</div>
                        <div class="text-[11px]" style="color:rgba(255,255,255,0.3)">{{ $course->teacher->full_name }}</div>
                    </div>
                    <a href="#" class="text-[11px] font-bold px-2.5 py-1 rounded-lg shrink-0"
                       style="background:rgba(232,184,75,0.12);color:#e8b84b">Voir →</a>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <div class="text-3xl mb-2">✅</div>
                    <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucun cours en attente</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── RÉPARTITION UTILISATEURS + TOP FORMATEURS ──────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            {{-- Donut répartition --}}
            <div class="glass-card p-6 anim anim-4">
                <h2 class="font-playfair text-lg font-bold text-white mb-5">Répartition des utilisateurs</h2>
                <div class="flex items-center gap-8">
                    {{-- Donut SVG dynamique --}}
                    <div class="relative shrink-0">
                        <svg width="130" height="130" viewBox="0 0 130 130">
                            <circle cx="65" cy="65" r="52" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="18"/>
                            {{-- Apprenants --}}
                            <circle cx="65" cy="65" r="52" fill="none"
                                    stroke="{{ $donut['students']['color'] }}" stroke-width="18"
                                    stroke-dasharray="{{ $donut['students']['dash'] }} {{ 2*M_PI*52 }}"
                                    stroke-dashoffset="{{ $donut['students']['offset'] }}"
                                    stroke-linecap="butt"/>
                            {{-- Formateurs --}}
                            <circle cx="65" cy="65" r="52" fill="none"
                                    stroke="{{ $donut['teachers']['color'] }}" stroke-width="18"
                                    stroke-dasharray="{{ $donut['teachers']['dash'] }} {{ 2*M_PI*52 }}"
                                    stroke-dashoffset="{{ $donut['teachers']['offset'] }}"
                                    stroke-linecap="butt"/>
                            {{-- Admins --}}
                            <circle cx="65" cy="65" r="52" fill="none"
                                    stroke="{{ $donut['admins']['color'] }}" stroke-width="18"
                                    stroke-dasharray="{{ $donut['admins']['dash'] }} {{ 2*M_PI*52 }}"
                                    stroke-dashoffset="{{ $donut['admins']['offset'] }}"
                                    stroke-linecap="butt"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="font-playfair text-xl font-bold text-white">{{ number_format($totalUsers) }}</span>
                            <span class="text-[10px]" style="color:rgba(255,255,255,0.4)">total</span>
                        </div>
                    </div>

                    {{-- Légende --}}
                    <div class="space-y-4 flex-1">
                        @foreach([
                            ['Apprenants', $totalStudents,  $donut['students']['pct'], $donut['students']['color']],
                            ['Formateurs', $totalTeachers,  $donut['teachers']['pct'], $donut['teachers']['color']],
                            ['Admins',     $totalAdmins,    $donut['admins']['pct'],   $donut['admins']['color']],
                        ] as [$label, $count, $pct, $color])
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-sm" style="background:{{ $color }}"></div>
                                    <span class="text-sm" style="color:rgba(255,255,255,0.6)">{{ $label }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-white">{{ number_format($count) }}</span>
                                    <span class="text-xs" style="color:{{ $color }}">{{ $pct }}%</span>
                                </div>
                            </div>
                            <div class="prog-bar">
                                <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
                            </div>
                        </div>
                        @endforeach

                        <div class="pt-2 mt-1 border-t border-white/5">
                            <div class="text-xs font-semibold" style="color:#25c26e">
                                ↑ +{{ $newUsersThisMonth }} ce mois
                            </div>
                            <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">
                                {{ $userGrowthPct >= 0 ? '+' : '' }}{{ $userGrowthPct }}% vs mois précédent
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top formateurs --}}
            <div class="glass-card overflow-hidden anim anim-5">
                <div class="px-6 py-4 border-b border-white/5">
                    <h2 class="font-playfair text-lg font-bold text-white">Top formateurs</h2>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">Par nombre d'inscriptions</p>
                </div>
                @php
                    $avatarColors = ['#1a8a47','#e8b84b','#3b82f6','#a78bfa','#f87171'];
                    $maxEnrollments = $topTeachers->max('total_enrollments') ?: 1;
                @endphp
                @forelse($topTeachers as $teacher)
                <div class="flex items-center gap-4 px-6 py-3.5 border-b border-white/4 last:border-0 hover:bg-white/2 transition-colors">
                    <span class="text-sm font-bold w-5 text-center shrink-0"
                          style="color:{{ $avatarColors[$loop->index] ?? '#fff' }}">
                        {{ $loop->iteration }}
                    </span>
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                         style="background:{{ $avatarColors[$loop->index % 5] }}">
                        {{ $teacher->initials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-white truncate">{{ $teacher->full_name }}</div>
                        <div class="prog-bar mt-1.5">
                            <div class="prog-fill"
                                 style="width:{{ round(($teacher->total_enrollments / $maxEnrollments) * 100) }}%;background:{{ $avatarColors[$loop->index % 5] }}"></div>
                        </div>
                    </div>
                    <span class="text-sm font-bold shrink-0" style="color:{{ $avatarColors[$loop->index % 5] }}">
                        {{ $teacher->total_enrollments }}
                    </span>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucun formateur encore.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── ACTIVITÉ RÉCENTE + SIDEBAR DROITE ──────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Activité récente --}}
            <div class="lg:col-span-2 glass-card overflow-hidden anim anim-5">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                    <h2 class="font-playfair text-lg font-bold text-white">Activité récente</h2>
                    <a href="#" class="text-xs font-semibold" style="color:#e8b84b">Voir les logs →</a>
                </div>
                <div class="divide-y divide-white/4">
                    @forelse($recentActivity as $event)
                    <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-white/2 transition-colors">
                        <div class="{{ $event['dot'] }} mt-0.5 shrink-0"></div>
                        <span class="text-lg shrink-0">{{ $event['icon'] }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-white">{{ $event['action'] }}</div>
                            <div class="text-xs truncate" style="color:rgba(255,255,255,0.35)">{{ $event['detail'] }}</div>
                        </div>
                        <span class="text-[10px] shrink-0 whitespace-nowrap" style="color:rgba(255,255,255,0.2)">
                            {{ $event['time_human'] }}
                        </span>
                    </div>
                    @empty
                    <div class="px-6 py-10 text-center">
                        <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucune activité récente.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Sidebar droite --}}
            <div class="space-y-4 anim anim-6">

                {{-- Revenus du mois --}}
                <div class="gold-card p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-playfair text-base font-bold text-white">Revenus du mois</h3>
                        <span class="{{ $revenueGrowthPct >= 0 ? 'trend-up' : 'trend-down' }}">
                            {{ $revenueGrowthPct >= 0 ? '↑' : '↓' }} {{ abs($revenueGrowthPct) }}%
                        </span>
                    </div>
                    <div class="font-playfair text-3xl font-bold mb-1" style="color:#e8b84b">
                        {{ number_format($revenueThisMonth / 1000, 0, ',', ' ') }}K
                    </div>
                    <div class="text-xs mb-3" style="color:rgba(255,255,255,0.35)">
                        XAF · {{ $totalTransactionsThisMonth }} transaction(s)
                    </div>
                    <div class="prog-bar">
                        <div class="prog-fill"
                             style="width:{{ $revenueGoalPct }}%;background:linear-gradient(90deg,#e8b84b,#f0d070)">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs mt-1" style="color:rgba(255,255,255,0.3)">
                        <span>Objectif : {{ number_format($revenueGoal / 1000) }}K XAF</span>
                        <span>{{ $revenueGoalPct }}%</span>
                    </div>
                </div>

                {{-- Actions rapides --}}
                <div class="glass-card p-5">
                    <h3 class="font-playfair text-base font-bold text-white mb-4">Actions rapides</h3>
                    <div class="space-y-2">
                        @foreach([
                            ['👤','consulter les utilisateurs','#25c26e','users'],
                            ['📚','Valider des cours','#e8b84b','courses'],
                            ['💳','Traiter les paiements','#3b82f6','#'],
                            ['📧','Envoyer une notification','#a78bfa','#'],
                            ['🛡️','Voir les logs sécurité','#f87171','#'],
                        ] as [$icon,$label,$color,$href])
                        <a href="{{ $href }}"
                           class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-left transition-all hover:-translate-x-0.5 hover:bg-white/3"
                           style="color:rgba(255,255,255,0.65)">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                  style="background:{{ $color }}15">{{ $icon }}</span>
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Santé système --}}
                <div class="glass-card p-5">
                    <h3 class="font-playfair text-base font-bold text-white mb-4">Santé système</h3>
                    <div class="space-y-3">
                        @foreach([
                            ['Serveur',        'Opérationnel', '#25c26e'],
                            ['Base de données', 'Opérationnel', '#25c26e'],
                            ['Stockage',        'Surveiller',   '#e8b84b'],
                            ['Emails',          'Opérationnel', '#25c26e'],
                        ] as [$service, $status, $color])
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full shrink-0" style="background:{{ $color }}"></div>
                            <span class="text-xs flex-1" style="color:rgba(255,255,255,0.5)">{{ $service }}</span>
                            <span class="text-xs font-semibold" style="color:{{ $color }}">{{ $status }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>