@extends('student.layouts.app')

@section('title', 'Classement')
@section('page-title', 'Classement')
@section('page-subtitle', 'Top apprenants MboaAcademy')

@push('styles')
<style>
    .podium-wrap { display:flex; align-items:flex-end; justify-content:center; gap:12px; margin-bottom:32px; }
    .podium-col  { display:flex; flex-direction:column; align-items:center; }

    .podium-card {
        background:#fff; border-radius:20px; padding:20px 16px;
        text-align:center; border:1px solid rgba(0,0,0,0.06);
        transition:all .3s; position:relative; overflow:hidden;
    }
    .podium-card.rank-1 {
        border-color:rgba(232,184,75,0.3);
        background:linear-gradient(135deg,#fff,rgba(232,184,75,0.04));
        box-shadow:0 8px 32px rgba(232,184,75,0.15);
    }
    .podium-card.rank-2 {
        border-color:rgba(156,163,175,0.3);
        background:linear-gradient(135deg,#fff,rgba(156,163,175,0.04));
    }
    .podium-card.rank-3 {
        border-color:rgba(205,127,50,0.3);
        background:linear-gradient(135deg,#fff,rgba(205,127,50,0.04));
    }

    .podium-block {
        width:100%; border-radius:14px 14px 0 0;
        display:flex; align-items:center; justify-content:center;
        font-size:1.5rem; font-weight:900; color:rgba(255,255,255,0.8);
    }

    .lb-table-row {
        display:flex; align-items:center; gap:12px;
        padding:14px 20px; border-radius:14px;
        transition:all .2s; border:1px solid transparent;
    }
    .lb-table-row:hover { background:rgba(37,194,110,0.04); border-color:rgba(37,194,110,0.1); }
    .lb-table-row.me    { background:rgba(37,194,110,0.06); border-color:rgba(37,194,110,0.2); }

    .badge-mini { width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.7rem; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .45s ease both; }
    .d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}
    .d4{animation-delay:.2s}.d5{animation-delay:.25s}.d6{animation-delay:.3s}

    @keyframes crownBounce { 0%,100%{transform:translateY(0) rotate(-5deg)} 50%{transform:translateY(-6px) rotate(5deg)} }
    .crown { animation: crownBounce 2s ease-in-out infinite; display:inline-block; }
</style>
@endpush

@section('content')

@php
    $avatarColors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e','#0f766e','#b45309'];
    $top3    = $fullLeaderboard->take(3);
    $rest    = $fullLeaderboard->skip(3);
    $myRank  = $fullLeaderboard->search(fn($e) => $e['user_id'] === auth()->id()) + 1;
@endphp

{{-- ── PODIUM TOP 3 ── --}}
@if($top3->count() >= 3)
<div class="anim d1 mb-8">
    <div class="text-center mb-8">
        <div class="text-3xl mb-1"><span class="crown">👑</span></div>
        <h2 class="text-xl font-black text-gray-800" style="font-family:'Playfair Display',serif">
            Top apprenants
        </h2>
        <p class="text-sm text-gray-400 mt-1">Basé sur le nombre de badges obtenus</p>
    </div>

    <div class="podium-wrap">
        {{-- 2ème place --}}
        @php $second = $top3->get(1); @endphp
        <div class="podium-col" style="width:140px">
            <div class="podium-card rank-2 w-full mb-3">
                <div class="w-14 h-14 rounded-full mx-auto flex items-center justify-center font-bold text-white text-base mb-2"
                     style="background:{{ $avatarColors[1] }}">{{ $second['initials'] }}</div>
                <div class="text-sm font-bold text-gray-700 truncate">{{ $second['name'] }}</div>
                <div class="text-2xl font-black mt-1" style="color:#9ca3af;font-family:'Playfair Display',serif">🥈</div>
                <div class="text-xs font-bold mt-1" style="color:#e8b84b">{{ $second['badges'] }} badges</div>
                <div class="text-[10px] text-gray-400">{{ $second['xp'] }} XP</div>
            </div>
            <div class="podium-block" style="height:80px;background:linear-gradient(to bottom,#9ca3af,#6b7280)">2</div>
        </div>

        {{-- 1ère place --}}
        @php $first = $top3->get(0); @endphp
        <div class="podium-col" style="width:160px">
            <div class="podium-card rank-1 w-full mb-3">
                <div class="text-2xl mb-2">👑</div>
                <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center font-bold text-white text-lg mb-2"
                     style="background:{{ $avatarColors[0] }};box-shadow:0 0 0 3px rgba(232,184,75,0.3)">{{ $first['initials'] }}</div>
                <div class="text-sm font-bold text-gray-800 truncate">{{ $first['name'] }}</div>
                <div class="text-2xl font-black mt-1" style="font-family:'Playfair Display',serif">🥇</div>
                <div class="text-xs font-bold mt-1" style="color:#e8b84b">{{ $first['badges'] }} badges</div>
                <div class="text-[10px] text-gray-400">{{ $first['xp'] }} XP</div>
            </div>
            <div class="podium-block" style="height:120px;background:linear-gradient(to bottom,#e8b84b,#b8860b)">1</div>
        </div>

        {{-- 3ème place --}}
        @php $third = $top3->get(2); @endphp
        <div class="podium-col" style="width:140px">
            <div class="podium-card rank-3 w-full mb-3">
                <div class="w-14 h-14 rounded-full mx-auto flex items-center justify-center font-bold text-white text-base mb-2"
                     style="background:{{ $avatarColors[2] }}">{{ $third['initials'] }}</div>
                <div class="text-sm font-bold text-gray-700 truncate">{{ $third['name'] }}</div>
                <div class="text-2xl font-black mt-1" style="color:#cd7f32;font-family:'Playfair Display',serif">🥉</div>
                <div class="text-xs font-bold mt-1" style="color:#e8b84b">{{ $third['badges'] }} badges</div>
                <div class="text-[10px] text-gray-400">{{ $third['xp'] }} XP</div>
            </div>
            <div class="podium-block" style="height:60px;background:linear-gradient(to bottom,#cd7f32,#92400e)">3</div>
        </div>
    </div>
</div>
@endif

{{-- ── MA POSITION (si pas dans le top 10) ── --}}
@if($myRank > 10)
@php $myEntry = $fullLeaderboard->firstWhere('user_id', auth()->id()); @endphp
@if($myEntry)
<div class="mb-4 anim d2">
    <div class="px-5 py-3 rounded-xl text-xs font-medium" style="background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);color:#2563eb">
        📍 Votre position actuelle : <strong>#{{ $myRank }}</strong> sur {{ $fullLeaderboard->count() }} apprenants
    </div>
</div>
@endif
@endif

{{-- ── TABLEAU COMPLET ── --}}
<div class="bg-white border border-black/5 rounded-2xl overflow-hidden anim d3">
    <div class="flex items-center justify-between px-6 py-4 border-b border-black/5">
        <h2 class="text-sm font-bold text-gray-700" style="font-family:'Playfair Display',serif">
            Classement général
        </h2>
        <span class="text-xs text-gray-400">{{ $fullLeaderboard->count() }} apprenants</span>
    </div>

    {{-- En-tête tableau --}}
    <div class="flex items-center gap-3 px-6 py-2 border-b border-black/5"
         style="color:rgba(0,0,0,0.3);font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08rem">
        <span class="w-8 text-center">#</span>
        <span class="flex-1">Apprenant</span>
        <span class="w-20 text-center hidden sm:block">Badges</span>
        <span class="w-20 text-center hidden md:block">XP</span>
        <span class="w-24 text-center hidden lg:block">Niveau</span>
    </div>

    <div class="p-3 space-y-1">
        @foreach($fullLeaderboard->take(20) as $entry)
        @php
            $isMe      = $entry['user_id'] === auth()->id();
            $rank      = $loop->iteration;
            $medals    = [1=>'🥇',2=>'🥈',3=>'🥉'];
            $rankBg    = match($rank) { 1=>'rgba(232,184,75,0.15)', 2=>'rgba(156,163,175,0.12)', 3=>'rgba(205,127,50,0.12)', default=>'rgba(0,0,0,0.04)' };
            $lvl       = max(1, intdiv($entry['badges'], 2) + 1);
        @endphp

        <div class="lb-table-row {{ $isMe ? 'me' : '' }} anim d{{ min($rank, 6) }}">

            {{-- Rang --}}
            <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs shrink-0"
                 style="background:{{ $rankBg }}">
                {{ $medals[$rank] ?? $rank }}
            </div>

            {{-- Avatar + Nom --}}
            <div class="flex items-center gap-2.5 flex-1 min-w-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-white text-xs shrink-0"
                     style="background:{{ $avatarColors[$loop->index % count($avatarColors)] }}">
                    {{ $entry['initials'] }}
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-semibold {{ $isMe ? 'text-green-700' : 'text-gray-700' }} truncate">
                        {{ $entry['name'] }}
                        @if($isMe)<span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-1" style="background:rgba(37,194,110,0.1);color:#1a8a47">Moi</span>@endif
                    </div>
                    {{-- Mini badges gagnés --}}
                    <div class="flex gap-1 mt-0.5">
                        @foreach($entry['recent_badges'] ?? [] as $b)
                        <span class="text-xs" title="{{ $b }}">{{ $b }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Badges --}}
            <div class="w-20 text-center hidden sm:block">
                <span class="font-black text-sm" style="color:#e8b84b">{{ $entry['badges'] }}</span>
                <div class="text-[10px] text-gray-400">badges</div>
            </div>

            {{-- XP --}}
            <div class="w-20 text-center hidden md:block">
                <span class="font-bold text-sm text-gray-600">{{ $entry['xp'] }}</span>
                <div class="text-[10px] text-gray-400">XP</div>
            </div>

            {{-- Niveau --}}
            <div class="w-24 text-center hidden lg:block">
                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold"
                     style="background:rgba(37,194,110,0.08);color:#1a8a47">
                    Niv. {{ $lvl }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="mt-4 text-center anim d6">
    <a href="{{ route('student.badges.index') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
        🏆 Mes badges →
    </a>
</div>

@endsection