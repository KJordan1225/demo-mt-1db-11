{{-- resources/views/central/admin-tasks.blade.php --}}
@extends('layouts.landlord')

@section('title', 'Admin Tasks')

@section('content')
@php
    // If your route needs a tenant parameter, pass a model or id via $tenant
    $priceSetupUrl = isset($tenant)
        ? route('central.price.setup', $tenant)   // route-model binding friendly
        : route('central.price.setup');           // no param needed
@endphp

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-8 col-lg-5">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5 text-center">
          <h2 class="h4 fw-bold mb-4">Admin Tasks</h2>
        @php
            // Get the first segment of the current URL (e.g. "tenant-name" from /tenant-name/dashboard)
            $firstSegment = request()->segment(1);
        @endphp  
          <a href="{{ route('central.price.setup', ['tenant' => $firstSegment]) }}" class="btn btn-outline-primary btn-lg">
            Set My Subscription Price
          </a>
          <a href="{{ route('tenant.carousel.post.image.upload', ['tenant' => $firstSegment]) }}" class="btn btn-outline-primary btn-lg">
            Upload Preview Images for Carousel
          </a>
          <a href="{{ route('tenant.post.image.upload', ['tenant' => $firstSegment]) }}" class="btn btn-outline-primary btn-lg">
            Upload Media (Posts)
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
