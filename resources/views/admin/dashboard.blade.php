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

        /* Sidebar très sombre avec bordure dorée */
        .sidebar { width:270px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;display:flex;flex-direction:column;
                   background:#040a05;border-right:1px solid rgba(232,184,75,0.12); }
        .main-content { margin-left:270px;min-height:100vh; }

        /* Cards */
        .glass-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:18px; }
        .gold-card   { background:rgba(232,184,75,0.05);border:1px solid rgba(232,184,75,0.15);border-radius:18px; }

        /* Nav item admin — accentuation dorée */
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

        /* Activity dot */
        .dot-green { width:8px;height:8px;border-radius:50%;background:#25c26e;flex-shrink:0; }
        .dot-gold  { width:8px;height:8px;border-radius:50%;background:#e8b84b;flex-shrink:0; }
        .dot-red   { width:8px;height:8px;border-radius:50%;background:#f87171;flex-shrink:0; }

        /* Stat trend */
        .trend-up   { color:#25c26e;font-size:0.7rem;font-weight:700; }
        .trend-down { color:#f87171;font-size:0.7rem;font-weight:700; }

        /* Scrollbar */
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

    {{-- Admin profile --}}
    <div class="px-6 py-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-dark shrink-0"
                 style="background:linear-gradient(135deg,#e8b84b,#f0d070)">
                SA
            </div>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">Super Admin</div>
                <div class="text-xs px-2 py-0.5 rounded-full w-fit" style="background:rgba(232,184,75,0.12);color:#e8b84b">Administrateur</div>
            </div>
        </div>
    </div>

    <nav class="flex-1 py-4 overflow-y-auto">
        <div class="px-6 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Vue générale</div>
        <a href="#" class="nav-item active"><span class="icon">📊</span> Tableau de bord</a>
        <a href="#" class="nav-item"><span class="icon">📈</span> Analytiques</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Utilisateurs</div>
        <a href="#" class="nav-item"><span class="icon">👥</span> Tous les utilisateurs</a>
        <a href="#" class="nav-item"><span class="icon">🎓</span> Apprenants</a>
        <a href="#" class="nav-item"><span class="icon">📖</span> Formateurs</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Contenu</div>
        <a href="#" class="nav-item"><span class="icon">📚</span> Tous les cours <span class="ml-auto pill pill-gold">3 en attente</span></a>
        <a href="#" class="nav-item"><span class="icon">📝</span> Quiz & Exercices</a>
        <a href="#" class="nav-item"><span class="icon">💬</span> Forum</a>
        <a href="#" class="nav-item"><span class="icon">🏆</span> Certificats</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Finances</div>
        <a href="#" class="nav-item"><span class="icon">💰</span> Revenus globaux</a>
        <a href="#" class="nav-item"><span class="icon">💳</span> Paiements</a>
        <a href="#" class="nav-item"><span class="icon">🔄</span> Reversements</a>

        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Système</div>
        <a href="#" class="nav-item"><span class="icon">⚙️</span> Paramètres</a>
        <a href="#" class="nav-item"><span class="icon">🔔</span> Notifications</a>
        <a href="#" class="nav-item"><span class="icon">🛡️</span> Sécurité & Logs</a>
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

    {{-- Topbar admin --}}
    <header class="sticky top-0 z-30 flex items-center justify-between px-8 py-4 border-b"
            style="background:rgba(7,13,9,0.97);backdrop-filter:blur(12px);border-color:rgba(232,184,75,0.08)">
        <div>
            <h1 class="font-playfair text-xl font-bold text-white">Tableau de bord Admin</h1>
            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">{{ now()->isoFormat('dddd D MMMM YYYY') }} · Tout va bien 🟢</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Search --}}
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);color:rgba(255,255,255,0.35)">
                🔍 <input placeholder="Rechercher..." class="bg-transparent outline-none text-white placeholder-white/30 w-36 text-sm">
            </div>
            <button class="relative w-9 h-9 rounded-xl flex items-center justify-center text-sm"
                    style="background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.5)">
                🔔
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-[9px] font-bold text-white flex items-center justify-center">8</span>
            </button>
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs text-dark"
                 style="background:linear-gradient(135deg,#e8b84b,#f0d070)">SA</div>
        </div>
    </header>

    <div class="p-8">

        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl anim"
             style="background:rgba(232,184,75,0.08);border:1px solid rgba(232,184,75,0.2)">
            <span class="text-xl">👑</span>
            <p class="text-sm font-medium" style="color:#e8b84b">{{ session('success') }}</p>
        </div>
        @endif

        {{-- ── ALERTE VALIDATIONS EN ATTENTE --}}
        <div class="mb-6 flex items-center justify-between px-5 py-4 rounded-2xl anim"
             style="background:rgba(232,184,75,0.06);border:1px solid rgba(232,184,75,0.15)">
            <div class="flex items-center gap-3">
                <span class="text-xl">⏳</span>
                <p class="text-sm font-medium" style="color:#e8b84b">
                    <strong>3 cours</strong> attendent votre validation et <strong>2 formateurs</strong> demandent une vérification.
                </p>
            </div>
            <a href="#" class="text-xs font-bold px-4 py-2 rounded-xl shrink-0"
               style="background:#e8b84b;color:#0a1a0f">Traiter →</a>
        </div>

        {{-- ── STATS GLOBALES --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach([
                ['👥','Utilisateurs total','3 847','↑ 234 ce mois','#25c26e','green'],
                ['📚','Cours publiés','148','8 en attente','#3b82f6','blue'],
                ['💰','Revenus globaux','2 840 000 XAF','↑ 18% ce mois','#e8b84b','gold'],
                ['🌍','Pays représentés','14','+2 nouveaux','#a78bfa','purple'],
            ] as [$icon,$label,$val,$sub,$color,$key])
            <div class="card-hover glass-card p-5 anim anim-{{ $loop->index+1 }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center text-2xl"
                         style="background:{{ $color }}18">{{ $icon }}</div>
                    <span class="trend-up">{{ $sub }}</span>
                </div>
                <div class="font-playfair text-2xl font-bold mb-1" style="color:{{ $color }}">{{ $val }}</div>
                <div class="text-xs" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- ── COURS EN ATTENTE DE VALIDATION --}}
            <div class="lg:col-span-2 glass-card overflow-hidden anim anim-3">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <h2 class="font-playfair text-lg font-bold text-white">Cours à valider</h2>
                        <span class="pill pill-gold">3 en attente</span>
                    </div>
                    <a href="#" class="text-xs font-semibold" style="color:#e8b84b">Voir tout →</a>
                </div>
                <div class="divide-y divide-white/5">
                    @foreach([
                        ['🤖','Introduction au Machine Learning','Amara Diallo','Data Science','il y a 2h'],
                        ['🔒','Cybersécurité — Les Bases','Kofi Mensah','Sécurité','il y a 5h'],
                        ['📱','Développement Mobile React Native','Fatou O.','Mobile','il y a 1j'],
                    ] as [$icon,$title,$teacher,$tag,$date])
                    <div class="flex items-center gap-4 px-6 py-4 hover:bg-white/2 transition-colors">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-2xl shrink-0"
                             style="background:rgba(232,184,75,0.08)">{{ $icon }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-white truncate mb-1">{{ $title }}</div>
                            <div class="flex items-center gap-2 text-xs" style="color:rgba(255,255,255,0.4)">
                                <span>Par {{ $teacher }}</span>
                                <span>·</span>
                                <span class="pill pill-gray">{{ $tag }}</span>
                                <span>·</span>
                                <span>{{ $date }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button class="text-xs px-3 py-1.5 rounded-lg font-semibold transition-all hover:scale-105"
                                    style="background:rgba(37,194,110,0.12);color:#25c26e;border:1px solid rgba(37,194,110,0.2)">
                                ✓ Valider
                            </button>
                            <button class="text-xs px-3 py-1.5 rounded-lg font-semibold transition-all hover:scale-105"
                                    style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2)">
                                ✗ Refuser
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── RÉPARTITION UTILISATEURS --}}
            <div class="glass-card p-6 anim anim-4">
                <h2 class="font-playfair text-lg font-bold text-white mb-6">Répartition</h2>

                {{-- Donut simplifié --}}
                <div class="relative flex justify-center mb-6">
                    <svg width="130" height="130" viewBox="0 0 130 130">
                        {{-- Cercle de fond --}}
                        <circle cx="65" cy="65" r="52" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="18"/>
                        {{-- Students 76% --}}
                        <circle cx="65" cy="65" r="52" fill="none" stroke="#25c26e" stroke-width="18"
                                stroke-dasharray="{{ 2*3.14159*52*0.76 }} {{ 2*3.14159*52*0.24 }}"
                                stroke-dashoffset="{{ 2*3.14159*52*0.25 }}" stroke-linecap="butt"/>
                        {{-- Teachers 20% --}}
                        <circle cx="65" cy="65" r="52" fill="none" stroke="#e8b84b" stroke-width="18"
                                stroke-dasharray="{{ 2*3.14159*52*0.20 }} {{ 2*3.14159*52*0.80 }}"
                                stroke-dashoffset="{{ 2*3.14159*52*(0.25-0.76) }}" stroke-linecap="butt"/>
                        {{-- Admins 4% --}}
                        <circle cx="65" cy="65" r="52" fill="none" stroke="#a78bfa" stroke-width="18"
                                stroke-dasharray="{{ 2*3.14159*52*0.04 }} {{ 2*3.14159*52*0.96 }}"
                                stroke-dashoffset="{{ 2*3.14159*52*(0.25-0.96) }}" stroke-linecap="butt"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="font-playfair text-2xl font-bold text-white">3 847</span>
                        <span class="text-xs" style="color:rgba(255,255,255,0.4)">total</span>
                    </div>
                </div>

                {{-- Légende --}}
                <div class="space-y-3">
                    @foreach([
                        ['Apprenants','2 920','76%','#25c26e'],
                        ['Formateurs','771','20%','#e8b84b'],
                        ['Admins','156','4%','#a78bfa'],
                    ] as [$label,$count,$pct,$color])
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-sm shrink-0" style="background:{{ $color }}"></div>
                        <span class="text-sm flex-1" style="color:rgba(255,255,255,0.6)">{{ $label }}</span>
                        <span class="text-sm font-semibold text-white">{{ $count }}</span>
                        <span class="text-xs" style="color:{{ $color }}">{{ $pct }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Nouveaux inscrits --}}
                <div class="mt-5 p-3 rounded-xl" style="background:rgba(37,194,110,0.06);border:1px solid rgba(37,194,110,0.12)">
                    <div class="text-xs font-semibold mb-1" style="color:#25c26e">↑ +234 ce mois</div>
                    <div class="text-xs" style="color:rgba(255,255,255,0.35)">+18% vs mois précédent</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── ACTIVITÉ RÉCENTE GLOBALE --}}
            <div class="lg:col-span-2 glass-card overflow-hidden anim anim-5">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                    <h2 class="font-playfair text-lg font-bold text-white">Activité récente</h2>
                    <a href="#" class="text-xs font-semibold" style="color:#e8b84b">Voir les logs →</a>
                </div>
                <div class="divide-y divide-white/4">
                    @foreach([
                        ['🆕','Nouvelle inscription','Jean-Pierre Ngando (Apprenant)','il y a 5min','dot-green'],
                        ['📚','Cours soumis','Machine Learning — Amara Diallo','il y a 23min','dot-gold'],
                        ['✅','Cours validé','Full Stack Laravel — Admin','il y a 1h','dot-green'],
                        ['💰','Paiement reçu','35 000 XAF — Jean-Pierre N.','il y a 2h','dot-green'],
                        ['⚠️','Signalement forum','Message inapproprié détecté','il y a 3h','dot-red'],
                        ['🎓','Certificat émis','Fatou O. — Design UI/UX','il y a 4h','dot-green'],
                        ['🔐','Tentative échouée','3 tentatives login — IP inconnue','il y a 5h','dot-red'],
                    ] as [$icon,$action,$detail,$time,$dotClass])
                    <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-white/2 transition-colors">
                        <div class="{{ $dotClass }} mt-0.5"></div>
                        <span class="text-lg shrink-0">{{ $icon }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-white">{{ $action }}</div>
                            <div class="text-xs truncate" style="color:rgba(255,255,255,0.35)">{{ $detail }}</div>
                        </div>
                        <span class="text-[10px] shrink-0" style="color:rgba(255,255,255,0.2)">{{ $time }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── GESTION RAPIDE --}}
            <div class="space-y-4 anim anim-6">

                {{-- Revenus --}}
                <div class="gold-card p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-playfair text-base font-bold text-white">Revenus du mois</h3>
                        <span class="text-xs font-bold" style="color:#25c26e">↑ +18%</span>
                    </div>
                    <div class="font-playfair text-3xl font-bold mb-1" style="color:#e8b84b">2,84M</div>
                    <div class="text-xs mb-3" style="color:rgba(255,255,255,0.35)">XAF · 156 transactions</div>
                    <div class="prog-bar">
                        <div class="prog-fill" style="width:71%;background:linear-gradient(90deg,#e8b84b,#f0d070)"></div>
                    </div>
                    <div class="flex justify-between text-xs mt-1" style="color:rgba(255,255,255,0.3)">
                        <span>Objectif: 4M XAF</span><span>71%</span>
                    </div>
                </div>

                {{-- Actions rapides --}}
                <div class="glass-card p-5">
                    <h3 class="font-playfair text-base font-bold text-white mb-4">Actions rapides</h3>
                    <div class="space-y-2">
                        @foreach([
                            ['👤','Ajouter un utilisateur','#25c26e'],
                            ['📚','Valider des cours','#e8b84b'],
                            ['💳','Traiter les paiements','#3b82f6'],
                            ['📧','Envoyer une notification','#a78bfa'],
                            ['🛡️','Voir les logs sécurité','#f87171'],
                        ] as [$icon,$label,$color])
                        <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-left transition-all hover:-translate-x-0.5 hover:bg-white/3"
                                style="color:rgba(255,255,255,0.65)">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                  style="background:{{ $color }}15">{{ $icon }}</span>
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Santé système --}}
                <div class="glass-card p-5">
                    <h3 class="font-playfair text-base font-bold text-white mb-4">Santé système</h3>
                    <div class="space-y-3">
                        @foreach([
                            ['Serveur','Opérationnel','#25c26e',100],
                            ['Base de données','Opérationnel','#25c26e',98],
                            ['Stockage','82% utilisé','#e8b84b',82],
                            ['Emails','Opérationnel','#25c26e',100],
                        ] as [$service,$status,$color,$pct])
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full shrink-0" style="background:{{ $color }}"></div>
                            <span class="text-xs flex-1 text-white/60">{{ $service }}</span>
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