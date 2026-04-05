<?php

namespace App\Http\Controllers\Api\V1\Pembimbing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pembimbing\SuperviseeService;

class SuperviseeController extends Controller
{
    protected $superviseeService;

    public function __construct(SuperviseeService $superviseeService)
    {
        $this->superviseeService = $superviseeService;
    }

    public function index(Request $request)
    {
        $data = $this->superviseeService->getList($request->user()->id);

        return response()->json($data);
    }

    public function show($id, Request $request)
    {
        $data = $this->superviseeService->getDetail($id, $request->user()->id);

        return response()->json($data);
    }

    public function reportProblem(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $this->superviseeService->terminateInternship($id, $request->user()->id, $request->reason);

            return response()->json([
                'message' => 'Siswa berhasil ditandai bermasalah dan akses logbook telah dikunci.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
