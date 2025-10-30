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
        $super = Role::firstOrCreate(['name' => 'super-admin', 'slug' => 'super-admin','tenant_id' => null]);
        $landAdmin = Role::firstOrCreate(['name' => 'admin', 'slug' => 'admin', 'tenant_id' => null]);


        // Give the first user super-admin (example)
        if ($owner = User::orderBy('id')->first()) {
            $owner->roles()->syncWithoutDetaching([$super->id]);
        }

        // Per-tenant roles & permissions
        Tenant::query()->each(function (Tenant $t) {
            $tid = method_exists($t, 'getTenantKey') ? $t->getTenantKey() : $t->id;

            $tAdmin = Role::firstOrCreate(['name' => 'admin', 'slug' => 'admin', 'tenant_id' => $tid]);
            $tUser  = Role::firstOrCreate(['name' => 'user',  'slug' => 'user',  'tenant_id' => $tid]);

        });
    }
}
