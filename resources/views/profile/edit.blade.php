@extends('student.layouts.app')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')
@section('page-subtitle', 'Gérez vos informations personnelles')

@push('styles')
<style>
    .profile-hero {
        background: linear-gradient(135deg, #0a1a0f 0%, #0d5c2e 100%);
        border-radius: 24px;
        padding: 36px;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
    }
    .profile-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: repeating-linear-gradient(
            45deg,
            rgba(37,194,110,0.04) 0,
            rgba(37,194,110,0.04) 1px,
            transparent 1px,
            transparent 28px
        );
    }
    .profile-hero::after {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(37,194,110,0.12), transparent 70%);
    }
    .avatar-ring {
        width: 96px; height: 96px;
        border-radius: 50%;
        padding: 3px;
        background: linear-gradient(135deg, #25c26e, #e8b84b);
        flex-shrink: 0;
        position: relative; z-index: 1;
    }
    .avatar-ring img, .avatar-ring .avatar-fallback {
        width: 100%; height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #0a1a0f;
    }
    .avatar-fallback {
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #1a8a47, #25c26e);
        font-size: 1.6rem; font-weight: 700; color: #fff;
        border: 2px solid #0a1a0f;
    }
    .section-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 22px;
        overflow: hidden;
    }
    .section-header {
        padding: 20px 28px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex; align-items: center; gap: 12px;
    }
    .section-icon {
        width: 38px; height: 38px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .form-group { margin-bottom: 20px; }
    .form-label {
        display: block;
        font-size: .72rem; font-weight: 700;
        color: #374151;
        text-transform: uppercase; letter-spacing: .06rem;
        margin-bottom: 8px;
    }
    .form-input {
        width: 100%;
        background: #f9fafb;
        border: 1.5px solid rgba(0,0,0,0.09);
        border-radius: 14px;
        padding: 11px 16px;
        font-family: 'Outfit', sans-serif;
        font-size: .875rem; color: #1f2937;
        outline: none; transition: all .2s;
    }
    .form-input:focus {
        border-color: #25c26e;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37,194,110,0.1);
    }
    .form-input.error { border-color: #ef4444; background: rgba(239,68,68,0.03); }
    textarea.form-input { min-height: 110px; resize: vertical; line-height: 1.7; }
    .link-input-wrapper {
        position: relative;
    }
    .link-input-wrapper .link-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        font-size: .9rem; pointer-events: none;
    }
    .link-input-wrapper .form-input { padding-left: 40px; }
    .btn-primary {
        display: inline-flex; align-items: center; gap-8px; gap: 8px;
        padding: 11px 24px;
        border-radius: 14px;
        font-family: 'Outfit', sans-serif;
        font-size: .875rem; font-weight: 700;
        color: #fff; border: none; cursor: pointer;
        background: linear-gradient(135deg, #1a8a47, #25c26e);
        box-shadow: 0 4px 14px rgba(37,194,110,0.3);
        transition: all .2s;
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,194,110,0.4); }
    .btn-secondary {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 20px;
        border-radius: 14px;
        font-family: 'Outfit', sans-serif;
        font-size: .875rem; font-weight: 600;
        color: #6b7280; border: 1.5px solid rgba(0,0,0,0.1);
        background: transparent; cursor: pointer;
        transition: all .2s;
    }
    .btn-secondary:hover { border-color: #25c26e; color: #1a8a47; background: rgba(37,194,110,0.04); }
    .avatar-upload-zone {
        border: 2px dashed rgba(37,194,110,0.25);
        border-radius: 16px;
        padding: 28px;
        text-align: center;
        background: rgba(37,194,110,0.02);
        transition: all .2s;
        cursor: pointer;
        position: relative;
    }
    .avatar-upload-zone:hover {
        border-color: #25c26e;
        background: rgba(37,194,110,0.05);
    }
    .avatar-upload-zone input[type="file"] {
        position: absolute; inset: 0;
        opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .avatar-preview {
        width: 72px; height: 72px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(37,194,110,0.3);
        margin: 0 auto 12px;
        display: block;
    }
    .stat-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px;
        border-radius: 100px;
        font-size: .72rem; font-weight: 600;
    }
    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation: fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.09s}.d3{animation-delay:.14s}.d4{animation-delay:.19s}
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')

{{-- ── HERO PROFIL ──────────────────────────────────────────────────────── --}}
<div class="profile-hero anim d1">
    <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-6">

        {{-- Avatar --}}
        <div class="avatar-ring">
            @if($user->avatar)
                <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->full_name }}">
            @else
                <div class="avatar-fallback">{{ $user->initials }}</div>
            @endif
        </div>

        {{-- Infos --}}
        <div class="flex-1 min-w-0">
            <h2 class="text-white text-2xl font-black leading-tight mb-1"
                style="font-family:'Playfair Display',serif">
                {{ $user->full_name }}
            </h2>
            <p class="text-white/50 text-sm mb-4">{{ $user->email }}</p>
            <div class="flex flex-wrap gap-2">
                <span class="stat-chip" style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.55)">
                    📅 Membre depuis {{ $user->created_at->translatedFormat('M Y') }}
                </span>
                @if($user->github)
                <a href="{{ $user->github }}" target="_blank"
                   class="stat-chip hover:opacity-80 transition-opacity"
                   style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.55)">
                    🔗 GitHub
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── GRILLE ───────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- COLONNE GAUCHE : Avatar --}}
    <div class="space-y-5 anim d2">

        {{-- Upload avatar --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(37,194,110,0.1)">🖼️</div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Photo de profil</h3>
                    <p class="text-xs text-gray-400 mt-0.5">JPG, PNG · Max 2 Mo</p>
                </div>
            </div>
            <div class="p-6">
                @if(session('avatar_success'))
                <div class="mb-4 px-4 py-3 rounded-xl text-xs font-semibold"
                     style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2);color:#1a8a47">
                    🎉 {{ session('avatar_success') }}
                </div>
                @endif
                <form method="POST" action="{{ route('profile.avatar') }}"
                      enctype="multipart/form-data"
                      x-data="{ filename: null, preview: null }"
                      @submit.prevent="$el.submit()">
                    @csrf

                    <div class="avatar-upload-zone mb-4"
                         @dragover.prevent @drop.prevent="
                            const f = $event.dataTransfer.files[0];
                            if(f){ filename=f.name; const r=new FileReader(); r.onload=e=>preview=e.target.result; r.readAsDataURL(f); }
                         ">
                        <input type="file" name="avatar" accept="image/*"
                               @change="
                                const f=$event.target.files[0];
                                if(f){ filename=f.name; const r=new FileReader(); r.onload=e=>preview=e.target.result; r.readAsDataURL(f); }
                               ">

                        <template x-if="preview">
                            <img :src="preview" class="avatar-preview">
                        </template>
                        <template x-if="!preview">
                            <div class="text-3xl mb-2">📷</div>
                        </template>

                        <p class="text-xs text-gray-500 font-medium" x-text="filename ?? 'Glissez ou cliquez pour choisir'"></p>
                        <p class="text-[10px] text-gray-400 mt-1">Formats acceptés : JPG, PNG, WEBP</p>
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center">
                        📤 Mettre à jour la photo
                    </button>
                </form>
            </div>
        </div>

        {{-- Liens sociaux --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(59,130,246,0.1)">🔗</div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Liens & réseaux</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Partagez vos profils</p>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PATCH')

                    <div class="form-group">
                        <label class="form-label">GitHub</label>
                        <div class="link-input-wrapper">
                            <span class="link-icon">🐙</span>
                            <input type="url" name="github"
                                   value="{{ old('github', $user->github) }}"
                                   class="form-input @error('github') error @enderror"
                                   placeholder="https://github.com/username">
                        </div>
                        @error('github')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">LinkedIn</label>
                        <div class="link-input-wrapper">
                            <span class="link-icon">💼</span>
                            <input type="url" name="linkedin"
                                   value="{{ old('linkedin', $user->linkedin) }}"
                                   class="form-input @error('linkedin') error @enderror"
                                   placeholder="https://linkedin.com/in/username">
                        </div>
                        @error('linkedin')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Portfolio</label>
                        <div class="link-input-wrapper">
                            <span class="link-icon">🌐</span>
                            <input type="url" name="portfolio"
                                   value="{{ old('portfolio', $user->portfolio) }}"
                                   class="form-input @error('portfolio') error @enderror"
                                   placeholder="https://monsite.com">
                        </div>
                        @error('portfolio')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-secondary w-full justify-center mt-5">
                        💾 Sauvegarder les liens
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- COLONNE DROITE : Infos personnelles --}}
    <div class="lg:col-span-2 space-y-5 anim d3">

        {{-- Alertes --}}
        @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 rounded-2xl"
             style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2)">
            <span class="text-xl">🎉</span>
            <p class="text-sm font-semibold" style="color:#1a8a47">{{ session('success') }}</p>
        </div>
        @endif

        @if($errors->any())
        <div class="px-5 py-4 rounded-2xl"
             style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.15)">
            <div class="flex items-center gap-2 mb-2 text-red-600 font-bold text-xs">⚠ Erreurs de saisie</div>
            @foreach($errors->all() as $error)
            <p class="text-xs text-red-500">• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        {{-- Informations personnelles --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(232,184,75,0.1)">👤</div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Informations personnelles</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Votre identité sur MboaAcademy</p>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PATCH')

                    {{-- Prénom / Nom --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div class="form-group" style="margin-bottom:0">
                            <label class="form-label" for="first_name">Prénom</label>
                            <input type="text" id="first_name" name="first_name"
                                   value="{{ old('first_name', $user->first_name) }}"
                                   class="form-input @error('first_name') error @enderror"
                                   placeholder="Votre prénom" required>
                            @error('first_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label class="form-label" for="last_name">Nom</label>
                            <input type="text" id="last_name" name="last_name"
                                   value="{{ old('last_name', $user->last_name) }}"
                                   class="form-input @error('last_name') error @enderror"
                                   placeholder="Votre nom" required>
                            @error('last_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Email (lecture seule) --}}
                    <div class="form-group">
                        <label class="form-label">Adresse e-mail</label>
                        <div class="relative">
                            <input type="email" value="{{ $user->email }}"
                                   class="form-input" disabled
                                   style="background:rgba(0,0,0,0.03);color:#9ca3af;cursor:not-allowed">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs px-2 py-0.5 rounded-lg"
                                  style="background:rgba(0,0,0,0.06);color:#9ca3af">Non modifiable</span>
                        </div>
                    </div>

                    {{-- Bio --}}
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="bio">
                            Biographie
                            <span class="ml-1 normal-case tracking-normal font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <textarea id="bio" name="bio"
                                  class="form-input @error('bio') error @enderror"
                                  placeholder="Parlez un peu de vous — votre parcours, vos passions, vos objectifs…"
                                  maxlength="500"
                                  x-data="{ count: {{ strlen(old('bio', $user->bio ?? '')) }} }"
                                  @input="count = $event.target.value.length">{{ old('bio', $user->bio) }}</textarea>
                        <div class="text-right text-[10px] text-gray-400 mt-1"
                             x-data="{ count: {{ strlen(old('bio', $user->bio ?? '')) }} }"
                             @input.window="count = document.getElementById('bio').value.length">
                            <span x-text="count"></span> / 500
                        </div>
                        @error('bio')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex gap-3 pt-5 mt-2 border-t border-black/5">
                        <button type="submit" class="btn-primary">
                            ✅ Enregistrer les modifications
                        </button>
                        <a href="{{ route('student.dashboard') }}" class="btn-secondary">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sécurité --}}
        <div class="section-card anim d4">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(239,68,68,0.08)">🔒</div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Sécurité</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Modifier votre mot de passe</p>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="form-group" style="margin-bottom:0">
                            <label class="form-label">Mot de passe actuel</label>
                            <input type="password" name="current_password"
                                   class="form-input @error('current_password') error @enderror"
                                   placeholder="••••••••">
                            @error('current_password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label class="form-label">Nouveau</label>
                            <input type="password" name="password"
                                   class="form-input @error('password') error @enderror"
                                   placeholder="••••••••">
                            @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label class="form-label">Confirmer</label>
                            <input type="password" name="password_confirmation"
                                   class="form-input"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="pt-5 mt-2 border-t border-black/5">
                        <button type="submit" class="btn-secondary">
                            🔑 Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection