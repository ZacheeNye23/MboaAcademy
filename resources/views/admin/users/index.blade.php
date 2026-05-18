@extends('admin.layouts.app')

@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des utilisateurs')
@section('page-subtitle', number_format($users->total()) . ' utilisateurs au total')

@section('topbar-actions')
<a href="{{ route('admin.users.create') }}"
   class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
   style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 4px 14px rgba(37,194,110,0.3)">
    ➕ Nouvel utilisateur
</a>
@endsection

@push('styles')
<style>
    /* ── Barre recherche ── */
    .search-bar {
        background: rgba(255,255,255,0.04);
        border: 1.5px solid rgba(255,255,255,0.08);
        border-radius: 14px; padding: 10px 16px 10px 42px;
        color: #fff; font-family:'Outfit',sans-serif; font-size:.875rem;
        outline: none; transition: all .2s; width: 100%;
    }
    .search-bar::placeholder { color: rgba(255,255,255,0.25); }
    .search-bar:focus { border-color: rgba(232,184,75,0.4); background: rgba(255,255,255,0.06); }

    /* ── Filter tabs ── */
    .f-tab { padding:7px 16px;border-radius:100px;font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap; }
    .f-tab.on  { background:rgba(232,184,75,0.15);color:#e8b84b;border:1px solid rgba(232,184,75,0.25); }
    .f-tab.off { background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.45);border:1px solid rgba(255,255,255,0.08); }
    .f-tab.off:hover { color:rgba(255,255,255,0.75);border-color:rgba(255,255,255,0.15); }

    /* ── Table ── */
    .user-row {
        display:flex;align-items:center;gap:14px;
        padding:14px 20px;border-bottom:1px solid rgba(255,255,255,0.04);
        transition:background .15s;
    }
    .user-row:hover { background:rgba(255,255,255,0.02); }
    .user-row:last-child { border-bottom:none; }

    /* ── Avatar ── */
    .u-avatar {
        width:38px;height:38px;border-radius:50%;
        display:flex;align-items:center;justify-content:center;
        font-size:.75rem;font-weight:700;color:#fff;flex-shrink:0;
    }

    /* ── Status dot ── */
    .status-dot { width:7px;height:7px;border-radius:50%;flex-shrink:0; }
    .status-active   { background:#25c26e; }
    .status-inactive { background:#f87171; }

    /* ── Role badge ── */
    .role-badge {
        display:inline-flex;align-items:center;gap:4px;
        padding:2px 9px;border-radius:100px;
        font-size:.65rem;font-weight:700;letter-spacing:.3px;
    }
    .role-student { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
    .role-teacher { background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }
    .role-admin   { background:rgba(167,139,250,0.12);color:#a78bfa;border:1px solid rgba(167,139,250,0.2); }

    /* ── Action btns ── */
    .action-btn {
        padding:5px 12px;border-radius:8px;font-size:.72rem;font-weight:600;
        cursor:pointer;transition:all .2s;border:none;text-decoration:none;
        display:inline-flex;align-items:center;gap:4px;
    }
    .btn-view   { background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.6); }
    .btn-view:hover   { background:rgba(255,255,255,0.1);color:#fff; }
    .btn-toggle-on    { background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.15); }
    .btn-toggle-on:hover { background:rgba(239,68,68,0.18); }
    .btn-toggle-off   { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.15); }
    .btn-toggle-off:hover { background:rgba(37,194,110,0.18); }
    .btn-delete { background:rgba(239,68,68,0.08);color:#f87171; }
    .btn-delete:hover { background:rgba(239,68,68,0.15); }

    /* ── Checkbox select ── */
    .user-check {
        appearance:none;width:16px;height:16px;border-radius:4px;
        border:1.5px solid rgba(255,255,255,0.15);background:transparent;
        cursor:pointer;transition:all .2s;flex-shrink:0;
    }
    .user-check:checked { background:#e8b84b;border-color:#e8b84b; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}.d3{animation-delay:.12s}

    [x-cloak] { display:none !important; }
</style>
@endpush

@section('content')

{{-- ── STATS RAPIDES ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 anim d1">
    @foreach([
        ['🎓', 'Apprenants',  $stats['students'], '#25c26e'],
        ['📖', 'Formateurs', $stats['teachers'], '#e8b84b'],
        ['🛡️', 'Admins',      $stats['admins'],   '#a78bfa'],
        ['🚫', 'Désactivés', $stats['inactive'],  '#f87171'],
    ] as [$icon, $label, $count, $color])
    <div class="glass p-4 text-center">
        <div class="text-2xl mb-1">{{ $icon }}</div>
        <div class="text-xl font-bold mb-0.5" style="font-family:'Playfair Display',serif;color:{{ $color }}">
            {{ number_format($count) }}
        </div>
        <div class="text-xs" style="color:rgba(255,255,255,0.4)">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- ── FILTRES + RECHERCHE ── --}}
<div class="glass p-5 mb-5 anim d2">
    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Recherche --}}
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm" style="color:rgba(255,255,255,0.3)">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="search-bar" placeholder="Nom, email, pays...">
            </div>

            {{-- Rôle --}}
            <select name="role" onchange="this.form.submit()"
                    class="search-bar px-4" style="width:auto;cursor:pointer">
                <option value="" style="background:#040a05" {{ !request('role') ? 'selected' : '' }}>Tous les rôles</option>
                <option value="student" style="background:#040a05" {{ request('role')==='student' ? 'selected' : '' }}>🎓 Apprenants</option>
                <option value="teacher" style="background:#040a05" {{ request('role')==='teacher' ? 'selected' : '' }}>📖 Formateurs</option>
                <option value="admin"   style="background:#040a05" {{ request('role')==='admin'   ? 'selected' : '' }}>🛡️ Admins</option>
            </select>

            {{-- Statut --}}
            <select name="status" onchange="this.form.submit()"
                    class="search-bar px-4" style="width:auto;cursor:pointer">
                <option value="" style="background:#040a05" {{ !request('status') ? 'selected' : '' }}>Tous les statuts</option>
                <option value="active"   style="background:#040a05" {{ request('status')==='active'   ? 'selected' : '' }}>✅ Actifs</option>
                <option value="inactive" style="background:#040a05" {{ request('status')==='inactive' ? 'selected' : '' }}>🚫 Désactivés</option>
            </select>

            {{-- Trier --}}
            <select name="sort" onchange="this.form.submit()"
                    class="search-bar px-4" style="width:auto;cursor:pointer">
                <option value="latest"    style="background:#040a05" {{ request('sort','latest')==='latest'    ? 'selected' : '' }}>Plus récents</option>
                <option value="oldest"    style="background:#040a05" {{ request('sort')==='oldest'    ? 'selected' : '' }}>Plus anciens</option>
                <option value="name"      style="background:#040a05" {{ request('sort')==='name'      ? 'selected' : '' }}>Nom A→Z</option>
                <option value="enrollments" style="background:#040a05" {{ request('sort')==='enrollments' ? 'selected' : '' }}>+ d'inscriptions</option>
            </select>

            <button type="submit"
                    class="px-5 py-2 rounded-xl text-sm font-semibold text-white shrink-0"
                    style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                Filtrer
            </button>

            @if(request()->hasAny(['search','role','status','sort']))
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-medium shrink-0"
               style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
                ✕ Réinitialiser
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ── TABLEAU UTILISATEURS ── --}}
<div class="glass overflow-hidden anim d3" x-data="{ selected: [], selectAll: false }">

    {{-- En-tête table --}}
    <div class="flex items-center gap-4 px-5 py-3 border-b border-white/5"
         style="color:rgba(255,255,255,0.2);font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08rem">
        <input type="checkbox" class="user-check" x-model="selectAll"
               @change="selectAll ? selected = {{ json_encode($users->pluck('id')) }} : selected = []">
        <span class="flex-1">Utilisateur</span>
        <span class="w-24 hidden md:block">Rôle</span>
        <span class="w-28 hidden lg:block">Pays</span>
        <span class="w-28 hidden xl:block">Inscrit le</span>
        <span class="w-20 hidden lg:block text-center">Statut</span>
        <span class="w-32 text-right">Actions</span>
    </div>

    {{-- Lignes utilisateurs --}}
    @forelse($users as $user)
    @php
        $avatarColors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e','#0f766e','#b45309'];
        $avatarBg = $avatarColors[$user->id % count($avatarColors)];
    @endphp

    <div class="user-row">
        {{-- Checkbox --}}
        <input type="checkbox" class="user-check" :value="{{ $user->id }}" x-model="selected">

        {{-- Avatar + Info --}}
        <div class="flex-1 flex items-center gap-3 min-w-0">
            <div class="u-avatar shrink-0" style="background:{{ $avatarBg }}">
                {{ $user->initials }}
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-white truncate">{{ $user->full_name }}</span>
                    @if(!$user->is_active)
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold" style="background:rgba(239,68,68,0.1);color:#f87171">Désactivé</span>
                    @endif
                </div>
                <div class="text-xs truncate" style="color:rgba(255,255,255,0.35)">{{ $user->email }}</div>
            </div>
        </div>

        {{-- Rôle --}}
        <div class="w-24 hidden md:block">
            <span class="role-badge role-{{ $user->role }}">
                {{ ['student'=>'🎓 Apprenant','teacher'=>'📖 Formateur','admin'=>'🛡️ Admin'][$user->role] ?? $user->role }}
            </span>
        </div>

        {{-- Pays --}}
        <div class="w-28 hidden lg:block">
            @php $flags = ['CM'=>'🇨🇲','SN'=>'🇸🇳','CI'=>'🇨🇮','GH'=>'🇬🇭','NG'=>'🇳🇬','BJ'=>'🇧🇯']; @endphp
            <span class="text-xs" style="color:rgba(255,255,255,0.5)">
                {{ $flags[$user->country] ?? '🌍' }} {{ $user->country ?? '—' }}
            </span>
        </div>

        {{-- Date inscription --}}
        <div class="w-28 hidden xl:block">
            <span class="text-xs" style="color:rgba(255,255,255,0.35)">
                {{ $user->created_at->translatedFormat('d M Y') }}
            </span>
        </div>

        {{-- Statut --}}
        <div class="w-20 hidden lg:flex justify-center">
            <div class="flex items-center gap-1.5">
                <div class="status-dot {{ $user->is_active ? 'status-active' : 'status-inactive' }}"></div>
                <span class="text-xs" style="color:{{ $user->is_active ? '#25c26e' : '#f87171' }}">
                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                </span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="w-32 flex items-center justify-end gap-1.5">
            <a href="{{ route('admin.users.show', $user) }}" class="action-btn btn-view">
                👁
            </a>
            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline">
                @csrf @method('PATCH')
                <button type="submit"
                        class="action-btn {{ $user->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}"
                        title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                    {{ $user->is_active ? '🚫' : '✅' }}
                </button>
            </form>
            @if($user->id !== auth()->id())
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  onsubmit="return confirm('Supprimer {{ $user->full_name }} ? Cette action est irréversible.')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn btn-delete" title="Supprimer">🗑</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="text-4xl mb-3">👥</div>
        <p class="text-sm" style="color:rgba(255,255,255,0.35)">Aucun utilisateur trouvé.</p>
        @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.users.index') }}" class="mt-3 text-xs font-semibold" style="color:#e8b84b">
            Effacer les filtres
        </a>
        @endif
    </div>
    @endforelse

    {{-- Barre actions groupées --}}
    <div x-show="selected.length > 0" x-cloak
         class="flex items-center gap-4 px-5 py-3 border-t border-white/5"
         style="background:rgba(232,184,75,0.05)">
        <span class="text-xs font-semibold" style="color:#e8b84b">
            <span x-text="selected.length"></span> sélectionné(s)
        </span>
        <form method="POST" action="{{ route('admin.users.bulk-toggle') }}" class="inline">
            @csrf
            <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
            <button type="submit" class="action-btn btn-toggle-off">✅ Activer tous</button>
        </form>
        <form method="POST" action="{{ route('admin.users.bulk-delete') }}"
              onsubmit="return confirm('Supprimer les utilisateurs sélectionnés ?')" class="inline">
            @csrf @method('DELETE')
            <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
            <button type="submit" class="action-btn btn-delete">🗑 Supprimer</button>
        </form>
    </div>

    {{-- Pagination + infos --}}
    <div class="flex items-center justify-between px-5 py-4 border-t border-white/5">
        <p class="text-xs" style="color:rgba(255,255,255,0.3)">
            {{ $users->firstItem() }}–{{ $users->lastItem() }} sur {{ $users->total() }} utilisateurs
        </p>
        {{ $users->withQueryString()->links() }}
    </div>
</div>

@endsection