{{-- resources/views/tenant/subscription/landing.blade.php --}}
@extends('layouts.tenant')

@section('title', 'Welcome')

@section('content')
@php
    // Assume 'tenant()' helper is available via Stancl middleware
    $creator = tenant();
    $priceId = $creator?->subscription_price_id ?? null;
    $priceDescription = $priceId ? '$9.99/month' : 'Price TBD';
@endphp

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body p-4 p-md-5 text-center">

          <h2 class="h3 fw-bold text-primary mb-2">
            Welcome to {{ $creator?->id }}'s Site!
          </h2>
          <p class="text-muted mb-4">
            Get exclusive, ad-free content and support the creator directly.
          </p>

          {{-- Session messages --}}
          @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif
          @if (session('info'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              {{ session('info') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          {{-- Plan card --}}
          <div class="card border-primary-subtle mb-4 text-start">
            <div class="card-body">
              <p class="h5 fw-semibold text-primary mb-1">Exclusive Access Plan</p>
              <p class="display-6 fw-bold text-primary my-2">{{ $priceDescription }}</p>
              <p class="text-muted small mb-0">Billed monthly. Cancel anytime.</p>
            </div>
          </div>

          {{-- CTA --}}
          @if ($priceId)
            <form action="{{ route('subscription.checkout', ['tenant' => $creator?->id]) }}" method="POST" class="d-grid">
              @csrf
              <button type="submit" class="btn btn-success btn-lg">
                Start Subscription Now
              </button>
            </form>
            <p class="text-muted small mt-3 mb-0">
              Payment processed by Stripe, funds sent directly to {{ $creator?->id }}.
            </p>
          @else
            <div class="alert alert-danger mb-0" role="alert">
              Subscription is not yet configured by the creator.
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
