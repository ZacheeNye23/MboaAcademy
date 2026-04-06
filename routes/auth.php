<?php

// routes/auth.php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Support\Facades\Route;

// ── Inscription ────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {

    Route::get('/register', [RegisteredUserController::class, 'create'])
         ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store']);

    // ── Connexion ──────────────────────────────────────────────────────────
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
         ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // ── Mot de passe oublié ────────────────────────────────────────────────
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
         ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
         ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
         ->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
         ->name('password.store');
});

// ── Déconnexion ────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');

    // Vérification email
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
         ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
         ->middleware('signed')
         ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
         ->middleware('throttle:6,1')
         ->name('verification.send');
});