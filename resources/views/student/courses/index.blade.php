@extends('student.layouts.app')

@section('title', 'Explorer les cours')
@section('page-title', 'Explorer les cours')
@section('page-subtitle', $courses->total() . ' cours disponibles')

@section('topbar-actions')
<a href="{{ route('student.courses.mine') }}"
   class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all hover:-translate-y-0.5"
   style="background:rgba(37,194,110,0.1);color:#1a8a47;border:1px solid rgba(37,194,110,0.2)">
    📚 Mes cours
</a>
@endsection

@push('styles')
<style>
    .filter-btn { padding:6px 14px; border-radius:100px; font-size:.78rem; font-weight:600; cursor:pointer; transition:all .2s; border:1.5px solid transparent; white-space:nowrap; }
    .filter-btn.on  { background:var(--green-mid);color:#fff;border-color:var(--green-mid); }
    .filter-btn.off { background:#fff;color:#5a7060;border-color:rgba(0,0,0,0.1); }
    .filter-btn.off:hover { border-color:var(--green-mid);color:var(--green-mid); }

    .course-card { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:20px; overflow:hidden; transition:all .25s; }
    .course-card:hover { transform:translateY(-4px); box-shadow:0 16px 40px rgba(0,0,0,0.10); border-color:rgba(37,194,110,0.2); }
    .course-card:hover .card-cta { opacity:1; transform:translateY(0); }
    .card-cta { opacity:0; transform:translateY(6px); transition:all .2s; }

    .search-input {
        background:#fff; border:1.5px solid rgba(0,0,0,0.1); border-radius:14px;
        padding:10px 16px 10px 42px; font-size:.875rem; font-family:'Outfit',sans-serif;
        outline:none; transition:all .2s; width:100%;
    }
    .search-input:focus { border-color:#25c26e; box-shadow:0 0 0 3px rgba(37,194,110,0.1); }

    .level-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:6px; font-size:.65rem; font-weight:700; letter-spacing:.3px; text-transform:uppercase; }
    .level-beginner     { background:rgba(37,194,110,0.1); color:#1a8a47; }
    .level-intermediate { background:rgba(232,184,75,0.12); color:#b8860b; }
    .level-advanced     { background:rgba(239,68,68,0.1); color:#dc2626; }

    .thumb-bg { height:160px; display:flex; align-items:center; justify-content:center; font-size:3rem; position:relative; }
    .thumb-overlay { position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(0,0,0,0.3)); }

    .star-rating { color:#e8b84b; font-size:.75rem; }
    .star-empty  { color:#e5e7eb; }
</style>
@endpush

@section('content')

{{-- ── BARRE DE RECHERCHE + FILTRES ── --}}
<div class="mb-6 anim d1">
    <form method="GET" action="{{ route('student.courses.index') }}" x-data="{ q: '{{ request('search') }}' }">

        {{-- Recherche --}}
        <div class="relative mb-4">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
            <input type="text" name="search" x-model="q"
                   class="search-input" placeholder="Rechercher un cours, un formateur..."
                   value="{{ request('search') }}">
            @if(request('search'))
            <a href="{{ route('student.courses.index') }}"
               class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 hover:text-gray-600 px-2 py-1 rounded-lg bg-gray-100">✕</a>
            @endif
        </div>

        {{-- Filtres --}}
        <div class="flex flex-wrap gap-2 items-center">
            {{-- Catégorie --}}
            <div class="flex gap-1.5 flex-wrap">
                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}"
                   class="filter-btn {{ !request('category') ? 'on' : 'off' }}">Tous</a>
                @foreach($categories as $cat)
                <a href="{{ request()->fullUrlWithQuery(['category' => $cat]) }}"
                   class="filter-btn {{ request('category') === $cat ? 'on' : 'off' }}">{{ $cat }}</a>
                @endforeach
            </div>

            <div class="h-5 w-px bg-black/10 mx-1 hidden sm:block"></div>

            {{-- Niveau --}}
            <div class="flex gap-1.5">
                @foreach(['beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'] as $val => $label)
                <a href="{{ request()->fullUrlWithQuery(['level' => request('level') === $val ? null : $val]) }}"
                   class="filter-btn {{ request('level') === $val ? 'on' : 'off' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="h-5 w-px bg-black/10 mx-1 hidden sm:block"></div>

            {{-- Prix --}}
            <div class="flex gap-1.5">
                <a href="{{ request()->fullUrlWithQuery(['free' => request('free') ? null : '1']) }}"
                   class="filter-btn {{ request('free') ? 'on' : 'off' }}">🆓 Gratuit</a>
            </div>

            {{-- Submit caché (pour la recherche) --}}
            <button type="submit" class="hidden"></button>
        </div>
    </form>
</div>

{{-- ── RÉSULTATS ── --}}
@if($courses->isEmpty())
<div class="flex flex-col items-center justify-center py-24 text-center anim d2">
    <div class="text-6xl mb-4">🔍</div>
    <h3 class="text-lg font-bold text-gray-700 mb-2" style="font-family:'Playfair Display',serif">Aucun cours trouvé</h3>
    <p class="text-gray-400 text-sm mb-6">Essayez de modifier vos filtres ou votre recherche.</p>
    <a href="{{ route('student.courses.index') }}"
       class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">Voir tous les cours</a>
</div>
@else

{{-- Info résultats --}}
<div class="flex items-center justify-between mb-5 anim d2">
    <p class="text-sm text-gray-500">
        <span class="font-semibold text-gray-700">{{ $courses->total() }}</span> cours trouvé(s)
        @if(request('search')) pour "<span class="font-semibold text-gray-700">{{ request('search') }}</span>"@endif
    </p>
    <div class="flex items-center gap-2">
        <span class="text-xs text-gray-400">Trier par</span>
        <select onchange="window.location=this.value"
                class="text-xs border border-black/10 rounded-lg px-2 py-1.5 bg-white outline-none cursor-pointer">
            <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}"   {{ request('sort','latest')==='latest'   ? 'selected' : '' }}>Plus récents</option>
            <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}"  {{ request('sort')==='popular'  ? 'selected' : '' }}>Populaires</option>
            <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}"   {{ request('sort')==='rating'   ? 'selected' : '' }}>Mieux notés</option>
            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort')==='price_asc' ? 'selected' : '' }}>Prix croissant</option>
        </select>
    </div>
</div>

{{-- Grille de cours --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
    @php
        $thumbColors = [
            ['from-green-deep to-green-mid',   '💻'],
            ['from-[#7a3b1e] to-[#c4682d]',    '📊'],
            ['from-[#1a2a6c] to-[#4a4aad]',    '🎨'],
            ['from-[#065f46] to-[#10b981]',    '🤖'],
            ['from-[#92400e] to-[#f59e0b]',    '📱'],
            ['from-[#4c1d95] to-[#7c3aed]',    '🔒'],
        ];
    @endphp

    @foreach($courses as $course)
    @php
        $tc       = $thumbColors[$loop->index % count($thumbColors)];
        $enrolled = $enrolledIds->contains($course->id);
        $rating   = $course->average_rating;
    @endphp

    <div class="course-card anim d{{ min($loop->iteration, 6) }}">

        {{-- Thumbnail --}}
        <div class="thumb-bg bg-gradient-to-br {{ $tc[0] }}">
            @if($course->thumbnail)
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="absolute inset-0 w-full h-full object-cover">
            @else
                <span class="relative z-10">{{ $tc[1] }}</span>
            @endif
            <div class="thumb-overlay"></div>

            {{-- Badges sur la thumbnail --}}
            <div class="absolute top-3 left-3 flex gap-1.5 z-10">
                @if($course->is_free)
                <span class="badge-pill badge-green">🆓 Gratuit</span>
                @endif
                <span class="level-badge level-{{ $course->level }}">
                    {{ ['beginner'=>'Débutant','intermediate'=>'Intermédiaire','advanced'=>'Avancé'][$course->level] }}
                </span>
            </div>

            @if($enrolled)
            <div class="absolute top-3 right-3 z-10">
                <span class="badge-pill" style="background:rgba(37,194,110,0.9);color:#fff;">✓ Inscrit</span>
            </div>
            @endif
        </div>

        {{-- Body --}}
        <div class="p-5">

            {{-- Catégorie --}}
            @if($course->category)
            <span class="badge-pill badge-blue mb-2">{{ $course->category }}</span>
            @endif

            {{-- Titre --}}
            <h3 class="font-bold text-gray-800 text-sm leading-snug mb-2 line-clamp-2"
                style="font-family:'Playfair Display',serif">
                {{ $course->title }}
            </h3>

            {{-- Formateur --}}
            <div class="flex items-center gap-2 mb-3">
                <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold text-white"
                     style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                    {{ $course->teacher->initials }}
                </div>
                <span class="text-xs text-gray-400">{{ $course->teacher->full_name }}</span>
            </div>

            {{-- Méta --}}
            <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
                <span>📚 {{ $course->total_lessons }} leçons</span>
                <span>⏱ {{ $course->duration_formatted }}</span>
                <span>👥 {{ $course->enrollments_count }}</span>
            </div>

            {{-- Rating --}}
            @if($rating > 0)
            <div class="flex items-center gap-1.5 mb-4">
                <div class="star-rating flex">
                    @for($i=1; $i<=5; $i++)
                    <span class="{{ $i <= round($rating) ? 'star-rating' : 'star-empty' }}">★</span>
                    @endfor
                </div>
                <span class="text-xs font-semibold text-gray-600">{{ $rating }}</span>
                <span class="text-xs text-gray-400">({{ $course->reviews->count() }} avis)</span>
            </div>
            @endif

            {{-- Footer --}}
            <div class="flex items-center justify-between pt-3 border-t border-black/5">
                <div>
                    @if($course->is_free)
                        <span class="text-base font-bold" style="color:#1a8a47">Gratuit</span>
                    @else
                        <span class="text-base font-bold text-gray-800">{{ number_format($course->price, 0, ',', ' ') }}</span>
                        <span class="text-xs text-gray-400 ml-0.5">XAF</span>
                    @endif
                </div>

                @if($enrolled)
                    <a href="{{ route('student.courses.learn', $course->slug) }}"
                       class="card-cta px-4 py-2 rounded-xl text-xs font-semibold text-white transition-all hover:scale-105"
                       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                        ▶ Continuer
                    </a>
                @else
                    <a href="{{ route('student.courses.show', $course->slug) }}"
                       class="card-cta px-4 py-2 rounded-xl text-xs font-semibold text-white transition-all hover:scale-105"
                       style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                        Voir le cours →
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div class="anim d6">
    {{ $courses->withQueryString()->links() }}
</div>

@endif

@endsection