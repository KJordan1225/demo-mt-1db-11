<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use App\Models\User;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $casts = ['data' => 'array'];
	protected $fillable = [ 
						'display_name',
						'slug', 
						'data',
						'domain_name',
						'logo_url',
						'primary_color',
						'accent_color',
						'bg_color',
						'text_color',
						'stripe_account_id',
						'stripe_payouts_enabled',
						'stripe_details_submitted',
						'stripe_product_id',
						'stripe_price_id',
					]; // or: protected $guarded = [];

    // Relationship to users
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

}