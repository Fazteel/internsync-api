<?php

namespace App\Http\Controllers\Api\V1\Pembimbing;

use App\Http\Controllers\Controller;
use App\Services\Pembimbing\MonitoringService;
use Illuminate\Http\Request;
use Exception;

class MonitoringController extends Controller
{
    protected $service;

    public function __construct(MonitoringService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json($this->service->getVisitsList($request->user()->id));
    }

    public function showForm(Request $request, $visitId)
    {
        return response()->json($this->service->getMonitoringForm($visitId, $request->user()->id));
    }

    public function store(Request $request, $visitId)
    {
        $validated = $request->validate([
            'evaluations' => 'required|array',
            'evaluations.*.internship_id' => 'required|exists:tr_internships,id',
            'evaluations.*.notes' => 'nullable|string',
        ]);

        try {
            $this->service->submitMonitoring($visitId, $request->user()->id, $validated);
            return response()->json(['message' => 'Laporan monitoring berhasil disimpan secara massal!']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function exportExcel(Request $request, $visitId)
    {
        try {
            return $this->service->exportMonitoringExcel($visitId, $request->user()->id);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal export data: ' . $e->getMessage()], 400);
        }
    }
}
