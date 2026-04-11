<?php

namespace App\Services\Siswa;

use App\Repositories\Siswa\StudentDashboardRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StudentDashboardService
{
    protected $repo;
    public function __construct(StudentDashboardRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getStats($userId)
    {
        return Cache::remember("student_stats_{$userId}", 120, function () use ($userId) {
            $student = $this->repo->getStudentByUserId($userId);

            if (!$student || !$student->internship) {
                return $this->emptyResponse();
            }

            $internship = $student->internship;
            $totalLogbook = $this->repo->countTotalLogbooks($internship->id);

            return [
                'metrics' => [
                    'total_logbook_diisi' => $totalLogbook,
                ],
                'recent_logbooks' => $this->repo->getRecentLogbooks($internship->id, 3)->map(fn($log) => [
                    'id' => $log->id,
                    'date' => Carbon::parse($log->date)->translatedFormat('d M Y'),
                    'activity' => $log->activity,
                ]),
                'progress' => $this->calculateProgress($internship, $totalLogbook),
                'last_updated' => now()->format('H:i')
            ];
        });
    }

    private function calculateProgress($internship, $filledLogbooks)
    {
        if (!$internship->start_date || !$internship->end_date) return $this->emptyProgress();

        $start = Carbon::parse($internship->start_date)->startOfDay();
        $end = Carbon::parse($internship->end_date)->endOfDay();
        $now = Carbon::now();

        $totalDays = intval($start->diffInDays($end)) + 1;

        if ($now->lt($start)) return ['total_days' => $totalDays, 'days_passed' => 0, 'days_remaining' => $totalDays, 'percentage' => 0];

        $daysPassedInTime = intval($start->diffInDays($now)) + 1;
        if ($daysPassedInTime > $totalDays) $daysPassedInTime = $totalDays;

        $percentage = round(($filledLogbooks / $totalDays) * 100);
        if ($percentage > 100) $percentage = 100;

        return [
            'total_days' => $totalDays,
            'days_passed' => $filledLogbooks,
            'days_remaining' => max(0, $totalDays - $daysPassedInTime),
            'percentage' => $percentage,
            'raw_start_date' => $internship->start_date,
            'raw_end_date' => $internship->end_date,
        ];
    }

    private function emptyResponse()
    {
        return ['metrics' => ['total_logbook_diisi' => 0], 'recent_logbooks' => [], 'progress' => $this->emptyProgress()];
    }

    private function emptyProgress()
    {
        return ['total_days' => 0, 'days_passed' => 0, 'days_remaining' => 0, 'percentage' => 0];
    }
}
