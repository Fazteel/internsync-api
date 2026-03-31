<?php

namespace App\Imports;

use App\Jobs\ProcessUserActivation;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Major;
use App\Models\Classroom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow
{
    public $successCount = 0;
    public $failCount = 0;

    public function collection(Collection $rows)
    {
        if ($rows->count() > 100)
            throw new \Exception('Maksimal 100 baris per import. Total baris: ' . $rows->count());
        
        foreach ($rows as $row) {
            if (empty($row['nama']) || empty($row['identifier']) || empty($row['role']) || empty($row['email'])) {
                $this->failCount++;
                continue;
            }

            DB::beginTransaction();
            try {
                $roleName = ucfirst(strtolower($row['role']));
                $role = Role::where('name', $roleName)->first();
                
                if (!$role && $roleName === 'Admin') {
                    $this->failCount++;
                    DB::rollBack();
                    continue;
                }

                $majorCode = null;
                $className = null;

                if ($roleName === 'Siswa') {
                    $major = Major::where('major_code', $row['jurusan'])->orWhere('major_name', $row['jurusan'])->first();
                    $classroom = Classroom::where('name', $row['kelas'])->first();

                    if (!$major || !$classroom) {
                        $this->failCount++;
                        DB::rollBack();
                        continue;
                    }
                    $majorCode = $major->major_code;
                    $className = $classroom->name;
                }

                $user = User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['nama'],
                        'nip' => $roleName !== 'Siswa' ? $row['identifier'] : null,
                        // 'password' => Hash::make(Str::random(32)), // Password ketika menggunakan aktivasi email
                        'password' => Hash::make('12345678'),
                        'phone' => $row['phone'] ?? null,
                        'address' => $row['address'] ?? null,
                        'is_active' => true
                    ]
                );

                $user->roles()->sync([$role->id]);

                if ($roleName === 'Siswa') {
                    Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nis' => $row['identifier'],
                            'jurusan' => $majorCode,
                            'kelas' => $className,
                        ]
                    );
                }

                DB::commit();
                $this->successCount++;

                // if (!$user->is_active){
                //     ProcessUserActivation::dispatch($user);
                // }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->failCount++;
                \Illuminate\Support\Facades\Log::error('Gagal Import Baris: ' . $row['nama'] . ' - Error: ' . $e->getMessage());
            }
        }
    }
}