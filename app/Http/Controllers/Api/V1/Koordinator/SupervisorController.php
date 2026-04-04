<?php

namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Koordinator\SupervisorService;

class SupervisorController extends Controller
{
    protected $service;

    public function __construct(SupervisorService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->getPlottingList());
    }

    public function teachers()
    {
        return response()->json($this->service->getAvailableTeachers());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:m_students,id',
            'pembimbing_id' => 'required|exists:m_users,id',
        ]);

        $this->service->assignTeacher($validated);

        return response()->json(['message' => 'Guru pembimbing berhasil ditugaskan!']);
    }
}
