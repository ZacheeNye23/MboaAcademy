<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Créer un cours — MboaAcademy</title>
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
        .field-label { display:block;font-size:0.75rem;font-weight:600;letter-spacing:.06rem;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:8px; }
        .field-input { width:100%;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);border-radius:12px;color:#fff;font-family:'Outfit',sans-serif;font-size:0.9rem;padding:12px 16px;transition:all 0.25s;outline:none; }
        .field-input::placeholder { color:rgba(255,255,255,0.25); }
        .field-input:focus { border-color:#25c26e;background:rgba(37,194,110,0.05);box-shadow:0 0 0 3px rgba(37,194,110,0.1); }
        select.field-input option { background:#0f1f14;color:#fff; }
        textarea.field-input { resize:vertical;min-height:110px;line-height:1.6; }
        .form-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:20px;padding:28px; }
        .field-error { font-size:0.78rem;color:#f87171;margin-top:5px; }
        .upload-zone { border:2px dashed rgba(255,255,255,0.12);border-radius:16px;padding:28px;text-align:center;cursor:pointer;transition:all 0.25s; }
        .upload-zone:hover { border-color:#25c26e;background:rgba(37,194,110,0.04); }
        .toggle-wrap { display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px; }
        .btn-primary { display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:linear-gradient(135deg,#1a8a47,#25c26e);border-radius:12px;color:#fff;font-size:0.9rem;font-weight:600;border:none;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif; }
        .btn-primary:hover { transform:translateY(-1px);box-shadow:0 8px 20px rgba(37,194,110,0.3); }
        .btn-secondary { display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:rgba(255,255,255,0.7);font-size:0.9rem;font-weight:500;cursor:pointer;transition:all 0.2s;text-decoration:none; }
        .btn-secondary:hover { background:rgba(255,255,255,0.08);color:#fff; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim { animation:fadeUp 0.4s ease both; }
        .char-count { font-size:0.73rem;color:rgba(255,255,255,0.22);text-align:right;margin-top:3px; }
    </style>
</head>
<body x-data="courseCreate()">

<aside class="sidebar">
    <div class="px-6 py-5 border-b border-white/5">
        <a href="{{ route('welcome') }}" style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:900;color:#fff;text-decoration:none;">
            Mboa<span style="color:#e8b84b">Academy</span>
        </a>
        <div style="color:#25c26e;font-size:0.65rem;font-weight:700;letter-spacing:.1rem;text-transform:uppercase;margin-top:4px;">Espace Formateur</div>
    </div>

    <div class="px-4 py-5 border-b border-white/5">
        <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:.08rem;font-weight:700;color:rgba(255,255,255,0.2);padding:0 8px 8px;">Étapes de création</div>
        @foreach([
            [1,'Informations','Titre & description'],
            [2,'Médias','Thumbnail & vidéo'],
            [3,'Tarification','Prix & accès'],
        ] as [$n,$label,$sub])
        <div style="display:flex;align-items:center;gap:10px;padding:10px 8px;border-radius:12px;{{ $n==1 ? 'background:rgba(37,194,110,0.08)' : '' }}">
            <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;flex-shrink:0;{{ $n==1 ? 'background:#25c26e;color:#fff' : 'border:2px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.25)' }}">{{ $n }}</div>
            <div>
                <div style="font-size:0.8rem;font-weight:500;{{ $n==1 ? 'color:#25c26e' : 'color:rgba(255,255,255,0.35)' }}">{{ $label }}</div>
                <div style="font-size:0.68rem;color:rgba(255,255,255,0.2)">{{ $sub }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <nav class="flex-1 py-4">
        <a href="{{ route('teacher.dashboard') }}" class="nav-item"><span class="icon">🏠</span> Dashboard</a>
        <a href="{{ route('teacher.courses.index') }}" class="nav-item"><span class="icon">📚</span> Mes cours</a>
        <a href="{{ route('teacher.courses.create') }}" class="nav-item active"><span class="icon">➕</span> Nouveau cours</a>
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
    <header class="sticky top-0 z-30 flex items-center justify-between px-8 py-4 border-b"
            style="background:rgba(15,31,20,0.96);backdrop-filter:blur(12px);border-color:rgba(37,194,110,0.08)">
        <div>
            <h1 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:#fff;">Créer un nouveau cours</h1>
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:2px;">Remplissez les informations — vous ajouterez les chapitres & leçons ensuite</p>
        </div>
        <a href="{{ route('teacher.courses.index') }}" class="btn-secondary" style="font-size:0.85rem;padding:8px 18px;">← Retour</a>
    </header>

    <div class="p-8 max-w-3xl mx-auto">

        @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl anim" style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2)">
            <div style="color:#f87171;font-size:0.8rem;font-weight:600;margin-bottom:6px;">⚠ Erreurs de saisie</div>
            @foreach($errors->all() as $error)
            <div style="font-size:0.8rem;color:rgba(248,113,113,0.85);margin-top:3px">• {{ $error }}</div>
            @endforeach
        </div>
        @endif

        @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl anim" style="background:rgba(37,194,110,0.08);border:1px solid rgba(37,194,110,0.2)">
            <div style="color:#25c26e;font-size:0.85rem;font-weight:500;">🎉 {{ session('success') }}</div>
        </div>
        @endif

        <form method="POST" action="{{ route('teacher.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- ── SECTION 1 : Infos générales ── --}}
            <div class="form-card anim" style="animation-delay:.05s">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(37,194,110,0.12);display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">📋</div>
                    <div>
                        <div style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;">Informations générales</div>
                        <div style="font-size:0.75rem;color:rgba(255,255,255,0.35)">Décrivez votre cours pour attirer les bons apprenants</div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="field-label">Titre du cours <span style="color:#f87171">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="field-input" placeholder="Ex: Full Stack Web — Laravel & Vue.js de A à Z"
                           maxlength="255" x-on:input="titleCount = $el.value.length">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                        @error('title')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        <span class="char-count ml-auto" x-text="titleCount + ' / 255'"></span>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="field-label">Description complète <span style="color:#f87171">*</span></label>
                    <textarea name="description" class="field-input" rows="5"
                              placeholder="Décrivez ce que les apprenants vont apprendre, les prérequis et pourquoi ce cours est unique..."
                              maxlength="3000" x-on:input="descCount = $el.value.length">{{ old('description') }}</textarea>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                        @error('description')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        <span class="char-count ml-auto" x-text="descCount + ' / 3000 (min 50)'"></span>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="field-label">Objectifs d'apprentissage</label>
                    <textarea name="what_you_learn" class="field-input" rows="4"
                              placeholder="• Maîtriser Laravel 10&#10;• Construire une API REST complète&#10;• Déployer en production">{{ old('what_you_learn') }}</textarea>
                    <div style="font-size:0.72rem;color:rgba(255,255,255,0.25);margin-top:4px">Un objectif par ligne, commencez par •</div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div>
                        <label class="field-label">Catégorie <span style="color:#f87171">*</span></label>
                        <select name="category" class="field-input">
                            <option value="">Choisir...</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category')<span class="field-error">⚠ {{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="field-label">Niveau <span style="color:#f87171">*</span></label>
                        <select name="level" class="field-input">
                            @foreach($levels as $val => $label)
                            <option value="{{ $val }}" {{ old('level') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('level')<span class="field-error">⚠ {{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="field-label">Langue <span style="color:#f87171">*</span></label>
                        <select name="language" class="field-input">
                            @foreach($languages as $val => $label)
                            <option value="{{ $val }}" {{ old('language','fr') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 2 : Médias ── --}}
            <div class="form-card anim" style="animation-delay:.1s">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(232,184,75,0.12);display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">🖼️</div>
                    <div>
                        <div style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;">Miniature & aperçu</div>
                        <div style="font-size:0.75rem;color:rgba(255,255,255,0.35)">La première impression de votre cours</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div>
                        <label class="field-label">Image de couverture</label>
                        <div class="upload-zone"
                             x-on:dragover.prevent="$el.classList.add('dragover-active')"
                             x-on:dragleave="$el.classList.remove('dragover-active')"
                             x-on:drop.prevent="handleThumbDrop($event)"
                             x-on:click="$refs.thumbInput.click()"
                             style="min-height:140px;display:flex;align-items:center;justify-content:center;">
                            <input type="file" name="thumbnail" accept="image/*"
                                   x-ref="thumbInput" class="sr-only"
                                   x-on:change="previewThumb($event)">
                            <template x-if="!thumbPreview">
                                <div style="text-align:center;">
                                    <div style="font-size:2rem;margin-bottom:8px;">📷</div>
                                    <div style="font-size:0.85rem;font-weight:500;color:rgba(255,255,255,0.55);">Glissez ou cliquez</div>
                                    <div style="font-size:0.72rem;color:rgba(255,255,255,0.25);margin-top:4px;">JPG, PNG, WebP · Max 2 Mo</div>
                                </div>
                            </template>
                            <template x-if="thumbPreview">
                                <div style="position:relative;width:100%;">
                                    <img :src="thumbPreview" style="width:100%;height:130px;object-fit:cover;border-radius:10px;">
                                    <button type="button" x-on:click.stop="clearThumb()"
                                            style="position:absolute;top:8px;right:8px;width:26px;height:26px;border-radius:50%;background:rgba(0,0,0,0.75);color:#fff;border:none;cursor:pointer;font-size:0.75rem;">✕</button>
                                </div>
                            </template>
                        </div>
                        @error('thumbnail')<span class="field-error">⚠ {{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="field-label">Vidéo d'aperçu (URL)</label>
                        <div style="border:1.5px dashed rgba(255,255,255,0.1);border-radius:16px;padding:18px;min-height:140px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;">
                            <div style="font-size:2rem;">▶️</div>
                            <div style="font-size:0.78rem;color:rgba(255,255,255,0.35);text-align:center;">Lien YouTube ou Vimeo</div>
                            <input type="url" name="preview_video" value="{{ old('preview_video') }}"
                                   class="field-input" style="text-align:center;padding:10px 14px;"
                                   placeholder="https://youtube.com/watch?v=...">
                        </div>
                        @error('preview_video')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        <div style="font-size:0.7rem;color:rgba(255,255,255,0.22);margin-top:6px;">Une vidéo de 2-3 min augmente les inscriptions de 40%</div>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 3 : Tarification ── --}}
            <div class="form-card anim" style="animation-delay:.15s">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(37,194,110,0.12);display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">💰</div>
                    <div>
                        <div style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;">Tarification</div>
                        <div style="font-size:0.75rem;color:rgba(255,255,255,0.35)">Définissez l'accès à votre cours</div>
                    </div>
                </div>

                <div class="toggle-wrap mb-5">
                    <div>
                        <div style="font-size:0.88rem;font-weight:500;color:#fff;">Cours gratuit</div>
                        <div style="font-size:0.76rem;color:rgba(255,255,255,0.4);">Accessible à tous sans paiement</div>
                    </div>
                    <div style="width:44px;height:24px;border-radius:12px;position:relative;cursor:pointer;transition:background 0.2s;"
                         :style="isFree ? 'background:#25c26e' : 'background:rgba(255,255,255,0.12)'"
                         x-on:click="isFree = !isFree; if(isFree) price = 0">
                        <div style="width:18px;height:18px;background:#fff;border-radius:50%;position:absolute;top:3px;left:3px;transition:transform 0.2s;"
                             :style="isFree ? 'transform:translateX(20px)' : ''"></div>
                    </div>
                </div>
                <input type="hidden" name="is_free" :value="isFree ? '1' : '0'">

                <div x-show="!isFree" x-transition>
                    <label class="field-label">Prix (XAF) <span style="color:#f87171">*</span></label>
                    <div style="position:relative;">
                        <input type="number" name="price" x-model="price"
                               class="field-input" style="padding-right:65px;"
                               placeholder="0" min="0" step="500">
                        <span style="position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:0.82rem;color:rgba(255,255,255,0.4);font-weight:600;">XAF</span>
                    </div>
                    @error('price')<span class="field-error">⚠ {{ $message }}</span>@enderror

                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;">
                        @foreach([5000,10000,15000,25000,35000,50000] as $s)
                        <button type="button" x-on:click="price = {{ $s }}"
                                :style="price == {{ $s }} ? 'border-color:#25c26e;color:#25c26e' : 'border-color:rgba(255,255,255,0.1);color:rgba(255,255,255,0.4)'"
                                style="padding:5px 14px;border-radius:100px;font-size:0.78rem;background:transparent;border:1px solid;cursor:pointer;transition:all 0.2s;font-family:'Outfit',sans-serif;">
                            {{ number_format($s,0,',',' ') }} XAF
                        </button>
                        @endforeach
                    </div>

                    <div style="margin-top:14px;padding:12px 14px;border-radius:12px;background:rgba(37,194,110,0.05);border:1px solid rgba(37,194,110,0.12);font-size:0.78rem;color:rgba(255,255,255,0.5);line-height:1.65;">
                        Commission MboaAcademy : 20%. Pour un cours à
                        <strong style="color:#e8b84b;" x-text="formatXAF(price)"></strong>,
                        vous recevrez <strong style="color:#25c26e;" x-text="formatXAF(price * 0.8)"></strong>.
                    </div>
                </div>

                <div x-show="isFree" x-transition style="padding:16px;border-radius:12px;text-align:center;background:rgba(37,194,110,0.05);border:1px solid rgba(37,194,110,0.12);">
                    <div style="font-size:1.5rem;margin-bottom:6px;">🎁</div>
                    <div style="font-size:0.85rem;color:rgba(255,255,255,0.55);">Ce cours sera accessible gratuitement à tous les apprenants.</div>
                    <input type="hidden" name="price" value="0">
                </div>
            </div>

            {{-- ── ACTIONS ── --}}
            <div style="display:flex;align-items:center;justify-content:space-between;" class="anim" style="animation-delay:.2s">
                <a href="{{ route('teacher.courses.index') }}" class="btn-secondary">← Annuler</a>
                <div style="display:flex;align-items:center;gap:12px;">
                    <span style="font-size:0.78rem;color:rgba(255,255,255,0.3);">Vous ajouterez les chapitres ensuite</span>
                    <button type="submit" class="btn-primary">Créer le cours →</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
function courseCreate() {
    return {
        isFree: {{ old('is_free') ? 'true' : 'false' }},
        price: {{ old('price', 0) }},
        thumbPreview: null,
        titleCount: {{ strlen(old('title', '')) }},
        descCount: {{ strlen(old('description', '')) }},

        previewThumb(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => this.thumbPreview = e.target.result;
            reader.readAsDataURL(file);
        },

        handleThumbDrop(event) {
            const file = event.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.thumbInput.files = dt.files;
            const reader = new FileReader();
            reader.onload = e => this.thumbPreview = e.target.result;
            reader.readAsDataURL(file);
        },

        clearThumb() {
            this.thumbPreview = null;
            this.$refs.thumbInput.value = '';
        },

        formatXAF(amount) {
            if (!amount || isNaN(amount)) return '0 XAF';
            return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' XAF';
        },
    }
}
</script>
</body>
</html>