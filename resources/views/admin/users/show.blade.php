@extends('admin.layouts.app')

@section('title', $user->full_name)
@section('page-title', 'Profil utilisateur')
@section('page-subtitle', $user->email)

@section('topbar-actions')
<div class="flex gap-2">
    <a href="{{ route('admin.users.edit', $user) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5"
       style="background:rgba(232,184,75,0.12);color:#e8b84b;border:1px solid rgba(232,184,75,0.2)">
        ✏️ Modifier
    </a>
    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
        @csrf @method('PATCH')
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5"
                style="background:{{ $user->is_active ? 'rgba(239,68,68,0.1)' : 'rgba(37,194,110,0.1)' }};
                       color:{{ $user->is_active ? '#f87171' : '#25c26e' }};
                       border:1px solid {{ $user->is_active ? 'rgba(239,68,68,0.2)' : 'rgba(37,194,110,0.2)' }}">
            {{ $user->is_active ? '🚫 Désactiver' : '✅ Activer' }}
        </button>
    </form>
</div>
@endsection

@push('styles')
<style>
    .info-row { display:flex;align-items:flex-start;gap:14px;padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.05); }
    .info-row:last-child { border-bottom:none; }
    .info-label { font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.3);width:120px;flex-shrink:0;padding-top:2px; }
    .info-value { font-size:.875rem;color:rgba(255,255,255,0.8);flex:1; }

    .stat-box { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:16px;padding:18px;text-align:center; }

    .course-row { display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid rgba(255,255,255,0.05); }
    .course-row:last-child { border-bottom:none; }

    .prog-bar { height:4px;border-radius:2px;background:rgba(255,255,255,0.07);overflow:hidden;margin-top:4px; }
    .prog-fill { height:100%;border-radius:2px; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}.d3{animation-delay:.12s}.d4{animation-delay:.16s}
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-xs mb-6 anim d1" style="color:rgba(255,255,255,0.35)">
    <a href="{{ route('admin.users.index') }}" class="hover:text-white transition-colors">← Utilisateurs</a>
    <span>/</span>
    <span class="text-white">{{ $user->full_name }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── COLONNE GAUCHE : Profil ── --}}
    <div class="space-y-5">

        {{-- Carte profil --}}
        <div class="glass p-6 text-center anim d1">
            @php $colors = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46','#92400e']; @endphp
            <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center font-bold text-2xl text-white mb-4"
                 style="background:linear-gradient(135deg,{{ $colors[$user->id % count($colors)] }},{{ $colors[($user->id+1) % count($colors)] }})">
                {{ $user->initials }}
            </div>
            <h2 class="text-lg font-bold text-white mb-1" style="font-family:'Playfair Display',serif">
                {{ $user->full_name }}
            </h2>
            <p class="text-sm mb-3" style="color:rgba(255,255,255,0.4)">{{ $user->email }}</p>

            {{-- Rôle --}}
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold mb-4"
                  style="background:{{ ['student'=>'rgba(37,194,110,0.12)','teacher'=>'rgba(232,184,75,0.12)','admin'=>'rgba(167,139,250,0.12)'][$user->role] }};
                         color:{{ ['student'=>'#25c26e','teacher'=>'#e8b84b','admin'=>'#a78bfa'][$user->role] }};
                         border:1px solid {{ ['student'=>'rgba(37,194,110,0.2)','teacher'=>'rgba(232,184,75,0.2)','admin'=>'rgba(167,139,250,0.2)'][$user->role] }}">
                {{ ['student'=>'🎓 Apprenant','teacher'=>'📖 Formateur','admin'=>'🛡️ Admin'][$user->role] }}
            </span>

            {{-- Statut --}}
            <div class="flex items-center justify-center gap-2">
                <div class="w-2 h-2 rounded-full" style="background:{{ $user->is_active ? '#25c26e' : '#f87171' }}"></div>
                <span class="text-xs font-semibold" style="color:{{ $user->is_active ? '#25c26e' : '#f87171' }}">
                    {{ $user->is_active ? 'Compte actif' : 'Compte désactivé' }}
                </span>
            </div>
        </div>

        {{-- Infos détaillées --}}
        <div class="glass p-5 anim d2">
            <h3 class="text-sm font-bold text-white mb-4" style="font-family:'Playfair Display',serif">
                ℹ Informations
            </h3>
            <div>
                @foreach([
                    ['Téléphone',    $user->phone ?? '—'],
                    ['Pays',         $user->country ?? '—'],
                    ['Inscrit le',   $user->created_at->translatedFormat('d F Y')],
                    ['Dernière conn.', $user->updated_at->translatedFormat('d F Y')],
                    ['Email vérifié', $user->email_verified_at ? '✅ Oui' : '❌ Non'],
                ] as [$label, $value])
                <div class="info-row">
                    <span class="info-label">{{ $label }}</span>
                    <span class="info-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bio --}}
        @if($user->bio)
        <div class="glass p-5 anim d3">
            <h3 class="text-sm font-bold text-white mb-3" style="font-family:'Playfair Display',serif">Bio</h3>
            <p class="text-sm leading-relaxed" style="color:rgba(255,255,255,0.55)">{{ $user->bio }}</p>
        </div>
        @endif

        {{-- Actions danger --}}
        <div class="glass p-5 anim d4" style="border-color:rgba(239,68,68,0.15)">
            <h3 class="text-sm font-bold mb-4" style="color:#f87171">Zone dangereuse</h3>
            <div class="space-y-2">
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-left flex items-center gap-2"
                            style="background:rgba(239,68,68,0.07);color:#f87171;border:1px solid rgba(239,68,68,0.12)">
                        {{ $user->is_active ? '🚫 Désactiver le compte' : '✅ Réactiver le compte' }}
                    </button>
                </form>
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Supprimer définitivement {{ $user->full_name }} ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-left flex items-center gap-2"
                            style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2)">
                        🗑 Supprimer le compte
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ── COLONNE DROITE : Activité ── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 anim d2">
            @if($user->isStudent())
            @foreach([
                ['📚', $activityStats['courses_enrolled'],   'Cours inscrits',     '#25c26e'],
                ['✅', $activityStats['courses_completed'],  'Cours terminés',     '#3b82f6'],
                ['📝', $activityStats['quiz_attempts'],      'Quiz passés',        '#a78bfa'],
                ['🏆', $activityStats['badges_count'],       'Badges',             '#e8b84b'],
            ] as [$icon, $val, $label, $color])
            <div class="stat-box">
                <div class="text-xl mb-1">{{ $icon }}</div>
                <div class="text-xl font-bold" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
                <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
            </div>
            @endforeach
            @elseif($user->isTeacher())
            @foreach([
                ['📚', $activityStats['courses_created'],    'Cours créés',        '#e8b84b'],
                ['✅', $activityStats['courses_published'],  'Publiés',            '#25c26e'],
                ['👥', $activityStats['total_students'],     'Apprenants',         '#3b82f6'],
                ['💰', number_format($activityStats['total_revenue']/1000).'K', 'XAF revenus', '#f97316'],
            ] as [$icon, $val, $label, $color])
            <div class="stat-box">
                <div class="text-xl mb-1">{{ $icon }}</div>
                <div class="text-xl font-bold" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ $val }}</div>
                <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- Cours (apprenant) ou Cours créés (formateur) --}}
        @if($user->isStudent() && $enrollments->count() > 0)
        <div class="glass overflow-hidden anim d3">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
                <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                    📚 Cours inscrits
                </h3>
                <span class="text-xs" style="color:rgba(255,255,255,0.3)">{{ $enrollments->count() }} cours</span>
            </div>
            <div class="divide-y divide-white/5 px-5">
                @foreach($enrollments as $enrollment)
                <div class="course-row">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xl shrink-0"
                         style="background:rgba(37,194,110,0.08)">📚</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ $enrollment->course->title }}</div>
                        <div class="prog-bar">
                            <div class="prog-fill" style="width:{{ $enrollment->progress_percent }}%;background:linear-gradient(90deg,#1a8a47,#25c26e)"></div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-xs font-bold" style="color:#25c26e">{{ $enrollment->progress_percent }}%</div>
                        @if($enrollment->completed_at)
                        <div class="text-[10px]" style="color:#e8b84b">✓ Terminé</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($user->isTeacher() && $courses->count() > 0)
        <div class="glass overflow-hidden anim d3">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
                <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                    📚 Cours créés
                </h3>
            </div>
            <div class="divide-y divide-white/5 px-5">
                @foreach($courses as $course)
                <div class="course-row">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-sm font-medium text-white truncate">{{ $course->title }}</span>
                            <span class="pill pill-{{ ['published'=>'green','pending'=>'gold','draft'=>'gray'][$course->status] ?? 'gray' }} shrink-0">
                                {{ $course->status_label }}
                            </span>
                        </div>
                        <div class="text-xs" style="color:rgba(255,255,255,0.35)">
                            {{ $course->enrollments_count }} inscrits · {{ $course->total_lessons }} leçons
                        </div>
                    </div>
                    <a href="{{ route('admin.courses.index', ['search' => $course->title]) }}"
                       class="text-xs px-3 py-1.5 rounded-lg font-medium" style="background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.5)">
                        Voir
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Activité récente --}}
        <div class="glass overflow-hidden anim d4">
            <div class="px-5 py-4 border-b border-white/5">
                <h3 class="text-sm font-bold text-white" style="font-family:'Playfair Display',serif">
                    🕐 Activité récente
                </h3>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($recentActivity as $activity)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0"
                         style="background:{{ $activity['color'] }}18">{{ $activity['icon'] }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-medium text-white">{{ $activity['action'] }}</div>
                        <div class="text-xs truncate" style="color:rgba(255,255,255,0.35)">{{ $activity['detail'] }}</div>
                    </div>
                    <span class="text-[10px] shrink-0" style="color:rgba(255,255,255,0.2)">
                        {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                    </span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-xs" style="color:rgba(255,255,255,0.3)">
                    Aucune activité récente.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection