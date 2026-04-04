<?php

namespace App\Repositories\Koordinator;

use App\Models\Student;
use App\Models\Industry;
use App\Models\Internship;

class KoordinatorDashboardRepository
{
    public function getTotalStudentsCount()
    {
        return Student::count();
    }

    public function getStudentsWithoutPlacementCount()
    {
        return Student::whereDoesntHave('internship', function ($q) {
            $q->whereNotNull('industry_id');
        })->count();
    }

    public function getActiveIndustriesCount()
    {
        return Industry::where('is_active', true)->count();
    }

    public function getRecentStudents($limit = 5)
    {
        return Student::with(['user', 'internship.industry'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getInternshipsThisYear()
    {
        return Internship::whereNotNull('start_date')
            ->whereYear('start_date', date('Y'))
            ->get();
    }
}
