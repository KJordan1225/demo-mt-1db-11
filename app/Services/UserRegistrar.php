<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;


class UserRegistrar
{
    public function registerLandlord(array $data): User
    {
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'tenant_id' => null, // landlord/global
        ]);

        // ensure role exists
        Role::firstOrCreate(
            ['slug'=>'super_admin','scope'=>'landlord','tenant_id'=>null],
            ['name'=>'Super Admin']
        );

        if (trim($user->name) === 'Super Admin') {
            $user->assignRole('super_admin','landlord', null);
        }

        event(new Registered($user));
        
        return $user;
    }
}
