{{-- resources/views/posts/index.blade.php --}}
@extends('layouts.tenant') {{-- or use your landlord layout if this is central --}}

@section('title', 'All Posts')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">All Posts</h1>
    {{-- optional: add a create button --}}
    {{-- <a href="{{ route('tenant.posts.create', ['tenant' => tenant('id')]) }}" class="btn btn-primary btn-sm">New Post</a> --}}
  </div>

  @includeIf('components.alerts')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif


  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th scope="col" style="width:120px;">ID</th>
              <th scope="col">Title</th>
            </tr>
          </thead>
          <tbody>
            @forelse($posts as $post)
              <tr>
                <td>{{ $post->id }}</td>
                <td class="text-truncate">{{ $post->title }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="2" class="text-center text-muted py-4">No posts found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($posts, 'links'))
        <div class="mt-3">
          {{ $posts->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
