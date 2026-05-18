@extends('student.layouts.app')

@section('title', 'Mes Certificats')
@section('page-title', 'Mes Certificats')
@section('page-subtitle', $certificates->count() . ' certificat(s) obtenu(s)')

@push('styles')
<style>
    /* ── Hero ── */
    .cert-hero {
        background: linear-gradient(135deg, #0a1a0f 0%, #0d5c2e 60%, #1a8a47 100%);
        border-radius: 24px; padding: 32px 36px;
        position: relative; overflow: hidden; margin-bottom: 28px;
    }
    .cert-hero::before {
        content: ''; position: absolute; inset: 0;
        background-image:
            radial-gradient(circle at 15% 50%, rgba(232,184,75,0.12) 0%, transparent 45%),
            radial-gradient(circle at 85% 30%, rgba(37,194,110,0.12) 0%, transparent 45%),
            repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0, rgba(255,255,255,0.02) 1px, transparent 1px, transparent 26px);
    }

    /* ── Certificate card ── */
    .cert-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 22px;
        overflow: hidden;
        transition: all .3s;
        position: relative;
    }
    .cert-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        border-color: rgba(232,184,75,0.3);
    }

    /* ── Miniature certificat ── */
    .cert-preview {
        height: 180px;
        background: linear-gradient(135deg, #faf6ef 0%, #f0ebe0 100%);
        position: relative;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .cert-preview::before {
        content: ''; position: absolute; inset: 8px;
        border: 1.5px solid rgba(232,184,75,0.35);
        border-radius: 10px;
        pointer-events: none;
    }
    .cert-preview::after {
        content: '';
        position: absolute; inset: 0;
        background-image:
            repeating-linear-gradient(0deg, transparent, transparent 29px, rgba(232,184,75,0.06) 30px),
            repeating-linear-gradient(90deg, transparent, transparent 29px, rgba(232,184,75,0.06) 30px);
    }
    .cert-inner {
        position: relative; z-index: 2;
        text-align: center; padding: 12px;
    }
    .cert-seal {
        width: 52px; height: 52px; border-radius: 50%;
        background: linear-gradient(135deg, #e8b84b, #f0d070);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; margin: 0 auto 8px;
        box-shadow: 0 4px 12px rgba(232,184,75,0.4);
    }

    /* ── Badge nouveau ── */
    .new-ribbon {
        position: absolute; top: 12px; right: -20px;
        background: linear-gradient(135deg, #25c26e, #1a8a47);
        color: #fff; font-size: .6rem; font-weight: 800;
        padding: 3px 28px; transform: rotate(35deg);
        letter-spacing: .5px; z-index: 10;
    }

    /* ── Boutons ── */
    .btn-gold {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 12px;
        font-size: .8rem; font-weight: 700;
        background: linear-gradient(135deg, #e8b84b, #f0d070);
        color: #0a1a0f; text-decoration: none;
        transition: all .2s;
        box-shadow: 0 4px 12px rgba(232,184,75,0.3);
    }
    .btn-gold:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(232,184,75,0.45); }

    .btn-outline-dark {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 12px;
        font-size: .8rem; font-weight: 600;
        background: rgba(0,0,0,0.04); color: #6b7280;
        text-decoration: none; transition: all .2s;
        border: 1px solid rgba(0,0,0,0.1);
    }
    .btn-outline-dark:hover { background: rgba(0,0,0,0.07); color: #374151; }

    /* ── Partage ── */
    .share-btn {
        width: 34px; height: 34px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; cursor: pointer; transition: all .2s;
        border: 1px solid rgba(0,0,0,0.1); background: #fff;
        text-decoration: none;
    }
    .share-btn:hover { transform: scale(1.1); }
    .share-linkedin { border-color: rgba(10,102,194,0.25); color: #0a66c2; }
    .share-linkedin:hover { background: rgba(10,102,194,0.08); }
    .share-twitter { border-color: rgba(0,0,0,0.15); color: #000; }
    .share-twitter:hover { background: rgba(0,0,0,0.05); }
    .share-copy { border-color: rgba(37,194,110,0.25); color: #1a8a47; }
    .share-copy:hover { background: rgba(37,194,110,0.08); }

    /* ── In progress ── */
    .in-progress-card {
        background: #fff; border: 1px dashed rgba(37,194,110,0.3);
        border-radius: 22px; overflow: hidden; opacity: .85;
    }
    .prog-bar-cert { height: 5px; border-radius: 3px; background: rgba(0,0,0,0.08); overflow: hidden; }
    .prog-fill-cert { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #1a8a47, #25c26e); }

    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation: fadeUp .45s ease both; }
    .d1{animation-delay:.05s}.d2{animation-delay:.10s}.d3{animation-delay:.15s}
    .d4{animation-delay:.20s}.d5{animation-delay:.25s}.d6{animation-delay:.30s}

    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
    .float { animation: float 4s ease-in-out infinite; }

    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<div class="cert-hero anim d1">
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center gap-6">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl float"
                     style="background:rgba(232,184,75,0.15);border:1.5px solid rgba(232,184,75,0.3)">🎓</div>
                <div>
                    <h2 class="text-white text-xl font-black" style="font-family:'Playfair Display',serif">
                        Mes Certificats
                    </h2>
                    <p class="text-white/50 text-xs">Preuves officielles de vos compétences</p>
                </div>
            </div>
            <p class="text-white/55 text-sm leading-relaxed max-w-lg">
                Chaque certificat est <strong class="text-white/80">vérifiable</strong> et peut être partagé sur LinkedIn,
                ajouté à votre CV ou envoyé à un employeur.
            </p>
        </div>

        {{-- Stats --}}
        <div class="flex gap-4 shrink-0">
            @foreach([
                ['🎓', $certificates->count(),        'Obtenus'],
                ['📚', $inProgressEnrollments->count(),'En cours'],
                ['⭐', $certificates->count() * 150,   'XP gagnés'],
            ] as [$icon, $val, $label])
            <div class="text-center px-4 py-3 rounded-2xl"
                 style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1)">
                <div class="text-xl mb-1">{{ $icon }}</div>
                <div class="text-white font-black text-xl" style="font-family:'Playfair Display',serif">{{ $val }}</div>
                <div class="text-white/40 text-[10px] uppercase tracking-wide">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── CERTIFICATS OBTENUS ── --}}
@if($certificates->isNotEmpty())
<div class="mb-10">
    <div class="flex items-center gap-3 mb-5 anim d2">
        <h2 class="text-base font-bold text-gray-800" style="font-family:'Playfair Display',serif">
            ✅ Certificats obtenus
        </h2>
        <div class="flex-1 h-px bg-black/6"></div>
        <span class="text-xs text-gray-400">{{ $certificates->count() }} certificat(s)</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($certificates as $certificate)
        @php
            $isNew = $certificate->issued_at->gt(now()->subDays(7));
        @endphp

        <div class="cert-card anim d{{ min($loop->iteration, 6) }}" x-data="{ copied: false }">

            @if($isNew)
            <div class="new-ribbon">Nouveau !</div>
            @endif

            {{-- Aperçu certificat --}}
            <div class="cert-preview">
                <div class="cert-inner">
                    <div class="cert-seal">🎓</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest mb-1"
                         style="color:#b8860b;font-family:'Playfair Display',serif">
                        Certificat de complétion
                    </div>
                    <div class="font-bold text-sm text-gray-700 mb-0.5 line-clamp-1"
                         style="font-family:'Playfair Display',serif;max-width:160px">
                        {{ $certificate->course->title }}
                    </div>
                    <div class="text-[10px] text-gray-400">{{ auth()->user()->full_name }}</div>
                    <div class="text-[9px] font-mono text-gray-300 mt-1.5">
                        {{ $certificate->certificate_number }}
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-5">
                {{-- Cours --}}
                <h3 class="font-bold text-gray-800 text-sm leading-snug mb-1"
                    style="font-family:'Playfair Display',serif">
                    {{ $certificate->course->title }}
                </h3>
                <p class="text-xs text-gray-400 mb-3">
                    Par {{ $certificate->course->teacher->full_name }}
                </p>

                {{-- Infos --}}
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="px-3 py-2 rounded-xl text-center" style="background:rgba(232,184,75,0.06);border:1px solid rgba(232,184,75,0.15)">
                        <div class="text-[10px] text-gray-400 mb-0.5">Délivré le</div>
                        <div class="text-xs font-bold text-gray-700">
                            {{ $certificate->issued_at->translatedFormat('d M Y') }}
                        </div>
                    </div>
                    <div class="px-3 py-2 rounded-xl text-center" style="background:rgba(37,194,110,0.06);border:1px solid rgba(37,194,110,0.15)">
                        <div class="text-[10px] text-gray-400 mb-0.5">Numéro</div>
                        <div class="text-xs font-mono font-bold text-gray-600 truncate">
                            {{ $certificate->certificate_number }}
                        </div>
                    </div>
                </div>

                {{-- Partage --}}
                <div class="flex items-center gap-2 mb-4 pb-4 border-b border-black/5">
                    <span class="text-xs text-gray-400 mr-1">Partager :</span>
                    {{-- LinkedIn --}}
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('certificates.verify', $certificate->certificate_number)) }}"
                       target="_blank" class="share-btn share-linkedin" title="Partager sur LinkedIn">
                        in
                    </a>
                    {{-- Twitter/X --}}
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode('Je viens d\'obtenir mon certificat ' . $certificate->course->title . ' sur MboaAcademy 🎓') }}&url={{ urlencode(route('certificates.verify', $certificate->certificate_number)) }}"
                       target="_blank" class="share-btn share-twitter" title="Partager sur X">
                        𝕏
                    </a>
                    {{-- Copier lien --}}
                    <button @click="
                            navigator.clipboard.writeText('{{ route('certificates.verify', $certificate->certificate_number) }}');
                            copied = true;
                            setTimeout(() => copied = false, 2000)
                        "
                        class="share-btn share-copy" :title="copied ? 'Copié !' : 'Copier le lien'">
                        <span x-text="copied ? '✓' : '🔗'"></span>
                    </button>
                    <span x-show="copied" x-transition
                          class="text-xs font-semibold" style="color:#1a8a47">
                        Copié !
                    </span>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2">
                    <a href="{{ route('student.certificates.show', $certificate) }}"
                       class="btn-gold flex-1 justify-center text-center">
                        👁 Voir
                    </a>
                    <a href="{{ route('student.certificates.download', $certificate) }}"
                       class="btn-outline-dark flex-1 justify-center text-center">
                        ⬇ Télécharger
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── COURS EN COURS (certificats à venir) ── --}}
@if($inProgressEnrollments->isNotEmpty())
<div class="mb-8 anim d4">
    <div class="flex items-center gap-3 mb-5">
        <h2 class="text-base font-bold text-gray-800" style="font-family:'Playfair Display',serif">
            ⏳ Certificats en cours d'obtention
        </h2>
        <div class="flex-1 h-px bg-black/6"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($inProgressEnrollments as $enrollment)
        @php $course = $enrollment->course; @endphp

        <div class="in-progress-card anim d{{ min($loop->iteration, 6) }}">
            {{-- Preview verrouillé --}}
            <div class="cert-preview" style="background:linear-gradient(135deg,#f3f4f6,#e5e7eb)">
                <div class="cert-inner">
                    <div class="w-14 h-14 rounded-full mx-auto mb-3 flex items-center justify-center text-2xl"
                         style="background:rgba(0,0,0,0.06)">🔒</div>
                    <div class="text-xs font-semibold text-gray-400 text-center">
                        Terminez le cours pour<br>obtenir votre certificat
                    </div>
                </div>
            </div>

            <div class="p-5">
                <h3 class="font-bold text-gray-700 text-sm mb-1" style="font-family:'Playfair Display',serif">
                    {{ $course->title }}
                </h3>
                <p class="text-xs text-gray-400 mb-4">Par {{ $course->teacher->full_name }}</p>

                {{-- Progression --}}
                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-500">Progression</span>
                        <span class="font-bold" style="color:#25c26e">{{ $enrollment->progress_percent }}%</span>
                    </div>
                    <div class="prog-bar-cert">
                        <div class="prog-fill-cert" style="width:{{ $enrollment->progress_percent }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1.5">
                        @php
                            $totalLessons     = $course->lessons->count();
                            $completedLessons = round($enrollment->progress_percent / 100 * $totalLessons);
                            $remaining        = $totalLessons - $completedLessons;
                        @endphp
                        Plus que {{ $remaining }} leçon(s) pour obtenir votre certificat
                    </p>
                </div>

                <a href="{{ route('student.courses.learn', $course->slug) }}"
                   class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
                   style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 4px 12px rgba(37,194,110,0.25)">
                    ▶ Continuer le cours
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── ÉTAT VIDE ── --}}
@if($certificates->isEmpty() && $inProgressEnrollments->isEmpty())
<div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-black/5 anim d2">
    <div class="text-6xl mb-4 float">🎓</div>
    <h3 class="text-xl font-bold text-gray-700 mb-2" style="font-family:'Playfair Display',serif">
        Aucun certificat pour l'instant
    </h3>
    <p class="text-sm text-gray-400 mb-6 max-w-sm">
        Terminez un cours pour obtenir votre premier certificat officiel MboaAcademy !
    </p>
    <a href="{{ route('student.courses.index') }}"
       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white"
       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
        🔍 Explorer les cours
    </a>
</div>
@endif

@endsection