<?php

namespace App\Http\Controllers\Api\V1\Hubin;

use App\Http\Controllers\Controller;
use App\Services\Hubin\HubinInternshipService;
use Illuminate\Http\Request;

class HubinInternshipController extends Controller
{
    protected $service;

    public function __construct(HubinInternshipService $service)
    {
        $this->service = $service;
    }

    public function getPendingApplications()
    {
        try {
            $data = $this->service->getPendingApplications();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menarik data antrian: ' . $e->getMessage()], 500);
        }
    }

    public function getPendingPlacements()
    {
        try {
            $data = $this->service->getPendingPlacements();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menarik data antrian: ' . $e->getMessage()], 500);
        }
    }

    public function processApplication(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        try {
            $application = $this->service->processApplication($id, $validated);

            $msg = $validated['action'] === 'approve'
                ? 'Pengajuan berhasil dan Surat telah di-generate!'
                : 'Pengajuan telah ditolak!';

            return response()->json([
                'message' => $msg,
                'data' => $application
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memproses pengajuan: ' . $e->getMessage()], 500);
        }
    }

    public function processPlacement(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        try {
            $application = $this->service->processPlacement($id, $validated);

            $msg = $validated['action'] === 'approve'
                ? 'Pengiriman berhasil dan Surat telah di-generate!'
                : 'Pengajuan telah ditolak!';

            return response()->json([
                'message' => $msg,
                'data' => $application
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memproses pengiriman' . $e->getMessage()], 500);
        }
    }
}
