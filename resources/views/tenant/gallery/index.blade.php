@extends('layouts.tenant')

@section('title', 'Image Gallery')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-4">Image Gallery</h1>

  @if($media->isEmpty())
    <div class="alert alert-info">No images found.</div>
  @else
    @foreach ($media->chunk(3) as $row)
      <div class="row g-3 mb-3">
        @foreach ($row as $m)
          <div class="col-12 col-sm-6 col-md-4">
            <div class="card shadow-sm h-100">
              <div class="ratio ratio-1x1">
                <img
                  src="{{ method_exists($m, 'getUrl') ? $m->getUrl() : asset($m->getPath()) }}"
                  alt="{{ $m->name }}"
                  class="w-100 h-100"
                  style="object-fit: cover;"
                >
              </div>
              <div class="card-body p-2">
                <div class="small text-muted text-truncate">{{ $m->name }}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endforeach

    <div class="d-flex justify-content-center mt-4">
      {{ $media->withQueryString()->links() }}
    </div>
  @endif
</div>
@endsection
