<?php

namespace App\Http\Controllers\Api\V1\Pembimbing;

use App\Http\Controllers\Controller;
use App\Services\Pembimbing\EvaluationService;
use Illuminate\Http\Request;
use Exception;

class EvaluationController extends Controller
{
    protected $evalService;

    public function __construct(EvaluationService $evalService)
    {
        $this->evalService = $evalService;
    }

    public function index(Request $request)
    {
        $evaluations = $this->evalService->getStudentEvaluations($request->user()->id);
        return response()->json($evaluations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'internship_id' => 'required|exists:tr_internships,id',
            'score' => 'required|numeric|min:0|max:100',
            'notes' => 'required|string',
        ]);

        try {
            $this->evalService->processEvaluation($request->user()->id, $validated);
            return response()->json(['message' => 'Penilaian akhir berhasil disimpan dan status magang diperbarui.']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}