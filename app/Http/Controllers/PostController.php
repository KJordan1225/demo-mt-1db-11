<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class PostController extends Controller
{
    public function create()
    {
        return view ('post.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => ['required','string','max:255'],
            'description'  => ['nullable','string'],
            'tenant'       => ['nullable','string'],
            'media_type'   => ['nullable','in:image,video'],

            // IMAGES
            'images'       => ['nullable','array'],
            'images.*'     => ['nullable','image','max:5120'], // 5MB each

            // VIDEOS
            'videos'       => ['nullable','array'],
            'videos.*'     => [
                'nullable',
                'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo,video/x-matroska',
                'max:512000' // 500MB each (KB)
            ],
        ]);

        $post = Post::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'media_type'  => $data['media_type'] ?? null,
            // tenant_id auto-set by BelongsToTenant trait if used
        ]);

        $tenant = $data['tenant'] ?? tenant('id');

        // --- Store IMAGES -> image_gallery ---
        if (
            (!$data['media_type'] || $data['media_type'] === 'image') &&
            $request->hasFile('images')
        ) {
            foreach ((array) $request->file('images') as $img) {
                if ($img && $img->isValid()) {
                    $post->addMedia($img)->toMediaCollection('image_gallery');
                }
            }
        }

        // --- Store VIDEOS -> video_gallery ---
        if (
            (!$data['media_type'] || $data['media_type'] === 'video') &&
            $request->hasFile('videos')
        ) {
            foreach ((array) $request->file('videos') as $vid) {
                if ($vid && $vid->isValid()) {
                    $post->addMedia($vid)->toMediaCollection('video_gallery');
                }
            }
        }

        return redirect()
            ->route('tenant.posts.index', ['tenant' => $tenant])
            ->with('success', 'Post created.');
    }


    public function index()
    {
        // Without pagination:
        // $posts = Post::withoutGlobalScopes()->orderByDesc('id')->get();

        // With pagination:
        $posts = Post::withoutGlobalScopes()->orderByDesc('id')->paginate(20);

        return view('post.index', compact('posts'));
    }

    public function showCarousel(Request $request)
    {      

        $tenantId = request()->segment(1);

        $images = Media::query()
            ->where('collection_name', 'carousel_samples')
            ->whereHasMorph('model', [Post::class], function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->orderBy('order_column')   // Spatie’s ordering if you use it
            ->get(); 

        return view('tenant.landing', ['tenant' => $tenantId],compact('images'));
    }


    public function clearMediaCollections(Request $request, Post $post)
    {
        $post->clearMediaCollection('carousel_samples');

        return redirect()->back()->with('success', 'Media collection cleared.');
    }

    public function viewImageGallery(Request $request)
    {
        $tenantId = request()->segment(1);
        // All media in `image_gallery` whose parent model has tenant_id = $tenantId
        $media = Media::query()
            ->where('collection_name', 'image_gallery')
            ->whereHasMorph('model', [Post::class], function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->orderByDesc('id')
            ->paginate(9);

        return view('tenant.gallery.index', compact('media', 'tenantId'));
    }


    public function viewVideoGallery(Request $request)
    {
        $tenantId = request()->segment(1);
        // Assuming Tenant has a media relationship (adjust accordingly to your model setup)
        $tenant = Tenant::findOrFail($tenantId);

        // Fetch all videos in 'video_gallery' collection, paginate 9 per page
        // $videos = $tenant->media()
        //                  ->where('collection_name', 'video_gallery')
        //                  ->where('mime_type', 'video/%')  // Ensuring we're only fetching video files
        //                  ->paginate(9);  // Paginate 9 videos per page

        $videos = Media::query()
            ->where('collection_name', 'video_gallery')
            ->whereHasMorph('model', [Post::class], function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->where(function ($query) {
                $query->where('mime_type', 'like', 'video/%');
            })
            ->orderByDesc('id')
            ->paginate(9);

        return view('tenant.videos.index', compact('videos'));
    }

}
