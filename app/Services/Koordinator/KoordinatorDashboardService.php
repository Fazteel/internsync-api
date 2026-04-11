<?php

namespace App\Services\Koordinator;

use App\Repositories\Koordinator\KoordinatorDashboardRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class KoordinatorDashboardService
{
    protected $repository;

    public function __construct(KoordinatorDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardStats()
    {
        $coordinatorId = Auth::id();

        return Cache::remember('koordinator_dashboard_stats_' . $coordinatorId, 120, function () use ($coordinatorId) {

            $activePlacements = $this->repository->getRecentPlacements($coordinatorId);
            $sebaranIndustri = $this->repository->getIndustryDistribution($coordinatorId);

            return [
                'metrics' => [
                    'total_siswa' => $this->repository->getTotalStudentsCount(),
                    'siswa_aktif_pkl' => $this->repository->getActiveStudentsCount($coordinatorId),
                    'belum_ditempatkan' => $this->repository->getStudentsWithoutPlacementCount(),
                ],
                'sebaran_industri' => $sebaranIndustri,
                'recent_placements' => $activePlacements->map(function ($intern) {
                    return [
                        'id' => $intern->id,
                        'name' => $intern->student->name ?? 'N/A',
                        'nis' => $intern->student->nis,
                        'major' => $intern->student->jurusan,
                        'industry' => $intern->industry->name,
                        'status' => $intern->status
                    ];
                }),
                'monthly_chart' => $this->prepareChartData($coordinatorId),
                'last_updated' => now()->format('H:i')
            ];
        });
    }

    private function prepareChartData($coordinatorId)
    {
        $chartData = array_fill(0, 12, 0);
        $internships = $this->repository->getInternshipsThisYear($coordinatorId);

        foreach ($internships as $internship) {
            if ($internship->start_date) {
                $monthIndex = (int)date('n', strtotime($internship->start_date)) - 1;
                $chartData[$monthIndex]++;
            }
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'datasets' => [
                [
                    'name' => 'Siswa Berangkat',
                    'data' => $chartData
                ]
            ]
        ];
    }
}
