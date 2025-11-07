<?php

namespace App\Http\Controllers;

use App\Models\CarouselPost;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class CarouselPostController extends Controller
{
    public function create()
    {
        return view ('post.carousel.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'tenant' => ['nullable','string'],
            'images.*' => ['nullable','image','max:5120'],
        ]);

        $post = CarouselPost::create([
            'title' => $data['title'],
            // tenant_id auto-set by BelongsToTenant (if you use that trait)
        ]);

        $tenant = $data['tenant']; // get current tenant_id

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $post->addMedia($img)->toMediaCollection('carousel_samples');
            }
        }

        return redirect()->route('tenant.posts.index', ['tenant' => $tenant])
                        ->with('success', 'Post created.');
    }

    public function index()
    {
        // Without pagination:
        // $posts = Post::withoutGlobalScopes()->orderByDesc('id')->get();

        // With pagination:
        $posts = CarouselPost::withoutGlobalScopes()
            ->orderByDesc('id')
            ->paginate(20);

        return view('post.carousel.index', compact('posts'));
    }

    public function showCarousel(Request $request)
    {      

        $tenantId = request()->segment(1);

        $images = Media::query()
            ->where('collection_name', 'carousel_samples')
            ->whereHasMorph('model', [CarouselPost::class], function ($q) use ($tenantId) {
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

}
