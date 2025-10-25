<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConnectCustomer extends Model
{
    protected $fillable = ['user_id','tenant_id','connected_customer_id'];
}

