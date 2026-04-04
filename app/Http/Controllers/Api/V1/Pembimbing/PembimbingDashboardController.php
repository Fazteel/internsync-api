<?php

namespace App\Http\Controllers\Api\V1\Pembimbing;

use App\Http\Controllers\Controller;
use App\Services\Pembimbing\PembimbingDashboardService;
use Illuminate\Http\Request;

class PembimbingDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(PembimbingDashboardService $pembimbingDashboardService)
    {
        $this->dashboardService = $pembimbingDashboardService;
    }

    public function index(Request $request)
    {
        $data = $this->dashboardService->getDashboardData($request->user()->id);
        return response()->json($data);
    }
}