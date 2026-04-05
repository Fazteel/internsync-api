<?php

namespace App\Repositories\Koordinator;

use App\Models\Student;
use App\Models\Industry;
use App\Models\Internship;

class PlacementRepository
{
    public function getAllStudentsWithPlacement()
    {
        return Student::with(['user', 'internship.industry', 'internship.pembimbing'])->get();
    }

    public function getActiveIndustriesWithCount()
    {
        return Industry::where('is_active', true)->withCount('internships')->get();
    }

    public function findIndustryById($id)
    {
        return Industry::withCount('internships')->findOrFail($id);
    }

    public function findInternshipByStudentId($studentId)
    {
        return Internship::where('student_id', $studentId)->first();
    }

    public function updateOrCreateInternship(array $attributes, array $values)
    {
        return Internship::updateOrCreate($attributes, $values);
    }

    public function withdrawInternship($studentId)
    {
        return Internship::where('student_id', $studentId)->delete();
    }
}
