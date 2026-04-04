<?php

namespace App\Repositories\Hubin;

use App\Models\Industry;
use App\Models\IndustryVisit;
use App\Models\Internship;

class HubinDashboardRepository
{
    public function countActiveIndustries()
    {
        return Industry::where('is_active', true)->count();
    }

    public function countPendingVisits()
    {
        return IndustryVisit::whereNotNull('industry_id')
            ->whereNotNull('pembimbing_id')
            ->where('status', 'pending')
            ->count();
    }

    public function getRecentPendingInternships($limit = 5)
    {
        return Internship::with(['student.user', 'coordinator'])
            ->whereNotNull('industry_id')
            ->whereNotNull('pembimbing_id')
            ->where('status', 'Pending')
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getAllActiveIndustries()
    {
        return Industry::where('is_active', true)->get();
    }
}