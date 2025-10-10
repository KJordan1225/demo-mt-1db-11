<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

/*
|--------------------------------------------------------------------------
| Tenant Auth Routes (Breeze, Blade stack)
| Paths are relative to /{tenant}/... because this file is included
| inside your Route::prefix('{tenant}') group.
| All route NAMES are prefixed with "tenant." to avoid clashing with
| landlord (central) auth route names.
|--------------------------------------------------------------------------
*/

// Guest (not authenticated)
Route::middleware('guest')->group(function () {
    // Register
    Route::get('register', [RegisteredUserController::class, 'tenantCreate'])
        ->name('tenant.register');
    Route::post('register', [RegisteredUserController::class, 'tenantStore'])
        ->name('tenant.register');
        
    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('tenant.login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('tenant.login');

    // Forgot password
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('tenant.password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('tenant.password.email');

    // Reset password
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('tenant.password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('tenant.password.update');
});

// Authenticated
Route::middleware('auth')->group(function () {
    // Email verification prompt
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('tenant.verification.notice');

    // Verify email link
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('tenant.verification.verify');

    // Resend verification email
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('tenant.verification.send');

    // Confirm password
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('tenant.password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Update password (from profile)
    Route::put('password', [PasswordController::class, 'update'])
        ->name('tenant.password.update.profile');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('tenant.logout');
});

