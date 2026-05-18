@extends('student.layouts.app')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres')
@section('page-subtitle', 'Personnalisez votre expérience')

@section('content')

<div class="space-y-6">

    {{-- NOTIFICATIONS --}}
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(37,194,110,0.1)">🔔</div>
            <div>
                <h3 class="text-sm font-bold text-gray-800">Notifications</h3>
                <p class="text-xs text-gray-400">Contrôlez ce que vous recevez</p>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf @method('PATCH')

                <label class="flex justify-between">
                    <span>Réponses à mes discussions</span>
                    <input type="checkbox" name="notify_forum" {{ $user->notify_forum ? 'checked' : '' }}>
                </label>

                <label class="flex justify-between">
                    <span>Mentions (@vous)</span>
                    <input type="checkbox" name="notify_mentions" {{ $user->notify_mentions ? 'checked' : '' }}>
                </label>

                <label class="flex justify-between">
                    <span>Nouveaux cours</span>
                    <input type="checkbox" name="notify_courses" {{ $user->notify_courses ? 'checked' : '' }}>
                </label>

                <label class="flex justify-between">
                    <span>Badges</span>
                    <input type="checkbox" name="notify_badges" {{ $user->notify_badges ? 'checked' : '' }}>
                </label>

                <button class="btn-primary mt-4">💾 Sauvegarder</button>
            </form>
        </div>
    </div>

    {{-- EXPERIENCE --}}
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(232,184,75,0.1)">🎨</div>
            <div>
                <h3 class="text-sm font-bold text-gray-800">Expérience</h3>
                <p class="text-xs text-gray-400">Personnalisez votre interface</p>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf @method('PATCH')

                <label class="flex justify-between">
                    <span>Mode sombre</span>
                    <input type="checkbox" name="dark_mode" {{ $user->dark_mode ? 'checked' : '' }}>
                </label>

                <label class="flex justify-between">
                    <span>Réduire les animations</span>
                    <input type="checkbox" name="reduce_animations" {{ $user->reduce_animations ? 'checked' : '' }}>
                </label>

                <button class="btn-secondary mt-4">Appliquer</button>
            </form>
        </div>
    </div>

    {{-- COMPORTEMENT --}}
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(59,130,246,0.1)">⚙️</div>
            <div>
                <h3 class="text-sm font-bold text-gray-800">Comportement</h3>
                <p class="text-xs text-gray-400">Optimisez votre navigation</p>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf @method('PATCH')

                <label class="flex justify-between">
                    <span>Reprendre automatiquement les cours</span>
                    <input type="checkbox" name="auto_resume_course" {{ $user->auto_resume_course ? 'checked' : '' }}>
                </label>

                <label class="flex justify-between">
                    <span>Masquer contenus terminés</span>
                    <input type="checkbox" name="hide_completed" {{ $user->hide_completed ? 'checked' : '' }}>
                </label>

                <button class="btn-secondary mt-4">Mettre à jour</button>
            </form>
        </div>
    </div>

</div>

@endsection