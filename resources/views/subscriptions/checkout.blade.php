@extends('layouts.landlord')

@section('content')
<style>
    .checkout-card { border-radius: 1rem; }
    @media (max-width: 576px) { .card-body { padding: 1rem !important; } }
</style>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card checkout-card shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h4 mb-3">Subscribe to {{ $plan['name'] }}</h1>
                    <p class="mb-2"><strong>${{ number_format(($plan['amount']/100), 2) }}/month</strong></p>
                    <p class="text-muted">Enter your payment details to start your subscription.</p>

                    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

                    <form id="subscription-form"
                          action="{{ route('guest.subscribe.store', ['plan' => $planKey]) }}"
                          method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" id="payment_method">

                        <div class="mb-3">
                            <label class="form-label">Payment details</label>
                            <div id="payment-element"></div>
                        </div>

                        <div class="d-grid">
                            <button id="submitBtn" class="btn btn-primary btn-lg" type="submit">
                                Start Subscription
                            </button>
                        </div>

                        <div id="form-errors" class="text-danger small mt-3" style="display:none;"></div>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                You’ll be charged ${{ number_format(($plan['amount']/100), 2) }} today and monthly thereafter. Cancel anytime.
            </p>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const stripe = Stripe(@json($stripeKey));
    const options = { clientSecret: @json($clientSecret), appearance: { theme: 'stripe' } };

    const elements = stripe.elements(options);
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    const form = document.getElementById('subscription-form');
    const submitBtn = document.getElementById('submitBtn');
    const errorsBox = document.getElementById('form-errors');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        submitBtn.disabled = true;
        errorsBox.style.display = 'none';
        errorsBox.textContent = '';

        const {setupIntent, error} = await stripe.confirmSetup({
            elements,
            confirmParams: { return_url: window.location.href },
            redirect: 'if_required'
        });

        if (error) {
            errorsBox.textContent = error.message;
            errorsBox.style.display = 'block';
            submitBtn.disabled = false;
            return;
        }
        document.getElementById('payment_method').value = setupIntent.payment_method;
        form.submit();
    });
});
</script>
@endsection
