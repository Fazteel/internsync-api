<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Industry extends Model
{
    use SoftDeletes;
    protected $table = 'm_industries';
    protected $fillable = [
        'name', 'address', 'hr_name', 'hr_phone', 'hr_email', 'kuota_siswa', 'is_active', 'mou_file'
    ];

    protected $casts = ['is_active' => 'boolean',];

    public function internships()
    {
        return $this->hasMany(Internship::class, 'industry_id');
    }
}
