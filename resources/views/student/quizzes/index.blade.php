@extends('student.layouts.app')

@section('title', 'Mes Quiz')
@section('page-title', 'Quiz & Évaluations')
@section('page-subtitle', 'Testez vos connaissances')

@push('styles')
<style>
    .quiz-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 20px;
        overflow: hidden;
        transition: all .25s;
        display: flex;
        flex-direction: column;
    }
    .quiz-card:hover { transform: translateY(-3px); box-shadow: 0 14px 36px rgba(0,0,0,0.09); }
    .quiz-card.locked { opacity: .6; filter: grayscale(.3); }

    .score-ring { transform: rotate(-90deg); }

    .diff-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:100px; font-size:.68rem; font-weight:700; letter-spacing:.3px; }
    .diff-easy   { background:rgba(37,194,110,0.1); color:#1a8a47; border:1px solid rgba(37,194,110,0.2); }
    .diff-medium { background:rgba(232,184,75,0.12); color:#b8860b; border:1px solid rgba(232,184,75,0.25); }
    .diff-hard   { background:rgba(239,68,68,0.1);   color:#dc2626; border:1px solid rgba(239,68,68,0.2); }

    .stat-chip { display:flex; align-items:center; gap:5px; font-size:.75rem; color:#6b7280; }

    .attempt-bar { height:4px; border-radius:2px; background:rgba(0,0,0,0.08); overflow:hidden; }
    .attempt-fill { height:100%; border-radius:2px; }

    .tab-filter { padding:7px 16px; border-radius:100px; font-size:.78rem; font-weight:600; cursor:pointer; transition:all .2s; border:1.5px solid transparent; text-decoration:none; }
    .tab-filter.on  { background:#1a8a47; color:#fff; border-color:#1a8a47; }
    .tab-filter.off { background:#fff; color:#6b7280; border-color:rgba(0,0,0,0.1); }
    .tab-filter.off:hover { border-color:#1a8a47; color:#1a8a47; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .45s ease both; }
    .d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}
    .d4{animation-delay:.2s}.d5{animation-delay:.25s}.d6{animation-delay:.3s}
</style>
@endpush

@section('content')

{{-- ── RÉSUMÉ STATS ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $allQuizzes   = collect($quizzes);
        $passedCount  = $allQuizzes->filter(fn($q) => $q['passed'])->count();
        $doneCount    = $allQuizzes->filter(fn($q) => $q['attempts'] > 0)->count();
        $avgScore     = $allQuizzes->filter(fn($q) => $q['best_score'] !== null)->avg('best_score');
        $pendingCount = $allQuizzes->filter(fn($q) => $q['can_attempt'])->count();
    @endphp

    @foreach([
        ['📝', 'Quiz disponibles', $allQuizzes->count(),  'dans vos cours',   '#25c26e'],
        ['✅', 'Quiz réussis',     $passedCount,           'sur '.$doneCount.' tentés', '#3b82f6'],
        ['📊', 'Score moyen',      $avgScore ? round($avgScore).'%' : '—', 'meilleur score',  '#e8b84b'],
        ['⏳', 'En attente',       $pendingCount,          'à passer',         '#a78bfa'],
    ] as [$icon, $label, $val, $sub, $color])
    <div class="anim d{{ $loop->iteration }} bg-white rounded-2xl p-5 border border-black/5 hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                 style="background:{{ $color }}15">{{ $icon }}</div>
            <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background:{{ $color }}12;color:{{ $color }}">{{ $sub }}</span>
        </div>
        <div class="text-3xl font-bold mb-1" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
        <div class="text-xs text-gray-400">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- ── FILTRES ── --}}
<div class="flex gap-2 flex-wrap mb-6 anim d1">
    @foreach(['all' => 'Tous', 'pending' => '⏳ À faire', 'passed' => '✅ Réussis', 'failed' => '❌ Échoués'] as $val => $label)
    <a href="{{ request()->fullUrlWithQuery(['filter' => $val]) }}"
       class="tab-filter {{ request('filter', 'all') === $val ? 'on' : 'off' }}">{{ $label }}</a>
    @endforeach
</div>

{{-- ── LISTE DES QUIZ ── --}}
@php
    $filtered = collect($quizzes)->filter(function($q) {
        return match(request('filter', 'all')) {
            'pending' => $q['can_attempt'] && !$q['passed'],
            'passed'  => $q['passed'],
            'failed'  => $q['attempts'] > 0 && !$q['passed'],
            default   => true,
        };
    });
@endphp

@if($filtered->isEmpty())
<div class="flex flex-col items-center justify-center py-20 text-center anim d2">
    <div class="text-6xl mb-4">📝</div>
    <h3 class="text-lg font-bold text-gray-700 mb-2" style="font-family:'Playfair Display',serif">Aucun quiz trouvé</h3>
    <p class="text-sm text-gray-400 mb-6">Inscrivez-vous à des cours pour accéder aux quiz.</p>
    <a href="{{ route('student.courses.index') }}"
       class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">Explorer les cours</a>
</div>
@else

{{-- Regroupement par cours --}}
@php $grouped = $filtered->groupBy(fn($q) => $q['quiz']->course->title ?? 'Sans cours'); @endphp

@foreach($grouped as $courseName => $courseQuizzes)
<div class="mb-8 anim d2">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm font-bold text-white"
             style="background:linear-gradient(135deg,#1a8a47,#25c26e)">📚</div>
        <h2 class="text-sm font-bold text-gray-700">{{ $courseName }}</h2>
        <div class="flex-1 h-px bg-black/6"></div>
        <span class="text-xs text-gray-400">{{ $courseQuizzes->count() }} quiz</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($courseQuizzes as $data)
        @php
            $quiz       = $data['quiz'];
            $attempts   = $data['attempts'];
            $canAttempt = $data['can_attempt'];
            $bestScore  = $data['best_score'];
            $passed     = $data['passed'];
            $remaining  = $quiz->max_attempts - $attempts;
            $qCount     = $quiz->questions->count();
            $circ       = 2 * M_PI * 22;
            $offset     = $bestScore !== null ? $circ * (1 - $bestScore / 100) : $circ;

            $diffLabel = match(true) {
                $quiz->passing_score >= 80 => ['Difficile', 'diff-hard'],
                $quiz->passing_score >= 60 => ['Moyen',    'diff-medium'],
                default                    => ['Facile',   'diff-easy'],
            };
        @endphp

        <div class="quiz-card {{ !$canAttempt && !$passed ? 'locked' : '' }} anim d{{ min($loop->iteration, 6) }}">

            {{-- Header coloré --}}
            <div class="p-5 relative overflow-hidden"
                 style="background:linear-gradient(135deg,{{ $passed ? '#0d5c2e,#1a8a47' : ($canAttempt ? '#1a2a6c,#2d3a8c' : '#374151,#4b5563') }})">

                {{-- Pattern de fond --}}
                <div class="absolute inset-0 opacity-10"
                     style="background-image:repeating-linear-gradient(45deg,rgba(255,255,255,0.08) 0,rgba(255,255,255,0.08) 1px,transparent 1px,transparent 18px)"></div>

                <div class="relative z-10 flex items-start justify-between">
                    <div class="flex-1 min-w-0 pr-3">
                        <span class="diff-badge {{ $diffLabel[1] }} mb-2 inline-flex">{{ $diffLabel[0] }}</span>
                        <h3 class="text-white font-bold text-sm leading-snug mb-1">{{ $quiz->title }}</h3>
                        @if($quiz->description)
                        <p class="text-white/50 text-xs leading-relaxed line-clamp-2">{{ $quiz->description }}</p>
                        @endif
                    </div>

                    {{-- Anneau score --}}
                    @if($bestScore !== null)
                    <div class="shrink-0 relative" style="width:54px;height:54px">
                        <svg width="54" height="54" viewBox="0 0 54 54">
                            <circle cx="27" cy="27" r="22" fill="rgba(0,0,0,0.25)" stroke="rgba(255,255,255,0.12)" stroke-width="5"/>
                            <circle cx="27" cy="27" r="22" fill="none"
                                    stroke="{{ $passed ? '#25c26e' : '#f87171' }}" stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $circ }}"
                                    stroke-dashoffset="{{ $offset }}"
                                    class="score-ring"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-white font-black text-xs leading-none">{{ $bestScore }}%</span>
                        </div>
                    </div>
                    @else
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
                         style="background:rgba(255,255,255,0.08)">
                        {{ $canAttempt ? '🎯' : '🔒' }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Body --}}
            <div class="p-5 flex flex-col flex-1">

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="stat-chip flex-col items-start gap-0.5">
                        <span class="font-bold text-gray-700 text-sm">{{ $qCount }}</span>
                        <span class="text-[10px]">questions</span>
                    </div>
                    <div class="stat-chip flex-col items-start gap-0.5">
                        <span class="font-bold text-gray-700 text-sm">{{ $quiz->passing_score }}%</span>
                        <span class="text-[10px]">requis</span>
                    </div>
                    <div class="stat-chip flex-col items-start gap-0.5">
                        @if($quiz->duration_minutes)
                        <span class="font-bold text-gray-700 text-sm">{{ $quiz->duration_minutes }}min</span>
                        @else
                        <span class="font-bold text-gray-700 text-sm">∞</span>
                        @endif
                        <span class="text-[10px]">durée</span>
                    </div>
                </div>

                {{-- Tentatives --}}
                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-400">Tentatives</span>
                        <span class="font-semibold {{ $remaining > 0 ? 'text-gray-600' : 'text-red-500' }}">
                            {{ $attempts }} / {{ $quiz->max_attempts }}
                        </span>
                    </div>
                    <div class="attempt-bar">
                        <div class="attempt-fill" style="width:{{ ($attempts/$quiz->max_attempts)*100 }}%;
                             background:{{ $passed ? 'linear-gradient(90deg,#1a8a47,#25c26e)' : ($remaining > 0 ? 'linear-gradient(90deg,#3b82f6,#6366f1)' : 'linear-gradient(90deg,#ef4444,#f87171)') }}">
                        </div>
                    </div>
                </div>

                {{-- Status badge --}}
                @if($passed)
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl mb-4"
                     style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.18)">
                    <span class="text-base">🏆</span>
                    <div class="flex-1">
                        <div class="text-xs font-bold" style="color:#1a8a47">Quiz réussi !</div>
                        <div class="text-[10px] text-gray-400">Meilleur score : {{ $bestScore }}%</div>
                    </div>
                </div>
                @elseif($attempts > 0 && !$canAttempt)
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl mb-4"
                     style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.15)">
                    <span class="text-base">❌</span>
                    <div>
                        <div class="text-xs font-bold text-red-600">Tentatives épuisées</div>
                        <div class="text-[10px] text-gray-400">Score : {{ $bestScore }}% (requis : {{ $quiz->passing_score }}%)</div>
                    </div>
                </div>
                @endif

                {{-- CTA --}}
                <div class="mt-auto flex gap-2">
                    @if($canAttempt)
                    <a href="{{ route('student.quizzes.show', $quiz) }}"
                       class="flex-1 py-2.5 rounded-xl text-xs font-bold text-white text-center transition-all hover:-translate-y-0.5"
                       style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 4px 12px rgba(37,194,110,0.25)">
                        {{ $attempts > 0 ? '🔄 Réessayer' : '🚀 Commencer' }}
                    </a>
                    @else
                    <button disabled
                            class="flex-1 py-2.5 rounded-xl text-xs font-bold text-center cursor-not-allowed"
                            style="background:rgba(0,0,0,0.05);color:#9ca3af">
                        🔒 Non disponible
                    </button>
                    @endif

                    @if($attempts > 0)
                    {{-- Voir le dernier résultat --}}
                    @php $lastAttempt = $quiz->attemptsForUser(auth()->id())->latest()->first(); @endphp
                    @if($lastAttempt)
                    <a href="{{ route('student.quizzes.result', $lastAttempt) }}"
                       class="py-2.5 px-3 rounded-xl text-xs font-medium transition-colors hover:bg-gray-100"
                       style="background:rgba(0,0,0,0.04);color:#6b7280" title="Voir les résultats">
                        📊
                    </a>
                    @endif
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

@endif

@endsection