@extends('layouts.creator')

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
                <h1 class="display-5 fw-bold mb-2">Tenant User Home</h1>                
            </div>            
        </div>
    </div>    
</div>

@endsection