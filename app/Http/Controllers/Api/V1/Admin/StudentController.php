<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Services\Admin\StudentService;
use App\Repositories\Admin\StudentRepository;

class StudentController extends Controller
{
    protected $service, $repo;
    public function __construct(StudentService $service, StudentRepository $repo)
    {
        $this->service = $service;
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        return response()->json($this->repo->getAll($request->query('search')));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:m_users,email',
            'identifier' => 'required|string|unique:m_students,nis',
            'jurusan' => 'nullable|string',
            'kelas' => 'nullable|string',
            'academic_year_id' => 'nullable|exists:m_academic_years,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'required|in:Aktif,Nonaktif'
        ]);

        $validated['nis'] = $validated['identifier'];

        $student = $this->service->createStudent($validated);

        AuditLog::record('m_students', 'create', "Menambah Siswa: {$validated['name']}");

        return response()->json([
            'message' => 'Data Siswa berhasil ditambahkan',
            'data' => $student
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $userId = $student->user_id;

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => "required|email|unique:m_users,email,{$userId}",
            'identifier' => "required|string|unique:m_students,nis,{$id}",
            'jurusan' => 'nullable|string',
            'kelas' => 'nullable|string',
            'academic_year_id' => 'nullable|exists:m_academic_years,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'required|in:Aktif,Nonaktif'
        ]);

        $validated['nis'] = $validated['identifier'];

        $updatedStudent = $this->service->updateStudent($id, $validated);

        AuditLog::record('m_students', 'update', "Memperbarui data Siswa: {$validated['name']}");

        return response()->json([
            'message' => 'Data Siswa berhasil diperbarui',
            'data' => $updatedStudent
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $name = $student->name;

        $this->service->deleteStudent($id);

        AuditLog::record('m_students', 'delete', "Menghapus Siswa: {$name}");

        return response()->json(['message' => 'Data Siswa berhasil dihapus']);
    }
}
