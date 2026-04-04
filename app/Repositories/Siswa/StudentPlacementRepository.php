<?php

namespace App\Repositories\Siswa;

use App\Models\Student;

class StudentPlacementRepository
{
    public function getStudentPlacement($userId)
    {
        return Student::where('user_id', $userId)
            ->with(['internship.industry', 'internship.pembimbing', 'internship.letters'])
            ->first();
    }
}
