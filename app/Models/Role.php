<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model {
    protected $fillable = ['name','slug','scope','tenant_id'];
    public function permissions() { return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps(); }
    public function users()       { return $this->belongsToMany(User::class, 'role_user')->withTimestamps(); }
}
