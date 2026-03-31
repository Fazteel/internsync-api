<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    protected $table = 'm_roles';
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'm_user_roles', 'role_id', 'user_id');
    }
}
