@extends('layouts.landlord')

@section('content')
<div class="container py-4 py-md-5">
    <h1 class="h4 mb-3">Subscription: {{ $subscription->name }}</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Stripe ID</dt>
                <dd class="col-sm-9"><code>{{ $subscription->stripe_id }}</code></dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ $subscription->stripe_status }}</dd>

                <dt class="col-sm-3">Price</dt>
                <dd class="col-sm-9"><code>{{ $subscription->stripe_price }}</code></dd>

                <dt class="col-sm-3">Ends At</dt>
                <dd class="col-sm-9">
                    {{ $subscription->ends_at ? $subscription->ends_at->toDayDateTimeString() : '—' }}
                </dd>
            </dl>

            <div class="d-flex gap-2">
                @if($subscription->active() && !$subscription->onGracePeriod())
                    <form method="POST"
                          action="{{ route('tenant.subscriptions.cancel', ['tenant' => $tenant->id, 'stripeId' => $subscription->stripe_id]) }}">
                        @csrf
                        <button class="btn btn-outline-warning">Cancel (period end)</button>
                    </form>

                    <form method="POST"
                          action="{{ route('tenant.subscriptions.cancelNow', ['tenant' => $tenant->id, 'stripeId' => $subscription->stripe_id]) }}">
                        @csrf
                        <button class="btn btn-outline-danger">Cancel now</button>
                    </form>
                @endif

                <a href="{{ route('tenant.subscriptions.index', ['tenant' => $tenant->id]) }}" class="btn btn-secondary ms-auto">
                    Back to subscriptions
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
