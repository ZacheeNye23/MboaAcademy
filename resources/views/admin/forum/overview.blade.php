@extends('admin.layouts.app')

@section('title', 'Forum')
@section('page-title', 'Modération du Forum')
@section('page-subtitle', $stats['total_threads'].' discussions · '.$stats['active_courses'].' cours actifs')

@push('styles')
<style>
    .forum-course-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        overflow: hidden;
        transition: all .25s;
        text-decoration: none;
        display: block;
    }
    .forum-course-card:hover {
        transform: translateY(-2px);
        border-color: rgba(232,184,75,0.2);
        box-shadow: 0 10px 28px rgba(0,0,0,0.2);
    }
    .activity-dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    }
</style>
@endpush

@section('content')

{{-- ── STATS GLOBALES ────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @foreach([
        ['💬', 'Threads total',    $stats['total_threads'],  '#e8b84b'],
        ['↩️',  'Réponses total',  $stats['total_replies'],  '#25c26e'],
        ['✅', 'Résolus',          $stats['solved'],          '#25c26e'],
        ['🔓', 'Ouverts',          $stats['open'],            '#60a5fa'],
        ['🕐', 'Cette semaine',    $stats['this_week'],       '#a78bfa'],
        ['📚', 'Cours actifs',     $stats['active_courses'], '#e8b84b'],
    ] as [$icon, $label, $val, $color])
    <div class="glass anim d{{ min($loop->iteration, 6) }} p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl">{{ $icon }}</span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                  style="background:{{ $color }}15;color:{{ $color }}">{{ $label }}</span>
        </div>
        <div class="text-3xl font-bold text-white" style="font-family:'Playfair Display',serif;color:{{ $color }}">
            {{ number_format($val) }}
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- ── ACTIVITÉ RÉCENTE ──────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 glass overflow-hidden anim d3">
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
            <h2 class="font-bold text-white text-base" style="font-family:'Playfair Display',serif">
                Activité récente
            </h2>
            <span class="pill pill-gold">Derniers threads</span>
        </div>
        @forelse($recentThreads as $thread)
        @php
            $avatarColors = ['#1a8a47','#e8b84b','#3b82f6','#a78bfa','#f87171'];
            $bg = $avatarColors[$thread->author->id % 5];
        @endphp
        <div class="flex items-center gap-3 px-6 py-3.5 border-b border-white/4 last:border-0 hover:bg-white/2 transition-colors">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                 style="background:{{ $bg }}">{{ $thread->author->initials }}</div>
            <div class="flex-1 min-w-0">
                <a href="{{ route('admin.forum.show', [$thread->course->slug, $thread]) }}"
                   class="text-sm font-medium text-white hover:text-yellow-300 transition-colors truncate block">
                    {{ $thread->title }}
                </a>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[11px]" style="color:rgba(255,255,255,0.35)">{{ $thread->author->full_name }}</span>
                    <span style="color:rgba(255,255,255,0.15)">·</span>
                    <span class="text-[11px]" style="color:rgba(255,255,255,0.35)">{{ $thread->course->title }}</span>
                </div>
            </div>
            <div class="shrink-0 text-right">
                @if($thread->is_pinned)  <span class="pill pill-gold mb-1 block">📌</span> @endif
                @if($thread->is_closed)  <span class="pill pill-gray mb-1 block">🔒</span> @endif
                @if($thread->is_solved)  <span class="pill pill-green mb-1 block">✅</span> @endif
                <span class="text-[10px]" style="color:rgba(255,255,255,0.25)">
                    {{ $thread->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
        @empty
        <div class="px-6 py-10 text-center">
            <p class="text-sm" style="color:rgba(255,255,255,0.3)">Aucun thread récent.</p>
        </div>
        @endforelse
    </div>

    {{-- ── TOP CONTRIBUTEURS ─────────────────────────────────────────────── --}}
    <div class="space-y-4 anim d4">
        <div class="glass overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5">
                <h2 class="font-bold text-white text-base" style="font-family:'Playfair Display',serif">
                    🏆 Top contributeurs
                </h2>
            </div>
            @php $avatarColors = ['#e8b84b','#25c26e','#3b82f6','#a78bfa','#f87171']; @endphp
            @forelse($topContributors as $contributor)
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-white/4 last:border-0">
                <span class="text-sm font-bold w-4 shrink-0 text-center"
                      style="color:{{ $avatarColors[$loop->index] ?? '#fff' }}">
                    {{ $loop->iteration }}
                </span>
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                     style="background:{{ $avatarColors[$loop->index % 5] }}">
                    {{ strtoupper(substr($contributor->first_name, 0, 1).substr($contributor->last_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-white truncate">
                        {{ $contributor->first_name }} {{ $contributor->last_name }}
                    </div>
                </div>
                <span class="text-sm font-bold shrink-0" style="color:{{ $avatarColors[$loop->index % 5] }}">
                    {{ $contributor->replies_count }}
                </span>
            </div>
            @empty
            <div class="px-5 py-8 text-center">
                <p class="text-xs" style="color:rgba(255,255,255,0.3)">Aucune réponse encore.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── FORUMS PAR COURS ──────────────────────────────────────────────────── --}}
<div class="mb-4 flex items-center justify-between anim d4">
    <h2 class="font-bold text-white text-base" style="font-family:'Playfair Display',serif">
        Forums par cours
    </h2>
    <span class="text-xs" style="color:rgba(255,255,255,0.3)">{{ $courses->total() }} cours avec activité</span>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 anim d5">
    @foreach($courses as $course)
    @php
        $gradients = [
            ['#0d5c2e','#1a8a47'], ['#7a3b1e','#c4682d'],
            ['#1a2a6c','#4a4aad'], ['#4c1d95','#7c3aed'],
            ['#065f46','#10b981'], ['#92400e','#f59e0b'],
        ];
        $g = $gradients[$loop->index % count($gradients)];
        $solvedPct = $course->forum_threads_count > 0
            ? round($course->solved_threads_count / $course->forum_threads_count * 100)
            : 0;
    @endphp
    <a href="{{ route('admin.forum.index', $course->slug) }}" class="forum-course-card anim d{{ min($loop->iteration, 6) }}">

        {{-- Header coloré --}}
        <div class="p-4 border-b border-white/5"
             style="background:linear-gradient(135deg,{{ $g[0] }},{{ $g[1] }})">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <div class="text-[10px] uppercase tracking-widest font-bold mb-1"
                         style="color:rgba(255,255,255,0.5)">Forum</div>
                    <h3 class="text-white font-bold text-sm leading-snug line-clamp-2"
                        style="font-family:'Playfair Display',serif">
                        {{ $course->title }}
                    </h3>
                    <div class="text-xs mt-1" style="color:rgba(255,255,255,0.45)">
                        {{ $course->teacher->full_name }}
                    </div>
                </div>
                @if($course->open_threads_count > 0)
                <span class="pill pill-gold shrink-0 mt-1">
                    {{ $course->open_threads_count }} ouverts
                </span>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div class="p-4">
            {{-- Stats --}}
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="pill pill-gray">💬 {{ $course->forum_threads_count }} threads</span>
                @if($course->solved_threads_count > 0)
                <span class="pill pill-green">✅ {{ $course->solved_threads_count }} résolus</span>
                @endif
                @if($course->recent_threads_count > 0)
                <span class="pill" style="background:rgba(167,139,250,0.1);color:#a78bfa;border:1px solid rgba(167,139,250,0.2)">
                    🕐 {{ $course->recent_threads_count }} récents
                </span>
                @endif
            </div>

            {{-- Barre résolution --}}
            <div>
                <div class="flex justify-between text-[10px] mb-1" style="color:rgba(255,255,255,0.35)">
                    <span>Taux de résolution</span>
                    <span style="color:#25c26e">{{ $solvedPct }}%</span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" style="width:{{ $solvedPct }}%;background:linear-gradient(90deg,#1a8a47,#25c26e)"></div>
                </div>
            </div>

            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs font-semibold" style="color:#e8b84b">Modérer →</span>
            </div>
        </div>
    </a>
    @endforeach
</div>

{{-- Pagination --}}
<div class="mt-6">{{ $courses->links() }}</div>

@endsection