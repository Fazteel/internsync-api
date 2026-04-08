<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $table = 'm_roles';

    protected $fillable = ['name', 'guard_name'];
}
