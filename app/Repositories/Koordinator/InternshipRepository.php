<?php

namespace App\Repositories\Koordinator;

use App\Models\InternshipApplication;

class InternshipRepository
{
    public function getApplicationsByStatus($statuses = [])
    {
        return InternshipApplication::with(['industry', 'pembimbing.teacher', 'students.user'])
            ->whereIn('status', $statuses)
            ->latest()
            ->get();
    }

    public function findApplication($id)
    {
        return InternshipApplication::with('students')->findOrFail($id);
    }

    public function findWithDetails($id)
    {
        return InternshipApplication::with([
            'industry',
            'pembimbing',
            'students.user',
            'students.major'
        ])->findOrFail($id);
    }
}
