{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription — MboaAcademy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Motif kente animé */
        .kente {
            background-image:
                repeating-linear-gradient(45deg,  rgba(232,184,75,0.06) 0px, rgba(232,184,75,0.06) 2px, transparent 2px, transparent 18px),
                repeating-linear-gradient(-45deg, rgba(37,194,110,0.06) 0px, rgba(37,194,110,0.06) 2px, transparent 2px, transparent 18px);
        }
        /* Float animation pour les pastilles déco */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33%       { transform: translateY(-14px) rotate(3deg); }
            66%       { transform: translateY(-7px) rotate(-2deg); }
        }
        .float-1 { animation: float 7s ease-in-out infinite; }
        .float-2 { animation: float 9s ease-in-out infinite 1.5s; }
        .float-3 { animation: float 6s ease-in-out infinite 3s; }

        /* Entrée du formulaire */
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .slide-right { animation: slideRight 0.7s ease both; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        /* Input custom focus */
        .mboa-input {
            background: rgba(255,255,255,0.03);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.92rem;
            padding: 0.85rem 1.1rem;
            width: 100%;
            transition: all 0.25s;
            outline: none;
        }
        .mboa-input::placeholder { color: rgba(255,255,255,0.3); }
        .mboa-input:focus {
            border-color: #25c26e;
            background: rgba(37,194,110,0.06);
            box-shadow: 0 0 0 4px rgba(37,194,110,0.12);
        }
        .mboa-input:hover:not(:focus) { border-color: rgba(255,255,255,0.2); }

        /* Password strength bar */
        @keyframes growBar { from { width: 0; } }
        .strength-bar { animation: growBar 0.4s ease; }

        /* Checkbox custom */
        .mboa-check {
            appearance: none;
            width: 18px; height: 18px;
            border: 1.5px solid rgba(255,255,255,0.2);
            border-radius: 5px;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            flex-shrink: 0;
        }
        .mboa-check:checked {
            background: #25c26e;
            border-color: #25c26e;
        }
        .mboa-check:checked::after {
            content: '✓';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
        }

        /* Scrollbar dark */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0a1a0f; }
        ::-webkit-scrollbar-thumb { background: #1a8a47; border-radius: 3px; }
    </style>
</head>

<body class="bg-dark font-outfit overflow-x-hidden" style="background:#0a1a0f;">

<div class="min-h-screen flex">

    {{-- ══════════════════════════════════════
        PANNEAU GAUCHE — Branding + Visuels
    ══════════════════════════════════════ --}}
    <div class="hidden lg:flex lg:w-[45%] xl:w-[42%] relative flex-col justify-between p-10 overflow-hidden kente"
         style="background: linear-gradient(145deg, #0d5c2e 0%, #0a1a0f 60%, #071409 100%);">

        {{-- Glow radial --}}
        <div class="absolute inset-0 pointer-events-none"
             style="background: radial-gradient(ellipse 70% 50% at 30% 40%, rgba(37,194,110,0.18) 0%, transparent 65%),
                               radial-gradient(ellipse 40% 30% at 80% 80%, rgba(232,184,75,0.10) 0%, transparent 60%);">
        </div>

        {{-- Pastilles flottantes déco --}}
        <div class="float-1 absolute top-32 right-16 w-28 h-28 rounded-full border border-green-bright/15
                    flex items-center justify-center text-4xl opacity-60">🌿</div>
        <div class="float-2 absolute bottom-48 right-8 w-20 h-20 rounded-full border border-gold/15
                    flex items-center justify-center text-3xl opacity-50">⭐</div>
        <div class="float-3 absolute top-1/2 left-4 w-16 h-16 rounded-full border border-green-mid/20
                    flex items-center justify-center text-2xl opacity-40">📚</div>

        {{-- Logo --}}
        <a href="{{ route('welcome') }}" class="relative z-10">
            <span class="font-playfair text-2xl font-black text-white">Mboa<span style="color:#e8b84b">Academy</span></span>
        </a>

        {{-- Contenu central --}}
        <div class="relative z-10 flex-1 flex flex-col justify-center py-12">
            <div class="inline-flex items-center gap-2 bg-green-bright/10 border border-green-bright/25
                        rounded-full px-4 py-1.5 mb-6 w-fit">
                <span class="w-1.5 h-1.5 rounded-full bg-green-bright animate-pulse" style="background:#25c26e"></span>
                <span class="text-xs font-bold uppercase tracking-widest" style="color:#25c26e">Rejoins la communauté</span>
            </div>

            <h2 class="font-playfair text-4xl xl:text-5xl font-black text-white leading-[1.1] mb-5">
                Ton parcours<br>
                commence<br>
                <em class="not-italic" style="color:#e8b84b">ici.</em>
            </h2>

            <p class="text-sm leading-relaxed mb-10 max-w-xs" style="color:rgba(255,255,255,0.55);">
                Crée ton compte gratuitement et accède à des centaines de formations pensées pour l'Afrique.
            </p>

            {{-- Avantages --}}
            <div class="space-y-4">
                @foreach([
                    ['🎬', 'Accès immédiat à 180+ cours vidéo'],
                    ['🏆', 'Certificats reconnus & téléchargeables'],
                    ['💬', 'Communauté de 2 400+ apprenants'],
                    ['📱', 'Apprentissage hors ligne disponible'],
                ] as [$icon, $text])
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base shrink-0"
                         style="background:rgba(37,194,110,0.12); border:1px solid rgba(37,194,110,0.2);">
                        {{ $icon }}
                    </div>
                    <span class="text-sm" style="color:rgba(255,255,255,0.65);">{{ $text }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Testimonial bas --}}
        <div class="relative z-10 p-5 rounded-2xl" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.07);">
            <p class="text-sm italic mb-3" style="color:rgba(255,255,255,0.6);">
                "J'ai appris le développement web en 4 mois grâce à MboaAcademy. Aujourd'hui je travaille en freelance."
            </p>
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white"
                     style="background:#1a8a47">JN</div>
                <div>
                    <div class="text-xs font-semibold text-white">Jean-Pierre Ngando</div>
                    <div class="text-[10px]" style="color:rgba(255,255,255,0.4);">Développeur Freelance — Douala</div>
                </div>
                <div class="ml-auto flex gap-0.5">
                    @for($i=0;$i<5;$i++)<span style="color:#e8b84b; font-size:10px;">★</span>@endfor
                </div>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════
        PANNEAU DROIT — Formulaire
    ══════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 lg:px-14 xl:px-20 overflow-y-auto"
         style="background:#0f1f14;">

        {{-- Logo mobile --}}
        <div class="lg:hidden mb-10">
            <a href="{{ route('welcome') }}" class="font-playfair text-2xl font-black text-white">
                Mboa<span style="color:#e8b84b">Academy</span>
            </a>
        </div>

        <div class="w-full max-w-md">

            {{-- En-tête --}}
            <div class="mb-8 slide-right">
                <h1 class="font-playfair text-3xl font-bold text-white mb-2">Créer un compte</h1>
                <p class="text-sm" style="color:rgba(255,255,255,0.45);">
                    Déjà membre ?
                    <a href="{{ route('login') }}" class="font-semibold transition-colors hover:underline" style="color:#25c26e;">
                        Se connecter
                    </a>
                </p>
            </div>

            {{-- Erreurs de validation --}}
            @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl slide-right" style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.25);">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-red-400 text-sm">⚠</span>
                    <span class="text-red-400 text-xs font-semibold uppercase tracking-wide">Erreurs de saisie</span>
                </div>
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-xs" style="color:rgba(252,165,165,0.9);">• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('register') }}" x-data="registerForm()" class="space-y-5">
                @csrf

                {{-- Choix du rôle --}}
                <div class="slide-right delay-1">
                    <label class="block text-xs font-semibold uppercase tracking-widest mb-3" style="color:rgba(255,255,255,0.45);">
                        Je m'inscris en tant que
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([['student', '🎓', 'Apprenant', 'Je veux apprendre'], ['teacher', '📖', 'Formateur', 'Je veux enseigner']] as [$val, $icon, $label, $sub])
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="{{ $val }}"
                                   x-model="role"
                                   class="sr-only peer"
                                   {{ old('role', 'student') === $val ? 'checked' : '' }}>
                            <div class="p-4 rounded-2xl transition-all duration-200 peer-checked:shadow-lg text-center"
                                 :class="role === '{{ $val }}'
                                    ? 'border-green-bright bg-green-bright/10 shadow-green-bright/20'
                                    : 'border-white/10 bg-white/[0.02] hover:border-white/20'"
                                 style="border-width:1.5px; border-style:solid;">
                                <div class="text-2xl mb-1">{{ $icon }}</div>
                                <div class="text-sm font-semibold text-white">{{ $label }}</div>
                                <div class="text-[11px] mt-0.5" style="color:rgba(255,255,255,0.4);">{{ $sub }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Prénom + Nom --}}
                <div class="grid grid-cols-2 gap-3 slide-right delay-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(255,255,255,0.45);">Prénom</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm" style="color:rgba(255,255,255,0.3);">👤</span>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="mboa-input pl-9" placeholder="Jean" required autofocus>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(255,255,255,0.45);">Nom</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                               class="mboa-input" placeholder="Dupont" required>
                    </div>
                </div>

                {{-- Email --}}
                <div class="slide-right delay-3">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(255,255,255,0.45);">
                        Adresse email
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm" style="color:rgba(255,255,255,0.3);">✉</span>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="mboa-input pl-9" placeholder="jean@exemple.com" required>
                    </div>
                </div>

                {{-- Mot de passe --}}
                <div class="slide-right delay-4" x-data="{ show: false }">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(255,255,255,0.45);">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm" style="color:rgba(255,255,255,0.3);">🔒</span>
                        <input :type="show ? 'text' : 'password'"
                               name="password"
                               x-model="password"
                               @input="checkStrength"
                               class="mboa-input pl-9 pr-12"
                               placeholder="Min. 8 caractères" required>
                        <button type="button" @click="show = !show"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm transition-colors"
                                style="color:rgba(255,255,255,0.3);">
                            <span x-show="!show">👁</span>
                            <span x-show="show">🙈</span>
                        </button>
                    </div>

                    {{-- Barre de force --}}
                    <div class="mt-2.5 space-y-1.5" x-show="password.length > 0" x-transition>
                        <div class="flex gap-1.5">
                            @for($i=0; $i<4; $i++)
                            <div class="flex-1 h-1 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.08);">
                                <div class="h-full strength-bar rounded-full transition-all duration-400"
                                     :style="`width: ${strength > {{ $i }} ? '100%' : '0%'}; background: ${strengthColor}`">
                                </div>
                            </div>
                            @endfor
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[11px] transition-colors" :style="`color: ${strengthColor}`" x-text="strengthLabel"></span>
                            <span class="text-[11px]" style="color:rgba(255,255,255,0.3);" x-text="`${password.length} caractères`"></span>
                        </div>
                    </div>
                </div>

                {{-- Confirmer mot de passe --}}
                <div class="slide-right delay-5" x-data="{ show: false }">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(255,255,255,0.45);">
                        Confirmer le mot de passe
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm" style="color:rgba(255,255,255,0.3);">🔐</span>
                        <input :type="show ? 'text' : 'password'"
                               name="password_confirmation"
                               x-model="passwordConfirm"
                               class="mboa-input pl-9 pr-12"
                               placeholder="Répéter le mot de passe" required>
                        <button type="button" @click="show = !show"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm"
                                style="color:rgba(255,255,255,0.3);">
                            <span x-show="!show">👁</span>
                            <span x-show="show">🙈</span>
                        </button>
                        {{-- Indicateur de match --}}
                        <div x-show="passwordConfirm.length > 0"
                             class="absolute right-10 top-1/2 -translate-y-1/2 text-xs font-bold"
                             :style="passwordConfirm === password ? 'color:#25c26e' : 'color:#f87171'"
                             x-text="passwordConfirm === password ? '✓' : '✗'">
                        </div>
                    </div>
                </div>

                {{-- CGU --}}
                <div class="flex items-start gap-3 slide-right delay-5">
                    <input type="checkbox" name="terms" id="terms" class="mboa-check mt-0.5" required>
                    <label for="terms" class="text-xs leading-relaxed cursor-pointer" style="color:rgba(255,255,255,0.5);">
                        J'accepte les
                        <a href="#" class="font-semibold transition-colors hover:underline" style="color:#25c26e;">Conditions d'utilisation</a>
                        et la
                        <a href="#" class="font-semibold transition-colors hover:underline" style="color:#25c26e;">Politique de confidentialité</a>
                        de MboaAcademy.
                    </label>
                </div>

                {{-- Bouton submit --}}
                <div class="slide-right delay-5 pt-1">
                    <button type="submit"
                            class="w-full py-4 rounded-2xl font-semibold text-white text-base
                                   bg-gradient-to-r from-green-mid to-green-bright
                                   shadow-xl shadow-green-bright/25
                                   hover:-translate-y-0.5 hover:shadow-green-bright/45
                                   active:translate-y-0
                                   transition-all duration-200
                                   flex items-center justify-center gap-2"
                            style="background: linear-gradient(135deg, #1a8a47, #25c26e);">
                        🚀 Créer mon compte gratuitement
                    </button>
                </div>

                {{-- Séparateur --}}
                <div class="flex items-center gap-3 slide-right delay-5">
                    <div class="flex-1 h-px" style="background:rgba(255,255,255,0.07)"></div>
                    <span class="text-xs" style="color:rgba(255,255,255,0.25);">ou continuer avec</span>
                    <div class="flex-1 h-px" style="background:rgba(255,255,255,0.07)"></div>
                </div>

                {{-- Social logins --}}
                <div class="grid grid-cols-2 gap-3 slide-right delay-5">
                    <button type="button"
                            class="flex items-center justify-center gap-2 py-3 rounded-2xl text-sm font-medium text-white transition-all duration-200 hover:-translate-y-0.5"
                            style="background:rgba(255,255,255,0.04); border:1.5px solid rgba(255,255,255,0.1);"
                            onmouseover="this.style.borderColor='rgba(255,255,255,0.2)'"
                            onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Google
                    </button>
                    <button type="button"
                            class="flex items-center justify-center gap-2 py-3 rounded-2xl text-sm font-medium text-white transition-all duration-200 hover:-translate-y-0.5"
                            style="background:rgba(255,255,255,0.04); border:1.5px solid rgba(255,255,255,0.1);"
                            onmouseover="this.style.borderColor='rgba(255,255,255,0.2)'"
                            onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'">
                        <svg class="w-4 h-4" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </button>
                </div>

            </form>

            {{-- Footer minimal --}}
            <p class="text-center text-xs mt-8" style="color:rgba(255,255,255,0.2);">
                © {{ date('Y') }} MboaAcademy · 🌍 Made in Africa
            </p>
        </div>
    </div>
</div>

<script>
function registerForm() {
    return {
        role: '{{ old('role', 'student') }}',
        password: '',
        passwordConfirm: '',
        strength: 0,
        strengthLabel: '',
        strengthColor: '#e8b84b',

        checkStrength() {
            const p = this.password;
            let score = 0;
            if (p.length >= 8)                        score++;
            if (/[A-Z]/.test(p))                      score++;
            if (/[0-9]/.test(p))                      score++;
            if (/[^A-Za-z0-9]/.test(p))               score++;

            this.strength = score;
            const labels = ['', 'Faible', 'Moyen', 'Fort', 'Très fort'];
            const colors = ['', '#f87171', '#e8b84b', '#4ade80', '#25c26e'];
            this.strengthLabel = labels[score] || '';
            this.strengthColor = colors[score] || '#e8b84b';
        }
    }
}
</script>

</body>
</html>