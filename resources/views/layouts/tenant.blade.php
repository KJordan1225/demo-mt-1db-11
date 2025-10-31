@php
    // $branding is shared by your provider
    $pageTitle = trim($__env->yieldContent('title'));
    $title = $pageTitle
        ? $pageTitle . ' · ' . $branding['display_name']
        : $branding['display_name'] . ' · Dashboard';
@endphp

<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    {{-- Bootstrap 5 (CDN). If you use Vite + npm, replace with your @vite assets. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --brand-primary: {{ $branding['primary_color'] }};
            --brand-accent:  {{ $branding['accent_color'] }};
            --brand-bg:      {{ $branding['bg_color'] }};
            --brand-text:    {{ $branding['text_color'] }};
        }
        body { background: var(--brand-bg); color: var(--brand-text); }
        .brand-btn { background: var(--brand-primary); color: #fff; }
        .brand-link { color: var(--brand-primary) !important; }
        .brand-accent { color: var(--brand-accent) !important; }
        .brand-topbar { background: var(--brand-primary); color: #fff; }
        .navbar-brand img { height: 32px; width: 32px; object-fit: cover; border-radius: .5rem; }
        .tenant-badge { opacity: .9; }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- Topbar / Navbar --}}
    <header class="brand-topbar">
        <nav class="navbar navbar-expand-lg" style="background: transparent;">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="{{ route('tenant.dashboard', ['tenant' => $branding['slug']]) }}">
                    @if($branding['logo_url'])
                        <img src="{{ $branding['logo_url'] }}" alt="Logo">
                    @endif
                    <span class="fw-semibold">{{ $branding['display_name'] }}</span>
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#tenantNav" aria-controls="tenantNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                </button>

                <div class="collapse navbar-collapse" id="tenantNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="{{ route('tenant.dashboard', ['tenant' => $branding['slug']]) }}">Dashboard</a>
                        </li>
                        @yield('nav') {{-- child views can inject extra <li> items --}}
                    </ul>

                    <span class="navbar-text tenant-badge text-white-50">
                        Tenant: <strong>{{ $branding['slug'] }}</strong>
                    </span>
                    @auth
                    {{-- Tenant badge --}}
                    <span class="text-white-50 small d-none d-sm-inline">
                        Logged in: <strong>{{ auth()->user()->name }}</strong>
                    </span>
                    <form method="POST" action="{{ route('landlord.logout') }}" class="m-0">
                        @csrf
                        <button class="btn btn-sm btn-light" type="submit">Log out</button>
                    </form>
                    @else
                    <span class="text-white-50 small d-none d-sm-inline">
                        Not Logged In
                    </span> 
                    <a href="{{ route('landlord.login') }}" class="btn btn-primary btn-sm">
                        Login
                    </a>                   
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    {{-- Content --}}
    <main class="flex-grow-1 py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    {{-- Footer (optional) --}}
    <footer class="mt-auto py-3">
        <div class="container text-center small text-muted">
            {{-- add footer content if needed --}}
        </div>
    </footer>

    {{-- Bootstrap JS (CDN). If using Vite, include your compiled app.js instead. --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
