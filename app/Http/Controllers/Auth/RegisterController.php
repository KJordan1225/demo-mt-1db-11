<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    public function showLandlordForm()
    {
        return view('auth.register-landlord');
    }

    public function showTenantForm()
    {
        return view('auth.register-tenant');
    }
    
    // For registering the landlord account (tenant_id = null)
    public function registerLandlord(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Create landlord user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => null, // Landlord level, no tenant association
        ]);

        // ensure role exists
        Role::firstOrCreate(
            ['slug'=>'super_admin','scope'=>'landlord','tenant_id'=>null],
            ['name'=>'Super Admin']
        );
        
        if (trim($user->name) === 'Super Admin') {
            $user->assignRole('super_admin','landlord', null);
        } else {
            $user->assignRole('admin','landlord', null);
        };

        // event(new Registered($user));
        Auth::login($user);
        
        return redirect()->route('guest.home'); // Or wherever you want to redirect
    }

    // For registering a tenant account (tenant_id = tenant's ID)
    public function registerTenant(Request $request, $tenantSlug)
    {
        $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Ensure that email is unique per tenant
        if (!User::emailIsUniqueForTenant($request->email, $tenant->id)) {
            return back()->withErrors(['email' => 'This email is already registered for this tenant.']);
        }

        // Create tenant user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenant->id, // Tenant ID is set
        ]);

        return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug]); // Redirect to tenant dashboard
    }
}
