{{-- resources/views/media/upload.blade.php --}}
@extends('layouts.creator')

@section('content')
<div class="container py-4">

  <h1 class="h4 mb-4">Upload Media</h1>

  {{-- Flash + errors --}}
  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form
    action="{{ route('tenant.admin.media.store', ['tenant' => tenant('id')]) }}" {{-- update to your route --}}
    method="POST"
    enctype="multipart/form-data"
    id="media-upload-form"
  >
    @csrf

    {{-- Title / optional caption --}}
    <div class="mb-3">
      <label for="title" class="form-label">Title (optional)</label>
      <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}">
    </div>

    {{-- Media description --}}
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="body" rows="4" class="form-control">{{ old('body') }}</textarea>
    </div>


    {{-- Publish at --}}
    <div class="mb-3">
        <label class="form-label">Publish At</label>
        <input type="datetime-local" name="published_at"
            value="{{ old('published_at') }}" class="form-control">
    </div>

    {{-- Media type selector --}}
    <div class="mb-3">
      <label for="type" class="form-label">Media Type</label>
      <select name="type" id="type" class="form-select" required>
        <option value="" disabled {{ old('type') ? '' : 'selected' }}>Choose…</option>
        <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>Image</option>
        <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Video</option>
      </select>
      @error('type') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    {{-- Image upload (Spatie: will add to "images" or "featured_image" as you choose) --}}
    <div class="mb-3" id="image-input" style="display:none;">
      <label for="image" class="form-label">Upload Image</label>
      <input
        type="file"
        id="image"
        name="image"
        class="form-control @error('image') is-invalid @enderror"
        accept="image/*"
      >
      <div class="form-text">Accepted: jpg, jpeg, png, gif, webp.</div>
      @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Video upload (Spatie: will add to "videos") --}}
    <div class="mb-3" id="video-input" style="display:none;">
      <label for="video" class="form-label">Upload Video</label>
      <input
        type="file"
        id="video"
        name="video"
        class="form-control @error('video') is-invalid @enderror"
        accept="video/*"
      >
      <div class="form-text">Accepted: mp4, webm, mov (adjust in validation if needed).</div>
      @error('video') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-primary">Upload</button>
  </form>
</div>

{{-- Toggle logic --}}
<script>
(function () {
  const selectEl = document.getElementById('type');
  const imageGroup = document.getElementById('image-input');
  const videoGroup = document.getElementById('video-input');
  const imageInput = document.getElementById('image');
  const videoInput = document.getElementById('video');

  function toggle() {
    const val = selectEl.value;
    if (val === 'image') {
      imageGroup.style.display = '';
      videoGroup.style.display = 'none';
      videoInput.value = '';
    } else if (val === 'video') {
      videoGroup.style.display = '';
      imageGroup.style.display = 'none';
      imageInput.value = '';
    } else {
      imageGroup.style.display = 'none';
      videoGroup.style.display = 'none';
      imageInput.value = '';
      videoInput.value = '';
    }
  }

  selectEl.addEventListener('change', toggle);
  toggle(); // initialize with old('type') state
})();
</script>
@endsection
