<?php

namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use App\Services\Koordinator\IndustryVisitService;
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
        $trips = $this->visitService->getVisitsByCoordinator($request->user()->id);
        return response()->json($trips);
    }

    public function getOptions(Request $request)
    {
        $options = $this->visitService->getFormOptions($request->user()->id);
        return response()->json($options);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pembimbing_id' => 'required|exists:m_users,id',
            'industry_id' => 'required|exists:m_industries,id',
            'planned_date' => 'required|date',
            'purpose' => 'required|string|max:500',
        ]);

        $trip = $this->visitService->createVisit($request->user()->id, $validated);

        return response()->json([
            'message' => 'Pengajuan perjalanan dinas berhasil dikirim ke Hubin.',
            'data' => $trip
        ], 201);
    }
}
