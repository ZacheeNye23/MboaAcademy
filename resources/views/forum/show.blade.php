@extends('student.layouts.app')

@section('title', $thread->title)
@section('page-title', 'Discussion')
@section('page-subtitle', $course->title . ' · Forum')

@push('styles')
<style>
    .thread-main { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:22px; overflow:hidden; margin-bottom:20px; }
    .thread-header { padding:24px 28px; border-bottom:1px solid rgba(0,0,0,0.06); background:linear-gradient(135deg,#f9fafb,#f0fdf4); }
    .reply-card { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:18px; padding:20px 24px; margin-bottom:12px; position:relative; transition:all .2s; }
    .reply-card:hover { box-shadow:0 4px 16px rgba(0,0,0,0.06); }
    .reply-card.solution { border-color:rgba(37,194,110,0.3); background:linear-gradient(135deg,#fff,rgba(37,194,110,0.03)); }
    .reply-card.solution::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:18px 0 0 18px; background:linear-gradient(to bottom,#1a8a47,#25c26e); }
    .reply-nested { margin-left:48px; padding-left:16px; border-left:2px solid rgba(37,194,110,0.15); }
    .avatar    { width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; color:#fff; flex-shrink:0; font-size:.8rem; }
    .avatar-sm { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; color:#fff; flex-shrink:0; font-size:.7rem; }
    .role-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 8px; border-radius:100px; font-size:.62rem; font-weight:700; }
    .role-teacher { background:rgba(232,184,75,0.12); color:#b8860b; border:1px solid rgba(232,184,75,0.25); }
    .role-author  { background:rgba(37,194,110,0.1); color:#1a8a47; border:1px solid rgba(37,194,110,0.2); }
    .reply-form { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:18px; padding:22px 24px; }
    .reply-input { width:100%; background:#f9fafb; border:1.5px solid rgba(0,0,0,0.1); border-radius:12px; padding:12px 16px; font-family:'Outfit',sans-serif; font-size:.875rem; color:#1f2937; outline:none; transition:all .2s; min-height:110px; resize:vertical; line-height:1.7; }
    .reply-input:focus { border-color:#25c26e; background:#fff; box-shadow:0 0 0 3px rgba(37,194,110,0.1); }
    .action-btn { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:8px; font-size:.72rem; font-weight:600; cursor:pointer; transition:all .2s; border:none; background:transparent; }
    .action-btn:hover { background:rgba(0,0,0,0.06); }
    @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}.d3{animation-delay:.12s}
    [x-cloak] { display:none !important; }
</style>
@endpush

@section('content')
@php
    // FIX : prefix dynamique utilisé partout dans la vue
    $prefix = auth()->user()->isTeacher() ? 'teacher.' : 'student.';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="{ replyingTo: null }">

    {{-- COLONNE PRINCIPALE --}}
    <div class="lg:col-span-3">

        {{-- Breadcrumb --}}
        {{-- FIX : route breadcrumb dynamique --}}
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-4 anim d1">
            <a href="{{ route($prefix.'forum.index', $course->slug) }}" class="hover:text-green-700 transition-colors font-medium">← Forum</a>
            <span>/</span>
            <span class="text-gray-600 truncate max-w-xs">{{ Str::limit($thread->title, 50) }}</span>
        </div>

        {{-- THREAD PRINCIPAL --}}
        <div class="thread-main anim d1">
            <div class="thread-header">
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($thread->is_pinned)<span class="role-badge" style="background:rgba(232,184,75,0.12);color:#b8860b;border:1px solid rgba(232,184,75,0.25)">📌 Épinglé</span>@endif
                    @if($thread->is_solved)<span class="role-badge" style="background:rgba(37,194,110,0.1);color:#1a8a47;border:1px solid rgba(37,194,110,0.2)">✅ Résolu</span>@endif
                    @if($thread->is_closed)<span class="role-badge" style="background:rgba(107,114,128,0.1);color:#6b7280;border:1px solid rgba(107,114,128,0.2)">🔒 Fermé</span>@endif
                </div>
                <h1 class="text-gray-800 text-xl font-black mb-4 leading-snug" style="font-family:'Playfair Display',serif">{{ $thread->title }}</h1>
                @php $ac = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e']; @endphp
                <div class="flex items-center gap-3">
                    <div class="avatar" style="background:{{ $ac[$thread->user_id % count($ac)] }}">{{ $thread->author->initials }}</div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-700">{{ $thread->author->full_name }}</span>
                            @if($thread->author->isTeacher())<span class="role-badge role-teacher">👨‍🏫 Formateur</span>@endif
                            @if($thread->user_id === auth()->id())<span class="role-badge role-author">Moi</span>@endif
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $thread->created_at->translatedFormat('d F Y à H:i') }} · 👁 {{ $thread->views }} · 💬 {{ $thread->replies->count() }}</div>
                    </div>
                </div>

                {{-- Actions Teacher : pin / close / delete --}}
                @if(auth()->user()->isTeacher())
                <div class="flex gap-2 mt-4 pt-4 border-t border-black/5">
                    <form method="POST" action="{{ route('teacher.forum.pin', [$course->slug, $thread->id]) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="action-btn" style="color:#b8860b">
                            {{ $thread->is_pinned ? '📌 Désépingler' : '📌 Épingler' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('teacher.forum.close', [$course->slug, $thread->id]) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="action-btn" style="color:#6b7280">
                            {{ $thread->is_closed ? '🔓 Rouvrir' : '🔒 Fermer' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('teacher.forum.thread.destroy', [$course->slug, $thread->id]) }}" onsubmit="return confirm('Supprimer cette discussion ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn" style="color:#ef4444">🗑 Supprimer</button>
                    </form>
                </div>
                @endif
            </div>

            <div class="px-7 py-6">
                <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $thread->body }}</div>
            </div>

            @if(!$thread->is_closed)
            <div class="px-7 py-4 border-t border-black/5">
                <button @click="replyingTo = 'main'; $nextTick(() => $refs.mainReply.focus())"
                        class="action-btn" style="color:#2563eb">💬 Répondre à cette discussion</button>
            </div>
            @endif
        </div>

        {{-- RÉPONSES --}}
        @if($thread->replies->whereNull('parent_id')->count() > 0)
        <h2 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2 anim d2">
            <span>💬</span> {{ $thread->replies->count() }} réponse(s)
        </h2>

        @foreach($thread->replies->whereNull('parent_id') as $reply)
        <div class="reply-card anim d{{ min($loop->iteration,6) }} {{ $reply->is_solution?'solution':'' }}">
            @if($reply->is_solution)
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold mb-3" style="background:rgba(37,194,110,0.1);color:#1a8a47">✅ Réponse acceptée comme solution</div>
            @endif
            <div class="flex items-start gap-3">
                <div class="avatar" style="background:{{ $ac[$reply->user_id % count($ac)] }}">{{ $reply->author->initials }}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm font-semibold text-gray-700">{{ $reply->author->full_name }}</span>
                        @if($reply->author->isTeacher())<span class="role-badge role-teacher">👨‍🏫 Formateur</span>@endif
                        @if($reply->user_id === auth()->id())<span class="role-badge role-author">Moi</span>@endif
                        <span class="text-xs text-gray-400">· {{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap mb-3">{{ $reply->body }}</div>

                    <div class="flex items-center gap-1 flex-wrap">
                        @if(!$thread->is_closed)
                        <button @click="replyingTo = {{ $reply->id }}"
                                class="action-btn" style="color:#2563eb">↩ Répondre</button>

                        {{-- FIX : route solution dynamique --}}
                        @if((auth()->id() === $thread->user_id || auth()->user()->isTeacher()) && !$reply->is_solution)
                        <form method="POST" action="{{ route($prefix.'forum.solution', [$course->slug, $reply]) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="action-btn" style="color:#1a8a47">✅ Marquer solution</button>
                        </form>
                        @endif
                        @endif

                        {{-- Supprimer réponse (auteur ou teacher) --}}
                        @if(auth()->id() === $reply->user_id || auth()->user()->isTeacher())
                        <form method="POST" action="{{ route($prefix.'forum.reply.destroy', [$course->slug, $reply]) }}" class="inline" onsubmit="return confirm('Supprimer cette réponse ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn" style="color:#ef4444">🗑</button>
                        </form>
                        @endif
                    </div>

                    @if(!$thread->is_closed)
                    {{-- Formulaire réponse inline --}}
                    <div x-show="replyingTo === {{ $reply->id }}" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-4">
                        {{-- FIX : route reply dynamique --}}
                        <form method="POST" action="{{ route($prefix.'forum.reply', [$course->slug, $thread->id]) }}">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                            <div class="flex gap-2">
                                <div class="avatar-sm shrink-0 mt-1" style="background:linear-gradient(135deg,#1a8a47,#25c26e)">{{ auth()->user()->initials }}</div>
                                <div class="flex-1">
                                    <textarea name="body" rows="3" class="reply-input" placeholder="Votre réponse..." required></textarea>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold text-white" style="background:linear-gradient(135deg,#1a8a47,#25c26e)">Répondre</button>
                                        <button type="button" @click="replyingTo = null" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-500 bg-gray-100">Annuler</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Réponses imbriquées --}}
        @if($reply->children->count() > 0)
        <div class="reply-nested mb-4">
            @foreach($reply->children as $child)
            <div class="reply-card mb-2" style="border-color:rgba(0,0,0,0.04);padding:14px 18px">
                <div class="flex items-start gap-2.5">
                    <div class="avatar-sm" style="background:{{ $ac[$child->user_id % count($ac)] }}">{{ $child->author->initials }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-xs font-semibold text-gray-700">{{ $child->author->full_name }}</span>
                            @if($child->author->isTeacher())<span class="role-badge role-teacher">👨‍🏫</span>@endif
                            <span class="text-xs text-gray-400">· {{ $child->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $child->body }}</p>
                        {{-- Supprimer réponse enfant --}}
                        @if(auth()->id() === $child->user_id || auth()->user()->isTeacher())
                        <form method="POST" action="{{ route($prefix.'forum.reply.destroy', [$course->slug, $child]) }}" class="inline mt-2" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn text-xs" style="color:#ef4444">🗑</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @endforeach
        @endif

        {{-- FORMULAIRE RÉPONSE PRINCIPALE --}}
        @if(!$thread->is_closed)
        <div class="reply-form anim d3">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <div class="avatar-sm" style="background:linear-gradient(135deg,#1a8a47,#25c26e)">{{ auth()->user()->initials }}</div>
                Votre réponse
            </h3>
            @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2);color:#1a8a47">🎉 {{ session('success') }}</div>
            @endif
            {{-- FIX : route reply dynamique --}}
            <form action="{{ route($prefix.'forum.reply', [$course->slug, $thread->id]) }}" method="POST">
                @csrf
                <textarea x-ref="mainReply" name="body" rows="5" class="reply-input @error('body') border-red-400 @enderror"
                          placeholder="Partagez votre réponse, votre expérience ou posez une question complémentaire..." required>{{ old('body') }}</textarea>
                @error('body')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
                            style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 4px 14px rgba(37,194,110,0.3)">
                        💬 Publier la réponse
                    </button>
                    <p class="text-xs text-gray-400">Soyez respectueux et constructif.</p>
                </div>
            </form>
        </div>
        @else
        <div class="reply-form text-center py-8" style="background:rgba(0,0,0,0.03)">
            <span class="text-2xl mb-2 block">🔒</span>
            <p class="text-sm text-gray-500 font-medium">Cette discussion est fermée.</p>
            @if(auth()->user()->isTeacher())
            <form method="POST" action="{{ route('teacher.forum.close', [$course->slug, $thread->id]) }}" class="mt-3">
                @csrf @method('PATCH')
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold text-white" style="background:#1a8a47">🔓 Rouvrir la discussion</button>
            </form>
            @endif
        </div>
        @endif
    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-4 anim d2">
        <div class="bg-white border border-black/5 rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4" style="font-family:'Playfair Display',serif">ℹ Infos</h3>
            <div class="space-y-3 text-xs text-gray-500">
                @foreach([['Créée le',$thread->created_at->translatedFormat('d M Y')],['Vues',$thread->views],['Réponses',$thread->replies->count()]] as [$l,$v])
                <div class="flex justify-between"><span>{{$l}}</span><span class="font-medium text-gray-700">{{$v}}</span></div>
                @endforeach
                <div class="flex justify-between">
                    <span>Statut</span>
                    <span class="font-medium" style="color:{{ $thread->is_solved?'#1a8a47':($thread->is_closed?'#6b7280':'#e8b84b') }}">
                        {{ $thread->is_solved?'✅ Résolu':($thread->is_closed?'🔒 Fermé':'❓ Ouvert') }}
                    </span>
                </div>
            </div>
        </div>

        @php $participants = collect([$thread->author])->merge($thread->replies->pluck('author'))->unique('id')->take(6); @endphp
        <div class="bg-white border border-black/5 rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4" style="font-family:'Playfair Display',serif">👥 Participants</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($participants as $p)
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background:{{ $ac[$p->id % count($ac)] }}" title="{{ $p->full_name }}">{{ $p->initials }}</div>
                @endforeach
            </div>
        </div>

        {{-- FIX : liens sidebar dynamiques --}}
        <a href="{{ route($prefix.'forum.index', $course->slug) }}" class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-medium bg-white border border-black/5 text-gray-600 hover:text-green-700 transition-all">← Retour au forum</a>
        <a href="{{ route($prefix.'forum.create', $course->slug) }}" class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5" style="background:linear-gradient(135deg,#1a8a47,#25c26e)">✏️ Nouvelle discussion</a>
    </div>
</div>

@endsection