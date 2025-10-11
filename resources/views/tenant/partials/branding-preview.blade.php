<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h5 fw-semibold mb-3">Branding Preview</h2>

        <div class="d-flex align-items-center gap-3 mb-3">
            <span class="rounded-circle d-inline-block" style="width:24px;height:24px;background:var(--brand-primary)" title="Primary color"></span>
            <span class="rounded-circle d-inline-block" style="width:24px;height:24px;background:var(--brand-accent)" title="Accent color"></span>
            <span class="rounded-circle d-inline-block border" style="width:24px;height:24px;background:var(--brand-bg)" title="Background color"></span>
        </div>

        <div class="mb-3 small text-muted">
            <div>Primary: <code>{{ $branding['primary_color'] }}</code></div>
            <div>Accent: <code>{{ $branding['accent_color'] }}</code></div>
            <div>Background: <code>{{ $branding['bg_color'] }}</code></div>
            <div>Text: <code>{{ $branding['text_color'] }}</code></div>
        </div>

        <button type="button" class="btn brand-btn shadow-sm">
            Primary Action
        </button>
        <a href="{{ route('tenant.dashboard', ['tenant' => $branding['slug']]) }}" class="btn btn-link brand-link ms-2 p-0 align-baseline">
            View Dashboard
        </a>
    </div>
</div>
