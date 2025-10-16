{{-- resources/views/landlord/tenants/create.blade.php --}}
@extends('layouts.landlord') {{-- change to your layout --}}

@section('title', 'Create Tenant')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 m-0">Create Tenant</h1>
        <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('guest.microsite.configure.store') }}" class="row g-4">
        @csrf

        {{-- Tenant ID / Slug (primary key used by stancl/tenancy) --}}
        <div class="col-12">
            <label for="id" class="form-label">Tenant ID / Slug</label>
			<p class="text-muted small mt-2">
				Tip: Pick a short, memorable name—avoid spaces and special characters.
				Instead of spaces, use underscores. Make your Tenant ID/Slug all lowercase.
			</p>
            <input type="text" id="id" name="id"
                   class="form-control @error('id') is-invalid @enderror"
                   value="{{ old('id') }}" required>
            <div class="form-text">e.g. <code>alpha</code>, <code>bravo-studios</code>. Used in the URL path.</div>
            @error('id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Display name --}}
        <div class="col-12">
            <label for="display_name" class="form-label">Display Name</label>
            <input type="text" id="display_name" name="display_name"
                   class="form-control @error('display_name') is-invalid @enderror"
                   value="{{ old('display_name') }}" required>
            @error('display_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Password/Confirmation --}}
        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                required
                autocomplete="new-password"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                required
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        {{-- Logo URL (optional) --}}
        <div class="col-12">
            <label for="logo_url" class="form-label">Logo URL (optional)</label>
            <input type="url" id="logo_url" name="logo_url"
                   class="form-control @error('logo_url') is-invalid @enderror"
                   value="{{ old('logo_url') }}">
            @error('logo_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Branding colors --}}
        <div class="col-12 col-lg-3">
            <label for="primary_color" class="form-label">Primary Color</label>
            <input type="color" id="primary_color" name="primary_color"
                   class="form-control form-control-color @error('primary_color') is-invalid @enderror"
                   value="{{ old('primary_color', '#4f46e5') }}" title="Choose color">
            @error('primary_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-lg-3">
            <label for="accent_color" class="form-label">Accent Color</label>
            <input type="color" id="accent_color" name="accent_color"
                   class="form-control form-control-color @error('accent_color') is-invalid @enderror"
                   value="{{ old('accent_color', '#22c55e') }}" title="Choose color">
            @error('accent_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-lg-3">
            <label for="bg_color" class="form-label">Background Color</label>
            <input type="color" id="bg_color" name="bg_color"
                   class="form-control form-control-color @error('bg_color') is-invalid @enderror"
                   value="{{ old('bg_color', '#ffffff') }}" title="Choose color">
            @error('bg_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-lg-3">
            <label for="text_color" class="form-label">Text Color</label>
            <input type="color" id="text_color" name="text_color"
                   class="form-control form-control-color @error('text_color') is-invalid @enderror"
                   value="{{ old('text_color', '#111827') }}" title="Choose color">
            @error('text_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <p class="text-muted small mt-2">
				Click the color boxes to pick a color. These will be used in the microsite's login page and other areas.
			</p>
        </div>

        {{-- Preview --}}
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded" style="width:28px;height:28px;background:{{ old('primary_color', '#4f46e5') }};"></div>
                        <div class="rounded" style="width:28px;height:28px;background:{{ old('accent_color', '#22c55e') }};"></div>
                        <div class="rounded border" style="width:28px;height:28px;background:{{ old('bg_color', '#ffffff') }};"></div>
                        <div class="ms-auto small text-muted">Preview of chosen colors</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create Tenant</button>
            <a href="{{ route('guest.home') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </form>
</div>
@endsection
