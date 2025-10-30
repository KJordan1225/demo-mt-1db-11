<?php

namespace App\Http\Controllers\Connect;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stancl\Tenancy\Tenancy;


class OnboardingController extends Controller
{
    /**
     * Start the onboarding process for a tenant (creator).
     * This handles Stripe account creation and bank payout setup via onboarding link.
     */
    public function start(Request $request, $tenantId, Tenancy $tenancy)
    {
        /**
         * ---------------------------------------------------------
         *  1. Manually initialize tenancy
         * ---------------------------------------------------------
         */
        $tenant = Tenant::where('id', $tenantId)
            ->orWhere('slug', $tenantId)
            ->firstOrFail();

        $tenancy->initialize($tenant);

        /**
         * ---------------------------------------------------------
         *  2. Set Stripe API key
         * ---------------------------------------------------------
         */
        Stripe::setApiKey(config('services.stripe.secret'));

        /**
         * ---------------------------------------------------------
         *  3. Create (or retrieve) Stripe Connect Express account
         * ---------------------------------------------------------
         */
        if (!$tenant->stripe_account_id) {
            $account = Account::create([
                'type' => 'express',
                'country' => 'US',
                'business_type' => 'individual',
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'tenant_name' => $tenant->display_name,
                ],
            ]);

            $tenant->update(['stripe_account_id' => $account->id]);
        } else {
            $account = Account::retrieve($tenant->stripe_account_id);
        }

        /**
         * ---------------------------------------------------------
         *  4. Generate onboarding link for payout setup
         * ---------------------------------------------------------
         */
        $link = AccountLink::create([
            'account' => $account->id,
            'refresh_url' => url("/{$tenant->id}/connect/onboarding/refresh"),
            'return_url'  => url("/{$tenant->id}/connect/onboarding/return"),
            'type'        => 'account_onboarding',
        ]);

        /**
         * ---------------------------------------------------------
         *  5. Redirect the creator to Stripe-hosted onboarding
         * ---------------------------------------------------------
         */
        return redirect()->away($link->url);
    }

    /**
     * Handle return from Stripe after onboarding completion.
     */
    public function return(Request $request, $tenantId, Tenancy $tenancy)
    {
        /**
         * ---------------------------------------------------------
         *  1. Re-initialize tenancy
         * ---------------------------------------------------------
         */
        $tenant = Tenant::where('id', $tenantId)
            ->orWhere('slug', $tenantId)
            ->firstOrFail();

        $tenancy->initialize($tenant);

        Stripe::setApiKey(config('services.stripe.secret'));

        /**
         * ---------------------------------------------------------
         *  2. Retrieve connected account to confirm payout setup
         * ---------------------------------------------------------
         */
        $account = Account::retrieve($tenant->stripe_account_id);

        // Verify payouts_enabled
        if (!$account->payouts_enabled) {
            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->id])
                ->with('status', 'Stripe account connected, but payouts not yet enabled. Please complete payout setup.');
        }

        // Update local tenant record if verified
        $tenant->update([
            'stripe_payouts_enabled' => $account->payouts_enabled,
            'stripe_details_submitted' => $account->details_submitted,
        ]);

        return view('tenant.admin.dashboard', [
            'tenant' => $tenant,
            'status' => 'Stripe account connected and payout setup complete!',
        ]);

    }

    /**
     * Handle onboarding refresh (in case user cancels mid-flow)
     */
    public function refresh(Request $request, $tenantId, Tenancy $tenancy)
    {
        $tenant = Tenant::where('id', $tenantId)
            ->orWhere('slug', $tenantId)
            ->firstOrFail();

        $tenancy->initialize($tenant);

        return redirect()->route('tenant.connect.onboarding', ['tenant' => $tenant->id])
            ->with('status', 'Please restart the onboarding process.');
    }






}
