<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::all();
        return view('landlord.creator-index', compact('tenants'));
    }

    public function create()
    {
        return view('landlord.creator-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id'            => ['required','string','max:191','unique:tenants,id'],
            'display_name'  => ['required','string','max:255'],
            'logo_url'      => ['nullable','url','max:2048'],
            'primary_color' => ['required','string','max:7'],
            'accent_color'  => ['required','string','max:7'],
            'bg_color'      => ['required','string','max:7'],
            'text_color'    => ['required','string','max:7'],
        ]);

        $data['slug'] = $data['id']; // set slug == id for simplicity 
        
        $tenant =Tenant::create([
            'id'   => $data['id'],
            'data' => [],
            'display_name' => $data['display_name'], // Adjust domain as needed
            'logo_url'      => $data['logo_url'] ?? null,
            'primary_color' => $data['primary_color'],
            'accent_color'  => $data['accent_color'],
            'bg_color'      => $data['bg_color'],
            'text_color'    => $data['text_color'],
            'slug'          => $data['id'], // set slug == id for simplicity
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

        return redirect()->route('tenants.index')->with('status', 'Tenant created.');
    }

    public function massDestroy(Request $request)
    {
        // Optional: authorize this action
        // $this->authorize('deleteTenants'); 

        $validated = $request->validate([
            'ids'   => ['required','array','min:1'],
            'ids.*' => ['string','distinct','exists:tenants,id'],
        ]);

        $ids = $validated['ids'];

        // Delete in chunks to avoid large IN() issues
        $deleted = 0;
        DB::transaction(function () use ($ids, &$deleted) {
            collect($ids)->chunk(100)->each(function ($chunk) use (&$deleted) {
                Tenant::whereIn('id', $chunk)->get()->each(function (Tenant $tenant) use (&$deleted) {
                    // If you want hard delete: $tenant->forceDelete();
                    $tenant->delete();
                    $deleted++;
                });
            });
        });

        return back()->with('status', "{$deleted} tenant(s) deleted.");
    }


    public function edit(Tenant $tenant)
    {
        if (! is_array($tenant->data ?? null)) {
            $tenant->data = (array) json_decode($tenant->data ?? '[]', true);
        }
    //    dd($tenant);
        // $tenant is route-model-bound by id
        return view('landlord.creator-edit', compact('tenant'));
    }

    public function update(Request $request, \Stancl\Tenancy\Database\Models\Tenant $tenant)
    {
        $val = $request->validate([
            'display_name'  => ['required','string','max:255'],
            'logo_url'      => ['nullable','url','max:2048'],
            'primary_color' => ['required','string','max:7'],
            'accent_color'  => ['required','string','max:7'],
            'bg_color'      => ['required','string','max:7'],
            'text_color'    => ['required','string','max:7'],
        ]);

        // Safely mutate JSON: read → modify → reassign
        $data = $tenant->data ?? [];
        data_set($data, 'display_name', $val['display_name']);
        data_set($data, 'slug', $tenant->id); // keep slug == id (optional)
        data_set($data, 'branding.logo_url', $val['logo_url'] ?? null);
        data_set($data, 'branding.primary_color', $val['primary_color']);
        data_set($data, 'branding.accent_color',  $val['accent_color']);
        data_set($data, 'branding.bg_color',      $val['bg_color']);
        data_set($data, 'branding.text_color',    $val['text_color']);

        $tenant->data = $data;   // reassign so Eloquent marks as dirty
        $tenant->save();

        return redirect()->route('tenants.index')->with('status', 'Tenant updated.');
    }

}
