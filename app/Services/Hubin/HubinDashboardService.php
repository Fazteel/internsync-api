<?php

namespace App\Services\Hubin;

use App\Repositories\Hubin\HubinDashboardRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class HubinDashboardService
{
    protected $repository;

    public function __construct(HubinDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getStats()
    {
        return Cache::remember('hubin_stas', 120, function () {
            $totalIndustri = $this->repository->countActiveIndustries();

            $recentApproval = $this->repository->getRecentPendingInternships()
                ->map(fn($intern) => [
                    'id' => $intern->id,
                    'requester' => $intern->coordinator->name ?? 'Koordinator PKL',
                    'role' => 'Koordinator',
                    'type' => 'Pemberangkatan PKL (' . ($intern->student->user->name ?? 'Siswa') . ')',
                    'date' => Carbon::parse($intern->updated_at)->translatedFormat('d M Y')
                ]);

            $sebaran = $this->calculateIndustryDistribution($totalIndustri);

            return [
                'metrics' => [
                    'total_industri' => $totalIndustri,
                    'total_requests' => $this->repository->countPendingVisits(),
                ],
                'table' => $recentApproval,
                'sebaran' => $sebaran,
                'last_updated' => now()->format('H:i')
            ];
        });
    }

    private function calculateIndustryDistribution($total)
    {
        return $this->repository->getAllActiveIndustries()
            ->groupBy(function ($item) {
                if (preg_match('/(Kab\.|Kota)\s+[a-zA-Z\s]+/', $item->address, $matches)) {
                    return trim($matches[0]);
                }
                return 'Lainnya';
            })
            ->map(fn($group, $key) => [
                'name' => $key,
                'count' => $group->count(),
                'percentage' => $total > 0 ? round(($group->count() / $total) * 100) : 0
            ])
            ->values()
            ->sortByDesc('count')
            ->take(5)
            ->toArray();
    }
}
