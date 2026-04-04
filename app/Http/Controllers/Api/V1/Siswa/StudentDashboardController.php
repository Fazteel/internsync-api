<?php

namespace App\Http\Controllers\Api\V1\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Siswa\StudentDashboardService;

class StudentDashboardController extends Controller
{
    protected $service;
    public function __construct(StudentDashboardService $service)
    {
        $this->service = $service;
    }

    public function stats(Request $request)
    {
        return response()->json($this->service->getStats($request->user()->id));
    }
}
