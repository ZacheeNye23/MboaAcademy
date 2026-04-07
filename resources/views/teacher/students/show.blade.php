<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $user->full_name }} — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { font-family:'Outfit',sans-serif; background:#0f1f14; color:#e0ebe2; }
        .sidebar { width:260px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;
                   display:flex;flex-direction:column;
                   background:linear-gradient(180deg,#081409,#0a1a0f);
                   border-right:1px solid rgba(37,194,110,0.08); }
        .main-content { margin-left:260px;min-height:100vh; }
        .nav-item { display:flex;align-items:center;gap:12px;padding:10px 20px;border-radius:12px;
                    color:rgba(255,255,255,0.45);font-size:0.875rem;font-weight:500;
                    text-decoration:none;transition:all 0.2s;margin:2px 12px; }
        .nav-item:hover { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.8); }
        .nav-item.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .nav-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;
                    justify-content:center;font-size:1rem;flex-shrink:0;background:rgba(255,255,255,0.04); }
        .nav-item.active .nav-icon { background:rgba(37,194,110,0.18); }

        /* ── Surfaces ── */
        .glass  { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:18px; }
        .glass2 { background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:12px; }
        .glass-green { background:rgba(37,194,110,0.04);border:1px solid rgba(37,194,110,0.12);border-radius:14px; }
        .glass-gold  { background:rgba(232,184,75,0.05);border:1px solid rgba(232,184,75,0.15);border-radius:14px; }

        /* ── Progress ── */
        .prog-bar  { height:5px;border-radius:3px;background:rgba(255,255,255,0.07);overflow:hidden; }
        .prog-fill { height:100%;border-radius:3px;background:linear-gradient(90deg,#1a8a47,#25c26e);transition:width 0.8s ease; }
        .prog-fill.done { background:#25c26e; }

        /* ── Tabs ── */
        .tab-btn { padding:8px 18px;border-radius:10px;font-size:0.85rem;font-weight:500;
                   cursor:pointer;border:none;background:transparent;transition:all 0.2s;
                   font-family:'Outfit',sans-serif;white-space:nowrap; }
        .tab-btn.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .tab-btn:not(.active) { color:rgba(255,255,255,0.45); }
        .tab-btn:not(.active):hover { color:rgba(255,255,255,0.75); }

        /* ── Score badges ── */
        .s-badge { display:inline-flex;align-items:center;justify-content:center;
                   min-width:46px;padding:3px 9px;border-radius:100px;
                   font-size:0.72rem;font-weight:700; }
        .s-pass { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.25); }
        .s-fail { background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2); }
        .s-mid  { background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }

        /* ── Pills ── */
        .pill { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                border-radius:100px;font-size:0.7rem;font-weight:700; }
        .pill-done { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
        .pill-prog { background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2); }
        .pill-new  { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.4);border:1px solid rgba(255,255,255,0.1); }

        /* ── Leçon row ── */
        .lesson-row { display:flex;align-items:center;gap:10px;padding:9px 14px;
                      border-radius:10px;margin-bottom:5px;transition:background 0.15s; }
        .lesson-row:hover { background:rgba(255,255,255,0.025); }
        .lesson-check { width:22px;height:22px;border-radius:50%;display:flex;align-items:center;
                        justify-content:center;font-size:0.62rem;font-weight:700;flex-shrink:0; }
        .lesson-check.done { background:rgba(37,194,110,0.15);color:#25c26e; }
        .lesson-check.todo { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.2); }

        /* ── Timeline activité ── */
        .timeline-item { display:flex;gap:14px;padding-bottom:16px;position:relative; }
        .timeline-item:not(:last-child)::before {
            content:'';position:absolute;left:14px;top:30px;bottom:0;
            width:1px;background:rgba(255,255,255,0.06); }
        .tl-dot { width:28px;height:28px;border-radius:50%;display:flex;align-items:center;
                  justify-content:center;font-size:0.85rem;flex-shrink:0;border:2px solid transparent; }
        .tl-body { flex:1;min-width:0;padding-top:2px; }

        /* ── Certificat ── */
        .cert-card { background:linear-gradient(135deg,rgba(13,92,46,0.4),rgba(26,138,71,0.2));
                     border:1px solid rgba(37,194,110,0.25);border-radius:16px;padding:22px;
                     position:relative;overflow:hidden; }
        .cert-card::before { content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;
                              border-radius:50%;background:rgba(37,194,110,0.08); }
        .cert-card::after  { content:'';position:absolute;bottom:-20px;left:-20px;width:80px;height:80px;
                             border-radius:50%;background:rgba(232,184,75,0.06); }

        /* ── Btn ── */
        .btn-ghost { display:inline-flex;align-items:center;gap:7px;padding:8px 15px;
                     background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);
                     border-radius:9px;color:rgba(255,255,255,0.6);font-size:0.82rem;font-weight:500;
                     cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .btn-ghost:hover { background:rgba(255,255,255,0.08);color:#fff; }
        .btn-green { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
                     background:linear-gradient(135deg,#1a8a47,#25c26e);border-radius:10px;
                     color:#fff;font-size:0.85rem;font-weight:600;border:none;cursor:pointer;
                     transition:all 0.2s;font-family:'Outfit',sans-serif;text-decoration:none; }
        .btn-green:hover { transform:translateY(-1px);box-shadow:0 6px 18px rgba(37,194,110,0.3); }

        /* ── Section headers ── */
        .sec-title { font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;margin-bottom:3px; }
        .sec-sub   { font-size:0.75rem;color:rgba(255,255,255,0.35);margin-bottom:14px; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .anim-1{animation-delay:.04s}.anim-2{animation-delay:.08s}
        .anim-3{animation-delay:.12s}.anim-4{animation-delay:.16s}.anim-5{animation-delay:.2s}

        /* ── Mini sparkline ── */
        .spark-bar { height:28px;display:flex;align-items:flex-end;gap:3px; }
        .spark-b { border-radius:2px 2px 0 0;min-width:6px;transition:background 0.2s; }
    </style>
</head>
<body x-data="{ tab: 'progress' }">

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>

    {{-- Aperçu apprenant --}}
    <div style="padding:14px 20px;border-bottom:1px solid rgba(255,255,255,0.05);">
        <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:.08rem;color:rgba(255,255,255,0.2);margin-bottom:10px;">Apprenant</div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#1a8a47,#25c26e);display:flex;align-items:center;justify-content:center;font-size:0.82rem;font-weight:700;color:#fff;flex-shrink:0;">
                {{ $user->initials }}
            </div>
            <div style="min-width:0;">
                <div style="font-size:0.85rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $user->full_name }}</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $user->email }}</div>
            </div>
        </div>
        <div class="prog-bar" style="margin-bottom:4px;">
            <div class="prog-fill {{ $progressPct >= 100 ? 'done' : '' }}" style="width:{{ $progressPct }}%"></div>
        </div>
        <div style="font-size:0.7rem;color:rgba(255,255,255,0.3);">{{ $progressPct }}% complété · {{ $completedCount }}/{{ $totalLessons }} leçons</div>
    </div>

    <nav style="flex:1;padding:16px 0;overflow-y:auto;">
        <a href="{{ route('teacher.dashboard') }}" class="nav-item"><span class="nav-icon">🏠</span>Dashboard</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="nav-icon">📚</span>Mes cours</a>
        <a href="{{ route('teacher.quizzes.index') }}" class="nav-item"><span class="nav-icon">📝</span>Quiz</a>
        <a href="{{ route('teacher.students.index') }}" class="nav-item active"><span class="nav-icon">👥</span>Mes apprenants</a>
        <a href="#" class="nav-item"><span class="nav-icon">💰</span>Revenus</a>
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

    {{-- Topbar --}}
    <header style="position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;padding:12px 32px;border-bottom:1px solid rgba(37,194,110,0.08);background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);">
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="{{ route('teacher.students.index') }}" class="btn-ghost" style="padding:6px 12px;">← Apprenants</a>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#1a8a47,#25c26e);display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;color:#fff;flex-shrink:0;">
                    {{ $user->initials }}
                </div>
                <div>
                    <span style="font-size:0.92rem;font-weight:600;color:#fff;">{{ $user->full_name }}</span>
                    <span style="margin-left:8px;font-size:0.72rem;color:rgba(255,255,255,0.35);">{{ $course->title }}</span>
                </div>
            </div>
        </div>
        @if($certificate)
        <a href="{{ route('teacher.students.index') }}" class="btn-ghost" style="font-size:0.8rem;color:#e8b84b;border-color:rgba(232,184,75,0.25);">
            🎓 Certificat émis
        </a>
        @endif
    </header>

    <div style="padding:24px 32px;">

        {{-- ── LIGNE 1 : Hero card + KPIs ── --}}
        <div style="display:grid;grid-template-columns:280px 1fr;gap:18px;margin-bottom:20px;">

            {{-- Profil hero --}}
            <div class="glass anim anim-1" style="padding:24px;text-align:center;">
                {{-- Avatar large --}}
                <div style="position:relative;display:inline-block;margin-bottom:14px;">
                    {{-- Ring SVG --}}
                    @php
                        $r     = 46;
                        $circ  = 2 * M_PI * $r;
                        $off   = $circ * (1 - $progressPct / 100);
                        $rColor = $progressPct >= 100 ? '#25c26e' : ($progressPct > 0 ? '#3b82f6' : 'rgba(255,255,255,0.1)');
                    @endphp
                    <svg width="108" height="108" viewBox="0 0 108 108" style="position:absolute;top:0;left:0;">
                        <circle cx="54" cy="54" r="{{ $r }}" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="8"/>
                        <circle cx="54" cy="54" r="{{ $r }}" fill="none" stroke="{{ $rColor }}" stroke-width="8"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $circ }}"
                                stroke-dashoffset="{{ $off }}"
                                style="transform:rotate(-90deg);transform-origin:center;transition:stroke-dashoffset 1.2s ease;"/>
                    </svg>
                    <div style="width:108px;height:108px;border-radius:50%;background:linear-gradient(135deg,#1a8a47,#25c26e);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:#fff;">
                        {{ $user->initials }}
                    </div>
                </div>

                <div style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:2px;">{{ $user->full_name }}</div>
                <div style="font-size:0.75rem;color:rgba(255,255,255,0.38);margin-bottom:12px;">{{ $user->email }}</div>

                {{-- Statut --}}
                @if($enrollment->completed_at)
                <span class="pill pill-done" style="margin-bottom:12px;">✓ Cours terminé</span>
                @elseif($progressPct > 0)
                <span class="pill pill-prog" style="margin-bottom:12px;">⏳ En cours</span>
                @else
                <span class="pill pill-new" style="margin-bottom:12px;">○ Pas commencé</span>
                @endif

                {{-- Métadonnées --}}
                <div style="display:flex;flex-direction:column;gap:7px;margin-top:12px;text-align:left;">
                    @foreach([
                        ['📅', 'Inscrit le',   $enrollment->enrolled_at?->format('d/m/Y') ?? '—'],
                        ['🏁', 'Terminé le',   $enrollment->completed_at?->format('d/m/Y') ?? '—'],
                        ['🌍', 'Pays',          $user->country ?? '—'],
                        ['📱', 'Téléphone',     $user->phone ?? '—'],
                    ] as [$icon, $lbl, $val])
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 10px;border-radius:8px;background:rgba(255,255,255,0.02);">
                        <span style="font-size:0.73rem;color:rgba(255,255,255,0.38);">{{ $icon }} {{ $lbl }}</span>
                        <span style="font-size:0.75rem;font-weight:500;color:rgba(255,255,255,0.65);">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- KPIs ── --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;gap:14px;">

                {{-- Progression --}}
                <div class="glass2 anim anim-2" style="padding:18px;display:flex;flex-direction:column;justify-content:space-between;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                        <span style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.3);font-weight:600;">Progression</span>
                        <span style="font-size:0.68rem;color:rgba(255,255,255,0.25);">{{ $completedCount }}/{{ $totalLessons }} leçons</span>
                    </div>
                    <div style="font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:700;color:{{ $rColor }};line-height:1;margin-bottom:8px;">{{ $progressPct }}%</div>
                    <div class="prog-bar">
                        <div class="prog-fill {{ $progressPct >= 100 ? 'done' : '' }}" style="width:{{ $progressPct }}%"></div>
                    </div>
                    @if($enrollment->completed_at)
                    <div style="font-size:0.72rem;color:#25c26e;margin-top:6px;">✓ Terminé le {{ $enrollment->completed_at->format('d/m/Y') }}</div>
                    @endif
                </div>

                {{-- Quiz --}}
                <div class="glass2 anim anim-2" style="padding:18px;">
                    <span style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.3);font-weight:600;display:block;margin-bottom:10px;">Quiz</span>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        @php
                            $bestScore  = $quizAttempts->max('score') ?? 0;
                            $avgScore   = $quizAttempts->count() > 0 ? round($quizAttempts->avg('score'),1) : 0;
                            $passedQuiz = $quizAttempts->where('passed',true)->pluck('quiz_id')->unique()->count();
                            $totalQuizzes = $quizSummary->count();
                        @endphp
                        <div style="text-align:center;padding:8px;border-radius:9px;background:rgba(255,255,255,0.02);">
                            <div style="font-size:1.2rem;font-weight:700;color:#e8b84b;">{{ $bestScore }}%</div>
                            <div style="font-size:0.6rem;color:rgba(255,255,255,0.28);text-transform:uppercase;letter-spacing:.04rem;">Meilleur</div>
                        </div>
                        <div style="text-align:center;padding:8px;border-radius:9px;background:rgba(255,255,255,0.02);">
                            <div style="font-size:1.2rem;font-weight:700;color:#a78bfa;">{{ $avgScore }}%</div>
                            <div style="font-size:0.6rem;color:rgba(255,255,255,0.28);text-transform:uppercase;letter-spacing:.04rem;">Moy.</div>
                        </div>
                        <div style="text-align:center;padding:8px;border-radius:9px;background:rgba(255,255,255,0.02);">
                            <div style="font-size:1.2rem;font-weight:700;color:#25c26e;">{{ $passedQuiz }}/{{ $totalQuizzes }}</div>
                            <div style="font-size:0.6rem;color:rgba(255,255,255,0.28);text-transform:uppercase;letter-spacing:.04rem;">Réussis</div>
                        </div>
                        <div style="text-align:center;padding:8px;border-radius:9px;background:rgba(255,255,255,0.02);">
                            <div style="font-size:1.2rem;font-weight:700;color:#3b82f6;">{{ $quizAttempts->count() }}</div>
                            <div style="font-size:0.6rem;color:rgba(255,255,255,0.28);text-transform:uppercase;letter-spacing:.04rem;">Tentatives</div>
                        </div>
                    </div>
                </div>

                {{-- Temps & Engagement --}}
                <div class="glass2 anim anim-3" style="padding:18px;">
                    <span style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.3);font-weight:600;display:block;margin-bottom:10px;">Temps & Engagement</span>
                    @php
                        $totalSec   = $totalWatchSeconds + $totalQuizTime;
                        $fmtTime    = fn(int $s) => $s < 60 ? $s.'s' : (intdiv($s,60) < 60 ? intdiv($s,60).'min' : intdiv($s,3600).'h '.intdiv($s%3600,60).'min');
                    @endphp
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach([
                            ['⏱', 'Vidéos vues',     $fmtTime($totalWatchSeconds), '#25c26e'],
                            ['📝', 'Temps quiz',      $fmtTime($totalQuizTime),     '#3b82f6'],
                            ['💬', 'Posts forum',     $forumRepliesCount + $forumActivity->count(), '#a78bfa'],
                        ] as [$icon, $lbl, $val, $color])
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:0.78rem;color:rgba(255,255,255,0.45);">{{ $icon }} {{ $lbl }}</span>
                            <span style="font-size:0.82rem;font-weight:600;color:{{ $color }};">{{ $val }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Certificat --}}
                @if($certificate)
                <div class="cert-card anim anim-3">
                    <div style="position:relative;z-index:1;">
                        <div style="font-size:1.8rem;margin-bottom:8px;">🎓</div>
                        <div style="font-size:0.85rem;font-weight:600;color:#fff;margin-bottom:3px;">Certificat émis</div>
                        <div style="font-size:0.7rem;color:rgba(255,255,255,0.45);margin-bottom:10px;">
                            N° {{ $certificate->certificate_number }}<br>
                            {{ $certificate->issued_at->format('d/m/Y') }}
                        </div>
                        @if($certificate->file_path)
                        <a href="{{ $certificate->download_url }}" class="btn-green" style="font-size:0.78rem;padding:7px 14px;">
                            ⬇ Télécharger
                        </a>
                        @endif
                    </div>
                </div>
                @else
                <div class="glass2 anim anim-3" style="padding:18px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;">
                    <div style="font-size:2rem;margin-bottom:8px;opacity:0.4;">🎓</div>
                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.35);margin-bottom:4px;">Pas encore de certificat</div>
                    <div style="font-size:0.72rem;color:rgba(255,255,255,0.2);">
                        {{ $progressPct < 100 ? 'Complétion du cours requise' : 'En attente d\'émission' }}
                    </div>
                </div>
                @endif

            </div>
        </div>

        {{-- ── AUTRES COURS ── --}}
        @if($otherEnrollments->count() > 0)
        <div class="glass anim anim-3" style="padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <span style="font-size:0.75rem;color:rgba(255,255,255,0.35);flex-shrink:0;">Autres cours :</span>
            @foreach($otherEnrollments as $oe)
            <a href="{{ route('teacher.students.show', $oe) }}"
               style="display:inline-flex;align-items:center;gap:8px;padding:6px 12px;border-radius:100px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);text-decoration:none;transition:border-color 0.2s;"
               onmouseover="this.style.borderColor='rgba(37,194,110,0.3)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'">
                <span style="font-size:0.78rem;color:rgba(255,255,255,0.6);">{{ Str::limit($oe->course->title, 25) }}</span>
                <span style="font-size:0.7rem;font-weight:700;color:{{ $oe->progress_percent >= 100 ? '#25c26e' : '#60a5fa' }};">{{ $oe->progress_percent }}%</span>
            </a>
            @endforeach
        </div>
        @endif

        {{-- ── TABS ── --}}
        <div style="display:flex;gap:4px;margin-bottom:20px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:5px;width:fit-content;flex-wrap:wrap;" class="anim anim-3">
            <button class="tab-btn" :class="tab==='progress'?'active':''" x-on:click="tab='progress'">
                📚 Progression ({{ $completedCount }}/{{ $totalLessons }})
            </button>
            <button class="tab-btn" :class="tab==='quizzes'?'active':''"  x-on:click="tab='quizzes'">
                📝 Quiz ({{ $quizSummary->count() }})
            </button>
            <button class="tab-btn" :class="tab==='activity'?'active':''" x-on:click="tab='activity'">
                🕐 Activité ({{ count($recentActivity) }})
            </button>
            @if($forumActivity->count() > 0)
            <button class="tab-btn" :class="tab==='forum'?'active':''"    x-on:click="tab='forum'">
                💬 Forum ({{ $forumActivity->count() }})
            </button>
            @endif
        </div>

        {{-- ════════════════════
            TAB : PROGRESSION
        ════════════════════ --}}
        <div x-show="tab === 'progress'" x-transition>
            <div style="display:grid;grid-template-columns:1fr 260px;gap:18px;align-items:start;">

                {{-- Chapitres & leçons --}}
                <div>
                    @foreach($chapterStats as $cs)
                    @php $chapter = $cs['chapter']; @endphp
                    <div class="glass2 anim" style="margin-bottom:12px;overflow:hidden;">

                        {{-- Header chapitre --}}
                        <div style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid rgba(255,255,255,0.05);"
                             x-data="{open:true}" x-on:click="open=!open" style="cursor:pointer">
                            <div style="width:26px;height:26px;border-radius:8px;background:{{ $cs['pct']===100 ? 'rgba(37,194,110,0.15)' : 'rgba(255,255,255,0.06)' }};display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:{{ $cs['pct']===100 ? '#25c26e' : 'rgba(255,255,255,0.4)' }};flex-shrink:0;">
                                {{ $cs['pct'] === 100 ? '✓' : $loop->iteration }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:0.9rem;font-weight:600;color:#fff;margin-bottom:5px;">{{ $chapter->title }}</div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="prog-bar" style="flex:1;height:4px;">
                                        <div class="prog-fill {{ $cs['pct']===100?'done':'' }}" style="width:{{ $cs['pct'] }}%"></div>
                                    </div>
                                    <span style="font-size:0.68rem;font-weight:600;white-space:nowrap;color:{{ $cs['pct']===100 ? '#25c26e' : 'rgba(255,255,255,0.4)' }};">
                                        {{ $cs['completed'] }}/{{ $cs['total'] }}
                                    </span>
                                    @if($cs['watch_time'] > 0)
                                    @php $sec = $cs['watch_time']; $fmt = $sec < 60 ? $sec.'s' : intdiv($sec,60).'min'; @endphp
                                    <span style="font-size:0.65rem;color:rgba(255,255,255,0.2);">⏱ {{ $fmt }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Leçons --}}
                        <div style="padding:8px 12px;">
                            @foreach($chapter->lessons as $lesson)
                            @php
                                $lp       = $lessonProgressMap->get($lesson->id);
                                $isDone   = $lp && $lp->is_completed;
                                $wt       = $lp ? $lp->watch_time : 0;
                            @endphp
                            <div class="lesson-row">
                                <div class="lesson-check {{ $isDone ? 'done' : 'todo' }}">
                                    {{ $isDone ? '✓' : $loop->iteration }}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <span style="font-size:0.83rem;{{ $isDone ? 'color:rgba(255,255,255,0.75)' : 'color:rgba(255,255,255,0.38)' }};">
                                        {{ $lesson->title }}
                                    </span>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                                    {{-- Type --}}
                                    @if($lesson->type === 'video')
                                    <span style="font-size:0.62rem;padding:1px 6px;border-radius:100px;background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.15);">🎬</span>
                                    @elseif($lesson->type === 'text')
                                    <span style="font-size:0.62rem;padding:1px 6px;border-radius:100px;background:rgba(167,139,250,0.1);color:#c4b5fd;border:1px solid rgba(167,139,250,0.15);">📄</span>
                                    @elseif($lesson->type === 'mixed')
                                    <span style="font-size:0.62rem;padding:1px 6px;border-radius:100px;background:rgba(249,115,22,0.1);color:#fb923c;border:1px solid rgba(249,115,22,0.15);">🔀</span>
                                    @endif

                                    {{-- Durée --}}
                                    @if($lesson->duration)
                                    <span style="font-size:0.65rem;color:rgba(255,255,255,0.22);">{{ $lesson->duration_formatted }}</span>
                                    @endif

                                    {{-- Temps regardé --}}
                                    @if($wt > 0)
                                    @php $fwt = $wt < 60 ? $wt.'s' : intdiv($wt,60).'min'; @endphp
                                    <span style="font-size:0.62rem;color:rgba(37,194,110,0.5);">⏱ {{ $fwt }}</span>
                                    @endif

                                    {{-- Date complétion --}}
                                    @if($isDone && $lp->completed_at)
                                    <span style="font-size:0.62rem;color:rgba(255,255,255,0.18);">{{ $lp->completed_at->format('d/m') }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Résumé progression côté droit --}}
                <div style="position:sticky;top:76px;display:flex;flex-direction:column;gap:14px;">

                    {{-- Stats par chapitre --}}
                    <div class="glass" style="padding:18px;">
                        <div class="sec-title" style="margin-bottom:12px;">Par chapitre</div>
                        @foreach($chapterStats as $cs)
                        <div style="margin-bottom:10px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                                <span style="font-size:0.75rem;color:rgba(255,255,255,0.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">{{ Str::limit($cs['chapter']->title, 18) }}</span>
                                <span style="font-size:0.72rem;font-weight:600;color:{{ $cs['pct']===100?'#25c26e':'rgba(255,255,255,0.4)' }};flex-shrink:0;margin-left:6px;">{{ $cs['pct'] }}%</span>
                            </div>
                            <div class="prog-bar" style="height:4px;">
                                <div class="prog-fill {{ $cs['pct']===100?'done':'' }}" style="width:{{ $cs['pct'] }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Ressources téléchargées --}}
                    @php $totalResources = $course->chapters->flatMap->lessons->flatMap->resources->count(); @endphp
                    @if($totalResources > 0)
                    <div class="glass" style="padding:16px;">
                        <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.3);margin-bottom:10px;font-weight:600;">Ressources</div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="font-size:1.5rem;">📎</div>
                            <div>
                                <div style="font-size:1rem;font-weight:700;color:#fff;">{{ $totalResources }}</div>
                                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);">ressource(s) disponible(s)</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ════════════════════
            TAB : QUIZ
        ════════════════════ --}}
        <div x-show="tab === 'quizzes'" x-transition>

            @forelse($quizSummary as $qs)
            @php
                $quiz       = $qs['quiz'];
                $bestScore  = $qs['best_score'];
                $scoreColor = $bestScore >= $quiz->passing_score ? '#25c26e' : '#f87171';
            @endphp
            <div class="glass2 anim" style="margin-bottom:14px;overflow:hidden;">

                {{-- Header quiz --}}
                <div style="display:flex;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.05);">

                    {{-- Score ring --}}
                    @php
                        $sc  = 2 * M_PI * 24;
                        $soff = $sc * (1 - $bestScore / 100);
                    @endphp
                    <div style="position:relative;width:56px;height:56px;flex-shrink:0;">
                        <svg width="56" height="56" viewBox="0 0 56 56">
                            <circle cx="28" cy="28" r="24" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="6"/>
                            <circle cx="28" cy="28" r="24" fill="none" stroke="{{ $scoreColor }}" stroke-width="6"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $sc }}"
                                    stroke-dashoffset="{{ $soff }}"
                                    style="transform:rotate(-90deg);transform-origin:center;transition:stroke-dashoffset 1s ease;"/>
                        </svg>
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:{{ $scoreColor }};">{{ $bestScore }}%</div>
                    </div>

                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                            <span style="font-size:0.92rem;font-weight:600;color:#fff;">{{ $quiz->title }}</span>
                            @if($qs['passed'])
                            <span class="s-badge s-pass">✓ Réussi</span>
                            @else
                            <span class="s-badge s-fail">✗ Échoué</span>
                            @endif
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;font-size:0.72rem;color:rgba(255,255,255,0.35);flex-wrap:wrap;">
                            <span>{{ $qs['attempts'] }} tentative(s) / {{ $quiz->max_attempts }} max</span>
                            <span>·</span>
                            <span>Score requis : {{ $quiz->passing_score }}%</span>
                            <span>·</span>
                            <span>Score moy. : {{ $qs['avg_score'] }}%</span>
                            <span>·</span>
                            <span>{{ $qs['last_attempt']->diffForHumans() }}</span>
                        </div>
                    </div>

                    {{-- Sparkline tentatives --}}
                    @if($qs['all_attempts']->count() > 1)
                    <div style="flex-shrink:0;">
                        @php $attempts = $qs['all_attempts']->sortBy('attempt_number'); $maxSc = $attempts->max('score') ?: 1; @endphp
                        <div class="spark-bar" style="height:32px;">
                            @foreach($attempts as $att)
                            @php $h = max(10, round($att->score / 100 * 100)); $c = $att->passed ? '#25c26e' : '#f87171'; @endphp
                            <div class="spark-b" style="height:{{ $h }}%;background:{{ $c }};opacity:0.7;min-width:8px;flex:1;" title="#{{ $att->attempt_number }} : {{ $att->score }}%"></div>
                            @endforeach
                        </div>
                        <div style="font-size:0.6rem;color:rgba(255,255,255,0.2);text-align:center;margin-top:2px;">évolution</div>
                    </div>
                    @endif
                </div>

                {{-- Détail tentatives --}}
                <div style="padding:10px 20px;">
                    @foreach($qs['all_attempts']->sortBy('attempt_number') as $attempt)
                    <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.03);">
                        <span style="font-size:0.72rem;color:rgba(255,255,255,0.3);width:24px;flex-shrink:0;">#{{ $attempt->attempt_number }}</span>
                        <div style="flex:1;">
                            <div class="prog-bar" style="height:4px;">
                                <div class="prog-fill" style="width:{{ $attempt->score }}%;background:{{ $attempt->passed ? '#25c26e' : '#f87171' }};"></div>
                            </div>
                        </div>
                        <span class="s-badge {{ $attempt->score >= $quiz->passing_score ? 's-pass' : 's-fail' }}" style="min-width:42px;">{{ $attempt->score }}%</span>
                        <span style="font-size:0.68rem;color:rgba(255,255,255,0.25);width:60px;text-align:right;flex-shrink:0;">{{ $attempt->earned_points }}/{{ $attempt->total_points }} pts</span>
                        @if($attempt->time_spent)
                        @php $sec = $attempt->time_spent; @endphp
                        <span style="font-size:0.65rem;color:rgba(255,255,255,0.2);width:50px;flex-shrink:0;">{{ $sec < 60 ? $sec.'s' : intdiv($sec,60).'min' }}</span>
                        @endif
                        <span style="font-size:0.65rem;color:rgba(255,255,255,0.2);width:70px;text-align:right;flex-shrink:0;">
                            {{ ($attempt->finished_at ?? $attempt->created_at)->format('d/m/Y') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="glass" style="padding:50px 20px;text-align:center;">
                <div style="font-size:3rem;margin-bottom:10px;">📝</div>
                <div style="font-size:0.9rem;color:rgba(255,255,255,0.4);">Aucun quiz passé pour ce cours</div>
            </div>
            @endforelse
        </div>

        {{-- ════════════════════
            TAB : ACTIVITÉ
        ════════════════════ --}}
        <div x-show="tab === 'activity'" x-transition>
            <div style="display:grid;grid-template-columns:1fr 300px;gap:18px;">

                {{-- Timeline --}}
                <div class="glass" style="padding:22px;">
                    <div class="sec-title">Fil d'activité</div>
                    <div class="sec-sub">Toutes les actions de cet apprenant sur ce cours</div>

                    @forelse($recentActivity as $act)
                    <div class="timeline-item">
                        <div class="tl-dot" style="background:{{ $act['color'] }}18;border-color:{{ $act['color'] }}30;">
                            <span style="font-size:0.85rem;">{{ $act['icon'] }}</span>
                        </div>
                        <div class="tl-body">
                            <div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-bottom:2px;">
                                <span style="font-size:0.85rem;font-weight:500;color:#fff;">{{ $act['title'] }}</span>
                                <span style="font-size:0.68rem;color:rgba(255,255,255,0.25);flex-shrink:0;">
                                    {{ $act['time'] ? \Carbon\Carbon::parse($act['time'])->diffForHumans() : '' }}
                                </span>
                            </div>
                            <div style="font-size:0.78rem;color:rgba(255,255,255,0.45);">{{ $act['detail'] }}</div>
                            @if($act['meta'])
                            <span style="display:inline-block;margin-top:4px;font-size:0.65rem;padding:1px 7px;border-radius:100px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.35);">{{ $act['meta'] }}</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:30px 0;font-size:0.88rem;color:rgba(255,255,255,0.35);">Aucune activité enregistrée</div>
                    @endforelse
                </div>

                {{-- Synthèse rapide --}}
                <div style="display:flex;flex-direction:column;gap:14px;">

                    {{-- Récapitulatif --}}
                    <div class="glass" style="padding:18px;">
                        <div class="sec-title" style="margin-bottom:12px;">Récapitulatif</div>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            @foreach([
                                ['✅', 'Leçons terminées',  $completedCount . ' / ' . $totalLessons, '#25c26e'],
                                ['📝', 'Quiz passés',       $quizAttempts->count() . ' tentative(s)', '#3b82f6'],
                                ['🏆', 'Quiz réussis',      $quizSummary->where('passed',true)->count() . ' / ' . $quizSummary->count(), '#e8b84b'],
                                ['💬', 'Posts forum',       ($forumActivity->count() + $forumRepliesCount) . ' post(s)', '#a78bfa'],
                            ] as [$icon, $lbl, $val, $color])
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:9px;background:rgba(255,255,255,0.02);">
                                <span style="font-size:0.78rem;color:rgba(255,255,255,0.45);">{{ $icon }} {{ $lbl }}</span>
                                <span style="font-size:0.82rem;font-weight:600;color:{{ $color }};">{{ $val }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Certificat --}}
                    @if($certificate)
                    <div class="cert-card">
                        <div style="position:relative;z-index:1;">
                            <div style="font-size:1.5rem;margin-bottom:6px;">🎓</div>
                            <div style="font-size:0.88rem;font-weight:600;color:#fff;margin-bottom:2px;">Certificat obtenu</div>
                            <div style="font-size:0.7rem;color:rgba(255,255,255,0.45);margin-bottom:10px;">
                                N° {{ $certificate->certificate_number }}<br>
                                Émis le {{ $certificate->issued_at->format('d/m/Y') }}
                            </div>
                            @if($certificate->file_path)
                            <a href="{{ $certificate->download_url }}" class="btn-green" style="font-size:0.78rem;padding:7px 14px;">⬇ Télécharger</a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ════════════════════
            TAB : FORUM
        ════════════════════ --}}
        @if($forumActivity->count() > 0)
        <div x-show="tab === 'forum'" x-transition>
            <div class="glass" style="overflow:hidden;">
                <div style="padding:18px 22px;border-bottom:1px solid rgba(255,255,255,0.07);">
                    <div class="sec-title">Discussions du forum</div>
                    <div class="sec-sub">Threads créés par {{ $user->first_name }} dans ce cours</div>
                </div>
                @foreach($forumActivity as $thread)
                <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 22px;border-bottom:1px solid rgba(255,255,255,0.04);">
                    <div style="width:32px;height:32px;border-radius:50%;background:rgba(167,139,250,0.12);display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;margin-top:2px;">💬</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.88rem;font-weight:500;color:#fff;margin-bottom:3px;">{{ $thread->title }}</div>
                        <div style="font-size:0.78rem;color:rgba(255,255,255,0.4);margin-bottom:5px;line-height:1.5;">
                            {{ Str::limit($thread->body, 120) }}
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;font-size:0.68rem;color:rgba(255,255,255,0.25);">
                            <span>{{ $thread->replies_count }} réponse(s)</span>
                            <span>·</span>
                            <span>{{ $thread->created_at->diffForHumans() }}</span>
                            @if($thread->is_solved)<span>·</span><span style="color:#25c26e;">✓ Résolu</span>@endif
                        </div>
                    </div>
                    <a href="{{ route('teacher.forum.show', [$course->slug, $thread]) }}"
                       class="btn-ghost" style="font-size:0.78rem;flex-shrink:0;">
                       Voir →
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
</body>
</html>