<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use SoftDeletes;

    protected $table = 'm_academic_years';
    
    protected $fillable = [
        'name', 
        'is_active'
    ];
}