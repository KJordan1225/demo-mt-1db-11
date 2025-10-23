@extends('layouts.creator')

@section('content')
<div class="container py-4 py-md-5">

    {{-- Post Title --}}
    <h1 class="display-5 fw-bold mb-4">{{ $post->title }}</h1>

    {{-- Post Image --}}
    <div class="mb-4">
        <img src="{{ $post->getFirstMediaUrl('images', 'original') }}" 
             class="img-fluid rounded" alt="{{ $post->title }}" />
    </div>

    {{-- Post Description --}}
    <div class="mb-4">
        <p>{{ $post->body }}</p>
    </div>

    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('tenant.admin.image.posts', ['tenant' => $tenant->id]) }}" class="btn btn-secondary">
            &larr; Back to Posts
        </a>
    </div>

</div>
@endsection
