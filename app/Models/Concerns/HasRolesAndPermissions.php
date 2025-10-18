<?php

namespace App\Models\Concerns;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function assignRole(string $slug, string $scope, ?string $tenantId = null): void
    {
        $role = Role::where('slug',$slug)->where('scope',$scope)
            ->when($scope === 'tenant', fn($q)=>$q->where('tenant_id',$tenantId), fn($q)=>$q->whereNull('tenant_id'))
            ->firstOrFail();

        $this->roles()->syncWithoutDetaching([$role->id => ['tenant_id' => $role->tenant_id]]);
    }

    public function hasRole(string $slug, string $scope, ?string $tenantId = null): bool
    {
        return $this->roles()
            ->where('roles.slug',$slug)->where('roles.scope',$scope)
            ->when($scope === 'tenant', fn($q)=>$q->where('roles.tenant_id',$tenantId), fn($q)=>$q->whereNull('roles.tenant_id'))
            ->exists();
    }

    public function canDo(string $permissionSlug, ?string $tenantId = null): bool
    {
        // landlord super admin bypass
        if ($this->hasRole('super_admin','landlord', null)) return true;

        // check tenant or landlord permissions via roles
        return $this->roles()
            ->when($tenantId, fn($q)=>$q->where('roles.scope','tenant')->where('roles.tenant_id',$tenantId),
                          fn($q)=>$q->where('roles.scope','landlord')->whereNull('roles.tenant_id'))
            ->whereHas('permissions', fn($q)=>$q->where('permissions.slug',$permissionSlug))
            ->exists();
    }
}
