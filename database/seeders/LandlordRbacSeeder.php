<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;


class LandlordRbacSeeder extends Seeder
{
    public function run(): void
    {
        $super = Role::firstOrCreate(
            ['slug'=>'super_admin','scope'=>'landlord','tenant_id'=>null],
            ['name'=>'Super Admin']
        );
        $admin = Role::firstOrCreate(
            ['slug'=>'admin','scope'=>'landlord','tenant_id'=>null],
            ['name'=>'Admin']
        );        
    }
}
