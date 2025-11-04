@extends('layouts.tenant')

@section('title', 'Subscription Successful')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card text-center shadow-lg border-success border-3">
                <div class="card-body p-4 p-md-5">

                    <svg class="text-success mb-4" width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg> 

                    <h1 class="h3 card-title fw-bold text-success mb-2">Subscription Active!</h1>

                    <p class="text-muted mb-4">
                        Thank you for subscribing to **{{ optional(tenant())->id ?? 'this Creator' }}**'s exclusive content.
                    </p>

                    <div class="alert alert-success text-start" role="alert">
                        <h6 class="alert-heading fw-bold">What Happens Next?</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li>
                                <i class="bi bi-lock-fill me-2"></i>You now have full access to all exclusive content.
                            </li>
                            <li>
                                <i class="bi bi-credit-card-fill me-2"></i>Your first payment has been processed successfully.
                            </li>
                            <li>
                                <i class="bi bi-calendar-check-fill me-2"></i>Your subscription will automatically renew monthly.
                            </li>
                        </ul>
                    </div>

                    <a href="#" class="btn btn-primary mt-3 w-75">
                        Go to Exclusive Content
                    </a>

                    <p class="small text-muted mt-3 mb-0">
                        *If you have questions, please contact the Creator.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
