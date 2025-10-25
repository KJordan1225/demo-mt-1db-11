@extends('layouts.creator') {{-- or your layout --}}

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">Create Subscription Plan</h1>

    {{-- Flash success --}}
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tenant.pricing.store', ['tenant' => $tenant->id]) }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Plan Name</label>
            <input type="text" name="name" class="form-control" required
                   value="{{ old('name', 'Subscription for ' . ($tenant->display_name ?? 'Creator')) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Description (optional)</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Monthly Price (USD)</label>
            <input type="number" name="amount" class="form-control" required min="1" step="0.01"
                   value="{{ old('amount', 9.99) }}">
            <div class="form-text">Charge users this amount per month.</div>
        </div>

        <button type="submit" class="btn btn-primary">Create Plan</button>
    </form>

    {{-- Optional: show current Stripe product/price IDs if already set --}}
    @if(!empty($tenant->stripe_product_id) || !empty($tenant->stripe_price_id))
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="mb-2">Current Plan</h6>
                <div class="small text-muted">
                    Product: <code>{{ $tenant->stripe_product_id ?? '—' }}</code><br>
                    Price: <code>{{ $tenant->stripe_price_id ?? '—' }}</code>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
