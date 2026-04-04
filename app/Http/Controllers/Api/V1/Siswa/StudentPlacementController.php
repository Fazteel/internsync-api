<?php

namespace App\Http\Controllers\Api\V1\Siswa;

use App\Http\Controllers\Controller;
use App\Services\Siswa\StudentPlacementService;
use Illuminate\Http\Request;

class StudentPlacementController extends Controller
{
    protected $service;
    public function __construct(StudentPlacementService $service)
    {
        $this->service = $service;
    }

    public function show(Request $request)
    {
        try {
            return response()->json(['data' => $this->service->getPlacementDetails($request->user()->id)]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
