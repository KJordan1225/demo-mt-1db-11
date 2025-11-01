<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;


class StripeConnectController extends Controller
{
    public function return(Request $request)
    {
        // Optionally verify completion to decide success vs error
        $tenant = tenant(); // or resolve by route param
        $stripe = new StripeClient(config('services.stripe.secret'));

        $ok = false;
        if ($tenant?->stripe_account_id) {
            $acct = $stripe->accounts->retrieve($tenant->stripe_account_id, []);
            $ok   = (bool) $acct->details_submitted; // or also check charges_enabled/payouts_enabled
        }

        return $ok
            ? redirect()->route('tenant.dashboard', ['tenant' => $tenant->id])
                ->with('success', 'Creator account successfully created!')
            : redirect()->route('tenant.dashboard', ['tenant' => $tenant->id])
                ->with('error', 'Stripe onboarding not completed. Please finish setup.');
    }

    public function refresh(Request $request)
    {
        $tenant = tenant();
        return redirect()->route('tenant.dashboard', ['tenant' => $tenant->id])
            ->with('error', 'Stripe onboarding was interrupted or expired. Please try again.');
    }
}

