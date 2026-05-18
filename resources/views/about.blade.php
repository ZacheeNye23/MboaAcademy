@extends('layouts.app')

@section('title', 'À propos — MboaAcademy')

@section('content')

{{-- ═══════════════════════════════════════════
     HERO ABOUT
     ═══════════════════════════════════════════ --}}
<section class="relative min-h-[70vh] bg-dark flex items-center overflow-hidden pt-[72px]">

    {{-- Fonds décoratifs --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute inset-0"
             style="background: radial-gradient(ellipse 70% 60% at 20% 50%, rgba(26,138,71,0.16) 0%, transparent 60%),
                               radial-gradient(ellipse 50% 40% at 80% 80%, rgba(232,184,75,0.09) 0%, transparent 50%),
                               radial-gradient(ellipse 40% 30% at 90% 10%, rgba(37,194,110,0.10) 0%, transparent 50%);">
        </div>
        <div class="absolute inset-0 opacity-20"
             style="background-image: repeating-linear-gradient(90deg, rgba(37,194,110,0.05) 0px, rgba(37,194,110,0.05) 1px, transparent 1px, transparent 60px),
                                      repeating-linear-gradient(0deg, rgba(37,194,110,0.05) 0px, rgba(37,194,110,0.05) 1px, transparent 1px, transparent 60px);">
        </div>
    </div>

    <div class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-10 py-24">
        <div class="max-w-3xl">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-white/30 text-xs mb-8">
                <a href="{{ route('welcome') }}" class="hover:text-green-bright transition-colors">Accueil</a>
                <span>/</span>
                <span class="text-green-bright">À propos</span>
            </div>

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 bg-gold/10 border border-gold/30 rounded-full px-4 py-1.5 mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                <span class="text-gold text-xs font-bold tracking-widest uppercase">Notre histoire</span>
            </div>

            <h1 class="font-playfair text-5xl lg:text-6xl xl:text-7xl font-black text-white leading-[1.08] tracking-tight mb-6">
                Nés en Afrique,<br>
                <em class="not-italic text-green-bright relative">
                    pour l'Afrique.
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-green-bright to-transparent rounded"></span>
                </em>
            </h1>

            <p class="text-white/55 text-xl leading-relaxed max-w-2xl font-light">
                MboaAcademy est née d'une conviction simple : l'accès au savoir de qualité ne devrait pas dépendre de ta géographie, ta connexion internet ou ton portefeuille.
            </p>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
     MISSION
     ═══════════════════════════════════════════ --}}
<section class="py-24 px-6 lg:px-10 bg-cream">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Texte --}}
            <div class="reveal">
                <div class="flex items-center gap-2 text-green-mid text-xs font-bold uppercase tracking-widest mb-4">
                    <span class="w-6 h-0.5 bg-green-mid rounded"></span>
                    Notre mission
                </div>
                <h2 class="font-playfair text-4xl lg:text-5xl font-bold text-dark leading-tight mb-6">
                    Démocratiser l'éducation numérique en Afrique
                </h2>
                <p class="text-text-light text-base leading-relaxed mb-5">
                    Nous croyons que chaque jeune africain mérite une formation de classe mondiale, dispensée dans sa langue, pensée pour son contexte, et accessible depuis son téléphone — même avec une connexion limitée.
                </p>
                <p class="text-text-light text-base leading-relaxed mb-8">
                    MboaAcademy est plus qu'une plateforme : c'est une communauté de formateurs locaux passionnés et d'apprenants déterminés qui construisent ensemble l'économie numérique africaine de demain.
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-light rounded-full text-sm text-green-deep font-semibold">🌍 Panafricaine</span>
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-gold/15 rounded-full text-sm text-yellow-700 font-semibold">🤝 Communautaire</span>
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-dark/10 rounded-full text-sm text-dark font-semibold">📱 Mobile first</span>
                </div>
            </div>

            {{-- Encart visuel --}}
            <div class="reveal">
                <div class="relative">
                    {{-- Carte principale --}}
                    <div class="bg-dark rounded-2xl p-8 border border-white/[0.08] shadow-[0_40px_80px_rgba(0,0,0,0.2)]">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full bg-green-bright/20 flex items-center justify-center text-green-bright text-lg">🎯</div>
                            <div>
                                <div class="text-white font-semibold text-sm">Notre objectif 2026</div>
                                <div class="text-white/40 text-xs">Impact mesurable</div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @foreach([
                                ['50 000',  'Apprenants actifs',         85],
                                ['500+',    'Cours disponibles',         60],
                                ['30 pays', 'Présence sur le continent', 40],
                            ] as [$val, $lbl, $pct])
                            <div>
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-white/60 text-xs">{{ $lbl }}</span>
                                    <span class="text-green-bright font-bold text-sm font-playfair">{{ $val }}</span>
                                </div>
                                <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-green-mid to-green-bright rounded-full"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Badge flottant --}}
                    <div class="absolute -top-4 -right-4 bg-gold text-dark text-xs font-black px-4 py-2 rounded-full shadow-lg rotate-3">
                        🏆 Top plateforme Afrique
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
     VALEURS
     ═══════════════════════════════════════════ --}}
<section class="py-24 px-6 lg:px-10 bg-dark relative overflow-hidden">
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(37,194,110,0.10), transparent 70%)"></div>

    <div class="max-w-7xl mx-auto relative">

        <div class="mb-14 text-center">
            <div class="flex items-center justify-center gap-2 text-green-bright text-xs font-bold uppercase tracking-widest mb-3">
                <span class="w-6 h-0.5 bg-green-bright rounded"></span>
                Ce qui nous guide
                <span class="w-6 h-0.5 bg-green-bright rounded"></span>
            </div>
            <h2 class="font-playfair text-4xl lg:text-5xl font-bold text-white leading-tight">
                Nos valeurs fondatrices
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['🌱', 'Accessibilité',   'from-green-deep to-green-mid',    'Chaque apprenant mérite l\'accès au savoir, quelle que soit sa situation.'],
                ['🤝', 'Communauté',      'from-[#7a3b1e] to-[#c4682d]',     'Apprendre ensemble est plus puissant qu\'apprendre seul. Nous cultivons l\'entraide.'],
                ['💡', 'Excellence',      'from-[#1a4a7a] to-[#2d7aad]',     'Nos formations sont rigoureuses, actuelles et conçues pour le marché africain.'],
                ['🌍', 'Enracinement',    'from-[#4a1a6c] to-[#8a4aad]',     'Nous valorisons les savoirs africains et formons des experts qui restent sur le continent.'],
            ] as [$icon, $title, $gradient, $desc])
            <div class="reveal group bg-white/[0.03] border border-white/[0.07] rounded-2xl p-7 hover:-translate-y-1 hover:border-green-bright/25 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform duration-300">
                    {{ $icon }}
                </div>
                <h3 class="font-playfair text-lg font-bold text-white mb-3">{{ $title }}</h3>
                <p class="text-white/45 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
     HISTOIRE / TIMELINE
     ═══════════════════════════════════════════ --}}
<section class="py-24 px-6 lg:px-10 bg-cream">
    <div class="max-w-4xl mx-auto">

        <div class="mb-14">
            <div class="flex items-center gap-2 text-green-mid text-xs font-bold uppercase tracking-widest mb-3">
                <span class="w-6 h-0.5 bg-green-mid rounded"></span>
                Chronologie
            </div>
            <h2 class="font-playfair text-4xl lg:text-5xl font-bold text-dark leading-tight">
                Notre parcours
            </h2>
        </div>

        <div class="relative">
            {{-- Ligne verticale --}}
            <div class="absolute left-6 top-0 bottom-0 w-px bg-gradient-to-b from-green-mid via-green-bright to-transparent"></div>

            <div class="space-y-10">
                @foreach([
                    [
                        'year'  => '2026',
                        'tag'   => 'Fondation',
                        'title' => 'L\'idée naît à Yaoundé',
                        'desc'  => 'D\'un développeur camerounais, frustré par le manque de formations tech en français adaptées au contexte africain, décident de créer la plateforme qu\'ils auraient voulu avoir.',
                        'color' => 'bg-green-mid',
                    ],
                    [
                        'year'  => '2026',
                        'tag'   => 'prototype',
                        'title' => 'Premiers cours en ligne',
                        'desc'  => 'MboaAcademy lance ses 12 premiers cours en développement web et design. 800 apprenants s\'inscrivent en 3 mois. La validation du marché est là.',
                        'color' => 'bg-green-bright',
                    ],
                    [
                        'year'  => '2026',
                        'tag'   => 'Presentation',
                        'title' => 'Ouverture à toute l\'Afrique francophone',
                        'desc'  => 'Grâce au bouche-à-oreille et à la qualité de nos formations, la plateforme attire des apprenants du Sénégal, de Côte d\'Ivoire, du Mali, du Congo et de 8 autres pays.',
                        'color' => 'bg-gold',
                    ],
                    [
                        'year'  => '2026',
                        'tag'   => 'Vision',
                        'title' => 'Mode hors-ligne & app mobile',
                        'desc'  => 'Face aux réalités de connectivité du continent, nous lançons le téléchargement de cours et notre application mobile. Le taux de complétion des cours bondit de 40%.',
                        'color' => 'bg-green-mid',
                    ],
                    [
                        'year'  => 'Aujourd\'hui',
                        'tag'   => 'Présent',
                        'title' => '2 400+ apprenants, 180+ cours',
                        'desc'  => 'MboaAcademy est la référence e-learning en Afrique francophone. Nous accueillons chaque mois de nouveaux formateurs locaux et élargissons notre catalogue.',
                        'color' => 'bg-green-bright',
                    ],
                ] as $item)
                <div class="reveal relative flex gap-8 pl-16">
                    {{-- Dot --}}
                    <div class="absolute left-0 top-1 w-12 h-12 rounded-full {{ $item['color'] }} flex items-center justify-center text-white font-black text-xs shadow-lg z-10">
                        {{ substr($item['year'], -2) }}
                    </div>
                    {{-- Contenu --}}
                    <div class="flex-1 bg-white border border-black/[0.07] rounded-2xl p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-xs font-bold text-white px-3 py-1 rounded-full {{ $item['color'] }}">{{ $item['year'] }}</span>
                            <span class="text-xs text-text-light uppercase tracking-widest font-semibold">{{ $item['tag'] }}</span>
                        </div>
                        <h3 class="font-playfair text-lg font-bold text-dark mb-2">{{ $item['title'] }}</h3>
                        <p class="text-text-light text-sm leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
     ÉQUIPE
     ═══════════════════════════════════════════ --}}
<section class="py-24 px-6 lg:px-10 bg-dark">
    <div class="max-w-7xl mx-auto">

        <div class="mb-14 text-center">
            <div class="flex items-center justify-center gap-2 text-green-bright text-xs font-bold uppercase tracking-widest mb-3">
                <span class="w-6 h-0.5 bg-green-bright rounded"></span>
                Le visage
                <span class="w-6 h-0.5 bg-green-bright rounded"></span>
            </div>
            <h2 class="font-playfair text-4xl lg:text-5xl font-bold text-white leading-tight mb-4">
                L'équipe fondatrice
            </h2>
            <p class="text-white/45 text-base max-w-lg mx-auto">
                Un passionné du numérique africain, convaincu que l'éducation change tout.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8">
            @foreach([
                [
                    'initials' => 'ZN',
                    'name'     => 'Zachee Nyemeg',
                    'role'     => 'fondateur & CTO',
                    'bio'      => 'Développeur passionné par les applications performantes et accessibles, même sur des appareils modestes et des connexions limitées.',
                    'bg'       => 'from-[#1a2a6c] to-[#4a4aad]',
                    'Github' => "https://github.com/ZacheeNye23",
                ],
            ] as $member)
            <div class="reveal group bg-white/[0.04] border border-white/[0.08] rounded-2xl p-8 text-center hover:-translate-y-1.5 hover:border-green-bright/25 transition-all duration-300">

                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-full bg-gradient-to-br {{ $member['bg'] }} flex items-center justify-center text-white text-2xl font-black font-playfair mx-auto mb-5 shadow-xl group-hover:scale-105 transition-transform duration-300">
                    {{ $member['initials'] }}
                </div>

                <h3 class="font-playfair text-lg font-bold text-white mb-1">{{ $member['name'] }}</h3>
                <div class="text-green-bright text-xs font-semibold uppercase tracking-wider mb-4">{{ $member['role'] }}</div>
                <p class="text-white/45 text-sm leading-relaxed mb-5">{{ $member['bio'] }}</p>

                <a href="{{ $member['Github'] }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 text-xs text-white/30 hover:text-green-bright transition-colors">
                   <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
    <path d="M12 .5a12 12 0 00-3.79 23.4c.6.11.82-.26.82-.58v-2.03c-3.34.73-4.04-1.41-4.04-1.41a3.18 3.18 0 00-1.34-1.76c-1.1-.75.08-.74.08-.74a2.5 2.5 0 011.83 1.23 2.56 2.56 0 003.5 1 2.57 2.57 0 01.76-1.6c-2.66-.3-5.46-1.33-5.46-5.93a4.64 4.64 0 011.24-3.22 4.3 4.3 0 01.12-3.18s1-.32 3.3 1.23a11.5 11.5 0 016 0c2.28-1.55 3.3-1.23 3.3-1.23a4.3 4.3 0 01.12 3.18 4.64 4.64 0 011.24 3.22c0 4.61-2.8 5.62-5.47 5.92a2.88 2.88 0 01.82 2.24v3.32c0 .32.21.7.83.58A12 12 0 0012 .5z"/>
</svg>
                    Github
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
     CHIFFRES CLÉS
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
                ['2',  ',4K', 'Apprenants actifs'],
                ['180', '+',  'Cours disponibles'],
                ['95',  '%',  'Taux de satisfaction'],
                ['12',  ' pays', 'Présence africaine'],
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
     CTA FINAL
     ═══════════════════════════════════════════ --}}
<section class="py-28 px-6 lg:px-10 bg-dark text-center relative overflow-hidden">
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="w-[600px] h-[600px] rounded-full"
             style="background: radial-gradient(circle, rgba(37,194,110,0.08), transparent 70%)"></div>
    </div>
    <div class="max-w-2xl mx-auto relative">

        <div class="inline-flex items-center gap-2 bg-green-bright/10 border border-green-bright/30 rounded-full px-4 py-1.5 mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-green-bright animate-pulse"></span>
            <span class="text-green-bright text-xs font-bold tracking-widest uppercase">Rejoins-nous</span>
        </div>

        <h2 class="font-playfair text-4xl lg:text-5xl font-black text-white mb-4 leading-tight">
            Tu partages <em class="not-italic text-gold">notre vision</em> ?
        </h2>
        <p class="text-white/50 text-base max-w-md mx-auto leading-relaxed mb-10">
            Que tu sois apprenant, formateur ou partenaire — il y a une place pour toi dans l'aventure MboaAcademy.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-mid to-green-bright text-white font-semibold rounded-full shadow-xl shadow-green-bright/30 hover:-translate-y-0.5 hover:shadow-green-bright/50 transition-all duration-200">
                🚀 Commencer gratuitement
            </a>
            <a href="{{ route('student.courses.index') }}"
               class="inline-flex items-center gap-2 px-8 py-4 border border-white/20 text-white/80 font-medium rounded-full hover:border-gold hover:text-gold transition-all duration-200">
                Voir le catalogue →
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Scroll reveal (identique à la welcome page)
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