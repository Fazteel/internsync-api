<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin',
            'Siswa',
            'Pembimbing',
            'Koordinator',
            'Hubin'
        ];

        $roleModels = [];
        foreach ($roles as $roleName) {
            $roleModels[$roleName] = Role::create(['name' => $roleName]);
        }

        $admin = User::create([
            'name' => 'Fahmi Andika',
            'email' => 'fahmiandika31@gmail.com',
            'nip' => '3123512901',
            'password' => Hash::make('admin123'),
            'phone' => '0895333535044',
            'address' => 'Bandung, Jawa Barat',
            'is_active' => true,
        ]);
        $admin->roles()->attach($roleModels['Admin']->id);

        $major = ['RPL', 'TKJ'];

        $this->command->info('Data Roles dan Admin berhasil di-seed.');
    }
}