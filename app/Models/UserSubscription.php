<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    // Table associated with the model
    protected $table = 'usersubscriptions';

    // Fillable attributes (columns that can be mass-assigned)
    protected $fillable = [
        'user_id', 
        'tenant_id', 
        'stripe_subscription_id',
    ];

    // Defining the relationship between UserSubscription and User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Defining the relationship between UserSubscription and Tenant model
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
