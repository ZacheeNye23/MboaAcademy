{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — MboaAcademy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .kente {
            background-image:
                repeating-linear-gradient(45deg, rgba(232,184,75,0.06) 0px, rgba(232,184,75,0.06) 2px, transparent 2px, transparent 18px),
                repeating-linear-gradient(-45deg, rgba(37,194,110,0.06) 0px, rgba(37,194,110,0.06) 2px, transparent 2px, transparent 18px);
        }

        .mboa-input {
            background: rgba(255,255,255,0.03);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            color: #fff;
            font-size: 0.9rem;
            padding: 0.8rem 1rem;
            width: 100%;
            transition: all 0.25s;
            outline: none;
        }

        .mboa-input:focus {
            border-color: #25c26e;
            background: rgba(37,194,110,0.06);
            box-shadow: 0 0 0 4px rgba(37,194,110,0.12);
        }

        .mboa-check {
            appearance: none;
            width: 18px; height: 18px;
            border: 1.5px solid rgba(255,255,255,0.2);
            border-radius: 5px;
        }

        .mboa-check:checked {
            background: #25c26e;
            border-color: #25c26e;
        }
    </style>
</head>

<body class="bg-dark font-outfit" style="background:#0a1a0f;">

<div class="min-h-screen flex">

    {{-- 🔥 LEFT PANEL --}}
    <div class="hidden lg:flex lg:w-[45%] relative flex-col justify-between p-10 kente"
         style="background: linear-gradient(145deg, #0d5c2e 0%, #0a1a0f 60%);">

        <a href="{{ route('welcome') }}" class="text-white font-playfair text-2xl font-black">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>

        <div>
            <h2 class="text-4xl font-playfair font-black text-white mb-4">
                Bon retour 👋
            </h2>
            <p class="text-white/60 text-sm max-w-xs">
                Connecte-toi pour continuer ton apprentissage et accéder à tes cours.
            </p>
        </div>

        <div class="text-xs text-white/40">
            © {{ date('Y') }} MboaAcademy
        </div>
    </div>

    {{-- 🧊 RIGHT PANEL --}}
    <div class="flex-1 flex items-center justify-center px-6">

        <div class="w-full max-w-sm">

            {{-- HEADER --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-white mb-1">Connexion</h1>
                <p class="text-xs text-white/50">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="text-green-bright hover:underline">
                        S'inscrire
                    </a>
                </p>
            </div>

            {{-- ERRORS --}}
            @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl" style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2);">
                @foreach ($errors->all() as $error)
                    <div class="text-xs text-red-300">• {{ $error }}</div>
                @endforeach
            </div>
            @endif

            {{-- FORM --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <input type="email" name="email"
                           placeholder="Email"
                           class="mboa-input">
                </div>

                {{-- PASSWORD --}}
                <div class="relative">
                    <input type="password" name="password"
                           placeholder="Mot de passe"
                           class="mboa-input pr-10">
                </div>

                {{-- OPTIONS --}}
                <div class="flex items-center justify-between text-xs text-white/50">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="mboa-check">
                        Se souvenir
                    </label>

                    <a href="#" class="text-green-bright hover:underline">
                        Mot de passe oublié ?
                    </a>
                </div>

                {{-- BUTTON --}}
                <button type="submit"
                        class="w-full py-3 rounded-xl text-white font-semibold
                               bg-gradient-to-r from-green-mid to-green-bright
                               hover:-translate-y-0.5 transition">
                    🔐 Se connecter
                </button>

                {{-- DIVIDER --}}
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-px bg-white/10"></div>
                    <span class="text-xs text-white/40">ou</span>
                    <div class="flex-1 h-px bg-white/10"></div>
                </div>

                {{-- SOCIAL --}}
                <button type="button"
                        class="w-full py-3 rounded-xl text-white text-sm border border-white/10 hover:border-white/20 transition">
                    Continuer avec Google
                </button>

            </form>
        </div>
    </div>
</div>

</body>
</html>