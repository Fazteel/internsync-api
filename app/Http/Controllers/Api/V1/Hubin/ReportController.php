<?php

namespace App\Http\Controllers\Api\V1\Hubin;

use App\Http\Controllers\Controller;
use App\Services\Hubin\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        return response()->json($this->reportService->getMasterReportData());
    }

    public function downloadExcel()
    {
        return $this->reportService->exportExcel();
    }
    public function downloadPdf()
    {
        return $this->reportService->exportPdf();
    }
}
