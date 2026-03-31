<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use SoftDeletes;

    protected $table = 'm_classrooms';
    
    protected $fillable = [
        'major_id', 
        'name', 
        'is_active'
    ];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }
    
}