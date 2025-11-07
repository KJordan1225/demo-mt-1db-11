{{-- resources/views/layouts/tenant-with-sidebar.blade.php --}}
@php
    use Stancl\Tenancy\Tenancy;
    use App\Models\Tenant;

    $pageTitle = trim($__env->yieldContent('title'));
    $title = $pageTitle
        ? $pageTitle . ' · ' . $branding['display_name']
        : $branding['display_name'] . ' · Dashboard';

    $tenancy = app(Tenancy::class);
    $tenantId = null;

    if (function_exists('tenant') && tenant()) {
        $tenantId = tenant('id');
    } else {
        $tenantKey = request()->route('tenant');
        if ($tenantKey) {
            $tenant = Tenant::find($tenantKey) ?? Tenant::where('id', $tenantKey)->first();
            if ($tenant) {
                $tenancy->initialize($tenant);
                $tenantId = tenant('id');
            }
        }
    }
    $tenantSlug = $branding['slug'] ?? $tenantId ?? request()->segment(1);
@endphp

<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{
            --brand-primary: {{ $branding['primary_color'] }};
            --brand-accent:  {{ $branding['accent_color'] }};
            --brand-bg:      {{ $branding['bg_color'] }};
            --brand-text:    {{ $branding['text_color'] }};
        }
        body { background: var(--brand-bg); color: var(--brand-text); }
        .brand-topbar { background: var(--brand-primary); color: #fff; }
        .navbar-brand img { height: 32px; width: 32px; object-fit: cover; border-radius: .5rem; }

        /* Desktop sidebar */
        .sidebar {
            background: #0b0b0b;
            min-height: calc(100vh - 56px);
            border-right: 1px solid rgba(255,255,255,.08);
            position: sticky;
            top: 56px;
            padding-bottom: 2rem;
        }
        .sidebar .nav-link { color: #cfd3d8; }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,.06); }
        .sidebar .nav-heading { color: #9aa2ab; font-size: .75rem; letter-spacing: .08em; text-transform: uppercase; }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- Topbar --}}
    <header class="brand-topbar">
        <nav class="navbar navbar-expand-lg" style="background: transparent;">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center gap-2 text-white"
                   href="{{ route('tenant.dashboard', ['tenant' => $tenantSlug]) }}">
                    @if(!empty($branding['logo_url']))
                        <img src="{{ $branding['logo_url'] }}" alt="Logo">
                    @endif
                    <span class="fw-semibold">{{ $branding['display_name'] }}</span>
                </a>

                <div class="d-flex ms-auto">
                    {{-- Sidebar hamburger: visible only on small screens --}}
                    <button class="btn btn-outline-light d-inline-flex d-md-none me-2"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#tenantSidebarOffcanvas"
                            aria-controls="tenantSidebarOffcanvas"
                            aria-label="Open menu">
                        <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                    </button>

                    {{-- Optional top-nav collapse (links you inject via @section('nav')) --}}
                    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#tenantTopNav" aria-controls="tenantTopNav" aria-expanded="false"
                            aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="tenantTopNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @yield('nav')
                    </ul>

                    @auth
                        <span class="text-white-50 small me-2 d-none d-sm-inline">
                            {{ auth()->user()->name }}
                        </span>
                        <form method="POST" action="{{ route('landlord.logout') }}" class="m-0">
                            @csrf
                            <button class="btn btn-sm btn-light" type="submit">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('landlord.login') }}" class="btn btn-light btn-sm">Login</a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    {{-- Mobile offcanvas sidebar --}}
    <div class="offcanvas offcanvas-start text-bg-dark d-md-none"
         tabindex="-1"
         id="tenantSidebarOffcanvas"
         aria-labelledby="tenantSidebarOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="tenantSidebarOffcanvasLabel">Menu</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="nav-heading mb-2">Navigation</div>
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('tenant.dashboard')) active @endif"
                       href="{{ route('tenant.dashboard', ['tenant' => $tenantSlug]) }}">
                        Dashboard
                    </a>
                </li>
                {{-- Child views can inject extra links here (same as desktop) --}}
                @yield('sidebar')
            </ul>
        </div>
    </div>

    {{-- Main with left sidebar on md+ --}}
    <main class="flex-grow-1">
        <div class="container-fluid">
            <div class="row">
                {{-- Desktop sidebar (hidden on < md) --}}
                <nav class="col-12 col-md-3 col-lg-2 sidebar p-3 d-none d-md-block">
                    <div class="mb-2">
                        <div class="nav-heading mb-2">Navigation</div>
                        <ul class="nav nav-pills flex-column gap-1">
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('tenant.dashboard')) active @endif"
                                   href="{{ route('tenant.dashboard', ['tenant' => $tenantSlug]) }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('tenant.dashboard')) active @endif"
                                   href="{{ route('tenant.carousel.post.image.upload', ['tenant' => $tenantSlug]) }}">
                                    Upload Media (Carousel)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('tenant.dashboard')) active @endif"
                                   href="{{ route('tenant.post.image.upload', ['tenant' => $tenantSlug]) }}">
                                    Upload Media (Posts)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('tenant.dashboard')) active @endif"
                                   href="{{ route('tenant.gallery.index', ['tenant' => $tenantSlug]) }}">
                                    View Image Gallery
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('tenant.dashboard')) active @endif"
                                   href="{{ route('tenant.video.gallery.index', ['tenant' => $tenantSlug]) }}">
                                    View Video Gallery
                                </a>
                            </li>
                            @yield('sidebar')
                        </ul>
                    </div>
                </nav>

                {{-- Page content --}}
                <section class="col-12 col-md-9 col-lg-10 py-4">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </section>
            </div>
        </div>
    </main>

    <footer class="mt-auto py-3">
        <div class="container text-center small text-muted">
            {{-- Footer content (optional) --}}
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
