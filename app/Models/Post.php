<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Tenancy
use App\Models\Concerns\BelongsToTenant;

// Spatie Media Library
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;


class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, BelongsToTenant;

    protected $fillable = [
        'title',
        'body',
        'user_id',
        'published_at',
        'media_type',
        // 'tenant_id' is auto-set by trait; include it in $fillable only if you want manual overrides
    ];

    /**
     * Media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg','image/png','image/gif','image/webp'])
            ->withResponsiveImages();

        $this->addMediaCollection('featured_image')
            ->useDisk('public')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg','image/png','image/webp'])
            ->withResponsiveImages();

        $this->addMediaCollection('videos')
            ->useDisk('public')
            ->acceptsMimeTypes(['video/mp4','video/webm','video/quicktime']);        
    }

    /**
     * Conversions (images only)
     */
    public function registerMediaConversions(Media $media = null): void
    {
        if ($media && str_starts_with($media->mime_type, 'image/')) {
            $this->addMediaConversion('thumb')
                ->fit(Fit::Crop, 300, 300)
                ->performOnCollections('images','featured_image');
            $this->addMediaConversion('medium')
                ->fit(Fit::Contain, 1024, 1024)    // or Cover/Max/etc. as you prefer
                ->performOnCollections('images','featured_image');
        }

        // VIDEOS -> thumbnail from frame at timecode (configured above)
        if ($media && str_starts_with($media->mime_type, 'video/')) {
            $this->addMediaConversion('thumb')
                // After the generator creates an image, apply image fit/crop
                ->fit(Fit::Cover, 640, 360)           // 16:9 thumbnail
                ->performOnCollections('videos');
                // ->nonQueued(); // uncomment if you want sync generation during upload
        }
    }

    /**
     * Accessors
     */
    public function getFeaturedThumbUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('featured_image');
        return $media ? $media->getUrl('thumb') : null;
    }

    public function getFirstImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('images');
    }

    /**
     * Relationships
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
