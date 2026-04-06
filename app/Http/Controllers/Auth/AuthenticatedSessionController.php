<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    // ── GET /login ──────────────────────────────────────────────────────────
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRoute());
        }
        return view('auth.login');
    }

    // ── POST /login ─────────────────────────────────────────────────────────
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user    = Auth::user();
        $message = "Bon retour, {$user->first_name} ! 👋";

        // ✅ Redirection selon le rôle
        return match ($user->role) {
            'teacher' => redirect()->intended(route('teacher.dashboard'))
                            ->with('success', $message),
            'admin'   => redirect()->intended(route('admin.dashboard'))
                            ->with('success', $message),
            default   => redirect()->intended(route('student.dashboard'))
                            ->with('success', $message),
        };
    }

    // ── POST /logout ────────────────────────────────────────────────────────
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}