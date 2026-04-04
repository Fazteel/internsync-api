<?php

namespace App\Services\Pembimbing;

use App\Repositories\Pembimbing\PembimbingDashboardRepository;
use Carbon\Carbon;

class PembimbingDashboardService
{
    protected $repository;

    public function __construct(PembimbingDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardData($pembimbingId)
    {
        $metrics = [
            'total_bimbingan' => $this->repository->countActiveInternships($pembimbingId),
            'menunggu_verifikasi' => $this->repository->countSubmittedLogbooks($pembimbingId),
            'kunjungan_bulan_ini' => $this->repository->countApprovedVisitsThisMonth($pembimbingId),
        ];

        $pendingLogbooks = $this->repository->getLatestSubmittedLogbooks($pembimbingId)
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'studentName' => $log->internship->student->user->name ?? '-',
                    'industry' => $log->internship->industry->name ?? '-',
                    'date' => Carbon::parse($log->date)->translatedFormat('d M Y'),
                    'activity' => $log->activity,
                ];
            });

        $chartData = $this->formatChartData($pembimbingId);

        return [
            'metrics' => $metrics,
            'pending_logbooks' => $pendingLogbooks,
            'chart' => $chartData
        ];
    }

    private function formatChartData($pembimbingId)
    {
        $rawData = $this->repository->getWeeklyLogbookStats($pembimbingId);
        
        $categories = [];
        $approvedSeries = [];
        $revisedSeries = [];

        foreach ($rawData as $data) {
            $weekNum = substr($data->week, 4, 2); 
            $categories[] = "Mg " . $weekNum;
            $approvedSeries[] = (int) $data->approved_count;
            $revisedSeries[] = (int) $data->revised_count;
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Logbook Disetujui', 'data' => $approvedSeries],
                ['name' => 'Revisi', 'data' => $revisedSeries],
            ]
        ];
    }
}