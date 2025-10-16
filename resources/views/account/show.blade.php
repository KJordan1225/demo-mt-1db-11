@extends('layouts.landlord')

@section('content')
<div class="container py-4 py-md-5">
    <h1 class="mb-4">My Account</h1>

    @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif

    @if($subscription)
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title">Subscription</h5>
                <p class="mb-1"><strong>Status:</strong> {{ $subscription->stripe_status }}</p>
                @if($subscription->onGracePeriod())
                    <p class="text-warning">On grace period until {{ optional($subscription->ends_at)->toFormattedDateString() }}</p>
                @endif

                <div class="d-flex gap-2 mt-3">
                    @if($subscription->active() && !$subscription->onGracePeriod())
                        <form method="POST" action="{{ route('guest.subscription.cancel') }}">@csrf
                            <button class="btn btn-outline-danger">Cancel at period end</button>
                        </form>
                    @endif

                    @if($subscription->onGracePeriod())
                        <form method="POST" action="{{ route('guest.subscription.resume') }}">@csrf
                            <button class="btn btn-success">Resume</button>
                        </form>
                    @endif

                    <a href="{{ route('guest.microsite.configure') }}" class="btn btn-success" role="button">Configure your micro-site</a>
                </div>
            </div>
        </div>
    @else
        <p>No active subscription.</p>
        <a href="{{ route('guest.plans.index') }}" class="btn btn-primary">Choose a Plan</a>
    @endif
</div>
@endsection
