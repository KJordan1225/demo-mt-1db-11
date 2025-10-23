@extends('layouts.creator')

@section('content')
<div class="container py-4 py-md-5">

    <h1 class="display-5 fw-bold mb-4">Posts with Images</h1>

    <!-- Posts Row -->
    <div class="row g-4">
        @foreach ($posts as $post)
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card shadow-sm">
                    <!-- Image Section -->                                      
                    <a href="{{ route('tenant.posts.show', ['tenant' => $tenantId, 'post' => $post->id]) }}">
                        <img src="{{ $post->getFirstMediaUrl('images', 'medium') }}" class="card-img-top" alt="{{ $post->title }}" style="object-fit: cover; height: 200px;">
                    </a>
                    <!-- Card Body Section -->
                    <div class="card-body">
                        <h5 class="card-title">{{ $post->title }}</h5>
                        <p class="card-text">{{ \Str::limit($post->body, 100) }}</p>
                        <a href="{{ route('tenant.posts.show', ['tenant' => $tenantId, 'post' => $post->id]) }}"  class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
