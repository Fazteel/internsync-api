<?php

namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use App\Services\Koordinator\SummaryService;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    protected $service;

    public function __construct(SummaryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'major', 'status']);
        return response()->json($this->service->getFormattedSummary($filters));
    }

    public function downloadExcel(Request $request)
    {
        return $this->service->exportExcel($request->all());
    }

    public function downloadStudentPdf($id)
    {
        return $this->service->exportStudentPdf($id);
    }
}
