<?php

namespace App\Models\Concerns;

use App\Models\Tenant;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Auto-assign tenant_id on create
        static::creating(function ($model) {
            if (function_exists('tenant') && tenant('id') && empty($model->tenant_id)) {
                $model->tenant_id = tenant('id');
            }
        });

        // Global scope: always filter by current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (function_exists('tenant') && tenant('id')) {
                $table = $builder->getModel()->getTable();
                $builder->where($table.'.tenant_id', tenant('id'));
            }
        });
    }

    // Optional: relation back to Tenant (Stancl tenant model)
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }
}
