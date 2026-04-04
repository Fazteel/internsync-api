<?php

namespace App\Http\Controllers\Api\V1\Siswa;

use App\Http\Controllers\Controller;
use App\Services\Siswa\StudentEvaluationService;
use Illuminate\Http\Request;

class StudentEvaluationController extends Controller
{
    protected $service;

    public function __construct(StudentEvaluationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json($this->service->getData($request->user()->id));
    }

    public function download(Request $request)
    {
        try {
            $pdfContent = $this->service->generatePdf($request->user()->id);
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Nilai_PKL.pdf"');
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}