<?php

namespace App\Imports;

use App\Models\{User, Role, Student, Teacher, Major, Classroom, AcademicYear};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Hash, DB, Log};
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow};

class UsersImport implements ToCollection, WithHeadingRow
{
    public $successCount = 0;
    public $failCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['nama']) || empty($row['email']) || empty($row['role']) || empty($row['identifier'])) {
                $this->failCount++;
                continue;
            }

            DB::beginTransaction();
            try {
                $roleName = ucfirst(strtolower($row['role']));
                $role = Role::where('name', $roleName)->first();
                if (!$role) {
                    $this->failCount++;
                    DB::rollBack();
                    continue;
                }

                $user = User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'password' => Hash::make('12345678'),
                        'is_active' => true
                    ]
                );
                $user->syncRoles([$role->id]);

                if ($roleName === 'Siswa') {
                    $major = Major::where('major_code', $row['jurusan'])->first();
                    $class = Classroom::where('name', $row['kelas'])->first();
                    $academic = AcademicYear::where('name', $row['tahun_ajaran'])->first();

                    if (!$major || !$class || !$academic) {
                        Log::warning("Import Siswa {$row['nama']} gagal: Data master tidak ditemukan.");
                        $this->failCount++;
                        DB::rollBack();
                        continue;
                    }

                    Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nis' => $row['identifier'],
                            'name' => $row['nama'],
                            'jurusan' => $major->major_code,
                            'kelas' => $class->name,
                            'academic_year_id' => $academic->id,
                            'phone' => $row['phone'] ?? null,
                            'address' => $row['address'] ?? null,
                            'is_pkl' => false
                        ]
                    );
                } else {
                    Teacher::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nip' => $row['identifier'],
                            'name' => $row['nama'],
                            'phone' => $row['phone'] ?? null,
                            'address' => $row['address'] ?? null
                        ]
                    );
                }

                DB::commit();
                $this->successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->failCount++;
                Log::error('Import error baris ' . $row['nama'] . ': ' . $e->getMessage());
            }
        }
    }
}
