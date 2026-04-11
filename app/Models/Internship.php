<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    protected $table = 'tr_internships';
    protected $fillable = [
        'application_id',
        'student_id',
        'industry_id',
        'pembimbing_id',
        'coordinator_id',
        'start_date',
        'end_date',
        'duration_month',
        'is_extended',
        'status',
        'cancelled_reason',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }
    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }
    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }
    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'internship_id');
    }
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'internship_id');
    }
    public function letters()
    {
        return $this->hasMany(Letter::class, 'internship_id');
    }
    public function application()
    {
        return $this->belongsTo(InternshipApplication::class, 'application_id');
    }
}
