<?php

namespace App\Http\Controllers\Api\V1\Pembimbing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pembimbing\LogbookApprovalService;

class LogbookApprovalController extends Controller
{
    protected $logbookService;

    public function __construct(LogbookApprovalService $logbookService)
    {
        $this->logbookService = $logbookService;
    }

    public function index(Request $request)
    {
        $logbooks = $this->logbookService->getLogbooks($request->user()->id);

        return response()->json($logbooks);
    }

    public function verify(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,revised',
            'revisionNote' => 'required_if:status,revised|string|nullable'
        ]);

        $this->logbookService->processVerification($id, $request->user()->id, $validated);

        return response()->json(['message' => 'Logbook berhasil diverifikasi.']);
    }

    public function bulkVerify(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tr_logbooks,id',
            'status' => 'required|in:approved,rejected'
        ]);

        $this->logbookService->bulkProcess($request->user()->id, $validated);

        return response()->json(['message' => count($validated['ids']) . ' logbook berhasil diproses.']);
    }
}
