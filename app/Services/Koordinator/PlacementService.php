<?php

namespace App\Services\Koordinator;

use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use App\Repositories\Koordinator\PlacementRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PlacementService
{
    protected $repository;

    public function __construct(PlacementRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getFormattedStudents()
    {
        $students = $this->repository->getAllStudentsWithPlacement();

        return $students->map(function ($student) {
            $status = 'Belum Ditempatkan';
            if ($student->internship && $student->internship->industry_id) {
                $status = 'Sudah Ditempatkan';
            } elseif ($student->internship && $student->internship->pembimbing_id) {
                $status = 'Sudah Diplot';
            }

            return [
                'id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->user->name ?? 'Tanpa Nama',
                'major' => $student->jurusan ?? '-',
                'industry_id' => $student->internship->industry_id ?? null,
                'industry' => $student->internship->industry->name ?? null,
                'duration' => $student->internship->duration_month ?? null,
                'startDate' => $student->internship->start_date ?? null,
                'endDate' => $student->internship->end_date ?? null,
                'is_extended' => (bool) ($student->internship->is_extended ?? false),
                'supervisor_id' => $student->internship->pembimbing_id ?? null,
                'supervisor_name' => $student->internship->pembimbing->name ?? null,
                'status' => $status
            ];
        });
    }

    public function storePlacement(array $data)
    {
        $industry = $this->repository->findIndustryById($data['industry_id']);
        $existingInternship = $this->repository->findInternshipByStudentId($data['student_id']);

        if (!$existingInternship || $existingInternship->industry_id != $data['industry_id']) {
            if ($industry->internships_count >= $industry->kuota_siswa) {
                throw ValidationException::withMessages(['industry_id' => 'Kuota industri sudah penuh!']);
            }
        }

        $startDate = Carbon::parse($data['start_date']);

        $totalMonths = (int) $data['duration'];
        if (isset($data['is_extended']) && $data['is_extended']) {
            $totalMonths += (int) ($data['extension_month'] ?? 0);
        }

        $endDate = $startDate->copy()->addMonths($totalMonths);

        $internship = $this->repository->updateOrCreateInternship(
            ['student_id' => $data['student_id']],
            [
                'industry_id' => $data['industry_id'],
                'duration_month' => $data['duration'],
                'is_extended' => $data['is_extended'] ?? false,
                'start_date' => $data['start_date'],
                'end_date' => $endDate->format('Y-m-d'),
                'coordinator_id' => Auth::id(),
                'status' => 'pending'
            ]
        );

        $student = Student::with('user')->find($data['student_id']);

        if ($student && $student->user) {
            Notification::send(
                $student->user_id,
                'Penempatan PKL',
                "Anda telah ditempatkan di industri {$industry->name} untuk pelaksanaan PKL. Silakan menunggu verifikasi keberangkatan dari pihak Hubin.",
                'info'
            );

            $hubinUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'hubin');
            })->get();

            foreach ($hubinUsers as $hubin) {
                Notification::send(
                    $hubin->id,
                    'Permintaan Keberangkatan',
                    "Terdapat pengajuan penempatan PKL baru untuk siswa {$student->user->name} di {$industry->name} yang memerlukan verifikasi keberangkatan.",
                    'warning'
                );
            }
        }

        return $internship;
    }
}
