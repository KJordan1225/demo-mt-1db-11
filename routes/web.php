<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\Tenant\DashboardController;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

// ----- Landlord (central) -----
Route::get('/', fn () => view('welcome'))->name('home');

// Breeze landlord auth (default names: login, register, etc.)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Admin (landlord) routes
    Route::prefix('admin')->name('tenants.')
                            ->controller(TenantController::class)
                            ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::delete('/mass-destroy', 'massDestroy')->name('massDestroy');
        Route::get('/{tenant}/edit', 'edit')->name('edit');
        Route::any('/update/{tenant}', 'update')->name('update'); // keep 'any' as in your original
        // If you prefer RESTful verbs instead:
        // Route::match(['put','patch'], '/{tenant}', 'update')->name('update');
    });
});


// ----- Tenant -----
Route::prefix('{tenant}')
    ->middleware(['web', 'tenant', 'tenant.defaults'])
    ->group(function () {
        // Tenant-auth routes (prefixed names to avoid clashes with landlord)
        if (file_exists(__DIR__.'/tenant_auth.php')) {
            require __DIR__.'/tenant_auth.php';
        }

        // Route::get('/', fn () => redirect()->route('tenant.landing', ['tenant' => tenant('id')]))
        //     ->name('tenant.home');

        // routes/web.php (inside your {tenant} + web + tenant middleware group)
        Route::get('/', fn () => view('tenant.landing', ['tenant' => tenant('id')]))
            ->name('tenant.landing');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('tenant.dashboard');
    });

// Central landing shows tenant switcher
Route::get('/', [TenantSwitchController::class, 'index'])->name('home');

// Handle manual entry of a tenant id and redirect to /{tenant}/login
Route::post('/tenant/switch', [TenantSwitchController::class, 'switch'])
    ->name('tenant.switch');

// Landlord (central) profile.edit`
Route::middleware(['auth','verified'])
    ->get('/profile/edit', [ProfileController::class, 'edit'])
    ->name('profile.edit');

// Landlord (central)
Route::middleware(['web','ctx.tenant'])->group(function () {
    Route::get('/landlord', function () {
        abort_unless(auth()->check(), 401);
        abort_unless(auth()->user()->hasRole('admin', null) || auth()->user()->hasRole('super-admin', null), 403);
        return 'Landlord area';
    })->name('landlord.home');
});



// Route::prefix('{tenant}')
//     ->middleware(['web','tenant','ctx.tenant'])
//     ->group(function () {
//         Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
//             $tenantId = $request->attributes->get('tenantId');
//             abort_unless(auth()->check(), 401);
//             abort_unless(
//                 auth()->user()->hasRole('user', $tenantId) || auth()->user()->hasRole('admin', $tenantId),
//                 403
//             );
//             return 'Tenant dashboard: '.$tenantId;
//         })->name('tenant.dashboard');
//     });





