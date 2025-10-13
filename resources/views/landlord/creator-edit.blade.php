{{-- resources/views/landlord/tenants/edit.blade.php --}}
@extends('layouts.landlord') {{-- change to your layout --}}

@section('title', 'Edit Tenant')

@section('content')
@php
    // Safely read nested JSON from tenants.data
    $displayName  = old('display_name', data_get($tenant, 'data.display_name', ''));
    $logoUrl      = old('logo_url', data_get($tenant, 'data.branding.logo_url'));
    $primaryColor = old('primary_color', data_get($tenant, 'data.branding.primary_color', '#4f46e5'));
    $accentColor  = old('accent_color',  data_get($tenant, 'data.branding.accent_color',  '#22c55e'));
    $bgColor      = old('bg_color',      data_get($tenant, 'data.branding.bg_color',      '#ffffff'));
    $textColor    = old('text_color',    data_get($tenant, 'data.branding.text_color',    '#111827'));
@endphp

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 m-0">Edit Tenant</h1>
        <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">Back</a>
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

    <form method="PUT" action="{{ route('tenants.update', $tenant->id) }}" class="row g-4">
        @csrf
        @method('PUT')

        {{-- Tenant ID / Slug (primary key) --}}
        <div class="col-12 col-md-6">
            <label for="id" class="form-label">Tenant ID / Slug</label>
            <input type="text" id="id" name="id"
                   class="form-control"
                   value="{{ $tenant->id }}" readonly>
            <div class="form-text">Primary key; typically not editable.</div>
        </div>

        {{-- Display name --}}
        <div class="col-12 col-md-6">
            <label for="display_name" class="form-label">Display Name</label>
            <input type="text" id="display_name" name="display_name"
                   class="form-control @error('display_name') is-invalid @enderror"
                   value="{{ $displayName }}" required>
            @error('display_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Logo URL (optional) --}}
        <div class="col-12">
            <label for="logo_url" class="form-label">Logo URL (optional)</label>
            <input type="url" id="logo_url" name="logo_url"
                   class="form-control @error('logo_url') is-invalid @enderror"
                   value="{{ $logoUrl }}">
            @error('logo_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Branding colors --}}
        <div class="col-12 col-lg-3">
            <label for="primary_color" class="form-label">Primary Color</label>
            <input type="color" id="primary_color" name="primary_color"
                   class="form-control form-control-color @error('primary_color') is-invalid @enderror"
                   value="{{ $primaryColor }}" title="Choose color">
            @error('primary_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-lg-3">
            <label for="accent_color" class="form-label">Accent Color</label>
            <input type="color" id="accent_color" name="accent_color"
                   class="form-control form-control-color @error('accent_color') is-invalid @enderror"
                   value="{{ $accentColor }}" title="Choose color">
            @error('accent_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-lg-3">
            <label for="bg_color" class="form-label">Background Color</label>
            <input type="color" id="bg_color" name="bg_color"
                   class="form-control form-control-color @error('bg_color') is-invalid @enderror"
                   value="{{ $bgColor }}" title="Choose color">
            @error('bg_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-lg-3">
            <label for="text_color" class="form-label">Text Color</label>
            <input type="color" id="text_color" name="text_color"
                   class="form-control form-control-color @error('text_color') is-invalid @enderror"
                   value="{{ $textColor }}" title="Choose color">
            @error('text_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Preview --}}
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded" style="width:28px;height:28px;background:{{ $primaryColor }};"></div>
                        <div class="rounded" style="width:28px;height:28px;background:{{ $accentColor }};"></div>
                        <div class="rounded border" style="width:28px;height:28px;background:{{ $bgColor }};"></div>
                        <div class="ms-auto small text-muted">Preview of current colors</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
