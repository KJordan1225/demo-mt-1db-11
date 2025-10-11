<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        // Generic gate for DB permission checks
        Gate::define('perm', function ($user, string $permissionName, ?string $tenantId = null) {
            return $user->canPermission($permissionName, $tenantId);
        });

        // Optional named examples:
        Gate::define('manage-users', fn($user, ?string $tenantId = null) =>
            $user->canPermission('manage-users', $tenantId)
        );
    }
}
