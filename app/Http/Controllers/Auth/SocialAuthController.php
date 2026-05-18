<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserStreak;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $nameParts = explode(' ', $googleUser->getName(), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? '';

        // Nouvel utilisateur OU utilisateur existant
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'google_id'  => $googleUser->getId(),
                'avatar'     => $googleUser->getAvatar(),
                'role'       => 'student',
                'country'    => 'CM',
                'password'   => bcrypt(str()->random(24)),
            ]
        );

        // Streak uniquement pour les nouveaux
        if ($user->wasRecentlyCreated) {
            UserStreak::create([
                'user_id'        => $user->id,
                'current_streak' => 0,
                'longest_streak' => 0,
            ]);

            $message = "Bienvenue sur MboaAcademy, {$user->first_name} ! 🎉 Explorez nos formations.";
        } else {
            // Utilisateur existant → connexion
            $message = "Bon retour, {$user->first_name} ! 👋";
        }

        Auth::login($user);

        // Même redirection par rôle que vos autres contrôleurs
        return match ($user->role) {
            'teacher' => redirect()->route('teacher.dashboard')->with('success', $message),
            'admin'   => redirect()->route('admin.dashboard')->with('success', $message),
            default   => redirect()->route('student.dashboard')->with('success', $message),
        };
    }
}