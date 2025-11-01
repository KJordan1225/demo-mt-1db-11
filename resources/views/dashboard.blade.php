@extends('layouts.landlord')

@section('content')
<div class="container my-4">

    {{-- Page heading (was <x-slot name="header">) --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h4 mb-0">{{ __('Dashboard') }}</h2>
    </div>

    {{-- Flash messages (success/error) + validation errors --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="fw-semibold mb-1">{{ __('Please fix the following:') }}</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li class="mb-1">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
    @endif

    {{-- Main content --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">                
                <div class="card-body text-muted">
                    @if (tenant('id'))
                    <form method="POST" action="{{ route('creator.pricing.update', ['tenant' => tenant('id')]) }}" class="w-100">
                        @csrf

                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h2 class="h5 mb-1">Update Subscription Plan</h2>
                                <p class="text-muted mb-4">Set the name, currency, price (in cents), and billing interval.</p>

                                {{-- Plan Name --}}
                                <div class="mb-3">
                                    <label for="plan_name" class="form-label">Plan Name</label>
                                    <input
                                        id="plan_name"
                                        name="plan_name"
                                        type="text"
                                        required
                                        autocomplete="off"
                                        value="{{ old('plan_name', tenant()->plan_name) }}"
                                        class="form-control @error('plan_name') is-invalid @enderror"
                                        placeholder="e.g., Pro Creator"
                                    >
                                    @error('plan_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Currency --}}
                                <div class="mb-3">
                                    <label for="plan_currency" class="form-label">Currency</label>
                                    <input
                                        id="plan_currency"
                                        name="plan_currency"
                                        type="text"
                                        value="{{ old('plan_currency', tenant()->plan_currency ?? 'usd') }}"
                                        class="form-control text-uppercase @error('plan_currency') is-invalid @enderror"
                                        placeholder="usd"
                                    >
                                    <div class="form-text">Use a three-letter ISO code (e.g., USD, EUR, GBP).</div>
                                    @error('plan_currency')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Amount (in cents) --}}
                                <div class="mb-3">
                                    <label for="plan_amount_cents" class="form-label">Amount (in cents)</label>
                                    <input
                                        id="plan_amount_cents"
                                        name="plan_amount_cents"
                                        type="number"
                                        min="100"
                                        step="1"
                                        required
                                        value="{{ old('plan_amount_cents', tenant()->plan_amount_cents) }}"
                                        class="form-control @error('plan_amount_cents') is-invalid @enderror"
                                        placeholder="e.g., 999 for $9.99"
                                    >
                                    <div class="form-text">Minimum 100 (i.e., $1.00).</div>
                                    @error('plan_amount_cents')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Interval --}}
                                <div class="mb-4">
                                    <label for="plan_interval" class="form-label">Interval</label>
                                    <select
                                        id="plan_interval"
                                        name="plan_interval"
                                        class="form-select @error('plan_interval') is-invalid @enderror"
                                    >
                                        @foreach (['day','week','month','year'] as $ivl)
                                            <option value="{{ $ivl }}" {{ (tenant()->plan_interval ?? 'month') === $ivl ? 'selected' : '' }}>
                                                {{ ucfirst($ivl) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('plan_interval')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Submit --}}
                                <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                    <span>Save Pricing</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-arrow-right-short" viewBox="0 0 16 16" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3.0 3a.5.5 0 0 1 0 .708l-3.0 3a.5.5 0 1 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>


                    @else
                        You're logged in!
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
