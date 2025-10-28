@extends('layouts.landlord')

@section('content')
<div class="container py-4">
    <h1 class="display-4 text-center mb-4">Subscription Canceled</h1>
    <p class="text-center">It seems like you have canceled the subscription process. Feel free to try again later.</p>
    <div class="d-flex justify-content-center">
        <a href="{{ route('tenant.dashboard') }}" class="btn btn-secondary">Go Back to Dashboard</a>
    </div>
</div>
@endsection
