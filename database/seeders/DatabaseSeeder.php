<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat peran (Menggunakan firstOrCreate agar tidak duplikat saat dijalankan kembali)
        $roles = ['Admin', 'Siswa', 'Pembimbing', 'Koordinator', 'Hubin'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Buat Akun Auth Admin di m_users
        $adminUser = User::create([
            'email' => 'admin@smkpgritelagasari.sch.id',
            'password' => Hash::make('admin'),
            'is_active' => true,
        ]);

        // 3. Menetapkan Peran Admin
        $adminUser->assignRole('Admin');

        // 4. Buat Profil Admin di m_teachers
        Teacher::create([
            'user_id' => $adminUser->id,
            'name' => 'Administrator',
        ]);

        $this->command->info('Database berhasil di-seed! Akun admin: admin@smkpgritelagasari.sch.id | admin');
    }
}

