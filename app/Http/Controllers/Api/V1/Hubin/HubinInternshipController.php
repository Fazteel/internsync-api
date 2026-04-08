<?php

namespace App\Http\Controllers\Api\V1\Hubin;

use App\Http\Controllers\Controller;
use App\Services\Hubin\InternshipApprovalService;
use Illuminate\Http\Request;

class InternshipApprovalController extends Controller
{
    protected $service;

    public function __construct(InternshipApprovalService $service)
    {
        $this->service = $service;
    }

    public function getPendingApplications()
    {
        try {
            $data = $this->service->getPendingApplications();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal narik data antrian: ' . $e->getMessage()], 500);
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
}
