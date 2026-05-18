@extends('student.layouts.app')

@section('title', 'Mes cours')
@section('page-title', 'Mes cours')
@section('page-subtitle', $enrollments->total() . ' cours inscrits')

@section('topbar-actions')
<a href="{{ route('student.courses.index') }}"
   class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all hover:-translate-y-0.5"
   style="background:linear-gradient(135deg,#1a8a47,#25c26e);color:#fff">
    🔍 Explorer les cours
</a>
@endsection

@push('styles')
<style>
    .enroll-card { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:20px; overflow:hidden; transition:all .25s; display:flex; flex-direction:column; }
    .enroll-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(0,0,0,0.09); border-color:rgba(37,194,110,0.18); }
    .tab-btn { padding:8px 18px; border-radius:100px; font-size:.8rem; font-weight:600; cursor:pointer; transition:all .2s; border:none; }
    .tab-btn.active { background:#1a8a47; color:#fff; }
    .tab-btn:not(.active) { background:rgba(0,0,0,0.05); color:#6b7280; }
    .tab-btn:not(.active):hover { background:rgba(37,194,110,0.1); color:#1a8a47; }
    .thumb-sm { height:130px; display:flex; align-items:center; justify-content:center; font-size:2.5rem; position:relative; }
    .circular-prog { transform: rotate(-90deg); }
</style>
@endpush

@section('content')

{{-- ── ONGLETS ── --}}
<div class="flex gap-2 mb-6 anim d1" x-data="{ tab: '{{ request('status', 'all') }}' }">
    @foreach(['all'=>'Tous','ongoing'=>'En cours','completed'=>'Terminés'] as $val => $label)
    <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
       class="tab-btn {{ request('status', 'all') === $val ? 'active' : '' }}">
        {{ $label }}
        @if($val === 'all') ({{ $enrollments->total() }}) @endif
    </a>
    @endforeach
</div>

@if($enrollments->isEmpty())
{{-- État vide --}}
<div class="flex flex-col items-center justify-center py-24 text-center anim d2">
    <div class="text-6xl mb-4">📚</div>
    <h3 class="text-lg font-bold text-gray-700 mb-2" style="font-family:'Playfair Display',serif">
        Aucun cours pour l'instant
    </h3>
    <p class="text-gray-400 text-sm mb-6">Explorez notre catalogue et inscrivez-vous à votre premier cours.</p>
    <a href="{{ route('student.courses.index') }}"
       class="px-6 py-3 rounded-xl text-sm font-semibold text-white"
       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
        🔍 Explorer les cours
    </a>
</div>
@else

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
    @php
        $gradients = [
            ['#0d5c2e','#1a8a47','💻'],
            ['#7a3b1e','#c4682d','📊'],
            ['#1a2a6c','#4a4aad','🎨'],
            ['#065f46','#10b981','🤖'],
            ['#92400e','#f59e0b','📱'],
            ['#4c1d95','#7c3aed','🔒'],
        ];
    @endphp

    @foreach($enrollments as $enrollment)
    @php
        $course = $enrollment->course;
        $pct    = $enrollment->progress_percent;
        $done   = $enrollment->isCompleted();
        $g      = $gradients[$loop->index % count($gradients)];
        $circ   = 2 * M_PI * 20; // circonférence du cercle r=20
        $offset = $circ * (1 - $pct / 100);
    @endphp

    <div class="enroll-card anim d{{ min($loop->iteration, 6) }}">

        {{-- Thumbnail --}}
        <div class="thumb-sm relative" style="background:linear-gradient(135deg,{{ $g[0] }},{{ $g[1] }})">
            @if($course->thumbnail)
                <img src="{{ $course->thumbnail_url }}" alt="" class="absolute inset-0 w-full h-full object-cover">
            @else
                <span>{{ $g[2] }}</span>
            @endif

            {{-- Anneau de progression --}}
            <div class="absolute bottom-3 right-3">
                <svg width="52" height="52" viewBox="0 0 52 52">
                    <circle cx="26" cy="26" r="20" fill="rgba(0,0,0,0.35)" stroke="rgba(255,255,255,0.15)" stroke-width="5"/>
                    <circle cx="26" cy="26" r="20" fill="none"
                            stroke="{{ $done ? '#e8b84b' : '#25c26e' }}" stroke-width="5"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $circ }}"
                            stroke-dashoffset="{{ $offset }}"
                            class="circular-prog"/>
                    <text x="26" y="30" text-anchor="middle"
                          fill="white" font-size="10" font-weight="700" font-family="Outfit,sans-serif">
                        {{ $pct }}%
                    </text>
                </svg>
            </div>

            {{-- Badge terminé --}}
            @if($done)
            <div class="absolute top-3 left-3">
                <span class="badge-pill" style="background:rgba(232,184,75,0.9);color:#333;font-size:.65rem;">🏆 Terminé</span>
            </div>
            @endif
        </div>

        {{-- Body --}}
        <div class="p-5 flex flex-col flex-1">
            {{-- Catégorie --}}
            @if($course->category)
            <span class="badge-pill badge-blue mb-2 self-start">{{ $course->category }}</span>
            @endif

            {{-- Titre --}}
            <h3 class="font-bold text-gray-800 text-sm leading-snug mb-2 flex-1"
                style="font-family:'Playfair Display',serif">{{ $course->title }}</h3>

            {{-- Formateur --}}
            <p class="text-xs text-gray-400 mb-3">Par {{ $course->teacher->full_name }}</p>

            {{-- Barre de progression --}}
            <div class="mb-4">
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-gray-400">Progression</span>
                    <span class="font-semibold" style="color:{{ $done ? '#e8b84b' : '#25c26e' }}">{{ $pct }}%</span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $done ? 'linear-gradient(90deg,#e8b84b,#f0d070)' : 'linear-gradient(90deg,#1a8a47,#25c26e)' }}"></div>
                </div>
                <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                    <span>Inscrit {{ $enrollment->enrolled_at->diffForHumans() }}</span>
                    @if($done && $enrollment->completed_at)
                    <span>Terminé {{ $enrollment->completed_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>

            {{-- CTA --}}
            <div class="flex gap-2 mt-auto">
                @if($done)
                    <a href="{{ route('student.certificates.index') }}"
                       class="flex-1 py-2.5 rounded-xl text-xs font-semibold text-center transition-all hover:scale-105"
                       style="background:rgba(232,184,75,0.1);color:#b8860b;border:1px solid rgba(232,184,75,0.25)">
                        🎓 Mon certificat
                    </a>
                @else
                    <a href="{{ route('student.courses.learn', $course->slug) }}"
                       class="flex-1 py-2.5 rounded-xl text-xs font-semibold text-white text-center transition-all hover:-translate-y-0.5"
                       style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 4px 12px rgba(37,194,110,0.25)">
                        ▶ Continuer
                    </a>
                @endif
                <a href="{{ route('student.courses.show', $course->slug) }}"
                   class="py-2.5 px-3 rounded-xl text-xs font-medium transition-colors hover:bg-gray-100"
                   style="background:rgba(0,0,0,0.04);color:#6b7280" title="Détails du cours">
                    ℹ
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
{{ $enrollments->withQueryString()->links() }}

@endif

@endsection