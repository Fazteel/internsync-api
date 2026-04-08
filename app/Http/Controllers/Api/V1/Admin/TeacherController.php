<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\TeacherService;
use App\Repositories\Admin\TeacherRepository;
use App\Models\AuditLog;
use App\Models\Teacher;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    protected $service, $repo;

    public function __construct(TeacherService $service, TeacherRepository $repo)
    {
        $this->service = $service;
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $teachers = $this->repo->getAll($search, $role);
        return response()->json($teachers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:m_users,email',
            'identifier' => 'nullable|string|unique:m_teachers,nip',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Nonaktif',
            'role' => [
                'required',
                Rule::in(['Hubin', 'Koordinator', 'Pembimbing', 'Admin'])
            ],
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['nip'] = $validated['identifier'];
        if ($request->hasFile('signature')) {
            $validated['signature'] = $request->file('signature');
        }

        $teacher = $this->service->createTeacher($validated);

        AuditLog::record('m_teachers', 'create', "Menambah Guru/Staff: {$validated['name']}");

        return response()->json([
            'message' => 'Data Guru berhasil ditambahkan',
            'data' => $teacher
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        $userId = $teacher->user_id;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => "required|email|unique:m_users,email,{$userId}",
            'identifier' => "nullable|string|unique:m_teachers,nip,{$id}",
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Nonaktif',
            'role' => [
                'required',
                Rule::in(['Admin', 'Hubin', 'Koordinator', 'Pembimbing'])
            ],
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['nip'] = $validated['identifier'];
        if ($request->hasFile('signature')) {
            $validated['signature'] = $request->file('signature');
        }

        $updatedTeacher = $this->service->updateTeacher($id, $validated);

        AuditLog::record('m_teachers', 'update', "Memperbarui data Guru: {$validated['name']}");

        return response()->json([
            'message' => 'Data Guru berhasil diperbarui',
            'data' => $updatedTeacher
        ]);
    }

    public function destroy($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        $name = $teacher->name;

        $this->service->deleteTeacher($id);

        AuditLog::record('m_teachers', 'delete', "Menghapus akun Guru: {$name}");

        return response()->json(['message' => 'Data Guru berhasil dihapus']);
    }
}
