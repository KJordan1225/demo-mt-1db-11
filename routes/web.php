<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Connect\PricingController;
use App\Http\Controllers\TenantAdminPostController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\ConfigureMicrositeController;
use App\Http\Controllers\Connect\OnboardingController;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Laravel\Cashier\Http\Controllers\WebhookController;
use App\Http\Controllers\Connect\SubscriptionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


// ----- Landlord (central) -----
// Route::get('/', fn () => view('welcome'))->name('home');

// Breeze landlord auth (default names: login, register, etc.)
if (file_exists(__DIR__.'/guestAuth.php')) {
    require __DIR__.'/guestAuth.php';
}

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::prefix('guest')->name('guest.')->group(function () {
    Route::get('/', [GuestController::class, 'home'])->name('home');          // /guest
    Route::get('/about', [GuestController::class, 'about'])->name('about');   // /guest/about
	Route::get('/sign_up', [GuestController::class, 'sign_up'])->name('sign_up');
    Route::get('/contact', [GuestController::class, 'contact'])->name('contact'); // /guest/contact
    Route::post('/contact', [GuestController::class, 'send'])->name('contact.send');
    // Show plans & start subscribe flow
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    // Show payment form for a specific plan
    Route::get('/subscribe/{plan}', [SubscriptionController::class, 'showCheckout'])->name('subscribe.show');

    // Create the subscription
    Route::post('/subscribe/{plan}', [SubscriptionController::class, 'store'])->name('subscribe.store');

    // Manage subscription
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::post('/subscription/resume', [SubscriptionController::class, 'resume'])->name('subscription.resume');

    // Account page
    Route::get('/account', [SubscriptionController::class, 'account'])->name('account');

    Route::get('/microsite/configure', [ConfigureMicrositeController::class, 'index'])->name('microsite.configure');
    Route::post('/microsite/configure', [ConfigureMicrositeController::class, 'store'])->name('microsite.configure.store');
});

Route::get('/{tenant}/posts/image/{post}', [TenantAdminPostController::class, 'showSingleImagePost'])
    ->name('post.show');
Route::get('/{tenant}/posts/{post}', [TenantAdminPostController::class, 'showSingleVideoPost'])
    ->name('tenant.posts.show-vids');

Route::post('login', [AuthenticatedSessionController::class, 'store']);

// Landlord registration route
Route::get('/register-landlord', [RegisterController::class, 'showLandlordForm'])
    ->name('register.landlord');
Route::post('/register-landlord', [RegisterController::class, 'registerLandlord'])
    ->name('register.landlord.store');
// Tenant registration route (pass tenant slug)
Route::get('/register/{tenantSlug}', [RegisterController::class, 'showTenantForm'])
    ->name('register.tenant');
Route::post('/register/{tenantSlug}', [RegisterController::class, 'registerTenant'])
    ->name('register.tenant.store');

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
    ->middleware(['tenant', 'web', 'tenant.defaults'])
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

        // Route::get('/home', fn () => view('guest.tenant.home', ['tenant' => tenant('id')]))
        //     ->name('guest.home');
        Route::get('/home', function (Request $request) {
            $tenantId = function_exists('tenant') ? tenant('id') : null;
            $user     = auth()->user();

            // Not logged in → guest landing
            if (!$user) {
                return view('guest.tenant.home', ['tenant' => $tenantId]);
            }

            // Landlord/global role (no tenant scope)
            if ($user->hasRole('super-admin', null)) {
                return view('landlord.dashboard'); // e.g. resources/views/landlord/dashboard.blade.php
            }

            // Tenant-scoped roles
            if ($tenantId && $user->hasRole('admin', $tenantId)) {
                return view('tenant.admin.dashboard', ['tenant' => $tenantId]); // e.g. resources/views/tenant/admin/dashboard.blade.php
            }

            if ($tenantId && $user->hasRole('user', $tenantId)) {
                return view('tenant.user.home', ['tenant' => $tenantId]); // e.g. resources/views/tenant/user/home.blade.php
            }

            // Fallback if role doesn’t match anything
            return view('tenant.user.home', ['tenant' => $tenantId]);
        })->name('guest.home');       

        Route::get('/admin/media', [TenantAdminPostController::class, 'create'])
            ->name('tenant.admin.media.create');

        Route::post('/admin/media', [TenantAdminPostController::class, 'store'])
            ->name('tenant.admin.media.store');

        Route::get('/admin/image/posts', [TenantAdminPostController::class, 'accessImagePosts'])
            ->name('tenant.admin.image.posts');

        Route::get('/admin/video/posts', [TenantAdminPostController::class, 'accessVideoPosts'])
            ->name('tenant.admin.video.posts');

        Route::get('/user/post-list', [TenantAdminPostController::class, 'postList'])
            ->name('tenant.user.post-list');

    });

// Creator stripe onboarding
Route::get('/{tenant}/connect/onboarding', [OnboardingController::class, 'start'])
    ->name('tenant.connect.onboarding');
    
Route::get('/{tenant}/connect/onboarding/return', [OnboardingController::class, 'return'])
    ->name('tenant.connect.return');

// Creator stripe pricing
Route::post('/{tenant}/pricing', [PricingController::class, 'store'])
    ->name('tenant.pricing.store');

Route::get('/{tenant}/pricing/subscribe', [PricingController::class, 'showSubscriptionForm'])
    ->name('tenant.pricing.subscribe');

// Creator subscription handling
Route::middleware('auth')->group(function () {
    Route::post('/{tenant}/subscribe',        [SubscriptionController::class, 'subscribe'])
        ->name('tenant.subscribe');
    Route::get('/{tenant}/subscribe/success', [SubscriptionController::class, 'success'])
        ->name('tenant.subscribe.success');
    Route::get('/{tenant}/subscribe/cancel',  [SubscriptionController::class, 'cancel'])
        ->name('tenant.subscribe.cancel');
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

Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook');   



