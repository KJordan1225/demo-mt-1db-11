<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    // Table name is the default "subscriptions" — no need to set $table

    /**
     * Mass-assignable attributes
     */
    protected $fillable = [
        'user_id',
        'tenant_id',               // string FK to tenants.id
        'stripe_subscription_id',
        'status',                  // 'active', 'canceled', 'past_due'
        'ends_at',
    ];

    /**
     * Type casting
     */
    protected $casts = [
        'ends_at' => 'datetime',
    ];

    /**
     * Optional: status constants
     */
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_PAST_DUE = 'past_due';

    /**
     * Relationships
     */

    // The subscribing platform user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tenant by string primary key (tenants.id is a string)
    public function tenant()
    {
        // local key on Tenant is 'id' (string), FK on this model is 'tenant_id' (string)
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Query scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Helpers
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    public function isPastDue(): bool
    {
        return $this->status === self::STATUS_PAST_DUE;
    }
}
