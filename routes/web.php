<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\LandlordPlansController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\LandlordDashboardController;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Tenant\SubscriptionManageController;


// ----- Landlord (central) -----
Route::get('/', fn () => view('welcome'))->name('home');

// Breeze landlord auth (default names: login, register, etc.)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

// Landlord-level subscriptions management (not tenant-scoped)
Route::get('/admin/dashboard', [LandlordDashboardController::class, 'index'])
    ->middleware(['web', 'auth'])   // Add 'verified' if you require verified emails
    ->name('landlord.dashboard.index');

Route::name('guest.')->group(function () {
    Route::get('/', [GuestController::class, 'home'])->name('home');          // /guest
    Route::get('/about', [GuestController::class, 'about'])->name('about');   // /guest/about
	Route::get('/sign_up', [GuestController::class, 'sign_up'])->name('sign_up');
    Route::get('/contact', [GuestController::class, 'contact'])->name('contact'); // /guest/contact
    Route::post('/contact', [GuestController::class, 'send'])->name('contact.send');
    Route::get('/plans', [LandlordPlansController::class, 'index'])->name('plans'); // /guest/plans
    Route::get('/create-microsite', [LandlordPlansController::class, 'showCreateMicroSiteForm'])
        ->name('create.microsite'); 
    Route::post('/create-microsite', [LandlordPlansController::class, 'storeCreateMicroSiteForm'])
        ->name('store.microsite');
});



// Central landlord login page
Route::get('/landload/login', [TenantSwitchController::class, 'index'])
    ->name('landlord.login');
Route::post('/landlord/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('landlord.logout');

// Routes to creator subscription plans
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/subscribe/basic', [SubscriptionController::class, 'subscribeBasic'])
    ->name('subscribe.basic');
    Route::post('/subscribe/premium', [SubscriptionController::class, 'subscribePremium'])
        ->name('subscribe.premium');
    Route::get('/subscribe/success', [SubscriptionController::class, 'success'])
        ->name('subscribe.success');
    Route::get('/subscribe/cancel', [SubscriptionController::class, 'cancel'])
        ->name('subscribe.cancel');
});



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

        // routes/web.php (inside your {tenant} + web + tenant middleware group)
        Route::get('/', fn () => view('tenant.landing', ['tenant' => tenant('id')]))
            ->name('tenant.landing');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('tenant.dashboard'); 

        Route::get('/tenant-admin/dashboard', [DashboardController::class, 'tenantAdminDashboard'])
            ->name('tenant.admin.dashboard'); 
            
        Route::get('/subscriptions/{stripeId}', [SubscriptionManageController::class, 'show'])
            ->name('subscriptions.show');

        // Cancel at period end
        Route::post('/subscriptions/{stripeId}/cancel', [SubscriptionManageController::class, 'cancel'])
            ->name('subscriptions.cancel');

        // Cancel immediately (optional)
        Route::post('/subscriptions/{stripeId}/cancel-now', [SubscriptionManageController::class, 'cancelNow'])
            ->name('subscriptions.cancelNow');      

    });

// Landlord-level subscriptions management (not tenant-scoped)
Route::get('/admin/subscriptions', [SubscriptionManageController::class, 'indexCentral'])
    ->middleware(['web', 'auth'])   // Add 'verified' if you require verified emails
    ->name('landlord.subscriptions.index');

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








