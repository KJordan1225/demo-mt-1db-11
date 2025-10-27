<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StarCity Starz</title>

    {{-- Bootstrap 5 (replace with @vite if you bundle locally) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --brand-primary: {{ e($branding['primary_color'] ?? '#6C2BD9') }};
            --brand-accent:  {{ e($branding['accent_color']  ?? '#F59E0B') }};
            --brand-bg:      {{ e($branding['bg_color']      ?? '#0F172A') }};
            --brand-text:    {{ e($branding['text_color']    ?? '#E2E8F0') }};
        }
        body { background: #f8f9fa; }
        .brand-pane {
            background: var(--brand-primary);
            color: var(--brand-bg);
        }
        .brand-title {
            letter-spacing: .5px;
        }
        .auth-card {
            max-width: 460px;
            width: 100%;
        }
        .btn-brand {
            background: var(--brand-primary);
            color: #fff;
        }
        .btn-brand:hover {
            filter: brightness(.95);
            color: #fff;
        }
        .link-brand {
            color: var(--brand-primary) !important;
            text-decoration: none;
        }
        .link-brand:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="min-vh-100 d-flex">

    <div class="container-fluid g-0 flex-fill">
        <div class="row g-0 min-vh-100">

            {{-- LEFT HALF (hidden on mobile) --}}
            <div class="col-lg-6 d-none d-lg-flex brand-pane align-items-center justify-content-center">
                <div class="text-center px-4">
                    @if(($branding['logo_url'] ?? null))
                        <img src="{{ $branding['logo_url'] }}" alt="Logo"
                             class="mb-4" style="height:64px;width:64px;object-fit:cover;border-radius:.75rem;">
                    @endif

                    <h1 class="display-5 fw-semibold brand-title mb-2">
                        StarCity Starz 
                    </h1>

                    <div class="text-muted">
                        Tenant: <code>{{ $branding['slug'] }}</code>
                    </div>
                </div>
            </div>

            {{-- RIGHT HALF (login; full-width on mobile) --}}
            <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center py-5">
                <div class="auth-card px-4">
                    <div class="text-center mb-4 d-lg-none">
                        {{-- Optional compact header on mobile --}}
                        @if(($branding['logo_url'] ?? null))
                            <img src="{{ $branding['logo_url'] }}" alt="Logo"
                                 class="mb-3" style="height:48px;width:48px;object-fit:cover;border-radius:.5rem;">
                        @endif
                        <h2 class="h4 fw-semibold m-0">{{ $branding['display_name'] }}</h2>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="h5 fw-semibold mb-3 text-center">Sign in</h3>

                            <form method="POST" action="{{ route('login', ['tenant' => $branding['slug']]) }}" novalidate>
                                @csrf

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        class="form-control border border-2 @error('email') is-invalid border-danger @else border-secondary @enderror"
                                        value="{{ old('email') }}"
                                        required
                                        autofocus
                                        autocomplete="username"
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                {{-- Password --}}
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        class="form-control border border-2 @error('password') is-invalid border-danger @else border-secondary @enderror"
                                        required
                                        autocomplete="current-password"
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                {{-- Remember + Forgot --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input border border-2 border-secondary"
                                            type="checkbox"
                                            name="remember"
                                            id="remember"
                                        >
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>

                                    @if (Route::has('password.request'))
                                        <a class="small link-brand"
                                        href="{{ route('password.request', ['tenant' => $branding['slug']]) }}">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>


                                <button type="submit" class="btn btn-brand w-100">
                                    Log in
                                </button>
                            </form>

                            {{-- Optional: register link --}}
                            @if (Route::has('tenant.register'))
                                <div class="text-center mt-3">
                                    <span class="small text-muted">New here?</span>
                                    <a class="small ms-1 link-brand"
                                       href="{{ route('register') }}">
                                       Create an account
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-4 small text-muted">
                        &copy; {{ date('Y') }} StarCity Starz
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
