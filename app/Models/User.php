<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant, Billable;

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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /** Check a role in a context (tenant_id null = landlord) */
    public function hasRole(string $name, ?string $tenantId = null): bool
    {
        return $this->roles()
            ->where('name', $name)
            ->where('tenant_id', $tenantId)
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

    public function hasActiveSubscription(): bool
    {
        return (bool) $this->subscription('default')?->valid();
    }

    public function hasActivePrice(string $priceId): bool
    {
        $sub = $this->subscription('default');
        if (! $sub || ! $sub->valid()) return false;

        // Cashier links items in `subscription_items` table
        return $sub->items()->where('stripe_price', $priceId)->exists();
    }


    
}
