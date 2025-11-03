@php
    // Assumes $branding is shared (display_name, slug, branding colors)
    $title = $branding['display_name'] . ' · Welcome';

    use Illuminate\Support\Facades\File;

    // Read images from public/images/carousel
    $dir = public_path('images/carousel');
    $images = is_dir($dir)
        ? collect(File::files($dir))
            ->filter(fn($f) => in_array(strtolower($f->getExtension()), ['jpg','jpeg','png','gif','webp']))
            ->sortBy(fn($f) => $f->getFilename()) // stable order
            ->map(fn($f) => asset('images/carousel/'.$f->getFilename()))
            ->values()
        : collect();
@endphp

<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --brand-primary: {{ $branding['primary_color'] }};
            --brand-accent:  {{ $branding['accent_color'] }};
            --brand-bg:      {{ $branding['bg_color'] }};
            --brand-text:    {{ $branding['text_color'] }};
        }
        body { background: #f8f9fa; }

        .brand-pane { background:#000; color:var(--brand-bg); }
        .brand-title { letter-spacing:.5px; }

        .auth-card { max-width: 460px; width: 100%; }
        .btn-brand { background: var(--brand-primary); color: #fff; }
        .btn-brand:hover { filter: brightness(.95); color: #fff; }
        .link-brand { color: var(--brand-primary) !important; text-decoration: none; }
        .link-brand:hover { text-decoration: underline; }
        .muted-text { color: var(--brand-bg) !important; }

        /* === Responsive Carousel ===
           - Uses aspect-ratio so images scale with width
           - No fixed width/height; fills container
        */
        .carousel-frame {
            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1; /* square on very small screens */
            overflow: hidden;
            background: #000;
            border-radius: .75rem;
        }
        @media (min-width: 576px) { .carousel-frame { aspect-ratio: 4 / 3; } }
        @media (min-width: 768px) { .carousel-frame { aspect-ratio: 16 / 9; } }
        /* On lg+ in the left pane, give it a nice presence */
        @media (min-width: 992px) { .carousel-frame { max-width: 620px; } }
        @media (min-width: 1200px) { .carousel-frame { max-width: 720px; } }

        .slide {
            position: absolute; inset: 0;
            opacity: 0; transition: opacity .6s ease-in-out;
            display: flex;
        }
        .slide.active { opacity: 1; }
        .slide img {
            width: 100%; height: 100%;
            object-fit: cover; /* fills frame nicely */
        }
        .dots {
            position: absolute; left: 50%; bottom: 10px; transform: translateX(-50%);
            display: flex; gap: 8px; z-index: 2;
        }
        .dot {
            width: 10px; height: 10px; border-radius: 9999px;
            background: rgba(255,255,255,.55);
        }
        .dot.active { background: #fff; }
    </style>
</head>
<body class="min-vh-100 d-flex">

    <div class="container-fluid g-0 flex-fill">
        <div class="row g-0 min-vh-100">

            {{-- MOBILE HEADER + CAROUSEL (visible on < lg) --}}
            <div class="col-12 d-lg-none py-4 px-3">
                <div class="text-center mb-3">
                    @if(($branding['logo_url'] ?? null))
                        <img src="{{ $branding['logo_url'] }}" alt="Logo"
                             class="mb-2" style="height:48px;width:48px;object-fit:cover;border-radius:.5rem;">
                    @endif
                    <h1 class="h4 fw-semibold m-0">{{ $branding['display_name'] }}</h1>
                    <div class="text-muted small">Tenant: <code>{{ $branding['slug'] }}</code></div>
                </div>

                @if($images->isNotEmpty())
                    <div id="rotator-mobile" class="carousel-frame mx-auto">
                        @foreach($images as $i => $src)
                            <div class="slide {{ $i === 0 ? 'active' : '' }}">
                                <img src="{{ $src }}" alt="slide {{ $i+1 }}" loading="lazy" />
                            </div>
                        @endforeach
                        <div class="dots">
                            @foreach($images as $i => $src)
                                <div class="dot {{ $i === 0 ? 'active' : '' }}"></div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- LEFT PANE (large screens) --}}
            <div class="col-lg-6 d-none d-lg-flex brand-pane align-items-center justify-content-center">
                <div class="text-center px-4">
                    @if($images->isNotEmpty())
                        <div id="rotator" class="carousel-frame mx-auto mb-4">
                            @foreach($images as $i => $src)
                                <div class="slide {{ $i === 0 ? 'active' : '' }}">
                                    <img src="{{ $src }}" alt="slide {{ $i+1 }}" loading="lazy" />
                                </div>
                            @endforeach
                            <div class="dots">
                                @foreach($images as $i => $src)
                                    <div class="dot {{ $i === 0 ? 'active' : '' }}"></div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <h1 class="display-6 fw-semibold brand-title mb-2">
                        {{ $branding['display_name'] }}
                    </h1>
                    <div class="text-muted muted-text">
                        Tenant: <code>{{ $branding['slug'] }}</code>
                    </div>
                </div>
            </div>

            {{-- RIGHT PANE (form; full-width on mobile) --}}
            <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center py-5 px-3 px-md-4">
                <div class="auth-card w-100">
                    <div class="card shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="h5 fw-semibold mb-3 text-center">Sign in</h3>

                            <form method="POST" action="{{ route('login', ['tenant' => $branding['slug']]) }}" novalidate>
                                @csrf

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input id="email" type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required autofocus autocomplete="username">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password --}}
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required autocomplete="current-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Remember + Forgot --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
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

                            @if (Route::has('tenant.register'))
                                <div class="text-center mt-3">
                                    <span class="small text-muted">New here?</span>
                                    <a class="small ms-1 link-brand"
                                       href="{{ route('tenant.register', ['tenant' => $branding['slug']]) }}">
                                        Create an account
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-4 small text-muted">
                        &copy; {{ date('Y') }} {{ $branding['display_name'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Minimal rotator JS (reused for desktop & mobile instances) --}}
    <script>
        (function initRotator(id) {
            const root = document.getElementById(id);
            if (!root) return;
            const slides = root.querySelectorAll('.slide');
            const dots   = root.querySelectorAll('.dot');
            if (!slides.length) return;

            let i = 0, delay = 3000;

            function go(next) {
                slides[i].classList.remove('active');
                dots[i]?.classList.remove('active');
                i = next;
                slides[i].classList.add('active');
                dots[i]?.classList.add('active');
            }

            setInterval(() => go((i + 1) % slides.length), delay);
        })('rotator');

        (function initMobile() {
            const el = document.getElementById('rotator-mobile');
            if (!el) return;
            // clone rotator logic for mobile instance
            const slides = el.querySelectorAll('.slide');
            const dots   = el.querySelectorAll('.dot');
            if (!slides.length) return;
            let i = 0, delay = 3000;
            function go(n){ slides[i].classList.remove('active'); dots[i]?.classList.remove('active'); i=n; slides[i].classList.add('active'); dots[i]?.classList.add('active'); }
            setInterval(() => go((i+1)%slides.length), delay);
        })();
    </script>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
