@extends('layouts.landlord')

@section('content')

<style>
    /* Page theming (uses your brand vars if present) */
    :root{
        --brand-primary: {{ e($branding['primary_color'] ?? '#6C2BD9') }};
        --brand-accent:  {{ e($branding['accent_color']  ?? '#F59E0B') }};
        --brand-bg:      {{ e($branding['bg_color']      ?? '#0F172A0D') }};
        --brand-text:    {{ e($branding['text_color']    ?? '#0F172A') }};
    }

    .creator-hero {
        background: linear-gradient(135deg, var(--brand-bg), #ffffff);
        border-radius: 1rem;
    }

    .step {
        position: relative;
        padding-left: 3.25rem;
        margin-bottom: 2rem;
    }
    .step:before {
        content: attr(data-step);
        position: absolute;
        left: 0;
        top: 0;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        border: 2px solid var(--brand-primary);
        display: grid;
        place-items: center;
        font-weight: 700;
        color: var(--brand-primary);
        background: #fff;
    }
    .step + .step { margin-top: 1.5rem; }
    .step-line {
        position: absolute;
        left: 1.1rem;
        top: 2.25rem;
        width: 2px;
        height: calc(100% - 2.25rem);
        background: repeating-linear-gradient(
            to bottom,
            var(--brand-primary) 0,
            var(--brand-primary) 6px,
            transparent 6px,
            transparent 12px
        );
        opacity: .35;
        content: "";
    }
    .badge-accent {
        background: var(--brand-accent);
        color: #111;
    }
    .cta-card {
        border: 2px dashed var(--brand-primary);
        border-radius: 1rem;
    }
    .shadow-smooth { box-shadow: 0 8px 24px rgba(17, 24, 39, .08); }
    .example-url {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: .5rem;
        padding: .5rem .75rem;
        display: inline-block;
    }
    .img-frame {
        border: 2px solid #e5e7eb;
        border-radius: .75rem;
    }
</style>


    <div class="container py-4">
        <!-- Page Header / Title -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">Creator Plans</h1>
            <p class="lead text-muted">Choose a plan that fits your needs and start your creative journey!</p>
        </div>

        <!-- Plans Row -->
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <!-- Basic Plan Card -->
            <div class="col">
                <div class="card shadow-sm border-light">
                    <img src="https://via.placeholder.com/400x200?text=Basic+Plan+Image" class="card-img-top" alt="Basic Plan Image">
                    <div class="card-body">
                        <h5 class="card-title text-center">Basic Plan</h5>
                        <p class="card-text text-center">Perfect for new creators looking to get started.</p>
                        <div class="text-center mb-3">
                            <span class="display-6 fw-bold">$5/month</span>
                        </div>
                        <ul class="list-unstyled text-center">
                            <li><i class="bi bi-check-circle"></i> Access to basic content</li>
                            <li><i class="bi bi-check-circle"></i> Community features</li>
                            <li><i class="bi bi-check-circle"></i> Basic support</li>
                        </ul>
                        <div class="d-flex justify-content-center">
                            <form action="{{ route('subscribe.basic') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Subscribe to Basic Plan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Premium Plan Card -->
            <div class="col">
                <div class="card shadow-sm border-light">
                    <img src="https://via.placeholder.com/400x200?text=Premium+Plan+Image" class="card-img-top" alt="Premium Plan Image">
                    <div class="card-body">
                        <h5 class="card-title text-center">Premium Plan</h5>
                        <p class="card-text text-center">For seasoned creators who want to maximize their reach.</p>
                        <div class="text-center mb-3">
                            <span class="display-6 fw-bold">$10/month</span>
                        </div>
                        <ul class="list-unstyled text-center">
                            <li><i class="bi bi-check-circle"></i> Exclusive content</li>
                            <li><i class="bi bi-check-circle"></i> Priority support</li>
                            <li><i class="bi bi-check-circle"></i> Enhanced visibility</li>
                        </ul>
                        <div class="d-flex justify-content-center">
                            <form action="{{ route('subscribe.premium') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">Subscribe to Premium Plan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        /* Custom CSS for Plan Cards */
        .card {
            border-radius: 1rem;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            object-fit: cover;
            height: 200px;
            border-radius: 1rem 1rem 0 0;
        }

        /* Mobile responsiveness */
        .card-body {
            padding: 1.5rem;
        }

        .text-center {
            text-align: center;
        }

        .btn {
            padding: 0.75rem 2rem;
            font-size: 1.25rem;
        }

        /* Ensure buttons are aligned */
        .d-flex {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* List styling */
        .list-unstyled {
            padding-left: 0;
        }

        .list-unstyled li {
            padding-left: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .list-unstyled i {
            color: #28a745;
            margin-right: 0.5rem;
        }
    </style>
@endpush
