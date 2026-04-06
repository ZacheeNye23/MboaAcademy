{{-- resources/views/components/navbar.blade.php --}}
<nav
    x-data="{ scrolled: false, open: false }"
    x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 50)"
    :class="scrolled ? 'bg-dark/95 shadow-lg shadow-black/20' : 'bg-dark/90'"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 backdrop-blur-md border-b border-green-bright/10"
>
    <div class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between h-[72px]">

        {{-- Logo --}}
        <a href="{{ route('welcome') }}" class="font-playfair text-2xl font-black text-white tracking-tight">
            Mboa<span class="text-gold">Academy</span>
        </a>

        {{-- Navigation Desktop --}}
        <ul class="hidden lg:flex items-center gap-8 list-none">
            <li><a href="#features" class="text-white/70 hover:text-green-bright text-sm font-medium transition-colors duration-200">Fonctionnalités</a></li>
            <li><a href="#courses" class="text-white/70 hover:text-green-bright text-sm font-medium transition-colors duration-200">Cours</a></li>
            <li><a href="#temoignages" class="text-white/70 hover:text-green-bright text-sm font-medium transition-colors duration-200">Témoignages</a></li>
            <li><a href="#" class="text-white/70 hover:text-green-bright text-sm font-medium transition-colors duration-200">À propos</a></li>
        </ul>

        {{-- CTA Desktop --}}
        <div class="hidden lg:flex items-center gap-3">
            @auth
                {{-- ✅ Route dynamique selon le rôle --}}
                <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                   class="px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-green-mid to-green-bright rounded-full shadow-lg shadow-green-bright/30 hover:-translate-y-0.5 hover:shadow-green-bright/50 transition-all duration-200">
                    Mon espace →
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-5 py-2 text-sm font-medium text-white border border-white/25 rounded-full hover:border-green-bright hover:text-green-bright transition-all duration-200">
                    Connexion
                </a>
                <a href="{{ route('register') }}"
                   class="px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-green-mid to-green-bright rounded-full shadow-lg shadow-green-bright/30 hover:-translate-y-0.5 hover:shadow-green-bright/50 transition-all duration-200">
                    Commencer →
                </a>
            @endauth
        </div>

        {{-- Burger Mobile --}}
        <button @click="open = !open" class="lg:hidden text-white/70 hover:text-white transition-colors">
            <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Menu Mobile --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden bg-dark border-t border-green-bright/10 px-6 py-4 space-y-3">
        <a href="#features" class="block text-white/70 hover:text-green-bright text-sm font-medium py-2">Fonctionnalités</a>
        <a href="#courses" class="block text-white/70 hover:text-green-bright text-sm font-medium py-2">Cours</a>
        <a href="#temoignages" class="block text-white/70 hover:text-green-bright text-sm font-medium py-2">Témoignages</a>
        <div class="pt-2 flex flex-col gap-2">
            @auth
                {{-- ✅ Route dynamique selon le rôle --}}
                <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                   class="text-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-green-mid to-green-bright rounded-full">
                   Mon espace →
                </a>
            @else
                <a href="{{ route('login') }}" class="text-center px-5 py-2.5 text-sm font-medium text-white border border-white/25 rounded-full">Connexion</a>
                <a href="{{ route('register') }}" class="text-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-green-mid to-green-bright rounded-full">Commencer →</a>
            @endauth
        </div>
    </div>
</nav>