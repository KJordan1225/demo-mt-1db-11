<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription; // Cashier's model
use Stancl\Tenancy\Tenancy;

class SubscriptionManageController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth']);
    // }

    /**
     * List the current user's subscriptions filtered for the current tenant (by stripe_price_id).
     */
    public function index(Request $request, Tenant $tenant, Tenancy $tenancy)
    {
        // Safe fallback: initialize tenancy if middleware hasn’t run
        if (!tenancy()->initialized) {
            $tenancy->initialize($tenant);
        }

        $user = Auth::user();

        // Cashier keeps: name, stripe_id, stripe_status, stripe_price, ends_at, etc.
        // Filter by this tenant's price if you want tenant-specific list:
        $query = $user->subscriptions()->orderByDesc('created_at');

        if (!empty($tenant->stripe_price_id)) {
            $query->where('stripe_price', $tenant->stripe_price_id);
        }

        $subs = $query->get();

        return view('tenant.subscriptions.index', [
            'tenant' => $tenant,
            'subscriptions' => $subs,
        ]);
    }


    public function indexCentral(Request $request)
    {
        $user = Auth::user();

        // Cashier subscriptions for THIS user, landlord-scoped only
        // Assumes your subscriptions table has a nullable `tenant_id` column
        // $subscriptions = $user->subscriptions()
        //     ->whereNull('tenant_id')     // landlord scope
        //     ->orderByDesc('created_at')
        //     ->get();

        $subscriptions = Subscription::whereNull('tenant_id')
            ->orderByDesc('created_at')
            ->get();

        // Tip: if you want all users’ landlord subs (e.g., super-admin view), query the model directly:
        // $subscriptions = \App\Models\Subscription::whereNull('tenant_id')->latest()->get();

        return view('landlord.subscriptions.index', [
            'subscriptions' => $subscriptions,
        ]);
    }


    /**
     * Show a single subscription by its Stripe subscription ID (Cashier's stripe_id column).
     */
    public function show(Request $request, Tenant $tenant, string $stripeId, Tenancy $tenancy)
    {
        if (!tenancy()->initialized) {
            $tenancy->initialize($tenant);
        }

        $user = Auth::user();

        /** @var Subscription|null $sub */
        $sub = $user->subscriptions()->where('stripe_id', $stripeId)->firstOrFail();

        return view('tenant.subscriptions.show', [
            'tenant' => $tenant,
            'subscription' => $sub,
        ]);
    }

    /**
     * Cancel at period end.
     */
    public function cancel(Request $request, Tenant $tenant, string $stripeId, Tenancy $tenancy)
    {
        if (!tenancy()->initialized) {
            $tenancy->initialize($tenant);
        }

        $user = Auth::user();
        $sub = $user->subscriptions()->where('stripe_id', $stripeId)->firstOrFail();

        $sub->cancel();            // period-end
        $sub->syncStripeStatus();  // pull latest, sets ends_at, stripe_status, etc.

        return back()->with('status', 'Subscription will end at the period’s end.');
    }

    /**
     * Cancel immediately (no proration handling shown; adjust to your policy).
     */
    public function cancelNow(Request $request, Tenant $tenant, string $stripeId, Tenancy $tenancy)
    {
        if (!tenancy()->initialized) {
            $tenancy->initialize($tenant);
        }

        $user = Auth::user();
        $sub = $user->subscriptions()->where('stripe_id', $stripeId)->firstOrFail();

        $sub->cancelNow();
        $sub->syncStripeStatus();

        return back()->with('status', 'Subscription canceled immediately.');
    }
}
