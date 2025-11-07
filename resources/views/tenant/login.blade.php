{{-- resources/views/auth/tenant-login.blade.php --}}
@extends('layouts.tenant')

@section('title', $branding['display_name'] ?? 'StarCity Starz')

@section('content')
@php
    $altName = 'StarCity Starz';
    $title   = $branding['display_name'] ?? $altName;
    $slug    = $branding['slug'] ?? request()->segment(1);
@endphp

<style>
  :root{
      --brand-primary: {{ e($branding['primary_color'] ?? '#6C2BD9') }};
      --brand-accent:  {{ e($branding['accent_color']  ?? '#F59E0B') }};
      --brand-bg:      {{ e($branding['bg_color']      ?? '#0F172A') }};
      --brand-text:    {{ e($branding['text_color']    ?? '#E2E8F0') }};
  }
  .brand-pane { background: var(--brand-primary); color: var(--brand-bg); }
  .brand-title { letter-spacing: .5px; }
  .auth-card { max-width: 460px; width: 100%; }
  .btn-brand { background: var(--brand-primary); color: #fff; }
  .btn-brand:hover { filter: brightness(.95); color: #fff; }
  .link-brand { color: var(--brand-primary) !important; text-decoration: none; }
  .link-brand:hover { text-decoration: underline; }
</style>

<div class="container-fluid g-0">
  <div class="row g-0 min-vh-100">
    {{-- LEFT HALF (hidden on mobile) --}}
    <div class="col-lg-6 d-none d-lg-flex brand-pane align-items-center justify-content-center">
      <div class="text-center px-4">
        @if(($branding['logo_url'] ?? null))
          <img src="{{ asset('images/landlord-login-img1.png') }}"
               alt="Login Logo"
               class="mb-4"
               style="height:64px;width:64px;object-fit:cover;border-radius:.75rem;">
        @endif

        <h1 class="display-5 fw-semibold brand-title mb-2">
          {{ $title }}
        </h1>

        @if(!empty($slug))
          <div class="text-muted">
            Tenant: <code>{{ $slug }}</code>
          </div>
        @endif
      </div>
    </div>

    {{-- RIGHT HALF (login; full-width on mobile) --}}
    <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center py-5">
      <div class="auth-card px-4">
        <div class="text-center mb-4 d-lg-none">
          @if(($branding['logo_url'] ?? null))
            <img src="{{ $branding['logo_url'] }}"
                 alt="Logo"
                 class="mb-3"
                 style="height:48px;width:48px;object-fit:cover;border-radius:.5rem;">
          @endif
          <h2 class="h4 fw-semibold m-0">{{ $title }}</h2>
        </div>

        <div class="card shadow-sm">
          <div class="card-body p-4 p-md-5">
            <h3 class="h5 fw-semibold mb-3 text-center">Sign in</h3>

            {{-- Session + validation messages (optional include) --}}
            @if (session('status'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
            @if (session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
            @if ($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif

            <form method="POST" action="{{ route('login', ['tenant' => $slug]) }}" novalidate>
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
                  <input class="form-check-input border border-2 border-secondary" type="checkbox" name="remember" id="remember">
                  <label class="form-check-label" for="remember">Remember me</label>
                </div>
                @if (Route::has('password.request'))
                  <a class="small link-brand" href="{{ route('password.request', ['tenant' => $slug]) }}">
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
                <a class="small ms-1 link-brand" href="{{ route('tenant.register', ['tenant' => $slug]) }}">
                  Create an account
                </a>
              </div>
            @endif
          </div>
        </div>

        <div class="text-center mt-4 small text-muted">
          &copy; {{ date('Y') }} {{ $altName }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
