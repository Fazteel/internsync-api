<?php

namespace App\Repositories\Siswa;

use App\Models\Student;
use App\Models\Logbook;

class StudentDashboardRepository
{
    public function getStudentByUserId($userId)
    {
        return Student::where('user_id', $userId)->with('internship')->first();
    }

    public function countLogbooksByStatus($internshipId, $status)
    {
        return Logbook::where('internship_id', $internshipId)->where('status', $status)->count();
    }

    public function getRecentLogbooks($internshipId, $limit = 5)
    {
        return Logbook::where('internship_id', $internshipId)
            ->orderBy('date', 'desc')
            ->take($limit)
            ->get();
    }
}
