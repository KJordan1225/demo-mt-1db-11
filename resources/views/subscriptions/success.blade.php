@extends('layouts.landlord')

@section('content')
<div class="container py-4">
    <h1 class="display-4 text-center mb-4">Subscription Successful!</h1>
    <p class="text-center">Thank you for subscribing to the selected plan. You can now enjoy all the creator's content.</p>
    <div class="d-flex justify-content-center">
        <a href="{{ route('guest.home') }}" class="btn btn-primary">Go to Dashboard</a>
    </div>
</div>
@endsection
