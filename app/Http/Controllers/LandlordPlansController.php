<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LandlordPlansController extends Controller
{
    public function index()
    {
        return view('guest.plans');
    }

    public function showCreateMicroSiteForm()
    {
        return view('landlord.microsite.create');
    }

    public function storeCreateMicroSiteForm(Request $request)
    {
        $data = $request->validate([
            'id'             => ['required', 'string', 'max:191', 'unique:tenants,id'],
            'display_name'   => ['required', 'string', 'max:255'],
            'logo_url'       => ['nullable', 'url', 'max:2048'],
            'primary_color'  => ['required', 'string', 'max:7'],
            'accent_color'   => ['required', 'string', 'max:7'],
            'bg_color'       => ['required', 'string', 'max:7'],
            'text_color'     => ['required', 'string', 'max:7'],

            // Validation for email, password, and confirm password
            'email' => [
                'required', 
                'email', 
                'max:255', 
                'unique:users,email,NULL,id,tenant_id,' . tenant('id')  // Ensure email is unique within tenant
            ],
            'password'       => ['required', 'string', 'min:8', 'confirmed'], // Confirmed automatically checks password_confirmation field
            'password_confirmation' => ['required', 'string', 'min:8'], // Ensure password confirmation matches

            // New validation rule for name field
            'name' => ['required', 'string', 'max:255'],  // Name is required, should be a string, and have a maximum length of 255 characters
        ]);
        // 1) Create the tenant (micro-site)
        $tenant =Tenant::create([
            'id'   => $data['id'],
            'data' => [],
            'display_name' => $data['display_name'], // Adjust domain as needed
            'logo_url'      => $data['logo_url'] ?? null,
            'primary_color' => $data['primary_color'],
            'accent_color'  => $data['accent_color'],
            'bg_color'      => $data['bg_color'],
            'text_color'    => $data['text_color'],
        ]);

        // 2) Seed per-tenant roles & permissions
        $tid = method_exists($tenant, 'getTenantKey') ? $tenant->getTenantKey() : $tenant->id;

        // Roles
        $tAdmin = Role::firstOrCreate(['name' => 'admin', 'tenant_id' => $tid]);
        $tUser  = Role::firstOrCreate(['name' => 'user',  'tenant_id' => $tid]);

        // Permissions
        $viewDash    = Permission::firstOrCreate(['name' => 'view-dashboard', 'tenant_id' => $tid]);
        $manageUsers = Permission::firstOrCreate(['name' => 'manage-users',  'tenant_id' => $tid]);

        // Attach permissions to roles (idempotent)
        $tAdmin->permissions()->syncWithoutDetaching([$viewDash->id, $manageUsers->id]);
        $tUser->permissions()->syncWithoutDetaching([$viewDash->id]);

        // (Optional) auto-assign the creating user as tenant admin:
        // if (auth()->check()) {
        //     auth()->user()->roles()->syncWithoutDetaching([$tAdmin->id]);
        // }

        // 3) Register the user within the tenant
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password
        ]);

        // Assign the roleto new user
        $user->roles()->attach($tAdmin->id, ['tenant_id' => $tenant->id ?? null]);

        return redirect()->route('guest.home')->with('status', 'Tenant created.');
    }
}
