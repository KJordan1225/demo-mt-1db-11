<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot('tenant_id')
            ->withTimestamps();
    }

    public function hasRole(string $name, ?string $tenantId = null): bool
    {
        return $this->roles()
            ->where('roles.name', $name) // qualify column
            ->when(
                $tenantId !== null,
                fn ($q) => $q->where('role_user.tenant_id', $tenantId), // pivot table
                fn ($q) => $q->whereNull('role_user.tenant_id')
            )
            ->exists();
    }


    /** Query permissions available to this user in a context */
    public function permissions(?string $tenantId = null)
    {
        return \App\Models\Permission::query()
            ->whereIn('id', function ($q) use ($tenantId) {
                $q->select('permission_id')
                ->from('permission_role')
                ->whereIn('role_id', function ($q2) use ($tenantId) {
                    $q2->select('roles.id')
                        ->from('roles')
                        ->join('role_user', 'roles.id', '=', 'role_user.role_id')
                        ->where('role_user.user_id', $this->id)
                        ->where('roles.tenant_id', $tenantId);
                });
            });
    }

    /** Boolean permission check with sensible bypasses */
    public function canPermission(string $permissionName, ?string $tenantId = null): bool
    {
        // Landlord super-admin bypass
        if ($this->hasRole('super-admin', null)) return true;
        // Tenant admin bypass inside its tenant
        if ($tenantId !== null && $this->hasRole('admin', $tenantId)) return true;

        return $this->permissions($tenantId)->where('name', $permissionName)->exists();
    }

    public function isSubscribedToLandlordPlan()
    {
        return $this->subscribed('landlord');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

}
