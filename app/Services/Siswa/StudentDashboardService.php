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

            return [
                'metrics' => [
                    'approved_count' => $this->repo->countLogbooksByStatus($internship->id, 'approved'),
                    'revision_count' => $this->repo->countLogbooksByStatus($internship->id, 'revised'),
                ],
                'recent_logbooks' => $this->repo->getRecentLogbooks($internship->id)->map(fn($log) => [
                    'id' => $log->id,
                    'date' => Carbon::parse($log->date)->translatedFormat('d M Y'),
                    'activity' => $log->activity,
                    'status' => $this->mapStatus($log->status)
                ]),
                'progress' => $this->calculateProgress($internship),
                'last_updated' => now()->format('H:i')
            ];
        });
    }

    private function mapStatus($status)
    {
        return match ($status) {
            'approved' => 'Approved',
            'revised' => 'Revision',
            default => 'Pending'
        };
    }

    private function calculateProgress($internship)
    {
        if (!$internship->start_date || !$internship->end_date) return $this->emptyProgress();

        $start = Carbon::parse($internship->start_date)->startOfDay();
        $end = Carbon::parse($internship->end_date)->endOfDay();
        $now = Carbon::now();

        $totalDays = intval($start->diffInDays($end)) + 1;

        if ($now->lt($start)) return ['total_days' => $totalDays, 'days_passed' => 0, 'days_remaining' => $totalDays, 'percentage' => 0];
        if ($now->gt($end)) return ['total_days' => $totalDays, 'days_passed' => $totalDays, 'days_remaining' => 0, 'percentage' => 100];

        $daysPassed = intval($start->diffInDays($now)) + 1;
        return [
            'total_days' => $totalDays,
            'days_passed' => $daysPassed,
            'days_remaining' => max(0, $totalDays - $daysPassed),
            'percentage' => round(($daysPassed / $totalDays) * 100),
            'raw_start_date' => $internship->start_date,
            'raw_end_date' => $internship->end_date,
        ];
    }

    private function emptyResponse()
    {
        return ['metrics' => ['approved_count' => 0, 'revision_count' => 0], 'recent_logbooks' => [], 'progress' => $this->emptyProgress()];
    }

    private function emptyProgress()
    {
        return ['total_days' => 0, 'days_passed' => 0, 'days_remaining' => 0, 'percentage' => 0];
    }
}
