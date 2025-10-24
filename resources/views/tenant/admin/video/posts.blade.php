@extends('layouts.creator')

@section('content')
<div class="container py-4 py-md-5">

    <h1 class="display-5 fw-bold mb-4">Posts with Videos</h1>

    <!-- Posts Row (3 Columns) -->
    <div class="row g-4">
        @foreach ($posts as $post)
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card shadow-sm">
                    <!-- Video Thumbnail Section (clickable) -->
                    <div class="mb-4">
                        <video class="img-fluid rounded" controls>
                            <source src="{{ $post->getFirstMediaUrl('videos', 'thumb') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    
                    <!-- Card Body Section -->
                    <div class="card-body">
                        <h5 class="card-title">{{ $post->title }}</h5>
                        <p class="card-text">{{ \Str::limit($post->body, 100) }}</p>
                        <a href="{{ route('tenant.posts.show-vids', ['tenant' => $tenantId, 'post' => $post->id]) }}"  
                           class="btn btn-primary">Read More!!
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
