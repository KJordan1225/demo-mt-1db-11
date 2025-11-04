{{-- resources/views/tenant/creator-payment-setup.blade.php --}}
@extends('layouts.tenant')

@section('title', 'Creator Payment Setup')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body p-4 p-md-5">

          <h2 class="h3 fw-bold text-center mb-3">Creator Payment Setup</h2>
          <p class="text-muted text-center mb-4">
            To start receiving subscription payments on your micro-site, connect your Stripe account.
            This is a one-time setup for verification and payouts.
          </p>

          {{-- Session alerts --}}
          @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @php

            use App\Models\Tenant;

            $tenantId = Auth::user()->tenant_id;
            $tenant = Tenant::find($tenantId);
            $tenantModel = Auth::check() ? optional(Auth::user()->currentTenant()) : null;
            $stripeId    = $tenant?->stripe_account_id;
            $tenantParam = tenant('id') ?? $tenant?->id;
          @endphp

          @if (Auth::check() && empty($stripeId))
            <a
                href="{{ route('stripe.connect.create', ['tenant' => $tenant]) }}"
                class="btn btn-primary btn-lg w-100"
            >
              Connect with Stripe
            </a>
            <p class="text-muted small text-center mt-3 mb-0">
              You’ll be redirected to Stripe to complete verification.
            </p>

          @elseif (Auth::check() && !empty($stripeId))
            <div class="alert alert-success text-center mb-0" role="alert">
              <div class="mb-2">
                <span class="badge bg-success-subtle border border-success text-success">
                  acct_{{ \Illuminate\Support\Str::of($stripeId)->after('acct_')->value() }}
                </span>
              </div>
              Stripe account is connected!
            </div>

          @else
            <div class="alert alert-warning text-center mb-0" role="alert">
              Please log in to continue setup.
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
