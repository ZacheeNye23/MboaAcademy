<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} — Admin</title>
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
        .pill{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:100px;font-size:.72rem;font-weight:700}
        .pill-green{background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2)}
        .pill-gold{background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2)}
        .pill-red{background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2)}
        .pill-gray{background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.35);border:1px solid rgba(255,255,255,0.08)}
        .stat-box{background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:14px;padding:18px 20px}
        .action-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:11px;font-family:'Outfit',sans-serif;font-size:.8rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;border:none}
        .btn-primary{background:linear-gradient(135deg,#e8b84b,#f0d070);color:#0a1a0f}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(232,184,75,0.3)}
        .btn-approve{background:rgba(37,194,110,0.12);color:#25c26e}
        .btn-approve:hover{background:rgba(37,194,110,0.22)}
        .btn-danger{background:rgba(239,68,68,0.1);color:#f87171}
        .btn-danger:hover{background:rgba(239,68,68,0.2)}
        .btn-ghost{background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)}
        .btn-ghost:hover{background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.75)}
        .prog-bar{height:5px;border-radius:3px;background:rgba(255,255,255,0.06);overflow:hidden}
        .prog-fill{height:100%;border-radius:3px}
        @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
        .anim{animation:fadeUp .4s ease both}
        .d1{animation-delay:.04s}.d2{animation-delay:.09s}.d3{animation-delay:.14s}
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
        <a href="{{ route('admin.dashboard') }}" class="nav-item"><span class="icon">📊</span> Tableau de bord</a>
        <a href="{{ route('admin.users.index') }}" class="nav-item mt-2"><span class="icon">👥</span> Utilisateurs</a>
        <a href="{{ route('admin.courses.index') }}" class="nav-item active"><span class="icon">📚</span> Cours</a>
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
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" class="action-btn btn-ghost" style="padding:7px 14px;font-size:.8rem">← Retour</a>
            <div style="width:1px;height:20px;background:rgba(255,255,255,0.08)"></div>
            <div>
                <h1 class="font-playfair text-xl font-bold text-white truncate max-w-md">{{ $course->title }}</h1>
                <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.3)">Par {{ $course->teacher->full_name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($course->status === 'pending')
                <form method="POST" action="{{ route('admin.courses.approve', $course) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="action-btn btn-approve">✅ Valider</button>
                </form>
                <button type="button" class="action-btn btn-danger"
                        x-data @click="$dispatch('open-reject-modal', { id: {{ $course->id }}, title: '{{ addslashes($course->title) }}' })">
                    🚫 Rejeter
                </button>
            @elseif($course->status === 'published')
                <form method="POST" action="{{ route('admin.courses.unpublish', $course) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="action-btn btn-ghost">⏸ Dépublier</button>
                </form>
            @endif
            <a href="{{ route('admin.courses.edit', $course) }}" class="action-btn btn-primary">✏️ Modifier</a>
        </div>
    </header>

    <div class="p-8">

        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl anim d1"
             style="background:rgba(37,194,110,0.07);border:1px solid rgba(37,194,110,0.18)">
            <span>🎉</span><p class="text-sm font-semibold" style="color:#25c26e">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Alerte cours en attente --}}
        @if($course->status === 'pending')
        <div class="mb-6 px-5 py-4 rounded-2xl anim d1"
             style="background:rgba(232,184,75,0.06);border:1px solid rgba(232,184,75,0.25)">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⏳</span>
                <div>
                    <p class="text-sm font-bold" style="color:#e8b84b">Ce cours est en attente de validation</p>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.4)">Vérifiez le contenu ci-dessous avant de valider ou rejeter.</p>
                </div>
            </div>
        </div>
        @endif

        @if($course->status === 'rejected' && $course->rejection_reason)
        <div class="mb-6 px-5 py-4 rounded-2xl anim d1"
             style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.2)">
            <p class="text-xs font-bold mb-1" style="color:#f87171">🚫 Raison du rejet :</p>
            <p class="text-sm" style="color:rgba(255,255,255,0.6)">{{ $course->rejection_reason }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Colonne gauche --}}
            <div class="space-y-5 anim d1">

                {{-- Thumbnail --}}
                <div class="glass-card overflow-hidden">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}"
                             class="w-full h-40 object-cover">
                    @else
                        <div class="w-full h-40 flex items-center justify-center text-5xl"
                             style="background:rgba(232,184,75,0.06)">📚</div>
                    @endif
                    <div class="p-5">
                        @php
                            $statusPill  = match($course->status) { 'published'=>'pill-green','pending'=>'pill-gold','rejected'=>'pill-red',default=>'pill-gray' };
                            $statusLabel = match($course->status) { 'published'=>'✅ Publié','pending'=>'⏳ En attente','rejected'=>'🚫 Rejeté',default=>'✏️ Brouillon' };
                        @endphp
                        <div class="flex items-center justify-between mb-3">
                            <span class="pill {{ $statusPill }}">{{ $statusLabel }}</span>
                            @if($course->is_free)
                                <span class="pill pill-green">Gratuit</span>
                            @else
                                <span class="text-sm font-bold" style="color:#e8b84b">
                                    {{ number_format($course->price, 0, ',', ' ') }} XAF
                                </span>
                            @endif
                        </div>
                        <h2 class="font-playfair text-base font-bold text-white mb-1">{{ $course->title }}</h2>
                        <p class="text-xs" style="color:rgba(255,255,255,0.4)">
                            Créé {{ $course->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                {{-- Formateur --}}
                <div class="glass-card p-5">
                    <h3 class="font-playfair text-sm font-bold text-white mb-4">Formateur</h3>
                    <div class="flex items-center gap-3">
                        @php $avatarColors=['#1a8a47','#e8b84b','#3b82f6','#a78bfa','#f87171']; $bg=$avatarColors[$course->teacher->id % 5]; @endphp
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                             style="background:{{ $bg }}">{{ $course->teacher->initials }}</div>
                        <div>
                            <div class="text-sm font-semibold text-white">{{ $course->teacher->full_name }}</div>
                            <div class="text-xs" style="color:rgba(255,255,255,0.4)">{{ $course->teacher->email }}</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $course->teacher) }}"
                       class="mt-4 flex items-center justify-center gap-2 py-2 rounded-xl text-xs font-semibold transition-colors hover:bg-white/5"
                       style="background:rgba(255,255,255,0.03);color:rgba(255,255,255,0.5);border:1px solid rgba(255,255,255,0.07)">
                        Voir le profil →
                    </a>
                </div>

                {{-- Infos --}}
                <div class="glass-card p-5">
                    <h3 class="font-playfair text-sm font-bold text-white mb-4">Informations</h3>
                    <div class="space-y-3 text-sm">
                        @foreach([
                            ['🎯','Niveau',    match($course->level??'beginner'){'intermediate'=>'Intermédiaire','advanced'=>'Avancé',default=>'Débutant'}],
                            ['📖','Chapitres', $course->chapters_count.' chapitres'],
                            ['📄','Leçons',    $course->lessons_count.' leçons'],
                            ['📝','Quiz',      $course->quizzes_count.' quiz'],
                            ['📅','Créé le',   $course->created_at->format('d/m/Y')],
                        ] as [$icon,$label,$val])
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2" style="color:rgba(255,255,255,0.4)">
                                <span>{{ $icon }}</span><span>{{ $label }}</span>
                            </div>
                            <span class="text-white font-medium">{{ $val }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-5 anim d2">

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-4">
                    @foreach([
                        ['🎓','Inscrits',    $course->enrollments_count,  '#25c26e'],
                        ['✅','Taux compl.', $completionRate.'%',          '#e8b84b'],
                        ['💬','Discussions', $course->forumThreads()->count(),'#a78bfa'],
                    ] as [$icon,$label,$val,$color])
                    <div class="stat-box text-center">
                        <div class="text-2xl mb-1">{{ $icon }}</div>
                        <div class="font-playfair text-2xl font-bold" style="color:{{ $color }}">{{ $val }}</div>
                        <div class="text-xs mt-1" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>

                {{-- Description --}}
                @if($course->description)
                <div class="glass-card p-5">
                    <h3 class="font-playfair text-base font-bold text-white mb-3">Description</h3>
                    <p class="text-sm leading-relaxed" style="color:rgba(255,255,255,0.55)">{{ $course->description }}</p>
                </div>
                @endif

                {{-- Programme --}}
                <div class="glass-card overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
                        <h3 class="font-playfair text-base font-bold text-white">Programme</h3>
                        <span class="text-xs" style="color:rgba(255,255,255,0.3)">{{ $course->chapters_count }} chapitres · {{ $course->lessons_count }} leçons</span>
                    </div>
                    @forelse($course->chapters as $chapter)
                    <div class="px-5 py-3.5 border-b border-white/4 last:border-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold shrink-0"
                                  style="background:rgba(232,184,75,0.15);color:#e8b84b">{{ $loop->iteration }}</span>
                            <span class="text-sm font-semibold text-white">{{ $chapter->title }}</span>
                            <span class="text-xs ml-auto" style="color:rgba(255,255,255,0.3)">{{ $chapter->lessons->count() }} leçons</span>
                        </div>
                        @if($chapter->lessons->count() > 0)
                        <div class="ml-7 space-y-1">
                            @foreach($chapter->lessons->take(3) as $lesson)
                            <div class="text-xs" style="color:rgba(255,255,255,0.35)">
                                · {{ $lesson->title }}
                            </div>
                            @endforeach
                            @if($chapter->lessons->count() > 3)
                            <div class="text-xs" style="color:rgba(255,255,255,0.2)">
                                + {{ $chapter->lessons->count() - 3 }} autres...
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucun chapitre ajouté.</p>
                    </div>
                    @endforelse
                </div>

                {{-- Dernières inscriptions --}}
                <div class="glass-card overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
                        <h3 class="font-playfair text-base font-bold text-white">Dernières inscriptions</h3>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                              style="background:rgba(37,194,110,0.1);color:#25c26e">{{ $course->enrollments_count }} total</span>
                    </div>
                    @forelse($recentEnrollments as $enrollment)
                    @php $avatarColors=['#1a8a47','#e8b84b','#3b82f6','#a78bfa','#f87171']; $bg=$avatarColors[$enrollment->user->id % 5]; @endphp
                    <div class="flex items-center gap-3 px-5 py-3.5 border-b border-white/4 last:border-0 hover:bg-white/2 transition-colors">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                             style="background:{{ $bg }}">{{ $enrollment->user->initials }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-white">{{ $enrollment->user->full_name }}</div>
                            <div class="text-xs" style="color:rgba(255,255,255,0.3)">{{ $enrollment->user->email }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-xs" style="color:rgba(255,255,255,0.25)">{{ $enrollment->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs font-semibold mt-0.5" style="color:#25c26e">{{ $enrollment->progress_percent ?? 0 }}%</div>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucune inscription encore.</p>
                    </div>
                    @endforelse
                </div>

                {{-- Zone danger --}}
                <div class="glass-card p-5" style="border-color:rgba(239,68,68,0.15)">
                    <h3 class="font-playfair text-base font-bold mb-3" style="color:#f87171">⚠️ Zone dangereuse</h3>
                    <p class="text-xs mb-4" style="color:rgba(255,255,255,0.35)">La suppression est irréversible. Toutes les inscriptions, leçons et quiz liés seront supprimés.</p>
                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                          onsubmit="return confirm('Supprimer définitivement « {{ addslashes($course->title) }} » ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn btn-danger">🗑 Supprimer ce cours</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal rejet --}}
<div x-data="rejectModal()" x-cloak
     @open-reject-modal.window="open($event.detail)"
     x-show="show"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,0.75);backdrop-filter:blur(4px)">
    <div class="w-full max-w-md rounded-2xl p-6"
         style="background:#0d1f10;border:1px solid rgba(239,68,68,0.2)"
         @click.outside="show=false" x-transition>
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background:rgba(239,68,68,0.1)">🚫</div>
            <div>
                <h3 class="font-playfair text-base font-bold text-white">Rejeter le cours</h3>
                <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.4)" x-text="courseTitle"></p>
            </div>
        </div>
        <form :action="'/admin/courses/' + courseId + '/reject'" method="POST">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color:rgba(255,255,255,0.4)">Raison du rejet *</label>
                <textarea name="reason" rows="4" required
                          class="w-full rounded-xl px-4 py-3 text-sm text-white resize-none outline-none"
                          style="background:rgba(255,255,255,0.05);border:1.5px solid rgba(255,255,255,0.08);font-family:'Outfit',sans-serif"
                          placeholder="Expliquez pourquoi ce cours est rejeté..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold" style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.25)">🚫 Confirmer</button>
                <button type="button" @click="show=false" class="px-5 py-2.5 rounded-xl text-sm font-semibold" style="background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.45)">Annuler</button>
            </div>
        </form>
    </div>
</div>
<script>function rejectModal(){return{show:false,courseId:null,courseTitle:'',open(d){this.courseId=d.id;this.courseTitle=d.title;this.show=true}}}</script>

</body>
</html>