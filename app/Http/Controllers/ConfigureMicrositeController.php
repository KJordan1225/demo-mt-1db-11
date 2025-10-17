<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

		// Ensure that email is unique per tenant
		if (!User::emailIsUniqueForTenant($request->email, $tenant->id)) {
			return back()->withErrors(['email' => 'This email is already registered for this tenant.']);
		}

		// Create tenant user
		$user = User::create([
			'name'     => $data['display_name'],
			'email'    => $email,
			'password' => Hash::make($data['password']),
			'tenant_id' => $tenant->id, // Tenant ID is set
		]);

		// (Optional) auto-assign the creating user as tenant admin:
		//if (auth()->check()) {
		//	auth()->user()->roles()->syncWithoutDetaching([$tAdmin->id]);
		//}
		// If you have a $tenant model (stancl/tenancy)
		return redirect()
			->route('tenant.landing', ['tenant' => $tenant->id])   // or $tenant->slug
			->with('status', 'Creator account created.');

    }
}
