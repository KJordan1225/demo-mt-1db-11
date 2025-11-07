@extends('layouts.tenant')

@section('content')
    <div class="container">
        <div class="row">
            @foreach($videos as $index => $video)
                @if ($index % 3 == 0 && $index > 0)
                    </div><div class="row"> <!-- Start a new row after 3 videos -->
                @endif

                <div class="col-md-4 mb-4">
                    <div class="card">
                        <video controls class="w-100">
                            <source src="{{ $video->getUrl() }}" type="{{ $video->mime_type }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination links -->
        <div class="d-flex justify-content-center">
            {{ $videos->links() }}
        </div>
    </div>
@endsection
