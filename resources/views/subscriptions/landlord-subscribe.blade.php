@extends('layouts.tenant')

@section('content')
<div class="container my-4">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h5 class="card-title mb-1">Subscribe to Landlord</h5>
          <p class="text-muted mb-4">Unlock platform-level features and support.</p>

          <ul class="mb-4">
            <li>• Priority support</li>
            <li>• Advanced analytics</li>
            <li>• Platform-wide perks</li>
          </ul>

          <form method="POST" action="{{ route('landlord.subscribe.start', ['tenant' => tenant('id')]) }}">
            @csrf
            {{-- Add inputs for coupon or quantity if needed --}}
            <button type="submit" class="btn btn-primary w-100">
              Subscribe Now
            </button>
          </form>

          <div class="mt-3 text-muted small">
            Billed via Stripe. You can cancel anytime from your account settings.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
