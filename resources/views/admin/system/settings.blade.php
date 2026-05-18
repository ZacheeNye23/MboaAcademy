@extends('admin.layouts.app')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres')
@section('page-subtitle', 'Configuration de la plateforme')

@push('styles')
<style>
    .settings-nav { display:flex;gap:4px;flex-wrap:wrap;margin-bottom:28px; }
    .s-tab {
        padding:8px 18px; border-radius:12px;
        font-size:.8rem; font-weight:600; cursor:pointer;
        transition:all .2s; text-decoration:none; border:none;
        font-family:'Outfit',sans-serif;
    }
    .s-tab.on  { background:#e8b84b; color:#0a1a0f; }
    .s-tab.off { background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.45);border:1px solid rgba(255,255,255,0.08); }
    .s-tab.off:hover { border-color:rgba(232,184,75,0.3);color:#e8b84b; }
    .form-label { display:block;font-size:.7rem;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:.07rem;margin-bottom:7px; }
    .form-input { width:100%;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);border-radius:12px;padding:10px 15px;font-family:'Outfit',sans-serif;font-size:.875rem;color:#fff;outline:none;transition:all .2s; }
    .form-input:focus { border-color:rgba(232,184,75,0.4);background:rgba(255,255,255,0.06); }
    .form-input.error { border-color:rgba(239,68,68,0.5); }
    .form-input option { background:#0d1f10; }
    textarea.form-input { min-height:100px;resize:vertical;line-height:1.7; }
    .section-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:18px;overflow:hidden;margin-bottom:20px; }
    .section-header { padding:18px 24px;border-bottom:1px solid rgba(255,255,255,0.05);display:flex;align-items:center;gap:12px; }
    .section-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
    .section-body { padding:24px; }
    .btn-save { display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border-radius:12px;font-family:'Outfit',sans-serif;font-size:.875rem;font-weight:700;color:#0a1a0f;background:linear-gradient(135deg,#e8b84b,#f0d070);border:none;cursor:pointer;transition:all .2s; }
    .btn-save:hover { transform:translateY(-1px);box-shadow:0 5px 16px rgba(232,184,75,0.3); }
    .toggle-wrap { display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.05); }
    .toggle-wrap:last-child { border-bottom:none; }
    .toggle { position:relative;width:44px;height:24px;flex-shrink:0; }
    .toggle input { opacity:0;width:0;height:0;position:absolute; }
    .toggle-slider { position:absolute;inset:0;border-radius:24px;background:rgba(255,255,255,0.1);cursor:pointer;transition:.3s; }
    .toggle-slider::before { content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:#fff;left:3px;top:3px;transition:.3s; }
    .toggle input:checked + .toggle-slider { background:#e8b84b; }
    .toggle input:checked + .toggle-slider::before { transform:translateX(20px); }
    [x-cloak] { display:none!important; }
</style>
@endpush

@section('content')

{{-- Onglets --}}
<div class="settings-nav anim d1" x-data="{ tab: window.location.hash.replace('#','') || 'platform' }">
    @foreach(['platform'=>'🏢 Plateforme','mail'=>'📧 Email SMTP','commission'=>'💰 Commission','social'=>'🔗 Réseaux sociaux','maintenance'=>'🔧 Maintenance'] as $key=>$label)
    <button class="s-tab" :class="tab==='{{ $key }}' ? 'on' : 'off'"
            @click="tab='{{ $key }}'">{{ $label }}</button>
    @endforeach

    {{-- ══ PLATEFORME ══ --}}
    <div x-show="tab==='platform'" class="w-full mt-2" x-cloak>
        <div class="section-card anim d2">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(232,184,75,0.1)">🏢</div>
                <div>
                    <h3 class="text-sm font-bold text-white">Informations de la plateforme</h3>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">Nom, description, logo, contact</p>
                </div>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('admin.settings.platform') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="form-label">Nom de la plateforme *</label>
                            <input type="text" name="platform_name" value="{{ old('platform_name', $settings['platform_name']) }}"
                                   class="form-input @error('platform_name') error @enderror" required>
                            @error('platform_name')<p class="text-xs mt-1 text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Email de contact *</label>
                            <input type="email" name="platform_email" value="{{ old('platform_email', $settings['platform_email']) }}"
                                   class="form-input @error('platform_email') error @enderror" required>
                        </div>
                        <div>
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="platform_phone" value="{{ old('platform_phone', $settings['platform_phone']) }}"
                                   class="form-input" placeholder="+237 6XX XXX XXX">
                        </div>
                        <div>
                            <label class="form-label">Logo (JPG, PNG · max 2Mo)</label>
                            <input type="file" name="platform_logo" accept="image/*"
                                   class="form-input" style="padding:8px 12px">
                            @if($settings['platform_logo'])
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ asset('storage/'.$settings['platform_logo']) }}"
                                     class="w-10 h-10 rounded-lg object-cover">
                                <span class="text-xs" style="color:rgba(255,255,255,0.4)">Logo actuel</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Description</label>
                        <textarea name="platform_description" class="form-input">{{ old('platform_description', $settings['platform_description']) }}</textarea>
                    </div>
                    <button type="submit" class="btn-save">💾 Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ══ EMAIL SMTP ══ --}}
    <div x-show="tab==='mail'" class="w-full mt-2" x-cloak>
        <div class="section-card anim d2">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(59,130,246,0.1)">📧</div>
                <div>
                    <h3 class="text-sm font-bold text-white">Configuration SMTP</h3>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">Serveur d'envoi des emails</p>
                </div>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('admin.settings.mail') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="form-label">Hôte SMTP *</label>
                            <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}"
                                   class="form-input" placeholder="smtp.gmail.com" required>
                        </div>
                        <div>
                            <label class="form-label">Port *</label>
                            <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}"
                                   class="form-input" placeholder="587" required>
                        </div>
                        <div>
                            <label class="form-label">Nom d'utilisateur *</label>
                            <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}"
                                   class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">Mot de passe <span style="color:rgba(255,255,255,0.3)">(laisser vide pour conserver)</span></label>
                            <input type="password" name="mail_password" class="form-input" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="form-label">Adresse expéditeur *</label>
                            <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                                   class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">Nom expéditeur *</label>
                            <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                                   class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">Chiffrement</label>
                            <select name="mail_encryption" class="form-input">
                                @foreach(['tls'=>'TLS (recommandé)','ssl'=>'SSL','none'=>'Aucun'] as $v=>$l)
                                <option value="{{ $v }}" {{ $settings['mail_encryption']===$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="btn-save">💾 Enregistrer</button>
                        <a href="{{ route('admin.settings.mail') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
                           style="background:rgba(59,130,246,0.1);color:#60a5fa">
                            📨 Tester l'envoi
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══ COMMISSION ══ --}}
    <div x-show="tab==='commission'" class="w-full mt-2" x-cloak>
        <div class="section-card anim d2">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(37,194,110,0.1)">💰</div>
                <div>
                    <h3 class="text-sm font-bold text-white">Frais de commission</h3>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">Répartition des revenus entre formateurs et plateforme</p>
                </div>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('admin.settings.commission') }}"
                      x-data="{ teacher: {{ $settings['teacher_commission'] }} }">
                    @csrf

                    {{-- Visualisation --}}
                    <div class="mb-6 p-5 rounded-2xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06)">
                        <div class="flex justify-between text-xs mb-2" style="color:rgba(255,255,255,0.4)">
                            <span>Formateurs</span>
                            <span>Plateforme</span>
                        </div>
                        <div class="h-8 rounded-xl overflow-hidden flex">
                            <div class="h-full flex items-center justify-center text-xs font-bold transition-all"
                                 :style="'width:'+teacher+'%;background:linear-gradient(90deg,#1a8a47,#25c26e);color:#fff'"
                                 x-text="teacher+'%'"></div>
                            <div class="h-full flex items-center justify-center text-xs font-bold flex-1 transition-all"
                                 style="background:linear-gradient(90deg,#e8b84b,#f0d070);color:#0a1a0f"
                                 x-text="(100-teacher)+'%'"></div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-sm font-bold" style="color:#25c26e" x-text="teacher+'% formateurs'"></span>
                            <span class="text-sm font-bold" style="color:#e8b84b" x-text="(100-teacher)+'% plateforme'"></span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label">Part des formateurs (%)</label>
                        <input type="range" name="teacher_commission" min="50" max="95" step="5"
                               x-model="teacher" class="w-full mb-3"
                               style="accent-color:#e8b84b">
                        <input type="number" x-model="teacher" name="teacher_commission_display"
                               min="50" max="95"
                               class="form-input" style="width:100px" readonly>
                    </div>

                    <div class="p-4 rounded-xl mb-5" style="background:rgba(232,184,75,0.06);border:1px solid rgba(232,184,75,0.15)">
                        <p class="text-xs" style="color:rgba(255,255,255,0.55)">
                            ⚠️ Ce changement s'appliquera aux <strong style="color:#e8b84b">prochains paiements uniquement</strong>.
                            Les transactions existantes ne sont pas affectées.
                        </p>
                    </div>

                    <button type="submit" class="btn-save">💾 Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ══ RÉSEAUX SOCIAUX ══ --}}
    <div x-show="tab==='social'" class="w-full mt-2" x-cloak>
        <div class="section-card anim d2">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(96,165,250,0.1)">🔗</div>
                <div>
                    <h3 class="text-sm font-bold text-white">Réseaux sociaux</h3>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">Liens affichés sur la plateforme</p>
                </div>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('admin.settings.social') }}">
                    @csrf
                    <div class="space-y-4 mb-6">
                        @foreach([
                            ['social_facebook',  '🔵 Facebook',  'https://facebook.com/...'],
                            ['social_twitter',   '🐦 Twitter/X', 'https://twitter.com/...'],
                            ['social_instagram', '📸 Instagram', 'https://instagram.com/...'],
                            ['social_linkedin',  '💼 LinkedIn',  'https://linkedin.com/...'],
                            ['social_youtube',   '▶️ YouTube',   'https://youtube.com/...'],
                        ] as [$name, $label, $placeholder])
                        <div>
                            <label class="form-label">{{ $label }}</label>
                            <input type="url" name="{{ $name }}"
                                   value="{{ old($name, $settings[$name]) }}"
                                   class="form-input" placeholder="{{ $placeholder }}">
                        </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn-save">💾 Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ══ MAINTENANCE ══ --}}
    <div x-show="tab==='maintenance'" class="w-full mt-2" x-cloak>
        <div class="section-card anim d2"
             style="{{ $settings['maintenance_mode'] ? 'border-color:rgba(239,68,68,0.25)' : '' }}">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(239,68,68,0.1)">🔧</div>
                <div>
                    <h3 class="text-sm font-bold text-white">Mode maintenance</h3>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35)">
                        @if($settings['maintenance_mode'])
                            <span style="color:#f87171">⚠️ Site actuellement en maintenance</span>
                        @else
                            Site en ligne · Aucune maintenance active
                        @endif
                    </p>
                </div>
                <div class="ml-auto">
                    <span class="pill {{ $settings['maintenance_mode'] ? 'pill-red' : 'pill-green' }}">
                        {{ $settings['maintenance_mode'] ? '🔴 Hors ligne' : '🟢 En ligne' }}
                    </span>
                </div>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('admin.settings.maintenance') }}">
                    @csrf
                    @if(!$settings['maintenance_mode'])
                    <div class="mb-5">
                        <label class="form-label">Message de maintenance</label>
                        <textarea name="maintenance_message" class="form-input"
                                  placeholder="Ex: Site en maintenance pour amélioration. Retour prévu dans 1h.">{{ $settings['maintenance_message'] }}</textarea>
                    </div>
                    @endif

                    <div class="p-4 rounded-xl mb-5"
                         style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.15)">
                        <p class="text-xs" style="color:rgba(255,255,255,0.55)">
                            ⚠️ En activant la maintenance, <strong style="color:#f87171">tous les utilisateurs</strong>
                            (sauf les admins) verront un message d'indisponibilité.
                        </p>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold transition-all hover:-translate-y-0.5 border-none cursor-pointer"
                            style="{{ $settings['maintenance_mode']
                                ? 'background:rgba(37,194,110,0.15);color:#25c26e;border:1px solid rgba(37,194,110,0.25)'
                                : 'background:rgba(239,68,68,0.12);color:#f87171;border:1px solid rgba(239,68,68,0.25)' }}"
                            onclick="return confirm('{{ $settings['maintenance_mode'] ? 'Remettre le site en ligne ?' : 'Activer le mode maintenance ?' }}')">
                        {{ $settings['maintenance_mode'] ? '✅ Remettre en ligne' : '🔧 Activer la maintenance' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection