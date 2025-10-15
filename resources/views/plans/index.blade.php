@extends('layouts.landlord')

@section('content')
<div class="container py-4 py-md-5">
    <h1 class="mb-4">Creator Plans</h1>

    <div class="row g-4">
        @foreach($plans as $key => $p)
            <div class="col-12 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="h4">{{ $p['name'] }}</h3>
                        <p class="display-6">
                            ${{ number_format($p['amount']/100, 2) }}
                            <small class="text-muted">/month</small>
                        </p>
                        <p class="text-muted">Everything you need to get started. Upgrade anytime.</p>

                        <a href="{{ route('guest.subscribe.show', ['plan' => $key])  }}"
                           class="btn btn-primary">
                           Choose {{ $p['name'] }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
