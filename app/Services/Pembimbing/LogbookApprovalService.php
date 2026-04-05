<?php

namespace App\Services\Pembimbing;

use App\Models\Logbook;
use App\Models\Notification;
use App\Repositories\Pembimbing\LogbookRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogbookApprovalService
{
    protected $repository;

    public function __construct(LogbookRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getLogbooks($pembimbingId)
    {
        $logbooks = $this->repository->getByPembimbing($pembimbingId);

        return $logbooks->map(function ($log) {

            return [
                'id' => $log->id,
                'studentName' => $log->internship->student->user->name ?? '-',
                'nis' => $log->internship->student->nis ?? '-',
                'industry' => $log->internship->industry->name ?? '-',
                'date' => Carbon::parse($log->date)->translatedFormat('d M Y'),
                'activity' => $log->activity,
                'attachment' => $log->file_path ? basename($log->file_path) : '-',
                'attachment_url' => $log->file_path ? asset('storage/' . $log->file_path) : null,
                'status' => $log->status,
                'revisionNote' => $log->revision_note
            ];
        });
    }

    public function processVerification($id, $pembimbingId, array $data)
    {
        $updateData = [
            'status' => $data['status'],
            'revision_note' => $data['status'] === 'Revision' ? $data['revisionNote'] : null,
            'approved_by' => $pembimbingId,
            'approved_at' => now(),
        ];

        $result = $this->repository->updateLogbook($id, $updateData);

        $logbook = Logbook::with('internship.student')->find($id);

        if ($logbook && $logbook->internship && $logbook->internship->student) {
            $studentId = $logbook->internship->student->user_id;

            $statusText = $data['status'] === 'approved' ? 'Disetujui' : 'Direvisi';
            $tipe = $data['status'] === 'approved' ? 'success' : 'warning';

            Notification::send(
                $studentId,
                'Status Logbook Harian',
                "Logbook harian Anda telah {$statusText} oleh pembimbing.",
                $tipe
            );
        }

        return $result;
    }

    public function bulkProcess($pembimbingId, array $data)
    {
        return DB::transaction(function () use ($pembimbingId, $data) {
            $result = $this->repository->bulkUpdateStatusByPembimbing(
                $data['ids'],
                $pembimbingId,
                $data['status']
            );

            $logbooks = Logbook::with('internship.student')->whereIn('id', $data['ids'])->get();

            $statusText = $data['status'] === 'approved' ? 'Disetujui' : 'Direvisi';
            $tipe = $data['status'] === 'approved' ? 'success' : 'warning';

            foreach ($logbooks as $logbook) {
                if ($logbook->internship && $logbook->internship->student) {
                    Notification::send(
                        $logbook->internship->student->user_id,
                        'Status Logbook Harian',
                        "Logbook harian Anda telah {$statusText} oleh pembimbing.",
                        $tipe
                    );
                }
            }

            return $result;
        });
    }
}
