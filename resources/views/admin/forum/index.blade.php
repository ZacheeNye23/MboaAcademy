@extends('admin.layouts.app')

@section('title', 'Forum — '.$course->title)
@section('page-title', 'Forum · '.$course->title)
@section('page-subtitle', 'Modération des discussions')

@section('topbar-actions')
<a href="{{ route('admin.forum.overview') }}"
   class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-medium transition-colors"
   style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
    ← Tous les forums
</a>
@endsection

@push('styles')
<style>
    .thread-row {
        display:flex; align-items:center; gap:16px;
        padding:16px 24px;
        border-bottom:1px solid rgba(255,255,255,0.04);
        transition:background .2s;
    }
    .thread-row:hover { background:rgba(255,255,255,0.02); }
    .thread-row:last-child { border-bottom:none; }
    .thread-row.pinned { border-left:3px solid rgba(232,184,75,0.4); }
    .thread-row.closed { opacity:.65; }
    .filter-btn {
        padding:6px 14px; border-radius:100px;
        font-size:.75rem; font-weight:600;
        cursor:pointer; transition:all .2s;
        text-decoration:none; white-space:nowrap;
        border:none; font-family:'Outfit',sans-serif;
    }
    .filter-btn.on  { background:#e8b84b; color:#0a1a0f; }
    .filter-btn.off { background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.45);border:1px solid rgba(255,255,255,0.08); }
    .filter-btn.off:hover { border-color:rgba(232,184,75,0.3);color:#e8b84b; }
    .search-input {
        background:rgba(255,255,255,0.04); border:1.5px solid rgba(255,255,255,0.08);
        border-radius:12px; padding:8px 16px 8px 38px; color:#fff;
        font-family:'Outfit',sans-serif; font-size:.875rem; outline:none;
        transition:all .2s; width:260px;
    }
    .search-input::placeholder { color:rgba(255,255,255,0.25); }
    .search-input:focus { border-color:rgba(232,184,75,0.35);background:rgba(255,255,255,0.05); }
    .mod-btn {
        display:inline-flex; align-items:center; gap:4px;
        padding:4px 10px; border-radius:8px;
        font-size:.7rem; font-weight:600;
        cursor:pointer; transition:all .18s;
        text-decoration:none; border:none; font-family:'Outfit',sans-serif;
    }
    .mod-btn-view   { background:rgba(37,194,110,0.1);color:#25c26e; }
    .mod-btn-view:hover { background:rgba(37,194,110,0.2); }
    .mod-btn-pin    { background:rgba(232,184,75,0.1);color:#e8b84b; }
    .mod-btn-pin:hover { background:rgba(232,184,75,0.2); }
    .mod-btn-close  { background:rgba(96,165,250,0.1);color:#60a5fa; }
    .mod-btn-close:hover { background:rgba(96,165,250,0.2); }
    .mod-btn-delete { background:rgba(239,68,68,0.08);color:#f87171; }
    .mod-btn-delete:hover { background:rgba(239,68,68,0.15); }
</style>
@endpush

@section('content')

{{-- Stats du cours --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-7">
    @foreach([
        ['💬','Threads',  $courseStats['total'],   '#e8b84b'],
        ['✅','Résolus',  $courseStats['solved'],   '#25c26e'],
        ['🔒','Fermés',   $courseStats['closed'],   '#60a5fa'],
        ['📌','Épinglés', $courseStats['pinned'],   '#a78bfa'],
        ['↩️', 'Réponses', $courseStats['replies'],  '#25c26e'],
    ] as [$icon,$label,$val,$color])
    <div class="glass p-4 anim d{{ $loop->iteration }}">
        <div class="text-xl mb-2">{{ $icon }}</div>
        <div class="text-2xl font-bold text-white" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
        <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Filtres + recherche --}}
<div class="glass p-4 mb-6 anim d2">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color:rgba(255,255,255,0.3)">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="search-input" placeholder="Rechercher un thread...">
        </div>
        <div class="flex gap-2 flex-wrap">
            @foreach(['all'=>'🗂 Tous','recent'=>'🕐 Récents','pinned'=>'📌 Épinglés','solved'=>'✅ Résolus','unsolved'=>'❓ Non résolus','closed'=>'🔒 Fermés'] as $val=>$label)
            <button type="submit" name="filter" value="{{ $val }}"
                    class="filter-btn {{ request('filter','all')===$val?'on':'off' }}">{{ $label }}</button>
            @endforeach
        </div>
    </form>
</div>

{{-- Table threads --}}
<div class="glass overflow-hidden anim d3">

    {{-- En-tête --}}
    <div class="flex items-center gap-4 px-6 py-3 border-b border-white/5"
         style="color:rgba(255,255,255,0.22);font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07rem">
        <div class="flex-1">Discussion</div>
        <div class="w-28 text-center hidden md:block">Auteur</div>
        <div class="w-20 text-center hidden lg:block">Réponses</div>
        <div class="w-24 text-center hidden lg:block">Vues</div>
        <div class="w-28 text-center hidden md:block">Date</div>
        <div class="w-44 text-right">Actions</div>
    </div>

    @forelse($threads as $thread)
    @php
        $avatarColors = ['#1a8a47','#e8b84b','#3b82f6','#a78bfa','#f87171'];
        $bg = $avatarColors[$thread->author->id % 5];
    @endphp
    <div class="thread-row {{ $thread->is_pinned?'pinned':'' }} {{ $thread->is_closed?'closed':'' }}">

        {{-- Titre + badges --}}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-1.5 mb-1">
                @if($thread->is_pinned)  <span class="pill pill-gold"   style="font-size:.6rem">📌 Épinglé</span>  @endif
                @if($thread->is_solved)  <span class="pill pill-green"  style="font-size:.6rem">✅ Résolu</span>   @endif
                @if($thread->is_closed)  <span class="pill pill-gray"   style="font-size:.6rem">🔒 Fermé</span>   @endif
            </div>
            <a href="{{ route('admin.forum.show', [$course->slug, $thread]) }}"
               class="text-sm font-semibold text-white hover:text-yellow-300 transition-colors truncate block"
               style="font-family:'Playfair Display',serif">
                {{ $thread->title }}
            </a>
            <p class="text-xs mt-0.5 line-clamp-1" style="color:rgba(255,255,255,0.3)">
                {{ Str::limit(strip_tags($thread->body), 100) }}
            </p>
        </div>

        {{-- Auteur --}}
        <div class="w-28 hidden md:flex items-center gap-2 shrink-0">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold text-white shrink-0"
                 style="background:{{ $bg }}">{{ $thread->author->initials }}</div>
            <span class="text-xs truncate" style="color:rgba(255,255,255,0.5)">{{ $thread->author->full_name }}</span>
        </div>

        {{-- Réponses --}}
        <div class="w-20 text-center hidden lg:block">
            <span class="text-sm font-bold text-white">{{ $thread->replies_count }}</span>
        </div>

        {{-- Vues --}}
        <div class="w-24 text-center hidden lg:block">
            <span class="text-sm" style="color:rgba(255,255,255,0.45)">{{ $thread->views ?? 0 }}</span>
        </div>

        {{-- Date --}}
        <div class="w-28 text-center hidden md:block">
            <span class="text-xs" style="color:rgba(255,255,255,0.3)">
                {{ $thread->created_at->format('d/m/Y') }}
            </span>
        </div>

        {{-- Actions --}}
        <div class="w-44 flex items-center justify-end gap-1.5 shrink-0">
            <a href="{{ route('admin.forum.show', [$course->slug, $thread]) }}"
               class="mod-btn mod-btn-view">👁</a>

            {{-- Épingler --}}
            <form method="POST" action="{{ route('admin.forum.pin', [$course->slug, $thread]) }}">
                @csrf @method('PATCH')
                <button type="submit" class="mod-btn mod-btn-pin"
                        title="{{ $thread->is_pinned ? 'Désépingler' : 'Épingler' }}">
                    {{ $thread->is_pinned ? '📌' : '📍' }}
                </button>
            </form>

            {{-- Fermer --}}
            <form method="POST" action="{{ route('admin.forum.close', [$course->slug, $thread]) }}">
                @csrf @method('PATCH')
                <button type="submit" class="mod-btn mod-btn-close"
                        title="{{ $thread->is_closed ? 'Réouvrir' : 'Fermer' }}">
                    {{ $thread->is_closed ? '🔓' : '🔒' }}
                </button>
            </form>

            {{-- Supprimer --}}
            <form method="POST" action="{{ route('admin.forum.thread.destroy', [$course->slug, $thread]) }}"
                  onsubmit="return confirm('Supprimer ce thread et toutes ses réponses ?')">
                @csrf @method('DELETE')
                <button type="submit" class="mod-btn mod-btn-delete">🗑</button>
            </form>
        </div>
    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl mb-4">💬</div>
        <h3 class="font-bold text-white mb-2" style="font-family:'Playfair Display',serif">Aucun thread trouvé</h3>
        <p class="text-sm" style="color:rgba(255,255,255,0.3)">Essayez de modifier vos filtres.</p>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $threads->withQueryString()->links() }}</div>

@endsection