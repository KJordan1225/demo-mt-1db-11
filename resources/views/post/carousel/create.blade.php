{{-- resources/views/tenant/posts/create.blade.php --}}
@extends('layouts.tenant')

@section('title', 'Create Post')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h1 class="h4 mb-4">Create Post</h1>

          <form method="POST"
                action="{{ route('tenant.carousel.posts.store', ['tenant' => tenant('id')]) }}"
                enctype="multipart/form-data"
                novalidate>
            @csrf

            {{-- Title --}}
            <div class="mb-3">
              <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
              <input id="title"
                     type="text"
                     name="title"
                     class="form-control @error('title') is-invalid @enderror"
                     value="{{ old('title') }}"
                     required>
              @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Description --}}
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea id="description"
                        name="description"
                        rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Optional">{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>            

            {{-- IMAGE input -> Spatie Media Library (collection: carousel_samples) --}}
            <div id="image-input" class="mb-3">
              <label for="images" class="form-label">Images</label>
              <input id="images"
                     type="file"
                     name="images[]"
                     class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                     accept="image/*"
                     multiple>
              <div class="form-text">
                Upload one or more images. They will be saved to the
                <code>carousel_samples</code> media collection.
              </div>
              @error('images')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              @error('images.*')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            
            {{-- tenant_id hidden field --}}
            <input type="hidden" name="tenant" value="{{ tenant('id') }}">

            {{-- Simple client-side previews --}}
            <div id="preview" class="row g-2 mb-3"></div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Save Post</button>
              <a href="#" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function () {
    const mediaType = document.getElementById('media_type');
    const imageGroup = document.getElementById('image-input');
    const videoGroup = document.getElementById('video-input');
    const imageInput = document.getElementById('images');
    const videoInput = document.getElementById('videos');
    const preview = document.getElementById('preview');

    function toggleGroups() {
      const val = mediaType.value;
      if (val === 'video') {
        imageGroup.classList.add('d-none');
        videoGroup.classList.remove('d-none');
        // clear previews
        preview.innerHTML = '';
        // clear opposite input selection
        imageInput && (imageInput.value = '');
      } else {
        videoGroup.classList.add('d-none');
        imageGroup.classList.remove('d-none');
        preview.innerHTML = '';
        videoInput && (videoInput.value = '');
      }
    }

    mediaType?.addEventListener('change', toggleGroups);
    // initialize on load (respect old() selection)
    toggleGroups();

    // Image previews
    imageInput?.addEventListener('change', () => {
      preview.innerHTML = '';
      const files = Array.from(imageInput.files || []);
      files.forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const url = URL.createObjectURL(file);
        const col = document.createElement('div');
        col.className = 'col-4 col-sm-3 col-md-2';
        col.innerHTML = `
          <div class="ratio ratio-1x1 border rounded overflow-hidden">
            <img src="${url}" alt="" style="object-fit:cover;">
          </div>`;
        preview.appendChild(col);
      });
    });

    // Simple video filename list (no heavy preview)
    videoInput?.addEventListener('change', () => {
      preview.innerHTML = '';
      const files = Array.from(videoInput.files || []);
      files.forEach(file => {
        const col = document.createElement('div');
        col.className = 'col-12';
        col.innerHTML = `
          <div class="border rounded p-2 small">
            <strong>Video:</strong> ${file.name}
          </div>`;
        preview.appendChild(col);
      });
    });
  })();
</script>
@endpush
@endsection
