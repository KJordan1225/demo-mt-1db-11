<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Stancl\Tenancy\Events\TenancyInitialized;
use App\Models\Role;
use App\Models\Permission;

class EnsureTenantRoles
{
    public function handle(TenancyInitialized $event): void
    {
        $tenantId = tenant('id');

        $admin = Role::firstOrCreate(
            ['slug'=>'admin','scope'=>'tenant','tenant_id'=>$tenantId],
            ['name'=>'Tenant Admin']
        );
        $user  = Role::firstOrCreate(
            ['slug'=>'user','scope'=>'tenant','tenant_id'=>$tenantId],
            ['name'=>'Tenant User']
        );        
    }
}
