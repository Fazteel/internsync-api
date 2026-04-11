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
            ->where('status', 'aktif')
            ->count();
    }

    public function countTotalLogbooks($pembimbingId)
    {
        return Logbook::whereHas('internship', function ($q) use ($pembimbingId) {
            $q->where('pembimbing_id', $pembimbingId);
        })
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

    public function getLatestLogbooks($pembimbingId, $limit = 5)
    {
        return Logbook::with(['internship.student.user', 'internship.industry'])
            ->whereHas('internship', function ($q) use ($pembimbingId) {
                $q->where('pembimbing_id', $pembimbingId);
            })
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getWeeklyLogbookStats($pembimbingId, $weeksCount = 7)
    {
        return Logbook::select(
            DB::raw('YEARWEEK(date, 1) as week'),
            DB::raw('COUNT(*) as total_count')
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
