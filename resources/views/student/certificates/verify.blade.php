<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Certificat — MboaAcademy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Outfit', sans-serif; background: #f4f7f4; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; }
        .verify-card { background: #fff; border-radius: 24px; padding: 48px 40px; max-width: 480px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.06); }
        .logo { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 900; color: #0a1a0f; margin-bottom: 32px; }
        .logo span { color: #e8b84b; }
        .status-icon { width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px; }
        .valid   { background: rgba(37,194,110,0.1); border: 2px solid rgba(37,194,110,0.25); }
        .invalid { background: rgba(239,68,68,0.08); border: 2px solid rgba(239,68,68,0.2); }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: .875rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #9ca3af; font-weight: 500; }
        .info-value { color: #1f2937; font-weight: 600; text-align: right; }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="logo">Mboa<span>Academy</span></div>

        @if($certificate)
        {{-- ✅ Valide --}}
        <div class="status-icon valid">✅</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:900;color:#0d5c2e;margin-bottom:8px">
            Certificat Valide
        </h2>
        <p style="font-size:.875rem;color:#6b7280;margin-bottom:28px">
            Ce certificat est authentique et a été délivré par MboaAcademy.
        </p>
        <div style="background:#f9fafb;border-radius:14px;padding:16px 20px;text-align:left">
            @foreach([
                ['Titulaire',    $certificate->user->full_name],
                ['Formation',    $certificate->course->title],
                ['Formateur',    $certificate->course->teacher->full_name],
                ['Délivré le',   $certificate->issued_at->translatedFormat('d F Y')],
                ['Numéro',       $certificate->certificate_number],
            ] as [$label, $value])
            <div class="info-row">
                <span class="info-label">{{ $label }}</span>
                <span class="info-value">{{ $value }}</span>
            </div>
            @endforeach
        </div>
        @else
        {{-- ❌ Invalide --}}
        <div class="status-icon invalid">❌</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:900;color:#dc2626;margin-bottom:8px">
            Certificat Invalide
        </h2>
        <p style="font-size:.875rem;color:#6b7280;margin-bottom:24px">
            Ce numéro de certificat n'existe pas dans notre base de données.
        </p>
        @endif

        <a href="{{ route('welcome') }}"
           style="display:inline-block;margin-top:24px;padding:10px 24px;border-radius:12px;background:linear-gradient(135deg,#1a8a47,#25c26e);color:#fff;font-size:.875rem;font-weight:600;text-decoration:none">
            ← Retour à MboaAcademy
        </a>
    </div>
</body>
</html>