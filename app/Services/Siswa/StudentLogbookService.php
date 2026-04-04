<?php

namespace App\Services\Siswa;

use App\Repositories\Siswa\StudentLogbookRepository;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StudentLogbookService
{
    protected $repoitory;
    public function __construct(StudentLogbookRepository $repoitory)
    {
        $this->repoitory = $repoitory;
    }

    public function listLogbooks($userId)
    {
        $student = $this->repoitory->getStudentByUserId($userId);
        if (!$student || !$student->internship) throw new \Exception('Data penempatan tidak ditemukan.', 404);

        return $this->repoitory->getLogbooks($student->internship->id)->map(fn($log) => [
            'id' => $log->id,
            'date' => $log->date,
            'activity' => $log->activity,
            'attachment' => $log->file_path ? basename($log->file_path) : '-',
            'attachment_url' => $log->file_path ? asset('storage/' . $log->file_path) : null,
            'status' => $log->status,
            'revisionNote' => $log->revision_note
        ]);
    }

    public function storeLogbook($userId, $data, $file)
    {
        $student = $this->repoitory->getStudentByUserId($userId);
        if ($this->repoitory->findLogByDate($student->internship->id, $data['date'])) throw new \Exception('Logbook untuk tanggal ini sudah ada!', 400);

        $fileName = 'Logbook_' . $student->nis . '_' . Carbon::parse($data['date'])->format('Ymd') . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('logbooks', $fileName, 'public');

        return $this->repoitory->create([
            'internship_id' => $student->internship->id,
            'date' => $data['date'],
            'activity' => $data['activity'],
            'file_path' => $filePath,
            'status' => 'submitted',
        ]);
    }

    public function updateLogbook($id, $activity, $file = null)
    {
        $logbook = $this->repoitory->findById($id);
        if ($file) {
            if ($logbook->file_path) Storage::disk('public')->delete($logbook->file_path);
            $fileName = 'Logbook_Rev_' . time() . '.' . $file->getClientOriginalExtension();
            $logbook->file_path = $file->storeAs('logbooks', $fileName, 'public');
        }
        $logbook->activity = $activity;
        $logbook->status = 'submitted';
        $logbook->save();
        return $logbook;
    }

    private function mapStatus($status)
    {
        return match ($status) {
            'approved' => 'Approved',
            'revised' => 'Revision',
            default => 'Pending'
        };
    }
}
