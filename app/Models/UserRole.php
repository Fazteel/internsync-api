<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'm_user_roles';
    protected $fillable = ['user_id', 'role_id'];
}
