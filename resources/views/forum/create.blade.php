@extends('student.layouts.app')

@section('title', 'Nouvelle discussion')
@section('page-title', 'Nouvelle discussion')
@section('page-subtitle', $course->title)

@push('styles')
<style>
    .create-card {
        background: #fff; border: 1px solid rgba(0,0,0,0.06);
        border-radius: 22px; padding: 32px; max-width: 760px;
    }
    .form-label { font-size: .78rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: .05rem; display: block; margin-bottom: 8px; }
    .form-input {
        width: 100%; background: #f9fafb; border: 1.5px solid rgba(0,0,0,0.1);
        border-radius: 14px; padding: 12px 16px;
        font-family: 'Outfit', sans-serif; font-size: .9rem; color: #1f2937;
        outline: none; transition: all .2s;
    }
    .form-input:focus { border-color: #25c26e; background: #fff; box-shadow: 0 0 0 3px rgba(37,194,110,0.1); }
    .form-textarea { min-height: 200px; resize: vertical; line-height: 1.7; }
    .tips-card { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid rgba(37,194,110,0.2); border-radius: 16px; padding: 18px; }
    .char-counter { font-size: .72rem; color: #9ca3af; text-align: right; margin-top: 6px; }
    .char-counter.warn { color: #e8b84b; }
    .char-counter.error { color: #ef4444; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation: fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}
</style>
@endpush

@section('content')
@php
    // FIX : prefix dynamique pour toutes les routes de la vue
    $prefix = auth()->user()->isTeacher() ? 'teacher.' : 'student.';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- FORMULAIRE --}}
    <div class="lg:col-span-2 anim d1">
        <div class="create-card">
            {{-- Header --}}
            <div class="flex items-center gap-3 mb-8 pb-5 border-b border-black/5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                     style="background:rgba(37,194,110,0.1)">💬</div>
                <div>
                    <h2 class="font-bold text-gray-800 text-lg" style="font-family:'Playfair Display',serif">
                        Créer une discussion
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Dans le forum de <strong>{{ $course->title }}</strong></p>
                </div>
            </div>

            {{-- Erreurs --}}
            @if($errors->any())
            <div class="mb-5 px-4 py-3.5 rounded-xl" style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.18)">
                <div class="flex items-center gap-2 mb-1.5 text-red-600 font-semibold text-xs">⚠ Erreurs de saisie</div>
                @foreach($errors->all() as $error)
                <p class="text-xs text-red-500">• {{ $error }}</p>
                @endforeach
            </div>
            @endif

            {{-- FIX : action du formulaire dynamique --}}
            <form method="POST" action="{{ route($prefix.'forum.store', $course->slug) }}"
                  x-data="{ title: '{{ old('title') }}', body: '{{ old('body') }}' }">
                @csrf

                {{-- Titre --}}
                <div class="mb-5">
                    <label class="form-label" for="title">Titre de la discussion *</label>
                    <input type="text" id="title" name="title" x-model="title"
                           class="form-input @error('title') border-red-400 @enderror"
                           placeholder="Ex: Comment utiliser les migrations Laravel ?"
                           value="{{ old('title') }}" required maxlength="150">
                    <div class="char-counter" :class="{ 'warn': title.length > 100, 'error': title.length > 140 }">
                        <span x-text="title.length"></span> / 150 caractères
                    </div>
                    @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Corps --}}
                <div class="mb-6">
                    <label class="form-label" for="body">Votre message *</label>
                    <textarea id="body" name="body" x-model="body"
                              class="form-input form-textarea @error('body') border-red-400 @enderror"
                              placeholder="Décrivez votre question ou problème en détail. Plus votre question est précise, meilleures seront les réponses !"
                              required>{{ old('body') }}</textarea>
                    <div class="char-counter" :class="{ 'warn': body.length > 800, 'error': body.length > 950 }">
                        <span x-text="body.length"></span> / 1000 caractères min. recommandés
                    </div>
                    @error('body')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Preview --}}
                <div x-show="body.length > 0" class="mb-6 p-4 rounded-xl border border-black/5" style="background:#f9fafb">
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-2">Aperçu du message</p>
                    <p class="text-sm text-gray-700 leading-relaxed" x-text="body"></p>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-5 border-t border-black/5">
                    <button type="submit"
                            class="flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
                            style="background:linear-gradient(135deg,#1a8a47,#25c26e);box-shadow:0 4px 14px rgba(37,194,110,0.3)">
                        💬 Publier la discussion
                    </button>
                    {{-- FIX : lien Annuler dynamique (était hardcodé sur student.) --}}
                    <a href="{{ route($prefix.'forum.index', $course->slug) }}"
                       class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold transition-colors hover:bg-gray-100"
                       style="background:rgba(0,0,0,0.05);color:#6b7280">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- CONSEILS --}}
    <div class="anim d2">
        <div class="tips-card mb-4">
            <h3 class="text-sm font-bold text-green-800 mb-4 flex items-center gap-2">
                💡 Conseils pour une bonne question
            </h3>
            <div class="space-y-3">
                @foreach([
                    ['🎯', 'Soyez précis', 'Décrivez exactement le problème et ce que vous avez déjà essayé.'],
                    ['📋', 'Partagez le contexte', 'Mentionnez la leçon concernée, votre environnement, etc.'],
                    ['🔍', 'Recherchez d\'abord', 'Vérifiez si la question n\'a pas déjà été posée.'],
                    ['✍️', 'Soignez l\'orthographe', 'Une question bien rédigée attire de meilleures réponses.'],
                ] as [$icon, $title, $desc])
                <div class="flex items-start gap-2.5">
                    <span class="text-base mt-0.5 shrink-0">{{ $icon }}</span>
                    <div>
                        <div class="text-xs font-bold text-green-800">{{ $title }}</div>
                        <div class="text-xs text-green-700/70 leading-relaxed">{{ $desc }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Retour au forum --}}
        <div class="bg-white border border-black/5 rounded-xl p-4">
            {{-- FIX : lien retour dynamique --}}
            <a href="{{ route($prefix.'forum.index', $course->slug) }}"
               class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-green-700 transition-colors">
                ← Retour au forum
            </a>
        </div>
    </div>
</div>

@endsection