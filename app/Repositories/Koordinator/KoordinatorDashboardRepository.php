<?php

namespace App\Repositories\Koordinator;

use App\Models\Student;
use App\Models\Industry;
use App\Models\Internship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KoordinatorDashboardRepository
{
    public function getTotalStudentsCount()
    {
        return Student::count();
    }

    public function getActiveStudentsCount($coordinatorId)
    {
        return Internship::where('status', 'aktif')
            ->whereHas('application', function ($q) use ($coordinatorId) {
                $q->where('coordinator_id', $coordinatorId);
            })->count();
    }

    public function getStudentsWithoutPlacementCount()
    {
        return Student::whereDoesntHave('internships', function ($q) {
            $q->whereIn('status', ['aktif', 'selesai']);
        })->count();
    }

    public function getIndustryDistribution($coordinatorId)
    {
        return Internship::where('status', 'aktif')
            ->whereHas('application', function ($q) use ($coordinatorId) {
                $q->where('coordinator_id', $coordinatorId);
            })
            ->join('m_industries', 'tr_internships.industry_id', '=', 'm_industries.id')
            ->select('m_industries.name as industry_name', DB::raw('count(*) as student_count'))
            ->groupBy('m_industries.name')
            ->get();
    }

    public function getRecentPlacements($coordinatorId, $limit = 5)
    {
        return Internship::with(['student.user', 'industry'])
            ->whereHas('application', function ($q) use ($coordinatorId) {
                $q->where('coordinator_id', $coordinatorId);
            })
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getInternshipsThisYear($coordinatorId)
    {
        return Internship::whereYear('start_date', date('Y'))
            ->whereHas('application', function ($q) use ($coordinatorId) {
                $q->where('coordinator_id', $coordinatorId);
            })
            ->get();
    }
}
