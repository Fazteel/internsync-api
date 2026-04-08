<?php

namespace App\Services\Admin;

use App\Models\{User, Student};
use Illuminate\Support\Facades\{DB, Hash};

class StudentService
{
    public function createStudent($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make('12345678'),
                'is_active' => $data['status'] === 'Aktif'
            ]);
            $user->assignRole('Siswa');

            return Student::create([
                'user_id' => $user->id,
                'academic_year_id' => $data['academic_year_id'] ?? null,
                'nis' => $data['nis'],
                'name' => $data['name'],
                'jurusan' => $data['jurusan'] ?? null,
                'kelas' => $data['kelas'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'is_pkl' => false,
            ]);
        });
    }

    public function updateStudent($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $student = Student::findOrFail($id);
            $user = $student->user;

            $user->update([
                'email' => $data['email'],
                'is_active' => $data['status'] === 'Aktif'
            ]);

            $student->update([
                'academic_year_id' => $data['academic_year_id'] ?? null,
                'nis' => $data['nis'],
                'name' => $data['name'],
                'jurusan' => $data['jurusan'] ?? null,
                'kelas' => $data['kelas'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ]);

            return $student->load('user');
        });
    }

    public function deleteStudent($id)
    {
        return DB::transaction(function () use ($id) {
            $student = Student::findOrFail($id);
            $user = $student->user;
            $student->delete();
            $user->delete();
            return true;
        });
    }
}
