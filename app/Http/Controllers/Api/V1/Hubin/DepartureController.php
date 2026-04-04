<?php

namespace App\Http\Controllers\Api\V1\Hubin;

use App\Http\Controllers\Controller;
use App\Services\Hubin\DepartureService;
use Illuminate\Http\Request;

class DepartureController extends Controller
{
    protected $service;
    public function __construct(DepartureService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->getDepartureList());
    }

    public function verify(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:approve,reject']);
        $message = $this->service->verifyDeparture($id, $request->action);
        return response()->json(['message' => $message]);
    }

    public function generateSurat($id)
    {
        try {
            $letterNumber = $this->service->generateLetter($id);
            return response()->json(['message' => 'Surat berhasil dibuat.', 'letter_number' => $letterNumber]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
