<?php

namespace App\Imports;

use App\Models\{User, Role, Student, Teacher, Major, Classroom, AcademicYear};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Hash, DB, Log};
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError, Importable};
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\{SkipsFailures, SkipsErrors};

class UsersImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;

    public $successCount = 0;
    public $failCount = 0;

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required_without:identifier', 'nullable', 'email', 'unique:m_users,email'],
            'role' => ['required', 'in:Siswa,Hubin,Koordinator,Pembimbing,Admin'],
            'identifier' => [
                'required_without:email',
                'nullable',
                function ($attribute, $value, $fail) {
                    if (empty($value)) return;
                    $existsInStudents = DB::table('m_students')->where('nis', $value)->exists();
                    $existsInTeachers = DB::table('m_teachers')->where('nip', $value)->exists();
                    if ($existsInStudents || $existsInTeachers) {
                        $fail("NIS/NIP '{$value}' sudah terdaftar.");
                    }
                }
            ],
            'phone' => ['nullable', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Indeks baris excel 2 (karena baris 1 adalah header)
            $rowNumber = $index + 2;

            $roleName = ucfirst(strtolower($row['role']));
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $this->onFailure(new Failure($rowNumber, 'role', ["Role '{$row['role']}' tidak ditemukan di sistem."], $row->toArray()));
                $this->failCount++;
                continue;
            }

            $major = null;
            $class = null;
            $academic = null;

            if ($roleName === 'Siswa') {
                $major = Major::where('major_code', $row['jurusan'])->first();
                $class = Classroom::where('name', $row['kelas'])->first();
                $academic = AcademicYear::where('name', $row['tahun_ajaran'])->first();

                if (!$major || !$class || !$academic) {
                    $errors = [];
                    if (!$major) $errors[] = "Jurusan '{$row['jurusan']}' tidak ditemukan.";
                    if (!$class) $errors[] = "Kelas '{$row['kelas']}' tidak ditemukan.";
                    if (!$academic) $errors[] = "Tahun ajaran '{$row['tahun_ajaran']}' tidak ditemukan.";

                    $this->onFailure(new Failure(
                        $rowNumber,
                        'master_data',
                        $errors,
                        $row->toArray()
                    ));
                    $this->failCount++;
                    continue;
                }
            }

            DB::beginTransaction();
            try {
                $user = User::create([
                    'email' => $row['email'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ]);
                $user->syncRoles([$role->id]);

                if ($roleName === 'Siswa') {
                    Student::create([
                        'user_id' => $user->id,
                        'nis' => $row['identifier'],
                        'name' => $row['nama'],
                        'jurusan' => $major->major_code,
                        'kelas' => $class->name,
                        'academic_year_id' => $academic->id,
                        'phone' => $row['phone'] ?? null,
                        'address' => $row['address'] ?? null,
                        'is_pkl' => false
                    ]);
                } else {
                    Teacher::create([
                        'user_id' => $user->id,
                        'nip' => $row['identifier'],
                        'name' => $row['nama'],
                        'phone' => $row['phone'] ?? null,
                        'address' => $row['address'] ?? null
                    ]);
                }

                DB::commit();
                $this->successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->failCount++;
                Log::error("Gagal mengimpor baris {$rowNumber} ({$row['nama']}): " . $e->getMessage());
                $this->onFailure(new Failure(
                    $rowNumber,
                    'database',
                    ["Gagal menyimpan data ke database: " . $e->getMessage()],
                    $row->toArray()
                ));
            }
        }
    }
}
