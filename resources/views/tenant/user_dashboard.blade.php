@extends('layouts.tenant')

@section('content')

@php
    $tenant = tenant();    
@endphp

<form action="{{ route('usersubscription.create', ['tenant' => $tenant->id]) }}" method="POST">
    @csrf
    <!-- Hidden input to pass tenant_id as a request parameter -->
    <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">
    <div>Auth ID: {{ auth()->id() }}</div>
    <button type="submit" class="btn btn-primary text-white">
        Subscribe to {{ $tenant->name }}
    </button>
</form>

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


@endsection