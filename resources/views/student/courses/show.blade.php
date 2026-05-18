{{-- resources/views/student/courses/show.blade.php --}}
@extends('student.layouts.app')

@section('title', $course->title)
@section('page-title', $course->title)
@section('page-subtitle', 'Par ' . $course->teacher->full_name)

@push('styles')
<style>
    .course-hero {
        background: linear-gradient(135deg, #0d5c2e 0%, #0a1a0f 100%);
        border-radius: 24px; padding: 32px; margin-bottom: 24px; position: relative; overflow: hidden;
    }
    .course-hero::before {
        content:''; position:absolute; inset:0;
        background-image: repeating-linear-gradient(45deg,rgba(37,194,110,0.05) 0,rgba(37,194,110,0.05) 1px,transparent 1px,transparent 30px);
    }
    .chapter-item { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:14px; overflow:hidden; margin-bottom:10px; }
    .chapter-header { display:flex; align-items:center; gap:12px; padding:14px 18px; cursor:pointer; transition:background .2s; }
    .chapter-header:hover { background:rgba(37,194,110,0.04); }
    .lesson-item { display:flex; align-items:center; gap:10px; padding:10px 18px 10px 44px; border-top:1px solid rgba(0,0,0,0.04); transition:background .2s; }
    .lesson-item:hover { background:rgba(37,194,110,0.04); }
    .lesson-item.free-preview .play-btn { opacity:1; }
    .play-btn { opacity:0; transition:opacity .2s; }
    .sidebar-card { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:20px; padding:24px; position:sticky; top:80px; }
    .what-learn-item { display:flex; align-items:flex-start; gap:10px; font-size:.875rem; color:#374151; }
</style>
@endpush

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── COLONNE PRINCIPALE ── --}}
    <div class="lg:col-span-2">

        {{-- Hero du cours --}}
        <div class="course-hero anim d1">
            <div class="relative z-10">
                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="badge-pill" style="background:rgba(37,194,110,0.15);color:#25c26e;border:1px solid rgba(37,194,110,0.3)">
                        {{ $course->category ?? 'Formation' }}
                    </span>
                    <span class="badge-pill" style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.12)">
                        {{ ['beginner'=>'🟢 Débutant','intermediate'=>'🟡 Intermédiaire','advanced'=>'🔴 Avancé'][$course->level] }}
                    </span>
                    <span class="badge-pill" style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.12)">
                        🌍 {{ strtoupper($course->language) }}
                    </span>
                </div>

                {{-- Titre --}}
                <h1 class="text-white text-2xl lg:text-3xl font-black leading-tight mb-3"
                    style="font-family:'Playfair Display',serif">{{ $course->title }}</h1>

                {{-- Description courte --}}
                <p class="text-white/65 text-sm leading-relaxed mb-5 max-w-2xl">
                    {{ Str::limit($course->description, 200) }}
                </p>

                {{-- Méta rapide --}}
                <div class="flex flex-wrap gap-5 text-sm">
                    @if($course->average_rating > 0)
                    <div class="flex items-center gap-1.5">
                        <span style="color:#e8b84b">★ {{ $course->average_rating }}</span>
                        <span style="color:rgba(255,255,255,0.45)">({{ $course->reviews->count() }} avis)</span>
                    </div>
                    @endif
                    <div class="flex items-center gap-1.5">
                        <span>👥</span>
                        <span style="color:rgba(255,255,255,0.7)">{{ $course->enrollments_count }} inscrits</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span>📚</span>
                        <span style="color:rgba(255,255,255,0.7)">{{ $course->total_lessons }} leçons</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span>⏱</span>
                        <span style="color:rgba(255,255,255,0.7)">{{ $course->duration_formatted }}</span>
                    </div>
                </div>

                {{-- Formateur --}}
                <div class="flex items-center gap-3 mt-5 pt-5 border-t border-white/10">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-white"
                         style="background:linear-gradient(135deg,#7a3b1e,#c4682d)">
                        {{ $course->teacher->initials }}
                    </div>
                    <div>
                        <div class="text-white text-sm font-semibold">{{ $course->teacher->full_name }}</div>
                        <div class="text-white/45 text-xs">Formateur MboaAcademy</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ce que vous allez apprendre --}}
        @if($course->what_you_learn)
        <div class="bg-white border border-black/6 rounded-2xl p-6 mb-5 anim d2">
            <h2 class="text-base font-bold text-gray-800 mb-4" style="font-family:'Playfair Display',serif">
                🎯 Ce que vous allez apprendre
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach(explode("\n", $course->what_you_learn) as $item)
                @if(trim($item))
                <div class="what-learn-item">
                    <span class="shrink-0 mt-0.5" style="color:#25c26e">✓</span>
                    <span>{{ trim($item) }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Programme (Chapitres + Leçons) --}}
        <div class="anim d3">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-800" style="font-family:'Playfair Display',serif">
                    📋 Programme du cours
                </h2>
                <span class="text-xs text-gray-400">{{ $course->chapters->count() }} chapitres · {{ $course->total_lessons }} leçons</span>
            </div>

            @foreach($course->chapters as $chapter)
            <div class="chapter-item" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                <div class="chapter-header" @click="open = !open">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0"
                         style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-800">{{ $chapter->title }}</div>
                        <div class="text-xs text-gray-400">{{ $chapter->lessons->count() }} leçon(s)</div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <div x-show="open" x-transition>
                    @foreach($chapter->lessons as $lesson)
                    <div class="lesson-item {{ $lesson->is_free ? 'free-preview' : '' }}">
                        <span class="text-sm {{ $lesson->type === 'video' ? 'text-blue-500' : 'text-gray-400' }}">
                            {{ $lesson->type === 'video' ? '▶' : '📄' }}
                        </span>
                        <span class="flex-1 text-sm text-gray-700">{{ $lesson->title }}</span>
                        <div class="flex items-center gap-2">
                            @if($lesson->duration)
                            <span class="text-xs text-gray-400">{{ $lesson->duration_formatted }}</span>
                            @endif
                            @if($lesson->is_free)
                                <span class="badge-pill badge-green play-btn">Aperçu gratuit</span>
                            @elseif($isEnrolled)
                                <span class="text-green-500 text-xs">✓</span>
                            @else
                                <span class="text-gray-300 text-sm">🔒</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Avis --}}
        @if($course->reviews->count() > 0)
        <div class="mt-6 anim d4">
            <h2 class="text-base font-bold text-gray-800 mb-4" style="font-family:'Playfair Display',serif">
                ⭐ Avis des apprenants
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($course->reviews->take(4) as $review)
                <div class="bg-white border border-black/6 rounded-2xl p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white"
                             style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                            {{ $review->student->initials }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-800">{{ $review->student->full_name }}</div>
                            <div class="flex gap-0.5">
                                @for($i=1; $i<=5; $i++)
                                <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }} text-xs">★</span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed italic">"{{ $review->comment }}"</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── SIDEBAR CTA ── --}}
    <div class="anim d2">
        <div class="sidebar-card">

            {{-- Prix --}}
            <div class="text-center mb-5">
                @if($course->is_free)
                    <div class="text-3xl font-black" style="font-family:'Playfair Display',serif;color:#1a8a47">Gratuit</div>
                    <div class="text-xs text-gray-400 mt-1">Accès illimité</div>
                @else
                    <div class="text-3xl font-black text-gray-800" style="font-family:'Playfair Display',serif">
                        {{ number_format($course->price, 0, ',', ' ') }}
                        <span class="text-lg font-semibold text-gray-500">XAF</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Paiement unique · Accès à vie</div>
                @endif
            </div>

            {{-- CTA inscription --}}
            @if($isEnrolled)
                <a href="{{ route('student.courses.learn', $course->slug) }}"
                   class="flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl font-semibold text-white text-sm transition-all hover:-translate-y-0.5 mb-3"
                   style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 6px 20px rgba(37,194,110,0.3)">
                    ▶ Continuer ma formation
                </a>
                @if($enrollment)
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                        <span>Progression</span>
                        <span class="font-semibold" style="color:#25c26e">{{ $enrollment->progress_percent }}%</span>
                    </div>
                    <div class="prog-bar"><div class="prog-fill" style="width:{{ $enrollment->progress_percent }}%"></div></div>
                </div>
                @endif
            @else
                <form method="POST" action="{{ route('student.courses.enroll', $course) }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl font-semibold text-white text-sm transition-all hover:-translate-y-0.5 mb-3"
                            style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 6px 20px rgba(37,194,110,0.3)">
                        🚀 S'inscrire {{ $course->is_free ? 'gratuitement' : 'maintenant' }}
                    </button>
                </form>
            @endif

            {{-- Ce que le cours inclut --}}
            <div class="space-y-2.5 border-t border-black/5 pt-4">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Ce cours inclut</h4>
                @foreach([
                    ['📚', $course->total_lessons . ' leçons vidéo'],
                    ['⏱', $course->duration_formatted . ' de contenu'],
                    ['📄', 'Ressources téléchargeables'],
                    ['📝', $course->quizzes->count() . ' quiz interactifs'],
                    ['🏆', 'Certificat de complétion'],
                    ['📱', 'Accès illimité'],
                ] as [$icon, $text])
                <div class="flex items-center gap-2.5 text-sm text-gray-600">
                    <span>{{ $icon }}</span> {{ $text }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection