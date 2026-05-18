@extends('admin.layouts.app')

@section('title', 'Quiz & Exercices')
@section('page-title', 'Quiz & Exercices')
@section('page-subtitle', number_format($quizzes->total()) . ' quiz sur la plateforme')

@push('styles')
<style>
    /* ── Recherche ── */
    .search-bar {
        background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);
        border-radius:12px;padding:10px 16px 10px 42px;color:#fff;
        font-family:'Outfit',sans-serif;font-size:.875rem;outline:none;
        transition:all .2s;width:100%;
    }
    .search-bar::placeholder { color:rgba(255,255,255,0.22); }
    .search-bar:focus { border-color:rgba(167,139,250,0.4);background:rgba(255,255,255,0.06); }

    /* ── Filter select ── */
    .f-select {
        background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);
        border-radius:12px;padding:10px 14px;color:rgba(255,255,255,0.7);
        font-family:'Outfit',sans-serif;font-size:.8rem;outline:none;cursor:pointer;
    }

    /* ── Quiz card ── */
    .quiz-card {
        background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);
        border-radius:18px;padding:20px;transition:all .25s;
        display:flex;flex-direction:column;gap:14px;
    }
    .quiz-card:hover { transform:translateY(-2px);border-color:rgba(167,139,250,0.2);box-shadow:0 8px 24px rgba(0,0,0,0.2); }

    /* ── Stat chip ── */
    .stat-chip {
        display:flex;flex-direction:column;align-items:center;gap:2px;
        padding:10px 14px;border-radius:12px;
        background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);
        text-align:center;min-width:72px;
    }

    /* ── Type badge ── */
    .type-badge {
        display:inline-flex;align-items:center;gap:4px;
        padding:2px 8px;border-radius:100px;font-size:.65rem;font-weight:700;
    }
    .type-course  { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
    .type-lesson  { background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2); }

    /* ── Diff badge ── */
    .diff-easy   { background:rgba(37,194,110,0.08);color:#25c26e; }
    .diff-medium { background:rgba(232,184,75,0.08);color:#e8b84b; }
    .diff-hard   { background:rgba(239,68,68,0.08);color:#f87171; }

    /* ── Prog bar ── */
    .prog-bar  { height:4px;border-radius:2px;background:rgba(255,255,255,0.07);overflow:hidden; }
    .prog-fill { height:100%;border-radius:2px; }

    /* ── Action btn ── */
    .act-btn {
        display:inline-flex;align-items:center;gap:5px;
        padding:6px 12px;border-radius:8px;font-size:.72rem;font-weight:600;
        cursor:pointer;transition:all .2s;border:none;text-decoration:none;
    }
    .act-view   { background:rgba(167,139,250,0.1);color:#a78bfa;border:1px solid rgba(167,139,250,0.18); }
    .act-view:hover { background:rgba(167,139,250,0.18); }
    .act-delete { background:rgba(239,68,68,0.08);color:#f87171;border:1px solid rgba(239,68,68,0.15); }
    .act-delete:hover { background:rgba(239,68,68,0.15); }

    @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}.d3{animation-delay:.12s}
    .d4{animation-delay:.16s}.d5{animation-delay:.20s}.d6{animation-delay:.24s}
</style>
@endpush

@section('content')

{{-- ── KPI CARDS ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 anim d1">
    @foreach([
        ['📝', 'Total quiz',         $globalStats['total'],         'sur la plateforme',             '#a78bfa'],
        ['🎯', 'Tentatives totales', $globalStats['total_attempts'],'toutes périodes',               '#3b82f6'],
        ['✅', 'Taux de réussite',   $globalStats['pass_rate'].'%', 'en moyenne',                   '#25c26e'],
        ['📊', 'Score moyen',        $globalStats['avg_score'].'%', 'toutes tentatives confondues',  '#e8b84b'],
    ] as [$icon, $label, $val, $sub, $color])
    <div class="glass p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background:{{ $color }}15">{{ $icon }}</div>
            <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background:{{ $color }}10;color:{{ $color }}">{{ $sub }}</span>
        </div>
        <div class="text-2xl font-bold mb-1" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
        <div class="text-xs" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- ── FILTRES ── --}}
<div class="glass p-4 mb-5 anim d2">
    <form method="GET" action="{{ route('admin.quizzes.index') }}">
        <div class="flex flex-col lg:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm" style="color:rgba(255,255,255,0.28)">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="search-bar" placeholder="Titre du quiz, cours...">
            </div>
            <select name="type" class="f-select" onchange="this.form.submit()">
                <option value=""       style="background:#040a05" {{ !request('type') ? 'selected' : '' }}>Tous types</option>
                <option value="course" style="background:#040a05" {{ request('type')==='course' ? 'selected' : '' }}>📚 Par cours</option>
                <option value="lesson" style="background:#040a05" {{ request('type')==='lesson' ? 'selected' : '' }}>📖 Par leçon</option>
            </select>
            <select name="difficulty" class="f-select" onchange="this.form.submit()">
                <option value=""       style="background:#040a05" {{ !request('difficulty') ? 'selected' : '' }}>Toute difficulté</option>
                <option value="easy"   style="background:#040a05" {{ request('difficulty')==='easy'   ? 'selected' : '' }}>🟢 Facile (≤59%)</option>
                <option value="medium" style="background:#040a05" {{ request('difficulty')==='medium' ? 'selected' : '' }}>🟡 Moyen (60-79%)</option>
                <option value="hard"   style="background:#040a05" {{ request('difficulty')==='hard'   ? 'selected' : '' }}>🔴 Difficile (≥80%)</option>
            </select>
            <select name="sort" class="f-select" onchange="this.form.submit()">
                <option value="latest"   style="background:#040a05" {{ request('sort','latest')==='latest'   ? 'selected' : '' }}>Plus récents</option>
                <option value="attempts" style="background:#040a05" {{ request('sort')==='attempts' ? 'selected' : '' }}>+ de tentatives</option>
                <option value="pass_rate"style="background:#040a05" {{ request('sort')==='pass_rate' ? 'selected' : '' }}>Taux réussite</option>
                <option value="score"    style="background:#040a05" {{ request('sort')==='score'    ? 'selected' : '' }}>Score moyen</option>
            </select>
            <button type="submit" class="px-5 py-2 rounded-xl text-sm font-semibold text-white shrink-0"
                    style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
                Filtrer
            </button>
            @if(request()->hasAny(['search','type','difficulty','sort']))
            <a href="{{ route('admin.quizzes.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-medium shrink-0"
               style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.45)">
                ✕
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ── GRILLE QUIZ ── --}}
@if($quizzes->isEmpty())
<div class="glass flex flex-col items-center justify-center py-20 text-center">
    <div class="text-5xl mb-4">📝</div>
    <h3 class="text-lg font-bold text-white mb-2" style="font-family:'Playfair Display',serif">
        Aucun quiz trouvé
    </h3>
    <p class="text-sm" style="color:rgba(255,255,255,0.35)">
        Modifiez vos filtres ou les formateurs n'ont pas encore créé de quiz.
    </p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($quizzes as $quiz)
    @php
        $attempts   = $quiz->attempts_count ?? 0;
        $avgScore   = $quiz->avg_score ?? 0;
        $passRate   = $quiz->pass_rate ?? 0;
        $qCount     = $quiz->questions_count ?? 0;
        $diffClass  = $quiz->passing_score >= 80 ? 'diff-hard' : ($quiz->passing_score >= 60 ? 'diff-medium' : 'diff-easy');
        $diffLabel  = $quiz->passing_score >= 80 ? '🔴 Difficile' : ($quiz->passing_score >= 60 ? '🟡 Moyen' : '🟢 Facile');
    @endphp

    <div class="quiz-card anim d{{ min($loop->iteration, 6) }}">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                {{-- Badges type + difficulté --}}
                <div class="flex flex-wrap gap-1.5 mb-2">
                    @if($quiz->course_id && !$quiz->lesson_id)
                        <span class="type-badge type-course">📚 Cours</span>
                    @elseif($quiz->lesson_id)
                        <span class="type-badge type-lesson">📖 Leçon</span>
                    @endif
                    <span class="type-badge {{ $diffClass }}">{{ $diffLabel }}</span>
                </div>
                <h3 class="text-sm font-bold text-white leading-snug line-clamp-2"
                    style="font-family:'Playfair Display',serif">
                    {{ $quiz->title }}
                </h3>
                <p class="text-xs mt-1 truncate" style="color:rgba(255,255,255,0.35)">
                    📚 {{ $quiz->course->title ?? '—' }}
                    @if($quiz->lesson) · 📖 {{ $quiz->lesson->title }} @endif
                </p>
            </div>

            {{-- Score moyen (mini ring) --}}
            @php $circ = 2*M_PI*16; $offset = $circ*(1-$avgScore/100); @endphp
            <div class="relative shrink-0" style="width:42px;height:42px">
                <svg width="42" height="42" viewBox="0 0 42 42">
                    <circle cx="21" cy="21" r="16" fill="rgba(0,0,0,0.2)" stroke="rgba(255,255,255,0.07)" stroke-width="4"/>
                    <circle cx="21" cy="21" r="16" fill="none"
                            stroke="{{ $avgScore >= 70 ? '#25c26e' : ($avgScore >= 50 ? '#e8b84b' : '#f87171') }}"
                            stroke-width="4" stroke-linecap="round"
                            stroke-dasharray="{{ $circ }}"
                            stroke-dashoffset="{{ $offset }}"
                            style="transform:rotate(-90deg);transform-origin:center"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-[9px] font-black text-white">{{ $avgScore }}%</span>
                </div>
            </div>
        </div>

        {{-- Stats chips ── --}}
        <div class="flex gap-2">
            <div class="stat-chip flex-1">
                <span class="text-base">❓</span>
                <span class="text-sm font-bold text-white">{{ $qCount }}</span>
                <span class="text-[10px]" style="color:rgba(255,255,255,0.35)">questions</span>
            </div>
            <div class="stat-chip flex-1">
                <span class="text-base">🎯</span>
                <span class="text-sm font-bold text-white">{{ $attempts }}</span>
                <span class="text-[10px]" style="color:rgba(255,255,255,0.35)">tentatives</span>
            </div>
            <div class="stat-chip flex-1">
                <span class="text-base">✅</span>
                <span class="text-sm font-bold" style="color:#25c26e">{{ $passRate }}%</span>
                <span class="text-[10px]" style="color:rgba(255,255,255,0.35)">réussite</span>
            </div>
            @if($quiz->duration_minutes)
            <div class="stat-chip flex-1">
                <span class="text-base">⏱</span>
                <span class="text-sm font-bold text-white">{{ $quiz->duration_minutes }}m</span>
                <span class="text-[10px]" style="color:rgba(255,255,255,0.35)">durée</span>
            </div>
            @endif
        </div>

        {{-- Barre de réussite ── --}}
        <div>
            <div class="flex justify-between text-[10px] mb-1" style="color:rgba(255,255,255,0.3)">
                <span>Taux de réussite</span>
                <span class="font-bold" style="color:#25c26e">{{ $passRate }}%</span>
            </div>
            <div class="prog-bar">
                <div class="prog-fill"
                     style="width:{{ $passRate }}%;background:{{ $passRate >= 70 ? 'linear-gradient(90deg,#1a8a47,#25c26e)' : ($passRate >= 40 ? 'linear-gradient(90deg,#b8860b,#e8b84b)' : 'linear-gradient(90deg,#dc2626,#f87171)') }}">
                </div>
            </div>
        </div>

        {{-- Paramètres --}}
        <div class="flex items-center gap-3 text-xs" style="color:rgba(255,255,255,0.35)">
            <span>Score requis : <strong style="color:rgba(255,255,255,0.65)">{{ $quiz->passing_score }}%</strong></span>
            <span>·</span>
            <span>Max : <strong style="color:rgba(255,255,255,0.65)">{{ $quiz->max_attempts }} tentative(s)</strong></span>
            @if($quiz->show_answers)
            <span>·</span>
            <span style="color:#25c26e">✓ Correction visible</span>
            @endif
        </div>

        {{-- Footer : formateur + actions ── --}}
        <div class="flex items-center justify-between pt-2 border-t border-white/5">
            <div class="flex items-center gap-2">
                @php $ac = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46']; @endphp
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold text-white shrink-0"
                     style="background:{{ $ac[$quiz->course->user_id % count($ac)] ?? '#1a8a47' }}">
                    {{ $quiz->course->teacher->initials ?? '??' }}
                </div>
                <span class="text-xs truncate max-w-[100px]" style="color:rgba(255,255,255,0.4)">
                    {{ $quiz->course->teacher->full_name ?? '—' }}
                </span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="act-btn act-view">
                    👁 Détail
                </a>
                <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}"
                      onsubmit="return confirm('Supprimer ce quiz et toutes ses tentatives ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="act-btn act-delete">🗑</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $quizzes->withQueryString()->links() }}
</div>
@endif

@endsection