<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Major extends Model
{
    use SoftDeletes;

    protected $table = 'm_majors';
    
    protected $fillable = [
        'major_code', 
        'major_name', 
        'is_active'
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'major_id');
    }
}