<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\StripeService; // Import the StripeService
use Illuminate\Http\Request;

class CreatorController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Register a new tenant (creator).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validate and create the tenant
        $tenant = Tenant::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            // Add other necessary fields for your tenant
        ]);

        // Create a Stripe Connect account for this tenant (creator)
        try {
            $this->stripeService->createStripeConnectAccount($tenant);
            return redirect()->route('tenant.dashboard2')->with('success', 'Creator account successfully created!');
        } catch (\Exception $e) {
            return redirect()->route('tenant.dashboard2')->with('error', 'Error creating Stripe account: ' . $e->getMessage());
        }
    }
}

