@extends('layouts.creator')
@section('content')
@php
    use Illuminate\Support\Facades\Auth;

    // $branding is shared (display_name, slug, colors, logo_url)
    $pageTitle = trim($__env->yieldContent('title'));
    $title = $pageTitle
        ? $pageTitle.' · '.$branding['display_name']
        : $branding['display_name'].' · Dashboard';
    $title = 'Super Admin Dashboard';

    /** @var \App\Models\User|null $user */
    $user = Auth::user();

    $temp = request()->route('tenant') ?? request()->segment(1);

    // If you're in tenant scope, this will be the tenant id (string); otherwise null
    $tenantId = function_exists('tenant') ? tenant('id') : null;
@endphp
<div class="container py-4">
  <div class="alert alert-success">Subscription activated! 🎉</div>
  <a class="btn btn-primary" href="{{ route('tenant.user.post-list', ['tenant' => $tenantId]) }}">Go to dashboard</a>
</div>
@endsection
