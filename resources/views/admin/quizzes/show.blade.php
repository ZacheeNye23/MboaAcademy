
@extends('admin.layouts.app')

@section('title', $quiz->title)
@section('page-title', 'Détail du quiz')
@section('page-subtitle', $quiz->title)

@section('topbar-actions')
<div class="flex gap-2">
    <a href="{{ route('admin.quizzes.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-colors"
       style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
        ← Retour
    </a>
    <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}"
          onsubmit="return confirm('Supprimer ce quiz et toutes ses tentatives ?')">
        @csrf @method('DELETE')
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold"
                style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2)">
            🗑 Supprimer
        </button>
    </form>
</div>
@endsection

@push('styles')
<style>
    .section-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:18px;overflow:hidden; }
    .section-header { padding:16px 22px;border-bottom:1px solid rgba(255,255,255,0.05); }

    .stat-box { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:14px;padding:16px;text-align:center; }

    /* Question card */
    .question-card { background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:14px;padding:18px;margin-bottom:10px; }
    .answer-row { display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:10px;margin-top:6px; }
    .answer-correct { background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.18); }
    .answer-wrong   { background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06); }

    /* Tentatives table */
    .attempt-row { display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,0.04);transition:background .15s; }
    .attempt-row:hover { background:rgba(255,255,255,0.02); }
    .attempt-row:last-child { border-bottom:none; }

    /* Score badge */
    .score-pill { display:inline-flex;align-items:center;padding:3px 10px;border-radius:100px;font-size:.72rem;font-weight:700; }
    .score-pass { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
    .score-fail { background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2); }

    .prog-bar  { height:4px;border-radius:2px;background:rgba(255,255,255,0.07);overflow:hidden; }
    .prog-fill { height:100%;border-radius:2px; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}.d3{animation-delay:.12s}.d4{animation-delay:.16s}
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-xs mb-6 anim d1" style="color:rgba(255,255,255,0.35)">
    <a href="{{ route('admin.quizzes.index') }}" class="hover:text-white transition-colors">← Quiz</a>
    <span>/</span>
    <span class="text-white truncate max-w-xs">{{ $quiz->title }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── COLONNE GAUCHE : Infos + Stats ── --}}
    <div class="space-y-5">

        {{-- Infos quiz --}}
        <div class="glass p-5 anim d1">
            <h3 class="text-sm font-bold text-white mb-4" style="font-family:'Playfair Display',serif">
                ℹ Informations
            </h3>
            <div class="space-y-3">
                @foreach([
                    ['📚', 'Cours',          $quiz->course->title ?? '—'],
                    ['📖', 'Leçon',          $quiz->lesson->title ?? 'Quiz de cours'],
                    ['❓', 'Questions',       $quiz->questions->count().' questions'],
                    ['🎯', 'Score requis',   $quiz->passing_score.'%'],
                    ['🔄', 'Max tentatives', $quiz->max_attempts.' tentative(s)'],
                    ['⏱', 'Durée',          $quiz->duration_minutes ? $quiz->duration_minutes.' min' : 'Sans limite'],
                    ['✅', 'Correction',     $quiz->show_answers ? 'Visible' : 'Cachée'],
                    ['📅', 'Créé le',        $quiz->created_at->translatedFormat('d F Y')],
                ] as [$icon, $label, $value])
                <div class="flex items-start gap-2.5">
                    <span class="text-sm shrink-0 mt-0.5">{{ $icon }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="text-[10px] uppercase tracking-wide font-bold" style="color:rgba(255,255,255,0.3)">{{ $label }}</div>
                        <div class="text-sm font-medium text-white mt-0.5 truncate">{{ $value }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Formateur --}}
            <div class="mt-4 pt-4 border-t border-white/5 flex items-center gap-2.5">
                @php $ac = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46']; @endphp
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                     style="background:{{ $ac[$quiz->course->user_id % count($ac)] ?? '#1a8a47' }}">
                    {{ $quiz->course->teacher->initials ?? '??' }}
                </div>
                <div>
                    <div class="text-xs font-semibold text-white">{{ $quiz->course->teacher->full_name ?? '—' }}</div>
                    <div class="text-[10px]" style="color:rgba(255,255,255,0.35)">Formateur</div>
                </div>
            </div>
        </div>

        {{-- Stats globales --}}
        <div class="grid grid-cols-2 gap-3 anim d2">
            @foreach([
                ['🎯', $stats['total_attempts'],  'Tentatives',   '#a78bfa'],
                ['✅', $stats['pass_rate'].'%',   'Taux réussite','#25c26e'],
                ['📊', $stats['avg_score'].'%',   'Score moyen',  '#3b82f6'],
                ['⭐', $stats['best_score'].'%',  'Meilleur score','#e8b84b'],
            ] as [$icon, $val, $label, $color])
            <div class="stat-box">
                <div class="text-xl mb-1.5">{{ $icon }}</div>
                <div class="text-lg font-bold" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
                <div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        {{-- Distribution des scores --}}
        <div class="glass p-5 anim d3">
            <h3 class="text-sm font-bold text-white mb-4" style="font-family:'Playfair Display',serif">
                📊 Distribution scores
            </h3>
            <div class="space-y-2.5">
                @foreach([
                    ['90-100%', $stats['score_dist']['excellent'], '#25c26e'],
                    ['70-89%',  $stats['score_dist']['good'],      '#3b82f6'],
                    ['50-69%',  $stats['score_dist']['average'],   '#e8b84b'],
                    ['0-49%',   $stats['score_dist']['poor'],      '#f87171'],
                ] as [$range, $count, $color])
                @php $pct = $stats['total_attempts'] > 0 ? round($count / $stats['total_attempts'] * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span style="color:rgba(255,255,255,0.5)">{{ $range }}</span>
                        <span class="font-bold" style="color:{{ $color }}">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    <div class="prog-bar">
                        <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── COLONNE DROITE ── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Questions du quiz --}}
        <div class="section-card anim d2">
            <div class="section-header flex items-center justify-between">
                <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                    ❓ Questions ({{ $quiz->questions->count() }})
                </h3>
                <div class="flex gap-2 text-xs" style="color:rgba(255,255,255,0.35)">
                    <span>{{ $quiz->questions->sum('points') }} pts total</span>
                </div>
            </div>
            <div class="p-5">
                @forelse($quiz->questions as $question)
                <div class="question-card">
                    {{-- Header question --}}
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0"
                             style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
                            {{ $loop->iteration }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold"
                                      style="background:rgba(167,139,250,0.1);color:#a78bfa">
                                    {{ ['single'=>'Choix unique','multiple'=>'Choix multiple','true_false'=>'Vrai/Faux'][$question->type] }}
                                </span>
                                <span class="text-[10px]" style="color:rgba(255,255,255,0.35)">
                                    {{ $question->points }} pt(s)
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-white leading-snug">
                                {{ $question->question }}
                            </p>
                        </div>
                    </div>

                    {{-- Réponses --}}
                    <div class="pl-10 space-y-1.5">
                        @foreach($question->answers as $answer)
                        <div class="answer-row {{ $answer->is_correct ? 'answer-correct' : 'answer-wrong' }}">
                            @if($answer->is_correct)
                                <span class="text-sm shrink-0" style="color:#25c26e">✓</span>
                            @else
                                <span class="text-sm shrink-0" style="color:rgba(255,255,255,0.2)">○</span>
                            @endif
                            <span class="text-sm {{ $answer->is_correct ? 'font-semibold' : '' }}"
                                  style="color:{{ $answer->is_correct ? '#25c26e' : 'rgba(255,255,255,0.55)' }}">
                                {{ $answer->answer_text }}
                            </span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Explication --}}
                    @if($question->explanation)
                    <div class="mt-3 pl-10">
                        <div class="flex items-start gap-2 px-3 py-2 rounded-lg text-xs"
                             style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.15)">
                            <span class="shrink-0" style="color:#60a5fa">💡</span>
                            <p style="color:rgba(148,192,255,0.8)">{{ $question->explanation }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Stats réponse --}}
                    @if(isset($questionStats[$question->id]))
                    @php $qs = $questionStats[$question->id]; @endphp
                    <div class="mt-3 pl-10 flex items-center gap-3 text-xs" style="color:rgba(255,255,255,0.3)">
                        <span>{{ $qs['correct'] }} bonne(s) réponse(s)</span>
                        <span>·</span>
                        <span>{{ $qs['total'] }} réponse(s)</span>
                        <span>·</span>
                        <span style="color:{{ $qs['rate'] >= 70 ? '#25c26e' : '#f87171' }}">{{ $qs['rate'] }}% de réussite</span>
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-sm text-center py-6" style="color:rgba(255,255,255,0.3)">
                    Aucune question pour ce quiz.
                </p>
                @endforelse
            </div>
        </div>

        {{-- Dernières tentatives --}}
        <div class="section-card anim d3">
            <div class="section-header flex items-center justify-between">
                <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                    🕐 Dernières tentatives ({{ $stats['total_attempts'] }})
                </h3>
            </div>

            {{-- En-tête --}}
            <div class="flex items-center gap-3 px-5 py-2 border-b border-white/5"
                 style="color:rgba(255,255,255,0.2);font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08rem">
                <span class="flex-1">Apprenant</span>
                <span class="w-20 text-center">Score</span>
                <span class="w-20 text-center hidden md:block">Tentative</span>
                <span class="w-20 text-center hidden lg:block">Temps</span>
                <span class="w-24 text-right">Date</span>
            </div>

            @php $ac = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e']; @endphp
            @forelse($recentAttempts as $attempt)
            <div class="attempt-row">
                <div class="flex-1 flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                         style="background:{{ $ac[$attempt->user_id % count($ac)] }}">
                        {{ $attempt->user->initials }}
                    </div>
                    <span class="text-sm font-medium text-white truncate">{{ $attempt->user->full_name }}</span>
                </div>

                <div class="w-20 text-center">
                    <span class="score-pill {{ $attempt->passed ? 'score-pass' : 'score-fail' }}">
                        {{ $attempt->score }}%
                    </span>
                </div>

                <div class="w-20 text-center hidden md:block">
                    <span class="text-xs" style="color:rgba(255,255,255,0.45)">
                        N° {{ $attempt->attempt_number }}
                    </span>
                </div>

                <div class="w-20 text-center hidden lg:block">
                    @if($attempt->time_spent)
                    <span class="text-xs" style="color:rgba(255,255,255,0.45)">
                        {{ intdiv($attempt->time_spent, 60) }}m{{ $attempt->time_spent % 60 }}s
                    </span>
                    @else
                    <span class="text-xs" style="color:rgba(255,255,255,0.2)">—</span>
                    @endif
                </div>

                <div class="w-24 text-right">
                    <span class="text-[10px]" style="color:rgba(255,255,255,0.25)">
                        {{ $attempt->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <div class="text-3xl mb-2">📝</div>
                <p class="text-sm" style="color:rgba(255,255,255,0.3)">Aucune tentative pour ce quiz.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection