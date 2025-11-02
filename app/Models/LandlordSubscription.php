<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordSubscription extends Model
{
    protected $fillable = [
        'user_id','stripe_customer_id','stripe_subscription_id','status',
        'cancel_at_period_end','current_period_end','raw',
    ];

    protected $casts = [
        'cancel_at_period_end' => 'boolean',
        'current_period_end'   => 'datetime',
        'raw'                  => 'array',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

