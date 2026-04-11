<?php

namespace App\Repositories\Pembimbing;

use App\Models\Logbook;

class LogbookMonitoringRepository
{
    public function getByPembimbing($pembimbingId, $studentId = null)
    {
        $query = Logbook::with(['internship.student.user', 'internship.industry'])
            ->whereHas('internship', function ($q) use ($pembimbingId) {
                $q->where('pembimbing_id', $pembimbingId);
            });

        if ($studentId) {
            $query->whereHas('internship', fn($q) => $q->where('student_id', $studentId));
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function findById($id)
    {
        return Logbook::findOrFail($id);
    }

    public function updateLogbook($id, array $data)
    {
        $logbook = $this->findById($id);
        $logbook->update($data);

        return $logbook;
    }

    public function bulkUpdateStatusByPembimbing(array $ids, $pembimbingId, $status)
    {
        return Logbook::whereIn('id', $ids)
            ->whereHas('internship', function ($q) use ($pembimbingId) {
                $q->where('pembimbing_id', $pembimbingId);
            })
            ->update(['status' => $status]);
    }
}
