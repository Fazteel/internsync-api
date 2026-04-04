<?php

namespace App\Repositories\Hubin;

use App\Models\Internship;
use App\Models\Letter;

class DepartureRepository
{
    public function getAllWithRelations()
    {
        return Internship::with(['student.user', 'student.major', 'industry', 'pembimbing', 'letters'])
            ->orderBy('created_at', 'desc')->get();
    }

    public function findInternship($id)
    {
        return Internship::with(['student.user', 'industry', 'student.major'])->findOrFail($id);
    }

    public function updateInternship($id, array $data)
    {
        return Internship::where('id', $id)->update($data);
    }

    public function checkExistingLetter($internshipId)
    {
        return Letter::where('internship_id', $internshipId)->first();
    }

    public function createLetter(array $data)
    {
        return Letter::create($data);
    }
}
