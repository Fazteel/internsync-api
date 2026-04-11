<?php

namespace App\Http\Controllers\Api\V1\Pembimbing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pembimbing\LogbookMonitoringService;

class LogbookMonitoringController extends Controller
{
    protected $logbookService;

    public function __construct(LogbookMonitoringService $logbookService)
    {
        $this->logbookService = $logbookService;
    }

    public function index(Request $request)
    {
        $logbooks = $this->logbookService->getLogbooks($request->user()->id, $request->query('student_id'));
        return response()->json($logbooks);
    }

    public function exportPdf(Request $request)
    {
        return $this->logbookService->exportPdf($request->user()->id, $request->query('student_id'));
    }
}
