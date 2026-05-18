@extends('admin.layouts.app')

@section('title', isset($user) ? 'Modifier ' . $user->full_name : 'Nouvel utilisateur')
@section('page-title', isset($user) ? 'Modifier l\'utilisateur' : 'Créer un utilisateur')
@section('page-subtitle', isset($user) ? $user->email : 'Ajouter un nouveau membre')

@push('styles')
<style>
    .form-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:20px;padding:28px; }
    .form-group { margin-bottom:20px; }
    .form-label { display:block;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.4);margin-bottom:8px; }
    .form-input {
        width:100%;background:rgba(255,255,255,0.04);
        border:1.5px solid rgba(255,255,255,0.08);
        border-radius:12px;padding:11px 16px;
        color:#fff;font-family:'Outfit',sans-serif;font-size:.9rem;
        outline:none;transition:all .2s;
    }
    .form-input:focus { border-color:rgba(232,184,75,0.4);background:rgba(255,255,255,0.06);box-shadow:0 0 0 3px rgba(232,184,75,0.08); }
    .form-input::placeholder { color:rgba(255,255,255,0.2); }
    .form-input.error { border-color:rgba(239,68,68,0.5); }

    .role-card {
        border:2px solid rgba(255,255,255,0.08);border-radius:14px;
        padding:16px;cursor:pointer;transition:all .2s;text-align:center;
    }
    .role-card:hover { border-color:rgba(255,255,255,0.2); }
    .role-card.selected-student { border-color:#25c26e;background:rgba(37,194,110,0.08); }
    .role-card.selected-teacher { border-color:#e8b84b;background:rgba(232,184,75,0.08); }
    .role-card.selected-admin   { border-color:#a78bfa;background:rgba(167,139,250,0.08); }
</style>
@endpush

@section('content')

<div class="max-w-2xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs mb-6" style="color:rgba(255,255,255,0.35)">
        <a href="{{ route('admin.users.index') }}" class="hover:text-white transition-colors">← Utilisateurs</a>
        @if(isset($user))
        <span>/</span>
        <a href="{{ route('admin.users.show', $user) }}" class="hover:text-white transition-colors">{{ $user->full_name }}</a>
        @endif
        <span>/</span>
        <span class="text-white">{{ isset($user) ? 'Modifier' : 'Créer' }}</span>
    </div>

    {{-- Erreurs --}}
    @if($errors->any())
    <div class="mb-5 p-4 rounded-xl" style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.18)">
        <div class="text-xs font-bold mb-2" style="color:#f87171">⚠ Erreurs de validation</div>
        @foreach($errors->all() as $error)
        <p class="text-xs" style="color:rgba(252,165,165,0.9)">• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST"
          action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
          x-data="{ role: '{{ old('role', $user->role ?? 'student') }}', showPassword: false }">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="form-card mb-5">
            <h3 class="text-sm font-bold text-white mb-5" style="font-family:'Playfair Display',serif">
                👤 Informations personnelles
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}"
                           class="form-input {{ $errors->has('first_name') ? 'error' : '' }}"
                           placeholder="Jean" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}"
                           class="form-input {{ $errors->has('last_name') ? 'error' : '' }}"
                           placeholder="Dupont" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                       class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                       placeholder="jean@exemple.com" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                           class="form-input" placeholder="+237 6XX XXX XXX">
                </div>
                <div class="form-group">
                    <label class="form-label">Pays</label>
                    <select name="country" class="form-input" style="cursor:pointer">
                        @foreach(['CM'=>'🇨🇲 Cameroun','SN'=>'🇸🇳 Sénégal','CI'=>'🇨🇮 Côte d\'Ivoire','GH'=>'🇬🇭 Ghana','NG'=>'🇳🇬 Nigeria','BJ'=>'🇧🇯 Bénin','TG'=>'🇹🇬 Togo','ML'=>'🇲🇱 Mali','BF'=>'🇧🇫 Burkina Faso','CD'=>'🇨🇩 Congo RDC'] as $code => $name)
                        <option value="{{ $code }}" style="background:#040a05"
                                {{ old('country', $user->country ?? 'CM') === $code ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group mb-0">
                <label class="form-label">Bio</label>
                <textarea name="bio" rows="3" class="form-input"
                          placeholder="Description courte...">{{ old('bio', $user->bio ?? '') }}</textarea>
            </div>
        </div>

        {{-- Rôle --}}
        <div class="form-card mb-5">
            <h3 class="text-sm font-bold text-white mb-5" style="font-family:'Playfair Display',serif">
                🎭 Rôle
            </h3>
            <div class="grid grid-cols-3 gap-3">
                @foreach([
                    ['student','🎓','Apprenant','Accès aux cours','#25c26e'],
                    ['teacher','📖','Formateur','Crée des cours','#e8b84b'],
                    ['admin',  '🛡️','Admin',    'Accès total',   '#a78bfa'],
                ] as [$val, $icon, $label, $desc, $color])
                <label class="role-card" :class="role === '{{ $val }}' ? 'selected-{{ $val }}' : ''"
                       @click="role = '{{ $val }}'">
                    <input type="radio" name="role" value="{{ $val }}" x-model="role" class="sr-only">
                    <div class="text-2xl mb-2">{{ $icon }}</div>
                    <div class="text-sm font-bold text-white">{{ $label }}</div>
                    <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.4)">{{ $desc }}</div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Mot de passe --}}
        <div class="form-card mb-5">
            <h3 class="text-sm font-bold text-white mb-5" style="font-family:'Playfair Display',serif">
                🔒 {{ isset($user) ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe *' }}
            </h3>
            @if(isset($user))
            <p class="text-xs mb-4" style="color:rgba(255,255,255,0.35)">
                Laissez vide pour conserver le mot de passe actuel.
            </p>
            @endif
            <div class="relative">
                <input :type="showPassword ? 'text' : 'password'"
                       name="password" class="form-input pr-12"
                       placeholder="{{ isset($user) ? 'Nouveau mot de passe...' : 'Min. 8 caractères' }}"
                       {{ isset($user) ? '' : 'required' }}>
                <button type="button" @click="showPassword = !showPassword"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-sm"
                        style="color:rgba(255,255,255,0.3)">
                    <span x-text="showPassword ? '🙈' : '👁'"></span>
                </button>
            </div>
            @if(!isset($user))
            <div class="relative mt-3">
                <input :type="showPassword ? 'text' : 'password'"
                       name="password_confirmation" class="form-input pr-12"
                       placeholder="Confirmer le mot de passe" required>
            </div>
            @endif
        </div>

        {{-- Statut --}}
        <div class="form-card mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-white mb-1">Compte actif</div>
                    <div class="text-xs" style="color:rgba(255,255,255,0.35)">
                        L'utilisateur peut se connecter et utiliser la plateforme.
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                           {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 rounded-full peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                         style="background:rgba(255,255,255,0.1)" x-bind:style="$el.previousElementSibling.checked ? 'background:#1a8a47' : 'background:rgba(255,255,255,0.1)'">
                    </div>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
                    style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 4px 14px rgba(37,194,110,0.3)">
                {{ isset($user) ? '✓ Mettre à jour' : '➕ Créer l\'utilisateur' }}
            </button>
            <a href="{{ isset($user) ? route('admin.users.show', $user) : route('admin.users.index') }}"
               class="px-5 py-3 rounded-xl text-sm font-semibold transition-colors"
               style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
                Annuler
            </a>
        </div>
    </form>
</div>

@endsection