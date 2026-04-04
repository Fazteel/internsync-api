<?php

namespace App\Http\Controllers\Api\V1\Hubin;

use App\Http\Controllers\Controller;
use App\Models\IndustryVisit;
use Illuminate\Http\Request;
use App\Services\Hubin\VisitApprovalService;
use Illuminate\Support\Facades\Storage;

class VisitApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(VisitApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index()
    {
        $trips = $this->approvalService->getAllVisits();
        return response()->json($trips);
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'feedback' => 'required_if:status,Rejected|string|nullable'
        ]);

        $this->approvalService->processApproval($id, $request->status, $request->feedback);

        return response()->json(['message' => 'Status persetujuan perjalanan dinas berhasil diperbarui.']);
    }

    public function generateSurat($id)
    {
        try {
            $visit = $this->approvalService->generateSPPD($id);
            
            return response()->json([
                'message' => 'Surat SPPD berhasil di-generate.',
                'file_url' => asset('storage/' . $visit->file_path)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function viewSurat($id)
    {
        $visit = IndustryVisit::findOrFail($id);

        if (!$visit->file_path || !Storage::disk('public')->exists($visit->file_path)) {
            return response()->json(['message' => 'Dokumen belum tersedia atau belum di-generate!'], 404);
        }

        return response()->json([
            'file_url' => asset('storage/' . $visit->file_path)
        ]);
    }
    
}
