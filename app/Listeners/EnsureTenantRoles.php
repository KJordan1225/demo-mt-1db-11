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

        $view = Permission::firstOrCreate(
            ['slug'=>'view_content','scope'=>'tenant','tenant_id'=>$tenantId],
            ['name'=>'View Content']
        );
        $manage = Permission::firstOrCreate(
            ['slug'=>'manage_content','scope'=>'tenant','tenant_id'=>$tenantId],
            ['name'=>'Manage Content']
        );

        $admin->permissions()->syncWithoutDetaching([$view->id, $manage->id]);
        $user->permissions()->syncWithoutDetaching($view->id);
    }
}
