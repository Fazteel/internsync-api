<?php

namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Services\Koordinator\PlacementService;
use App\Repositories\Koordinator\PlacementRepository;

class PlacementController extends Controller
{
    protected $placementService;
    protected $placementRepo;

    public function __construct(PlacementService $placementService, PlacementRepository $placementRepo)
    {
        $this->placementService = $placementService;
        $this->placementRepo = $placementRepo;
    }

    public function index()
    {
        $data = $this->placementService->getFormattedStudents();
        return response()->json($data);
    }

    public function industries()
    {
        $industries = $this->placementRepo->getActiveIndustriesWithCount()->map(function ($ind) {
            return [
                'id' => $ind->id,
                'name' => $ind->name,
                'kuota_total' => $ind->kuota_siswa,
                'sisa_kuota' => max(0, $ind->kuota_siswa - $ind->internships_count)
            ];
        });
        return response()->json($industries);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:m_students,id',
            'industry_id' => 'required|exists:m_industries,id',
            'duration' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'is_extended' => 'nullable|boolean',
            'extension_month' => 'required_if:is_extended,true|nullable|integer|min:1'
        ]);

        try {
            $internship = $this->placementService->storePlacement($validated);
            return response()->json([
                'message' => 'Data penempatan berhasil disimpan!',
                'data' => $internship
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function withdraw($id)
    {
        $this->placementService->withdrawPlacement($id);
        return response()->json(['message' => 'Siswa berhasil ditarik dari industri.']);
    }
}
