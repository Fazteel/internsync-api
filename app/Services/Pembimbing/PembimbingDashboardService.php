<?php

namespace App\Services\Pembimbing;

use App\Repositories\Pembimbing\PembimbingDashboardRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PembimbingDashboardService
{
    protected $repository;

    public function __construct(PembimbingDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardData($pembimbingId)
    {
        return Cache::remember("pembimbing_stats_{$pembimbingId}", 120, function () use ($pembimbingId) {
            $metrics = [
                'total_bimbingan' => $this->repository->countActiveInternships($pembimbingId),
                'total_logbook_diisi' => $this->repository->countTotalLogbooks($pembimbingId),
                'kunjungan_bulan_ini' => $this->repository->countApprovedVisitsThisMonth($pembimbingId),
            ];

            $recentLogbooks = $this->repository->getLatestLogbooks($pembimbingId)
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'studentName' => $log->internship->student->user->name ?? $log->internship->student->name ?? '-',
                        'industry' => $log->internship->industry->name ?? '-',
                        'date' => Carbon::parse($log->date)->translatedFormat('d M Y'),
                        'activity' => $log->activity,
                    ];
                });

            $chartData = $this->formatChartData($pembimbingId);

            return [
                'metrics' => $metrics,
                'recent_logbooks' => $recentLogbooks,
                'chart' => $chartData,
                'last_updated' => now()->format('H:i')
            ];
        });
    }

    private function formatChartData($pembimbingId)
    {
        $rawData = $this->repository->getWeeklyLogbookStats($pembimbingId);

        $categories = [];
        $totalSeries = [];

        foreach ($rawData as $data) {
            $weekNum = substr($data->week, 4, 2);
            $categories[] = "Minggu " . (int)$weekNum;
            $totalSeries[] = (int) $data->total_count;
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Logbook Diisi', 'data' => $totalSeries]
            ]
        ];
    }
}
