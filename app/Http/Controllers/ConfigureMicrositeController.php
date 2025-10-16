<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class ConfigureMicrositeController extends Controller
{
    public function index()
    {
        return view('guest.configure_site');
    }

    public function store(Request $request)
{
	$data = $request->validate([
		'id'             => ['required', 'string', 'max:191', 'unique:tenants,id'],
		'display_name'   => ['required', 'string', 'max:255'],
		'logo_url'       => ['nullable', 'url', 'max:2048'],
		'primary_color'  => ['required', 'string', 'max:7'],
		'accent_color'   => ['required', 'string', 'max:7'],
		'bg_color'       => ['required', 'string', 'max:7'],
		'text_color'     => ['required', 'string', 'max:7'],
		
		// Password validation
		'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' will check for matching passwords
		'password_confirmation' => ['required', 'string', 'min:8'],
	]);

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

  
    $email = auth()->user()->email;
    $user = app(\App\Services\UserRegistrar::class)->register([
        'name'  => $data['display_name'],
        'email' => $email,
        'password' => $data['password'], // or omit to auto-generate
    ], $tenant->id ?? null);

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
	//if (auth()->check()) {
	//	auth()->user()->roles()->syncWithoutDetaching([$tAdmin->id]);
	//}
    return redirect()->route('tenants.index')->with('status', 'Tenant created.');
    }
}
