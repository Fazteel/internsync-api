<?php

namespace App\Repositories\Pembimbing;

use App\Models\IndustryVisit;
use App\Models\Internship;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PembimbingDashboardRepository
{
    public function countActiveInternships($pembimbingId)
    {
        return Internship::where('pembimbing_id', $pembimbingId)
            ->where('status', 'active')
            ->count();
    }

    public function countSubmittedLogbooks($pembimbingId)
    {
        return Logbook::whereHas('internship', function ($q) use ($pembimbingId) {
                $q->where('pembimbing_id', $pembimbingId);
            })
            ->where('status', 'submitted')
            ->count();
    }

    public function countApprovedVisitsThisMonth($pembimbingId)
    {
        return IndustryVisit::where('pembimbing_id', $pembimbingId)
            ->whereMonth('planned_date', Carbon::now()->month)
            ->whereYear('planned_date', Carbon::now()->year)
            ->where('status', 'approved')
            ->count();
    }

    public function getLatestSubmittedLogbooks($pembimbingId, $limit = 5)
    {
        return Logbook::with(['internship.student.user', 'internship.industry'])
            ->whereHas('internship', function ($q) use ($pembimbingId) {
                $q->where('pembimbing_id', $pembimbingId);
            })
            ->where('status', 'submitted')
            ->orderBy('date', 'desc')
            ->take($limit)
            ->get();
    }

    public function getWeeklyLogbookStats($pembimbingId, $weeksCount = 7)
    {
        return Logbook::select(
                DB::raw('YEARWEEK(date, 1) as week'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN status = "revised" THEN 1 ELSE 0 END) as revised_count')
            )
            ->whereHas('internship', function ($q) use ($pembimbingId) {
                $q->where('pembimbing_id', $pembimbingId);
            })
            ->where('date', '>=', Carbon::now()->subWeeks($weeksCount))
            ->groupBy('week')
            ->orderBy('week', 'asc')
            ->get();
    }
}