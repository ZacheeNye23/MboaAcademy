@extends('admin.layouts.app')

@section('title', $thread->title)
@section('page-title', 'Thread · Modération')
@section('page-subtitle', $course->title)

@section('topbar-actions')
<a href="{{ route('admin.forum.index', $course->slug) }}"
   class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-medium transition-colors"
   style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
    ← Forum du cours
</a>
@endsection

@push('styles')
<style>
    .reply-card {
        background:rgba(255,255,255,0.025);
        border:1px solid rgba(255,255,255,0.05);
        border-radius:16px; padding:20px;
        transition:border-color .2s;
    }
    .reply-card:hover { border-color:rgba(255,255,255,0.09); }
    .reply-card.solution {
        border-color:rgba(37,194,110,0.25);
        background:rgba(37,194,110,0.04);
    }
    .reply-child {
        margin-left:48px; margin-top:12px;
        background:rgba(255,255,255,0.015);
        border:1px solid rgba(255,255,255,0.04);
        border-radius:12px; padding:14px 16px;
    }
    .avatar {
        width:38px; height:38px; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        font-size:.75rem; font-weight:700; color:#fff; flex-shrink:0;
    }
    .mod-action {
        display:inline-flex; align-items:center; gap:5px;
        padding:6px 14px; border-radius:10px;
        font-family:'Outfit',sans-serif; font-size:.78rem; font-weight:600;
        cursor:pointer; transition:all .2s; text-decoration:none; border:none;
    }
    .ma-pin    { background:rgba(232,184,75,0.1);color:#e8b84b; }
    .ma-pin:hover { background:rgba(232,184,75,0.2); }
    .ma-close  { background:rgba(96,165,250,0.1);color:#60a5fa; }
    .ma-close:hover { background:rgba(96,165,250,0.2); }
    .ma-delete { background:rgba(239,68,68,0.1);color:#f87171; }
    .ma-delete:hover { background:rgba(239,68,68,0.2); }
    .ma-ghost  { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.45); }
    .ma-ghost:hover { background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.7); }
</style>
@endpush

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── THREAD PRINCIPAL + RÉPONSES ───────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Thread --}}
        <div class="glass p-6 anim d1">

            {{-- Badges statut --}}
            <div class="flex flex-wrap gap-2 mb-4">
                @if($thread->is_pinned) <span class="pill pill-gold">📌 Épinglé</span> @endif
                @if($thread->is_solved) <span class="pill pill-green">✅ Résolu</span> @endif
                @if($thread->is_closed) <span class="pill pill-gray">🔒 Fermé</span>  @endif
                @if(!$thread->is_closed && !$thread->is_solved)
                    <span class="pill" style="background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2)">🔓 Ouvert</span>
                @endif
            </div>

            {{-- Titre --}}
            <h2 class="text-xl font-bold text-white mb-4 leading-snug"
                style="font-family:'Playfair Display',serif">
                {{ $thread->title }}
            </h2>

            {{-- Auteur --}}
            @php
                $avatarColors = ['#1a8a47','#e8b84b','#3b82f6','#a78bfa','#f87171'];
                $bg = $avatarColors[$thread->author->id % 5];
            @endphp
            <div class="flex items-center gap-3 mb-5 pb-5 border-b border-white/5">
                <div class="avatar" style="background:{{ $bg }}">{{ $thread->author->initials }}</div>
                <div>
                    <div class="text-sm font-semibold text-white">{{ $thread->author->full_name }}</div>
                    <div class="text-xs" style="color:rgba(255,255,255,0.35)">
                        {{ $thread->created_at->translatedFormat('d F Y à H:i') }}
                        · {{ $thread->views ?? 0 }} vues
                    </div>
                </div>
            </div>

            {{-- Corps --}}
            <div class="text-sm leading-relaxed mb-5" style="color:rgba(255,255,255,0.7)">
                {!! nl2br(e($thread->body)) !!}
            </div>

            {{-- Actions de modération du thread --}}
            <div class="flex flex-wrap gap-2 pt-4 border-t border-white/5">
                <form method="POST" action="{{ route('admin.forum.pin', [$course->slug, $thread]) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="mod-action ma-pin">
                        {{ $thread->is_pinned ? '📌 Désépingler' : '📍 Épingler' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.forum.close', [$course->slug, $thread]) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="mod-action ma-close">
                        {{ $thread->is_closed ? '🔓 Réouvrir' : '🔒 Fermer' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.forum.thread.destroy', [$course->slug, $thread]) }}"
                      onsubmit="return confirm('Supprimer ce thread et toutes ses réponses ? Action irréversible.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="mod-action ma-delete">🗑 Supprimer le thread</button>
                </form>
            </div>
        </div>

        {{-- Réponses --}}
        <div class="anim d2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-white" style="font-family:'Playfair Display',serif">
                    Réponses <span class="text-sm font-normal" style="color:rgba(255,255,255,0.4)">({{ $thread->replies->count() }})</span>
                </h3>
            </div>

            @forelse($thread->replies as $reply)
            @php $rbg = $avatarColors[$reply->author->id % 5]; @endphp

            <div class="reply-card mb-4 {{ $reply->is_solution ? 'solution' : '' }}">
                {{-- En-tête réponse --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="avatar" style="background:{{ $rbg }};width:34px;height:34px;font-size:.7rem">
                            {{ $reply->author->initials }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-white">{{ $reply->author->full_name }}</span>
                                @if($reply->is_solution)
                                <span class="pill pill-green" style="font-size:.6rem">✅ Solution</span>
                                @endif
                            </div>
                            <div class="text-xs" style="color:rgba(255,255,255,0.3)">
                                {{ $reply->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    {{-- Supprimer la réponse --}}
                    <form method="POST"
                          action="{{ route('admin.forum.reply.destroy', [$course->slug, $reply]) }}"
                          onsubmit="return confirm('Supprimer cette réponse ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="mod-action ma-delete" style="padding:4px 10px;font-size:.68rem">
                            🗑 Supprimer
                        </button>
                    </form>
                </div>

                {{-- Corps réponse --}}
                <p class="text-sm leading-relaxed" style="color:rgba(255,255,255,0.65)">
                    {!! nl2br(e($reply->body)) !!}
                </p>

                {{-- Réponses imbriquées --}}
                @foreach($reply->children as $child)
                @php $cbg = $avatarColors[$child->author->id % 5]; @endphp
                <div class="reply-child">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2">
                            <div class="avatar" style="background:{{ $cbg }};width:28px;height:28px;font-size:.62rem">
                                {{ $child->author->initials }}
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-white">{{ $child->author->full_name }}</span>
                                <span class="text-[10px] ml-2" style="color:rgba(255,255,255,0.3)">
                                    {{ $child->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <form method="POST"
                              action="{{ route('admin.forum.reply.destroy', [$course->slug, $child]) }}"
                              onsubmit="return confirm('Supprimer cette réponse imbriquée ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="mod-action ma-delete"
                                    style="padding:3px 8px;font-size:.65rem">🗑</button>
                        </form>
                    </div>
                    <p class="text-xs leading-relaxed" style="color:rgba(255,255,255,0.55)">
                        {!! nl2br(e($child->body)) !!}
                    </p>
                </div>
                @endforeach
            </div>
            @empty
            <div class="glass p-10 text-center">
                <div class="text-4xl mb-3">💬</div>
                <p class="text-sm" style="color:rgba(255,255,255,0.3)">Aucune réponse encore.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── SIDEBAR DROITE ─────────────────────────────────────────────────── --}}
    <div class="space-y-5 anim d3">

        {{-- Infos thread --}}
        <div class="glass p-5">
            <h3 class="font-bold text-white text-sm mb-4" style="font-family:'Playfair Display',serif">
                📋 Infos du thread
            </h3>
            <div class="space-y-3 text-sm">
                @foreach([
                    ['📅','Créé le',    $thread->created_at->translatedFormat('d F Y')],
                    ['👁','Vues',        ($thread->views ?? 0).' vues'],
                    ['↩️', 'Réponses',   $thread->replies->count().' réponse(s)'],
                    ['📚','Cours',       $course->title],
                ] as [$icon,$label,$val])
                <div class="flex items-start gap-2">
                    <span>{{ $icon }}</span>
                    <div>
                        <div class="text-[10px] uppercase tracking-wide font-bold mb-0.5"
                             style="color:rgba(255,255,255,0.25)">{{ $label }}</div>
                        <div style="color:rgba(255,255,255,0.6)">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Infos auteur --}}
        <div class="glass p-5">
            <h3 class="font-bold text-white text-sm mb-4" style="font-family:'Playfair Display',serif">
                👤 Auteur du thread
            </h3>
            @php $bg = $avatarColors[$thread->author->id % 5]; @endphp
            <div class="flex items-center gap-3 mb-4">
                <div class="avatar" style="background:{{ $bg }}">{{ $thread->author->initials }}</div>
                <div>
                    <div class="text-sm font-semibold text-white">{{ $thread->author->full_name }}</div>
                    <div class="text-xs" style="color:rgba(255,255,255,0.4)">{{ $thread->author->email }}</div>
                </div>
            </div>
            <a href="{{ route('admin.users.show', $thread->author) }}"
               class="mod-action ma-ghost w-full justify-center" style="display:flex">
                Voir le profil →
            </a>
        </div>

        {{-- Actions rapides --}}
        <div class="glass p-5">
            <h3 class="font-bold text-white text-sm mb-4" style="font-family:'Playfair Display',serif">
                ⚡ Actions rapides
            </h3>
            <div class="space-y-2">
                <form method="POST" action="{{ route('admin.forum.pin', [$course->slug, $thread]) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="mod-action ma-pin w-full justify-center" style="display:flex">
                        {{ $thread->is_pinned ? '📌 Désépingler' : '📍 Épingler le thread' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.forum.close', [$course->slug, $thread]) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="mod-action ma-close w-full justify-center" style="display:flex">
                        {{ $thread->is_closed ? '🔓 Réouvrir' : '🔒 Fermer la discussion' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Zone danger --}}
        <div class="glass p-5" style="border-color:rgba(239,68,68,0.15)">
            <h3 class="font-bold text-sm mb-3" style="font-family:'Playfair Display',serif;color:#f87171">
                ⚠️ Zone dangereuse
            </h3>
            <p class="text-xs mb-4" style="color:rgba(255,255,255,0.35)">
                Supprimer ce thread effacera également toutes ses réponses de façon irréversible.
            </p>
            <form method="POST"
                  action="{{ route('admin.forum.thread.destroy', [$course->slug, $thread]) }}"
                  onsubmit="return confirm('Supprimer définitivement ce thread et toutes ses réponses ?')">
                @csrf @method('DELETE')
                <button type="submit" class="mod-action ma-delete w-full justify-center" style="display:flex">
                    🗑 Supprimer le thread
                </button>
            </form>
        </div>

    </div>
</div>

@endsection