@extends('student.layouts.app')

@section('title', 'Forum')
@section('page-title', 'Forum')
@section('page-subtitle', 'Discussions de vos cours')

@push('styles')
<style>
    .forum-card { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:20px; overflow:hidden; transition:all .25s; text-decoration:none; display:block; }
    .forum-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(0,0,0,0.09); border-color:rgba(37,194,110,0.2); }
    .stat-pill { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:100px; font-size:.72rem; font-weight:600; background:rgba(0,0,0,0.04); color:#6b7280; }
    .unread-dot { width:8px; height:8px; border-radius:50%; background:#25c26e; flex-shrink:0; animation:pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.6;transform:scale(1.3)} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}.d3{animation-delay:.12s}
    .d4{animation-delay:.16s}.d5{animation-delay:.2s}.d6{animation-delay:.24s}
    .last-avatar { width:24px; height:24px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:.6rem; font-weight:700; color:#fff; flex-shrink:0; }
</style>
@endpush

@section('content')
@php
    // FIX : prefix dynamique
    $prefix = auth()->user()->isTeacher() ? 'teacher.' : 'student.';
    $isTeacher = auth()->user()->isTeacher();
@endphp

<div class="flex items-center justify-between mb-6 anim d1">
    <p class="text-sm text-gray-500">
        @if($isTeacher)
            Forums de vos <span class="font-semibold text-gray-700">{{ $enrolledCourses->count() }}</span> cours enseignés
        @else
            Forums de vos <span class="font-semibold text-gray-700">{{ $enrolledCourses->count() }}</span> cours inscrits
        @endif
    </p>
</div>

@if($enrolledCourses->isEmpty())
<div class="bg-white border border-black/5 rounded-2xl p-16 text-center anim d2">
    <div class="text-5xl mb-4">💬</div>
    <h3 class="text-lg font-bold text-gray-700 mb-2" style="font-family:'Playfair Display',serif">Aucun forum disponible</h3>
    @if($isTeacher)
    <p class="text-sm text-gray-400 mb-6">Créez des cours pour accéder à leurs forums.</p>
    <a href="{{ route('teacher.courses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#1a8a47,#25c26e)">➕ Créer un cours</a>
    @else
    <p class="text-sm text-gray-400 mb-6">Inscrivez-vous à des cours pour accéder à leurs forums.</p>
    <a href="{{ route('student.courses.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#1a8a47,#25c26e)">🔍 Explorer les cours</a>
    @endif
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @php
        $gradients=[['#0d5c2e','#1a8a47'],['#7a3b1e','#c4682d'],['#1a2a6c','#4a4aad'],['#065f46','#10b981'],['#4c1d95','#7c3aed'],['#92400e','#f59e0b']];
        $avatarColors=['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e'];
    @endphp

    @foreach($enrolledCourses as $enrollment)
    @php
        // FIX : pour le teacher, $enrollment est un objet simple {course: ...}
        // Pour le student, c'est un vrai modèle Enrollment
        $course      = $enrollment->course;
        $g           = $gradients[$loop->index % count($gradients)];
        $threadCount = $course->forumThreads()->count();
        $solvedCount = $course->forumThreads()->where('is_solved', true)->count();
        $lastThread  = $course->forumThreads()->with('author')->latest()->first();
        $unreadCount = $course->forumThreads()
                          ->where('created_at', '>=', now()->subDays(3))
                          ->where('user_id', '!=', auth()->id())
                          ->count();
    @endphp

    {{-- FIX : route index dynamique --}}
    <a href="{{ route($prefix.'forum.index', $course->slug) }}"
       class="forum-card anim d{{ min($loop->iteration, 6) }}">

        {{-- Header coloré --}}
        <div class="p-5 border-b border-black/5" style="background:linear-gradient(135deg,{{ $g[0] }},{{ $g[1] }})">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="text-white/55 text-[10px] font-bold uppercase tracking-widest mb-1">Forum</div>
                    <h3 class="text-white font-bold text-sm leading-snug line-clamp-2" style="font-family:'Playfair Display',serif">
                        {{ $course->title }}
                    </h3>
                    <div class="text-white/45 text-xs mt-1">
                        @if($isTeacher)
                            Votre cours
                        @else
                            Par {{ $course->teacher->full_name }}
                        @endif
                    </div>
                </div>
                @if($unreadCount > 0)
                <div class="shrink-0 flex items-center gap-1.5 mt-1">
                    <span class="unread-dot"></span>
                    <span class="text-white text-xs font-bold">{{ $unreadCount }} nouveau(x)</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div class="p-5">
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="stat-pill">💬 {{ $threadCount }} discussion(s)</span>
                @if($solvedCount > 0)
                <span class="stat-pill" style="background:rgba(37,194,110,0.08);color:#1a8a47">✅ {{ $solvedCount }} résolue(s)</span>
                @endif
                <span class="stat-pill">👥 {{ $course->enrollments_count }}</span>
            </div>

            @if($lastThread)
            <div class="flex items-start gap-2.5 pt-3 border-t border-black/5">
                <div class="last-avatar shrink-0 mt-0.5" style="background:{{ $avatarColors[$lastThread->user_id % count($avatarColors)] }}">{{ $lastThread->author->initials }}</div>
                <div class="flex-1 min-w-0">
                    <div class="text-[10px] text-gray-400 mb-0.5">Dernière discussion</div>
                    <div class="text-xs font-medium text-gray-700 truncate">{{ $lastThread->title }}</div>
                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $lastThread->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @else
            <div class="pt-3 border-t border-black/5">
                <p class="text-xs text-gray-400 italic">Aucune discussion encore. Soyez le premier !</p>
            </div>
            @endif

            <div class="mt-4 flex items-center justify-between">
                <span class="text-xs font-semibold" style="color:#1a8a47">Accéder au forum →</span>
                @if($threadCount === 0)
                <span class="text-[10px] px-2 py-1 rounded-lg font-medium" style="background:rgba(37,194,110,0.1);color:#1a8a47">Démarrer la discussion</span>
                @endif
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

@endsection