<?php

namespace App\Services\Koordinator;

use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use App\Repositories\Koordinator\SupervisorRepository;

class SupervisorService
{
    protected $repository;

    public function __construct(SupervisorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getPlottingList()
    {
        return $this->repository->getStudentsWithRelations()->map(function ($student) {
            return [
                'id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->user->name,
                'major' => $student->jurusan ?? '-',
                'industry' => $student->internship->industry->name ?? 'Belum Ada Penempatan',
                'supervisor_id' => $student->internship->pembimbing_id ?? null,
                'supervisor_name' => $student->internship->pembimbing->name ?? null,
                'status' => ($student->internship && $student->internship->pembimbing_id) ? 'Sudah Diplot' : 'Belum Diplot'
            ];
        });
    }

    public function getAvailableTeachers()
    {
        return $this->repository->getTeachers();
    }

    public function assignTeacher($data)
    {
        $result = $this->repository->assignSupervisor($data['student_id'], $data['pembimbing_id']);

        $studentProfile = Student::find($data['student_id']);

        if ($studentProfile) {
            $studentUser = User::find($studentProfile->user_id);
            $teacherUser = User::find($data['pembimbing_id']);

            if ($studentUser && $teacherUser) {
                Notification::send(
                    $studentUser->id,
                    'Pembimbing PKL Ditetapkan',
                    `Bapak/Ibu {$teacherUser->name} telah ditugaskan sebagai guru pembimbing praktik kerja lapangan anda.`,
                    'info'
                );

                Notification::send(
                    $teacherUser->id,
                    'Siswa Bimbingan Baru',
                    `Siswa {$studentUser->name} telah ditambahkan ke dalam siswa bimbingan praktik kerja lapangan anda.`,
                    'info'
                );
            }
        }

        return $result;
    }
}
