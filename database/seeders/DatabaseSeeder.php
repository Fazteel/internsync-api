<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Teacher; // Import model Teacher
use App\Models\Major;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
            'email' => 'fahmiandika31@gmail.com',
            'password' => Hash::make('admin123'),
            'is_active' => true,
        ]);

        // 3. Menetapkan Peran Admin (Menggunakan metode assignRole Spatie yang lebih sederhana)
        $adminUser->assignRole('Admin');

        // 4. Buat Profil Admin di m_teachers (Karena name, nip, dll pindah ke sini)
        Teacher::create([
            'user_id' => $adminUser->id,
            'nip' => '3123512901',
            'name' => 'Fahmi Andika',
            'phone' => '0895333535044',
            'address' => 'Bandung, Jawa Barat',
            'signature_path' => null, // Admin opsional menggunakan tanda tangan
        ]);

        // 5. Tambahan: Membuat Data Master Awal (Agar sistem langsung dapat digunakan)

        // Buat Tahun Ajaran
        $academicYear = AcademicYear::firstOrCreate([
            'name' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true
        ]);

        // Buat Jurusan
        $majors = [
            ['major_code' => 'RPL', 'major_name' => 'Rekayasa Perangkat Lunak', 'is_active' => true],
            ['major_code' => 'TKJ', 'major_name' => 'Teknik Komputer dan Jaringan', 'is_active' => true],
        ];

        foreach ($majors as $m) {
            Major::firstOrCreate(['major_code' => $m['major_code']], $m);
        }

        $this->command->info('Arsitektur baru berhasil dimasukkan ke basis data! Silakan masuk menggunakan: fahmiandika31@gmail.com | admin123');
    }
}
