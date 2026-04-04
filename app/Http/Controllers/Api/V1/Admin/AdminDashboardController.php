<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;

class AdminDashboardController extends Controller
{
    protected $service;
    public function __construct(AdminDashboardService $service)
    {
        $this->service = $service;
    }

    public function stats()
    {
        return response()->json($this->service->getDashboardStats());
    }

    public function logs()
    {
        return response()->json($this->service->getAllLogs());
    }
}
