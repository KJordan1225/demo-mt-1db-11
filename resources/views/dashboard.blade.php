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
                    <form method="POST" action="{{ route('creator.pricing.update', ['tenant' => tenant('id')]) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium">Plan Name</label>
                            <input name="plan_name" class="mt-1 w-full rounded border-gray-300" value="{{ old('plan_name', tenant()->plan_name) }}" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Currency</label>
                                <input name="plan_currency" class="mt-1 w-full rounded border-gray-300" value="{{ old('plan_currency', tenant()->plan_currency ?? 'usd') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Amount (in cents)</label>
                                <input name="plan_amount_cents" type="number" min="100" class="mt-1 w-full rounded border-gray-300" value="{{ old('plan_amount_cents', tenant()->plan_amount_cents) }}" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Interval</label>
                            <select name="plan_interval" class="mt-1 w-full rounded border-gray-300">
                                @foreach (['day','week','month','year'] as $ivl)
                                    <option value="{{ $ivl }}" {{ (tenant()->plan_interval ?? 'month') === $ivl ? 'selected' : '' }}>
                                        {{ ucfirst($ivl) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="px-4 py-2 rounded bg-indigo-600 text-white">Save Pricing</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
