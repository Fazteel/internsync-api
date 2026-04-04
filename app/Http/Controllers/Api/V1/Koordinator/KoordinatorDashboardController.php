<?php

namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use App\Services\Koordinator\KoordinatorDashboardService;

class KoordinatorDashboardController extends Controller
{
    protected $service;

    public function __construct(KoordinatorDashboardService $service)
    {
        $this->service = $service;
    }

    public function stats()
    {
        return response()->json($this->service->getDashboardStats());
    }
}
