<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Image\Enums\Fit;


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
        // Basic content + media type validation
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'body'          => ['nullable', 'string'],
            'published_at'  => ['nullable', 'date'],
            'type'          => ['required', 'in:image,video'],

            // File rules — adjust mimes/max to your needs
            'image'         => ['required_if:type,image', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'video'         => ['required_if:type,video', 'file', 'mimes:mp4,webm,mov,qt', 'max:51200'],
        ], [
            'image.required_if' => 'Please upload an image file when type is Image.',
            'video.required_if' => 'Please upload a video file when type is Video.',
        ]);

        // (Optional) enforce that only tenant admins can post
        $tenantId = tenant('id');
        abort_unless(Auth::user()?->hasRole('admin', $tenantId), 403, 'Only tenant admins may post.');

        // Create the post (tenant_id will be auto-set by BelongsToTenant on creating)
        $post = Post::create([
            'title'        => $validated['title'],
            'body'         => $validated['body'] ?? null,
            'user_id'      => Auth::id(),
            'published_at' => $validated['published_at'] ?? null,
            // no need to set tenant_id here; the trait handles it
        ]);

        // Attach media to the correct collection
        if ($validated['type'] === 'image' && $request->hasFile('image')) {
            // Choose which collection you prefer. 'images' (multi) or 'featured_image' (single).
            $post->addMediaFromRequest('image')->toMediaCollection('images');
        }

        if ($validated['type'] === 'video' && $request->hasFile('video')) {
            $post->addMediaFromRequest('video')->toMediaCollection('videos');
        }

        return redirect()
            ->back()
            ->with('status', 'Post created and media uploaded successfully.');
    }

}
