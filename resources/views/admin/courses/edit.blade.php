<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier {{ $course->title }} — Admin</title>
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
        .form-label{display:block;font-size:.72rem;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:.06rem;margin-bottom:8px}
        .form-input{width:100%;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);border-radius:12px;padding:11px 16px;font-family:'Outfit',sans-serif;font-size:.875rem;color:#fff;outline:none;transition:all .2s}
        .form-input:focus{border-color:rgba(232,184,75,0.4);background:rgba(255,255,255,0.06)}
        .form-input.error{border-color:rgba(239,68,68,0.5)}
        .form-input option{background:#0d1f10;color:#fff}
        textarea.form-input{min-height:130px;resize:vertical;line-height:1.7}
        .btn-primary{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border-radius:12px;font-family:'Outfit',sans-serif;font-size:.875rem;font-weight:700;color:#0a1a0f;background:linear-gradient(135deg,#e8b84b,#f0d070);border:none;cursor:pointer;transition:all .2s;text-decoration:none}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(232,184,75,0.3)}
        .btn-ghost{display:inline-flex;align-items:center;gap:8px;padding:11px 20px;border-radius:12px;font-family:'Outfit',sans-serif;font-size:.875rem;font-weight:600;color:rgba(255,255,255,0.5);background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);cursor:pointer;transition:all .2s;text-decoration:none}
        .btn-ghost:hover{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.75)}
        @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
        .anim{animation:fadeUp .4s ease both}
        .d1{animation-delay:.04s}.d2{animation-delay:.09s}
        ::-webkit-scrollbar{width:4px}::-webkit-scrollbar-track{background:#040a05}::-webkit-scrollbar-thumb{background:#1a8a47;border-radius:2px}
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
            <a href="{{ route('admin.courses.show', $course) }}" class="btn-ghost" style="padding:7px 14px;font-size:.8rem">← Retour</a>
            <div style="width:1px;height:20px;background:rgba(255,255,255,0.08)"></div>
            <h1 class="font-playfair text-xl font-bold text-white">Modifier le cours</h1>
        </div>
    </header>

    <div class="p-8 max-w-2xl">

        @if($errors->any())
        <div class="mb-6 px-5 py-4 rounded-2xl anim d1" style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.15)">
            <div class="flex items-center gap-2 mb-2 font-bold text-xs" style="color:#f87171">⚠ Erreurs</div>
            @foreach($errors->all() as $error)
            <p class="text-xs" style="color:#f87171">• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <div class="glass-card p-8 anim d1">
            <form method="POST" action="{{ route('admin.courses.update', $course) }}">
                @csrf @method('PUT')

                {{-- Titre --}}
                <div class="mb-5">
                    <label class="form-label">Titre *</label>
                    <input type="text" name="title" value="{{ old('title', $course->title) }}"
                           class="form-input @error('title') error @enderror" required>
                    @error('title')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div class="mb-5">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input @error('description') error @enderror">{{ old('description', $course->description) }}</textarea>
                    @error('description')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
                </div>

                {{-- Niveau + Statut --}}
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="form-label">Niveau *</label>
                        <select name="level" class="form-input @error('level') error @enderror">
                            @foreach(['beginner'=>'🟢 Débutant','intermediate'=>'🟡 Intermédiaire','advanced'=>'🔴 Avancé'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('level',$course->level)===$val?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Statut *</label>
                        <select name="status" class="form-input @error('status') error @enderror">
                            @foreach(['draft'=>'✏️ Brouillon','pending'=>'⏳ En attente','published'=>'✅ Publié','rejected'=>'🚫 Rejeté'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('status',$course->status)===$val?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Prix --}}
                <div class="mb-5" x-data="{ free: {{ $course->is_free ? 'true' : 'false' }} }">
                    <div class="flex items-center gap-3 mb-3">
                        <label class="form-label" style="margin-bottom:0">Prix</label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_free" value="1"
                                   x-model="free"
                                   {{ old('is_free', $course->is_free) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded accent-green-500">
                            <span class="text-xs" style="color:rgba(255,255,255,0.5)">Cours gratuit</span>
                        </label>
                    </div>
                    <div x-show="!free">
                        <input type="number" name="price" value="{{ old('price', $course->price) }}"
                               class="form-input @error('price') error @enderror"
                               placeholder="Prix en XAF" min="0" step="500">
                        @error('price')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div style="height:1px;background:rgba(255,255,255,0.06);margin-bottom:24px"></div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">💾 Enregistrer</button>
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn-ghost">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>