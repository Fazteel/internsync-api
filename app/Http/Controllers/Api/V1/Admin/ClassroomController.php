<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Classroom;
use App\Services\Admin\ClassroomService;

class ClassroomController extends Controller
{
    protected $classroomService;

    public function __construct(ClassroomService $classroomService)
    {
        $this->classroomService = $classroomService;
    }

    public function index()
    {
        return response()->json($this->classroomService->getAllClassrooms());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'major_id' => 'required|exists:m_majors,id',
            'name' => 'required|string|max:50|unique:m_classrooms,name',
            'is_active' => 'boolean'
        ]);
        
        $classroom = $this->classroomService->create($validated);
        $classroom->load('major'); 
        
        AuditLog::record(
            'm_classrooms', 
            'create', 
            "Menambahkan kelas baru: {$classroom->name} (Jurusan: {$classroom->major->major_name})"
        );
        return response()->json(['message' => 'Kelas berhasil ditambahkan', 'data' => $classroom], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'major_id' => 'required|exists:m_majors,id',
            'name' => 'required|string|max:50|unique:m_classrooms,name,'.$id,
            'is_active' => 'boolean'
        ]);

        $classroom = $this->classroomService->update($id, $validated);
        $classroom->load('major');

        AuditLog::record(
            'm_classrooms', 
            'update', 
            "Memperbarui kelas: {$classroom->name} (Jurusan: {$classroom->major->major_name})"
        );
        return response()->json(['message' => 'Kelas berhasil diperbarui', 'data' => $classroom]);
    }

    public function destroy($id)
    {
        $classroom = Classroom::find($id);
        $nama = $classroom ? $classroom->name : 'ID '.$id;

        $this->classroomService->delete($id);

        AuditLog::record(
            'm_classrooms', 
            'delete', 
            "Menghapus kelas: {$nama}"
        );
        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}