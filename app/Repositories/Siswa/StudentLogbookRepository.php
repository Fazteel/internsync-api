<?php

namespace App\Repositories\Siswa;

use App\Models\Logbook;
use App\Models\Student;

class StudentLogbookRepository
{
    public function getStudentByUserId($userId)
    {
        return Student::where('user_id', $userId)->first();
    }

    public function getLogbooks($internshipId)
    {
        return Logbook::where('internship_id', $internshipId)->orderBy('date', 'desc')->get();
    }

    public function findLogByDate($internshipId, $date)
    {
        return Logbook::where('internship_id', $internshipId)->where('date', $date)->first();
    }

    public function create(array $data)
    {
        return Logbook::create($data);
    }

    public function findById($id)
    {
        return Logbook::findOrFail($id);
    }
}
