<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipApplication extends Model
{
    use SoftDeletes;

    protected $table = 'tr_internship_applications';

    protected $fillable = [
        'application_number',
        'coordinator_id',
        'industry_id',
        'pembimbing_id',
        'suggested_start_date',
        'suggested_end_date',
        'departure_date',
        'duration_option',
        'final_end_date',
        'status',
        'application_letter_path',
        'placement_letter_path',
        'ba_path'
    ];

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    // Relasi Many-to-Many ke Student lewat table pivot
    public function students()
    {
        return $this->belongsToMany(Student::class, 'tr_application_students', 'application_id', 'student_id');
    }
}
