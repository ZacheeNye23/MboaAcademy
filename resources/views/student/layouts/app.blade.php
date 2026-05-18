<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MboaAcademy') — Espace Apprenant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --green-deep:   #0d5c2e;
            --green-mid:    #1a8a47;
            --green-bright: #25c26e;
            --green-light:  #d4f5e2;
            --gold:         #e8b84b;
            --cream:        #f4f7f4;
            --dark:         #0a1a0f;
            --sidebar-w:    260px;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: var(--cream); color: #1c2b1f; margin: 0; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--dark);
            position: fixed; left: 0; top: 0; bottom: 0;
            z-index: 40;
            display: flex; flex-direction: column;
            border-right: 1px solid rgba(37,194,110,0.08);
            transition: transform 0.3s ease;
        }
        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; }

        /* ── Nav items ── */
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 9px 16px; border-radius: 12px;
            color: rgba(255,255,255,0.48); font-size: 0.875rem; font-weight: 500;
            text-decoration: none; transition: all 0.2s; margin: 2px 10px;
        }
        .nav-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.85); }
        .nav-item.active { background: rgba(37,194,110,0.14); color: #25c26e; font-weight: 600; }
        .nav-item .ni { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; }
        .nav-item.active .ni  { background: rgba(37,194,110,0.18); }
        .nav-item:not(.active) .ni { background: rgba(255,255,255,0.05); }
        .nav-badge { margin-left: auto; background: #ef4444; color: #fff; font-size: 9px; font-weight: 700; border-radius: 100px; padding: 1px 6px; }

        /* ── Topbar ── */
        .topbar {
            position: sticky; top: 0; z-index: 30;
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 32px;
            background: rgba(244,247,244,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }

        /* ── Contenu ── */
        .page-body { padding: 28px 32px; }

        /* ── Utilitaires ── */
        .prog-bar { height: 5px; border-radius: 3px; background: rgba(0,0,0,0.08); overflow: hidden; }
        .prog-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg,#1a8a47,#25c26e); }
        .badge-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 100px;
            font-size: 0.68rem; font-weight: 700; letter-spacing: .3px;
        }
        .badge-green { background: rgba(37,194,110,0.1); color: #1a8a47; border: 1px solid rgba(37,194,110,0.22); }
        .badge-gold  { background: rgba(232,184,75,0.12); color: #b8860b; border: 1px solid rgba(232,184,75,0.25); }
        .badge-blue  { background: rgba(59,130,246,0.1); color: #2563eb; border: 1px solid rgba(59,130,246,0.2); }
        .badge-gray  { background: rgba(0,0,0,0.06); color: #6b7280; border: 1px solid rgba(0,0,0,0.1); }

        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim  { animation: fadeUp .45s ease both; }
        .d1 { animation-delay:.05s } .d2 { animation-delay:.1s } .d3 { animation-delay:.15s }
        .d4 { animation-delay:.2s  } .d5 { animation-delay:.25s } .d6 { animation-delay:.3s  }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
        }

        @stack('styles')
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
        <button @click="sidebarOpen=false" class="lg:hidden text-white/40 hover:text-white text-lg">✕</button>
    </div>

    {{-- Profil compact --}}
    <div class="px-5 py-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs text-white shrink-0"
                 style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                {{ auth()->user()->initials }}
            </div>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name }}</div>

            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 py-3 overflow-y-auto">

        <div class="px-5 mb-1.5 mt-1 text-[9px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.18)">Principal</div>

        <a href="{{ route('student.dashboard') }}"
           class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <span class="ni">🏠</span> Tableau de bord
        </a>

        <a href="{{ route('student.courses.mine') }}"
           class="nav-item {{ request()->routeIs('student.courses.mine') ? 'active' : '' }}">
            <span class="ni">📚</span> Mes cours
        </a>

        <a href="{{ route('student.courses.index') }}"
           class="nav-item {{ request()->routeIs('student.courses.index') ? 'active' : '' }}">
            <span class="ni">🔍</span> Explorer
        </a>

        <a href="{{ route('student.quizzes.index') }}"
           class="nav-item {{ request()->routeIs('student.quizzes.*') ? 'active' : '' }}">
            <span class="ni">📝</span> Mes quiz
            @php $pendingCount = \App\Models\Quiz::whereHas('course.enrollments', fn($q) => $q->where('user_id', auth()->id()))->get()->filter(fn($q) => $q->canAttempt(auth()->id()))->count(); @endphp
            @if($pendingCount > 0)<span class="nav-badge">{{ $pendingCount }}</span>@endif
        </a>

        <div class="px-5 mb-1.5 mt-4 text-[9px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.18)">Communauté</div>

        <a href="{{ route('student.forum.overview') }}" class="nav-item {{ request()->routeIs('student.forum.*') ? 'active' : '' }}">
            <span class="ni">💬</span> Forum
        </a>

        <a href="{{ route('student.badges.index') }}"
           class="nav-item {{ request()->routeIs('student.badges.*') ? 'active' : '' }}">
            <span class="ni">🏆</span> Badges
        </a>

        <a href="{{ route('student.certificates.index') }}"
           class="nav-item {{ request()->routeIs('student.certificates.*') ? 'active' : '' }}">
            <span class="ni">🎓</span> Certificats
        </a>

        <div class="px-5 mb-1.5 mt-4 text-[9px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.18)">Compte</div>

        <a href="{{ route ('profile.edit') }}" class="nav-item">
            <span class="ni">👤</span> Mon profil
        </a>
        <a href="{{route ('settings.index')}}" class="nav-item">
            <span class="ni">⚙️</span> Paramètres
        </a>
    </nav>

    {{-- Déconnexion --}}
    <div class="p-3 border-t border-white/5">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-left" style="background:rgba(239,68,68,0.07);color:rgba(239,68,68,0.75);">
                <span class="ni" style="background:rgba(239,68,68,0.1)">🚪</span> Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ── MAIN ── --}}
<div class="main-wrap">

    {{-- Topbar --}}
    <header class="topbar">
        <div class="flex items-center gap-3">
            {{-- Burger mobile --}}
            <button @click="sidebarOpen=true" class="lg:hidden w-8 h-8 rounded-lg bg-black/5 flex items-center justify-center text-sm">☰</button>
            <div>
                <h1 class="font-semibold text-gray-800 text-base leading-tight" style="font-family:'Playfair Display',serif">
                    @yield('page-title', 'Mon Espace')
                </h1>
                @hasSection('page-subtitle')
                <p class="text-xs text-gray-400 mt-0.5">@yield('page-subtitle')</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            @yield('topbar-actions')
            {{-- Notif --}}
            <div class="relative">
                <button class="w-9 h-9 rounded-xl bg-white border border-black/6 flex items-center justify-center text-sm shadow-sm hover:bg-gray-50 transition-colors">
                    🔔
                </button>
                @php $notifCount = auth()->user()->unreadNotifications->count(); @endphp
                @if($notifCount > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-[9px] font-bold text-white flex items-center justify-center">{{ $notifCount }}</span>
                @endif
            </div>
            {{-- Avatar --}}
            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs text-white"
                 style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                {{ auth()->user()->initials }}
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mx-8 mt-5 flex items-center gap-3 px-5 py-3.5 rounded-2xl"
         style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2)">
        <span>🎉</span>
        <p class="text-sm font-medium" style="color:#1a8a47">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="mx-8 mt-5 flex items-center gap-3 px-5 py-3.5 rounded-2xl"
         style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.18)">
        <span>⚠️</span>
        <p class="text-sm font-medium text-red-600">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Contenu de la page --}}
    <div class="page-body">
        @yield('content')
    </div>
</div>

{{-- Overlay mobile --}}
<div x-show="sidebarOpen" @click="sidebarOpen=false"
     class="fixed inset-0 bg-black/50 z-30 lg:hidden" style="display:none"></div>

@stack('scripts')
</body>
</html>