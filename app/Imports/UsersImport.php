<?php

namespace App\Imports;

use App\Models\{User, Role, Student, Teacher, Major, Classroom, AcademicYear};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Hash, DB, Log, Validator};
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow, SkipsOnFailure, SkipsOnError, Importable};
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\{SkipsFailures, SkipsErrors};

class UsersImport implements ToCollection, WithHeadingRow, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;

    public $successCount = 0;
    public $failCount = 0;

    public function collection(Collection $rows)
    {
        $rolesCache = Role::all()->groupBy(fn($item) => strtolower($item->name));
        $majorsCache = Major::all()->keyBy('major_code');
        $classroomsCache = Classroom::all()->keyBy('name');
        $academicYearsCache = AcademicYear::all()->keyBy('name');

        $defaultPasswordHash = Hash::make('12345678');

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $roleNameInput = trim($row['role'] ?? '');
            $roleNameKey = strtolower($roleNameInput);

            $role = isset($rolesCache[$roleNameKey]) ? $rolesCache[$roleNameKey]->first() : null;

            if (!$role) {
                $this->onFailure(new Failure($rowNumber, 'role', ["Role '{$roleNameInput}' tidak ditemukan di sistem."], $row->toArray()));
                $this->failCount++;
                continue;
            }

            $identifier = trim($row['identifier'] ?? '');
            $email = trim($row['email'] ?? '');
            $existingUser = null;

            if (!empty($identifier)) {
                $existingUser = User::whereHas('student', fn($q) => $q->where('nis', $identifier))
                    ->orWhereHas('teacher', fn($q) => $q->where('nip', $identifier))
                    ->first();
            }

            if (!$existingUser && !empty($email)) {
                $existingUser = User::where('email', $email)->first();
            }

            $rules = [
                'nama' => ['required', 'string', 'max:100'],
                'role' => ['required', 'in:Siswa,Hubin,Koordinator,Pembimbing,Admin'],
                'phone' => ['nullable', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
            ];

            if ($existingUser) {
                $rules['email'] = ['required_without:identifier', 'nullable', 'email', 'unique:m_users,email,' . $existingUser->id];
                $rules['identifier'] = [
                    'required_without:email',
                    'nullable',
                    function ($attribute, $value, $fail) use ($existingUser) {
                        if (empty($value)) return;
                        $existsInStudents = DB::table('m_students')->where('nis', $value)->where('user_id', '!=', $existingUser->id)->exists();
                        $existsInTeachers = DB::table('m_teachers')->where('nip', $value)->where('user_id', '!=', $existingUser->id)->exists();
                        if ($existsInStudents || $existsInTeachers) {
                            $fail("NIS/NIP '{$value}' sudah terdaftar pada pengguna lain.");
                        }
                    }
                ];
            } else {
                $rules['email'] = ['required_without:identifier', 'nullable', 'email', 'unique:m_users,email'];
                $rules['identifier'] = [
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
                ];
            }

            $validator = Validator::make($row->toArray(), $rules);

            if ($validator->fails()) {
                $this->onFailure(new Failure($rowNumber, 'validation', $validator->errors()->all(), $row->toArray()));
                $this->failCount++;
                continue;
            }

            $major = null;
            $class = null;
            $academic = null;

            if ($role->name === 'Siswa') {
                $major = $majorsCache->get($row['jurusan']);
                $class = $classroomsCache->get($row['kelas']);
                $academic = $academicYearsCache->get($row['tahun_ajaran']);

                if (!$major || !$class || !$academic) {
                    $errors = [];
                    if (!$major) $errors[] = "Jurusan '" . ($row['jurusan'] ?? '') . "' tidak ditemukan.";
                    if (!$class) $errors[] = "Kelas '" . ($row['kelas'] ?? '') . "' tidak ditemukan.";
                    if (!$academic) $errors[] = "Tahun ajaran '" . ($row['tahun_ajaran'] ?? '') . "' tidak ditemukan.";

                    $this->onFailure(new Failure($rowNumber, 'master_data', $errors, $row->toArray()));
                    $this->failCount++;
                    continue;
                }
            }

            DB::beginTransaction();
            try {
                if ($existingUser) {
                    $existingUser->update(['email' => $row['email'] ?? $existingUser->email]);
                    $existingUser->syncRoles([$role->id]);

                    if ($role->name === 'Siswa') {
                        $student = Student::where('user_id', $existingUser->id)->first();
                        if ($student) {
                            $student->update([
                                'nis' => $row['identifier'] ?? $student->nis,
                                'name' => $row['nama'],
                                'jurusan' => $major->major_code,
                                'kelas' => $class->name,
                                'academic_year_id' => $academic->id,
                                'phone' => $row['phone'] ?? $student->phone,
                                'address' => $row['address'] ?? $student->address,
                            ]);
                        } else {
                            Student::create([
                                'user_id' => $existingUser->id,
                                'nis' => $row['identifier'],
                                'name' => $row['nama'],
                                'jurusan' => $major->major_code,
                                'kelas' => $class->name,
                                'academic_year_id' => $academic->id,
                                'phone' => $row['phone'] ?? null,
                                'address' => $row['address'] ?? null,
                                'is_pkl' => false
                            ]);
                            Teacher::where('user_id', $existingUser->id)->delete();
                        }
                    } else {
                        $teacher = Teacher::where('user_id', $existingUser->id)->first();
                        if ($teacher) {
                            $teacher->update([
                                'nip' => $row['identifier'] ?? $teacher->nip,
                                'name' => $row['nama'],
                                'phone' => $row['phone'] ?? $teacher->phone,
                                'address' => $row['address'] ?? $teacher->address,
                            ]);
                        } else {
                            Teacher::create([
                                'user_id' => $existingUser->id,
                                'nip' => $row['identifier'],
                                'name' => $row['nama'],
                                'phone' => $row['phone'] ?? null,
                                'address' => $row['address'] ?? null
                            ]);
                            Student::where('user_id', $existingUser->id)->delete();
                        }
                    }
                } else {
                    $user = User::create([
                        'email' => $row['email'],
                        'password' => $defaultPasswordHash,
                        'is_active' => true
                    ]);
                    $user->syncRoles([$role->id]);

                    if ($role->name === 'Siswa') {
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
                }

                DB::commit();
                $this->successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->failCount++;
                Log::error("Gagal mengimpor baris {$rowNumber} (" . ($row['nama'] ?? '') . "): " . $e->getMessage());
                $this->onFailure(new Failure($rowNumber, 'database', ["Gagal menyimpan data ke database: " . $e->getMessage()], $row->toArray()));
            }
        }
    }
}
