<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Stancl\Tenancy\Facades\Tenancy;
use App\Models\Tenant as TenancyTenant;
use Spatie\MediaLibrary\MediaCollections\Jobs\PerformConversions as SpatiePerformConversions;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class PerformTenantConversions extends SpatiePerformConversions
{
    public function handle(): void
    {
        /** @var Media $media */
        $media = $this->media;

        // Initialize tenancy for queued context, if applicable
        if (! empty($media->tenant_id)) {
            if ($tenant = TenancyTenant::find($media->tenant_id)) {
                Tenancy::initialize($tenant);
            }
        }

        // Now run Spatie’s normal conversion handling
        parent::handle();
    }
}
