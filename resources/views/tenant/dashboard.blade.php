@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-12 col-md-6 col-lg-4">
        @include('tenant.partials.welcome')
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        @include('tenant.partials.branding-preview')
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        @include('tenant.partials.quick-links')
    </div>
</div>
@endsection

