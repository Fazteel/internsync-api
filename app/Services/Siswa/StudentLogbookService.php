<?php

namespace App\Services\Siswa;

use App\Models\Notification;
use App\Repositories\Siswa\StudentLogbookRepository;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StudentLogbookService
{
    protected $repository;
    public function __construct(StudentLogbookRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listLogbooks($userId)
    {
        $student = $this->repository->getStudentByUserId($userId);
        if (!$student || !$student->internship) throw new \Exception('Data penempatan tidak ditemukan.', 404);

        return $this->repository->getLogbooks($student->internship->id)->map(fn($log) => [
            'id' => $log->id,
            'date' => $log->date,
            'activity' => $log->activity,
            'attachment' => $log->file_path ? basename($log->file_path) : '-',
            'attachment_url' => $log->file_path ? asset('storage/' . $log->file_path) : null,
        ]);
    }

    public function storeLogbook($userId, $data, $file)
    {
        $student = $this->repository->getStudentByUserId($userId);

        if (!$student->internship || $student->internship->status !== 'aktif') {
            throw new \Exception('Akses Logbook dikunci. Status magang Anda: ' . ucfirst($student->internship->status), 403);
        }

        if ($this->repository->findLogByDate($student->internship->id, $data['date'])) {
            throw new \Exception('Logbook untuk tanggal ini sudah ada!', 400);
        }

        $fileName = 'Logbook_' . $student->nis . '_' . Carbon::parse($data['date'])->format('Ymd') . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('logbooks', $fileName, 'public');

        return $this->repository->create([
            'internship_id' => $student->internship->id,
            'date' => $data['date'],
            'activity' => $data['activity'],
            'file_path' => $filePath,
        ]);
    }

    public function updateLogbook($id, $activity, $file = null)
    {
        $logbook = $this->repository->findById($id);

        if ($logbook->internship->status !== 'aktif') {
            throw new \Exception('Anda tidak dapat merevisi logbook karena status magang dibekukan.', 403);
        }

        if ($file) {
            if ($logbook->file_path) Storage::disk('public')->delete($logbook->file_path);
            $fileName = 'Logbook_Rev_' . time() . '.' . $file->getClientOriginalExtension();
            $logbook->file_path = $file->storeAs('logbooks', $fileName, 'public');
        }
        $logbook->activity = $activity;
        $logbook->status = 'submitted';
        $logbook->save();
        $logbook->load('internship.student.user');

        $teacherId = $logbook->internship->pembimbing_id;
        $studentName = $logbook->internship->student->user->name ?? 'Siswa';

        if ($teacherId) {
            Notification::send(
                $teacherId,
                'Revisi Logbook Harian',
                "Siswa {$studentName} telah memperbarui logbook hariannya.",
                'info'
            );
        }
        return $logbook;
    }
}
