<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class TenantMedia extends Media
{
    protected $fillable = [
        // include tenant_id so mass-assign/update works
        'tenant_id',
        // keep the rest from Spatie (file_name, mime_type, etc.)
    ];

    // Ensure we always set tenant_id on create (HTTP or queue)
    protected static function booted(): void
    {
        static::creating(function (TenantMedia $media) {
            if (function_exists('tenant') && tenant() && empty($media->tenant_id)) {
                $media->tenant_id = method_exists(tenant(), 'getTenantKey')
                    ? tenant()->getTenantKey()
                    : tenant('id');
            }
        });

        // Global scope: only see current tenant’s media
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (function_exists('tenant') && tenant()) {
                $tenantId = method_exists(tenant(), 'getTenantKey')
                    ? tenant()->getTenantKey()
                    : tenant('id');

                $builder->where('tenant_id', $tenantId);
            }
        });
    }
}
