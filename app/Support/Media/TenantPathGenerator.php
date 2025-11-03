<?php

namespace App\Support\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class TenantPathGenerator implements PathGenerator
{
    protected function base(Media $media): string
    {
        $tenant = function_exists('tenant') && tenant() ? tenant('id') : 'central';

        // Example structure: tenants/{tenant}/{model}/{id}/
        $model = str_replace('\\', '/', strtolower(class_basename($media->model_type)));

        return "tenants/{$tenant}/{$model}/{$media->model_id}/";
    }

    public function getPath(Media $media): string
    {
        return $this->base($media);
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->base($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->base($media) . 'responsive-images/';
    }
}
