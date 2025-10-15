@extends('layouts.landlord')

@section('content')
<style>
    /* Brand-aware theming (falls back if not provided) */
    :root{
        --brand-primary: {{ e($branding['primary_color'] ?? '#6C2BD9') }};
        --brand-accent:  {{ e($branding['accent_color']  ?? '#F59E0B') }};
        --brand-bg:      {{ e($branding['bg_color']      ?? '#F8FAFC') }};
        --brand-text:    {{ e($branding['text_color']    ?? '#0F172A') }};
    }

    .plans-hero {
        background: linear-gradient(135deg, var(--brand-bg), #ffffff);
        border-radius: 1rem;
    }
    .shadow-smooth { box-shadow: 0 8px 24px rgba(17, 24, 39, .08); }

    .plan-card {
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        transition: transform .15s ease, box-shadow .15s ease;
        overflow: hidden;
    }
    .plan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(17, 24, 39, .1);
    }

    .plan-media {
        height: 160px;
        background-size: cover;
        background-position: center;
    }
    .ribbon {
        position: absolute;
        top: 12px;
        right: -40px;
        transform: rotate(35deg);
        background: var(--brand-accent);
        color: #111;
        padding: .35rem 2.25rem;
        font-weight: 700;
        font-size: .8rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.15);
    }
    .price {
        font-size: 2.25rem;
        line-height: 1;
        font-weight: 800;
        color: var(--brand-primary);
    }
    .per {
        color: #64748b;
        font-size: .95rem;
    }
    .feature-list li::marker { color: var(--brand-primary); }

    /* Mobile niceties */
    @media (max-width: 576px) {
        .plans-hero { border-radius: .75rem; }
        .plan-media { height: 140px; }
        .card-body { padding: 1rem !important; }
        .display-5 { font-size: 1.9rem; }
    }
</style>

<div class="container py-4 py-md-5">
    <!-- Header / Hero -->
    <div class="plans-hero p-4 p-md-5 shadow-smooth mb-4 mb-md-5">
        <div class="row align-items-center g-4">
            <div class="col-12 col-md-6">
                <h1 class="display-5 fw-bold mb-2">Creator Plans</h1>
                <p class="lead text-muted mb-3">
                    Pick a plan that fits your goals. Upgrade anytime—no hidden fees. You’ll get a hosted creator site,
                    secure payments, and tools to grow your audience.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border">Instant setup</span>
                    <span class="badge bg-light text-dark border">Stripe-ready</span>
                    <span class="badge" style="background: var(--brand-accent); color:#111;">Cancel anytime</span>
                </div>
            </div>
            <div class="col-12 col-md-6 text-center">
                <img
                    class="img-fluid rounded shadow-sm"
                    src="https://picsum.photos/seed/creator-plans-hero/640/360"
                    alt="Creator plans overview"
                    loading="lazy"
                >
            </div>
        </div>
    </div>

    <!-- Plans -->
    <div class="row g-4">
        <!-- BASIC -->
        <div class="col-12 col-md-6">
            <div class="card plan-card h-100 shadow-sm position-relative">
                <div class="plan-media" style="background-image:url('https://picsum.photos/seed/basic-plan/800/400');"></div>
                <div class="card-body p-4">
                    <h3 class="h4 mb-1">Basic</h3>
                    <div class="d-flex align-items-end gap-2 mb-3">
                        <span class="price">$5</span><span class="per">/month</span>
                    </div>
                    <p class="text-muted">
                        Start creating and monetizing with essentials. Perfect for testing your content strategy.
                    </p>
                    <ul class="feature-list small mb-4">
                        <li>Custom creator site at <code>http://127.0.0.1/&lt;your-site-name&gt;</code></li>
                        <li>Post photos & videos</li>
                        <li>Subscriber management</li>
                        <li>Basic analytics</li>
                        <li>Email support</li>
                    </ul>
                    <div class="d-grid gap-2">
                        <a href="{{ route('register', ['tenant' => $branding['slug'] ?? null, 'plan' => 'basic']) }}"
                           class="btn btn-primary">
                            Choose Basic
                        </a>
                        <a href="{{ route('guest.contact') ?? '#' }}" class="btn btn-outline-secondary">
                            Ask a question
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- PREMIUM -->
        <div class="col-12 col-md-6">
            <div class="card plan-card h-100 shadow-sm position-relative">
                <div class="ribbon">Most Popular</div>
                <div class="plan-media" style="background-image:url('https://picsum.photos/seed/premium-plan/800/400');"></div>
                <div class="card-body p-4">
                    <h3 class="h4 mb-1">Premium</h3>
                    <div class="d-flex align-items-end gap-2 mb-3">
                        <span class="price">$10</span><span class="per">/month</span>
                    </div>
                    <p class="text-muted">
                        Level up with advanced tools and priority support—ideal for growing creators.
                    </p>
                    <ul class="feature-list small mb-4">
                        <li>Everything in Basic</li>
                        <li>HD video hosting & larger uploads</li>
                        <li>Scheduled posts & drafts</li>
                        <li>Discount codes & promo campaigns</li>
                        <li>Priority email support</li>
                    </ul>
                    <div class="d-grid gap-2">
                        <a href="{{ route('register', ['tenant' => $branding['slug'] ?? null, 'plan' => 'premium']) }}"
                           class="btn btn-primary" style="background: var(--brand-primary);">
                            Choose Premium
                        </a>
                        <a href="{{ route('guest.about') ?? '#' }}" class="btn btn-outline-secondary">
                            See how Premium helps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ / Notes -->
    <div class="row mt-4 mt-md-5">
        <div class="col-12 col-lg-10 mx-auto">
            <div class="alert alert-info shadow-sm">
                <div class="d-flex align-items-start">
                    <div class="me-2 fs-4">ℹ️</div>
                    <div class="small">
                        <strong>No long-term contracts.</strong> You can upgrade or cancel anytime from your account settings.
                        After registering, we’ll provision your site automatically and you’ll be live within moments.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
