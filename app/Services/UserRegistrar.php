<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRegistrar
{
    /**
     * Create a user, fire Registered event, and (optionally) mark email for verification.
     */
    public function register(array $data, ?string $tenantId = null): User
    {
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password'] ?? Str::random(16)),
            // include tenant_id if you’re doing single-DB multi-tenancy
            'tenant_id' => $tenantId,
        ]);

        // Fire standard registration event (listeners may send verification email, etc.)
        event(new Registered($user));

        // If you use email verification and want to send it right now:
        // $user->sendEmailVerificationNotification();

        return $user;
    }
}
