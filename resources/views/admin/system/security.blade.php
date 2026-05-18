@extends('admin.layouts.app')

@section('title', 'Sécurité & Logs')
@section('page-title', 'Sécurité & Logs')
@section('page-subtitle', 'Surveillance et historique des activités')

@section('topbar-actions')
<form method="POST" action="{{ route('admin.security.logs.clear') }}"
      onsubmit="return confirm('Supprimer les logs de plus de 30 jours ?')">
    @csrf @method('DELETE')
    <input type="hidden" name="days" value="30">
    <button type="submit"
            class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold"
            style="background:rgba(239,68,68,0.1);color:#f87171">
        🗑 Purger les logs (30j)
    </button>
</form>
@endsection

@push('styles')
<style>
    .log-row { display:flex;align-items:flex-start;gap:12px;padding:13px 22px;border-bottom:1px solid rgba(255,255,255,0.04);transition:background .2s; }
    .log-row:hover { background:rgba(255,255,255,0.02); }
    .log-row:last-child { border-bottom:none; }
    .status-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:4px; }
    .filter-btn { padding:5px 12px;border-radius:100px;font-size:.73rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;border:none;font-family:'Outfit',sans-serif; }
    .filter-btn.on  { background:#e8b84b;color:#0a1a0f; }
    .filter-btn.off { background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.4);border:1px solid rgba(255,255,255,0.07); }
    .filter-btn.off:hover { border-color:rgba(232,184,75,0.3);color:#e8b84b; }
    .search-input { background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);border-radius:12px;padding:7px 14px 7px 36px;color:#fff;font-family:'Outfit',sans-serif;font-size:.8rem;outline:none;transition:all .2s;width:220px; }
    .search-input::placeholder { color:rgba(255,255,255,0.2); }
    .search-input:focus { border-color:rgba(232,184,75,0.3); }
    .tab-btn { padding:8px 18px;border-radius:12px;font-size:.8rem;font-weight:600;cursor:pointer;transition:all .2s;border:none;font-family:'Outfit',sans-serif; }
    .tab-btn.on  { background:#e8b84b;color:#0a1a0f; }
    .tab-btn.off { background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.45);border:1px solid rgba(255,255,255,0.08); }
    .tab-btn.off:hover { border-color:rgba(232,184,75,0.25);color:#e8b84b; }
    .action-chip { display:inline-flex;align-items:center;padding:2px 8px;border-radius:6px;font-size:.65rem;font-weight:700;font-family:'Outfit',sans-serif; }
    [x-cloak]{display:none!important}
</style>
@endpush

@section('content')

{{-- ── KPI SÉCURITÉ ──────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['🔐','Connexions totales',   $stats['total_logins'],  '#25c26e'],
        ['❌','Échecs connexion',     $stats['failed_logins'], '#f87171'],
        ['🚫','Utilisateurs bannis',  $stats['banned_users'],  '#e8b84b'],
        ['⚠️', 'IPs suspectes (24h)', $stats['suspicious_ips'],'#f87171'],
    ] as [$icon,$label,$val,$color])
    <div class="glass p-5 anim d{{ $loop->iteration }}">
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl">{{ $icon }}</span>
            @if($val > 0 && in_array($label, ['Échecs connexion','Utilisateurs bannis','IPs suspectes (24h)']))
            <span class="pill pill-red">⚠</span>
            @endif
        </div>
        <div class="text-3xl font-bold" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ number_format($val) }}</div>
        <div class="text-xs mt-1" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    @foreach([
        ['📊','Actions admin auj.', $stats['admin_today'],  '#a78bfa'],
        ['🛡️','Actions totales',    $stats['admin_actions'],'#a78bfa'],
        ['🕐','Connexions auj.',    $stats['logins_today'], '#25c26e'],
    ] as [$icon,$label,$val,$color])
    <div class="glass p-5 anim d{{ $loop->iteration + 4 }}">
        <div class="text-2xl mb-2">{{ $icon }}</div>
        <div class="text-2xl font-bold" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ number_format($val) }}</div>
        <div class="text-xs mt-1" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- ── ONGLETS ──────────────────────────────────────────────────────────── --}}
<div x-data="{ tab: 'logins' }" class="anim d3">

    <div class="flex gap-2 mb-5">
        <button class="tab-btn" :class="tab==='logins'  ? 'on':'off'" @click="tab='logins'">🔐 Journal connexions</button>
        <button class="tab-btn" :class="tab==='actions' ? 'on':'off'" @click="tab='actions'">🛡️ Actions admin</button>
        <button class="tab-btn" :class="tab==='banned'  ? 'on':'off'" @click="tab='banned'">
            🚫 Utilisateurs bannis
            @if($stats['banned_users'] > 0)
            <span class="ml-1.5 pill pill-red" style="font-size:.6rem">{{ $stats['banned_users'] }}</span>
            @endif
        </button>
    </div>

    {{-- ══ JOURNAL CONNEXIONS ══ --}}
    <div x-show="tab==='logins'" x-cloak>

        {{-- Filtre connexions --}}
        <div class="glass p-4 mb-4">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tab" value="logins">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs" style="color:rgba(255,255,255,0.3)">🔍</span>
                    <input type="text" name="login_search" value="{{ request('login_search') }}"
                           class="search-input" placeholder="Email, IP...">
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="login_status" value=""
                            class="filter-btn {{ !request('login_status') ? 'on' : 'off' }}">Tous</button>
                    <button type="submit" name="login_status" value="success"
                            class="filter-btn {{ request('login_status')==='success' ? 'on' : 'off' }}">✅ Succès</button>
                    <button type="submit" name="login_status" value="failed"
                            class="filter-btn {{ request('login_status')==='failed' ? 'on' : 'off' }}">❌ Échecs</button>
                </div>
            </form>
        </div>

        {{-- Table connexions --}}
        <div class="glass overflow-hidden">
            <div class="flex items-center gap-4 px-5 py-3 border-b border-white/5"
                 style="color:rgba(255,255,255,0.22);font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.07rem">
                <div class="w-4 shrink-0"></div>
                <div class="flex-1">Utilisateur</div>
                <div class="w-32 hidden md:block">Adresse IP</div>
                <div class="w-36 hidden lg:block">Navigateur</div>
                <div class="w-36 text-right">Date</div>
            </div>

            @forelse($loginAttempts as $attempt)
            @php $success = $attempt->status === 'success'; @endphp
            <div class="log-row">
                <div class="status-dot" style="background:{{ $success ? '#25c26e' : '#f87171' }}"></div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-white">
                        {{ $attempt->first_name ? $attempt->first_name.' '.$attempt->last_name : 'Inconnu' }}
                    </div>
                    <div class="text-xs" style="color:rgba(255,255,255,0.35)">{{ $attempt->email }}</div>
                </div>
                <div class="w-32 hidden md:block">
                    <span class="text-xs font-mono" style="color:rgba(255,255,255,0.45)">
                        {{ $attempt->ip_address ?? '—' }}
                    </span>
                </div>
                <div class="w-36 hidden lg:block">
                    <span class="text-xs truncate block" style="color:rgba(255,255,255,0.35)">
                        {{ Str::limit($attempt->user_agent ?? '—', 35) }}
                    </span>
                </div>
                <div class="w-36 text-right shrink-0">
                    <div class="text-xs" style="color:rgba(255,255,255,0.3)">
                        {{ \Carbon\Carbon::parse($attempt->created_at)->format('d/m/Y H:i') }}
                    </div>
                    <span class="text-[10px] font-bold" style="color:{{ $success ? '#25c26e' : '#f87171' }}">
                        {{ $success ? '✅ Succès' : '❌ Échec' }}
                    </span>
                </div>
            </div>
            @empty
            <div class="py-12 text-center">
                <p class="text-sm" style="color:rgba(255,255,255,0.3)">Aucun journal de connexion.</p>
            </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $loginAttempts->withQueryString()->links() }}</div>
    </div>

    {{-- ══ ACTIONS ADMIN ══ --}}
    <div x-show="tab==='actions'" x-cloak>

        {{-- Filtre actions --}}
        <div class="glass p-4 mb-4">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tab" value="actions">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs" style="color:rgba(255,255,255,0.3)">🔍</span>
                    <input type="text" name="log_search" value="{{ request('log_search') }}"
                           class="search-input" placeholder="Action, description...">
                </div>
                @if($actionTypes->count() > 0)
                <select name="log_action" onchange="this.form.submit()"
                        style="background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);border-radius:10px;padding:7px 12px;color:rgba(255,255,255,0.6);font-family:'Outfit',sans-serif;font-size:.78rem;outline:none">
                    <option value="">Toutes les actions</option>
                    @foreach($actionTypes as $action)
                    <option value="{{ $action }}" {{ request('log_action')===$action?'selected':'' }}>
                        {{ $action }}
                    </option>
                    @endforeach
                </select>
                @endif
                <button type="submit" class="filter-btn on">🔍 Filtrer</button>
            </form>
        </div>

        {{-- Table actions --}}
        <div class="glass overflow-hidden">
            <div class="flex items-center gap-4 px-5 py-3 border-b border-white/5"
                 style="color:rgba(255,255,255,0.22);font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.07rem">
                <div class="flex-1">Action</div>
                <div class="w-32 hidden md:block">Admin</div>
                <div class="w-28 hidden lg:block">IP</div>
                <div class="w-36 text-right">Date</div>
            </div>

            @forelse($adminLogs as $log)
            @php
                $actionColors = [
                    'approve_course' => ['#25c26e','rgba(37,194,110,0.1)'],
                    'reject_course'  => ['#f87171','rgba(239,68,68,0.1)'],
                    'ban_user'       => ['#f87171','rgba(239,68,68,0.1)'],
                    'unban_user'     => ['#25c26e','rgba(37,194,110,0.1)'],
                    'delete_user'    => ['#f87171','rgba(239,68,68,0.1)'],
                    'update_setting' => ['#e8b84b','rgba(232,184,75,0.1)'],
                ];
                [$chipColor, $chipBg] = $actionColors[$log->action] ?? ['#60a5fa','rgba(96,165,250,0.1)'];
            @endphp
            <div class="log-row">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="action-chip" style="background:{{ $chipBg }};color:{{ $chipColor }}">
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                    </div>
                    <p class="text-xs" style="color:rgba(255,255,255,0.45)">
                        {{ $log->description ?? '—' }}
                    </p>
                </div>
                <div class="w-32 hidden md:block">
                    <div class="text-xs font-medium text-white">
                        {{ $log->admin?->full_name ?? 'Système' }}
                    </div>
                </div>
                <div class="w-28 hidden lg:block">
                    <span class="text-xs font-mono" style="color:rgba(255,255,255,0.35)">
                        {{ $log->ip_address ?? '—' }}
                    </span>
                </div>
                <div class="w-36 text-right shrink-0">
                    <div class="text-xs" style="color:rgba(255,255,255,0.3)">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="text-[10px]" style="color:rgba(255,255,255,0.2)">
                        {{ $log->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center">
                <p class="text-sm" style="color:rgba(255,255,255,0.3)">Aucune action admin enregistrée.</p>
            </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $adminLogs->withQueryString()->links() }}</div>
    </div>

    {{-- ══ UTILISATEURS BANNIS ══ --}}
    <div x-show="tab==='banned'" x-cloak>

        {{-- Recherche --}}
        <div class="glass p-4 mb-4">
            <form method="GET" class="flex items-center gap-3">
                <input type="hidden" name="tab" value="banned">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs" style="color:rgba(255,255,255,0.3)">🔍</span>
                    <input type="text" name="banned_search" value="{{ request('banned_search') }}"
                           class="search-input" placeholder="Nom, email...">
                </div>
                <button type="submit" class="filter-btn on">🔍 Rechercher</button>
            </form>
        </div>

        <div class="glass overflow-hidden">
            <div class="flex items-center gap-4 px-5 py-3 border-b border-white/5"
                 style="color:rgba(255,255,255,0.22);font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.07rem">
                <div class="flex-1">Utilisateur</div>
                <div class="w-24 text-center hidden md:block">Rôle</div>
                <div class="w-24 text-center hidden lg:block">Inscriptions</div>
                <div class="w-28 text-center hidden md:block">Banni le</div>
                <div class="w-28 text-right">Action</div>
            </div>

            @forelse($bannedUsers as $user)
            @php
                $avatarColors = ['#1a8a47','#e8b84b','#3b82f6','#a78bfa','#f87171'];
                $bg = $avatarColors[$user->id % 5];
                $roleLabel = match($user->role) { 'teacher'=>'📖 Formateur','admin'=>'🛡️ Admin',default=>'🎓 Apprenant' };
                $rolePill  = match($user->role) { 'teacher'=>'pill-gold','admin'=>'pill-purple',default=>'pill-green' };
            @endphp
            <div class="log-row">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                         style="background:{{ $bg }};opacity:.7">{{ $user->initials }}</div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-white truncate">{{ $user->full_name }}</div>
                        <div class="text-xs" style="color:rgba(255,255,255,0.35)">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="w-24 text-center hidden md:block">
                    <span class="pill {{ $rolePill }}" style="font-size:.62rem">{{ $roleLabel }}</span>
                </div>
                <div class="w-24 text-center hidden lg:block">
                    <span class="text-sm font-bold text-white">{{ $user->enrollments_count }}</span>
                </div>
                <div class="w-28 text-center hidden md:block">
                    <span class="text-xs" style="color:rgba(255,255,255,0.3)">
                        {{ $user->updated_at->format('d/m/Y') }}
                    </span>
                </div>
                <div class="w-28 flex justify-end">
                    <form method="POST" action="{{ route('admin.security.unban', $user) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                style="background:rgba(37,194,110,0.1);color:#25c26e">
                            ✅ Réactiver
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="py-12 text-center">
                <div class="text-4xl mb-3">✅</div>
                <p class="text-sm" style="color:rgba(255,255,255,0.3)">Aucun utilisateur banni.</p>
            </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $bannedUsers->withQueryString()->links() }}</div>
    </div>

</div>

@endsection