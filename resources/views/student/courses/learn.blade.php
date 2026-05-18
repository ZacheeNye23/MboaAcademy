<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $currentLesson?->title ?? 'Cours' }} — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        :root { --sidebar-w: 320px; }
        * { box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #0a1a0f; color: #e0ebe2; margin: 0; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── Topbar ── */
        .learn-topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50; height: 56px;
            display: flex; align-items: center; justify-between; padding: 0 20px;
            background: #081409; border-bottom: 1px solid rgba(37,194,110,0.1);
        }

        /* ── Layout ── */
        .learn-layout { display: flex; padding-top: 56px; min-height: 100vh; }

        /* ── Zone vidéo ── */
        .learn-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

        .video-zone {
            background: #000; width: 100%; aspect-ratio: 16/9; position: relative;
            display: flex; align-items: center; justify-content: center;
        }
        .video-zone video { width: 100%; height: 100%; }
        .video-placeholder {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 16px; color: rgba(255,255,255,0.5); width: 100%; height: 100%;
        }

        /* ── Sidebar curriculum ── */
        .curriculum-sidebar {
            width: var(--sidebar-w); flex-shrink: 0;
            background: #081409; border-left: 1px solid rgba(37,194,110,0.08);
            overflow-y: auto; height: calc(100vh - 56px);
            position: sticky; top: 56px;
        }

        /* ── Contenu leçon ── */
        .lesson-body { flex: 1; padding: 28px; overflow-y: auto; }

        /* ── Chapitres dans sidebar ── */
        .ch-header { padding: 12px 16px; background: rgba(37,194,110,0.06); cursor: pointer; border-bottom: 1px solid rgba(37,194,110,0.06); }
        .ch-header:hover { background: rgba(37,194,110,0.1); }
        .ch-title { font-size: .8rem; font-weight: 700; color: rgba(255,255,255,0.7); }

        .lesson-row { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); cursor: pointer; transition: background .15s; text-decoration: none; }
        .lesson-row:hover { background: rgba(255,255,255,0.04); }
        .lesson-row.current { background: rgba(37,194,110,0.12); border-left: 3px solid #25c26e; }
        .lesson-row.done .lesson-check { color: #25c26e; }
        .lesson-row:not(.done) .lesson-check { color: rgba(255,255,255,0.18); }
        .lesson-title-text { font-size: .8rem; font-weight: 500; color: rgba(255,255,255,0.75); flex: 1; min-width: 0; }
        .lesson-row.current .lesson-title-text { color: #25c26e; font-weight: 600; }

        /* ── Tabs leçon ── */
        .lesson-tabs { display: flex; gap: 0; border-bottom: 1px solid rgba(255,255,255,0.07); padding: 0 28px; background: #0a1a0f; }
        .tab-btn-learn { padding: 12px 18px; font-size: .8rem; font-weight: 600; border: none; background: transparent; cursor: pointer; border-bottom: 2px solid transparent; color: rgba(255,255,255,0.4); transition: all .2s; }
        .tab-btn-learn.active { color: #25c26e; border-bottom-color: #25c26e; }
        .tab-btn-learn:hover:not(.active) { color: rgba(255,255,255,0.7); }

        /* ── Navigation leçons ── */
        .lesson-nav { display: flex; align-items: center; justify-content: space-between; padding: 16px 28px; border-top: 1px solid rgba(255,255,255,0.06); background: #081409; }
        .nav-lesson-btn { display: flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 12px; font-size: .8rem; font-weight: 600; border: none; cursor: pointer; transition: all .2s; }
        .nav-btn-prev { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.6); }
        .nav-btn-prev:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .nav-btn-next { background: linear-gradient(135deg,#1a8a47,#25c26e); color: #fff; box-shadow: 0 4px 12px rgba(37,194,110,0.3); }
        .nav-btn-next:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37,194,110,0.4); }
        .nav-btn-complete { background: rgba(232,184,75,0.15); color: #e8b84b; border: 1px solid rgba(232,184,75,0.3); }
        .nav-btn-complete:hover { background: rgba(232,184,75,0.25); }

        /* ── Resource item ── */
        .resource-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 12px; margin-bottom: 8px; text-decoration: none; transition: all .2s; }
        .resource-item:hover { background: rgba(37,194,110,0.08); border-color: rgba(37,194,110,0.2); }

        /* Progress topbar */
        .progress-bar-top { height: 3px; background: rgba(255,255,255,0.1); position: relative; }
        .progress-bar-top-fill { height: 100%; background: linear-gradient(90deg,#1a8a47,#25c26e); transition: width .5s ease; }
    </style>
</head>
<body x-data="{
    activeTab: 'content',
    completing: false,
    lessonDone: {{ $completedLessonIds->contains($currentLesson?->id) ? 'true' : 'false' }},
    watchTime: 0,
    async markComplete() {
        if (this.lessonDone || this.completing) return;
        this.completing = true;
        try {
            const res = await fetch('{{ route('student.courses.lesson.complete', $currentLesson?->id) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ watch_time: this.watchTime })
            });
            const data = await res.json();
            if (data.success) {
                this.lessonDone = true;
                document.getElementById('prog-fill').style.width = data.progress + '%';
                document.getElementById('prog-text').innerText = data.progress + '%';
            }
        } catch(e) { console.error(e); }
        this.completing = false;
    }
}">

{{-- ── TOPBAR ── --}}
<div class="learn-topbar">
    <div class="flex items-center gap-3">
        <a href="{{ route('student.courses.mine') }}" class="text-white/50 hover:text-white text-sm transition-colors">← Retour</a>
        <div class="h-4 w-px bg-white/15 mx-1"></div>
        <span class="text-sm font-semibold text-white truncate max-w-xs" style="font-family:'Playfair Display',serif">
            {{ $course->title }}
        </span>
    </div>
    <div class="flex items-center gap-4">
        {{-- Progression --}}
        <div class="hidden sm:flex items-center gap-3">
            <div class="w-32 h-1.5 rounded-full bg-white/10 overflow-hidden">
                <div id="prog-fill" class="h-full rounded-full bg-gradient-to-r from-green-600 to-green-400 transition-all duration-500"
                     style="width:{{ $enrollment->progress_percent }}%"></div>
            </div>
            <span id="prog-text" class="text-xs font-semibold" style="color:#25c26e">{{ $enrollment->progress_percent }}%</span>
        </div>
        {{-- Avatar --}}
        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold text-white"
             style="background:linear-gradient(135deg,#1a8a47,#25c26e)">{{ auth()->user()->initials }}</div>
    </div>
</div>

{{-- ── LAYOUT PRINCIPAL ── --}}
<div class="learn-layout">

    {{-- Zone de contenu --}}
    <div class="learn-main">

        {{-- ── Vidéo ou placeholder ── --}}
        <div class="video-zone">
            @if($currentLesson?->video_path)
                <video controls src="{{ asset('storage/' . $currentLesson->video_path) }}"
                       @timeupdate="watchTime = Math.floor($event.target.currentTime)"
                       @ended="markComplete()">
                    Votre navigateur ne supporte pas la vidéo HTML5.
                </video>
            @elseif($currentLesson?->video_url)
                <iframe src="{{ $currentLesson->video_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
            @else
                <div class="video-placeholder">
                    <div class="text-6xl">📄</div>
                    <div class="text-center">
                        <div class="text-white font-semibold mb-1">Leçon textuelle</div>
                        <div class="text-sm">Lisez le contenu ci-dessous</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Tabs ── --}}
        <div class="lesson-tabs">
            @foreach(['content'=>'📖 Contenu','resources'=>'📎 Ressources ('.$currentLesson?->resources->count().')','quiz'=>'📝 Quiz'] as $tab => $label)
            <button class="tab-btn-learn" :class="{ 'active': activeTab === '{{ $tab }}' }"
                    @click="activeTab = '{{ $tab }}'">{{ $label }}</button>
            @endforeach
        </div>

        {{-- ── Corps de la leçon ── --}}
        <div class="lesson-body">

            {{-- Titre + badge ── --}}
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-white text-xl font-bold mb-1" style="font-family:'Playfair Display',serif">
                        {{ $currentLesson?->title }}
                    </h2>
                    <div class="flex items-center gap-3 text-xs" style="color:rgba(255,255,255,0.4)">
                        <span>{{ $currentLesson?->type === 'video' ? '▶ Vidéo' : '📄 Texte' }}</span>
                        @if($currentLesson?->duration)
                        <span>· {{ $currentLesson->duration_formatted }}</span>
                        @endif
                    </div>
                </div>
                {{-- Badge complété --}}
                <div x-show="lessonDone" class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold"
                     style="background:rgba(37,194,110,0.12);color:#25c26e;border:1px solid rgba(37,194,110,0.2)">
                    ✓ Complétée
                </div>
            </div>

            {{-- Tab : Contenu --}}
            <div x-show="activeTab === 'content'">
                @if($currentLesson?->content)
                <div class="prose prose-invert max-w-none text-sm leading-relaxed" style="color:rgba(255,255,255,0.75)">
                    {!! nl2br(e($currentLesson->content)) !!}
                </div>
                @else
                <p style="color:rgba(255,255,255,0.35)" class="text-sm italic">Pas de contenu texte pour cette leçon.</p>
                @endif
            </div>

            {{-- Tab : Ressources --}}
            <div x-show="activeTab === 'resources'">
                @forelse($currentLesson?->resources ?? [] as $resource)
                <a href="{{ $resource->download_url }}" target="_blank" class="resource-item">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shrink-0"
                         style="background:rgba(37,194,110,0.1)">
                        {{ ['pdf'=>'📕','doc'=>'📘','link'=>'🔗','zip'=>'📦'][$resource->type] ?? '📄' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-white">{{ $resource->title }}</div>
                        <div class="text-xs" style="color:rgba(255,255,255,0.35)">
                            {{ strtoupper($resource->type) }}
                            @if($resource->file_size) · {{ $resource->file_size_formatted }} @endif
                        </div>
                    </div>
                    <span style="color:#25c26e;font-size:.75rem;">Télécharger ↓</span>
                </a>
                @empty
                <div class="text-center py-10">
                    <div class="text-4xl mb-3">📎</div>
                    <p class="text-sm" style="color:rgba(255,255,255,0.35)">Aucune ressource pour cette leçon.</p>
                </div>
                @endforelse
            </div>

            {{-- Tab : Quiz --}}
            <div x-show="activeTab === 'quiz'">
                @forelse($currentLesson?->quizzes ?? [] as $quiz)
                <div class="p-5 rounded-2xl mb-3" style="background:rgba(37,194,110,0.06);border:1px solid rgba(37,194,110,0.15)">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-bold text-white mb-1">{{ $quiz->title }}</div>
                            <div class="text-xs" style="color:rgba(255,255,255,0.4)">
                                {{ $quiz->questions->count() }} questions · Score requis : {{ $quiz->passing_score }}%
                                · Max {{ $quiz->max_attempts }} tentative(s)
                            </div>
                        </div>
                        @if($quiz->canAttempt(auth()->id()))
                        <a href="{{ route('student.quizzes.show', $quiz) }}"
                           class="shrink-0 px-4 py-2 rounded-xl text-xs font-semibold text-white transition-all hover:-translate-y-0.5"
                           style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                            Commencer →
                        </a>
                        @else
                        <span class="shrink-0 text-xs px-3 py-1.5 rounded-xl" style="background:rgba(232,184,75,0.1);color:#e8b84b">
                            ✓ Passé ({{ $quiz->bestScoreForUser(auth()->id()) }}%)
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-10">
                    <div class="text-4xl mb-3">📝</div>
                    <p class="text-sm" style="color:rgba(255,255,255,0.35)">Pas de quiz pour cette leçon.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Navigation leçon ── --}}
        @php
            $allLessons     = $course->chapters->flatMap->lessons;
            $currentIndex   = $allLessons->search(fn($l) => $l->id === $currentLesson?->id);
            $prevLesson     = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
            $nextLesson     = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;
        @endphp
        <div class="lesson-nav">
            @if($prevLesson)
            <a href="{{ route('student.courses.learn', [$course->slug, 'lesson' => $prevLesson->id]) }}"
               class="nav-lesson-btn nav-btn-prev">← {{ Str::limit($prevLesson->title, 25) }}</a>
            @else
            <div></div>
            @endif

            {{-- Bouton marquer comme complétée --}}
            <button @click="markComplete()"
                    :disabled="lessonDone || completing"
                    :class="lessonDone ? 'nav-btn-complete' : 'nav-btn-next'"
                    class="nav-lesson-btn">
                <span x-show="!completing" x-text="lessonDone ? '✓ Leçon complétée' : 'Marquer comme complétée'"></span>
                <span x-show="completing">⏳ En cours...</span>
            </button>

            @if($nextLesson)
            <a href="{{ route('student.courses.learn', [$course->slug, 'lesson' => $nextLesson->id]) }}"
               class="nav-lesson-btn nav-btn-next">{{ Str::limit($nextLesson->title, 25) }} →</a>
            @else
            <div></div>
            @endif
        </div>
    </div>

    {{-- ── SIDEBAR CURRICULUM ── --}}
    <aside class="curriculum-sidebar hidden lg:block">
        {{-- Header --}}
        <div class="px-4 py-4 border-b border-white/5">
            <div class="text-xs font-bold uppercase tracking-widest mb-2" style="color:rgba(255,255,255,0.3)">Programme</div>
            <div class="flex items-center gap-2">
                <div class="flex-1 h-1.5 rounded-full bg-white/10 overflow-hidden">
                    <div class="h-full rounded-full" style="width:{{ $enrollment->progress_percent }}%;background:linear-gradient(90deg,#1a8a47,#25c26e)"></div>
                </div>
                <span class="text-xs font-bold" style="color:#25c26e">{{ $enrollment->progress_percent }}%</span>
            </div>
        </div>

        {{-- Chapitres + leçons --}}
        @foreach($course->chapters as $chapter)
        <div>
            <div class="ch-header">
                <div class="ch-title">{{ $loop->iteration }}. {{ $chapter->title }}</div>
                <div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.3)">
                    {{ $chapter->lessons->whereIn('id', $completedLessonIds)->count() }} / {{ $chapter->lessons->count() }} complétées
                </div>
            </div>
            @foreach($chapter->lessons as $lesson)
            @php $isDone = $completedLessonIds->contains($lesson->id); $isCurrent = $lesson->id === $currentLesson?->id; @endphp
            <a href="{{ route('student.courses.learn', [$course->slug, 'lesson' => $lesson->id]) }}"
               class="lesson-row {{ $isCurrent ? 'current' : '' }} {{ $isDone ? 'done' : '' }}">
                <span class="lesson-check text-sm shrink-0">{{ $isDone ? '✓' : ($isCurrent ? '▶' : '○') }}</span>
                <div class="flex-1 min-w-0">
                    <div class="lesson-title-text truncate">{{ $lesson->title }}</div>
                    @if($lesson->duration)
                    <div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.25)">{{ $lesson->duration_formatted }}</div>
                    @endif
                </div>
                @if($lesson->is_free && !$isCurrent)
                <span style="color:rgba(37,194,110,0.6);font-size:10px;font-weight:700;">GRATUIT</span>
                @endif
            </a>
            @endforeach
        </div>
        @endforeach
    </aside>
</div>

</body>
</html>