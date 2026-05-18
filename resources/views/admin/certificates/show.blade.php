@extends('admin.layouts.app')

@section('title', 'Certificat ' . $certificate->certificate_number)
@section('page-title', 'Détail du certificat')
@section('page-subtitle', $certificate->certificate_number)

@section('topbar-actions')
<div class="flex gap-2">
    <a href="{{ route('admin.certificates.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium"
       style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5)">
        ← Retour
    </a>
    <a href="{{ route('certificates.verify', $certificate->certificate_number) }}" target="_blank"
       class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold"
       style="background:rgba(37,194,110,0.1);color:#25c26e;border:1px solid rgba(37,194,110,0.2)">
        🔗 Vérifier
    </a>
    <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}"
          onsubmit="return confirm('Révoquer définitivement ce certificat ?')">
        @csrf @method('DELETE')
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold"
                style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2)">
            🗑 Révoquer
        </button>
    </form>
</div>
@endsection

@push('styles')
<style>
    /* ── Aperçu certificat ── */
    .cert-preview-wrap {
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        border-radius: 20px; padding: 28px;
        display: flex; align-items: center; justify-content: center;
    }
    .cert-preview {
        width: 100%; max-width: 560px;
        background: #faf6ef;
        border-radius: 4px;
        position: relative; overflow: hidden;
        aspect-ratio: 1.414;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .cert-border { position:absolute;inset:12px;border:1.5px solid rgba(232,184,75,0.4);border-radius:2px;pointer-events:none;z-index:2; }
    .cert-bg {
        position:absolute;inset:0;
        background-image:
            radial-gradient(ellipse 60% 40% at 50% 50%, rgba(232,184,75,0.04) 0%, transparent 70%),
            repeating-linear-gradient(0deg,transparent,transparent 29px,rgba(232,184,75,0.04) 30px),
            repeating-linear-gradient(90deg,transparent,transparent 29px,rgba(232,184,75,0.04) 30px);
    }
    .cert-content {
        position:absolute;inset:0;z-index:3;
        display:flex;flex-direction:column;align-items:center;justify-content:center;
        padding:24px 40px;text-align:center;
    }
    .cert-logo-text { font-family:'Playfair Display',serif;font-size:.85rem;font-weight:900;color:#0d5c2e; }
    .cert-logo-text span { color:#e8b84b; }
    .cert-divider-line { flex:1;height:1px;background:linear-gradient(90deg,transparent,rgba(232,184,75,0.4),transparent); }
    .cert-title-text { font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:900;color:#0d5c2e;text-transform:uppercase;letter-spacing:.05rem; }
    .cert-recipient-text { font-family:'Georgia',serif;font-size:1.6rem;font-weight:700;color:#0a1a0f;font-style:italic; }
    .cert-course-text { font-family:'Playfair Display',serif;font-size:.75rem;font-weight:700;color:#1f2937; }

    .corner-svg { position:absolute;width:28px;height:28px;z-index:3; }

    /* ── Info sections ── */
    .info-row { display:flex;align-items:flex-start;gap:12px;padding:12px 0;border-bottom:1px solid rgba(255,255,255,0.05); }
    .info-row:last-child { border-bottom:none; }
    .info-label { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06rem;color:rgba(255,255,255,0.28);width:110px;flex-shrink:0;padding-top:2px; }
    .info-value { font-size:.875rem;color:rgba(255,255,255,0.75);flex:1; }

    .stat-box { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:14px;padding:16px;text-align:center; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .anim { animation:fadeUp .4s ease both; }
    .d1{animation-delay:.04s}.d2{animation-delay:.08s}.d3{animation-delay:.12s}.d4{animation-delay:.16s}
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-xs mb-6 anim d1" style="color:rgba(255,255,255,0.35)">
    <a href="{{ route('admin.certificates.index') }}" class="hover:text-white transition-colors">← Certificats</a>
    <span>/</span>
    <span class="text-white font-mono">{{ $certificate->certificate_number }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ── APERÇU CERTIFICAT (3 colonnes) ── --}}
    <div class="lg:col-span-3 space-y-5">

        {{-- Preview --}}
        <div class="cert-preview-wrap anim d1">
            <div class="cert-preview">
                <div class="cert-bg"></div>
                <div class="cert-border"></div>

                {{-- Coins SVG --}}
                @foreach(['top-3 left-3','top-3 right-3 rotate-90','bottom-3 left-3 -rotate-90','bottom-3 right-3 rotate-180'] as $pos)
                <svg class="corner-svg {{ $pos }}" viewBox="0 0 28 28" fill="none">
                    <path d="M2 2 L12 2 L2 12" stroke="#e8b84b" stroke-width="1.5" fill="none" opacity=".6"/>
                    <circle cx="4" cy="4" r="1.2" fill="#e8b84b" opacity=".5"/>
                </svg>
                @endforeach

                <div class="cert-content">
                    <div class="cert-logo-text mb-1">Mboa<span>Academy</span></div>
                    <div style="font-size:.45rem;font-weight:700;letter-spacing:.12rem;color:rgba(13,92,46,0.5);text-transform:uppercase;margin-bottom:10px">
                        Plateforme e-learning africaine
                    </div>

                    {{-- Divider --}}
                    <div class="flex items-center gap-2 w-full mb-8">
                        <div class="cert-divider-line"></div>
                        <span style="color:#e8b84b;font-size:.55rem">✦ ✦ ✦</span>
                        <div class="cert-divider-line"></div>
                    </div>

                    <div class="cert-title-text mb-2">Certificat de Complétion</div>
                    <div style="font-size:.45rem;color:#9ca3af;letter-spacing:.1rem;text-transform:uppercase;margin-bottom:6px">
                        Ce certificat est décerné à
                    </div>
                    <div class="cert-recipient-text mb-2">{{ $certificate->user->full_name }}</div>

                    <div class="flex items-center gap-2 w-full mb-6" style="opacity:.4">
                        <div class="cert-divider-line"></div>
                        <span style="color:#e8b84b;font-size:.4rem">✦</span>
                        <div class="cert-divider-line"></div>
                    </div>

                    <div style="font-size:.42rem;color:#9ca3af;letter-spacing:.08rem;text-transform:uppercase;margin-bottom:4px">
                        pour avoir complété avec succès
                    </div>
                    <div class="cert-course-text" style="max-width:320px">
                        {{ $certificate->course->title }}
                    </div>
                    <div style="font-size:.38rem;font-family:monospace;color:#9ca3af;margin-top:10px">
                        N° {{ $certificate->certificate_number }}
                    </div>
                </div>

                {{-- Footer preview --}}
                <div style="position:absolute;bottom:14px;left:30px;right:30px;display:flex;justify-content:space-between;align-items:flex-end;z-index:3">
                    <div style="text-align:center">
                        <div style="width:60px;height:1px;background:rgba(0,0,0,0.2);margin:0 auto 3px"></div>
                        <div style="font-size:.38rem;color:#6b7280;text-transform:uppercase;letter-spacing:.06rem">
                            {{ $certificate->course->teacher->full_name }}
                        </div>
                    </div>
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0d5c2e,#1a8a47);display:flex;align-items:center;justify-content:center;font-size:1rem">
                        🎓
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:.38rem;font-family:monospace;color:#9ca3af">{{ $certificate->certificate_number }}</div>
                        <div style="font-size:.38rem;color:#6b7280">{{ $certificate->issued_at->translatedFormat('d F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lien de vérification --}}
        <div class="glass p-5 anim d2">
            <h3 class="text-sm font-bold text-white mb-3" style="font-family:'Playfair Display',serif">
                🔗 Lien de vérification publique
            </h3>
            <div class="flex items-center gap-3" x-data="{ copied: false }">
                <div class="flex-1 min-w-0 px-4 py-3 rounded-xl font-mono text-xs truncate"
                     style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.55)">
                    {{ route('certificates.verify', $certificate->certificate_number) }}
                </div>
                <button @click="navigator.clipboard.writeText('{{ route('certificates.verify', $certificate->certificate_number) }}');copied=true;setTimeout(()=>copied=false,2000)"
                        class="shrink-0 px-4 py-3 rounded-xl text-xs font-semibold transition-all"
                        :style="copied ? 'background:rgba(37,194,110,0.12);color:#25c26e;border:1px solid rgba(37,194,110,0.2)' : 'background:rgba(232,184,75,0.1);color:#e8b84b;border:1px solid rgba(232,184,75,0.2)'"
                        x-text="copied ? '✓ Copié !' : '📋 Copier'">
                </button>
            </div>
            <p class="text-xs mt-3" style="color:rgba(255,255,255,0.3)">
                Ce lien permet à n'importe qui de vérifier l'authenticité de ce certificat.
            </p>
        </div>
    </div>

    {{-- ── SIDEBAR INFOS (2 colonnes) ── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Statut --}}
        <div class="glass p-5 anim d2" style="border-color:rgba(232,184,75,0.15)">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
                     style="background:rgba(232,184,75,0.1);border:1px solid rgba(232,184,75,0.2)">🏆</div>
                <div>
                    <div class="text-sm font-bold text-white">Certificat valide</div>
                    <div class="text-xs" style="color:rgba(37,194,110,0.8)">✓ Authentique et vérifiable</div>
                </div>
            </div>
            <div class="text-center py-2">
                <div class="font-mono text-lg font-bold" style="color:#e8b84b;letter-spacing:.05rem">
                    {{ $certificate->certificate_number }}
                </div>
                <div class="text-xs mt-1" style="color:rgba(255,255,255,0.3)">Numéro unique</div>
            </div>
        </div>

        {{-- Infos bénéficiaire --}}
        <div class="glass p-5 anim d2">
            <h3 class="text-sm font-bold text-white mb-4" style="font-family:'Playfair Display',serif">
                👤 Bénéficiaire
            </h3>
            @php $ac = ['#1a8a47','#7a3b1e','#1a3a6c','#5b21b6','#065f46']; @endphp
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-white/5">
                <div class="w-11 h-11 rounded-full flex items-center justify-center font-bold text-sm text-white shrink-0"
                     style="background:{{ $ac[$certificate->user_id % count($ac)] }}">
                    {{ $certificate->user->initials }}
                </div>
                <div>
                    <div class="text-sm font-bold text-white">{{ $certificate->user->full_name }}</div>
                    <div class="text-xs" style="color:rgba(255,255,255,0.4)">{{ $certificate->user->email }}</div>
                </div>
            </div>
            <div>
                @foreach([
                    ['Pays',     ($certificate->user->country ?? '—')],
                    ['Inscrit',  $certificate->user->created_at->translatedFormat('d M Y')],
                    ['Certificats', $certificate->user->certificates->count().' au total'],
                ] as [$label, $value])
                <div class="info-row">
                    <span class="info-label">{{ $label }}</span>
                    <span class="info-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('admin.users.show', $certificate->user) }}"
               class="mt-4 flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-xs font-semibold transition-all hover:-translate-y-0.5"
               style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5);border:1px solid rgba(255,255,255,0.08)">
                Voir le profil complet →
            </a>
        </div>

        {{-- Infos cours --}}
        <div class="glass p-5 anim d3">
            <h3 class="text-sm font-bold text-white mb-4" style="font-family:'Playfair Display',serif">
                📚 Cours
            </h3>
            <div>
                @foreach([
                    ['Titre',        Str::limit($certificate->course->title, 35)],
                    ['Formateur',    $certificate->course->teacher->full_name],
                    ['Catégorie',    $certificate->course->category ?? '—'],
                    ['Niveau',       ['beginner'=>'Débutant','intermediate'=>'Intermédiaire','advanced'=>'Avancé'][$certificate->course->level] ?? '—'],
                    ['Leçons',       $certificate->course->total_lessons.' leçons'],
                    ['Délivré le',   $certificate->issued_at->translatedFormat('d F Y à H:i')],
                ] as [$label, $value])
                <div class="info-row">
                    <span class="info-label">{{ $label }}</span>
                    <span class="info-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stats du cours --}}
        <div class="grid grid-cols-2 gap-3 anim d4">
            <div class="stat-box">
                <div class="text-xl mb-1">👥</div>
                <div class="text-lg font-bold" style="font-family:'Playfair Display',serif;color:#3b82f6">
                    {{ $certificate->course->enrollments_count }}
                </div>
                <div class="text-[10px]" style="color:rgba(255,255,255,0.35)">Inscrits</div>
            </div>
            <div class="stat-box">
                <div class="text-xl mb-1">🏆</div>
                <div class="text-lg font-bold" style="font-family:'Playfair Display',serif;color:#e8b84b">
                    {{ $certificate->course->certificates->count() }}
                </div>
                <div class="text-[10px]" style="color:rgba(255,255,255,0.35)">Certifiés</div>
            </div>
        </div>

        {{-- Zone danger --}}
        <div class="glass p-5 anim d4" style="border-color:rgba(239,68,68,0.15)">
            <h3 class="text-sm font-bold mb-3" style="color:#f87171">⚠ Zone dangereuse</h3>
            <p class="text-xs mb-4" style="color:rgba(255,255,255,0.35)">
                La révocation supprime définitivement ce certificat. Le lien de vérification
                deviendra invalide immédiatement.
            </p>
            <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}"
                  onsubmit="return confirm('Révoquer définitivement le certificat {{ $certificate->certificate_number }} de {{ $certificate->user->full_name }} ?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2"
                        style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2)">
                    🗑 Révoquer ce certificat
                </button>
            </form>
        </div>
    </div>
</div>

@endsection