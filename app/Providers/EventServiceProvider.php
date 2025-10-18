<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Stancl\Tenancy\Events\TenancyInitialized;   // ← Stancl event
use App\Listeners\EnsureTenantRoles;            // ← Your listener

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Fire every time tenancy is initialized for a request/command/job
        TenancyInitialized::class => [
            EnsureTenantRoles::class,
        ],
    ];

    /**
     * If you’re on Laravel <=10 and not using auto-discovery for events,
     * leave this as-is. For Laravel 11, this provider still works fine.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Set to true if you rely on Laravel’s event discovery (optional).
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
