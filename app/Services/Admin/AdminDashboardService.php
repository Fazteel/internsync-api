<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminDashboardRepository;

class AdminDashboardService
{
    protected $repository;
    public function __construct(AdminDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardStats()
    {
        $regStatus = $this->repository->getSettingValue('pkl_registration_status');
        return [
            'totalStudents' => $this->repository->countStudents(),
            'totalTeachers' => $this->repository->countTeachers(),
            'totalIndustries' => $this->repository->countIndustries(),
            'systemStatus' => $regStatus === 'Buka' ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup'
        ];
    }

    public function getAllLogs()
    {
        return $this->repository->getAuditLogs();
    }
}
