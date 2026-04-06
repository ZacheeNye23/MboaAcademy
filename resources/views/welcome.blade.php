{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app')

@section('title', 'MboaAcademy — Apprends. Grandis. Rayonne.')

@section('content')

{{-- ═══════════════════════════════════════════
    HERO
═══════════════════════════════════════════ --}}
<section class="relative min-h-screen bg-dark flex items-center overflow-hidden">

    {{-- Fond radial --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute inset-0"
             style="background: radial-gradient(ellipse 80% 60% at 70% 50%, rgba(26,138,71,0.18) 0%, transparent 60%),
                               radial-gradient(ellipse 50% 40% at 20% 80%, rgba(232,184,75,0.10) 0%, transparent 50%),
                               radial-gradient(ellipse 40% 30% at 80% 10%, rgba(37,194,110,0.12) 0%, transparent 50%);">
        </div>
        {{-- Grille subtile --}}
        <div class="absolute inset-0 opacity-30"
             style="background-image: repeating-linear-gradient(90deg, rgba(37,194,110,0.05) 0px, rgba(37,194,110,0.05) 1px, transparent 1px, transparent 60px),
                                      repeating-linear-gradient(0deg, rgba(37,194,110,0.05) 0px, rgba(37,194,110,0.05) 1px, transparent 1px, transparent 60px);">
        </div>
    </div>

    {{-- Contenu Hero --}}
    <div class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-10 pt-[72px] flex items-center min-h-screen">
        <div class="w-full lg:w-1/2 py-20 animate-fadeUp">

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 bg-green-bright/10 border border-green-bright/30 rounded-full px-4 py-1.5 mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-green-bright animate-pulse"></span>
                <span class="text-green-bright text-xs font-bold tracking-widest uppercase">🌍 Plateforme e-learning africaine</span>
            </div>

            {{-- Titre --}}
            <h1 class="font-playfair text-5xl lg:text-6xl xl:text-7xl font-black text-white leading-[1.08] tracking-tight mb-6">
                Apprends.<br>
                Grandis.<br>
                <em class="not-italic text-gold relative">
                    Rayonne.
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-gold to-transparent rounded"></span>
                </em>
            </h1>

            {{-- Sous-titre --}}
            <p class="text-white/60 text-lg leading-relaxed max-w-lg mb-10 font-light">
                MboaAcademy connecte les apprenants africains aux meilleures formations — vidéos, exercices, quiz et communauté, tout en un seul endroit.
            </p>

            {{-- CTA --}}
            <div class="flex flex-wrap gap-4 mb-14">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-mid to-green-bright text-white font-semibold rounded-full shadow-xl shadow-green-bright/30 hover:-translate-y-0.5 hover:shadow-green-bright/50 transition-all duration-200">
                    🚀 Commencer gratuitement
                </a>
                <a href="#courses"
                   class="inline-flex items-center gap-2 px-8 py-4 border border-white/20 text-white/80 font-medium rounded-full hover:border-gold hover:text-gold transition-all duration-200">
                    ▶ Voir une démo
                </a>
            </div>

            {{-- Stats --}}
            <div class="flex gap-10">
                @foreach([
                    ['2 400+', 'Apprenants'],
                    ['180+',   'Cours'],
                    ['98%',    'Satisfaction'],
                ] as [$num, $label])
                <div>
                    <div class="font-playfair text-3xl font-bold text-green-bright">{{ $num }}</div>
                    <div class="text-xs uppercase tracking-widest text-white/40 mt-1">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Dashboard Preview (caché sur mobile) --}}
        <div class="hidden lg:flex w-1/2 justify-center items-center pl-10 py-20 animate-fadeIn">
            <div class="w-full max-w-md bg-white/[0.04] border border-green-bright/20 rounded-2xl overflow-hidden shadow-[0_40px_80px_rgba(0,0,0,0.5)]">

                {{-- Barre titre --}}
                <div class="bg-green-deep/50 px-5 py-3 flex items-center gap-2 border-b border-green-bright/15">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                    <span class="ml-3 text-xs text-white/40 font-medium">Mes cours en cours</span>
                </div>

                {{-- Cours --}}
                <div class="p-5 space-y-3">
                    @foreach([
                        ['💻', 'rgba(37,194,110,0.15)', 'Développement Web Full Stack', 72],
                        ['📊', 'rgba(232,184,75,0.15)',  'Data Science & Machine Learning', 45],
                        ['🎨', 'rgba(122,59,30,0.20)',   'Design UI/UX Moderne', 90],
                    ] as [$icon, $bg, $title, $pct])
                    <div class="flex items-center gap-4 p-3 bg-white/[0.04] border border-white/[0.07] rounded-xl hover:bg-green-bright/[0.07] hover:border-green-bright/25 transition-all duration-300 group">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0"
                             style="background: {{ $bg }}">{{ $icon }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-semibold text-white/90 mb-1.5 truncate">{{ $title }}</div>
                            <div class="h-1 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-green-mid to-green-bright rounded-full transition-all duration-1000"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="text-[10px] text-green-bright mt-1">{{ $pct }}% complété</div>
                        </div>
                    </div>
                    @endforeach

                    {{-- Badge progress --}}
                    <div class="p-3 bg-green-bright/[0.07] border border-green-bright/20 rounded-xl">
                        <div class="text-[10px] text-green-bright font-bold mb-1">🏆 Prochain badge</div>
                        <div class="text-xs text-white/50">Plus que <strong class="text-white/80">2 leçons</strong> pour obtenir le badge <strong class="text-white/80">Expert Web</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
    FEATURES
═══════════════════════════════════════════ --}}
<section id="features" class="py-24 px-6 lg:px-10 bg-cream relative"
         style="background-image: repeating-linear-gradient(45deg, rgba(232,184,75,0.04) 0px, rgba(232,184,75,0.04) 1px, transparent 1px, transparent 40px),
                                  repeating-linear-gradient(-45deg, rgba(37,194,110,0.04) 0px, rgba(37,194,110,0.04) 1px, transparent 1px, transparent 40px);">
    <div class="max-w-7xl mx-auto">

        {{-- En-tête --}}
        <div class="mb-14">
            <div class="flex items-center gap-2 text-green-mid text-xs font-bold uppercase tracking-widest mb-3">
                <span class="w-6 h-0.5 bg-green-mid rounded"></span>
                Pourquoi MboaAcademy
            </div>
            <h2 class="font-playfair text-4xl lg:text-5xl font-bold text-dark leading-tight max-w-xl mb-4">
                Tout ce qu'il faut pour apprendre efficacement
            </h2>
            <p class="text-text-light text-base max-w-lg leading-relaxed">
                Une expérience d'apprentissage complète, pensée pour les réalités africaines.
            </p>
        </div>

        {{-- Grille features --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['🎬', 'Vidéos HD & Ressources',    'Des cours en vidéo de haute qualité, téléchargeables avec PDF, fiches et exercices pratiques.'],
                ['✅', 'Quiz & Auto-correction',     'Des QCM intelligents corrigés instantanément avec des explications détaillées pour progresser.'],
                ['📈', 'Suivi de Progression',       'Tableau de bord personnalisé pour suivre votre avancement, vos scores et vos badges obtenus.'],
                ['💬', 'Forum Communautaire',        'Un espace de discussion par cours pour poser vos questions et collaborer avec d\'autres apprenants.'],
                ['🏆', 'Certificats Reconnus',       'Obtenez des certificats vérifiables à partager sur LinkedIn et dans vos dossiers professionnels.'],
                ['📱', 'Accès Hors Ligne',           'Téléchargez vos cours et apprenez même sans connexion internet. Adapté aux réalités du terrain.'],
            ] as [$icon, $title, $desc])
            <div class="reveal group bg-white border border-black/[0.07] rounded-2xl p-8 relative overflow-hidden hover:-translate-y-1 hover:shadow-2xl hover:shadow-black/8 hover:border-green-bright/20 transition-all duration-300">
                {{-- Barre top au hover --}}
                <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-green-mid to-green-bright scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left rounded-t-2xl"></div>
                <div class="w-14 h-14 rounded-2xl bg-green-light flex items-center justify-center text-2xl mb-5">{{ $icon }}</div>
                <h3 class="font-playfair text-lg font-bold text-dark mb-2">{{ $title }}</h3>
                <p class="text-text-light text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
    COURS
═══════════════════════════════════════════ --}}
<section id="courses" class="py-24 px-6 lg:px-10 bg-dark relative overflow-hidden">
    {{-- Glow déco --}}
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(26,138,71,0.15), transparent 70%)"></div>

    <div class="max-w-7xl mx-auto relative">

        {{-- En-tête --}}
        <div class="mb-14">
            <div class="flex items-center gap-2 text-green-bright text-xs font-bold uppercase tracking-widest mb-3">
                <span class="w-6 h-0.5 bg-green-bright rounded"></span>
                Catalogue
            </div>
            <h2 class="font-playfair text-4xl lg:text-5xl font-bold text-white leading-tight mb-4">
                Cours populaires
            </h2>
            <p class="text-white/50 text-base max-w-lg leading-relaxed">
                Découvrez nos formations les plus suivies par la communauté.
            </p>
        </div>

        {{-- Grille cours --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                [
                    'gradient' => 'from-green-deep to-green-mid',
                    'icon'     => '💻',
                    'tag'      => 'Développement',
                    'title'    => 'Full Stack Web — Laravel & Vue.js',
                    'lessons'  => 42,
                    'hours'    => 28,
                    'rating'   => '4.9',
                    'initials' => 'NK',
                    'author'   => 'Nkosi Kamau',
                    'avatarBg' => 'bg-green-mid',
                    'price'    => '35 000 XAF',
                ],
                [
                    'gradient' => 'from-[#7a3b1e] to-[#c4682d]',
                    'icon'     => '📊',
                    'tag'      => 'Data Science',
                    'title'    => 'Python pour l\'Analyse de Données',
                    'lessons'  => 38,
                    'hours'    => 22,
                    'rating'   => '4.8',
                    'initials' => 'AM',
                    'author'   => 'Amara Diallo',
                    'avatarBg' => 'bg-[#7a3b1e]',
                    'price'    => '28 000 XAF',
                ],
                [
                    'gradient' => 'from-[#1a2a6c] to-[#4a4aad]',
                    'icon'     => '🎨',
                    'tag'      => 'Design',
                    'title'    => 'UI/UX Design — Figma Avancé',
                    'lessons'  => 30,
                    'hours'    => 18,
                    'rating'   => '4.7',
                    'initials' => 'FO',
                    'author'   => 'Fatou Ouédraogo',
                    'avatarBg' => 'bg-[#4a4aad]',
                    'price'    => '22 000 XAF',
                ],
            ] as $course)
            <div class="reveal group bg-white/[0.04] border border-white/[0.08] rounded-2xl overflow-hidden hover:-translate-y-1.5 hover:border-green-bright/30 hover:shadow-[0_20px_50px_rgba(0,0,0,0.4)] transition-all duration-300 cursor-pointer">

                {{-- Thumbnail --}}
                <div class="h-40 bg-gradient-to-br {{ $course['gradient'] }} flex items-center justify-center text-6xl relative">
                    {{ $course['icon'] }}
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/40"></div>
                </div>

                {{-- Body --}}
                <div class="p-5">
                    <span class="inline-block px-3 py-0.5 bg-green-bright/15 border border-green-bright/30 rounded-full text-[10px] font-bold text-green-bright uppercase tracking-wider mb-3">
                        {{ $course['tag'] }}
                    </span>
                    <h3 class="font-playfair text-base font-bold text-white mb-2 leading-snug">
                        {{ $course['title'] }}
                    </h3>
                    <div class="flex gap-4 text-xs text-white/40 mb-4">
                        <span>📚 {{ $course['lessons'] }} leçons</span>
                        <span>⏱ {{ $course['hours'] }}h</span>
                        <span>⭐ {{ $course['rating'] }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-white/[0.07]">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full {{ $course['avatarBg'] }} flex items-center justify-center text-xs font-bold text-white">
                                {{ $course['initials'] }}
                            </div>
                            <span class="text-xs text-white/55">{{ $course['author'] }}</span>
                        </div>
                        <span class="font-playfair text-base font-bold text-gold">{{ $course['price'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="#"
               class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-mid to-green-bright text-white font-semibold rounded-full shadow-xl shadow-green-bright/30 hover:-translate-y-0.5 transition-all duration-200">
                Voir tous les cours →
            </a>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
    STATS
═══════════════════════════════════════════ --}}
<section class="py-20 px-6 lg:px-10 relative overflow-hidden"
         style="background: linear-gradient(135deg, #0d5c2e, #1a8a47);">
    <div class="absolute inset-0 pointer-events-none"
         style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.03) 0px, rgba(255,255,255,0.03) 1px, transparent 1px, transparent 30px),
                                  repeating-linear-gradient(-45deg, rgba(255,255,255,0.03) 0px, rgba(255,255,255,0.03) 1px, transparent 1px, transparent 30px);">
    </div>
    <div class="max-w-7xl mx-auto relative">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            @foreach([
                ['2', ',4K', 'Apprenants actifs'],
                ['180', '+',  'Cours disponibles'],
                ['95',  '%',  'Taux de complétion'],
                ['12',  ' pays', 'Présence en Afrique'],
            ] as [$n, $suffix, $label])
            <div class="reveal">
                <div class="font-playfair text-5xl font-black text-white leading-none mb-2">
                    {{ $n }}<span class="text-gold">{{ $suffix }}</span>
                </div>
                <div class="text-sm text-white/60">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
    TÉMOIGNAGES
═══════════════════════════════════════════ --}}
<section id="temoignages" class="py-24 px-6 lg:px-10 bg-cream">
    <div class="max-w-7xl mx-auto">

        <div class="mb-14">
            <div class="flex items-center gap-2 text-green-mid text-xs font-bold uppercase tracking-widest mb-3">
                <span class="w-6 h-0.5 bg-green-mid rounded"></span>
                Témoignages
            </div>
            <h2 class="font-playfair text-4xl lg:text-5xl font-bold text-dark leading-tight mb-4">
                Ce que disent nos apprenants
            </h2>
            <p class="text-text-light text-base max-w-lg leading-relaxed">
                Des milliers de personnes transforment leur vie grâce à MboaAcademy.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                [
                    'text'    => 'MboaAcademy a complètement changé ma façon d\'apprendre. Les cours sont clairs, les quiz bien faits, et la communauté est incroyable. J\'ai décroché mon premier job en dev web 3 mois après.',
                    'name'    => 'Jean-Pierre Ngando',
                    'role'    => 'Développeur Web — Douala',
                    'initials'=> 'JN',
                    'bgColor' => 'bg-green-mid',
                    'stars'   => 5,
                ],
                [
                    'text'    => 'En tant que formatrice, j\'apprécie la simplicité de création de cours. L\'interface est intuitive, le suivi des apprenants est détaillé. La meilleure plateforme pour partager mon expertise.',
                    'name'    => 'Aminata Baldé',
                    'role'    => 'Formatrice Data Science — Dakar',
                    'initials'=> 'AB',
                    'bgColor' => 'bg-gold',
                    'stars'   => 5,
                ],
                [
                    'text'    => 'La possibilité d\'apprendre hors ligne est un vrai avantage pour moi. J\'habite une zone avec peu de connexion mais ça ne m\'empêche pas de progresser chaque jour.',
                    'name'    => 'Kofi Mensah',
                    'role'    => 'Étudiant en Design — Accra',
                    'initials'=> 'KM',
                    'bgColor' => 'bg-earth',
                    'stars'   => 4,
                ],
            ] as $testi)
            <div class="reveal bg-white border border-black/[0.07] rounded-2xl p-8 relative overflow-hidden">
                {{-- Guillemet déco --}}
                <div class="absolute top-4 right-5 font-playfair text-8xl text-green-light leading-none select-none pointer-events-none">"</div>

                {{-- Étoiles --}}
                <div class="flex gap-0.5 mb-4">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $testi['stars'] ? 'text-gold' : 'text-gray-200' }} text-sm">★</span>
                    @endfor
                </div>

                <p class="text-text-light text-sm leading-relaxed italic mb-6 relative">{{ $testi['text'] }}</p>

                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full {{ $testi['bgColor'] }} flex items-center justify-center text-sm font-bold text-white shrink-0">
                        {{ $testi['initials'] }}
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-dark">{{ $testi['name'] }}</div>
                        <div class="text-xs text-text-light">{{ $testi['role'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
    CTA FINAL
═══════════════════════════════════════════ --}}
<section class="py-28 px-6 lg:px-10 bg-dark text-center relative overflow-hidden">
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="w-[600px] h-[600px] rounded-full"
             style="background: radial-gradient(circle, rgba(37,194,110,0.10), transparent 70%)"></div>
    </div>
    <div class="max-w-2xl mx-auto relative">
        <h2 class="font-playfair text-4xl lg:text-5xl font-black text-white mb-4 leading-tight">
            Prêt à <em class="not-italic text-gold">transformer</em> ton avenir ?
        </h2>
        <p class="text-white/50 text-base max-w-md mx-auto leading-relaxed mb-10">
            Rejoins des milliers d'apprenants africains qui construisent leur expertise sur MboaAcademy.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-mid to-green-bright text-white font-semibold rounded-full shadow-xl shadow-green-bright/30 hover:-translate-y-0.5 hover:shadow-green-bright/50 transition-all duration-200">
                🚀 Créer mon compte gratuit
            </a>
            <a href="#courses"
               class="inline-flex items-center gap-2 px-8 py-4 border border-white/20 text-white/80 font-medium rounded-full hover:border-gold hover:text-gold transition-all duration-200">
                Voir le catalogue →
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Scroll reveal
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, i * 80);
            }
        });
    }, { threshold: 0.1 });

    reveals.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(24px)';
        el.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
        observer.observe(el);
    });
</script>
@endpush