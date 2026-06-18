<?php

namespace App\Http\Controllers\Api\V1\Hubin;

use App\Http\Controllers\Controller;
use App\Services\Hubin\IndustryService;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    protected $service;
    public function __construct(IndustryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json($this->service->getIndustries($request->search));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'hr_name' => 'required|string',
            'kuota_siswa' => 'required|integer'
        ]);
        $data = $this->service->createIndustry($request->all(), $request->file('mou_file'));
        return response()->json(['message' => 'Industri berhasil ditambahkan', 'data' => $data], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $this->service->updateIndustry($id, $request->all(), $request->file('mou_file'));
        return response()->json(['message' => 'Industri berhasil diperbarui', 'data' => $data]);
    }

    public function destroy($id)
    {
        $this->service->deleteIndustry($id);
        return response()->json(['message' => 'Industri berhasil dihapus']);
    }
}
