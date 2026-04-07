<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quiz & Exercices — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { font-family:'Outfit',sans-serif; background:#0f1f14; color:#e0ebe2; }
        .sidebar { width:260px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;display:flex;flex-direction:column;background:linear-gradient(180deg,#081409,#0a1a0f);border-right:1px solid rgba(37,194,110,0.08); }
        .main-content { margin-left:260px;min-height:100vh; }
        .nav-item { display:flex;align-items:center;gap:12px;padding:10px 20px;border-radius:12px;color:rgba(255,255,255,0.45);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all 0.2s;margin:2px 12px; }
        .nav-item:hover { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.8); }
        .nav-item.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .nav-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;background:rgba(255,255,255,0.04); }
        .nav-item.active .nav-icon { background:rgba(37,194,110,0.18); }

        .glass { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:18px; }
        .stat-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:16px;padding:18px 20px;transition:all 0.25s; }
        .stat-card:hover { border-color:rgba(37,194,110,0.2);transform:translateY(-2px); }

        .field-label { display:block;font-size:0.72rem;font-weight:600;letter-spacing:.06rem;text-transform:uppercase;color:rgba(255,255,255,0.38);margin-bottom:7px; }
        .field-input { width:100%;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);border-radius:10px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.88rem;padding:10px 14px;transition:all 0.25s;outline:none; }
        .field-input::placeholder { color:rgba(255,255,255,0.22); }
        .field-input:focus { border-color:#25c26e;background:rgba(37,194,110,0.05);box-shadow:0 0 0 3px rgba(37,194,110,0.1); }
        select.field-input option { background:#0f1f14; }
        textarea.field-input { resize:vertical;min-height:80px;line-height:1.6; }

        .btn-primary { display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#1a8a47,#25c26e);border-radius:10px;color:#fff;font-size:0.88rem;font-weight:600;border:none;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif; }
        .btn-primary:hover { transform:translateY(-1px);box-shadow:0 6px 18px rgba(37,194,110,0.3); }
        .btn-ghost { display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:10px;color:rgba(255,255,255,0.6);font-size:0.82rem;font-weight:500;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .btn-ghost:hover { background:rgba(255,255,255,0.08);color:#fff; }
        .btn-danger { display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.18);border-radius:9px;color:#f87171;font-size:0.78rem;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;border-width:1px;border-style:solid; }
        .btn-danger:hover { background:rgba(239,68,68,0.15); }

        .quiz-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:16px;overflow:hidden;transition:all 0.25s; }
        .quiz-card:hover { border-color:rgba(37,194,110,0.2);transform:translateY(-2px); }

        .prog-bar { height:5px;border-radius:3px;background:rgba(255,255,255,0.07);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px;background:linear-gradient(90deg,#1a8a47,#25c26e); }

        .pill { display:inline-flex;align-items:center;padding:3px 10px;border-radius:100px;font-size:0.7rem;font-weight:700; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .anim-1{animation-delay:.05s}.anim-2{animation-delay:.1s}.anim-3{animation-delay:.15s}.anim-4{animation-delay:.2s}

        /* Modal */
        .modal-backdrop { position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.65);display:flex;align-items:center;justify-content:center;padding:20px; }
        .modal-box { background:#0d1f13;border:1px solid rgba(37,194,110,0.15);border-radius:20px;width:100%;max-width:580px;max-height:90vh;overflow-y:auto; }
    </style>
</head>
<body x-data="quizzesPage()">

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>
    <nav style="flex:1;padding:16px 0;overflow-y:auto;">
        <a href="{{ route('teacher.dashboard') }}" class="nav-item"><span class="nav-icon">🏠</span>Tableau de bord</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="nav-icon">📚</span>Mes cours</a>
        <a href="{{ route('teacher.courses.create') }}" class="nav-item"><span class="nav-icon">➕</span>Créer un cours</a>
        <a href="{{ route('teacher.quizzes.index') }}" class="nav-item active"><span class="nav-icon">📝</span>Quiz & Exercices</a>
        <a href="{{route ('teacher.students.index')}}" class="nav-item"><span class="nav-icon">👥</span>Mes apprenants</a>
        <a href="#" class="nav-item"><span class="nav-icon">💬</span>Forum</a>
        <a href="{{route ('teacher.revenues.index')}}" class="nav-item"><span class="nav-icon">💰</span>Revenus</a>
    </nav>
    <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,0.05);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:rgba(239,68,68,0.07);color:rgba(239,68,68,0.75);border:none;cursor:pointer;">
                <span class="nav-icon" style="background:rgba(239,68,68,0.08);">🚪</span>Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ═══ MAIN ═══ --}}
<div class="main-content">
    <header style="position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;padding:14px 32px;border-bottom:1px solid rgba(37,194,110,0.08);background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);">
        <div>
            <h1 style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:#fff;">Quiz & Exercices</h1>
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:2px;">{{ $globalStats['total'] }} quiz · {{ $globalStats['total_attempts'] }} tentatives</p>
        </div>
        <button type="button" class="btn-primary" x-on:click="showCreateModal = true">
            ➕ Nouveau quiz
        </button>
    </header>

    <div style="padding:28px 32px;">

        @if(session('success'))
        <div class="anim" style="margin-bottom:20px;padding:14px 18px;border-radius:14px;background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2);display:flex;align-items:center;gap:10px;">
            <span>🎉</span><span style="font-size:0.88rem;color:#25c26e;">{{ session('success') }}</span>
        </div>
        @endif

        {{-- Stats globales --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;">
            @foreach([
                ['📝', $globalStats['total'],           'Quiz créés',      '#25c26e'],
                ['🔢', $globalStats['total_attempts'],  'Tentatives total', '#3b82f6'],
                ['📊', $globalStats['avg_score'].'%',   'Score moyen',     '#a78bfa'],
                ['✅', $globalStats['pass_rate'].'%',   'Taux de réussite','#e8b84b'],
            ] as [$icon, $val, $label, $color])
            <div class="stat-card anim anim-{{ $loop->index+1 }}">
                <div style="width:36px;height:36px;border-radius:10px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;font-size:1rem;margin-bottom:10px;">{{ $icon }}</div>
                <div style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:{{ $color }};line-height:1;">{{ $val }}</div>
                <div style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:4px;">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        {{-- Quiz par cours --}}
        @forelse($courses as $course)
        @if($course->quizzes->count() > 0)
        <div class="glass anim anim-3" style="margin-bottom:18px;overflow:hidden;">
            {{-- En-tête cours --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid rgba(255,255,255,0.06);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:9px;background:rgba(37,194,110,0.1);display:flex;align-items:center;justify-content:center;font-size:1rem;">📚</div>
                    <div>
                        <div style="font-size:0.92rem;font-weight:600;color:#fff;">{{ $course->title }}</div>
                        <div style="font-size:0.72rem;color:rgba(255,255,255,0.35);">{{ $course->quizzes->count() }} quiz · {{ $course->quizzes->sum('questions_count') }} questions</div>
                    </div>
                </div>
                <button type="button" class="btn-ghost" style="font-size:0.78rem;"
                        x-on:click="openCreateWithCourse({{ $course->id }}, '{{ addslashes($course->title) }}')">
                    + Ajouter un quiz
                </button>
            </div>

            {{-- Liste quiz du cours --}}
            <div style="divide-y:1px solid rgba(255,255,255,0.05);">
                @foreach($course->quizzes as $quiz)
                @php
                    $passRate = $quiz->attempts_count > 0
                        ? round(\App\Models\QuizAttempt::where('quiz_id',$quiz->id)->where('passed',true)->count() / $quiz->attempts_count * 100)
                        : 0;
                    $avgScore = $quiz->attempts_count > 0
                        ? round(\App\Models\QuizAttempt::where('quiz_id',$quiz->id)->avg('score'), 1)
                        : 0;
                @endphp
                <div style="display:flex;align-items:center;gap:16px;padding:14px 22px;border-top:1px solid rgba(255,255,255,0.04);transition:background 0.15s;"
                     onmouseover="this.style.background='rgba(37,194,110,0.03)'" onmouseout="this.style.background=''">

                    {{-- Infos quiz --}}
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                            <span style="font-size:0.88rem;font-weight:600;color:#fff;">{{ $quiz->title }}</span>
                            @if($quiz->lesson)
                            <span style="font-size:0.65rem;padding:2px 7px;border-radius:6px;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);color:#60a5fa;">Leçon : {{ Str::limit($quiz->lesson->title, 20) }}</span>
                            @else
                            <span style="font-size:0.65rem;padding:2px 7px;border-radius:6px;background:rgba(167,139,250,0.1);border:1px solid rgba(167,139,250,0.2);color:#c4b5fd;">Fin de cours</span>
                            @endif
                        </div>
                        <div style="display:flex;align-items:center;gap:12px;font-size:0.72rem;color:rgba(255,255,255,0.35);">
                            <span>{{ $quiz->questions_count }} questions</span>
                            <span>·</span>
                            <span>{{ $quiz->passing_score }}% requis</span>
                            <span>·</span>
                            <span>{{ $quiz->max_attempts }} tentative(s)</span>
                            @if($quiz->duration_minutes)
                            <span>·</span>
                            <span>{{ $quiz->duration_minutes }}min</span>
                            @endif
                        </div>
                    </div>

                    {{-- Mini stats --}}
                    <div style="display:flex;gap:16px;flex-shrink:0;">
                        <div style="text-align:center;">
                            <div style="font-size:0.95rem;font-weight:700;color:#fff;">{{ $quiz->attempts_count }}</div>
                            <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:.04rem;">Tentatives</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="font-size:0.95rem;font-weight:700;color:{{ $avgScore >= $quiz->passing_score ? '#25c26e' : '#f87171' }};">{{ $avgScore }}%</div>
                            <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:.04rem;">Moy.</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="font-size:0.95rem;font-weight:700;color:#e8b84b;">{{ $passRate }}%</div>
                            <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:.04rem;">Réussite</div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;gap:6px;flex-shrink:0;">
                        <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn-ghost" style="padding:6px 12px;font-size:0.78rem;">
                            ✏️ Éditer
                        </a>
                        <a href="{{ route('teacher.quizzes.stats', $quiz) }}" class="btn-ghost" style="padding:6px 12px;font-size:0.78rem;color:#60a5fa;border-color:rgba(59,130,246,0.2);">
                            📊
                        </a>
                        <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz) }}"
                              x-on:submit.prevent="confirm('Supprimer ce quiz ?') && $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger" style="padding:6px 10px;">🗑</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @empty
        <div class="glass" style="padding:60px 20px;text-align:center;">
            <div style="font-size:4rem;margin-bottom:14px;">📝</div>
            <div style="font-size:1rem;font-weight:600;color:rgba(255,255,255,0.55);margin-bottom:6px;">Aucun quiz pour l'instant</div>
            <div style="font-size:0.85rem;color:rgba(255,255,255,0.3);margin-bottom:22px;">Créez votre premier quiz pour évaluer vos apprenants.</div>
            <button type="button" class="btn-primary" x-on:click="showCreateModal = true">➕ Créer un quiz</button>
        </div>
        @endforelse

        {{-- Cours sans quiz --}}
        @foreach($courses->filter(fn($c) => $c->quizzes->count() === 0) as $course)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:12px;margin-bottom:8px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:1rem;">📚</span>
                <span style="font-size:0.85rem;color:rgba(255,255,255,0.5);">{{ Str::limit($course->title, 45) }}</span>
                <span style="font-size:0.68rem;padding:2px 8px;border-radius:100px;background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.3);">Aucun quiz</span>
            </div>
            <button type="button" class="btn-ghost" style="font-size:0.78rem;"
                    x-on:click="openCreateWithCourse({{ $course->id }}, '{{ addslashes($course->title) }}')">
                + Ajouter
            </button>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══ MODAL CRÉER UN QUIZ ═══ --}}
<div class="modal-backdrop" x-show="showCreateModal" x-on:click.self="showCreateModal = false"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     style="display:none;">
    <div class="modal-box" x-on:click.stop>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(255,255,255,0.07);">
            <div>
                <div style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#fff;">Nouveau quiz</div>
                <div style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:2px;">Configurez les paramètres de base</div>
            </div>
            <button type="button" x-on:click="showCreateModal = false"
                    style="width:30px;height:30px;border-radius:8px;background:rgba(255,255,255,0.06);border:none;color:rgba(255,255,255,0.5);cursor:pointer;font-size:1rem;">✕</button>
        </div>

        <form method="POST" action="{{ route('teacher.quizzes.store') }}" style="padding:24px;">
            @csrf

            {{-- Cours --}}
            <div style="margin-bottom:16px;">
                <label class="field-label">Cours <span style="color:#f87171">*</span></label>
                <select name="course_id" class="field-input" x-model="selectedCourseId"
                        x-on:change="loadLessons($event.target.value)">
                    <option value="">Choisir un cours...</option>
                    @foreach($availableCourses as $c)
                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Leçon (optionnelle) --}}
            <div style="margin-bottom:16px;" x-show="lessons.length > 0" x-transition>
                <label class="field-label">Associer à une leçon <span style="color:rgba(255,255,255,0.3);">(optionnel)</span></label>
                <select name="lesson_id" class="field-input">
                    <option value="">Fin de cours (aucune leçon)</option>
                    <template x-for="lesson in lessons" :key="lesson.id">
                        <option :value="lesson.id" x-text="lesson.title"></option>
                    </template>
                </select>
            </div>

            {{-- Titre --}}
            <div style="margin-bottom:16px;">
                <label class="field-label">Titre du quiz <span style="color:#f87171">*</span></label>
                <input type="text" name="title" class="field-input"
                       placeholder="Ex: Quiz — Les routes Laravel" required>
            </div>

            {{-- Description --}}
            <div style="margin-bottom:16px;">
                <label class="field-label">Description <span style="color:rgba(255,255,255,0.3);">(optionnel)</span></label>
                <textarea name="description" class="field-input" rows="2"
                          placeholder="Instructions pour les apprenants..."></textarea>
            </div>

            {{-- Paramètres en grille --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label class="field-label">Score requis (%) <span style="color:#f87171">*</span></label>
                    <input type="number" name="passing_score" class="field-input"
                           value="70" min="0" max="100" required>
                </div>
                <div>
                    <label class="field-label">Tentatives max <span style="color:#f87171">*</span></label>
                    <input type="number" name="max_attempts" class="field-input"
                           value="3" min="1" max="10" required>
                </div>
                <div>
                    <label class="field-label">Durée (min)</label>
                    <input type="number" name="duration_minutes" class="field-input"
                           placeholder="∞" min="1" max="360">
                </div>
            </div>

            {{-- Toggle correction --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:10px;margin-bottom:20px;">
                <div>
                    <div style="font-size:0.85rem;font-weight:500;color:#fff;">Afficher les corrections</div>
                    <div style="font-size:0.73rem;color:rgba(255,255,255,0.35);">L'apprenant voit les bonnes réponses après soumission</div>
                </div>
                <label style="position:relative;display:inline-block;width:40px;height:22px;cursor:pointer;">
                    <input type="checkbox" name="show_answers" value="1" checked style="opacity:0;width:0;height:0;">
                    <span x-data="{on:true}" x-on:click="on=!on"
                          :style="on ? 'background:#25c26e' : 'background:rgba(255,255,255,0.1)'"
                          style="position:absolute;inset:0;border-radius:11px;transition:background 0.2s;cursor:pointer;">
                        <span :style="on ? 'transform:translateX(18px)' : ''"
                              style="position:absolute;width:16px;height:16px;background:#fff;border-radius:50%;top:3px;left:3px;transition:transform 0.2s;"></span>
                    </span>
                </label>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" class="btn-ghost" x-on:click="showCreateModal = false">Annuler</button>
                <button type="submit" class="btn-primary">Créer et ajouter les questions →</button>
            </div>
        </form>
    </div>
</div>

<script>
function quizzesPage() {
    return {
        showCreateModal: false,
        selectedCourseId: '',
        lessons: [],
        csrf: document.querySelector('meta[name="csrf-token"]').content,

        openCreateWithCourse(courseId, courseTitle) {
            this.selectedCourseId = String(courseId);
            this.loadLessons(courseId);
            this.showCreateModal = true;
        },

        async loadLessons(courseId) {
            if (!courseId) { this.lessons = []; return; }
            try {
                const res = await fetch(`/teacher/courses/${courseId}/lessons-list`, {
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' }
                });
                if (res.ok) this.lessons = await res.json();
            } catch(e) { this.lessons = []; }
        },

        init() {
            @if(session('show_create_modal'))
            this.showCreateModal = true;
            @endif
        }
    }
}
</script>
</body>
</html>