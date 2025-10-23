@extends('layouts.creator')

@section('content')
<div class="container py-4 py-md-5">

    <h1 class="display-5 fw-bold mb-4">{{ $post->title }}</h1>

    <!-- Full Video Player Section -->
    <div class="mb-4">
        <video class="img-fluid rounded" controls>
            <source src="{{ $post->getFirstMediaUrl('videos', 'original') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- Post Description -->
    <div class="mb-4">
        <p>{{ $post->body }}</p>
    </div>

    <!-- Back Button -->
    <div>
        <a href="{{ route('tenant.admin.video.posts', ['tenant' => $tenant->id]) }}" class="btn btn-secondary">
            &larr; Back to Posts
        </a>
    </div>

</div>
@endsection
