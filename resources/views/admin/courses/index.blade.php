<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cours — Admin MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body{font-family:'Outfit',sans-serif;background:#070d09;color:#e0ebe2}
        .font-playfair{font-family:'Playfair Display',serif}
        .sidebar{width:270px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;display:flex;flex-direction:column;background:#040a05;border-right:1px solid rgba(232,184,75,0.12)}
        .main-content{margin-left:270px;min-height:100vh}
        .glass-card{background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:18px}
        .nav-item{display:flex;align-items:center;gap:12px;padding:10px 20px;border-radius:12px;color:rgba(255,255,255,0.4);font-size:.875rem;font-weight:500;text-decoration:none;transition:all .2s;margin:2px 12px}
        .nav-item:hover{background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.75)}
        .nav-item.active{background:rgba(232,184,75,0.1);color:#e8b84b}
        .nav-item .icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;background:rgba(255,255,255,0.04)}
        .nav-item.active .icon{background:rgba(232,184,75,0.15)}
        .pill{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:100px;font-size:.7rem;font-weight:700}
        .pill-green{background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2)}
        .pill-gold{background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2)}
        .pill-red{background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2)}
        .pill-gray{background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.35);border:1px solid rgba(255,255,255,0.08)}
        .pill-blue{background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2)}
        .filter-btn{padding:7px 14px;border-radius:100px;font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;border:none;font-family:'Outfit',sans-serif}
        .filter-btn.on{background:#e8b84b;color:#0a1a0f}
        .filter-btn.off{background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.45);border:1px solid rgba(255,255,255,0.08)}
        .filter-btn.off:hover{border-color:rgba(232,184,75,0.3);color:#e8b84b}
        .search-input{background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);border-radius:12px;padding:9px 16px 9px 40px;color:#fff;font-family:'Outfit',sans-serif;font-size:.875rem;outline:none;transition:all .2s;width:280px}
        .search-input::placeholder{color:rgba(255,255,255,0.25)}
        .search-input:focus{border-color:rgba(232,184,75,0.3);background:rgba(255,255,255,0.06)}
        .course-row{display:flex;align-items:center;gap:4px;padding:16px 24px;border-bottom:1px solid rgba(255,255,255,0.04);transition:background .2s}
        .course-row:hover{background:rgba(255,255,255,0.02)}
        .course-row:last-child{border-bottom:none}
        .action-btn{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;border-radius:8px;font-size:.72rem;font-weight:600;cursor:pointer;transition:all .18s;text-decoration:none;border:none;font-family:'Outfit',sans-serif}
        .btn-view{background:rgba(37,194,110,0.1);color:#25c26e}
        .btn-view:hover{background:rgba(37,194,110,0.2)}
        .btn-edit{background:rgba(232,184,75,0.1);color:#e8b84b}
        .btn-edit:hover{background:rgba(232,184,75,0.2)}
        .btn-approve{background:rgba(37,194,110,0.12);color:#25c26e}
        .btn-approve:hover{background:rgba(37,194,110,0.22)}
        .btn-reject{background:rgba(239,68,68,0.08);color:#f87171}
        .btn-reject:hover{background:rgba(239,68,68,0.15)}
        .btn-danger{background:rgba(239,68,68,0.06);color:rgba(239,68,68,0.6)}
        .btn-danger:hover{background:rgba(239,68,68,0.12);color:#f87171}
        .btn-primary{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:12px;font-family:'Outfit',sans-serif;font-size:.875rem;font-weight:700;color:#0a1a0f;background:linear-gradient(135deg,#e8b84b,#f0d070);border:none;cursor:pointer;transition:all .2s;text-decoration:none}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(232,184,75,0.3)}
        @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
        .anim{animation:fadeUp .4s ease both}
        .anim-1{animation-delay:.04s}.anim-2{animation-delay:.08s}.anim-3{animation-delay:.12s}.anim-4{animation-delay:.16s}
        ::-webkit-scrollbar{width:4px}::-webkit-scrollbar-track{background:#040a05}::-webkit-scrollbar-thumb{background:#1a8a47;border-radius:2px}
        [x-cloak]{display:none!important}
    </style>
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" class="font-playfair text-xl font-black text-white">Mboa<span style="color:#e8b84b">Academy</span></a>
        <div class="mt-1 text-xs font-semibold uppercase tracking-widest" style="color:#e8b84b">Administration</div>
    </div>
    <div class="px-6 py-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0" style="background:linear-gradient(135deg,#e8b84b,#f0d070);color:#0a1a0f">{{ auth()->user()->initials }}</div>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name }}</div>
                <div class="text-xs px-2 py-0.5 rounded-full w-fit" style="background:rgba(232,184,75,0.12);color:#e8b84b">Administrateur</div>
            </div>
        </div>
    </div>
    <nav class="flex-1 py-4 overflow-y-auto">
        <div class="px-6 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Vue générale</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item"><span class="icon">📊</span> Tableau de bord</a>
        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Utilisateurs</div>
        <a href="{{ route('admin.users.index') }}" class="nav-item"><span class="icon">👥</span> Utilisateurs</a>
        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Contenu</div>
        <a href="{{ route('admin.courses.index') }}" class="nav-item active">
            <span class="icon">📚</span> Cours
            @if($counts['pending'] > 0)
            <span class="ml-auto pill pill-gold">{{ $counts['pending'] }}</span>
            @endif
        </a>
        <a href="#" class="nav-item"><span class="icon">📝</span> Quiz</a>
        <a href="#" class="nav-item"><span class="icon">💬</span> Forum</a>
        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Finances</div>
        <a href="#" class="nav-item"><span class="icon">💰</span> Revenus</a>
        <div class="px-6 mt-4 mb-2 text-[10px] uppercase tracking-widest font-bold" style="color:rgba(255,255,255,0.15)">Système</div>
        <a href="#" class="nav-item"><span class="icon">⚙️</span> Paramètres</a>
    </nav>
    <div class="p-4 border-t border-white/5">
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" class="nav-item w-full text-left" style="background:rgba(239,68,68,0.07);color:rgba(239,68,68,0.7)">
                <span class="icon" style="background:rgba(239,68,68,0.08)">🚪</span> Déconnexion
            </button>
        </form>
    </div>
</aside>

<div class="main-content">

    {{-- Topbar --}}
    <header class="sticky top-0 z-30 flex items-center justify-between px-8 py-4 border-b"
            style="background:rgba(7,13,9,0.97);backdrop-filter:blur(12px);border-color:rgba(232,184,75,0.08)">
        <div>
            <h1 class="font-playfair text-xl font-bold text-white">Gestion des cours</h1>
            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">
                {{ $counts['all'] }} cours · <span style="color:#e8b84b">{{ $counts['pending'] }} en attente</span>
            </p>
        </div>
    </header>

    <div class="p-8">

        {{-- Flash --}}
        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl anim anim-1"
             style="background:rgba(37,194,110,0.07);border:1px solid rgba(37,194,110,0.18)">
            <span>🎉</span><p class="text-sm font-semibold" style="color:#25c26e">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach([
                ['📚','Total',       $counts['all'],       '#e8b84b'],
                ['✅','Publiés',     $counts['published'], '#25c26e'],
                ['⏳','En attente',  $counts['pending'],   '#e8b84b'],
                ['✏️','Brouillons',  $counts['draft'],     '#60a5fa'],
            ] as [$icon,$label,$count,$color])
            <div class="glass-card p-5 anim anim-{{ $loop->iteration }}">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-2xl">{{ $icon }}</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                          style="background:{{ $color }}15;color:{{ $color }}">{{ $label }}</span>
                </div>
                <div class="font-playfair text-3xl font-bold" style="color:{{ $color }}">{{ $count }}</div>
            </div>
            @endforeach
        </div>

        {{-- Filtres --}}
        <div class="glass-card p-5 mb-6 anim anim-2">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color:rgba(255,255,255,0.3)">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="search-input" placeholder="Titre, formateur...">
                </div>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['all'=>'🗂 Tous','published'=>'✅ Publiés','pending'=>'⏳ En attente','draft'=>'✏️ Brouillons','rejected'=>'🚫 Rejetés'] as $val=>$label)
                    <button type="submit" name="status" value="{{ $val }}"
                            class="filter-btn {{ request('status','all')===$val?'on':'off' }}">{{ $label }}</button>
                    @endforeach
                </div>
                <div class="ml-auto">
                    <select name="sort" onchange="this.form.submit()"
                            style="background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);border-radius:10px;padding:7px 12px;color:rgba(255,255,255,0.6);font-family:'Outfit',sans-serif;font-size:.8rem;outline:none">
                        <option value="latest"      {{ request('sort','latest')==='latest'      ?'selected':'' }}>Plus récents</option>
                        <option value="title"       {{ request('sort')==='title'                ?'selected':'' }}>A → Z</option>
                        <option value="enrollments" {{ request('sort')==='enrollments'          ?'selected':'' }}>Inscriptions ↓</option>
                        <option value="price_desc"  {{ request('sort')==='price_desc'           ?'selected':'' }}>Prix ↓</option>
                        <option value="price_asc"   {{ request('sort')==='price_asc'            ?'selected':'' }}>Prix ↑</option>
                    </select>
                </div>
            </form>
        </div>

        {{-- Alerte cours en attente --}}
        @if($counts['pending'] > 0 && request('status','all') !== 'pending')
        <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl anim anim-2"
             style="background:rgba(232,184,75,0.06);border:1px solid rgba(232,184,75,0.2)">
            <span class="text-xl">⏳</span>
            <p class="text-sm flex-1" style="color:rgba(255,255,255,0.7)">
                <span class="font-bold" style="color:#e8b84b">{{ $counts['pending'] }} cours</span>
                en attente de validation.
            </p>
            <a href="{{ route('admin.courses.index', ['status'=>'pending']) }}"
               class="action-btn btn-approve shrink-0">Voir →</a>
        </div>
        @endif

        {{-- Table --}}
        <div class="glass-card overflow-hidden anim anim-3">
            {{-- Entête --}}
            <div class="flex items-center gap-4 px-6 py-3 border-b border-white/5"
                 style="color:rgba(255,255,255,0.25);font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06rem">
                <div class="flex-1">Cours</div>
                <div class="w-32 hidden md:block">Formateur</div>
                <div class="w-24 text-center hidden lg:block">Inscrits</div>
                <div class="w-24 text-center hidden lg:block">Prix</div>
                <div class="w-28 text-center hidden md:block">Statut</div>
                <div class="w-40 text-right">Actions</div>
            </div>

            @forelse($courses as $course)
            @php
                $statusPill  = match($course->status) { 'published'=>'pill-green','pending'=>'pill-gold','rejected'=>'pill-red',default=>'pill-gray' };
                $statusLabel = match($course->status) { 'published'=>'✅ Publié','pending'=>'⏳ En attente','rejected'=>'🚫 Rejeté',default=>'✏️ Brouillon' };
                $levelLabel  = match($course->level ?? 'beginner') { 'intermediate'=>'Intermédiaire','advanced'=>'Avancé',default=>'Débutant' };
            @endphp
            <div class="course-row">
                {{-- Thumbnail + titre --}}
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}"
                             class="w-12 h-12 rounded-xl object-cover shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0"
                             style="background:rgba(232,184,75,0.08)">📚</div>
                    @endif
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-white truncate">{{ $course->title }}</div>
                        <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">
                            {{ $course->chapters_count }} chapitres · {{ $course->lessons_count }} leçons · {{ $levelLabel }}
                        </div>
                    </div>
                </div>

                {{-- Formateur --}}
                <div class="w-32 hidden md:block">
                    <div class="text-xs font-medium text-white truncate">{{ $course->teacher->full_name }}</div>
                </div>

                {{-- Inscrits --}}
                <div class="w-24 text-center hidden lg:block">
                    <span class="text-sm font-bold text-white">{{ $course->enrollments_count }}</span>
                </div>

                {{-- Prix --}}
                <div class="w-24 text-center hidden lg:block">
                    @if($course->is_free)
                        <span class="pill pill-green">Gratuit</span>
                    @else
                        <span class="text-sm font-bold" style="color:#e8b84b">
                            {{ number_format($course->price, 0, ',', ' ') }} XAF
                        </span>
                    @endif
                </div>

                {{-- Statut --}}
                <div class="w-28 text-center hidden md:block">
                    <span class="pill {{ $statusPill }}">{{ $statusLabel }}</span>
                </div>

                {{-- Actions --}}
                <div class="w-40 flex items-center justify-end gap-1.5 ml-4"
                     x-data="{ open: false }">
                    <a href="{{ route('admin.courses.show', $course) }}" class="action-btn btn-view">👁</a>
                    <a href="{{ route('admin.courses.edit', $course) }}" class="action-btn btn-edit">✏️</a>

                    {{-- Menu ... --}}
                    <div class="relative">
                        <button @click="open=!open" class="action-btn btn-edit" style="padding:5px 8px">⋯</button>
                        <div x-show="open" @click.outside="open=false" x-cloak
                             class="absolute right-0 top-8 w-48 rounded-xl overflow-hidden z-50 shadow-xl"
                             style="background:#0d1f10;border:1px solid rgba(255,255,255,0.08)"
                             x-transition>

                            {{-- Approuver --}}
                            @if($course->status === 'pending')
                            <form method="POST" action="{{ route('admin.courses.approve', $course) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-left hover:bg-white/5 transition-colors"
                                        style="color:#25c26e">
                                    ✅ Valider & publier
                                </button>
                            </form>
                            @endif

                            {{-- Dépublier --}}
                            @if($course->status === 'published')
                            <form method="POST" action="{{ route('admin.courses.unpublish', $course) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-left hover:bg-white/5 transition-colors"
                                        style="color:#e8b84b">
                                    ⏸ Dépublier
                                </button>
                            </form>
                            @endif

                            {{-- Rejeter --}}
                            @if(in_array($course->status, ['pending','published']))
                            <button type="button"
                                    @click="open=false; $dispatch('open-reject-modal', { id: {{ $course->id }}, title: '{{ addslashes($course->title) }}' })"
                                    class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-left hover:bg-white/5 transition-colors"
                                    style="color:#f87171">
                                🚫 Rejeter
                            </button>
                            @endif

                            <div style="height:1px;background:rgba(255,255,255,0.06);margin:4px 0"></div>

                            {{-- Supprimer --}}
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                  onsubmit="return confirm('Supprimer définitivement « {{ addslashes($course->title) }} » ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-left text-red-500 hover:bg-red-500/8 transition-colors">
                                    🗑 Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="text-5xl mb-4">📚</div>
                <h3 class="font-playfair text-lg font-bold text-white mb-2">Aucun cours trouvé</h3>
                <p class="text-sm" style="color:rgba(255,255,255,0.3)">Essayez de modifier vos filtres.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $courses->withQueryString()->links() }}</div>

    </div>
</div>

{{-- ═══ MODAL REJET ═══ --}}
<div x-data="rejectModal()" x-cloak
     @open-reject-modal.window="open($event.detail)"
     x-show="show"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,0.75);backdrop-filter:blur(4px)">

    <div class="w-full max-w-md rounded-2xl p-6"
         style="background:#0d1f10;border:1px solid rgba(239,68,68,0.2)"
         @click.outside="show=false"
         x-transition>

        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                 style="background:rgba(239,68,68,0.1)">🚫</div>
            <div>
                <h3 class="font-playfair text-base font-bold text-white">Rejeter le cours</h3>
                <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.4)" x-text="courseTitle"></p>
            </div>
        </div>

        <form :action="'/admin/courses/' + courseId + '/reject'" method="POST">
            @csrf @method('PATCH')

            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest mb-2"
                       style="color:rgba(255,255,255,0.4)">Raison du rejet *</label>
                <textarea name="reason" rows="4" required
                          class="w-full rounded-xl px-4 py-3 text-sm text-white resize-none outline-none"
                          style="background:rgba(255,255,255,0.05);border:1.5px solid rgba(255,255,255,0.08);font-family:'Outfit',sans-serif"
                          placeholder="Expliquez pourquoi ce cours est rejeté (sera envoyé au formateur)..."></textarea>
                @error('reason')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold transition-all hover:-translate-y-0.5"
                        style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.25)">
                    🚫 Confirmer le rejet
                </button>
                <button type="button" @click="show=false"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors hover:bg-white/5"
                        style="background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.45)">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function rejectModal() {
    return {
        show: false,
        courseId: null,
        courseTitle: '',
        open(detail) {
            this.courseId    = detail.id;
            this.courseTitle = detail.title;
            this.show        = true;
        }
    }
}
</script>

</body>
</html>