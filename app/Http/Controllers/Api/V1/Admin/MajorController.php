<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Major;
use App\Services\Admin\MajorService;

class MajorController extends Controller
{
    protected $majorService;

    public function __construct(MajorService $majorService)
    {
        $this->majorService = $majorService;
    }

    public function index()
    {
        return response()->json($this->majorService->getAll());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'major_code' => 'required|string|max:20|unique:m_majors,major_code',
            'major_name' => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);
        
        $major = $this->majorService->create($validated);

        AuditLog::record(
            'm_majors', 
            'create', 
            "Menambahkan jurusan baru: {$major->major_name} ({$major->major_code})"
        );
        return response()->json(['message' => 'Jurusan berhasil ditambahkan', 'data' => $major], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'major_code' => 'required|string|max:20|unique:m_majors,major_code,'.$id,
            'major_name' => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);

        $major = $this->majorService->update($id, $validated);

        AuditLog::record(
            'm_majors', 
            'update', 
            "Memperbarui jurusan: {$major->major_name} ({$major->major_code})"
        );
        return response()->json(['message' => 'Jurusan berhasil diperbarui', 'data' => $major]);
    }

    public function destroy($id)
    {
        $major = Major::find($id);
        $nama = $major ? $major->major_name : 'ID '.$id;
            
        $this->majorService->delete($id);
        AuditLog::record(
            'm_majors', 
            'delete', 
            "Menghapus jurusan: {$nama}"
        );
        return response()->json(['message' => 'Jurusan berhasil dihapus']);
    }

}