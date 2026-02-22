<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Pages\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profil/uredi', [ProfileController::class, 'edit'])->name('profile.edit');
});

Route::middleware('web')->group(function () {
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->middleware('guest')
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->middleware('guest')
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social.callback');

    // Login
    Route::get('/prijava', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');
    Route::post('/prijava', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login');

    // Logout
    Route::post('/odjava', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    // Registration
    Route::get('/prijava/registracija', [RegisteredUserController::class, 'create'])
        ->middleware('guest')
        ->name('register');
    Route::post('/prijava/registracija', [RegisteredUserController::class, 'store'])
        ->middleware('guest');

    // Password reset
    Route::get('/prijava/pozabljeno-geslo', [PasswordResetLinkController::class, 'create'])
        ->middleware('guest')
        ->name('password.request');
    Route::post('/prijava/pozabljeno-geslo', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');
    Route::get('/prijava/ponastavi-geslo/{token}', [NewPasswordController::class, 'create'])
        ->middleware('guest')
        ->name('password.reset');
    Route::post('/prijava/ponastavi-geslo', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.update');

    // Email verification
    Route::get('/prijava/potrdi-email', [EmailVerificationPromptController::class, '__invoke'])
        ->middleware('auth')
        ->name('verification.notice');
    Route::get('/prijava/potrdi-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['auth', 'signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/prijava/potrdi-email/ponastavi', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');

    // Confirm password (required for 2FA)
    Route::get('/prijava/potrdi-geslo', [ConfirmablePasswordController::class, 'show'])
        ->middleware('auth')
        ->name('password.confirm');
    Route::post('/prijava/potrdi-geslo', [ConfirmablePasswordController::class, 'store'])
        ->middleware('auth');
});
