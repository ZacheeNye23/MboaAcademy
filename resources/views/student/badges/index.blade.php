@extends('student.layouts.app')

@section('title', 'Mes Badges')
@section('page-title', 'Badges & Récompenses')
@section('page-subtitle', $earnedBadges->count() . ' / ' . $allBadges->count() . ' badges obtenus')

@push('styles')
<style>
    /* ── Hero ── */
    .badges-hero {
        background: linear-gradient(135deg, #0a1a0f 0%, #0d5c2e 60%, #1a8a47 100%);
        border-radius: 24px; padding: 32px 36px;
        position: relative; overflow: hidden; margin-bottom: 28px;
    }
    .badges-hero::before {
        content: ''; position: absolute; inset: 0;
        background-image:
            radial-gradient(circle at 20% 50%, rgba(37,194,110,0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(232,184,75,0.10) 0%, transparent 40%),
            repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0, rgba(255,255,255,0.02) 1px, transparent 1px, transparent 22px);
    }

    /* ── XP Bar ── */
    .xp-bar { height: 10px; border-radius: 5px; background: rgba(255,255,255,0.1); overflow: hidden; }
    .xp-fill {
        height: 100%;
        background: linear-gradient(90deg, #e8b84b, #f0d070);
        border-radius: 5px;
        transition: width 1.2s cubic-bezier(.25,.46,.45,.94);
        position: relative;
    }
    .xp-fill::after {
        content: ''; position: absolute; right: 0; top: 0; bottom: 0;
        width: 20px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4));
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer { 0%,100%{opacity:0} 50%{opacity:1} }

    /* ── Badge card ── */
    .badge-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 20px;
        padding: 24px 20px;
        text-align: center;
        transition: all .3s;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    .badge-card.earned {
        border-color: rgba(37,194,110,0.2);
    }
    .badge-card.earned:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.1);
        border-color: rgba(37,194,110,0.4);
    }
    .badge-card.locked {
        background: #f9fafb;
        filter: grayscale(.5);
    }
    .badge-card.locked:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .badge-card.new-badge::before {
        content: '✨ Nouveau !';
        position: absolute; top: 10px; right: -18px;
        background: linear-gradient(135deg, #e8b84b, #f0d070);
        color: #0a1a0f; font-size: .62rem; font-weight: 800;
        padding: 3px 24px; transform: rotate(35deg);
        letter-spacing: .5px;
    }

    /* ── Badge icon ── */
    .badge-icon-wrap {
        width: 72px; height: 72px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem;
        margin: 0 auto 14px;
        position: relative;
        transition: all .3s;
    }
    .badge-card.earned .badge-icon-wrap {
        box-shadow: 0 0 0 4px rgba(37,194,110,0.12), 0 0 0 8px rgba(37,194,110,0.06);
    }
    .badge-card.earned:hover .badge-icon-wrap {
        transform: scale(1.1) rotate(5deg);
    }
    .badge-card.locked .badge-icon-wrap {
        background: rgba(0,0,0,0.05) !important;
    }
    .badge-card.locked .badge-emoji {
        filter: grayscale(1); opacity: .4;
    }
    .lock-overlay {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(0,0,0,0.15);
        border-radius: 50%;
        font-size: 1.2rem;
    }

    /* ── Prog mini ── */
    .badge-prog { height: 4px; border-radius: 2px; background: rgba(0,0,0,0.08); overflow: hidden; margin-top: 10px; }
    .badge-prog-fill { height: 100%; border-radius: 2px; background: linear-gradient(90deg, #1a8a47, #25c26e); }

    /* ── Streak calendar ── */
    .streak-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
    .streak-day {
        aspect-ratio: 1; border-radius: 4px;
        display: flex; align-items: center; justify-content: center;
        font-size: .6rem; font-weight: 700;
    }
    .streak-day.active   { background: linear-gradient(135deg, #1a8a47, #25c26e); color: #fff; }
    .streak-day.inactive { background: rgba(0,0,0,0.05); color: #d1d5db; }
    .streak-day.today    { background: linear-gradient(135deg, #e8b84b, #f0d070); color: #0a1a0f; }

    /* ── Leaderboard ── */
    .lb-row {
        display: flex; align-items: center; gap: 14px;
        padding: 12px 16px; border-radius: 14px;
        transition: all .2s;
    }
    .lb-row:hover { background: rgba(37,194,110,0.04); }
    .lb-row.me    { background: rgba(37,194,110,0.06); border: 1px solid rgba(37,194,110,0.15); }
    .lb-rank {
        width: 28px; height: 28px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 800;
        flex-shrink: 0;
    }

    /* ── Tabs ── */
    .tab-btn { padding: 8px 18px; border-radius: 100px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .2s; border: 1.5px solid transparent; text-decoration: none; }
    .tab-btn.on  { background: #1a8a47; color: #fff; }
    .tab-btn.off { background: #fff; color: #6b7280; border-color: rgba(0,0,0,0.1); }
    .tab-btn.off:hover { border-color: #1a8a47; color: #1a8a47; }

    /* ── Animations ── */
    @keyframes fadeUp  { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    @keyframes popIn   { from{opacity:0;transform:scale(.7)} to{opacity:1;transform:scale(1)} }
    @keyframes glow    { 0%,100%{box-shadow:0 0 0 0 rgba(37,194,110,0)} 50%{box-shadow:0 0 20px 6px rgba(37,194,110,0.25)} }

    .anim { animation: fadeUp .45s ease both; }
    .d1{animation-delay:.05s}.d2{animation-delay:.10s}.d3{animation-delay:.15s}
    .d4{animation-delay:.20s}.d5{animation-delay:.25s}.d6{animation-delay:.30s}

    .badge-card.earned .badge-icon-wrap { animation: glow 3s ease-in-out infinite; }

    /* ── Modal badge ── */
    .modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:100;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px); }
    .modal-box { background:#fff;border-radius:24px;padding:32px;max-width:380px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.2); }
    @keyframes popIn { from{opacity:0;transform:scale(.8) translateY(20px)} to{opacity:1;transform:scale(1) translateY(0)} }
    .modal-box { animation: popIn .35s cubic-bezier(.34,1.56,.64,1) both; }

    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')

@php
    $earnedCount   = $earnedBadges->count();
    $totalCount    = $allBadges->count();
    $pct           = $totalCount > 0 ? round(($earnedCount / $totalCount) * 100) : 0;
    $xpTotal       = $earnedCount * 150; // 150 XP par badge
    $level         = max(1, intdiv($earnedCount, 2) + 1);
    $xpForNext     = $level * 300;
    $xpPct         = $xpTotal > 0 ? min(100, round(($xpTotal % 300) / 300 * 100)) : 0;
@endphp

{{-- ── HERO ── --}}
<div class="badges-hero anim d1">
    <div class="relative z-10">
        <div class="flex flex-col lg:flex-row lg:items-center gap-8">

            {{-- Niveau & XP --}}
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-5">
                    {{-- Badge niveau --}}
                    <div class="relative shrink-0">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-4xl"
                             style="background:linear-gradient(135deg,rgba(232,184,75,0.2),rgba(232,184,75,0.1));border:2px solid rgba(232,184,75,0.3)">
                            {{ ['🌱','⭐','🔥','💎','👑','🏆','🌟','🦁','🚀','💫'][$level - 1] ?? '🏆' }}
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-7 h-7 rounded-full flex items-center justify-center text-xs font-black text-dark"
                             style="background:linear-gradient(135deg,#e8b84b,#f0d070)">
                            {{ $level }}
                        </div>
                    </div>
                    <div>
                        <div class="text-white/55 text-xs uppercase tracking-widest mb-1">Niveau {{ $level }}</div>
                        <h2 class="text-white text-2xl font-black" style="font-family:'Playfair Display',serif">
                            {{ ['Novice','Apprenti','Explorateur','Praticien','Expert','Maître','Grand Maître','Légende','Élite','Champion'][$level - 1] ?? 'Champion' }}
                        </h2>
                        <div class="text-white/45 text-xs mt-0.5">{{ $xpTotal }} XP accumulés</div>
                    </div>
                </div>

                {{-- Barre XP --}}
                <div class="mb-2">
                    <div class="flex justify-between text-xs mb-2" style="color:rgba(255,255,255,0.5)">
                        <span>Progression vers niveau {{ $level + 1 }}</span>
                        <span style="color:#e8b84b">{{ $xpTotal % 300 }} / 300 XP</span>
                    </div>
                    <div class="xp-bar">
                        <div class="xp-fill" style="width:{{ $xpPct }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Stats rapides --}}
            <div class="grid grid-cols-3 gap-4">
                @foreach([
                    ['🏆', $earnedCount,          'Badges',    '#e8b84b'],
                    ['📈', $pct . '%',            'Complété',  '#25c26e'],
                    ['🔥', $streak->current_streak ?? 0, 'Jours streak', '#f97316'],
                ] as [$icon, $val, $label, $color])
                <div class="text-center p-4 rounded-2xl" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1)">
                    <div class="text-2xl mb-1">{{ $icon }}</div>
                    <div class="text-white font-black text-xl" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
                    <div class="text-white/45 text-[10px] uppercase tracking-wide mt-0.5">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── COLONNE PRINCIPALE ── --}}
    <div class="lg:col-span-2" x-data="{ selectedBadge: null, filter: '{{ request('filter', 'all') }}' }">

        {{-- Filtres --}}
        <div class="flex gap-2 flex-wrap mb-6 anim d2">
            @foreach(['all' => 'Tous ('.$totalCount.')', 'earned' => '✅ Obtenus ('.$earnedCount.')', 'locked' => '🔒 Verrouillés ('.($totalCount - $earnedCount).')'] as $val => $label)
            <a href="{{ request()->fullUrlWithQuery(['filter' => $val]) }}"
               class="tab-btn {{ request('filter','all') === $val ? 'on' : 'off' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        {{-- Catégories de badges --}}
        @php
            $categories = [
                'first_course'   => ['🎯', 'Premiers pas',    'Débuter son parcours'],
                'first_complete' => ['🎓', 'Diplôme',         'Terminer des cours'],
                'streak'         => ['🔥', 'Régularité',      'Apprendre chaque jour'],
                'quiz_master'    => ['🧠', 'Quiz Master',     'Maîtriser les évaluations'],
                'fast_learner'   => ['📚', 'Boulimie de savoirs','Accumuler les leçons'],
                'completionist'  => ['💻', 'Spécialiste',     'Maîtriser une catégorie'],
                'social'         => ['💬', 'Communauté',      'Participer aux forums'],
                'custom'         => ['⭐', 'Spéciaux',        'Récompenses uniques'],
            ];
            $badgesByType = $allBadges->groupBy('type');
            $filter = request('filter', 'all');
        @endphp

        @foreach($badgesByType as $type => $typeBadges)
        @php
            $catInfo = $categories[$type] ?? ['⭐', ucfirst($type), ''];
            // Filtrer les badges si nécessaire
            $displayBadges = $typeBadges->filter(function($badge) use ($filter, $earnedBadges) {
                if ($filter === 'earned') return $earnedBadges->has($badge->id);
                if ($filter === 'locked') return !$earnedBadges->has($badge->id);
                return true;
            });
        @endphp

        @if($displayBadges->isNotEmpty())
        <div class="mb-8 anim d3">
            {{-- En-tête catégorie --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-base"
                     style="background:rgba(37,194,110,0.1)">{{ $catInfo[0] }}</div>
                <div>
                    <h3 class="text-sm font-bold text-gray-700">{{ $catInfo[1] }}</h3>
                    <p class="text-xs text-gray-400">{{ $catInfo[2] }}</p>
                </div>
                <div class="flex-1 h-px bg-black/6 ml-2"></div>
                <span class="text-xs text-gray-400">
                    {{ $typeBadges->filter(fn($b) => $earnedBadges->has($b->id))->count() }}
                    / {{ $typeBadges->count() }}
                </span>
            </div>

            {{-- Grille badges --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach($displayBadges as $badge)
                @php
                    $earned    = $earnedBadges->has($badge->id);
                    $userBadge = $earnedBadges->get($badge->id);
                    $isNew     = $earned && $userBadge && $userBadge->earned_at->gt(now()->subDays(3));

                    // Calcul progression vers ce badge
                    $progress = match($badge->type) {
                        'streak'       => min(100, round(($streak->current_streak ?? 0) / $badge->required_value * 100)),
                        'fast_learner' => min(100, round($stats['lessons_completed'] / $badge->required_value * 100)),
                        'first_course' => $stats['total_enrolled'] > 0 ? 100 : 0,
                        'first_complete'=> $stats['completed_courses'] > 0 ? 100 : 0,
                        default        => $earned ? 100 : 0,
                    };
                @endphp

                <div class="badge-card {{ $earned ? 'earned' : 'locked' }} {{ $isNew ? 'new-badge' : '' }} anim d{{ min($loop->iteration, 6) }}"
                     @click="selectedBadge = {{ json_encode([
                         'icon'        => $badge->icon,
                         'name'        => $badge->name,
                         'description' => $badge->description,
                         'earned'      => $earned,
                         'earned_at'   => $earned && $userBadge ? $userBadge->earned_at->translatedFormat('d F Y') : null,
                         'progress'    => $progress,
                         'required'    => $badge->required_value,
                         'color'       => $badge->color,
                         'xp'          => 150,
                     ]) }}">

                    {{-- Icône --}}
                    <div class="badge-icon-wrap mx-auto"
                         style="background:{{ $earned ? $badge->color . '20' : 'rgba(0,0,0,0.05)' }};
                                border:2px solid {{ $earned ? $badge->color . '40' : 'transparent' }}">
                        <span class="badge-emoji text-3xl">{{ $badge->icon }}</span>
                        @if(!$earned)
                        <div class="lock-overlay">🔒</div>
                        @endif
                    </div>

                    {{-- Nom --}}
                    <h4 class="text-sm font-bold mb-1 {{ $earned ? 'text-gray-800' : 'text-gray-400' }}"
                        style="font-family:'Playfair Display',serif">
                        {{ $badge->name }}
                    </h4>

                    {{-- Description --}}
                    <p class="text-xs {{ $earned ? 'text-gray-500' : 'text-gray-300' }} leading-relaxed mb-2">
                        {{ Str::limit($badge->description, 55) }}
                    </p>

                    {{-- Date obtention ou progression --}}
                    @if($earned && $userBadge)
                    <div class="text-[10px] font-semibold" style="color:{{ $badge->color }}">
                        ✓ {{ $userBadge->earned_at->diffForHumans() }}
                    </div>
                    @elseif(!$earned && $progress > 0)
                    <div class="badge-prog">
                        <div class="badge-prog-fill" style="width:{{ $progress }}%;background:{{ $badge->color }}"></div>
                    </div>
                    <div class="text-[10px] text-gray-400 mt-1">{{ $progress }}% complété</div>
                    @else
                    <div class="text-[10px] text-gray-300">Non débloqué</div>
                    @endif

                    {{-- XP --}}
                    @if($earned)
                    <div class="mt-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"
                         style="background:rgba(232,184,75,0.1);color:#b8860b">
                        +150 XP
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

        {{-- Modal badge ── --}}
        <div x-show="selectedBadge !== null" x-cloak class="modal-overlay"
             @click.self="selectedBadge = null" x-transition>
            <div class="modal-box" @click.stop>
                <button @click="selectedBadge = null"
                        class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 transition-colors">✕</button>

                {{-- Icône --}}
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4"
                     :style="'background:' + (selectedBadge?.color || '#25c26e') + '20;border:2px solid ' + (selectedBadge?.color || '#25c26e') + '40'">
                    <span x-text="selectedBadge?.icon"></span>
                </div>

                <h3 class="text-xl font-black text-gray-800 mb-2" style="font-family:'Playfair Display',serif"
                    x-text="selectedBadge?.name"></h3>
                <p class="text-sm text-gray-500 mb-5 leading-relaxed" x-text="selectedBadge?.description"></p>

                {{-- Statut --}}
                <div x-show="selectedBadge?.earned"
                     class="px-4 py-3 rounded-xl mb-4"
                     style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2)">
                    <div class="text-sm font-bold" style="color:#1a8a47">🏆 Badge obtenu !</div>
                    <div class="text-xs text-gray-400 mt-0.5">Le <span x-text="selectedBadge?.earned_at"></span></div>
                    <div class="text-xs font-semibold mt-1" style="color:#b8860b">+150 XP gagnés</div>
                </div>

                <div x-show="!selectedBadge?.earned">
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="text-gray-500">Progression</span>
                        <span class="font-bold text-gray-700" x-text="selectedBadge?.progress + '%'"></span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden mb-3">
                        <div class="h-full rounded-full transition-all duration-700"
                             style="background:linear-gradient(90deg,#1a8a47,#25c26e)"
                             :style="'width:' + selectedBadge?.progress + '%'"></div>
                    </div>
                    <p class="text-xs text-gray-400">Continuez à apprendre pour débloquer ce badge !</p>
                </div>

                <button @click="selectedBadge = null"
                        class="mt-4 w-full py-2.5 rounded-xl text-sm font-semibold text-white"
                        style="background:linear-gradient(135deg,#1a8a47,#25c26e)">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    {{-- ── SIDEBAR DROITE ── --}}
    <div class="space-y-5">

        {{-- Streak Calendar ── --}}
        <div class="bg-white border border-black/5 rounded-2xl p-5 anim d2">
            <h3 class="text-sm font-bold text-gray-700 mb-1" style="font-family:'Playfair Display',serif">
                🔥 Streak d'apprentissage
            </h3>
            <div class="flex items-baseline gap-2 mb-4">
                <span class="text-3xl font-black" style="font-family:'Playfair Display',serif;color:#f97316">
                    {{ $streak->current_streak ?? 0 }}
                </span>
                <span class="text-sm text-gray-400">jours consécutifs</span>
            </div>

            {{-- Calendrier 28 derniers jours --}}
            @php
                $days = collect(range(27, 0))->map(fn($d) => now()->subDays($d)->toDateString());
                $lastActivity = $streak->last_activity_date?->toDateString();
            @endphp
            <div class="streak-grid mb-3">
                @foreach($days as $day)
                @php
                    $isToday  = $day === now()->toDateString();
                    $isActive = $streak->current_streak > 0
                        && $day >= now()->subDays($streak->current_streak - 1)->toDateString()
                        && $day <= now()->toDateString();
                @endphp
                <div class="streak-day {{ $isToday ? 'today' : ($isActive ? 'active' : 'inactive') }}"
                     title="{{ \Carbon\Carbon::parse($day)->translatedFormat('d M') }}">
                    {{ \Carbon\Carbon::parse($day)->format('d') }}
                </div>
                @endforeach
            </div>

            <div class="flex justify-between text-xs text-gray-400 mt-2">
                <span>Record : <strong class="text-gray-600">{{ $streak->longest_streak ?? 0 }}j</strong></span>
                @if(($streak->current_streak ?? 0) >= 7)
                <span class="font-semibold" style="color:#f97316">🔥 En feu !</span>
                @endif
            </div>
        </div>

        {{-- Prochain badge ── --}}
        @php
            $nextBadge = $allBadges->first(fn($b) => !$earnedBadges->has($b->id));
            $nextProgress = $nextBadge ? match($nextBadge->type) {
                'streak'       => min(100, round(($streak->current_streak ?? 0) / $nextBadge->required_value * 100)),
                'fast_learner' => min(100, round($stats['lessons_completed'] / $nextBadge->required_value * 100)),
                default        => 0,
            } : 0;
        @endphp
        @if($nextBadge)
        <div class="bg-white border border-black/5 rounded-2xl p-5 anim d3">
            <h3 class="text-sm font-bold text-gray-700 mb-4" style="font-family:'Playfair Display',serif">
                🎯 Prochain badge
            </h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl shrink-0"
                     style="background:{{ $nextBadge->color }}15;border:2px solid {{ $nextBadge->color }}30">
                    {{ $nextBadge->icon }}
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-800">{{ $nextBadge->name }}</div>
                    <div class="text-xs text-gray-400 leading-relaxed">{{ $nextBadge->description }}</div>
                </div>
            </div>
            <div class="flex justify-between text-xs mb-1.5">
                <span class="text-gray-400">Progression</span>
                <span class="font-bold" style="color:{{ $nextBadge->color }}">{{ $nextProgress }}%</span>
            </div>
            <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700"
                     style="width:{{ $nextProgress }}%;background:{{ $nextBadge->color }}"></div>
            </div>
            <div class="mt-3 text-xs text-gray-400 flex items-center gap-1.5">
                <span>💡</span>
                @if($nextBadge->type === 'streak')
                <span>Connectez-vous {{ $nextBadge->required_value - ($streak->current_streak ?? 0) }} jours de plus</span>
                @elseif($nextBadge->type === 'fast_learner')
                <span>Complétez {{ $nextBadge->required_value - $stats['lessons_completed'] }} leçons de plus</span>
                @else
                <span>Continuez à apprendre !</span>
                @endif
            </div>
        </div>
        @endif

        {{-- Mini leaderboard ── --}}
        <div class="bg-white border border-black/5 rounded-2xl overflow-hidden anim d4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-black/5">
                <h3 class="text-sm font-bold text-gray-700" style="font-family:'Playfair Display',serif">
                    🏅 Classement
                </h3>
                <a href="{{ route('student.badges.leaderboard') }}"
                   class="text-xs font-semibold hover:underline" style="color:#1a8a47">
                    Voir tout →
                </a>
            </div>
            <div class="p-3 space-y-1">
                @foreach($leaderboard as $entry)
                @php
                    $isMe    = $entry['user_id'] === auth()->id();
                    $medals  = ['🥇','🥈','🥉'];
                    $rankColors = ['rgba(232,184,75,0.15)','rgba(156,163,175,0.15)','rgba(205,127,50,0.15)'];
                    $avatarColors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46'];
                @endphp
                <div class="lb-row {{ $isMe ? 'me' : '' }}">
                    <div class="lb-rank" style="background:{{ $rankColors[$loop->index] ?? 'rgba(0,0,0,0.05)' }}">
                        {{ $medals[$loop->index] ?? $loop->iteration }}
                    </div>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                         style="background:{{ $avatarColors[$loop->index % count($avatarColors)] }}">
                        {{ $entry['initials'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold {{ $isMe ? 'text-green-700' : 'text-gray-700' }} truncate">
                            {{ $entry['name'] }} {{ $isMe ? '(Moi)' : '' }}
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-xs font-bold" style="color:#e8b84b">{{ $entry['badges'] }}</div>
                        <div class="text-[10px] text-gray-400">badges</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection