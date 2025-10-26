@extends('layouts.creator')

@section('content')

@php
    $tenant = tenant(); // Current tenant
@endphp
<div class="container py-4">

    {{-- Flash messages from success/cancel redirects --}}
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1 class="h4 mb-3">{{ $tenant->display_name }} — Subscribe</h1>

    {{-- Show plan info if you want --}}
    <div class="mb-3 small text-muted">
        Product: <code>{{ $tenant->stripe_product_id ?? '—' }}</code> ·
        Price: <code>{{ $tenant->stripe_price_id ?? '—' }}</code>
    </div>

    @if(!$tenant->stripe_account_id || !$tenant->stripe_price_id)
        <div class="alert alert-warning">
            Creator is not ready for subscriptions yet.
        </div>
    @else
        <form method="POST" action="{{ route('tenant.subscribe', ['tenant' => $tenant->id]) }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                Subscribe to {{ $tenant->display_name }}
            </button>
        </form>
    @endif

    <hr class="my-4">

    <p class="text-muted small">
        You will be redirected to a secure Stripe Checkout page to complete your subscription.
    </p>
</div>
@endsection
