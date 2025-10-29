@extends('layouts.landlord')

@section('content')
<div class="container py-4 py-md-5">
    <h1 class="h3 mb-4">My Subscriptions — {{ $tenant->display_name }}</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($subscriptions->isEmpty())
        <div class="alert alert-info">You have no subscriptions for this tenant.</div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Stripe Price</th>
                        <th>Ends At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $sub)
                        <tr>
                            <td class="fw-semibold">
                                <a href="{{ route('tenant.subscriptions.show', ['tenant' => $tenant->id, 'stripeId' => $sub->stripe_id]) }}">
                                    {{ $sub->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($sub->active()) bg-success
                                    @elseif($sub->canceled()) bg-warning text-dark
                                    @else bg-secondary @endif">
                                    {{ $sub->stripe_status }}
                                </span>
                            </td>
                            <td><code>{{ $sub->stripe_price }}</code></td>
                            <td>
                                @if($sub->ends_at)
                                    {{ $sub->ends_at->toDayDateTimeString() }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                @if($sub->active() && !$sub->onGracePeriod())
                                    <form method="POST"
                                          action="{{ route('tenant.subscriptions.cancel', ['tenant' => $tenant->id, 'stripeId' => $sub->stripe_id]) }}"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-warning"
                                                onclick="return confirm('Cancel at period end?')">
                                            Cancel (period end)
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('tenant.subscriptions.cancelNow', ['tenant' => $tenant->id, 'stripeId' => $sub->stripe_id]) }}"
                                          class="d-inline ms-2">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Cancel immediately?')">
                                            Cancel now
                                        </button>
                                    </form>
                                @elseif($sub->onGracePeriod())
                                    <span class="small text-muted">Ends {{ $sub->ends_at?->toFormattedDateString() }}</span>
                                @else
                                    <span class="small text-muted">Not active</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
