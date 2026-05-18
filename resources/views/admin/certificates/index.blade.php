@extends('admin.layouts.app')

@section('title', 'Certificats')
@section('page-title', 'Gestion des Certificats')
@section('page-subtitle', number_format($certificates->total()) . ' certificat(s) délivré(s)')

@push('styles')
<style>
    /* ── Recherche ── */
    .search-bar {
        background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);
        border-radius:12px;padding:10px 16px 10px 42px;color:#fff;
        font-family:'Outfit',sans-serif;font-size:.875rem;outline:none;
        transition:all .2s;width:100%;
    }
    .search-bar::placeholder { color:rgba(255,255,255,0.22); }
    .search-bar:focus { border-color:rgba(232,184,75,0.45);background:rgba(255,255,255,0.06); }

    .f-select {
        background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);
        border-radius:12px;padding:10px 14px;color:rgba(255,255,255,0.7);
        font-family:'Outfit',sans-serif;font-size:.8rem;outline:none;cursor:pointer;
    }

    /* ── Certificat row ── */
    .cert-row {
        display:flex;align-items:center;gap:14px;
        padding:14px 20px;border-bottom:1px solid rgba(255,255,255,0.04);
        transition:background .15s;
    }
    .cert-row:hover { background:rgba(255,255,255,0.02); }
    .cert-row:last-child { border-bottom:none; }

    /* ── Miniature certificat ── */
    .cert-thumb {
        width:52px;height:38px;border-radius:6px;flex-shrink:0;
        background:linear-gradient(135deg,#faf6ef,#f0ebe0);
        display:flex;align-items:center;justify-content:center;
        border:1px solid rgba(232,184,75,0.3);
        position:relative;overflow:hidden;
        font-size:1.1rem;
    }
    .cert-thumb::before {
        content:'';position:absolute;inset:3px;
        border:1px solid rgba(232,184,75,0.2);border-radius:3px;
    }

    /* ── Avatar ── */
    .u-avatar {
        width:34px;height:34px;border-radius:50%;
        display:flex;align-items:center;justify-content:center;
        font-size:.7rem;font-weight:700;color:#fff;flex-shrink:0;
    }

    /* ── Action btns ── */
    .act-btn {
        display:inline-flex;align-items:center;gap:4px;
        padding:5px 11px;border-radius:8px;font-size:.72rem;font-weight:600;
        cursor:pointer;transition:all .2s;border:none;text-decoration:none;
    }
    .act-view   { background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.18); }
    .act-view:hover   { background:rgba(232,184,75,0.18); }
    .act-revoke { background:rgba(239,68,68,0.08);color:#f87171;border:1px solid rgba(239,68,68,0.15); }
    .act-revoke:hover { background:rgba(239,68,68,0.15); }

    /* ── New badge ── */
    .new-dot { width:7px;height:7px;border-radius:50%;background:#25c26e;flex-shrink:0;animation:pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }

    /* ── Prog bar ── */
    .prog-bar  { height:3px;border-radius:2px;background:rgba(255,255,255,0.07);overflow:hidden; }
    .prog-fill { height:100%;border-radius:2px;background:linear-gradient(90deg,#b8860b,#e8b84b); }

    @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}.d3{animation-delay:.12s}
    .d4{animation-delay:.16s}
</style>
@endpush

@section('content')

{{-- ── KPI CARDS ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 anim d1">
    @foreach([
        ['🏆', 'Total délivrés',    $stats['total'],         'tous temps',                '#e8b84b'],
        ['📅', 'Ce mois',           $stats['this_month'],    'nouveaux certificats',      '#25c26e'],
        ['📚', 'Cours certifiés',   $stats['unique_courses'],'cours avec certificats',    '#3b82f6'],
        ['👥', 'Apprenants certifiés',$stats['unique_users'],'ont au moins 1 certificat', '#a78bfa'],
    ] as [$icon, $label, $val, $sub, $color])
    <div class="glass p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background:{{ $color }}15">{{ $icon }}</div>
            <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background:{{ $color }}10;color:{{ $color }}">{{ $sub }}</span>
        </div>
        <div class="text-2xl font-bold mb-1" style="font-family:'Playfair Display',serif;color:{{ $color }}">
            {{ number_format($val) }}
        </div>
        <div class="text-xs" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-5 mb-5">

    {{-- ── TOP COURS PAR CERTIFICATS ── --}}
    <div class="glass p-5 anim d2">
        <h3 class="text-sm font-bold text-white mb-4" style="font-family:'Playfair Display',serif">
            🏅 Top cours certifiés
        </h3>
        @php $maxCerts = $topCourses->max('certs_count') ?: 1; @endphp
        <div class="space-y-3">
            @forelse($topCourses as $course)
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="truncate max-w-[130px]" style="color:rgba(255,255,255,0.6)">
                        {{ Str::limit($course->title, 22) }}
                    </span>
                    <span class="font-bold shrink-0 ml-2" style="color:#e8b84b">{{ $course->certs_count }}</span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" style="width:{{ round($course->certs_count / $maxCerts * 100) }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucun certificat encore.</p>
            @endforelse
        </div>

        {{-- Émissions par mois (mini graphe texte) ── --}}
        <div class="mt-5 pt-4 border-t border-white/5">
            <h4 class="text-xs font-bold mb-3" style="color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:.06rem">
                Émissions récentes
            </h4>
            <div class="flex items-end gap-1 h-10">
                @php $maxM = max(array_values($monthlyStats) ?: [1]); @endphp
                @foreach($monthlyStats as $month => $count)
                <div class="flex-1 rounded-t-sm transition-all"
                     style="height:{{ $maxM > 0 ? max(4, round($count/$maxM*100)).'%' : '4%' }};
                            background:{{ $month == now()->month ? '#e8b84b' : 'rgba(232,184,75,0.2)' }}"
                     title="{{ $count }} en mois {{ $month }}">
                </div>
                @endforeach
            </div>
            <div class="flex justify-between text-[9px] mt-1" style="color:rgba(255,255,255,0.2)">
                <span>Jan</span><span>Avr</span><span>Juil</span><span>Oct</span><span>Déc</span>
            </div>
        </div>
    </div>

    {{-- ── TABLEAU PRINCIPAL ── --}}
    <div class="lg:col-span-3 glass overflow-hidden anim d2">

        {{-- Filtres --}}
        <div class="p-4 border-b border-white/5">
            <form method="GET" action="{{ route('admin.certificates.index') }}">
                <div class="flex gap-3 flex-wrap">
                    <div class="relative flex-1 min-w-48">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm" style="color:rgba(255,255,255,0.28)">🔍</span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="search-bar" placeholder="Nom, email, numéro, cours...">
                    </div>
                    <select name="course_id" class="f-select" onchange="this.form.submit()">
                        <option value="" style="background:#040a05">Tous les cours</option>
                        @foreach($coursesList as $c)
                        <option value="{{ $c->id }}" style="background:#040a05" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                            {{ Str::limit($c->title, 40) }}
                        </option>
                        @endforeach
                    </select>
                    <select name="period" class="f-select" onchange="this.form.submit()">
                        <option value=""    style="background:#040a05" {{ !request('period')   ? 'selected' : '' }}>Toute période</option>
                        <option value="7"   style="background:#040a05" {{ request('period')==='7'   ? 'selected' : '' }}>7 derniers jours</option>
                        <option value="30"  style="background:#040a05" {{ request('period')==='30'  ? 'selected' : '' }}>30 derniers jours</option>
                        <option value="90"  style="background:#040a05" {{ request('period')==='90'  ? 'selected' : '' }}>90 derniers jours</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white shrink-0"
                            style="background:linear-gradient(135deg,#b8860b,#e8b84b);color:#0a1a0f">
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search','course_id','period']))
                    <a href="{{ route('admin.certificates.index') }}"
                       class="px-3 py-2 rounded-xl text-sm font-medium shrink-0"
                       style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.4)">✕</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- En-tête colonnes --}}
        <div class="flex items-center gap-4 px-5 py-2.5 border-b border-white/5"
             style="color:rgba(255,255,255,0.2);font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08rem">
            <span style="width:52px;flex-shrink:0"></span>
            <span class="flex-1">Apprenant & Cours</span>
            <span class="w-36 hidden md:block">N° Certificat</span>
            <span class="w-28 hidden lg:block text-center">Délivré le</span>
            <span class="w-24 text-right">Actions</span>
        </div>

        {{-- Lignes --}}
        @forelse($certificates as $cert)
        @php
            $ac     = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e'];
            $isNew  = $cert->issued_at->gt(now()->subDays(7));
        @endphp

        <div class="cert-row">

            {{-- Miniature --}}
            <div class="cert-thumb">🎓</div>

            {{-- Apprenant + Cours --}}
            <div class="flex-1 flex items-center gap-3 min-w-0">
                <div class="u-avatar shrink-0" style="background:{{ $ac[$cert->user_id % count($ac)] }}">
                    {{ $cert->user->initials }}
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-white truncate">{{ $cert->user->full_name }}</span>
                        @if($isNew)<div class="new-dot shrink-0"></div>@endif
                    </div>
                    <div class="text-xs truncate" style="color:rgba(255,255,255,0.35)">
                        📚 {{ Str::limit($cert->course->title, 40) }}
                    </div>
                </div>
            </div>

            {{-- Numéro certificat --}}
            <div class="w-36 hidden md:block">
                <span class="font-mono text-xs" style="color:rgba(232,184,75,0.7)">
                    {{ $cert->certificate_number }}
                </span>
            </div>

            {{-- Date --}}
            <div class="w-28 hidden lg:block text-center">
                <div class="text-xs font-medium" style="color:rgba(255,255,255,0.6)">
                    {{ $cert->issued_at->translatedFormat('d M Y') }}
                </div>
                <div class="text-[10px]" style="color:rgba(255,255,255,0.25)">
                    {{ $cert->issued_at->diffForHumans() }}
                </div>
            </div>

            {{-- Actions --}}
            <div class="w-24 flex items-center justify-end gap-1.5">
                <a href="{{ route('admin.certificates.show', $cert) }}" class="act-btn act-view">
                    👁
                </a>
                <form method="POST" action="{{ route('admin.certificates.destroy', $cert) }}"
                      onsubmit="return confirm('Révoquer le certificat {{ $cert->certificate_number }} ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="act-btn act-revoke" title="Révoquer">🗑</button>
                </form>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="text-5xl mb-3">🏆</div>
            <p class="text-sm" style="color:rgba(255,255,255,0.35)">Aucun certificat trouvé.</p>
            @if(request()->hasAny(['search','course_id','period']))
            <a href="{{ route('admin.certificates.index') }}" class="mt-3 text-xs font-semibold" style="color:#e8b84b">
                Effacer les filtres
            </a>
            @endif
        </div>
        @endforelse

        {{-- Pagination --}}
        <div class="flex items-center justify-between px-5 py-4 border-t border-white/5">
            <p class="text-xs" style="color:rgba(255,255,255,0.3)">
                {{ $certificates->firstItem() }}–{{ $certificates->lastItem() }}
                sur {{ $certificates->total() }} certificats
            </p>
            {{ $certificates->withQueryString()->links() }}
        </div>
    </div>
</div>

@endsection