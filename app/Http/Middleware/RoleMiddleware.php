<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Vérifie que l'utilisateur connecté possède l'un des rôles autorisés.
     *
     * Usage dans les routes :
     *   ->middleware('role:admin')
     *   ->middleware('role:teacher,admin')
     *   ->middleware('role:student')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Non connecté → login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();

        // Compte désactivé
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé. Contactez le support.');
        }

        // Rôle autorisé ?
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Rôle non autorisé → redirection vers son propre dashboard
        return redirect()->route($user->dashboardRoute())
            ->with('error', 'Vous n\'avez pas accès à cette section.');
    }
}