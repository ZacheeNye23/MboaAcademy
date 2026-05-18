<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    // ── Afficher les paramètres ──────────────────────────────────────────────
    public function index(): View
    {
        $settings = [
            // Infos plateforme
            'platform_name'        => config('app.name', 'MboaAcademy'),
            'platform_description' => setting('platform_description', 'La meilleure plateforme d\'apprentissage en ligne.'),
            'platform_email'       => setting('platform_email', config('mail.from.address')),
            'platform_phone'       => setting('platform_phone', ''),
            'platform_logo'        => setting('platform_logo', ''),

            // SMTP
            'mail_host'            => config('mail.mailers.smtp.host', ''),
            'mail_port'            => config('mail.mailers.smtp.port', 587),
            'mail_username'        => config('mail.mailers.smtp.username', ''),
            'mail_from_address'    => config('mail.from.address', ''),
            'mail_from_name'       => config('mail.from.name', ''),
            'mail_encryption'      => config('mail.mailers.smtp.encryption', 'tls'),

            // Commission
            'teacher_commission'   => setting('teacher_commission', 70),
            'platform_commission'  => setting('platform_commission', 30),

            // Maintenance
            'maintenance_mode'     => app()->isDownForMaintenance(),
            'maintenance_message'  => setting('maintenance_message', 'Site en maintenance. Revenez bientôt !'),

            // Réseaux sociaux
            'social_facebook'      => setting('social_facebook', ''),
            'social_twitter'       => setting('social_twitter', ''),
            'social_instagram'     => setting('social_instagram', ''),
            'social_linkedin'      => setting('social_linkedin', ''),
            'social_youtube'       => setting('social_youtube', ''),
        ];

        return view('admin.system.settings', compact('settings'));
    }

    // ── Sauvegarder infos plateforme ─────────────────────────────────────────
    public function updatePlatform(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'platform_name'        => ['required', 'string', 'max:100'],
            'platform_description' => ['nullable', 'string', 'max:500'],
            'platform_email'       => ['required', 'email'],
            'platform_phone'       => ['nullable', 'string', 'max:20'],
            'platform_logo'        => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('platform_logo')) {
            $old = setting('platform_logo');
            if ($old) Storage::disk('public')->delete($old);
            $data['platform_logo'] = $request->file('platform_logo')->store('settings', 'public');
        }

        foreach ($data as $key => $value) {
            setting([$key => $value]);
        }

        return back()->with('success', '✅ Informations de la plateforme mises à jour.');
    }

    // ── Sauvegarder SMTP ─────────────────────────────────────────────────────
    public function updateMail(Request $request): RedirectResponse
    {
        $request->validate([
            'mail_host'         => ['required', 'string'],
            'mail_port'         => ['required', 'integer'],
            'mail_username'     => ['required', 'string'],
            'mail_password'     => ['nullable', 'string'],
            'mail_from_address' => ['required', 'email'],
            'mail_from_name'    => ['required', 'string'],
            'mail_encryption'   => ['required', 'in:tls,ssl,none'],
        ]);

        // Écriture dans le .env
        $this->updateEnv([
            'MAIL_HOST'         => $request->mail_host,
            'MAIL_PORT'         => $request->mail_port,
            'MAIL_USERNAME'     => $request->mail_username,
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME'    => '"'.$request->mail_from_name.'"',
            'MAIL_ENCRYPTION'   => $request->mail_encryption,
        ]);

        if ($request->filled('mail_password')) {
            $this->updateEnv(['MAIL_PASSWORD' => $request->mail_password]);
        }

        Artisan::call('config:clear');

        return back()->with('success', '✅ Configuration email mise à jour.');
    }

    // ── Sauvegarder commission ───────────────────────────────────────────────
    public function updateCommission(Request $request): RedirectResponse
    {
        $request->validate([
            'teacher_commission' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $teacher  = (int) $request->teacher_commission;
        $platform = 100 - $teacher;

        setting(['teacher_commission'  => $teacher]);
        setting(['platform_commission' => $platform]);

        return back()->with('success', '✅ Commission mise à jour. Formateurs : '.$teacher.'% — Plateforme : '.$platform.'%');
    }

    // ── Sauvegarder réseaux sociaux ──────────────────────────────────────────
    public function updateSocial(Request $request): RedirectResponse
    {
        $request->validate([
            'social_facebook'  => ['nullable', 'url'],
            'social_twitter'   => ['nullable', 'url'],
            'social_instagram' => ['nullable', 'url'],
            'social_linkedin'  => ['nullable', 'url'],
            'social_youtube'   => ['nullable', 'url'],
        ]);

        foreach ($request->only(['social_facebook','social_twitter','social_instagram','social_linkedin','social_youtube']) as $k => $v) {
            setting([$k => $v]);
        }

        return back()->with('success', '✅ Réseaux sociaux mis à jour.');
    }

    // ── Toggle maintenance ───────────────────────────────────────────────────
    public function toggleMaintenance(Request $request): RedirectResponse
    {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
            return back()->with('success', '✅ Site remis en ligne.');
        }

        $message = $request->input('maintenance_message', 'Site en maintenance.');
        setting(['maintenance_message' => $message]);
        Artisan::call('down', ['--message' => $message, '--retry' => 60]);

        return back()->with('success', '🔧 Mode maintenance activé.');
    }

    // ── Helper : modifier le .env ────────────────────────────────────────────
    private function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if (str_contains($envContent, $key.'=')) {
                $envContent = preg_replace('/^'.$key.'=.*/m', $key.'='.$value, $envContent);
            } else {
                $envContent .= "\n".$key.'='.$value;
            }
        }

        file_put_contents($envPath, $envContent);
    }
}