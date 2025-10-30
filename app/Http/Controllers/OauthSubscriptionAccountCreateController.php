<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\OAuth;
use App\Models\Tenant;
use Stancl\Tenancy\Facades\Tenancy;  // For tenancy initialization
use Illuminate\Support\Facades\Redirect;

class OauthSubscriptionAccountCreateController extends Controller
{
    /**
     * Create the Stripe Connect link for OAuth.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createStripeConnectLink()
    {
        // Initialize tenancy manually if not already done (fallback)
        if (!tenancy()->initialized) {
            $tenantId = request()->route('tenant');  // You can adjust how to get the tenant ID
            tenancy()->initialize($tenantId); // Initialize tenant manually
        }

        // Set the Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Generate the OAuth URL for Stripe authorization
        $url = OAuth::authorizeUrl([
            'response_type' => 'code', // Required for authorization code
            'scope' => 'read_write',   // Required for access scope
            'redirect_uri' => route('stripe.callback'), // Redirect URI after OAuth completion
        ]);

        // Redirect to the Stripe OAuth page
        return redirect($url);
    }

    /**
     * Handle the callback from Stripe after user authorizes the connection.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleStripeCallback(Request $request)
    {
        // Initialize tenancy manually if not already done (fallback)
        if (!tenancy()->initialized) {
            $tenantId = request()->route('tenant');  // You can adjust how to get the tenant ID
            tenancy()->initialize($tenantId); // Initialize tenant manually
        }

        // Check if an authorization code was returned by Stripe
        if (!$request->has('code')) {
            return redirect()->route('tenant.dashboard')->with('error', 'No authorization code returned.');
        }

        // Set the Stripe API secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Exchange the authorization code for an access token
            $response = OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $request->input('code'),
                'client_id' => env('STRIPE_CONNECT_CLIENT_ID'),
                'client_secret' => env('STRIPE_SECRET'),
            ]);

            // Get the Stripe account ID from the response
            $stripeAccountId = $response->stripe_user_id;

            // Get the current tenant
            $tenant = tenancy()->tenant(); // Tenant context for multi-tenant apps

            // Save the Stripe account ID to the tenant model
            $tenant->stripe_account_id = $stripeAccountId;
            $tenant->save();

            // Redirect to the dashboard with a success message
            return redirect()->route('tenant.dashboard')->with('success', 'Stripe account successfully connected!');
        } catch (\Stripe\Exception\OAuth\OAuthErrorException $e) {
            // Handle Stripe API errors (invalid code, etc.)
            return redirect()->route('tenant.dashboard')->with('error', 'Stripe connection failed: ' . $e->getMessage());
        }
    }
}
