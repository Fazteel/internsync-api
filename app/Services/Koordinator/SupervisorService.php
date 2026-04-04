<?php

namespace App\Services\Koordinator;

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
        return $this->repository->assignSupervisor($data['student_id'], $data['pembimbing_id']);
    }
}
