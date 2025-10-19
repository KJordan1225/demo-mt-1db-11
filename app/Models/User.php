<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\HasRolesAndPermissions;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant, Billable, HasRolesAndPermissions;

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

        /**
         * Determine if the email is unique per tenant (used for registration).
         */
        public static function emailIsUniqueForTenant($email, $tenantId = null)
        {
            return !self::where('email', $email)
                ->where('tenant_id', $tenantId)->exists();
        }

        /**
         * Get the tenant associated with the user.
         */
        public function tenant(): BelongsTo
        {
            return $this->belongsTo(Tenant::class);
        }
       

        /**
         * Tenant-aware role check (no Spatie).
         */
        public function hasRole(string $name, ?string $tenantId = null): bool
        {
            return $this->roles()
                ->where('name', $name)
                ->when(!is_null($tenantId), fn($q) => $q->where('tenant_id', $tenantId))
                ->exists();
        }


}
