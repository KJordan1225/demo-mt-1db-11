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

<div class="container py-4 py-md-5">
    <!-- Header / Hero -->
    <div class="creator-hero p-4 p-md-5 shadow-smooth mb-4 mb-md-5">
        <div class="row align-items-center g-4">
            <div class="col-md-7">
                <h1 class="display-5 fw-bold mb-2">Become a Creator</h1>
                <p class="lead mb-3 text-muted">
                    Launch your own micro-site, start earning from your content, and grow your community—
                    all in a few guided steps.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge badge-accent rounded-pill">Fast setup</span>
                    <span class="badge bg-light text-dark border">Custom subpath site</span>
                    <span class="badge bg-light text-dark border">Secure payments</span>
                </div>
            </div>
            <div class="col-md-5 text-center">
                <img
                    class="img-fluid img-frame"
                    src="https://picsum.photos/seed/creator-hero/640/360"
                    alt="Creators dashboard preview"
                >
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main content -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h4 mb-4">How it works</h2>

                    <!-- Step 1 -->
                    <div class="step" data-step="1">
                        <div class="step-line"></div>
                        <h3 class="h5 mb-2">Sign Up & pick your plan</h3>
                        <p class="mb-3">
                            Head to the <strong>Sign Up</strong> page, choose the creator plan that fits your goals,
                            and complete checkout. Plans include everything you need to start—hosting, analytics,
                            and payout support—so you can focus on creating.
                        </p>
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-6">
                                <img
                                    class="img-fluid img-frame"
                                    src="https://picsum.photos/seed/creator-plans/560/340"
                                    alt="Select a creator plan"
                                >
                            </div>
                            <div class="col-sm-6">
                                <ul class="list-unstyled small mb-3">
                                    <li>✓ Instant activation after payment</li>
                                    <li>✓ Change or upgrade your plan anytime</li>
                                    <li>✓ Transparent pricing—no hidden fees</li>
                                </ul>
                                <a href="#"
                                   class="btn btn-primary">
                                    View Plans
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="step" data-step="2">
                        <div class="step-line"></div>
                        <h3 class="h5 mb-2">Register your creator site</h3>
                        <p class="mb-3">
                            After checkout, go to <strong>Register</strong>. Enter your
                            <em>site name</em>, <em>email address</em>, and a
                            <em>secure password</em> (confirm it), then submit.
                            We’ll automatically provision your site and wire it into the platform.
                        </p>
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-6">
                                <img
                                    class="img-fluid img-frame"
                                    src="https://picsum.photos/seed/creator-register/560/340"
                                    alt="Register creator site"
                                >
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-2">In a few seconds, you’ll be live at:</p>
                                <div class="example-url">http://127.0.0.1/&lt;your-site-name&gt;</div>
                                <p class="text-muted small mt-2">
                                    Tip: Pick a short, memorable name—avoid spaces and special characters.
                                </p>
                                <a href="{{ route('register', null) }}"
                                   class="btn btn-outline-primary">
                                    Go to Registration
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 (nice embellishment) -->
                    <div class="step" data-step="3">
                        <div class="step-line"></div>
                        <h3 class="h5 mb-2">Customize & publish</h3>
                        <p class="mb-3">
                            Add your banner & avatar, set your pricing, and publish your first post or video.
                            Use our built-in branding tools to match your vibe—colors, typography, and layout.
                        </p>
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-6">
                                <img
                                    class="img-fluid img-frame"
                                    src="https://picsum.photos/seed/creator-theme/560/340"
                                    alt="Customize your brand"
                                >
                            </div>
                            <div class="col-sm-6">
                                <ul class="list-unstyled small">
                                    <li>✓ Drag-and-drop media uploads</li>
                                    <li>✓ Prebuilt themes & instant preview</li>
                                    <li>✓ SweetAlert confirmations for key actions</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Helpful notes -->
                    <div class="alert alert-info mt-4">
                        <div class="d-flex">
                            <div class="me-2 fs-4">💡</div>
                            <div>
                                <strong>Pro tip:</strong> You can revisit your plan later, and your content won’t be affected.
                                For best results, verify your email right after registering so followers can subscribe immediately.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / CTA -->
        <div class="col-lg-4">
            <div class="card cta-card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-2">Ready to start?</h5>
                    <p class="text-muted">
                        Create your site in minutes and unlock subscription income today.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="#"
                           class="btn btn-primary btn-lg">Choose a Plan</a>
                        <a href="{{ route('register', null) }}"
                           class="btn btn-outline-primary">Register Your Site</a>
                    </div>

                    <hr class="my-4">

                    <h6 class="text-uppercase small text-muted mb-2">What you get</h6>
                    <ul class="list-unstyled small mb-0">
                        <li>🚀 Fast site provisioning</li>
                        <li>🧾 Stripe-ready checkout</li>
                        <li>🎨 Custom branding tools</li>
                        <li>📈 Basic analytics</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="mb-2">Have questions?</h6>
                    <p class="small text-muted">
                        We’re happy to help you pick a plan and set up. Check our quick start guide or reach out.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('guest.about') ?? '#' }}" class="btn btn-light border">Quick Start Guide</a>
                        <a href="{{ route('guest.contact') ?? '#' }}" class="btn btn-light border">Contact Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection