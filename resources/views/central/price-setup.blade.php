@extends('layouts.tenant')

@section('title', 'Set Subscription Price')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
      <div class="card shadow-lg border-primary border-3 rounded-3">
        <div class="card-body p-4 p-md-5">

          <h2 class="h3 fw-bold text-center mb-4 text-primary">Set Your Subscription Price</h2>

          {{-- Session alerts --}}
          @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif
          @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
          @endif

          @php
            // $tenant is a single Tenant model (route-model bound)
            $stripeAccountId = $tenant->stripe_account_id ?? null;
          @endphp

          @if ($stripeAccountId)
            <form action="{{ route('central.price.store', $tenant) }}" method="POST">
              @csrf

              <p class="text-muted text-center mb-4 small">
                Set the <strong>monthly price</strong> your subscribers will pay for exclusive access.
                This is configured directly on your connected Stripe account.
              </p>

              <div class="mb-4">
                <label for="price" class="form-label fw-bold">Monthly Price (USD)</label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text">$</span>
                  <input
                    type="number"
                    class="form-control @error('price') is-invalid @enderror"
                    id="price"
                    name="price"
                    value="{{ old('price', $tenant->monthly_price) }}"
                    step="0.01"
                    min="1.00"
                    required
                    placeholder="9.99"
                  >
                </div>
                @error('price')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              @if ($tenant->subscription_price_id)
                <div class="alert alert-info small text-center p-2 mb-4">
                  ✅ <strong>Price ID Created:</strong>
                  <span class="fw-bold">{{ $tenant->subscription_price_id }}</span>.
                  All new subscriptions will use this price.
                </div>
              @endif

              <button type="submit" class="btn btn-success w-100 btn-lg shadow">
                {{ $tenant->subscription_price_id ? 'Update Price on Stripe' : 'Create Price on Stripe' }}
              </button>

              <p class="small text-muted text-center mt-3 mb-0">
                Tip: Changing the price creates a new Price ID on Stripe (old ones are preserved).
              </p>
            </form>
          @else
            <div class="alert alert-warning text-center" role="alert">
              ⚠️ <strong>Setup Required:</strong> Please connect your Stripe account first.
              <a href="{{ route('central.dashboard', $tenant) }}" class="alert-link fw-bold">Go to Payment Setup</a>
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
