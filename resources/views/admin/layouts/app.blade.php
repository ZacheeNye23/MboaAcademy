<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — MboaAcademy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        :root {
            --dark:       #070d09;
            --sidebar-bg: #040a05;
            --gold:       #e8b84b;
            --green:      #25c26e;
            --sidebar-w:  270px;
        }
        * { box-sizing: border-box; }
        body { font-family:'Outfit',sans-serif; background:var(--dark); color:#e0ebe2; margin:0; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w); min-height: 100vh;
            position: fixed; left:0; top:0; bottom:0; z-index:40;
            display: flex; flex-direction: column;
            background: var(--sidebar-bg);
            border-right: 1px solid rgba(232,184,75,0.1);
            transition: transform .3s ease;
        }
        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; }

        /* ── Nav items ── */
        .nav-item {
            display:flex; align-items:center; gap:12px;
            padding:9px 16px; border-radius:12px;
            color:rgba(255,255,255,0.4); font-size:.875rem; font-weight:500;
            text-decoration:none; transition:all .2s; margin:2px 10px;
        }
        .nav-item:hover { background:rgba(255,255,255,0.04); color:rgba(255,255,255,0.75); }
        .nav-item.active { background:rgba(232,184,75,0.1); color:#e8b84b; font-weight:600; }
        .nav-item .ni { width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0; }
        .nav-item.active .ni  { background:rgba(232,184,75,0.15); }
        .nav-item:not(.active) .ni { background:rgba(255,255,255,0.04); }
        .nav-badge { margin-left:auto;font-size:9px;font-weight:700;border-radius:100px;padding:1px 6px; }
        .nav-badge-gold { background:rgba(232,184,75,0.15);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }
        .nav-badge-red  { background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.2); }

        /* ── Topbar ── */
        .topbar {
            position:sticky; top:0; z-index:30;
            display:flex; align-items:center; justify-content:space-between;
            padding:14px 32px;
            background:rgba(7,13,9,0.97);
            backdrop-filter:blur(12px);
            border-bottom:1px solid rgba(232,184,75,0.07);
        }

        /* ── Contenu ── */
        .page-body { padding:28px 32px; }

        /* ── Utilitaires ── */
        .glass  { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:18px; }
        .glass-gold { background:rgba(232,184,75,0.04);border:1px solid rgba(232,184,75,0.12);border-radius:18px; }
        .pill { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:100px;font-size:.7rem;font-weight:700; }
        .pill-green { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
        .pill-gold  { background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }
        .pill-red   { background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2); }
        .pill-gray  { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.35);border:1px solid rgba(255,255,255,0.08); }
        .prog-bar { height:5px;border-radius:3px;background:rgba(255,255,255,0.06);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp .45s ease both; }
        .d1{animation-delay:.05s}.d2{animation-delay:.10s}.d3{animation-delay:.15s}
        .d4{animation-delay:.20s}.d5{animation-delay:.25s}.d6{animation-delay:.30s}

        ::-webkit-scrollbar { width:4px; }
        ::-webkit-scrollbar-track { background:#040a05; }
        ::-webkit-scrollbar-thumb { background:#1a8a47;border-radius:2px; }

        @media (max-width:768px) {
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .main-wrap { margin-left:0; }
        }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">

{{-- ── SIDEBAR ── --}}
<aside class="sidebar" :class="{ 'open': sidebarOpen }">

    {{-- Logo --}}
    <div class="px-5 py-5 border-b border-white/5 flex items-center justify-between">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <button @click="sidebarOpen=false" class="lg:hidden text-white/40 hover:text-white">✕</button>
    </div>

    {{-- Profil Admin --}}
    <div class="px-5 py-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                 style="background:linear-gradient(135deg,#e8b84b,#f0d070);color:#0a1a0f">
                {{ auth()->user()->initials }}
            </div>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name }}</div>
                <div class="text-[10px] px-2 py-0.5 rounded-full w-fit" style="background:rgba(232,184,75,0.12);color:#e8b84b">
                    Administrateur
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 py-3 overflow-y-auto">
        <div class="px-5 mb-1.5 mt-1 text-[9px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Vue générale</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="ni">📊</span> Tableau de bord
        </a>
        <a href="{{ route('admin.analytics') }}"
           class="nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
            <span class="ni">📈</span> Analytiques
        </a>

        <div class="px-5 mb-1.5 mt-4 text-[9px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Utilisateurs</div>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <span class="ni">👥</span> Tous les utilisateurs
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'student']) }}"
           class="nav-item {{ request()->routeIs('admin.users.*') && request('role') === 'student' ? 'active' : '' }}">
            <span class="ni">🎓</span> Apprenants
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'teacher']) }}"
           class="nav-item {{ request()->routeIs('admin.users.*') && request('role') === 'teacher' ? 'active' : '' }}">
            <span class="ni">📖</span> Formateurs
        </a>

        <div class="px-5 mb-1.5 mt-4 text-[9px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Contenu</div>

        <a href="{{ route('admin.courses.index') }}"
           class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
            <span class="ni">📚</span> Cours
            @php $pendingCourses = \App\Models\Course::where('status','pending')->count(); @endphp
            @if($pendingCourses > 0)
            <span class="nav-badge nav-badge-gold">{{ $pendingCourses }}</span>
            @endif
        </a>
        <a href="{{ route('admin.quizzes.index') }}"
           class="nav-item {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
            <span class="ni">📝</span> Quiz
        </a>
        <a href="{{ route('admin.forum.overview') }}"
           class="nav-item {{ request()->routeIs('admin.forum.*') ? 'active' : '' }}">
            <span class="ni">💬</span> Forum
        </a>
        <a href="{{ route('admin.certificates.index') }}"
           class="nav-item {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
            <span class="ni">🏆</span> Certificats
        </a>

        <div class="px-5 mb-1.5 mt-4 text-[9px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Finances</div>

        <a href="{{ route ('admin.revenues.index')}}"
           class="nav-item {{ request()->routeIs('admin.revenues.*') ? 'active' : '' }}">
            <span class="ni">💰</span> Revenus globaux
        </a>
        <a href="{{ route ('admin.payments.index') }}"
           class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <span class="ni">💳</span> Paiements
        </a>

        <div class="px-5 mb-1.5 mt-4 text-[9px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Système</div>

        <a href="{{ route('admin.settings.index') }}"
           class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <span class="ni">⚙️</span> Paramètres
        </a>
        <a href="{{ route('admin.notifications.index') }}"
           class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
            <span class="ni">🔔</span> Notifications
        </a>
        <a href="{{ route('admin.security.index') }}"
           class="nav-item {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
            <span class="ni">🛡️</span> Sécurité & Logs
        </a>
    </nav>

    {{-- Déconnexion --}}
    <div class="p-3 border-t border-white/5">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-left" style="background:rgba(239,68,68,0.07);color:rgba(239,68,68,0.7);">
                <span class="ni" style="background:rgba(239,68,68,0.08)">🚪</span> Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ── MAIN ── --}}
<div class="main-wrap">

    {{-- Topbar --}}
    <header class="topbar">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen=true" class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.05)">☰</button>
            <div>
                <h1 class="font-bold text-white text-base" style="font-family:'Playfair Display',serif">
                    @yield('page-title', 'Administration')
                </h1>
                @hasSection('page-subtitle')
                <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">@yield('page-subtitle')</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            @yield('topbar-actions')
            {{-- Notifications --}}
            <div class="relative">
                <button class="w-9 h-9 rounded-xl flex items-center justify-center text-sm transition-colors" style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
                    🔔
                </button>
                @php $notifCount = auth()->user()->unreadNotifications->count(); @endphp
                @if($notifCount > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-[9px] font-bold text-white flex items-center justify-center">{{ $notifCount }}</span>
                @endif
            </div>
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm"
                 style="background:linear-gradient(135deg,#e8b84b,#f0d070);color:#0a1a0f">
                {{ auth()->user()->initials }}
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mx-8 mt-5 flex items-center gap-3 px-5 py-3.5 rounded-2xl" style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2)">
        <span>🎉</span>
        <p class="text-sm font-medium" style="color:#25c26e">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="mx-8 mt-5 flex items-center gap-3 px-5 py-3.5 rounded-2xl" style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.18)">
        <span>⚠️</span>
        <p class="text-sm font-medium text-red-400">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Page content --}}
    <div class="page-body">
        @yield('content')
    </div>
</div>

{{-- Overlay mobile --}}
<div x-show="sidebarOpen" @click="sidebarOpen=false"
     class="fixed inset-0 bg-black/60 z-30 lg:hidden" style="display:none"></div>

@stack('scripts')
</body>
</html>