<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $quiz->title }} — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        :root { --green:#25c26e; --green-mid:#1a8a47; --dark:#0a1a0f; --gold:#e8b84b; }
        * { box-sizing:border-box; }
        body { font-family:'Outfit',sans-serif; background:#f4f7f4; min-height:100vh; margin:0; }

        /* ── Topbar ── */
        .quiz-topbar {
            position:fixed; top:0; left:0; right:0; z-index:50; height:58px;
            display:flex; align-items:center; justify-content:space-between;
            padding:0 24px; background:#fff; border-bottom:1px solid rgba(0,0,0,0.07);
            box-shadow:0 1px 12px rgba(0,0,0,0.05);
        }

        /* ── Barre de progression ── */
        .quiz-progress-track { position:fixed; top:58px; left:0; right:0; z-index:49; height:4px; background:rgba(0,0,0,0.07); }
        .quiz-progress-fill { height:100%; background:linear-gradient(90deg,var(--green-mid),var(--green)); transition:width .4s ease; border-radius:0 2px 2px 0; }

        /* ── Timer ── */
        .timer-circle { position:relative; width:52px; height:52px; }
        .timer-circle svg { transform:rotate(-90deg); }
        .timer-track { fill:none; stroke:rgba(0,0,0,0.08); stroke-width:4; }
        .timer-fill  { fill:none; stroke-width:4; stroke-linecap:round; transition:stroke-dashoffset .9s linear, stroke .3s; }
        .timer-text  { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:700; }

        /* ── Carte question ── */
        .question-card {
            background:#fff; border-radius:24px; padding:36px;
            box-shadow:0 4px 24px rgba(0,0,0,0.06); border:1px solid rgba(0,0,0,0.05);
            transition:all .3s;
        }

        /* ── Options de réponse ── */
        .answer-option {
            display:flex; align-items:center; gap:14px;
            padding:14px 18px; border-radius:14px;
            border:2px solid rgba(0,0,0,0.08); background:#fff;
            cursor:pointer; transition:all .2s; user-select:none;
        }
        .answer-option:hover { border-color:var(--green); background:rgba(37,194,110,0.04); transform:translateX(4px); }
        .answer-option.selected { border-color:var(--green-mid); background:rgba(37,194,110,0.08); }
        .answer-option.selected .opt-marker { background:var(--green-mid); border-color:var(--green-mid); color:#fff; }

        .opt-marker {
            width:32px; height:32px; border-radius:50%; border:2px solid rgba(0,0,0,0.15);
            display:flex; align-items:center; justify-content:center;
            font-size:.75rem; font-weight:700; color:#9ca3af; flex-shrink:0;
            transition:all .2s;
        }
        .answer-option.selected-multi { border-color:#3b82f6; background:rgba(59,130,246,0.06); }
        .answer-option.selected-multi .opt-marker { background:#3b82f6; border-color:#3b82f6; color:#fff; }

        /* ── Navigation ── */
        .nav-btn {
            display:flex; align-items:center; gap:8px; padding:12px 22px; border-radius:14px;
            font-size:.875rem; font-weight:600; border:none; cursor:pointer; transition:all .2s;
        }
        .btn-prev { background:rgba(0,0,0,0.06); color:#6b7280; }
        .btn-prev:hover { background:rgba(0,0,0,0.1); color:#374151; }
        .btn-next { background:linear-gradient(135deg,var(--green-mid),var(--green)); color:#fff; box-shadow:0 4px 14px rgba(37,194,110,0.3); }
        .btn-next:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(37,194,110,0.4); }
        .btn-submit { background:linear-gradient(135deg,#7c3aed,#6d28d9); color:#fff; box-shadow:0 4px 14px rgba(124,58,237,0.3); }
        .btn-submit:hover { transform:translateY(-1px); }

        /* ── Pastilles numéros de questions ── */
        .q-dot {
            width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center;
            font-size:.7rem; font-weight:700; cursor:pointer; transition:all .2s; flex-shrink:0;
        }
        .q-dot.answered  { background:var(--green-mid); color:#fff; }
        .q-dot.current   { background:var(--dark); color:#fff; box-shadow:0 0 0 3px rgba(37,194,110,0.3); }
        .q-dot.unanswered{ background:rgba(0,0,0,0.07); color:#9ca3af; }
        .q-dot:hover:not(.current) { transform:scale(1.1); }

        /* ── Modal confirmation ── */
        .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
        .modal-box { background:#fff; border-radius:24px; padding:32px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.2); }

        @keyframes slideIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        .slide-in { animation:slideIn .35s ease both; }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-8px)} 75%{transform:translateX(8px)} }
        .shake { animation:shake .3s ease; }
    </style>
</head>

<body x-data="quizApp()" x-init="init()">

{{-- ── TOPBAR ── --}}
<div class="quiz-topbar">
    <div class="flex items-center gap-3">
        <button @click="confirmQuit = true" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">✕</button>
        <div class="h-4 w-px bg-black/10"></div>
        <div>
            <div class="text-sm font-bold text-gray-800" style="font-family:'Playfair Display',serif">{{ $quiz->title }}</div>
            <div class="text-xs text-gray-400">Tentative {{ $attemptNumber }} / {{ $quiz->max_attempts }}</div>
        </div>
    </div>
    <div class="flex items-center gap-4">
        {{-- Questions answerd counter --}}
        <div class="hidden sm:flex items-center gap-1.5 text-sm">
            <span class="font-bold text-gray-700" x-text="answeredCount"></span>
            <span class="text-gray-400">/ {{ $quiz->questions->count() }} répondues</span>
        </div>

        {{-- Timer (si durée limitée) --}}
        @if($quiz->duration_minutes)
        <div class="timer-circle">
            <svg width="52" height="52" viewBox="0 0 52 52">
                <circle class="timer-track" cx="26" cy="26" r="20"/>
                <circle class="timer-fill" cx="26" cy="26" r="20"
                        :stroke="timerColor"
                        :stroke-dasharray="timerCirc"
                        :stroke-dashoffset="timerOffset"/>
            </svg>
            <div class="timer-text" :style="'color:'+timerColor" x-text="timerDisplay"></div>
        </div>
        @endif

        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold text-white"
             style="background:linear-gradient(135deg,#1a8a47,#25c26e)">{{ auth()->user()->initials }}</div>
    </div>
</div>

{{-- Barre de progression --}}
<div class="quiz-progress-track">
    <div class="quiz-progress-fill" :style="'width:' + progressPct + '%'"></div>
</div>

{{-- ── LAYOUT PRINCIPAL ── --}}
<div class="pt-20 pb-10 px-4 max-w-3xl mx-auto">

    {{-- ── Pastilles de navigation questions ── --}}
    <div class="flex flex-wrap gap-2 mb-6 p-4 bg-white rounded-2xl border border-black/5 shadow-sm">
        <span class="text-xs text-gray-400 font-medium mr-1 self-center">Questions :</span>
        @foreach($quiz->questions as $q)
        <button @click="goTo({{ $loop->index }})"
                :class="{
                    'answered':  answers[{{ $q->id }}] && answers[{{ $q->id }}].length > 0,
                    'current':   currentIndex === {{ $loop->index }},
                    'unanswered': !answers[{{ $q->id }}] || answers[{{ $q->id }}].length === 0
                }"
                class="q-dot">{{ $loop->iteration }}</button>
        @endforeach
    </div>

    {{-- ── CARTE QUESTION ── --}}
    <form id="quiz-form" method="POST" action="{{ route('student.quizzes.submit', $quiz) }}" @submit.prevent="confirmSubmit = true">
        @csrf
        <input type="hidden" name="time_spent" :value="timeSpent">

        @foreach($quiz->questions as $question)
        <div x-show="currentIndex === {{ $loop->index }}"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="question-card slide-in mb-6">

            {{-- Header question --}}
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0"
                     style="background:linear-gradient(135deg,#1a8a47,#25c26e)">{{ $loop->iteration }}</div>
                <div class="flex-1">
                    <div class="text-xs text-gray-400 font-medium">
                        Question {{ $loop->iteration }} sur {{ $quiz->questions->count() }}
                        @if($question->points > 1) · <span class="font-bold" style="color:#e8b84b">{{ $question->points }} pts</span>@endif
                    </div>
                </div>
                {{-- Type badge --}}
                <span class="text-[10px] font-bold px-2 py-1 rounded-lg"
                      style="background:{{ $question->type === 'multiple' ? 'rgba(59,130,246,0.1)' : 'rgba(37,194,110,0.1)' }};
                             color:{{ $question->type === 'multiple' ? '#2563eb' : '#1a8a47' }}">
                    {{ $question->type === 'single' ? '✦ Choix unique' : ($question->type === 'multiple' ? '✦ Choix multiple' : '✦ Vrai / Faux') }}
                </span>
            </div>

            {{-- Texte de la question --}}
            <p class="text-gray-800 text-base font-semibold leading-relaxed mb-6"
               style="font-family:'Playfair Display',serif">
                {{ $question->question }}
            </p>

            {{-- Réponses --}}
            <div class="space-y-3">
                @foreach($question->answers as $answer)
                @php $letter = chr(64 + $loop->iteration); @endphp

                @if($question->type === 'single' || $question->type === 'true_false')
                {{-- Radio --}}
                <label class="answer-option"
                       :class="{ 'selected': answers[{{ $question->id }}] && answers[{{ $question->id }}][0] == {{ $answer->id }} }"
                       @click="selectAnswer({{ $question->id }}, {{ $answer->id }}, 'single')">
                    <div class="opt-marker">{{ $letter }}</div>
                    <span class="text-sm text-gray-700 flex-1">{{ $answer->answer_text }}</span>
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                         :class="answers[{{ $question->id }}] && answers[{{ $question->id }}][0] == {{ $answer->id }}
                             ? 'border-green-600 bg-green-600' : 'border-gray-200'">
                        <div class="w-2 h-2 rounded-full bg-white"
                             x-show="answers[{{ $question->id }}] && answers[{{ $question->id }}][0] == {{ $answer->id }}"></div>
                    </div>
                    <input type="radio" name="answers[{{ $question->id }}][]"
                           value="{{ $answer->id }}" class="sr-only"
                           :checked="answers[{{ $question->id }}] && answers[{{ $question->id }}][0] == {{ $answer->id }}">
                </label>

                @else
                {{-- Checkbox multiple --}}
                <label class="answer-option"
                       :class="{ 'selected-multi': answers[{{ $question->id }}] && answers[{{ $question->id }}].includes({{ $answer->id }}) }"
                       @click="selectAnswer({{ $question->id }}, {{ $answer->id }}, 'multiple')">
                    <div class="opt-marker">{{ $letter }}</div>
                    <span class="text-sm text-gray-700 flex-1">{{ $answer->answer_text }}</span>
                    <div class="w-5 h-5 rounded flex items-center justify-center shrink-0 border-2 transition-all"
                         :class="answers[{{ $question->id }}] && answers[{{ $question->id }}].includes({{ $answer->id }})
                             ? 'border-blue-500 bg-blue-500' : 'border-gray-200'">
                        <svg x-show="answers[{{ $question->id }}] && answers[{{ $question->id }}].includes({{ $answer->id }})"
                             class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <input type="checkbox" name="answers[{{ $question->id }}][]"
                           value="{{ $answer->id }}" class="sr-only"
                           :checked="answers[{{ $question->id }}] && answers[{{ $question->id }}].includes({{ $answer->id }})">
                </label>
                @endif
                @endforeach
            </div>

            {{-- Hint type multiple --}}
            @if($question->type === 'multiple')
            <p class="text-xs text-blue-500 mt-3 flex items-center gap-1.5">
                <span>ℹ</span> Plusieurs réponses peuvent être correctes
            </p>
            @endif
        </div>
        @endforeach

        {{-- ── Navigation bas ── --}}
        <div class="flex items-center justify-between bg-white rounded-2xl px-6 py-4 border border-black/5 shadow-sm">
            <button type="button" @click="prev()"
                    x-show="currentIndex > 0"
                    class="nav-btn btn-prev">
                ← Précédente
            </button>
            <div x-show="currentIndex === 0"></div>

            {{-- Indicateur central --}}
            <div class="text-sm text-gray-400 font-medium">
                <span x-text="currentIndex + 1"></span> / {{ $quiz->questions->count() }}
            </div>

            <div class="flex gap-3">
                {{-- Bouton Suivant --}}
                <button type="button" @click="next()"
                        x-show="currentIndex < {{ $quiz->questions->count() - 1 }}"
                        :class="{ 'shake': !answers[currentQuestion?.id] }"
                        class="nav-btn btn-next">
                    Suivante →
                </button>

                {{-- Bouton Soumettre (dernière question) --}}
                <button type="button"
                        x-show="currentIndex === {{ $quiz->questions->count() - 1 }}"
                        @click="confirmSubmit = true"
                        class="nav-btn btn-submit">
                    ✓ Terminer le quiz
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ── MODAL CONFIRMATION SOUMISSION ── --}}
<div x-show="confirmSubmit" class="modal-overlay" x-transition>
    <div class="modal-box slide-in">
        <div class="text-center mb-6">
            <div class="text-5xl mb-3">📤</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2" style="font-family:'Playfair Display',serif">
                Soumettre le quiz ?
            </h3>
            <p class="text-sm text-gray-500">
                Vous avez répondu à <strong x-text="answeredCount" class="text-gray-700"></strong>
                question(s) sur {{ $quiz->questions->count() }}.
                @if($quiz->max_attempts > 1)
                <br><span class="text-xs">Vous aurez encore <span x-text="{{ $quiz->max_attempts }} - {{ $attemptNumber - 1 }} - 1"></span> tentative(s) restante(s) après.</span>
                @endif
            </p>
        </div>

        {{-- Résumé non répondues --}}
        <div x-show="unansweredCount > 0"
             class="px-4 py-3 rounded-xl mb-5 text-sm"
             style="background:rgba(232,184,75,0.08);border:1px solid rgba(232,184,75,0.2);color:#b8860b">
            ⚠ <span x-text="unansweredCount"></span> question(s) sans réponse.
        </div>

        <div class="flex gap-3">
            <button @click="confirmSubmit = false"
                    class="flex-1 py-3 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                Continuer
            </button>
            <button @click="submitQuiz()"
                    class="flex-1 py-3 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
                    style="background:linear-gradient(135deg,#7c3aed,#6d28d9);box-shadow:0 4px 14px rgba(124,58,237,0.3)">
                Soumettre →
            </button>
        </div>
    </div>
</div>

{{-- ── MODAL QUITTER ── --}}
<div x-show="confirmQuit" class="modal-overlay" x-transition>
    <div class="modal-box slide-in">
        <div class="text-center mb-6">
            <div class="text-5xl mb-3">🚪</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2" style="font-family:'Playfair Display',serif">Quitter le quiz ?</h3>
            <p class="text-sm text-gray-500">Vos réponses ne seront pas sauvegardées et cette tentative sera comptée.</p>
        </div>
        <div class="flex gap-3">
            <button @click="confirmQuit = false"
                    class="flex-1 py-3 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                Rester
            </button>
            <a href="{{ route('student.quizzes.index') }}"
               class="flex-1 py-3 rounded-xl text-sm font-semibold text-white text-center"
               style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                Quitter
            </a>
        </div>
    </div>
</div>

<script>
function quizApp() {
    return {
        currentIndex: 0,
        answers: {},
        confirmSubmit: false,
        confirmQuit: false,
        timeSpent: 0,
        timerSeconds: {{ $quiz->duration_minutes ? $quiz->duration_minutes * 60 : 0 }},
        timerTotal:   {{ $quiz->duration_minutes ? $quiz->duration_minutes * 60 : 0 }},
        timerCirc: 2 * Math.PI * 20,

        get currentQuestion() {
            return this.allQuestions[this.currentIndex];
        },
        get answeredCount() {
            return Object.values(this.answers).filter(a => a && a.length > 0).length;
        },
        get unansweredCount() {
            return {{ $quiz->questions->count() }} - this.answeredCount;
        },
        get progressPct() {
            return Math.round((this.currentIndex / {{ $quiz->questions->count() }}) * 100);
        },
        get timerDisplay() {
            if (!this.timerTotal) return '';
            const m = Math.floor(this.timerSeconds / 60);
            const s = this.timerSeconds % 60;
            return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        },
        get timerOffset() {
            if (!this.timerTotal) return 0;
            return this.timerCirc * (1 - this.timerSeconds / this.timerTotal);
        },
        get timerColor() {
            const ratio = this.timerSeconds / this.timerTotal;
            if (ratio > .5) return '#25c26e';
            if (ratio > .25) return '#e8b84b';
            return '#ef4444';
        },

        allQuestions: @json($quiz->questions->pluck('id')),

        init() {
            // Initialiser les réponses vides
            @foreach($quiz->questions as $q)
            this.answers[{{ $q->id }}] = [];
            @endforeach

            // Timer si durée limitée
            if (this.timerTotal > 0) {
                const interval = setInterval(() => {
                    this.timerSeconds--;
                    this.timeSpent++;
                    if (this.timerSeconds <= 0) {
                        clearInterval(interval);
                        this.submitQuiz();
                    }
                }, 1000);
            } else {
                // Tracker le temps passé même sans limite
                setInterval(() => this.timeSpent++, 1000);
            }
        },

        selectAnswer(questionId, answerId, type) {
            if (type === 'single') {
                this.answers[questionId] = [answerId];
            } else {
                const current = this.answers[questionId] || [];
                const idx = current.indexOf(answerId);
                if (idx === -1) {
                    this.answers[questionId] = [...current, answerId];
                } else {
                    this.answers[questionId] = current.filter(id => id !== answerId);
                }
            }
        },

        goTo(index) {
            this.currentIndex = index;
        },
        prev() {
            if (this.currentIndex > 0) this.currentIndex--;
        },
        next() {
            if (this.currentIndex < {{ $quiz->questions->count() - 1 }}) this.currentIndex++;
        },

        async submitQuiz() {
            this.confirmSubmit = false;

            // Construire le FormData à partir des réponses
            const form = document.getElementById('quiz-form');
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('time_spent', this.timeSpent);

            for (const [qId, aIds] of Object.entries(this.answers)) {
                for (const aId of aIds) {
                    formData.append(`answers[${qId}][]`, aId);
                }
            }

            const response = await fetch('{{ route('student.quizzes.submit', $quiz) }}', {
                method: 'POST',
                body: formData,
            });

            if (response.redirected) {
                window.location.href = response.url;
            } else {
                form.submit();
            }
        },
    };
}
</script>

</body>
</html>