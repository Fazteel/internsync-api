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
        // 1. Buat Roles (Pake firstOrCreate biar kaga duplikat pas di-run ulang)
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

        // 3. Tempel Role Admin (Pake Spatie method assignRole lebih simple)
        $adminUser->assignRole('Admin');

        // 4. Buat Profil Admin di m_teachers (Karena name, nip, dll pindah ke sini)
        Teacher::create([
            'user_id' => $adminUser->id,
            'nip' => '3123512901',
            'name' => 'Fahmi Andika',
            'phone' => '0895333535044',
            'address' => 'Bandung, Jawa Barat',
            'signature_path' => null, // Admin opsional pake TTD
        ]);

        // 5. Tambahan: Buat Master Data Awal (Biar sistem langsung bisa dipake)

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

        $this->command->info('Arsitektur baru berhasil di-seed! Login pake: fahmiandika31@gmail.com | admin123');
    }
}
