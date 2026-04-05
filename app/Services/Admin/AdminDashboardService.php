<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminDashboardRepository;
use Illuminate\Support\Facades\Cache;

class AdminDashboardService
{
    protected $repository;
    public function __construct(AdminDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardStats()
    {
        return Cache::remember('admin_stats', 120, function () {
            $regStatus = $this->repository->getSettingValue('pkl_registration_status');
            return [
                'totalStudents' => $this->repository->countStudents(),
                'totalTeachers' => $this->repository->countTeachers(),
                'totalIndustries' => $this->repository->countIndustries(),
                'systemStatus' => $regStatus === 'Buka' ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup',
                'last_updated' => now()->format('H:i')
            ];
        });
    }

    public function getAllLogs()
    {
        return $this->repository->getAuditLogs();
    }
}
