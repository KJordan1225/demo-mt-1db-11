<?php

namespace App\Support\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class TenantPathGenerator implements PathGenerator
{
    protected function base(Media $media): string
    {
        $tenantId = $media->tenant_id
            ?? (function_exists('tenant') && tenant()
                ? (method_exists(tenant(), 'getTenantKey') ? tenant()->getTenantKey() : tenant('id'))
                : 'central');

        // e.g. tenant/alpha/App.Models.User/123/
        return "tenant/{$tenantId}/{$media->model_type}/{$media->model_id}/";
    }

    public function getPath(Media $media): string
    {
        return $this->base($media);
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->base($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->base($media).'responsive/';
    }
}
