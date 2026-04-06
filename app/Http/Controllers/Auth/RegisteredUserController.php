<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserStreak;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    // ── GET /register ───────────────────────────────────────────────────────
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRoute());
        }
        return view('auth.register');
    }

    // ── POST /register ──────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name'  => ['required', 'string', 'min:2', 'max:50'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'role'       => ['required', 'in:student,teacher'],
            'password'   => ['required', 'confirmed', Password::defaults()],
            'terms'      => ['accepted'],
        ], [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.min'      => 'Le prénom doit contenir au moins 2 caractères.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.required'      => 'L\'adresse email est obligatoire.',
            'email.unique'        => 'Cette adresse email est déjà utilisée.',
            'role.required'       => 'Veuillez choisir un rôle.',
            'role.in'             => 'Rôle invalide.',
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.confirmed'  => 'Les mots de passe ne correspondent pas.',
            'terms.accepted'      => 'Vous devez accepter les conditions d\'utilisation.',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'country'    => $request->country ?? 'CM',
        ]);

        // Initialiser le streak de l'utilisateur
        UserStreak::create([
            'user_id'        => $user->id,
            'current_streak' => 0,
            'longest_streak' => 0,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // ✅ Redirection selon le rôle
        $message = "Bienvenue sur MboaAcademy, {$user->first_name} ! 🎉";

        return match ($user->role) {
            'teacher' => redirect()->route('teacher.dashboard')
                            ->with('success', $message . ' Commencez à créer vos cours.'),
            'admin'   => redirect()->route('admin.dashboard')
                            ->with('success', $message),
            default   => redirect()->route('student.dashboard')
                            ->with('success', $message . ' Explorez nos formations.'),
        };
    }
}