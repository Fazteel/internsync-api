<?php

namespace App\Services\Admin;

use App\Mail\AuthMail;
use App\Repositories\Admin\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Student;
use App\Models\AuditLog;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
    }

    public function getUsers($search, $role)
    {
        return $this->userRepository->getAllUsersWithRoles($search, $role);
    }

    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'nip' => $data['role'] !== 'Siswa' ? $data['identifier'] : null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                // 'password' => Hash::make(Str::random(32)), // Password ketika menggunakan aktivasi email
                'password' => Hash::make('12345678'),
                'is_active' => $data['status'] === 'Aktif'
            ]);

            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $this->userRepository->syncRoles($user->id, [$role->id]);
            }

            if ($data['role'] === 'Siswa') {
                Student::create([
                    'user_id' => $user->id,
                    'academic_year_id' => $data['academic_year_id'] ?? null,
                    'nis' => $data['identifier'],
                    'jurusan' => $data['jurusan'] ?? null,
                    'kelas' => $data['kelas'] ?? null,
                    'is_pkl' => $data['is_pkl'] ?? false,
                ]);
            }

            $token = Str::random(60);
            DB::table('tr_password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            //Fitur aktivasi akun via send email
            // $link = env('FRONTEND_URL', 'http://localhost:5173') . '/set-password?token=' . $token . '&email=' . urlencode($user->email);

            // try {
            //     Mail::to($user->email)->send(new AuthMail($user, $link, 'activation'));
            // } catch (\Exception $e) {
            //     Log::error('Gagal mengirim email aktivasi ke ' . $user->email . 'Error: ' . $e->getMessage());
            // }

            AuditLog::record(
                'm_users',
                'create',
                "Menambahkan akun {$data['role']} baru: {$data['name']} ({$data['identifier']})"
            );
            return $user;
        });
    }

    public function updateUser($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->userRepository->update($id, [
                'name' => $data['name'],
                'email' => $data['email'],
                'nip' => $data['role'] !== 'Siswa' ? $data['identifier'] : null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => $data['status'] === 'Aktif'
            ]);

            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $this->userRepository->syncRoles($user->id, [$role->id]);
            }

            if ($data['role'] === 'Siswa') {
                Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'academic_year_id' => $data['academic_year_id'] ?? null,
                        'nis' => $data['identifier'],
                        'jurusan' => $data['jurusan'] ?? null,
                        'kelas' => $data['kelas'] ?? null,
                        'is_pkl' => $data['is_pkl'] ?? ($student->is_pkl ?? false),
                    ]
                );
            } else {
                Student::where('user_id', $user->id)->delete();
            }

            AuditLog::record(
                'm_users',
                'update',
                "Memperbarui akun {$data['role']}: {$data['name']} ({$data['identifier']})"
            );
            return $user;
        });
    }
}
