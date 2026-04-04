<?php

namespace App\Http\Controllers\Api\V1\Pembimbing;

use App\Http\Controllers\Controller;
use App\Services\Pembimbing\IndustryVisitService;
use Illuminate\Http\Request;

class IndustryVisitController extends Controller
{
    protected $visitService;

    public function __construct(IndustryVisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    public function index(Request $request)
    {
        $trips = $this->visitService->getVisitsByPembimbing($request->user()->id);
        return response()->json($trips);
    }

    public function industries(Request $request)
    {
        $industries = $this->visitService->getAssignedIndustries($request->user()->id);
        return response()->json($industries);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'industry_id' => 'required|exists:m_industries,id',
            'planned_date' => 'required|date',
            'purpose' => 'required|string|max:500',
        ]);

        $trip = $this->visitService->createVisit($request->user()->id, $validated);

        return response()->json([
            'message' => 'Pengajuan perjalanan dinas berhasil dikirim.',
            'data' => $trip
        ], 201);
    }

    public function viewSPPD(Request $request, $id)
    {
        try {
            $url = $this->visitService->getVisitFileUrl($id, $request->user()->id);
            
            return response()->json([
                'file_url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        }
    }
}