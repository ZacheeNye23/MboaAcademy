<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat — {{ $certificate->course->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Outfit:wght@300;400;500;600&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Outfit', sans-serif;
            background: #1a1a2e;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        /* ── Barre d'actions ── */
        .action-bar {
            width: 100%; max-width: 860px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }
        .action-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 12px;
            font-size: .85rem; font-weight: 600;
            text-decoration: none; cursor: pointer;
            transition: all .2s; border: none;
        }
        .btn-back    { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.7); }
        .btn-back:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .btn-download { background: linear-gradient(135deg,#e8b84b,#f0d070); color: #0a1a0f; box-shadow: 0 4px 14px rgba(232,184,75,0.35); }
        .btn-download:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(232,184,75,0.5); }
        .btn-print { background: rgba(37,194,110,0.15); color: #25c26e; border: 1px solid rgba(37,194,110,0.25); }
        .btn-print:hover { background: rgba(37,194,110,0.25); }

        /* ── Le certificat ── */
        .certificate {
            width: 100%; max-width: 860px;
            background: #faf6ef;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
            aspect-ratio: 1.414; /* A4 landscape */
        }

        /* ── Bordure décorative ── */
        .cert-border-outer {
            position: absolute; inset: 16px;
            border: 2px solid rgba(232,184,75,0.5);
            border-radius: 2px;
            pointer-events: none; z-index: 2;
        }
        .cert-border-inner {
            position: absolute; inset: 22px;
            border: 1px solid rgba(232,184,75,0.25);
            border-radius: 1px;
            pointer-events: none; z-index: 2;
        }

        /* ── Ornements coins ── */
        .corner {
            position: absolute; width: 40px; height: 40px;
            z-index: 3;
        }
        .corner-tl { top: 14px; left: 14px; }
        .corner-tr { top: 14px; right: 14px; transform: scaleX(-1); }
        .corner-bl { bottom: 14px; left: 14px; transform: scaleY(-1); }
        .corner-br { bottom: 14px; right: 14px; transform: scale(-1,-1); }

        /* ── Fond décoratif ── */
        .cert-bg {
            position: absolute; inset: 0; z-index: 0;
            background-image:
                radial-gradient(ellipse 60% 40% at 50% 50%, rgba(232,184,75,0.04) 0%, transparent 70%),
                radial-gradient(circle at 10% 10%, rgba(13,92,46,0.04) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(13,92,46,0.04) 0%, transparent 40%),
                repeating-linear-gradient(0deg, transparent, transparent 49px, rgba(232,184,75,0.04) 50px),
                repeating-linear-gradient(90deg, transparent, transparent 49px, rgba(232,184,75,0.04) 50px);
        }

        /* ── Contenu central ── */
        .cert-content {
            position: relative; z-index: 4;
            width: 100%; height: 100%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 40px 60px; text-align: center;
        }

        /* ── Logo & En-tête ── */
        .cert-logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem; font-weight: 900;
            color: #0d5c2e; letter-spacing: -.3px;
            margin-bottom: 4px;
        }
        .cert-logo span { color: #e8b84b; }
        .cert-subtitle {
            font-size: .6rem; font-weight: 700; letter-spacing: .15rem;
            text-transform: uppercase; color: rgba(13,92,46,0.5);
            margin-bottom: 16px;
        }

        /* ── Séparateur doré ── */
        .cert-divider {
            display: flex; align-items: center; gap: 10px;
            width: 100%; margin-bottom: 14px;
        }
        .cert-divider-line { flex: 1; height: 1px; background: linear-gradient(90deg, transparent, rgba(232,184,75,0.5), transparent); }
        .cert-divider-star { color: #e8b84b; font-size: .8rem; }

        /* ── Titre principal ── */
        .cert-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem; font-weight: 900;
            color: #0d5c2e; letter-spacing: -.5px;
            line-height: 1.1; margin-bottom: 10px;
            text-transform: uppercase;
        }
        .cert-presented {
            font-size: .65rem; color: #9ca3af; letter-spacing: .12rem;
            text-transform: uppercase; margin-bottom: 6px;
        }

        /* ── Nom du bénéficiaire ── */
        .cert-recipient {
            font-family: 'Dancing Script', cursive;
            font-size: 2.4rem; font-weight: 700;
            color: #0a1a0f; margin-bottom: 10px;
            line-height: 1.1;
        }

        /* ── Cours ── */
        .cert-course-label {
            font-size: .62rem; color: #9ca3af; letter-spacing: .12rem;
            text-transform: uppercase; margin-bottom: 4px;
        }
        .cert-course-name {
            font-family: 'Playfair Display', serif;
            font-size: 1rem; font-weight: 700; color: #1f2937;
            line-height: 1.2;
        }

        /* ── Footer certificat ── */
        .cert-footer {
            position: absolute; bottom: 36px; left: 60px; right: 60px;
            display: flex; align-items: flex-end; justify-content: space-between;
            z-index: 4;
        }
        .cert-signature { text-align: center; }
        .cert-sig-line { width: 100px; height: 1px; background: rgba(0,0,0,0.2); margin: 4px auto; }
        .cert-sig-name { font-size: .6rem; color: #6b7280; letter-spacing: .08rem; text-transform: uppercase; }

        .cert-seal-big {
            width: 64px; height: 64px; border-radius: 50%;
            background: linear-gradient(135deg, #0d5c2e, #1a8a47);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            box-shadow: 0 0 0 3px rgba(13,92,46,0.15), 0 0 0 6px rgba(13,92,46,0.07);
        }
        .cert-seal-big span { font-size: 1.6rem; }

        .cert-info-block { text-align: right; }
        .cert-number { font-family: monospace; font-size: .6rem; color: #9ca3af; }
        .cert-date { font-size: .65rem; color: #6b7280; font-weight: 600; margin-top: 2px; }

        /* ── Vérification ── */
        .cert-verify {
            font-size: .55rem; color: #9ca3af; letter-spacing: .05rem;
            text-align: center; margin-top: 10px;
        }
        .cert-verify a { color: #1a8a47; text-decoration: none; font-weight: 700; }

        /* ── Print ── */
        @media print {
            body { background: #fff; padding: 0; }
            .action-bar { display: none; }
            .certificate { max-width: 100%; box-shadow: none; border-radius: 0; }
        }

        /* ── Responsive ── */
        @media (max-width: 600px) {
            .cert-recipient { font-size: 1.8rem; }
            .cert-title { font-size: 1.1rem; }
            .cert-content { padding: 24px 32px; }
            .cert-footer { left: 32px; right: 32px; bottom: 24px; }
        }

        /* ── Animation entrée ── */
        @keyframes certReveal { from{opacity:0;transform:scale(.96) translateY(20px)} to{opacity:1;transform:scale(1) translateY(0)} }
        .certificate { animation: certReveal .7s cubic-bezier(.34,1.2,.64,1) both .2s; }
    </style>
</head>
<body>

{{-- ── BARRE D'ACTIONS ── --}}
<div class="action-bar">
    <a href="{{ route('student.certificates.index') }}" class="action-btn btn-back">
        ← Mes certificats
    </a>
    <div class="flex gap-2 flex-wrap">
        {{-- LinkedIn --}}
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('certificates.verify', $certificate->certificate_number)) }}"
           target="_blank" class="action-btn" style="background:rgba(10,102,194,0.15);color:#6ba3dc;border:1px solid rgba(10,102,194,0.2)">
            in Partager LinkedIn
        </a>
        {{-- Print --}}
        <button onclick="window.print()" class="action-btn btn-print">
            🖨 Imprimer
        </button>
        {{-- Télécharger PDF --}}
        <a href="{{ route('student.certificates.download', $certificate) }}" class="action-btn btn-download">
            ⬇ Télécharger PDF
        </a>
    </div>
</div>

{{-- ── LE CERTIFICAT ── --}}
<div class="certificate" id="certificate">
    <div class="cert-bg"></div>
    <div class="cert-border-outer"></div>
    <div class="cert-border-inner"></div>

    {{-- Coins décoratifs SVG --}}
    @foreach(['cert-corner corner-tl','cert-corner corner-tr','cert-corner corner-bl','cert-corner corner-br'] as $cls)
    <svg class="corner {{ $cls }}" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M2 2 L16 2 L2 16" stroke="#e8b84b" stroke-width="1.5" fill="none" opacity=".6"/>
        <path d="M2 2 L2 10" stroke="#e8b84b" stroke-width="1" fill="none" opacity=".3"/>
        <path d="M2 2 L10 2" stroke="#e8b84b" stroke-width="1" fill="none" opacity=".3"/>
        <circle cx="5" cy="5" r="1.5" fill="#e8b84b" opacity=".5"/>
    </svg>
    @endforeach

    {{-- Contenu --}}
    <div class="cert-content">
        {{-- Logo --}}
        <div class="cert-logo">Mboa<span>Academy</span></div>
        <div class="cert-subtitle">Plateforme e-learning africaine</div>

        {{-- Divider --}}
        <div class="cert-divider">
            <div class="cert-divider-line"></div>
            <span class="cert-divider-star">✦</span>
            <span class="cert-divider-star" style="font-size:.5rem">✦</span>
            <span class="cert-divider-star">✦</span>
            <div class="cert-divider-line"></div>
        </div>

        {{-- Titre --}}
        <div class="cert-title">Certificat de Complétion</div>

        {{-- Présenté à --}}
        <div class="cert-presented">Ce certificat est décerné à</div>

        {{-- Nom bénéficiaire --}}
        <div class="cert-recipient">{{ $certificate->user->full_name }}</div>

        {{-- Divider fin --}}
        <div class="cert-divider" style="margin-bottom:10px;opacity:.5">
            <div class="cert-divider-line"></div>
            <span class="cert-divider-star" style="font-size:.5rem">✦</span>
            <div class="cert-divider-line"></div>
        </div>

        {{-- Cours --}}
        <div class="cert-course-label">pour avoir complété avec succès</div>
        <div class="cert-course-name">{{ $certificate->course->title }}</div>

        {{-- Vérification --}}
        <div class="cert-verify" style="margin-top:12px">
            Vérifiable sur
            <a href="{{ route('certificates.verify', $certificate->certificate_number) }}">
                mboacademy.com/verify/{{ $certificate->certificate_number }}
            </a>
        </div>
    </div>

    {{-- Footer du certificat --}}
    <div class="cert-footer">
        {{-- Signature formateur --}}
        <div class="cert-signature">
            <div class="cert-sig-line"></div>
            <div class="cert-sig-name">{{ $certificate->course->teacher->full_name }}</div>
            <div class="cert-sig-name" style="opacity:.6">Formateur</div>
        </div>

        {{-- Sceau --}}
        <div class="cert-seal-big">
            <span>🎓</span>
        </div>

        {{-- Infos certificat --}}
        <div class="cert-info-block">
            <div class="cert-number">N° {{ $certificate->certificate_number }}</div>
            <div class="cert-date">
                Délivré le {{ $certificate->issued_at->translatedFormat('d F Y') }}
            </div>
        </div>
    </div>
</div>

</body>
</html>