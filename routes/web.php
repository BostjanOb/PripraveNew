<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;

Route::get('/', IndexController::class)->name('home');
Route::get('/pomoc', FaqController::class)->name('help');
Route::view('/kontakt', 'pages.contact')->name('contact');

Route::get('/gradivo/{document:slug}', [DocumentController::class, 'show'])->name('document.show');
Route::get('/profil/{user:slug}', [ProfileController::class, 'show'])->name('profile.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profil/uredi', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::get('/gradivo/{document:slug}/prenesi/{file}', [DocumentController::class, 'downloadFile'])->name('document.download.file');
    Route::get('/gradivo/{document:slug}/prenesi-zip', [DocumentController::class, 'downloadZip'])->name('document.download.zip');
    Route::delete('/gradivo/{document:slug}', [DocumentController::class, 'destroy'])->name('document.destroy');
});

Route::middleware('guest')
    ->group(function () {
        Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
            ->whereIn('provider', ['google', 'facebook'])
            ->name('social.redirect');
        Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
            ->whereIn('provider', ['google', 'facebook'])
            ->name('social.callback');

        // Login
        Route::get('/prijava', [AuthenticatedSessionController::class, 'create'])
            ->middleware('guest')
            ->name('login');
        Route::post('/prijava', [AuthenticatedSessionController::class, 'store'])
            ->middleware('throttle:login');

        // Registration
        Route::get('/prijava/registracija', [RegisteredUserController::class, 'create'])
            ->name('register');
        Route::post('/prijava/registracija', [RegisteredUserController::class, 'store']);

        // Password reset
        Route::get('/prijava/pozabljeno-geslo', [PasswordResetLinkController::class, 'create'])
            ->name('password.request');
        Route::post('/prijava/pozabljeno-geslo', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');
        Route::get('/prijava/ponastavi-geslo/{token}', [NewPasswordController::class, 'create'])
            ->name('password.reset');
        Route::post('/prijava/ponastavi-geslo', [NewPasswordController::class, 'store'])
            ->name('password.update');
    });

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/odjava', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Email verification
    Route::get('/prijava/potrdi-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('/prijava/potrdi-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/prijava/potrdi-email/ponastavi', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    // Confirm password (required for 2FA)
    Route::get('/prijava/potrdi-geslo', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('/prijava/potrdi-geslo', [ConfirmablePasswordController::class, 'store']);
});
