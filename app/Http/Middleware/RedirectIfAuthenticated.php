<?php
namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Si l'utilisateur est déjà connecté et tente d'accéder à /login ou /register,
     * on le redirige vers son dashboard selon son rôle.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // ✅ Redirection selon le rôle au lieu du HOME fixe
                return redirect()->route($user->dashboardRoute());
            }
        }

        return $next($request);
    }
}