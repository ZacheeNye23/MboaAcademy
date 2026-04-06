<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Éditer quiz — {{ $quiz->title }}</title>
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
        .field-label { display:block;font-size:0.72rem;font-weight:600;letter-spacing:.06rem;text-transform:uppercase;color:rgba(255,255,255,0.38);margin-bottom:7px; }
        .field-input { width:100%;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);border-radius:10px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.88rem;padding:10px 14px;transition:all 0.25s;outline:none; }
        .field-input::placeholder { color:rgba(255,255,255,0.22); }
        .field-input:focus { border-color:#25c26e;background:rgba(37,194,110,0.05);box-shadow:0 0 0 3px rgba(37,194,110,0.1); }
        select.field-input option { background:#0f1f14; }
        textarea.field-input { resize:vertical;line-height:1.6; }

        .btn-primary { display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#1a8a47,#25c26e);border-radius:10px;color:#fff;font-size:0.88rem;font-weight:600;border:none;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif; }
        .btn-primary:hover { transform:translateY(-1px);box-shadow:0 6px 18px rgba(37,194,110,0.3); }
        .btn-ghost { display:inline-flex;align-items:center;gap:7px;padding:8px 15px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:9px;color:rgba(255,255,255,0.6);font-size:0.82rem;font-weight:500;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .btn-ghost:hover { background:rgba(255,255,255,0.08);color:#fff; }
        .btn-danger { display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.18);border-radius:8px;color:#f87171;font-size:0.75rem;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;border-width:1px;border-style:solid; }
        .btn-danger:hover { background:rgba(239,68,68,0.15); }

        /* Question card */
        .q-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:16px;overflow:hidden;transition:border-color 0.2s; }
        .q-card:hover { border-color:rgba(37,194,110,0.2); }
        .q-header { display:flex;align-items:flex-start;gap:12px;padding:16px 18px;cursor:pointer; }
        .q-body { border-top:1px solid rgba(255,255,255,0.06);padding:16px 18px; }
        .q-number { width:28px;height:28px;border-radius:8px;background:rgba(37,194,110,0.12);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#25c26e;flex-shrink:0;margin-top:2px; }

        /* Answer rows */
        .answer-row { display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);margin-bottom:7px;transition:all 0.2s; }
        .answer-row.correct { background:rgba(37,194,110,0.06);border-color:rgba(37,194,110,0.2); }
        .answer-row:hover { border-color:rgba(255,255,255,0.12); }

        /* Form ajout question */
        .add-answer-row { display:flex;align-items:center;gap:10px;padding:8px;border-radius:10px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);margin-bottom:7px; }

        /* Type badge */
        .type-badge { display:inline-flex;align-items:center;padding:2px 9px;border-radius:100px;font-size:0.67rem;font-weight:700; }
        .type-single   { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
        .type-multiple { background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2); }
        .type-tf       { background:rgba(249,115,22,0.1);color:#fb923c;border:1px solid rgba(249,115,22,0.2); }

        /* Points badge */
        .pts-badge { display:inline-flex;align-items:center;padding:2px 8px;border-radius:100px;font-size:0.67rem;font-weight:700;background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }

        /* Correct indicator */
        .correct-dot { width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.65rem;flex-shrink:0;font-weight:700; }
        .correct-dot.yes { background:rgba(37,194,110,0.15);color:#25c26e;border:1.5px solid rgba(37,194,110,0.3); }
        .correct-dot.no  { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.2);border:1.5px solid rgba(255,255,255,0.08); }

        /* Tabs */
        .tab-btn { padding:8px 18px;border-radius:10px;font-size:0.85rem;font-weight:500;cursor:pointer;border:none;background:transparent;transition:all 0.2s;font-family:'Outfit',sans-serif;color:rgba(255,255,255,0.45); }
        .tab-btn.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .tab-btn:hover:not(.active) { color:rgba(255,255,255,0.75); }

        /* Stat mini */
        .stat-mini { text-align:center;padding:12px 16px;border-radius:12px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06); }

        @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .anim-1{animation-delay:.04s}.anim-2{animation-delay:.08s}.anim-3{animation-delay:.12s}

        /* Toggle custom */
        .toggle-pill { width:40px;height:22px;border-radius:11px;position:relative;cursor:pointer;transition:background 0.2s;flex-shrink:0; }
        .toggle-thumb { width:16px;height:16px;background:#fff;border-radius:50%;position:absolute;top:3px;left:3px;transition:transform 0.2s; }
    </style>
</head>
<body x-data="quizEdit()">

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>

    {{-- Résumé quiz --}}
    <div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.05);">
        <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:.08rem;color:rgba(255,255,255,0.2);margin-bottom:8px;">Quiz en édition</div>
        <div style="font-size:0.85rem;font-weight:600;color:#fff;margin-bottom:8px;line-height:1.3;">{{ Str::limit($quiz->title, 32) }}</div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
            <span style="font-size:0.68rem;padding:2px 8px;border-radius:6px;background:rgba(37,194,110,0.1);border:1px solid rgba(37,194,110,0.2);color:#25c26e;">{{ $quiz->questions->count() }} questions</span>
            <span style="font-size:0.68rem;padding:2px 8px;border-radius:6px;background:rgba(232,184,75,0.1);border:1px solid rgba(232,184,75,0.2);color:#e8b84b;">{{ $quiz->passing_score }}% requis</span>
        </div>
        <div style="margin-top:10px;">
            <div style="height:3px;border-radius:2px;background:rgba(255,255,255,0.07);">
                <div style="width:{{ min(100, $quiz->questions->count() * 10) }}%;height:100%;border-radius:2px;background:#25c26e;"></div>
            </div>
            <div style="font-size:0.68rem;color:rgba(255,255,255,0.25);margin-top:4px;">{{ $quiz->questions->sum('points') }} points au total</div>
        </div>
    </div>

    <nav style="flex:1;padding:16px 0;overflow-y:auto;">
        <a href="{{ route('teacher.dashboard') }}" class="nav-item"><span class="nav-icon">🏠</span>Dashboard</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="nav-icon">📚</span>Mes cours</a>
        <a href="{{ route('teacher.quizzes.index') }}" class="nav-item active"><span class="nav-icon">📝</span>Quiz & Exercices</a>
        <a href="{{ route('teacher.students.index') }}" class="nav-item"><span class="nav-icon">👥</span>Apprenants</a>
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

{{-- MAIN --}}
<div class="main-content">

    {{-- Topbar --}}
    <header style="position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;padding:12px 32px;border-bottom:1px solid rgba(37,194,110,0.08);background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);">
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="{{ route('teacher.quizzes.index') }}" class="btn-ghost" style="padding:6px 12px;">← Retour</a>
            <div>
                <span style="font-size:0.9rem;font-weight:600;color:#fff;">{{ Str::limit($quiz->title, 50) }}</span>
                <span style="margin-left:8px;font-size:0.72rem;color:rgba(255,255,255,0.35);">{{ $quiz->course->title }}</span>
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('teacher.quizzes.stats', $quiz) }}" class="btn-ghost" style="font-size:0.82rem;">
                📊 Voir les stats
            </a>
        </div>
    </header>

    <div style="padding:28px 32px;display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">

        {{-- ── COLONNE GAUCHE : Questions ── --}}
        <div>

            {{-- Tabs --}}
            <div style="display:flex;gap:4px;margin-bottom:20px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:5px;width:fit-content;">
                <button class="tab-btn" :class="tab==='questions'?'active':''" x-on:click="tab='questions'">❓ Questions ({{ $quiz->questions->count() }})</button>
                <button class="tab-btn" :class="tab==='settings'?'active':''" x-on:click="tab='settings'">⚙️ Paramètres</button>
            </div>

            {{-- ── TAB QUESTIONS ── --}}
            <div x-show="tab === 'questions'" x-transition>

                {{-- Alertes --}}
                @if(session('success'))
                <div class="anim" style="margin-bottom:16px;padding:12px 16px;border-radius:12px;background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2);font-size:0.85rem;color:#25c26e;">
                    🎉 {{ session('success') }}
                </div>
                @endif
                <div x-show="successMsg" x-transition style="margin-bottom:16px;padding:12px 16px;border-radius:12px;background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2);font-size:0.85rem;color:#25c26e;" x-text="successMsg"></div>
                <div x-show="errorMsg" x-transition style="margin-bottom:16px;padding:12px 16px;border-radius:12px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);font-size:0.85rem;color:#f87171;" x-text="errorMsg"></div>

                {{-- Liste questions --}}
                <div style="margin-bottom:20px;">
                    <template x-for="(q, qi) in questions" :key="q.id">
                        <div class="q-card anim" style="margin-bottom:12px;">

                            {{-- Header question --}}
                            <div class="q-header" x-on:click="q.open = !q.open">
                                <div class="q-number" x-text="qi + 1"></div>
                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                                        <span style="font-size:0.9rem;font-weight:500;color:#fff;" x-text="q.question"></span>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                        <span class="type-badge"
                                              :class="q.type==='single'?'type-single':q.type==='multiple'?'type-multiple':'type-tf'"
                                              x-text="q.type==='single'?'Réponse unique':q.type==='multiple'?'Réponses multiples':'Vrai / Faux'"></span>
                                        <span class="pts-badge" x-text="q.points + ' pt(s)'"></span>
                                        <span style="font-size:0.68rem;color:rgba(255,255,255,0.3);" x-text="q.answers.length + ' réponses'"></span>
                                    </div>
                                </div>
                                <div style="display:flex;gap:6px;flex-shrink:0;" x-on:click.stop>
                                    <button type="button" class="btn-ghost" style="padding:5px 10px;font-size:0.75rem;"
                                            x-on:click="startEditQuestion(q)">✏️</button>
                                    <button type="button" class="btn-danger" style="padding:5px 10px;"
                                            x-on:click="deleteQuestion(q, qi)">🗑</button>
                                    <span style="font-size:0.85rem;color:rgba(255,255,255,0.3);" x-text="q.open?'▲':'▼'"></span>
                                </div>
                            </div>

                            {{-- Body : réponses --}}
                            <div class="q-body" x-show="q.open" x-transition>

                                {{-- Mode lecture --}}
                                <div x-show="editingQuestionId !== q.id">
                                    <template x-for="(a, ai) in q.answers" :key="a.id">
                                        <div class="answer-row" :class="a.is_correct ? 'correct' : ''">
                                            <div class="correct-dot" :class="a.is_correct ? 'yes' : 'no'"
                                                 x-text="a.is_correct ? '✓' : '○'"></div>
                                            <span style="font-size:0.85rem;" :style="a.is_correct ? 'color:#25c26e;font-weight:500' : 'color:rgba(255,255,255,0.6)'" x-text="a.answer_text"></span>
                                        </div>
                                    </template>
                                    <div x-show="q.explanation" style="margin-top:10px;padding:10px 12px;border-radius:9px;background:rgba(59,130,246,0.06);border:1px solid rgba(59,130,246,0.15);">
                                        <span style="font-size:0.72rem;color:#60a5fa;font-weight:600;">💡 Explication :</span>
                                        <span style="font-size:0.82rem;color:rgba(255,255,255,0.55);margin-left:6px;" x-text="q.explanation"></span>
                                    </div>
                                </div>

                                {{-- Mode édition --}}
                                <div x-show="editingQuestionId === q.id" x-transition>
                                    <div style="margin-bottom:12px;">
                                        <label class="field-label">Question</label>
                                        <textarea x-model="editForm.question" class="field-input" rows="2"></textarea>
                                    </div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:12px;">
                                        <div>
                                            <label class="field-label">Type</label>
                                            <select x-model="editForm.type" class="field-input" x-on:change="onTypeChange(editForm)">
                                                <option value="single">Réponse unique</option>
                                                <option value="multiple">Réponses multiples</option>
                                                <option value="true_false">Vrai / Faux</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="field-label">Points</label>
                                            <input type="number" x-model="editForm.points" class="field-input" min="1" max="100">
                                        </div>
                                    </div>
                                    <div style="margin-bottom:12px;">
                                        <label class="field-label">Réponses</label>
                                        <template x-for="(ans, ai) in editForm.answers" :key="ai">
                                            <div class="add-answer-row">
                                                <input type="text" x-model="ans.text" class="field-input" style="flex:1;padding:7px 10px;" placeholder="Texte de la réponse">
                                                <label style="display:flex;align-items:center;gap:5px;cursor:pointer;white-space:nowrap;">
                                                    <input type="checkbox" x-model="ans.correct"
                                                           x-on:change="editForm.type==='single' && ans.correct && clearOtherCorrect(editForm.answers, ai)"
                                                           style="width:15px;height:15px;accent-color:#25c26e;">
                                                    <span style="font-size:0.75rem;color:rgba(255,255,255,0.5);">Correcte</span>
                                                </label>
                                                <button type="button" x-show="editForm.answers.length > 2"
                                                        x-on:click="editForm.answers.splice(ai, 1)"
                                                        style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.15);border-radius:7px;color:#f87171;padding:5px 8px;cursor:pointer;font-size:0.75rem;">✕</button>
                                            </div>
                                        </template>
                                        <button type="button" x-on:click="editForm.answers.push({text:'',correct:false})"
                                                x-show="editForm.answers.length < 6"
                                                class="btn-ghost" style="font-size:0.78rem;margin-top:6px;">+ Ajouter une réponse</button>
                                    </div>
                                    <div style="margin-bottom:14px;">
                                        <label class="field-label">Explication (optionnel)</label>
                                        <textarea x-model="editForm.explanation" class="field-input" rows="2" placeholder="Explication affichée après correction..."></textarea>
                                    </div>
                                    <div style="display:flex;gap:8px;">
                                        <button type="button" class="btn-primary" x-on:click="saveEditQuestion(q)">💾 Enregistrer</button>
                                        <button type="button" class="btn-ghost" x-on:click="editingQuestionId = null">Annuler</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Empty --}}
                    <div x-show="questions.length === 0" style="padding:40px 20px;text-align:center;background:rgba(255,255,255,0.02);border:1px dashed rgba(255,255,255,0.08);border-radius:16px;">
                        <div style="font-size:3rem;margin-bottom:10px;">❓</div>
                        <div style="font-size:0.9rem;color:rgba(255,255,255,0.45);margin-bottom:4px;">Aucune question pour l'instant</div>
                        <div style="font-size:0.8rem;color:rgba(255,255,255,0.25);">Utilisez le formulaire ci-dessous pour en ajouter.</div>
                    </div>
                </div>

                {{-- ── FORMULAIRE AJOUT QUESTION ── --}}
                <div class="glass" style="padding:22px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
                        <div style="width:32px;height:32px;border-radius:9px;background:rgba(37,194,110,0.12);display:flex;align-items:center;justify-content:center;font-size:0.9rem;">➕</div>
                        <div>
                            <div style="font-size:0.92rem;font-weight:600;color:#fff;">Nouvelle question</div>
                            <div style="font-size:0.72rem;color:rgba(255,255,255,0.3);">Ajoutez une question à ce quiz</div>
                        </div>
                    </div>

                    {{-- Texte question --}}
                    <div style="margin-bottom:14px;">
                        <label class="field-label">Question <span style="color:#f87171">*</span></label>
                        <textarea x-model="newQ.question" class="field-input" rows="2"
                                  placeholder="Quelle est la différence entre GET et POST en HTTP ?"></textarea>
                    </div>

                    {{-- Type + Points --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:14px;">
                        <div>
                            <label class="field-label">Type <span style="color:#f87171">*</span></label>
                            <select x-model="newQ.type" class="field-input" x-on:change="onTypeChange(newQ)">
                                <option value="single">🔘 Réponse unique</option>
                                <option value="multiple">☑️ Réponses multiples</option>
                                <option value="true_false">⚖️ Vrai / Faux</option>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">Points <span style="color:#f87171">*</span></label>
                            <input type="number" x-model="newQ.points" class="field-input" min="1" max="100" value="1">
                        </div>
                        <div style="display:flex;align-items:flex-end;">
                            <div style="padding:8px 12px;border-radius:9px;background:rgba(232,184,75,0.08);border:1px solid rgba(232,184,75,0.15);font-size:0.78rem;color:rgba(255,255,255,0.45);width:100%;">
                                Total : <strong style="color:#e8b84b;" x-text="totalPoints + ' pts'"></strong>
                            </div>
                        </div>
                    </div>

                    {{-- Réponses --}}
                    <div style="margin-bottom:14px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                            <label class="field-label" style="margin:0;">Réponses <span style="color:#f87171">*</span></label>
                            <span style="font-size:0.72rem;color:rgba(255,255,255,0.3);">
                                <span x-show="newQ.type==='single'">Cochez UNE bonne réponse</span>
                                <span x-show="newQ.type==='multiple'">Cochez TOUTES les bonnes réponses</span>
                                <span x-show="newQ.type==='true_false'">Choisissez Vrai ou Faux</span>
                            </span>
                        </div>

                        <template x-for="(ans, ai) in newQ.answers" :key="ai">
                            <div class="add-answer-row" :style="ans.correct ? 'background:rgba(37,194,110,0.06);border-color:rgba(37,194,110,0.2)' : ''">
                                {{-- Lettre --}}
                                <div style="width:22px;height:22px;border-radius:6px;background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:rgba(255,255,255,0.4);flex-shrink:0;"
                                     x-text="['A','B','C','D','E','F'][ai]"></div>

                                {{-- Input texte (masqué pour vrai/faux) --}}
                                <div style="flex:1;" x-show="newQ.type !== 'true_false'">
                                    <input type="text" x-model="ans.text" class="field-input" style="padding:7px 10px;"
                                           :placeholder="'Réponse ' + ['A','B','C','D','E','F'][ai]">
                                </div>
                                {{-- Label fixe pour vrai/faux --}}
                                <div style="flex:1;font-size:0.88rem;font-weight:500;" x-show="newQ.type === 'true_false'"
                                     :style="ans.correct ? 'color:#25c26e' : 'color:rgba(255,255,255,0.6)'"
                                     x-text="ans.text"></div>

                                {{-- Case correcte --}}
                                <label style="display:flex;align-items:center;gap:5px;cursor:pointer;white-space:nowrap;">
                                    <input type="checkbox" x-model="ans.correct"
                                           x-on:change="newQ.type === 'single' && ans.correct && clearOtherCorrect(newQ.answers, ai)"
                                           style="width:16px;height:16px;accent-color:#25c26e;">
                                    <span style="font-size:0.75rem;" :style="ans.correct ? 'color:#25c26e;font-weight:600' : 'color:rgba(255,255,255,0.4)'">Correcte</span>
                                </label>

                                {{-- Suppr --}}
                                <button type="button" x-show="newQ.type !== 'true_false' && newQ.answers.length > 2"
                                        x-on:click="newQ.answers.splice(ai, 1)"
                                        style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.15);border-radius:7px;color:#f87171;padding:4px 8px;cursor:pointer;font-size:0.72rem;flex-shrink:0;">✕</button>
                            </div>
                        </template>

                        {{-- Ajouter réponse --}}
                        <button type="button" x-show="newQ.type !== 'true_false' && newQ.answers.length < 6"
                                x-on:click="newQ.answers.push({text:'', correct:false})"
                                class="btn-ghost" style="font-size:0.78rem;margin-top:6px;width:100%;justify-content:center;">
                            + Ajouter une réponse
                        </button>
                    </div>

                    {{-- Explication --}}
                    <div style="margin-bottom:16px;">
                        <label class="field-label">Explication (optionnel)</label>
                        <textarea x-model="newQ.explanation" class="field-input" rows="2"
                                  placeholder="Explication affichée à l'apprenant après correction..."></textarea>
                    </div>

                    {{-- Validation inline --}}
                    <div x-show="validationError" style="margin-bottom:10px;padding:8px 12px;border-radius:9px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);font-size:0.8rem;color:#f87171;" x-text="validationError"></div>

                    {{-- Submit --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div style="font-size:0.75rem;color:rgba(255,255,255,0.3);">
                            <span x-text="questions.length + ' question(s) · ' + totalPoints + ' pts total'"></span>
                        </div>
                        <button type="button" class="btn-primary" x-on:click="submitQuestion()" :disabled="saving">
                            <span x-show="!saving">✅ Ajouter la question</span>
                            <span x-show="saving">⏳ Enregistrement...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── TAB PARAMÈTRES ── --}}
            <div x-show="tab === 'settings'" x-transition>
                <form method="POST" action="{{ route('teacher.quizzes.update', $quiz) }}" class="glass" style="padding:24px;">
                    @csrf @method('PUT')
                    <input type="hidden" name="course_id" value="{{ $quiz->course_id }}">

                    <div style="display:grid;gap:16px;">
                        <div>
                            <label class="field-label">Titre du quiz *</label>
                            <input type="text" name="title" value="{{ old('title', $quiz->title) }}" class="field-input" required>
                        </div>
                        <div>
                            <label class="field-label">Description</label>
                            <textarea name="description" class="field-input" rows="2">{{ old('description', $quiz->description) }}</textarea>
                        </div>
                        <div>
                            <label class="field-label">Associer à une leçon</label>
                            <select name="lesson_id" class="field-input">
                                <option value="">Fin de cours (aucune leçon)</option>
                                @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ $quiz->lesson_id == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
                            <div>
                                <label class="field-label">Score requis (%) *</label>
                                <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" class="field-input" min="0" max="100" required>
                            </div>
                            <div>
                                <label class="field-label">Tentatives max *</label>
                                <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" class="field-input" min="1" max="10" required>
                            </div>
                            <div>
                                <label class="field-label">Durée (min)</label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" class="field-input" placeholder="∞" min="1">
                            </div>
                        </div>

                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:10px;">
                            <div>
                                <div style="font-size:0.85rem;font-weight:500;color:#fff;">Afficher les corrections</div>
                                <div style="font-size:0.73rem;color:rgba(255,255,255,0.35);">L'apprenant voit les bonnes réponses après soumission</div>
                            </div>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;" x-data="{on: {{ $quiz->show_answers ? 'true' : 'false' }}}">
                                <input type="checkbox" name="show_answers" value="1" :checked="on" style="display:none;">
                                <div class="toggle-pill" :style="on ? 'background:#25c26e' : 'background:rgba(255,255,255,0.1)'" x-on:click="on = !on">
                                    <div class="toggle-thumb" :style="on ? 'transform:translateX(18px)' : ''"></div>
                                </div>
                                <span style="font-size:0.82rem;" :style="on ? 'color:#25c26e' : 'color:rgba(255,255,255,0.4)'" x-text="on ? 'Activé' : 'Désactivé'"></span>
                            </label>
                        </div>
                    </div>

                    <div style="margin-top:20px;display:flex;align-items:center;justify-content:space-between;">
                        <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz) }}"
                              x-on:submit.prevent="confirm('Supprimer ce quiz et toutes ses questions ?') && $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger">🗑 Supprimer le quiz</button>
                        </form>
                        <button type="submit" class="btn-primary">💾 Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── COLONNE DROITE : Résumé + Stats ── --}}
        <div style="position:sticky;top:76px;">

            {{-- Résumé quiz --}}
            <div class="glass" style="padding:20px;margin-bottom:16px;">
                <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.3);margin-bottom:14px;font-weight:600;">Résumé du quiz</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
                    <div class="stat-mini">
                        <div style="font-size:1.4rem;font-weight:700;color:#25c26e;" x-text="questions.length">{{ $quiz->questions->count() }}</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);margin-top:2px;">Questions</div>
                    </div>
                    <div class="stat-mini">
                        <div style="font-size:1.4rem;font-weight:700;color:#e8b84b;" x-text="totalPoints">{{ $quiz->questions->sum('points') }}</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);margin-top:2px;">Points total</div>
                    </div>
                    <div class="stat-mini">
                        <div style="font-size:1.4rem;font-weight:700;color:#a78bfa;">{{ $quiz->passing_score }}%</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);margin-top:2px;">Score requis</div>
                    </div>
                    <div class="stat-mini">
                        <div style="font-size:1.4rem;font-weight:700;color:#3b82f6;">{{ $quiz->max_attempts }}</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);margin-top:2px;">Tentatives</div>
                    </div>
                </div>

                {{-- Types de questions --}}
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.3);margin-bottom:8px;">Types</div>
                @php
                    $singles   = $quiz->questions->where('type','single')->count();
                    $multiples = $quiz->questions->where('type','multiple')->count();
                    $tfs       = $quiz->questions->where('type','true_false')->count();
                @endphp
                <div style="display:flex;flex-direction:column;gap:5px;">
                    @if($singles > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.5);">🔘 Réponse unique</span>
                        <span style="font-size:0.75rem;font-weight:600;color:#25c26e;">{{ $singles }}</span>
                    </div>
                    @endif
                    @if($multiples > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.5);">☑️ Multiples</span>
                        <span style="font-size:0.75rem;font-weight:600;color:#60a5fa;">{{ $multiples }}</span>
                    </div>
                    @endif
                    @if($tfs > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.5);">⚖️ Vrai / Faux</span>
                        <span style="font-size:0.75rem;font-weight:600;color:#fb923c;">{{ $tfs }}</span>
                    </div>
                    @endif
                    @if($quiz->questions->count() === 0)
                    <div style="font-size:0.78rem;color:rgba(255,255,255,0.25);text-align:center;padding:8px 0;">Aucune question</div>
                    @endif
                </div>
            </div>

            {{-- Stats tentatives --}}
            <div class="glass" style="padding:20px;">
                <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.3);margin-bottom:14px;font-weight:600;">Performances</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div class="stat-mini">
                        <div style="font-size:1.3rem;font-weight:700;color:#fff;">{{ $stats['total_attempts'] }}</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);margin-top:2px;">Tentatives</div>
                    </div>
                    <div class="stat-mini">
                        <div style="font-size:1.3rem;font-weight:700;" style="color:{{ $stats['avg_score'] >= $quiz->passing_score ? '#25c26e' : '#f87171' }}">{{ $stats['avg_score'] }}%</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);margin-top:2px;">Score moyen</div>
                    </div>
                    <div class="stat-mini">
                        <div style="font-size:1.3rem;font-weight:700;color:#e8b84b;">{{ $stats['pass_rate'] }}%</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);margin-top:2px;">Réussite</div>
                    </div>
                    <div class="stat-mini">
                        <div style="font-size:1.3rem;font-weight:700;color:#a78bfa;">{{ $stats['best_score'] }}%</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.3);margin-top:2px;">Meilleur</div>
                    </div>
                </div>
                @if($stats['total_attempts'] > 0)
                <a href="{{ route('teacher.quizzes.stats', $quiz) }}"
                   class="btn-ghost" style="width:100%;justify-content:center;margin-top:12px;font-size:0.8rem;">
                    📊 Voir les statistiques détaillées
                </a>
                @endif
            </div>
        </div>

    </div>{{-- end grid --}}
</div>

<script>
function quizEdit() {
    return {
        tab: 'questions',
        questions: @json($quiz->questions->map(fn($q) => [
            'id'          => $q->id,
            'question'    => $q->question,
            'type'        => $q->type,
            'explanation' => $q->explanation,
            'points'      => $q->points,
            'order'       => $q->order,
            'open'        => true,
            'answers'     => $q->answers->map(fn($a) => [
                'id'          => $a->id,
                'answer_text' => $a->answer_text,
                'is_correct'  => $a->is_correct,
                'order'       => $a->order,
            ])->values()->all(),
        ])->values()->all()),

        // Formulaire nouvelle question
        newQ: {
            question:    '',
            type:        'single',
            explanation: '',
            points:      1,
            answers: [
                { text: '', correct: false },
                { text: '', correct: false },
                { text: '', correct: false },
                { text: '', correct: false },
            ],
        },

        // Formulaire édition
        editingQuestionId: null,
        editForm: {},

        saving: false,
        validationError: '',
        successMsg: '',
        errorMsg: '',
        csrf: document.querySelector('meta[name="csrf-token"]').content,

        get totalPoints() {
            return this.questions.reduce((s, q) => s + Number(q.points), 0) + Number(this.newQ.points || 0);
        },

        // ── Gestion des types ─────────────────────────────────────────────────

        onTypeChange(form) {
            if (form.type === 'true_false') {
                form.answers = [
                    { text: 'Vrai',  correct: false },
                    { text: 'Faux',  correct: false },
                ];
            } else if (form.answers.length === 2 && form.answers[0].text === 'Vrai') {
                form.answers = [
                    { text: '', correct: false },
                    { text: '', correct: false },
                    { text: '', correct: false },
                    { text: '', correct: false },
                ];
            }
        },

        clearOtherCorrect(answers, currentIndex) {
            answers.forEach((a, i) => { if (i !== currentIndex) a.correct = false; });
        },

        // ── Ajouter une question ─────────────────────────────────────────────

        validateNewQ() {
            if (!this.newQ.question.trim()) return 'La question est obligatoire.';
            if (this.newQ.answers.filter(a => a.text.trim()).length < 2) return 'Au moins 2 réponses sont requises.';
            if (!this.newQ.answers.some(a => a.correct)) return 'Cochez au moins une bonne réponse.';
            if (this.newQ.type === 'single' && this.newQ.answers.filter(a => a.correct).length > 1) return 'Un QCM à réponse unique ne peut avoir qu\'une seule bonne réponse.';
            return null;
        },

        async submitQuestion() {
            this.validationError = this.validateNewQ();
            if (this.validationError) return;

            this.saving = true;
            this.successMsg = '';
            this.errorMsg = '';

            const payload = {
                question:    this.newQ.question,
                type:        this.newQ.type,
                explanation: this.newQ.explanation,
                points:      this.newQ.points,
                answers:     this.newQ.answers
                    .filter(a => a.text.trim())
                    .map(a => ({ text: a.text, correct: a.correct ? 1 : 0 })),
            };

            try {
                const res = await fetch(`/teacher/quizzes/{{ $quiz->id }}/questions`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (data.success) {
                    this.questions.push({ ...data.question, open: true });
                    this.successMsg = '✅ Question ajoutée !';
                    this.resetNewQ();
                    setTimeout(() => this.successMsg = '', 3000);
                } else {
                    this.errorMsg = data.message || 'Erreur lors de l\'ajout.';
                }
            } catch(e) {
                this.errorMsg = 'Erreur réseau.';
            }

            this.saving = false;
        },

        resetNewQ() {
            this.newQ = { question:'', type:'single', explanation:'', points:1,
                answers:[{text:'',correct:false},{text:'',correct:false},{text:'',correct:false},{text:'',correct:false}] };
        },

        // ── Éditer une question ──────────────────────────────────────────────

        startEditQuestion(q) {
            this.editingQuestionId = q.id;
            this.editForm = {
                question:    q.question,
                type:        q.type,
                explanation: q.explanation || '',
                points:      q.points,
                answers:     q.answers.map(a => ({ text: a.answer_text, correct: a.is_correct })),
            };
        },

        async saveEditQuestion(q) {
            const payload = {
                question:    this.editForm.question,
                type:        this.editForm.type,
                explanation: this.editForm.explanation,
                points:      this.editForm.points,
                answers:     this.editForm.answers
                    .filter(a => a.text.trim())
                    .map(a => ({ text: a.text, correct: a.correct ? 1 : 0 })),
            };

            try {
                const res = await fetch(`/teacher/quizzes/questions/${q.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (data.success) {
                    q.question    = this.editForm.question;
                    q.type        = this.editForm.type;
                    q.explanation = this.editForm.explanation;
                    q.points      = Number(this.editForm.points);
                    q.answers     = this.editForm.answers.filter(a => a.text.trim()).map((a, i) => ({
                        id: q.answers[i]?.id,
                        answer_text: a.text,
                        is_correct:  a.correct,
                        order:       i,
                    }));
                    this.editingQuestionId = null;
                    this.successMsg = '✅ Question mise à jour !';
                    setTimeout(() => this.successMsg = '', 3000);
                }
            } catch(e) {
                this.errorMsg = 'Erreur lors de la mise à jour.';
            }
        },

        // ── Supprimer une question ────────────────────────────────────────────

        async deleteQuestion(q, qi) {
            if (!confirm('Supprimer cette question ?')) return;
            try {
                const res = await fetch(`/teacher/quizzes/questions/${q.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrf },
                });
                const data = await res.json();
                if (data.success) {
                    this.questions.splice(qi, 1);
                    this.successMsg = 'Question supprimée.';
                    setTimeout(() => this.successMsg = '', 3000);
                }
            } catch(e) {
                this.errorMsg = 'Erreur lors de la suppression.';
            }
        },
    }
}
</script>
</body>
</html>