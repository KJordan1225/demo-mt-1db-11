<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
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
use App\Http\Controllers\Central\CreatorOnboardingController;
use App\Http\Controllers\Tenant\UserSubscriptionController;
use App\Models\Post;
use App\Models\Tenant;



// ----- Landlord (central) -----
Route::get('/', fn () => view('welcome'))->name('home');

// Breeze landlord auth (default names: login, register, etc.)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

Route::get('/onboarding', function() {
    return view('central.onboarding');
    })->name('central.dashboard');


Route::middleware(['auth', 'no.self.sub'])->group(function () {
    // 1. Create Stripe Account and get Account Link
    Route::get('/stripe/connect/create', [CreatorOnboardingController::class, 'createStripeAccount'])
        ->name('stripe.connect.create');
    // 2. Stripe Redirect (where the creator returns after setup)
    Route::get('/stripe/connect/return', [CreatorOnboardingController::class, 'handleOauthRedirect'])
        ->name('stripe.connect.return');    
    // 3. Stripe Redirect Refresh (if link expired)
    Route::get('/stripe/connect/refresh', [CreatorOnboardingController::class, 'handleOauthRefresh'])
        ->name('stripe.connect.refresh');
});

// Central route – no tenant initialization
// Route::middleware(['web', 'universal']) // alias for PreventAccessFromCentralDomains
//     ->get('/onboarding', function() {
//     return view('central.onboarding');
//     })->name('central.dashboard'); // Used as the return target
                
// Route::domain(env('APP_URL'))->group(function () {
    
    
// });

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

Route::pattern('tenant', '[A-Za-z0-9_-]+');

// ----- Tenant -----
Route::prefix('{tenant}')
    ->middleware(['web', 'tenant', 'tenant.defaults'])
    ->group(function () {
        // Tenant-auth routes (prefixed names to avoid clashes with landlord)
        if (file_exists(__DIR__.'/tenant_auth.php')) {
            require __DIR__.'/tenant_auth.php';
        }  
        
        // Bind {post} so it must belong to the active tenant; supports ID or slug
        Route::bind('post', function ($value) {
            return Post::query()
                ->where('tenant_id', tenant('id'))
                ->when(is_numeric($value),
                    fn ($q) => $q->whereKey($value),
                    fn ($q) => $q->where('slug', $value) // remove if you don't use slugs
                )
                ->firstOrFail();
        });        

        Route::get('/clearMediaCollections',[PostController::class, 'clearMediaCollections'])
            ->name('tenant.posts.clearMediaCollections');       

        Route::get('/postsIndex', [PostController::class, 'index'])
            ->name('tenant.posts.index');
        Route::get('/postImageUpload', [PostController::class, 'create'])
            ->name('tenant.post.image.upload');
        Route::post('/postImageUpload', [PostController::class, 'store'])
            ->name('tenant.posts.store');
        // routes/web.php (inside your {tenant} + web + tenant middleware group)
        Route::get('/', [PostController::class, 'showCarousel'])
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
            
        // Checkout initiation route (POST handles actual Stripe API call)
        Route::post('/subscribe', [UserSubscriptionController::class, 'checkout'])
        ->name('subscription.checkout');
        
        // Success/Cancel pages
        Route::get('/subscribe/success', [UserSubscriptionController::class, 'success'])
        ->name('subscription.success');
        Route::get('/subscribe/cancel', [UserSubscriptionController::class, 'cancel'])
        ->name('subscription.cancel');

    // The landing page for the micro-site
    // Route::get('/', function () {
    //     return view('tenant.subscribe'); 
    // })->name('tenant.microsite');        

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





