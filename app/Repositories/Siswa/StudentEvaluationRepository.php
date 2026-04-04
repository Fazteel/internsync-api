<?php

namespace App\Repositories\Siswa;

use App\Models\Internship;

class StudentEvaluationRepository
{
    public function getEvaluationByStudent($userId)
    {
        return Internship::with(['industry', 'evaluations', 'pembimbing', 'student.user'])
            ->whereHas('student', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
    }
}