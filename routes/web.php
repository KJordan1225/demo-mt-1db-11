<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\Tenant\DashboardController;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

// ----- Landlord (central) -----
Route::get('/', fn () => view('welcome'))->name('home');

// Breeze landlord auth (default names: login, register, etc.)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

// Example landlord dashboard (optional)
Route::middleware(['auth', 'verified'])->get('/admin', function () {
    return 'Landlord dashboard';
})->name('landlord.dashboard');

// ----- Tenant -----
Route::prefix('{tenant}')
    ->middleware(['web', 'tenant', 'tenant.defaults'])
    ->group(function () {
        // Tenant-auth routes (prefixed names to avoid clashes with landlord)
        if (file_exists(__DIR__.'/tenant_auth.php')) {
            require __DIR__.'/tenant_auth.php';
        }

        Route::get('/', fn () => redirect()->route('tenant.dashboard', ['tenant' => tenant('id')]))
            ->name('tenant.home');

        Route::middleware(['auth', 'verified'])
            ->get('/dashboard', [DashboardController::class, 'index'])
            ->name('tenant.dashboard');
    });

// Central landing shows tenant switcher
Route::get('/', [TenantSwitchController::class, 'index'])->name('home');

// Handle manual entry of a tenant id and redirect to /{tenant}/login
Route::post('/tenant/switch', [TenantSwitchController::class, 'switch'])
    ->name('tenant.switch');

// Landlord (central) dashboard
Route::middleware(['auth','verified'])
    ->get('/dashboard', fn () => view('dashboard'))  // or return 'Landlord dashboard'
    ->name('dashboard');

// Landlord (central) profile.edit`
Route::middleware(['auth','verified'])
    ->get('/profile/edit', [ProfileController::class, 'edit'])
    ->name('profile.edit');



