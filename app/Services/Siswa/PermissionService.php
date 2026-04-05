<?php

namespace App\Services\Siswa;

use App\Models\Internship;
use App\Models\Notification;
use App\Models\Student;
use App\Repositories\Siswa\PermissionRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PermissionService
{
    protected $repo;
    public function __construct(PermissionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getPermissionsByStudent($userId)
    {
        $student = Student::where('user_id', $userId)->first();
        if (!$student || !$student->internship) return [];

        return $this->repo->getByInternship($student->internship->id)->map(fn($p) => $this->formatPermission($p));
    }

    public function getPermissionsBySupervisor($pembimbingId)
    {
        return $this->repo->getByPembimbing($pembimbingId)->map(fn($p) => $this->formatPermission($p));
    }

    private function formatPermission($p)
    {
        return [
            'id' => $p->id,
            'studentName' => $p->internship->student->user->name ?? '-',
            'start_date' => Carbon::parse($p->start_date)->translatedFormat('d F Y'),
            'end_date' => Carbon::parse($p->end_date)->translatedFormat('d F Y'),
            'raw_start_date' => $p->start_date,
            'raw_end_date' => $p->end_date,
            'type' => $p->type === 'sick' ? 'Sakit' : 'Izin',
            'reason' => $p->reason,
            'attachment' => $p->attachment ? asset('storage/' . $p->attachment) : null,
            'status' => ucfirst($p->status),
            'created_at' => $p->created_at->diffForHumans()
        ];
    }

    public function submitPermission($internshipId, $data, $file)
    {
        $fileName = 'Permission_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('permissions', $fileName, 'public');

        $permission = $this->repo->create([
            'internship_id' => $internshipId,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'type' => $data['type'],
            'reason' => $data['reason'],
            'attachment' => $path,
            'status' => 'pending'
        ]);

        $intern = Internship::with('student.user')->find($internshipId);
        if ($intern && $intern->pembimbing_id) {
            $studentName = $intern->student->user->name ?? 'Siswa';
            $typeLabel = $data['type'] === 'sick' ? 'Sakit' : 'Izin';

            Notification::send(
                $intern->pembimbing_id,
                'Pengajuan Izin Baru',
                "Siswa {$studentName} mengajukan {$typeLabel} untuk tanggal " .
                    Carbon::parse($data['start_date'])->translatedFormat('d M') . " s/d " .
                    Carbon::parse($data['end_date'])->translatedFormat('d M Y') . ".",
                'warning'
            );
        }

        return $permission;
    }

    public function handleVerification($id, $status)
    {
        $permission = $this->repo->updateStatus($id, $status);
        $permission->load('internship.student');

        if ($permission->internship && $permission->internship->student) {
            $studentUserId = $permission->internship->student->user_id;
            $statusText = $status === 'approved' ? 'DISETUJUI' : 'DITOLAK';
            $notifType = $status === 'approved' ? 'success' : 'error';
            $typeLabel = $permission->type === 'sick' ? 'Sakit' : 'Izin';

            Notification::send(
                $studentUserId,
                'Status Pengajuan Izin',
                "Pengajuan {$typeLabel} Anda untuk tanggal " .
                    Carbon::parse($permission->start_date)->translatedFormat('d M Y') .
                    " telah {$statusText} oleh pembimbing.",
                $notifType
            );
        }

        return $permission;
    }
}
