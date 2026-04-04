<?php

namespace App\Repositories\Koordinator;

use App\Models\Student;
use App\Models\User;
use App\Models\Internship;

class SupervisorRepository
{
    public function getStudentsWithRelations()
    {
        return Student::with(['user', 'internship.industry', 'internship.pembimbing'])->get();
    }

    public function getTeachers()
    {
        return User::whereHas('roles', function ($q) {
            $q->where('name', 'Pembimbing');
        })->select('id', 'name')->get();
    }

    public function assignSupervisor($studentId, $pembimbingId)
    {
        return Internship::updateOrCreate(
            ['student_id' => $studentId],
            ['pembimbing_id' => $pembimbingId]
        );
    }
}
