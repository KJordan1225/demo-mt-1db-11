<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

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

        Tenant::create([
            'id'   => $data['id'],
            'data' => [],
            'display_name' => $data['display_name'], // Adjust domain as needed
            'logo_url'      => $data['logo_url'] ?? null,
            'primary_color' => $data['primary_color'],
            'accent_color'  => $data['accent_color'],
            'bg_color'      => $data['bg_color'],
            'text_color'    => $data['text_color'],
        ]);

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

}
