<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Tenant scope/auto-stamp
use App\Models\Concerns\BelongsToTenant;

// Spatie Media Library
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class CarouselPost extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use BelongsToTenant;

    // If you keep the default table name `posts`, you can omit this.
    protected $table = 'carousel_posts';

    protected $fillable = [
        'tenant_id',     // string, nullable FK to tenants.id
        'title',         // required          '
    ];

    /**
     * Choose disk per tenant context.
     * If you created a tenant-aware disk (e.g., 'tenant_public'), use it.
     */
    protected function mediaDisk(): string
    {
        return (function_exists('tenant') && tenant())
            ? 'tenant_public'   // define in config/filesystems.php
            : 'public';
    }

    /**
     * Define media collections (Spatie).
     */
    public function registerMediaCollections(): void
    {
        $disk = $this->mediaDisk();

        // Single-file "cover" image
        $this->addMediaCollection('cover')
             ->singleFile()
             ->useDisk($disk)
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

        // Multi-file "gallery"
        $this->addMediaCollection('gallery')
             ->useDisk($disk)
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

        // Multi-file "samples" carousel
        // Multi-file "gallery"
        $this->addMediaCollection('carousel_samples')
             ->useDisk($disk)
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
          
    }

    /**
     * Define media conversions (thumbnails, web formats, etc.).
     */
    public function registerMediaConversions(Media $media = null): void
    {
        // Thumbnail (e.g., for cards)
        $this->addMediaConversion('thumb')
             ->fit(Fit::Crop, 480, 270)
             ->format('jpg')
             ->quality(85)
             ->nonQueued();

        // Square thumb (optional)
        $this->addMediaConversion('square')
             ->fit(Fit::Crop, 512, 512)
             ->format('jpg')
             ->quality(85)
             ->nonQueued();

        // Square thumb (optional)
        $this->addMediaConversion('carousel_slide')
             ->fit(Fit::Crop, 464, 464)
             ->format('jpg')
             ->quality(85)
             ->nonQueued();

        // Web-friendly version
        $this->addMediaConversion('web')
             ->format('webp')
             ->quality(82);
    }

    /* -----------------------------------------
       Relationships (optional)
    ------------------------------------------*/

    // Example: if you want to link author
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
