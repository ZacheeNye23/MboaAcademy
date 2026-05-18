@extends('student.layouts.app')

@section('title', 'Résultats — ' . $attempt->quiz->title)
@section('page-title', 'Résultats du quiz')
@section('page-subtitle', $attempt->quiz->title)

@push('styles')
<style>
    .result-hero {
        border-radius: 24px; padding: 36px; text-align: center;
        position: relative; overflow: hidden; margin-bottom: 24px;
    }
    .result-hero.passed {
        background: linear-gradient(135deg, #0d5c2e, #1a8a47);
    }
    .result-hero.failed {
        background: linear-gradient(135deg, #1f2937, #374151);
    }
    .result-hero::before {
        content: ''; position: absolute; inset: 0;
        background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.04) 0, rgba(255,255,255,0.04) 1px, transparent 1px, transparent 24px);
    }

    .score-ring-big { transform: rotate(-90deg); }

    .answer-review {
        background: #fff; border-radius: 18px; padding: 20px;
        border: 1px solid rgba(0,0,0,0.06); margin-bottom: 12px;
        transition: all .2s;
    }
    .answer-review:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }

    .opt-row { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; margin-bottom: 6px; font-size: .875rem; }
    .opt-correct   { background: rgba(37,194,110,0.08);  border: 1px solid rgba(37,194,110,0.2);  color: #1a8a47; }
    .opt-wrong     { background: rgba(239,68,68,0.07);   border: 1px solid rgba(239,68,68,0.18);  color: #dc2626; }
    .opt-missed    { background: rgba(232,184,75,0.08);  border: 1px solid rgba(232,184,75,0.2);  color: #b8860b; }
    .opt-neutral   { background: rgba(0,0,0,0.03);       border: 1px solid rgba(0,0,0,0.07);      color: #6b7280; }

    .stat-box { background: #fff; border: 1px solid rgba(0,0,0,0.06); border-radius: 16px; padding: 18px; text-align: center; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation: fadeUp .45s ease both; }
    .d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}
    .d4{animation-delay:.2s}.d5{animation-delay:.25s}.d6{animation-delay:.3s}

    @keyframes countUp { from { stroke-dashoffset: var(--full); } to { stroke-dashoffset: var(--target); } }
    .animated-ring { animation: countUp 1.2s ease-out .3s both; }
</style>
@endpush

@section('content')
@php
    $passed      = $attempt->passed;
    $score       = $attempt->score;
    $circ        = 2 * M_PI * 54;
    $offset      = $circ * (1 - $score / 100);
    $answersGiven = $attempt->answers_given ?? [];
    $quiz        = $attempt->quiz;
    $timeMin     = $attempt->time_spent ? intdiv($attempt->time_spent, 60) : null;
    $timeSec     = $attempt->time_spent ? $attempt->time_spent % 60 : null;
@endphp

{{-- ── HERO RÉSULTAT ── --}}
<div class="result-hero {{ $passed ? 'passed' : 'failed' }} anim d1">
    <div class="relative z-10">

        {{-- Emoji résultat --}}
        <div class="text-6xl mb-4">{{ $passed ? '🏆' : '💪' }}</div>

        {{-- Titre --}}
        <h2 class="text-white text-2xl font-black mb-2" style="font-family:'Playfair Display',serif">
            {{ $passed ? 'Félicitations ! Quiz réussi !' : 'Continuez vos efforts !' }}
        </h2>
        <p class="text-white/60 text-sm mb-8 max-w-md mx-auto">
            {{ $passed
                ? 'Vous avez atteint le score requis. Excellent travail !'
                : 'Vous n\'avez pas encore atteint le score minimum. Réessayez !' }}
        </p>

        {{-- Anneau de score --}}
        <div class="flex justify-center mb-6">
            <div class="relative" style="width:140px;height:140px">
                <svg width="140" height="140" viewBox="0 0 140 140">
                    <circle cx="70" cy="70" r="54" fill="rgba(0,0,0,0.2)" stroke="rgba(255,255,255,0.1)" stroke-width="10"/>
                    <circle cx="70" cy="70" r="54" fill="none"
                            stroke="{{ $passed ? '#25c26e' : '#f87171' }}" stroke-width="10"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $circ }}"
                            stroke-dashoffset="{{ $circ }}"
                            class="score-ring-big animated-ring"
                            style="--full:{{ $circ }};--target:{{ $offset }}"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-white text-4xl font-black" style="font-family:'Playfair Display',serif">{{ $score }}%</span>
                    <span class="text-white/50 text-xs">score</span>
                </div>
            </div>
        </div>

        {{-- Mini stats --}}
        <div class="flex justify-center gap-8 text-center">
            <div>
                <div class="text-white text-xl font-bold">{{ $attempt->earned_points }}</div>
                <div class="text-white/50 text-xs">/ {{ $attempt->total_points }} pts</div>
            </div>
            @if($attempt->time_spent)
            <div>
                <div class="text-white text-xl font-bold">{{ $timeMin }}:{{ str_pad($timeSec, 2, '0', STR_PAD_LEFT) }}</div>
                <div class="text-white/50 text-xs">temps</div>
            </div>
            @endif
            <div>
                <div class="text-white text-xl font-bold">{{ $attempt->attempt_number }}</div>
                <div class="text-white/50 text-xs">tentative n°</div>
            </div>
        </div>
    </div>
</div>

{{-- ── STATS DÉTAILLÉES ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 anim d2">
    @php
        $correctCount = collect($answersGiven)->filter(fn($a) => $a['is_correct'])->count();
        $wrongCount   = collect($answersGiven)->filter(fn($a) => !$a['is_correct'])->count();
    @endphp

    @foreach([
        ['✅', 'Bonnes réponses',     $correctCount,              'sur '.$quiz->questions->count().' questions', '#25c26e'],
        ['❌', 'Mauvaises réponses',   $wrongCount,               'à revoir',             '#ef4444'],
        ['🎯', 'Score requis',         $quiz->passing_score.'%',  $passed ? '✓ Atteint' : '✗ Non atteint', $passed ? '#25c26e' : '#ef4444'],
        ['🔄', 'Tentatives restantes', max(0, $quiz->max_attempts - $attempt->attempt_number), 'disponibles',  '#e8b84b'],
    ] as [$icon, $label, $val, $sub, $color])
    <div class="stat-box anim d{{ $loop->iteration }}">
        <div class="text-2xl mb-2">{{ $icon }}</div>
        <div class="text-2xl font-bold mb-1" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
        <div class="text-xs font-semibold text-gray-600">{{ $label }}</div>
        <div class="text-xs text-gray-400">{{ $sub }}</div>
    </div>
    @endforeach
</div>

{{-- ── CORRECTION DÉTAILLÉE ── --}}
@if($quiz->show_answers && !empty($answersGiven))
<div class="anim d3">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-bold text-gray-800" style="font-family:'Playfair Display',serif">
            📋 Correction détaillée
        </h2>
        <span class="text-xs text-gray-400">{{ $correctCount }} / {{ $quiz->questions->count() }} correctes</span>
    </div>

    @foreach($quiz->questions as $question)
    @php
        $qData      = $answersGiven[$question->id] ?? null;
        $isCorrect  = $qData['is_correct'] ?? false;
        $givenIds   = collect($qData['given'] ?? []);
        $correctIds = collect($qData['correct'] ?? []);
    @endphp

    <div class="answer-review anim d{{ min($loop->iteration, 6) }}">
        {{-- Header question --}}
        <div class="flex items-start gap-3 mb-4">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0"
                 style="background:{{ $isCorrect ? 'linear-gradient(135deg,#1a8a47,#25c26e)' : 'linear-gradient(135deg,#ef4444,#dc2626)' }}">
                {{ $isCorrect ? '✓' : '✗' }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold text-gray-400">Q{{ $loop->iteration }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold"
                          style="background:{{ $isCorrect ? 'rgba(37,194,110,0.1)' : 'rgba(239,68,68,0.1)' }};
                                 color:{{ $isCorrect ? '#1a8a47' : '#dc2626' }}">
                        {{ $isCorrect ? '+' . $question->points . ' pt(s)' : '0 pt' }}
                    </span>
                </div>
                <p class="text-sm font-semibold text-gray-800 leading-snug" style="font-family:'Playfair Display',serif">
                    {{ $question->question }}
                </p>
            </div>
        </div>

        {{-- Réponses --}}
        <div class="space-y-2 pl-11">
            @foreach($question->answers as $answer)
            @php
                $wasGiven   = $givenIds->contains($answer->id);
                $isRight    = $correctIds->contains($answer->id);

                $rowClass = match(true) {
                    $wasGiven && $isRight   => 'opt-correct',   // ✓ Bonne réponse cochée
                    $wasGiven && !$isRight  => 'opt-wrong',     // ✗ Mauvaise réponse cochée
                    !$wasGiven && $isRight  => 'opt-missed',    // ⚠ Bonne réponse manquée
                    default                 => 'opt-neutral',   // Réponse neutre
                };

                $rowIcon = match(true) {
                    $wasGiven && $isRight  => '✓',
                    $wasGiven && !$isRight => '✗',
                    !$wasGiven && $isRight => '○',
                    default               => '·',
                };
            @endphp
            <div class="opt-row {{ $rowClass }}">
                <span class="font-bold text-sm shrink-0 w-4">{{ $rowIcon }}</span>
                <span class="text-sm">{{ $answer->answer_text }}</span>
                @if(!$wasGiven && $isRight)
                <span class="ml-auto text-xs font-semibold" style="color:#b8860b">← Bonne réponse</span>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Explication --}}
        @if($question->explanation)
        <div class="mt-4 pl-11">
            <div class="flex items-start gap-2 px-4 py-3 rounded-xl text-sm"
                 style="background:rgba(59,130,246,0.06);border:1px solid rgba(59,130,246,0.15)">
                <span class="text-blue-500 shrink-0">💡</span>
                <p class="text-blue-700 text-xs leading-relaxed">{{ $question->explanation }}</p>
            </div>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- ── ACTIONS ── --}}
<div class="flex flex-wrap gap-3 mt-6 anim d4">
    @if($quiz->canAttempt(auth()->id()))
    <a href="{{ route('student.quizzes.show', $quiz) }}"
       class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
       style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 4px 14px rgba(37,194,110,0.3)">
        🔄 Réessayer le quiz
    </a>
    @endif

    <a href="{{ route('student.quizzes.index') }}"
       class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold transition-colors hover:bg-gray-200"
       style="background:rgba(0,0,0,0.06);color:#374151">
        ← Retour aux quiz
    </a>

    @if($attempt->quiz->course)
    <a href="{{ route('student.courses.learn', $attempt->quiz->course->slug) }}"
       class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5"
       style="background:rgba(59,130,246,0.1);color:#2563eb;border:1px solid rgba(59,130,246,0.2)">
        📚 Retour au cours
    </a>
    @endif
</div>

@endsection