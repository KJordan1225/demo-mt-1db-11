<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Tenant;


class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // Landlord roles
        $super = Role::firstOrCreate(['name' => 'super-admin', 'tenant_id' => null]);
        $landAdmin = Role::firstOrCreate(['name' => 'admin', 'tenant_id' => null]);

        // Landlord permission (optional)
        $landPerm = Permission::firstOrCreate(['name' => 'manage-landlord', 'tenant_id' => null]);
        $landAdmin->permissions()->syncWithoutDetaching([$landPerm->id]);

        // Give the first user super-admin (example)
        if ($owner = User::orderBy('id')->first()) {
            $owner->roles()->syncWithoutDetaching([$super->id]);
        }

        // Per-tenant roles & permissions
        Tenant::query()->each(function (Tenant $t) {
            $tid = method_exists($t, 'getTenantKey') ? $t->getTenantKey() : $t->id;

            $tAdmin = Role::firstOrCreate(['name' => 'admin', 'tenant_id' => $tid]);
            $tUser  = Role::firstOrCreate(['name' => 'user',  'tenant_id' => $tid]);

            $viewDash    = Permission::firstOrCreate(['name' => 'view-dashboard', 'tenant_id' => $tid]);
            $manageUsers = Permission::firstOrCreate(['name' => 'manage-users',  'tenant_id' => $tid]);

            $tAdmin->permissions()->syncWithoutDetaching([$viewDash->id, $manageUsers->id]);
            $tUser->permissions()->syncWithoutDetaching([$viewDash->id]);
        });
    }
}
