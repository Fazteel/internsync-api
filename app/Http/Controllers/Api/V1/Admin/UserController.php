<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Support\Facades\DB;
use Psy\Util\Str;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');
        
        $users = $this->userService->getUsers($search, $role);
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:m_users,email',
            'role' => 'required|string|exists:m_roles,name',
            'status' => 'required|in:Aktif,Nonaktif',
            'jurusan' => 'nullable|string',
            'kelas' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255'
        ];

        if ($request->role === 'Siswa') {
            $rules['identifier'] = 'required|string|unique:m_students,nis';
        } else {
            $rules['identifier'] = 'required|string|unique:m_users,nip';
        }

        $validated = $request->validate($rules);

        $user = $this->userService->createUser($validated);
        return response()->json(['message' => 'User berhasil ditambahkan', 'data' => $user], 201);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:m_users,email,'.$id,
            'role' => 'required|string|exists:m_roles,name',
            'status' => 'required|in:Aktif,Nonaktif',
            'jurusan' => 'nullable|string',
            'kelas' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255'
        ];

        if ($request->role === 'Siswa') {
            $student = Student::where('user_id', $id)->first();
            $studentId = $student ? $student->id : 'NULL';
            $rules['identifier'] = 'required|string|unique:m_students,nis,'.$studentId;
        } else {
            $rules['identifier'] = 'required|string|unique:m_users,nip,'.$id;
        }

        $validated = $request->validate($rules);

        $user = $this->userService->updateUser($id, $validated);
        return response()->json(['message' => 'User berhasil diupdate', 'data' => $user]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $nama = $user ? $user->name : 'ID '.$id;
        $this->userService->delete($id);

        AuditLog::record(
            'm_users', 
            'delete', 
            "Menghapus akun: {$nama}"
        );
        return response()->json(['message' => 'User berhasil dihapus']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new \App\Imports\UsersImport;
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
            
            AuditLog::record(
                'm_users', 
                'import', 
                "Mengimpor data pengguna dari Excel. Sukses: {$import->successCount}, Gagal: {$import->failCount}"
            );

            return response()->json([
                'message' => 'Proses import selesai',
                'success' => $import->successCount,
                'failed' => $import->failCount
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal import data: ' . $e->getMessage()], 500);
        }
    }

    public function resendActivationEmail(User $user)
    {
        $token = \Illuminate\Support\Str::random(60);

        DB::table('tr_password_reset_token')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => \Illuminate\Support\Facades\Hash::make($token), 
                'created_at' => now()
            ]
        );

        return response()->json(['message' => 'Email aktivasi berhasil dikirim ulang.']);
    }

}