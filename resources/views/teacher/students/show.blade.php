<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $enrollment->user->full_name }} — MboaAcademy</title>
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

        .glass  { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:18px; }
        .glass2 { background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:12px; }

        .btn-ghost { display:inline-flex;align-items:center;gap:7px;padding:8px 15px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:9px;color:rgba(255,255,255,0.6);font-size:0.82rem;font-weight:500;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .btn-ghost:hover { background:rgba(255,255,255,0.08);color:#fff; }

        .prog-bar  { height:6px;border-radius:3px;background:rgba(255,255,255,0.07);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px;background:linear-gradient(90deg,#1a8a47,#25c26e); }

        .lesson-row { display:flex;align-items:center;gap:10px;padding:9px 14px;border-radius:10px;margin-bottom:5px;transition:background 0.15s; }
        .lesson-row:hover { background:rgba(255,255,255,0.02); }

        .score-badge { display:inline-flex;align-items:center;justify-content:center;min-width:46px;padding:3px 8px;border-radius:100px;font-size:0.72rem;font-weight:700; }
        .score-pass { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
        .score-fail { background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2); }
        .score-mid  { background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }

        .section-title { font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;margin-bottom:4px; }
        .section-sub   { font-size:0.75rem;color:rgba(255,255,255,0.35);margin-bottom:16px; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .anim-1{animation-delay:.04s}.anim-2{animation-delay:.08s}.anim-3{animation-delay:.12s}.anim-4{animation-delay:.16s}
    </style>
</head>
<body x-data="{ tab: 'progress' }">

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>
    <nav style="flex:1;padding:16px 0;overflow-y:auto;">
        <a href="{{ route('teacher.dashboard') }}" class="nav-item"><span class="nav-icon">🏠</span>Dashboard</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="nav-icon">📚</span>Mes cours</a>
        <a href="{{ route('teacher.quizzes.index') }}" class="nav-item"><span class="nav-icon">📝</span>Quiz</a>
        <a href="{{ route('teacher.students.index') }}" class="nav-item active"><span class="nav-icon">👥</span>Mes apprenants</a>
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
    <header style="position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;padding:12px 32px;border-bottom:1px solid rgba(37,194,110,0.08);background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);">
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="{{ route('teacher.students.index') }}" class="btn-ghost" style="padding:6px 12px;">← Retour</a>
            <div>
                <span style="font-size:0.9rem;font-weight:600;color:#fff;">{{ $enrollment->user->full_name }}</span>
                <span style="margin-left:8px;font-size:0.72rem;color:rgba(255,255,255,0.35);">{{ $enrollment->course->title }}</span>
            </div>
        </div>
    </header>

    <div style="padding:28px 32px;">
        <div style="display:grid;grid-template-columns:300px 1fr;gap:22px;align-items:start;">

            {{-- ── COLONNE GAUCHE : Profil ── --}}
            <div>
                {{-- Carte profil --}}
                <div class="glass anim anim-1" style="padding:24px;margin-bottom:16px;text-align:center;">
                    <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#1a8a47,#25c26e);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;color:#fff;margin:0 auto 14px;">
                        {{ $enrollment->user->initials }}
                    </div>
                    <div style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:3px;">{{ $enrollment->user->full_name }}</div>
                    <div style="font-size:0.78rem;color:rgba(255,255,255,0.4);margin-bottom:14px;">{{ $enrollment->user->email }}</div>

                    {{-- Progression globale --}}
                    @php
                        $pct  = $enrollment->progress_percent;
                        $done = !is_null($enrollment->completed_at);
                        $circ = 2 * M_PI * 38;
                        $off  = $circ * (1 - $pct / 100);
                    @endphp
                    <div style="position:relative;display:inline-block;margin-bottom:10px;">
                        <svg width="88" height="88" viewBox="0 0 88 88">
                            <circle cx="44" cy="44" r="38" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="8"/>
                            <circle cx="44" cy="44" r="38" fill="none"
                                    stroke="{{ $done ? '#25c26e' : '#3b82f6' }}" stroke-width="8"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $circ }}"
                                    stroke-dashoffset="{{ $off }}"
                                    style="transform:rotate(-90deg);transform-origin:center;transition:stroke-dashoffset 1s ease;"/>
                        </svg>
                        <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                            <span style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:{{ $done ? '#25c26e' : '#60a5fa' }};">{{ $pct }}%</span>
                        </div>
                    </div>

                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.45);margin-bottom:14px;">
                        {{ $done ? '✅ Cours terminé !' : ($pct > 0 ? '⏳ En cours' : '○ Pas encore commencé') }}
                    </div>

                    {{-- Infos --}}
                    <div style="display:flex;flex-direction:column;gap:8px;text-align:left;">
                        @foreach([
                            ['📅', 'Inscrit le', $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d/m/Y') : '—'],
                            ['🏁', 'Terminé le', $enrollment->completed_at ? $enrollment->completed_at->format('d/m/Y') : '—'],
                            ['🌍', 'Pays',       $enrollment->user->country ?? '—'],
                        ] as [$icon, $label, $val])
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 8px;border-radius:8px;background:rgba(255,255,255,0.02);">
                            <span style="font-size:0.75rem;color:rgba(255,255,255,0.4);">{{ $icon }} {{ $label }}</span>
                            <span style="font-size:0.78rem;font-weight:500;color:rgba(255,255,255,0.7);">{{ $val }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Autres cours du même apprenant --}}
                @if($otherEnrollments->count() > 0)
                <div class="glass anim anim-2" style="padding:18px;">
                    <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.25);margin-bottom:12px;font-weight:600;">Autres cours</div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($otherEnrollments as $oe)
                        <a href="{{ route('teacher.students.show', $oe) }}" style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:9px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);text-decoration:none;transition:border-color 0.2s;" onmouseover="this.style.borderColor='rgba(37,194,110,0.2)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.05)'">
                            <span style="font-size:0.78rem;color:rgba(255,255,255,0.6);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:170px;">{{ Str::limit($oe->course->title, 22) }}</span>
                            <span style="font-size:0.75rem;font-weight:600;color:{{ $oe->progress_percent >= 100 ? '#25c26e' : '#60a5fa' }};flex-shrink:0;margin-left:6px;">{{ $oe->progress_percent }}%</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- ── COLONNE DROITE : Tabs ── --}}
            <div>
                {{-- Tabs --}}
                <div style="display:flex;gap:4px;margin-bottom:20px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:5px;width:fit-content;">
                    <button class="tab-btn" :class="tab==='progress'?'active':''" x-on:click="tab='progress'"
                            style="padding:7px 16px;border-radius:9px;font-size:0.83rem;font-weight:500;cursor:pointer;border:none;background:transparent;font-family:'Outfit',sans-serif;transition:all 0.2s;"
                            :style="tab==='progress' ? 'background:rgba(37,194,110,0.12);color:#25c26e' : 'color:rgba(255,255,255,0.45)'">
                        📚 Leçons ({{ $enrollment->course->lessons->count() }})
                    </button>
                    <button :style="tab==='quizzes' ? 'background:rgba(37,194,110,0.12);color:#25c26e' : 'color:rgba(255,255,255,0.45)'"
                            style="padding:7px 16px;border-radius:9px;font-size:0.83rem;font-weight:500;cursor:pointer;border:none;background:transparent;font-family:'Outfit',sans-serif;transition:all 0.2s;"
                            x-on:click="tab='quizzes'">
                        📝 Quiz ({{ $quizAttempts->count() }})
                    </button>
                </div>

                {{-- TAB : Progression leçons --}}
                <div x-show="tab === 'progress'" x-transition>
                    <div style="margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <div class="section-title">Progression par leçon</div>
                            <div class="section-sub">
                                {{ $completedLessonIds->count() }} / {{ $enrollment->course->lessons->count() }} leçons complétées
                            </div>
                        </div>
                        {{-- Mini stats --}}
                        <div style="display:flex;gap:10px;">
                            <div style="text-align:center;padding:8px 14px;border-radius:10px;background:rgba(37,194,110,0.06);border:1px solid rgba(37,194,110,0.12);">
                                <div style="font-size:1rem;font-weight:700;color:#25c26e;">{{ $completedLessonIds->count() }}</div>
                                <div style="font-size:0.62rem;color:rgba(255,255,255,0.3);">Complétées</div>
                            </div>
                            <div style="text-align:center;padding:8px 14px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);">
                                <div style="font-size:1rem;font-weight:700;color:rgba(255,255,255,0.5);">{{ $enrollment->course->lessons->count() - $completedLessonIds->count() }}</div>
                                <div style="font-size:0.62rem;color:rgba(255,255,255,0.3);">Restantes</div>
                            </div>
                        </div>
                    </div>

                    {{-- Chapitres et leçons --}}
                    @foreach($enrollment->course->chapters as $chapter)
                    @php
                        $chapterLessons = $chapter->lessons;
                        $completedInChapter = $chapterLessons->filter(fn($l) => $completedLessonIds->contains($l->id))->count();
                        $chapterPct = $chapterLessons->count() > 0 ? round($completedInChapter / $chapterLessons->count() * 100) : 0;
                    @endphp
                    <div class="glass2 anim" style="margin-bottom:10px;overflow:hidden;">
                        {{-- En-tête chapitre --}}
                        <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid rgba(255,255,255,0.05);">
                            <div style="width:24px;height:24px;border-radius:7px;background:rgba(37,194,110,0.1);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#25c26e;flex-shrink:0;">
                                {{ $loop->iteration }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:0.85rem;font-weight:600;color:#fff;">{{ $chapter->title }}</div>
                                <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                                    <div class="prog-bar" style="flex:1;height:4px;">
                                        <div class="prog-fill" style="width:{{ $chapterPct }}%;{{ $chapterPct === 100 ? 'background:#25c26e' : '' }}"></div>
                                    </div>
                                    <span style="font-size:0.68rem;color:{{ $chapterPct === 100 ? '#25c26e' : 'rgba(255,255,255,0.3)' }};white-space:nowrap;">{{ $completedInChapter }}/{{ $chapterLessons->count() }}</span>
                                </div>
                            </div>
                            @if($chapterPct === 100)
                            <span style="font-size:0.7rem;padding:2px 8px;border-radius:100px;background:rgba(37,194,110,0.1);border:1px solid rgba(37,194,110,0.2);color:#25c26e;flex-shrink:0;">✓ Fini</span>
                            @endif
                        </div>

                        {{-- Leçons --}}
                        <div style="padding:8px 10px;">
                            @foreach($chapterLessons as $lesson)
                            @php $done = $completedLessonIds->contains($lesson->id); @endphp
                            <div class="lesson-row">
                                <div style="width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.62rem;font-weight:700;flex-shrink:0;{{ $done ? 'background:rgba(37,194,110,0.15);color:#25c26e' : 'background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.2)' }}">
                                    {{ $done ? '✓' : $loop->iteration }}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <span style="font-size:0.82rem;{{ $done ? 'color:rgba(255,255,255,0.7)' : 'color:rgba(255,255,255,0.4)' }}">{{ $lesson->title }}</span>
                                </div>
                                <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                                    @if($lesson->type === 'video')
                                    <span style="font-size:0.65rem;padding:1px 6px;border-radius:100px;background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.15);">🎬</span>
                                    @elseif($lesson->type === 'text')
                                    <span style="font-size:0.65rem;padding:1px 6px;border-radius:100px;background:rgba(167,139,250,0.1);color:#c4b5fd;border:1px solid rgba(167,139,250,0.15);">📄</span>
                                    @endif
                                    @if($lesson->duration)
                                    <span style="font-size:0.65rem;color:rgba(255,255,255,0.25);">{{ $lesson->duration_formatted }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- TAB : Quiz --}}
                <div x-show="tab === 'quizzes'" x-transition>
                    <div class="section-title" style="margin-bottom:4px;">Résultats des quiz</div>
                    <div class="section-sub">{{ $quizAttempts->count() }} tentative(s) au total</div>

                    @forelse($quizAttempts as $attempt)
                    @php
                        $passed = $attempt->passed;
                        $score  = $attempt->score;
                    @endphp
                    <div class="glass2 anim" style="display:flex;align-items:center;gap:14px;padding:14px 16px;margin-bottom:8px;">
                        {{-- Score ring --}}
                        @php
                            $sc  = 2 * M_PI * 18;
                            $sof = $sc * (1 - $score / 100);
                            $sc  = $passed ? '#25c26e' : '#f87171';
                        @endphp
                        <div style="position:relative;width:46px;height:46px;flex-shrink:0;">
                            <svg width="46" height="46" viewBox="0 0 46 46">
                                <circle cx="23" cy="23" r="18" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="5"/>
                                <circle cx="23" cy="23" r="18" fill="none" stroke="{{ $sc }}" stroke-width="5"
                                        stroke-linecap="round"
                                        stroke-dasharray="{{ 2 * M_PI * 18 }}"
                                        stroke-dashoffset="{{ 2 * M_PI * 18 * (1 - $score/100) }}"
                                        style="transform:rotate(-90deg);transform-origin:center;"/>
                            </svg>
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;color:{{ $sc }};">{{ $score }}%</div>
                        </div>

                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.88rem;font-weight:500;color:#fff;margin-bottom:3px;">{{ $attempt->quiz->title }}</div>
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <span style="font-size:0.7rem;color:rgba(255,255,255,0.35);">Tentative #{{ $attempt->attempt_number }}</span>
                                <span style="font-size:0.7rem;color:rgba(255,255,255,0.35);">·</span>
                                <span style="font-size:0.7rem;color:rgba(255,255,255,0.35);">{{ $attempt->earned_points }}/{{ $attempt->total_points }} pts</span>
                                @if($attempt->time_spent)
                                <span style="font-size:0.7rem;color:rgba(255,255,255,0.35);">·</span>
                                @php $sec = $attempt->time_spent; @endphp
                                <span style="font-size:0.7rem;color:rgba(255,255,255,0.35);">{{ $sec < 60 ? $sec.'s' : intdiv($sec,60).'min '.($sec%60).'s' }}</span>
                                @endif
                            </div>
                        </div>

                        <div style="flex-shrink:0;text-align:right;">
                            @if($passed)
                            <span class="score-badge score-pass">✓ Réussi</span>
                            @else
                            <span class="score-badge score-fail">✗ Échoué</span>
                            @endif
                            <div style="font-size:0.68rem;color:rgba(255,255,255,0.25);margin-top:4px;">
                                {{ $attempt->finished_at ? $attempt->finished_at->format('d/m/Y') : $attempt->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="padding:40px 20px;text-align:center;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:14px;">
                        <div style="font-size:2.5rem;margin-bottom:10px;">📝</div>
                        <div style="font-size:0.88rem;color:rgba(255,255,255,0.4);">Aucun quiz passé pour ce cours</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>