<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    public function tenantCreate(): View
    {
        return view('auth.tenant.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        if ($user->name === 'Super Admin') {
            $tid = NULL;
            $tAdmin = Role::firstOrCreate(['name' => 'super-admin', 'tenant_id' => $tid]);
            // Assign the roleto new user
            $user->roles()->attach($tAdmin->id, ['tenant_id' => $tenant->id ?? null]);
        }

        Auth::login($user);

        return redirect(route('guest.home', absolute: false));
    }

    public function tenantStore(Request $request): RedirectResponse
    {
        // Ensure we're inside a tenant context
        abort_if(! tenant(), 404, 'No tenant context');

        $tenantId = tenant('id'); // or: optional(tenant())->getTenantKey()

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                // Uniqueness per-tenant
                Rule::unique('users', 'email')->where('tenant_id', $tenantId),
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'tenant_id' => $tenantId,                 // ⬅️ make it tenant-aware
            'name'      => $request->name,
            'email'     => strtolower($request->email),
            'password'  => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);        

        // DB::table('sessions')
        // ->where('id', session()->getId())
        // ->update(['tenant_id' => tenant()?->getTenantKey()]);

        return redirect(route('dashboard', absolute: false));
    }


}
