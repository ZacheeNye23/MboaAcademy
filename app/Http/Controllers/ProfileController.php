<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // ── Affichage profil ─────────────────────────────────
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    // ── Mise à jour profil ───────────────────────────────
    public function update(Request $request)
    {
        $user = Auth::user();

      $data = $request->validate([
    'first_name' => ['required', 'string'],
    'last_name'  => ['required', 'string'],
    'bio'        => ['nullable', 'string'],
    'github'     => ['nullable', 'url'],
    'linkedin'   => ['nullable', 'url'],
    'portfolio'  => ['nullable', 'url'],
]);

$user->update($data);

        return back()->with('success', 'Profil mis à jour ✅');
    }

    // ── Upload avatar ───────────────────────────────────
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048']
        ]);

        $user = Auth::user();

        // Supprimer ancien avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Stocker nouveau
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Photo mise à jour 📸');
    } // ── Mot de passe ──
    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Mot de passe incorrect'
            ]);
        }

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return back()->with('success', 'Mot de passe mis à jour 🔒');
    }


}