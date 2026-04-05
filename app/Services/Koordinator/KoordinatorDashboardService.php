<?php

namespace App\Services\Koordinator;

use App\Repositories\Koordinator\KoordinatorDashboardRepository;
use Illuminate\Support\Facades\Cache;

class KoordinatorDashboardService
{
    protected $repository;

    public function __construct(KoordinatorDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardStats()
    {
        return Cache::remember('koordinator_stas', 120, function () {
            $totalAll = $this->repository->getTotalStudentsCount();
            $belumDitempatkan = $this->repository->getStudentsWithoutPlacementCount();

            $recentStudents = $this->repository->getRecentStudents()->map(function ($student) {
                $status = 'Belum Ditempatkan';
                if ($student->internship && $student->internship->status === 'completed') {
                    $status = 'Selesai';
                } elseif ($student->internship && $student->internship->industry_id) {
                    $status = 'Aktif';
                }

                return [
                    'id' => $student->id,
                    'nis' => $student->nis,
                    'name' => $student->user->name ?? 'Tanpa Nama',
                    'major' => $student->jurusan ?? '-',
                    'industry' => $student->internship->industry->name ?? 'Belum Ada',
                    'status' => $status
                ];
            });

            return [
                'metrics' => [
                    'total_aktif' => $totalAll - $belumDitempatkan,
                    'belum_ditempatkan' => $belumDitempatkan,
                    'industri_aktif' => $this->repository->getActiveIndustriesCount()
                ],
                'table' => $recentStudents,
                'chart' => $this->prepareChartData(),
                'last_updated' => now()->format('H:i')
            ];
        });
    }

    private function prepareChartData()
    {
        $chartData = array_fill(0, 12, 0);
        $internships = $this->repository->getInternshipsThisYear();

        foreach ($internships as $internship) {
            $monthIndex = (int)date('n', strtotime($internship->start_date)) - 1;
            $chartData[$monthIndex]++;
        }

        return [
            'name' => 'Diberangkatkan',
            'data' => $chartData
        ];
    }
}
