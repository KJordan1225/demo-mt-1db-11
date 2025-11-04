

{{-- resources/views/tenant/landing.blade.php --}}
@extends('layouts.tenant')

@section('title', ($branding['display_name'] ?? 'Welcome').' · Landing')

@section('content')
<style>
  /* Center the card and control carousel sizing */
  .landing-wrap {
    min-height: calc(100vh - 4rem);
  }
  .carousel-fixed {
    width: 100%;
    aspect-ratio: 1 / 1;        /* mobile: square & responsive */
    overflow: hidden;
    border-radius: .5rem;
    background: #000;
  }
  @media (min-width: 768px) {   /* md and up: fixed 464x464 */
    .carousel-fixed {
      width: 464px;
      height: 464px;
      aspect-ratio: auto;
    }
  }
  .carousel-fixed img {
    width: 100%;
    height: 100%;
    object-fit: cover;           /* fill frame nicely */
  }
</style>

<div>
  {{-- Session Alerts --}}
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

  @if (session('warning'))
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
          {{ session('warning') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
  @endif

  @if (session('info'))
      <div class="alert alert-info alert-dismissible fade show" role="alert">
          {{ session('info') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
  @endif
</div>
<div class="container landing-wrap d-flex flex-column justify-content-center py-5">
  {{-- Carousel Card --}}
  <div class="row justify-content-center mb-4">
    <div class="col-12 d-flex justify-content-center">
      <div class="card shadow-sm" style="max-width: 520px; width:100%;">
        <div class="card-body d-flex justify-content-center">
          <div id="landingCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" aria-label="Hero image carousel">
            {{-- Indicators (dots) --}}
            @if(!empty($images) && count($images) > 1)
              <div class="carousel-indicators" style="bottom:-2.25rem;">
                @foreach($images as $i => $src)
                  <button type="button"
                          data-bs-target="#landingCarousel"
                          data-bs-slide-to="{{ $i }}"
                          @if($i===0) class="active" aria-current="true" @endif
                          aria-label="Slide {{ $i+1 }}"></button>
                @endforeach
              </div>
            @endif

            {{-- Slides --}}
            <div class="carousel-inner">
              @forelse($images ?? [] as $i => $src)
                <div class="carousel-item @if($i===0) active @endif">
                  <div class="carousel-fixed">                    
                    <img src="{{ $src->getUrl('carousel_slide') }}" 
                     class="d-block w-100" 
                     alt="{{ $src->alt }}" >
                  </div>
                </div>
              @empty
                <div class="carousel-item active">
                  <div class="carousel-fixed d-flex align-items-center justify-content-center text-white-50">
                    <span class="small">No images available</span>
                  </div>
                </div>
              @endforelse
            </div>

            {{-- Controls (optional) --}}
            @if(!empty($images) && count($images) > 1)
              <button class="carousel-control-prev" type="button" data-bs-target="#landingCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#landingCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Subscription Info Card --}}
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body p-4 p-md-5">
          <h2 class="h4 mb-3 text-center">
            Subscribe to {{ $branding['display_name'] ?? 'our micro-site' }}
          </h2>
          <p class="text-muted mb-4 text-center">
            Get exclusive updates, behind-the-scenes content, early access to drops, and members-only perks.
            Your subscription helps support ongoing content and special events delivered directly to you.
          </p>

          <div class="row g-3 justify-content-center mb-3">
            <div class="col-12 col-sm-6 col-lg-4">
              <div class="d-flex align-items-start">
                <div class="me-2">✅</div>
                <div>
                  <strong>Members-only posts</strong><br>
                  <span class="text-muted small">Private galleries and premium content.</span>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
              <div class="d-flex align-items-start">
                <div class="me-2">⚡</div>
                <div>
                  <strong>Early access</strong><br>
                  <span class="text-muted small">Be first to see new releases.</span>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
              <div class="d-flex align-items-start">
                <div class="me-2">🎁</div>
                <div>
                  <strong>Special perks</strong><br>
                  <span class="text-muted small">Discounts, shout-outs, and more.</span>
                </div>
              </div>
            </div>
          </div>
          @php  
            $firstSegment = request()->segment(1);
          @endphp
          <div class="text-center">
            <form method="POST" action="{{ route('subscription.checkout', ['tenant' => $firstSegment]) }}" class="text-center my-3">
              @csrf
              <button type="submit" class="btn btn-primary btn-lg w-100 w-md-auto">
                  Subscribe Now
              </button>
            </form>
            {{-- If you have a route: --}}
            {{-- <a href="{{ route('tenant.subscribe', ['tenant' => tenant('id')]) }}" class="btn btn-primary btn-lg px-4">Subscribe Now</a> --}}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
