<?php

namespace App\Services\Admin;

use App\Models\{User, Teacher};
use Illuminate\Support\Facades\{DB, Hash, Storage};

class TeacherService
{
    public function createTeacher($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make('12345678'),
                'is_active' => $data['status'] === 'Aktif'
            ]);
            $user->assignRole($data['role']);

            $path = null;
            if (isset($data['signature'])) {
                $path = $data['signature']->store('signatures', 'public');
            }

            return Teacher::create([
                'user_id' => $user->id,
                'nip' => $data['nip'],
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'signature_path' => $path
            ]);
        });
    }

    public function updateTeacher($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $teacher = Teacher::findOrFail($id);
            $user = $teacher->user;

            $user->update([
                'email' => $data['email'],
                'is_active' => $data['status'] === 'Aktif'
            ]);
            $user->syncRoles($data['role']);

            if (isset($data['signature'])) {
                if ($teacher->signature_path) {
                    Storage::disk('public')->delete($teacher->signature_path);
                }
                $data['signature_path'] = $data['signature']->store('signatures', 'public');
            }

            $teacher->update([
                'nip' => $data['nip'],
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'signature_path' => $data['signature_path'] ?? $teacher->signature_path
            ]);

            return $teacher;
        });
    }

    public function deleteTeacher($id)
    {
        return DB::transaction(function () use ($id) {
            $teacher = Teacher::findOrFail($id);
            if ($teacher->signature_path) {
                Storage::disk('public')->delete($teacher->signature_path);
            }
            $user = $teacher->user;
            $teacher->delete();
            $user->delete();
            return true;
        });
    }
}
