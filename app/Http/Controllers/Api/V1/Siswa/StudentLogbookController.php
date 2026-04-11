<?php

namespace App\Http\Controllers\Api\V1\Siswa;

use App\Http\Controllers\Controller;
use App\Services\Siswa\StudentLogbookService;
use Illuminate\Http\Request;

class StudentLogbookController extends Controller
{
    protected $service;
    public function __construct(StudentLogbookService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            return response()->json($this->service->listLogbooks($request->user()->id));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function store(Request $request)
    {
        $request->validate(['date' => 'required|date', 'activity' => 'required|string', 'attachment' => 'required|file|max:2048']);
        try {
            $this->service->storeLogbook($request->user()->id, $request->all(), $request->file('attachment'));
            return response()->json(['message' => 'Logbook berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate(['activity' => 'required|string', 'attachment' => 'nullable|file|max:2048']);
        $this->service->updateLogbook($id, $request->activity, $request->file('attachment'));
        return response()->json(['message' => 'Logbook berhasil diperbarui.']);
    }
}
