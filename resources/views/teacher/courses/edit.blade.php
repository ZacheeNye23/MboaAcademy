<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Éditer — {{ $course->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { font-family:'Outfit',sans-serif; background:#0f1f14; color:#e0ebe2; }
        .sidebar { width:260px;min-height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:40;display:flex;flex-direction:column;background:linear-gradient(180deg,#081409 0%,#0a1a0f 100%);border-right:1px solid rgba(37,194,110,0.08); }
        .main-content { margin-left:260px;min-height:100vh; }
        .nav-item { display:flex;align-items:center;gap:12px;padding:10px 20px;border-radius:12px;color:rgba(255,255,255,0.45);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all 0.2s;margin:2px 12px; }
        .nav-item:hover { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.8); }
        .nav-item.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .nav-item .icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;background:rgba(255,255,255,0.04); }
        .nav-item.active .icon { background:rgba(37,194,110,0.18); }
        .glass { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:20px; }
        .field-label { display:block;font-size:0.72rem;font-weight:600;letter-spacing:.06rem;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:7px; }
        .field-input { width:100%;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);border-radius:10px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.88rem;padding:10px 14px;transition:all 0.25s;outline:none; }
        .field-input::placeholder { color:rgba(255,255,255,0.22); }
        .field-input:focus { border-color:#25c26e;background:rgba(37,194,110,0.05);box-shadow:0 0 0 3px rgba(37,194,110,0.1); }
        select.field-input option { background:#0f1f14; }
        textarea.field-input { resize:vertical;min-height:80px;line-height:1.6; }

        /* Chapters & Lessons */
        .chapter-block { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:16px;overflow:hidden;margin-bottom:12px;transition:border-color 0.2s; }
        .chapter-block:hover { border-color:rgba(37,194,110,0.15); }
        .chapter-header { display:flex;align-items:center;gap:12px;padding:14px 18px;cursor:pointer;user-select:none; }
        .chapter-body { border-top:1px solid rgba(255,255,255,0.06);padding:16px 18px; }
        .lesson-row { display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);margin-bottom:8px;transition:all 0.2s; }
        .lesson-row:hover { background:rgba(37,194,110,0.05);border-color:rgba(37,194,110,0.15); }
        .lesson-type-badge { padding:3px 9px;border-radius:100px;font-size:0.68rem;font-weight:700;flex-shrink:0; }

        /* Upload vidéo */
        .video-upload-zone { border:2px dashed rgba(255,255,255,0.1);border-radius:12px;padding:18px;text-align:center;cursor:pointer;transition:all 0.25s; }
        .video-upload-zone:hover { border-color:#25c26e;background:rgba(37,194,110,0.04); }
        .upload-progress { height:4px;border-radius:2px;background:rgba(255,255,255,0.08);overflow:hidden;margin-top:8px; }
        .upload-progress-fill { height:100%;border-radius:2px;background:linear-gradient(90deg,#1a8a47,#25c26e);transition:width 0.4s; }

        /* Pills statuts */
        .pill-draft { background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.45);border:1px solid rgba(255,255,255,0.1); }
        .pill-pending { background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2); }
        .pill-published { background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2); }
        .pill-rejected { background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2); }
        .pill { display:inline-flex;align-items:center;padding:3px 10px;border-radius:100px;font-size:0.72rem;font-weight:700; }

        /* Btns */
        .btn-green { display:inline-flex;align-items:center;gap:7px;padding:10px 20px;background:linear-gradient(135deg,#1a8a47,#25c26e);border-radius:10px;color:#fff;font-size:0.85rem;font-weight:600;border:none;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif; }
        .btn-green:hover { transform:translateY(-1px);box-shadow:0 6px 18px rgba(37,194,110,0.3); }
        .btn-ghost { display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:10px;color:rgba(255,255,255,0.65);font-size:0.82rem;font-weight:500;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif; }
        .btn-ghost:hover { background:rgba(255,255,255,0.08);color:#fff; }
        .btn-danger { display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:8px;color:#f87171;font-size:0.78rem;font-weight:500;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif; }
        .btn-danger:hover { background:rgba(239,68,68,0.15); }

        @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .anim-1{animation-delay:.05s}.anim-2{animation-delay:.1s}.anim-3{animation-delay:.15s}

        /* Tabs */
        .tab-btn { padding:8px 18px;border-radius:10px;font-size:0.85rem;font-weight:500;cursor:pointer;border:none;background:transparent;transition:all 0.2s;font-family:'Outfit',sans-serif; }
        .tab-btn.active { background:rgba(37,194,110,0.12);color:#25c26e; }
        .tab-btn:not(.active) { color:rgba(255,255,255,0.45); }
        .tab-btn:not(.active):hover { color:rgba(255,255,255,0.75); }
    </style>
</head>
<body x-data="courseEdit()">

<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>

    {{-- Infos cours dans sidebar --}}
    <div class="px-5 py-4 border-b border-white/5">
        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.2);margin-bottom:8px;">Cours en édition</div>
        <div style="font-size:0.85rem;font-weight:600;color:#fff;margin-bottom:6px;line-height:1.3;">{{ Str::limit($course->title, 35) }}</div>
        <div class="pill pill-{{ $course->status }}">{{ $course->status_label }}</div>

        {{-- Progress chapitres --}}
        <div style="margin-top:12px;">
            <div style="display:flex;justify-content:space-between;font-size:0.7rem;color:rgba(255,255,255,0.3);margin-bottom:5px;">
                <span>Chapitres</span>
                <span>{{ $course->chapters->count() }}</span>
            </div>
            <div style="height:3px;border-radius:2px;background:rgba(255,255,255,0.07);">
                @php $pct = min(100, $course->chapters->count() * 20); @endphp
                <div style="width:{{ $pct }}%;height:100%;border-radius:2px;background:#25c26e;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.7rem;color:rgba(255,255,255,0.3);margin-top:5px;">
                <span>Leçons</span>
                <span>{{ $course->chapters->flatMap->lessons->count() }}</span>
            </div>
        </div>
    </div>

    <nav class="flex-1 py-4">
        <a href="{{ route('teacher.dashboard') }}" class="nav-item"><span class="icon">🏠</span> Dashboard</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="icon">📚</span> Mes cours</a>
        <a href="{{ route('teacher.courses.create') }}" class="nav-item"><span class="icon">➕</span> Nouveau cours</a>
    </nav>

    <div class="p-4 border-t border-white/5">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-left" style="background:rgba(239,68,68,0.07);color:rgba(239,68,68,0.75);">
                <span class="icon" style="background:rgba(239,68,68,0.08);">🚪</span> Déconnexion
            </button>
        </form>
    </div>
</aside>

<div class="main-content">

    {{-- Topbar --}}
    <header class="sticky top-0 z-30 flex items-center justify-between px-8 py-3 border-b"
            style="background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);border-color:rgba(37,194,110,0.08)">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('teacher.courses.index') }}" class="btn-ghost" style="padding:6px 12px;">← Retour</a>
            <div>
                <span style="font-size:0.82rem;color:rgba(255,255,255,0.35);">Édition :</span>
                <span style="font-size:0.92rem;font-weight:600;color:#fff;margin-left:6px;">{{ Str::limit($course->title, 40) }}</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            @if($course->status === 'draft')
            <form method="POST" action="{{ route('teacher.courses.submit', $course) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn-green" style="background:linear-gradient(135deg,#e8a820,#f0c040);color:#0a1a0f;">
                    🚀 Soumettre pour validation
                </button>
            </form>
            @elseif($course->status === 'pending')
            <div style="padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;background:rgba(232,184,75,0.1);border:1px solid rgba(232,184,75,0.2);color:#e8b84b;">
                ⏳ En attente de validation
            </div>
            @elseif($course->status === 'published')
            <div style="padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;background:rgba(37,194,110,0.1);border:1px solid rgba(37,194,110,0.2);color:#25c26e;">
                ✅ Cours publié
            </div>
            @endif
        </div>
    </header>

    <div class="p-8">

        @if(session('success'))
        <div class="mb-5 px-5 py-3 rounded-2xl" style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2)">
            <span style="color:#25c26e;font-size:0.85rem;">🎉 {{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-5 px-5 py-3 rounded-2xl" style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2)">
            <span style="color:#f87171;font-size:0.85rem;">⚠ {{ session('error') }}</span>
        </div>
        @endif

        {{-- ── TABS ── --}}
        <div style="display:flex;gap:4px;margin-bottom:24px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:5px;width:fit-content;">
            <button class="tab-btn" :class="tab === 'info' ? 'active' : ''" x-on:click="tab = 'info'">📋 Informations</button>
            <button class="tab-btn" :class="tab === 'content' ? 'active' : ''" x-on:click="tab = 'content'">📚 Chapitres & Leçons</button>
            <button class="tab-btn" :class="tab === 'settings' ? 'active' : ''" x-on:click="tab = 'settings'">⚙️ Paramètres</button>
        </div>

        {{-- ── TAB : INFORMATIONS ── --}}
        <div x-show="tab === 'info'" x-transition>
            <form method="POST" action="{{ route('teacher.courses.update', $course) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="glass p-7 mb-5 anim anim-1">
                    <div style="display:grid;grid-template-columns:1fr;gap:18px;">
                        <div>
                            <label class="field-label">Titre du cours *</label>
                            <input type="text" name="title" value="{{ old('title', $course->title) }}" class="field-input" required>
                            @error('title')<div style="font-size:0.78rem;color:#f87171;margin-top:4px;">⚠ {{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="field-label">Description *</label>
                            <textarea name="description" class="field-input" rows="5" required>{{ old('description', $course->description) }}</textarea>
                        </div>
                        <div>
                            <label class="field-label">Objectifs d'apprentissage</label>
                            <textarea name="what_you_learn" class="field-input" rows="4">{{ old('what_you_learn', $course->what_you_learn) }}</textarea>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
                            <div>
                                <label class="field-label">Catégorie *</label>
                                <select name="category" class="field-input">
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $course->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Niveau *</label>
                                <select name="level" class="field-input">
                                    @foreach($levels as $val => $label)
                                    <option value="{{ $val }}" {{ old('level', $course->level) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Langue</label>
                                <select name="language" class="field-input">
                                    @foreach($languages as $val => $label)
                                    <option value="{{ $val }}" {{ old('language', $course->language) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Thumbnail --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label class="field-label">Miniature</label>
                                <div class="video-upload-zone" x-on:click="$refs.thumbEdit.click()">
                                    <input type="file" name="thumbnail" accept="image/*" x-ref="thumbEdit" class="sr-only"
                                           x-on:change="previewNewThumb($event)">
                                    <template x-if="newThumbPreview">
                                        <img :src="newThumbPreview" style="width:100%;height:110px;object-fit:cover;border-radius:8px;">
                                    </template>
                                    <template x-if="!newThumbPreview">
                                        @if($course->thumbnail)
                                        <img src="{{ $course->thumbnail_url }}" style="width:100%;height:110px;object-fit:cover;border-radius:8px;">
                                        @else
                                        <div style="padding:20px 0;">
                                            <div style="font-size:1.8rem;margin-bottom:6px;">📷</div>
                                            <div style="font-size:0.8rem;color:rgba(255,255,255,0.4);">Changer la miniature</div>
                                        </div>
                                        @endif
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="field-label">Vidéo d'aperçu (URL)</label>
                                <div style="border:1.5px dashed rgba(255,255,255,0.1);border-radius:12px;padding:14px;min-height:130px;display:flex;flex-direction:column;justify-content:center;gap:8px;">
                                    <div style="font-size:1.5rem;text-align:center;">▶️</div>
                                    <input type="url" name="preview_video" value="{{ old('preview_video', $course->preview_video) }}"
                                           class="field-input" style="text-align:center;" placeholder="https://youtube.com/watch?v=...">
                                </div>
                            </div>
                        </div>

                        {{-- Prix --}}
                        <div>
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:10px;margin-bottom:12px;">
                                <div>
                                    <div style="font-size:0.85rem;font-weight:500;color:#fff;">Cours gratuit</div>
                                </div>
                                <div style="width:40px;height:22px;border-radius:11px;position:relative;cursor:pointer;transition:background 0.2s;"
                                     :style="editFree ? 'background:#25c26e' : 'background:rgba(255,255,255,0.1)'"
                                     x-on:click="editFree = !editFree; if(editFree) editPrice = 0">
                                    <div style="width:16px;height:16px;background:#fff;border-radius:50%;position:absolute;top:3px;left:3px;transition:transform 0.2s;"
                                         :style="editFree ? 'transform:translateX(18px)' : ''"></div>
                                </div>
                            </div>
                            <input type="hidden" name="is_free" :value="editFree ? '1' : '0'">
                            <div x-show="!editFree" x-transition>
                                <label class="field-label">Prix (XAF)</label>
                                <div style="position:relative;">
                                    <input type="number" name="price" x-model="editPrice"
                                           class="field-input" style="padding-right:60px;" min="0" step="500">
                                    <span style="position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:0.8rem;color:rgba(255,255,255,0.4);">XAF</span>
                                </div>
                            </div>
                            <input x-show="editFree" type="hidden" name="price" value="0">
                        </div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <button type="submit" class="btn-green">💾 Enregistrer les modifications</button>
                </div>
            </form>
        </div>

        {{-- ── TAB : CHAPITRES & LEÇONS ── --}}
        <div x-show="tab === 'content'" x-transition>

            {{-- Ajouter un chapitre --}}
            <div class="glass p-5 mb-5 anim anim-1">
                <div style="font-size:0.82rem;font-weight:600;color:rgba(255,255,255,0.5);margin-bottom:12px;text-transform:uppercase;letter-spacing:.06rem;">Ajouter un chapitre</div>
                <div style="display:flex;gap:10px;">
                    <input type="text" x-model="newChapterTitle" placeholder="Titre du chapitre..."
                           class="field-input flex-1"
                           x-on:keydown.enter.prevent="addChapter()">
                    <button type="button" class="btn-green" x-on:click="addChapter()" :disabled="!newChapterTitle.trim()">
                        + Ajouter
                    </button>
                </div>
                <div x-show="chapterError" x-transition style="font-size:0.78rem;color:#f87171;margin-top:6px;" x-text="chapterError"></div>
            </div>

            {{-- Liste chapitres --}}
            <div x-show="chapters.length === 0" class="glass p-10 text-center anim anim-2">
                <div style="font-size:3rem;margin-bottom:10px;">📚</div>
                <div style="font-size:0.9rem;color:rgba(255,255,255,0.4);">Aucun chapitre pour l'instant.</div>
                <div style="font-size:0.8rem;color:rgba(255,255,255,0.25);margin-top:4px;">Ajoutez votre premier chapitre ci-dessus.</div>
            </div>

            <template x-for="(chapter, ci) in chapters" :key="chapter.id">
                <div class="chapter-block anim">

                    {{-- En-tête chapitre --}}
                    <div class="chapter-header" x-on:click="chapter.open = !chapter.open">
                        <div style="width:28px;height:28px;border-radius:8px;background:rgba(37,194,110,0.12);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#25c26e;flex-shrink:0;" x-text="ci + 1"></div>
                        <div style="flex:1;">
                            <div x-show="!chapter.editing" style="font-size:0.9rem;font-weight:600;color:#fff;" x-text="chapter.title"></div>
                            <input x-show="chapter.editing" type="text" x-model="chapter.editTitle"
                                   class="field-input" style="padding:6px 10px;font-size:0.88rem;"
                                   x-on:click.stop x-on:keydown.enter.prevent="saveChapter(chapter)"
                                   x-on:keydown.escape="chapter.editing = false">
                            <div style="font-size:0.72rem;color:rgba(255,255,255,0.3);margin-top:2px;">
                                <span x-text="chapter.lessons ? chapter.lessons.length : 0"></span> leçon(s)
                            </div>
                        </div>
                        <div style="display:flex;gap:6px;align-items:center;" x-on:click.stop>
                            <button type="button" class="btn-ghost" style="padding:5px 10px;font-size:0.75rem;"
                                    x-on:click="chapter.editing = !chapter.editing; chapter.editTitle = chapter.title">
                                ✏️
                            </button>
                            <button x-show="chapter.editing" type="button" class="btn-green" style="padding:5px 10px;font-size:0.75rem;"
                                    x-on:click="saveChapter(chapter)">✓</button>
                            <button type="button" class="btn-danger" style="padding:5px 10px;font-size:0.75rem;"
                                    x-on:click="deleteChapter(chapter, ci)">🗑</button>
                            <div style="font-size:0.9rem;color:rgba(255,255,255,0.3);margin-left:4px;" x-text="chapter.open ? '▲' : '▼'"></div>
                        </div>
                    </div>

                    {{-- Body chapitre --}}
                    <div class="chapter-body" x-show="chapter.open" x-transition>

                        {{-- Leçons existantes --}}
                        <div style="margin-bottom:12px;">
                            <template x-for="(lesson, li) in chapter.lessons" :key="lesson.id">
                                <div class="lesson-row">
                                    <div style="width:22px;height:22px;border-radius:6px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;font-size:0.68rem;color:rgba(255,255,255,0.35);flex-shrink:0;" x-text="li + 1"></div>
                                    <div style="font-size:0.85rem;font-weight:500;color:#fff;flex:1;" x-text="lesson.title"></div>
                                    <span class="lesson-type-badge"
                                          :style="lesson.type === 'video' ? 'background:rgba(59,130,246,0.12);color:#60a5fa' : lesson.type === 'text' ? 'background:rgba(167,139,250,0.12);color:#c4b5fd' : 'background:rgba(249,115,22,0.12);color:#fb923c'"
                                          x-text="lesson.type === 'video' ? '🎬 Vidéo' : lesson.type === 'text' ? '📄 Texte' : '🔀 Mixte'"></span>
                                    <span style="font-size:0.72rem;color:rgba(255,255,255,0.3);" x-text="lesson.duration || '—'"></span>
                                    <span x-show="lesson.is_free" style="font-size:0.68rem;padding:2px 7px;border-radius:100px;background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2);">Gratuit</span>
                                    <button type="button" class="btn-danger" style="padding:4px 8px;font-size:0.72rem;"
                                            x-on:click="deleteLesson(lesson, chapter, li)">🗑</button>
                                </div>
                            </template>
                            <div x-show="!chapter.lessons || chapter.lessons.length === 0" style="padding:12px;text-align:center;font-size:0.82rem;color:rgba(255,255,255,0.3);border:1px dashed rgba(255,255,255,0.08);border-radius:10px;">
                                Aucune leçon — ajoutez-en une ci-dessous
                            </div>
                        </div>

                        {{-- Formulaire ajout leçon --}}
                        <div style="background:rgba(37,194,110,0.03);border:1px solid rgba(37,194,110,0.1);border-radius:12px;padding:16px;">
                            <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.35);margin-bottom:12px;">+ Nouvelle leçon</div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                                <div>
                                    <label class="field-label">Titre *</label>
                                    <input type="text" x-model="chapter.newLesson.title"
                                           class="field-input" placeholder="Titre de la leçon">
                                </div>
                                <div>
                                    <label class="field-label">Type *</label>
                                    <select x-model="chapter.newLesson.type" class="field-input">
                                        <option value="video">🎬 Vidéo</option>
                                        <option value="text">📄 Texte</option>
                                        <option value="mixed">🔀 Mixte</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Upload vidéo (si type video ou mixed) --}}
                            <div x-show="chapter.newLesson.type !== 'text'" x-transition class="mb-3">
                                <label class="field-label">Vidéo</label>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                    <div class="video-upload-zone" x-on:click="triggerVideoUpload(chapter)"
                                         :style="chapter.newLesson.videoFile ? 'border-color:#25c26e;background:rgba(37,194,110,0.04)' : ''">
                                        <input type="file" :x-ref="'video_' + chapter.id"
                                               accept="video/mp4,video/mpeg,video/quicktime,video/webm"
                                               :id="'video_ch_' + chapter.id"
                                               class="sr-only"
                                               x-on:change="handleVideoSelect($event, chapter)">
                                        <template x-if="!chapter.newLesson.videoFile">
                                            <div style="padding:10px 0;">
                                                <div style="font-size:1.5rem;margin-bottom:4px;">🎥</div>
                                                <div style="font-size:0.75rem;color:rgba(255,255,255,0.35);">Upload vidéo locale</div>
                                                <div style="font-size:0.68rem;color:rgba(255,255,255,0.2);margin-top:3px;">MP4, MOV, WebM · Max 500 Mo</div>
                                            </div>
                                        </template>
                                        <template x-if="chapter.newLesson.videoFile">
                                            <div style="padding:6px 0;">
                                                <div style="font-size:1.2rem;margin-bottom:3px;">✅</div>
                                                <div style="font-size:0.75rem;color:#25c26e;font-weight:500;" x-text="chapter.newLesson.videoFile.name"></div>
                                                <div style="font-size:0.68rem;color:rgba(255,255,255,0.3);" x-text="formatFileSize(chapter.newLesson.videoFile.size)"></div>
                                            </div>
                                        </template>
                                    </div>
                                    <div>
                                        <div style="font-size:0.72rem;color:rgba(255,255,255,0.3);margin-bottom:6px;">Ou lien externe</div>
                                        <input type="text" x-model="chapter.newLesson.video_url"
                                               class="field-input" style="padding:9px 12px;"
                                               placeholder="https://youtube.com/...">
                                        <div style="margin-top:8px;">
                                            <label class="field-label" style="font-size:0.68rem;">Durée (secondes)</label>
                                            <input type="number" x-model="chapter.newLesson.duration"
                                                   class="field-input" style="padding:8px 12px;" placeholder="0" min="0">
                                        </div>
                                    </div>
                                </div>

                                {{-- Barre progression upload --}}
                                <div x-show="chapter.newLesson.uploadProgress > 0 && chapter.newLesson.uploadProgress < 100" class="upload-progress mt-2">
                                    <div class="upload-progress-fill" :style="'width:' + chapter.newLesson.uploadProgress + '%'"></div>
                                </div>
                            </div>

                            {{-- Contenu texte --}}
                            <div x-show="chapter.newLesson.type !== 'video'" x-transition class="mb-3">
                                <label class="field-label">Contenu texte</label>
                                <textarea x-model="chapter.newLesson.content" class="field-input" rows="3"
                                          placeholder="Contenu de la leçon en Markdown ou HTML..."></textarea>
                            </div>

                            {{-- Options --}}
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                    <input type="checkbox" x-model="chapter.newLesson.is_free"
                                           style="width:16px;height:16px;accent-color:#25c26e;">
                                    <span style="font-size:0.82rem;color:rgba(255,255,255,0.55);">Leçon gratuite (aperçu)</span>
                                </label>
                                <button type="button" class="btn-green" style="padding:9px 18px;"
                                        x-on:click="addLesson(chapter)"
                                        :disabled="!chapter.newLesson.title.trim() || chapter.newLesson.uploading">
                                    <span x-show="!chapter.newLesson.uploading">+ Ajouter la leçon</span>
                                    <span x-show="chapter.newLesson.uploading">⏳ Upload en cours...</span>
                                </button>
                            </div>
                            <div x-show="chapter.newLesson.error" x-transition style="font-size:0.78rem;color:#f87171;margin-top:6px;" x-text="chapter.newLesson.error"></div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Checklist soumission --}}
            <div class="glass p-5 mt-6 anim anim-3">
                <div style="font-size:0.82rem;font-weight:600;color:rgba(255,255,255,0.5);margin-bottom:14px;text-transform:uppercase;letter-spacing:.06rem;">Checklist avant soumission</div>
                @php
                    $checks = [
                        [$course->title && $course->description, 'Titre et description renseignés'],
                        [$course->thumbnail, 'Miniature de couverture ajoutée'],
                        [$course->chapters->count() >= 1, 'Au moins 1 chapitre créé'],
                        [$course->chapters->flatMap->lessons->count() >= 1, 'Au moins 1 leçon ajoutée'],
                        [$course->price >= 0, 'Prix défini'],
                    ];
                @endphp
                <div style="display:grid;gap:8px;">
                    @foreach($checks as [$ok, $label])
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.7rem;flex-shrink:0;{{ $ok ? 'background:rgba(37,194,110,0.15);color:#25c26e' : 'background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.3)' }}">
                            {{ $ok ? '✓' : '○' }}
                        </div>
                        <span style="font-size:0.85rem;{{ $ok ? 'color:rgba(255,255,255,0.7)' : 'color:rgba(255,255,255,0.35)' }}">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>

                @if($course->status === 'draft')
                @php $allGood = collect($checks)->every(fn($c) => $c[0]); @endphp
                <div style="margin-top:16px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.06);">
                    @if($allGood)
                    <form method="POST" action="{{ route('teacher.courses.submit', $course) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-green" style="width:100%;justify-content:center;background:linear-gradient(135deg,#d4960a,#e8b84b);color:#0a1a0f;">
                            🚀 Soumettre pour validation
                        </button>
                    </form>
                    @else
                    <div style="text-align:center;font-size:0.82rem;color:rgba(255,255,255,0.35);padding:10px;">
                        Complétez tous les éléments pour pouvoir soumettre
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- ── TAB : PARAMÈTRES ── --}}
        <div x-show="tab === 'settings'" x-transition>
            <div class="glass p-6 anim anim-1">
                <div style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;margin-bottom:16px;">Paramètres avancés</div>

                @if(in_array($course->status, ['draft', 'rejected']))
                <div style="padding:16px;border-radius:12px;background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.15);margin-top:20px;">
                    <div style="font-size:0.88rem;font-weight:600;color:#f87171;margin-bottom:8px;">Zone dangereuse</div>
                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.45);margin-bottom:12px;">La suppression est irréversible. Tous les chapitres et leçons seront perdus.</div>
                    <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}"
                          x-on:submit.prevent="confirm('Supprimer définitivement ce cours ?') && $el.submit()">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger" style="padding:9px 18px;">🗑 Supprimer ce cours</button>
                    </form>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

<script>
function courseEdit() {
    return {
        tab: 'content',
        newChapterTitle: '',
        chapterError: '',
        newThumbPreview: null,
        editFree: {{ $course->is_free ? 'true' : 'false' }},
        editPrice: {{ $course->price }},

        chapters: @json($course->chapters->map(fn($c) => [
            'id'       => $c->id,
            'title'    => $c->title,
            'order'    => $c->order,
            'open'     => true,
            'editing'  => false,
            'editTitle'=> $c->title,
            'lessons'  => $c->lessons->map(fn($l) => [
                'id'       => $l->id,
                'title'    => $l->title,
                'type'     => $l->type,
                'duration' => $l->duration_formatted,
                'is_free'  => $l->is_free,
                'video_path'=> $l->video_path,
                'video_url' => $l->video_url,
            ])->values()->all(),
            'newLesson'=> [
                'title'          => '',
                'type'           => 'video',
                'content'        => '',
                'video_url'      => '',
                'duration'       => '',
                'is_free'        => false,
                'videoFile'      => null,
                'uploadProgress' => 0,
                'uploading'      => false,
                'error'          => '',
            ],
        ])->values()->all()),

        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

        // ── Chapitres ─────────────────────────────────────────────────────────

        async addChapter() {
            const title = this.newChapterTitle.trim();
            if (!title) return;
            this.chapterError = '';

            try {
                const res = await fetch(`/teacher/courses/{{ $course->id }}/chapters`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                    body: JSON.stringify({ title }),
                });
                const data = await res.json();
                if (data.success) {
                    this.chapters.push({
                        ...data.chapter,
                        open: true, editing: false, editTitle: data.chapter.title,
                        lessons: [],
                        newLesson: { title:'', type:'video', content:'', video_url:'', duration:'', is_free:false, videoFile:null, uploadProgress:0, uploading:false, error:'' },
                    });
                    this.newChapterTitle = '';
                }
            } catch (e) {
                this.chapterError = 'Erreur lors de l\'ajout du chapitre.';
            }
        },

        async saveChapter(chapter) {
            if (!chapter.editTitle.trim()) return;
            try {
                const res = await fetch(`/teacher/courses/chapters/${chapter.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                    body: JSON.stringify({ title: chapter.editTitle }),
                });
                const data = await res.json();
                if (data.success) {
                    chapter.title = chapter.editTitle;
                    chapter.editing = false;
                }
            } catch(e) {}
        },

        async deleteChapter(chapter, index) {
            if (!confirm(`Supprimer le chapitre "${chapter.title}" et toutes ses leçons ?`)) return;
            try {
                const res = await fetch(`/teacher/courses/chapters/${chapter.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken },
                });
                const data = await res.json();
                if (data.success) this.chapters.splice(index, 1);
            } catch(e) {}
        },

        // ── Leçons ────────────────────────────────────────────────────────────

        triggerVideoUpload(chapter) {
            document.getElementById('video_ch_' + chapter.id).click();
        },

        handleVideoSelect(event, chapter) {
            const file = event.target.files[0];
            if (file) chapter.newLesson.videoFile = file;
        },

        async addLesson(chapter) {
            const nl = chapter.newLesson;
            if (!nl.title.trim()) return;
            nl.error = '';
            nl.uploading = true;

            const formData = new FormData();
            formData.append('title',    nl.title);
            formData.append('type',     nl.type);
            formData.append('content',  nl.content);
            formData.append('video_url',nl.video_url);
            formData.append('duration', nl.duration || 0);
            formData.append('is_free',  nl.is_free ? '1' : '0');
            if (nl.videoFile) formData.append('video', nl.videoFile);

            try {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', `/teacher/courses/chapters/${chapter.id}/lessons`);
                xhr.setRequestHeader('X-CSRF-TOKEN', this.csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.onprogress = e => {
                    if (e.lengthComputable) nl.uploadProgress = Math.round((e.loaded / e.total) * 100);
                };

                xhr.onload = () => {
                    nl.uploading = false;
                    nl.uploadProgress = 0;
                    const data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        chapter.lessons.push(data.lesson);
                        nl.title = ''; nl.content = ''; nl.video_url = '';
                        nl.duration = ''; nl.is_free = false; nl.videoFile = null;
                    } else {
                        nl.error = 'Erreur lors de l\'ajout de la leçon.';
                    }
                };

                xhr.onerror = () => { nl.uploading = false; nl.error = 'Erreur réseau.'; };
                xhr.send(formData);

            } catch(e) {
                nl.uploading = false;
                nl.error = 'Erreur inattendue.';
            }
        },

        async deleteLesson(lesson, chapter, index) {
            if (!confirm(`Supprimer la leçon "${lesson.title}" ?`)) return;
            try {
                const res = await fetch(`/teacher/courses/lessons/${lesson.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken },
                });
                const data = await res.json();
                if (data.success) chapter.lessons.splice(index, 1);
            } catch(e) {}
        },

        // ── Utils ─────────────────────────────────────────────────────────────

        previewNewThumb(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => this.newThumbPreview = e.target.result;
            reader.readAsDataURL(file);
        },

        formatFileSize(bytes) {
            const mb = bytes / (1024 * 1024);
            return mb > 1 ? mb.toFixed(1) + ' Mo' : (bytes / 1024).toFixed(0) + ' Ko';
        },
    }
}
</script>
</body>
</html>