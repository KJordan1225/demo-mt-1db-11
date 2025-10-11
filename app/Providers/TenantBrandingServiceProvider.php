<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\TenantBranding;
use Illuminate\Support\Facades\View;


class TenantBrandingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share $branding in all views (only a tiny array, safe globally)
        View::composer('*', function ($view) {
            $view->with('branding', TenantBranding::current());
        });
    }
}
