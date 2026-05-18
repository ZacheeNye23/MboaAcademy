{{-- resources/views/forum/index.blade.php --}}
@extends('student.layouts.app')

@section('title', 'Forum — ' . $course->title)
@section('page-title', 'Forum')
@section('page-subtitle', $course->title)

@push('styles')
<style>
    .forum-hero {
        background: linear-gradient(135deg, #0a1a0f 0%, #0d5c2e 100%);
        border-radius: 22px; padding: 28px 32px;
        position: relative; overflow: hidden; margin-bottom: 24px;
    }
    .forum-hero::before {
        content: ''; position: absolute; inset: 0;
        background-image: repeating-linear-gradient(45deg, rgba(37,194,110,0.05) 0, rgba(37,194,110,0.05) 1px, transparent 1px, transparent 28px);
    }
    .thread-card {
        background: #fff; border: 1px solid rgba(0,0,0,0.06);
        border-radius: 18px; padding: 20px 24px;
        transition: all .22s; text-decoration: none; display: block; position: relative;
    }
    .thread-card:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,0,0,0.08); border-color: rgba(37,194,110,0.2); }
    .thread-card.pinned { border-color: rgba(232,184,75,0.3); background: linear-gradient(135deg,#fff,rgba(232,184,75,0.03)); }
    .thread-card.solved::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:18px 0 0 18px; background:linear-gradient(to bottom,#1a8a47,#25c26e); }
    .avatar-sm { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; color:#fff; flex-shrink:0; }
    .forum-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:100px; font-size:.65rem; font-weight:700; letter-spacing:.3px; }
    .badge-pinned { background:rgba(232,184,75,0.12); color:#b8860b; border:1px solid rgba(232,184,75,0.25); }
    .badge-solved { background:rgba(37,194,110,0.1); color:#1a8a47; border:1px solid rgba(37,194,110,0.2); }
    .badge-closed { background:rgba(107,114,128,0.1); color:#6b7280; border:1px solid rgba(107,114,128,0.2); }
    .badge-new    { background:rgba(59,130,246,0.1); color:#2563eb; border:1px solid rgba(59,130,246,0.2); }
    .forum-search { background:rgba(255,255,255,0.08); border:1.5px solid rgba(255,255,255,0.12); border-radius:12px; padding:10px 16px 10px 40px; color:#fff; font-family:'Outfit',sans-serif; font-size:.875rem; outline:none; width:100%; transition:all .2s; }
    .forum-search::placeholder { color:rgba(255,255,255,0.35); }
    .forum-search:focus { border-color:rgba(37,194,110,0.5); background:rgba(255,255,255,0.12); }
    .stat-card { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:16px; padding:18px; }
    .f-tab { padding:7px 14px; border-radius:100px; font-size:.78rem; font-weight:600; cursor:pointer; transition:all .2s; text-decoration:none; white-space:nowrap; }
    .f-tab.on  { background:#1a8a47; color:#fff; }
    .f-tab.off { background:#fff; color:#6b7280; border:1.5px solid rgba(0,0,0,0.1); }
    .f-tab.off:hover { border-color:#1a8a47; color:#1a8a47; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}.d3{animation-delay:.12s}
    .d4{animation-delay:.16s}.d5{animation-delay:.20s}.d6{animation-delay:.24s}
</style>
@endpush

@section('content')
@php
    // FIX : prefix dynamique utilisé dans toute la vue
    $prefix = auth()->user()->isTeacher() ? 'teacher.' : 'student.';
@endphp

{{-- HERO --}}
<div class="forum-hero anim d1">
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-5">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl">💬</span>
                <h2 class="text-white text-xl font-black" style="font-family:'Playfair Display',serif">Forum du cours</h2>
            </div>
            <p class="text-white/55 text-sm mb-4 max-w-lg leading-relaxed">
                Posez vos questions et partagez vos découvertes autour de <strong class="text-white/80">{{ $course->title }}</strong>.
            </p>
            <div class="flex gap-5 text-sm">
                @foreach([['💬',$threads->total().' discussions'],['👥',$course->enrollments_count.' membres'],['✅',$threads->where('is_solved',true)->count().' résolues']] as [$i,$l])
                <div class="flex items-center gap-1.5" style="color:rgba(255,255,255,0.6)"><span>{{$i}}</span><span>{{$l}}</span></div>
                @endforeach
            </div>
        </div>
        {{-- FIX : route create dynamique --}}
        <a href="{{ route($prefix.'forum.create', $course->slug) }}"
           class="shrink-0 flex items-center gap-2 px-5 py-3 rounded-xl font-semibold text-sm transition-all hover:-translate-y-0.5 self-start sm:self-center"
           style="background:linear-gradient(135deg,#e8b84b,#f0d070);color:#0a1a0f;box-shadow:0 4px 16px rgba(232,184,75,0.35)">
            ✏️ Nouvelle discussion
        </a>
    </div>
    <div class="relative mt-5">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-white/40 text-sm">🔍</span>
        <form method="GET">
            <input type="text" name="search" value="{{ request('search') }}" class="forum-search" placeholder="Rechercher dans les discussions...">
        </form>
    </div>
</div>

{{-- LAYOUT --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="lg:col-span-3">

        {{-- Filtres --}}
        <div class="flex gap-2 flex-wrap mb-5 anim d2">
            @foreach(['all'=>'🗂 Toutes','recent'=>'🕐 Récentes','solved'=>'✅ Résolues','unsolved'=>'❓ Non résolues','mine'=>'👤 Les miennes'] as $val=>$label)
            <a href="{{ request()->fullUrlWithQuery(['filter'=>$val]) }}" class="f-tab {{ request('filter','all')===$val?'on':'off' }}">{{ $label }}</a>
            @endforeach
        </div>

        {{-- Threads --}}
        @if($threads->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-black/5 anim d3">
            <div class="text-5xl mb-4">💬</div>
            <h3 class="text-lg font-bold text-gray-700 mb-2" style="font-family:'Playfair Display',serif">Aucune discussion</h3>
            <p class="text-sm text-gray-400 mb-6">Soyez le premier à poser une question !</p>
            {{-- FIX : route create dynamique --}}
            <a href="{{ route($prefix.'forum.create', $course->slug) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#1a8a47,#25c26e)">✏️ Créer une discussion</a>
        </div>
        @else
        <div class="space-y-3">
            @foreach($threads as $thread)
            @php
                $colors=['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e'];
                $avatarBg=$colors[$thread->user_id % count($colors)];
                $isNew=$thread->created_at->gt(now()->subDays(2));
                $isAuthor=$thread->user_id===auth()->id();
            @endphp
            {{-- FIX : route show dynamique (était hardcodée sur student.) --}}
            <a href="{{ route($prefix.'forum.show', [$course->slug, $thread]) }}"
               class="thread-card anim d{{ min($loop->iteration,6) }} {{ $thread->is_pinned?'pinned':'' }} {{ $thread->is_solved?'solved':'' }}">
                <div class="flex items-start gap-4">
                    <div class="avatar-sm shrink-0" style="background:{{ $avatarBg }}">{{ $thread->author->initials }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap gap-1.5 mb-1.5">
                            @if($thread->is_pinned)<span class="forum-badge badge-pinned">📌 Épinglé</span>@endif
                            @if($thread->is_solved)<span class="forum-badge badge-solved">✅ Résolu</span>@endif
                            @if($thread->is_closed)<span class="forum-badge badge-closed">🔒 Fermé</span>@endif
                            @if($isNew)<span class="forum-badge badge-new">✨ Nouveau</span>@endif
                            @if($isAuthor)<span class="forum-badge" style="background:rgba(37,194,110,0.08);color:#1a8a47;border:1px solid rgba(37,194,110,0.15)">👤 Ma discussion</span>@endif
                        </div>
                        <h3 class="font-bold text-gray-800 text-sm leading-snug mb-1.5 line-clamp-2" style="font-family:'Playfair Display',serif">{{ $thread->title }}</h3>
                        <p class="text-gray-400 text-xs leading-relaxed line-clamp-2 mb-3">{{ Str::limit(strip_tags($thread->body),160) }}</p>
                        <div class="flex items-center gap-4 text-xs text-gray-400">
                            <span class="font-medium text-gray-600">{{ $thread->author->full_name }}</span>
                            <span>·</span>
                            <span>{{ $thread->created_at->diffForHumans() }}</span>
                            <span>· 💬 {{ $thread->replies_count }} réponse(s)</span>
                            <span>· 👁 {{ $thread->views }}</span>
                        </div>
                    </div>
                    @if($thread->replies()->latest()->first()?->created_at->gt(now()->subHours(24)))
                    <div class="shrink-0 w-2 h-2 rounded-full mt-2" style="background:#25c26e"></div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $threads->withQueryString()->links() }}</div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4 anim d3">
        <div class="stat-card">
            <h3 class="text-sm font-bold text-gray-700 mb-4" style="font-family:'Playfair Display',serif">📊 Statistiques</h3>
            <div class="space-y-3">
                @foreach([['💬','Discussions',$threads->total()],['✅','Résolues',$threads->where('is_solved',true)->count()],['👥','Participants',$course->enrollments_count]] as [$i,$l,$v])
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm text-gray-500"><span>{{$i}}</span>{{$l}}</div>
                    <span class="font-bold text-gray-700 text-sm">{{$v}}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="stat-card">
            <h3 class="text-sm font-bold text-gray-700 mb-4" style="font-family:'Playfair Display',serif">🏆 Top contributeurs</h3>
            @php
                $contributors = \App\Models\ForumThread::where('course_id',$course->id)->with('author')->get()
                    ->groupBy('user_id')->map(fn($t)=>['author'=>$t->first()->author,'count'=>$t->count()])
                    ->sortByDesc('count')->take(5);
                $avatarColors=['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46'];
            @endphp
            @forelse($contributors as $c)
            <div class="flex items-center gap-2.5 mb-3 last:mb-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0" style="background:{{ $avatarColors[$loop->index%5] }}">{{ $c['author']->initials }}</div>
                <div class="flex-1 min-w-0"><div class="text-xs font-semibold text-gray-700 truncate">{{ $c['author']->full_name }}</div></div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:rgba(37,194,110,0.1);color:#1a8a47">{{ $c['count'] }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400">Aucune contribution encore.</p>
            @endforelse
        </div>

        <div class="stat-card" style="background:linear-gradient(135deg,#0a1a0f,#0d5c2e);border-color:rgba(37,194,110,0.15)">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-white" style="background:linear-gradient(135deg,#7a3b1e,#c4682d)">{{ $course->teacher->initials }}</div>
                <div>
                    <div class="text-white text-xs font-bold">{{ $course->teacher->full_name }}</div>
                    <div class="text-white/45 text-[10px]">Formateur du cours</div>
                </div>
            </div>
            <p class="text-white/50 text-xs leading-relaxed">Le formateur est actif sur ce forum et répond aux questions.</p>
        </div>
    </div>
</div>

@endsection