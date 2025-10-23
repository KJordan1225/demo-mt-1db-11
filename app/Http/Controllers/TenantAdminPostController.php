<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tenant;
use Spatie\Image\Enums\Fit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class TenantAdminPostController extends Controller
{
    public function create()
    {
        return view('tenant.admin.media.create');
    }
    
    /**
     * Store a tenant-aware post with optional image or video.
     * - Validates inputs
     * - Creates the Post with tenant_id auto-set by BelongsToTenant
     * - Attaches media to the proper collection (images/videos)
     */
    public function store(Request $request)
    {
        // Validate inputs (unchanged)
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'body'          => ['nullable', 'string'],
            'published_at'  => ['nullable', 'date'],
            'type'          => ['required', 'in:image,video'],  // <-- this is the selector value

            'image'         => ['required_if:type,image', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'video'         => ['required_if:type,video', 'file', 'mimes:mp4,webm,mov,qt', 'max:51200'],
        ], [
            'image.required_if' => 'Please upload an image file when type is Image.',
            'video.required_if' => 'Please upload a video file when type is Video.',
        ]);

        // (Optional) enforce that only tenant admins can post
        $tenantId = tenant('id');
        abort_unless(Auth::user()?->hasRole('admin', $tenantId), 403, 'Only tenant admins may post.');

        return DB::transaction(function () use ($validated, $request) {
            // Create the post (tenant_id auto-set by BelongsToTenant)
            $post = Post::create([
                'title'        => $validated['title'],
                'body'         => $validated['body'] ?? null,
                'user_id'      => Auth::id(),
                'published_at' => $validated['published_at'] ?? null,
                'media_type'        => $validated['type'],   // <-- store media type in column
            ]);

            // If you didn't add 'media' to $fillable, you can alternatively:
            // $post->forceFill(['media' => $validated['type']])->save();

            // Attach media to the correct collection
            if ($validated['type'] === 'image' && $request->hasFile('image')) {
                $post->addMediaFromRequest('image')->toMediaCollection('images'); // or 'featured_image'
            }

            if ($validated['type'] === 'video' && $request->hasFile('video')) {
                $post->addMediaFromRequest('video')->toMediaCollection('videos');
            }

            return redirect()
                ->back()
                ->with('status', 'Post created and media uploaded successfully.');
        });
    }

    public function index()
    {
        $tenantId = tenant('id');
        $postImageCount = Post::where('media_type', 'image')
                 ->where('tenant_id', $tenantId)
                 ->count();
        $postVideoCount = Post::where('media_type', 'video')
                 ->where('tenant_id', $tenantId)
                 ->count();

        return view('tenant.user.home', compact('postImageCount', 'postVideoCount'));
    }


    /**
     * Display all posts with media type 'image' for the given tenant.
     */
    public function accessImagePosts(Tenant $tenantId)
    {
        // Ensure tenant_id is valid (optional: depending on your routing method, you may use a route-model binding)
        $tenantId = tenant('id') ?? $tenantId;

        // Fetch posts where 'media' is 'image' and 'tenant_id' matches the given $tenantId
        $posts = Post::with('media')  // Eager load associated media
                     ->where('tenant_id', $tenantId)
                     ->where('media_type', 'image')  // Only posts with 'image' in the media column
                     ->get();         

        return view('tenant.admin.image.posts', [
            'posts' => $posts,
            'tenantId' => $tenantId,
        ]);
    }


    public function showSingleImagePost($tenantId, $postId)
    {
        // Find the post by ID and ensure tenant is valid
        $tenant = Tenant::findOrFail($tenantId);
        $post = Post::findOrFail($postId);

        return view('tenant.posts.show', compact('post', 'tenant'));
    }


    public function accessVideoPosts(Tenant $tenantId)
    {
        // Ensure tenant_id is valid (optional: depending on your routing method, you may use a route-model binding)
        $tenantId = tenant('id') ?? $tenantId;

        // Fetch posts where 'media' is 'video' and 'tenant_id' matches the given $tenantId
        $posts = Post::with('media')  // Eager load associated media
                     ->where('tenant_id', $tenantId)
                     ->where('media_type', 'video')  // Only posts with 'video' in the media column
                     ->get();         

        return view('tenant.admin.video.posts', [
            'posts' => $posts,
            'tenantId' => $tenantId,
        ]);
    }


    public function showSingleVideoPost($tenantId, $postId)
    {
        // Find the post by ID and ensure tenant is valid
        $tenant = Tenant::findOrFail($tenantId);
        $post = Post::findOrFail($postId);

        return view('tenant.posts.show-vids', compact('post', 'tenant'));
    }

}
