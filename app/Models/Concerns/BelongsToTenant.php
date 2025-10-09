<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (tenant('id') && empty($model->tenant_id)) {
                $model->tenant_id = tenant('id');
            }
        });
    }
}
