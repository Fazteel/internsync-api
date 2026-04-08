<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
    protected $table = 'm_students';
    protected $fillable = ['user_id', 'academic_year_id', 'nis', 'name', 'jurusan', 'kelas', 'phone', 'address', 'is_pkl'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function internship()
    {
        return $this->hasOne(Internship::class, 'student_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'jurusan', 'major_code');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'kelas', 'name');
    }
}
