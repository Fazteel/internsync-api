<?php

namespace App\Http\Controllers\Api\V1\Hubin;

use App\Http\Controllers\Controller;
use App\Services\Hubin\HubinDashboardService;

class HubinDashboardController extends Controller
{
    protected $service;

    public function __construct(HubinDashboardService $service)
    {
        $this->service = $service;
    }

    public function stats()
    {
        $data = $this->service->getStats();
        return response()->json($data);
    }
}