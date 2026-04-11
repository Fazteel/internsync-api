<?php

namespace App\Repositories\Hubin;

use App\Models\InternshipApplication;

class HubinInternshipRepository
{
    public function getPendingApplications()
    {
        return InternshipApplication::with([
            'industry',
            'pembimbing.teacher',
            'students',
        ])
            ->where('status', 'menunggu_acc_pengajuan')
            ->latest()
            ->get();
    }

    public function getPendingPlacements()
    {
        return InternshipApplication::with([
            'industry',
            'pembimbing.teacher',
            'students'
        ])
            ->where('status', 'menunggu_acc_pengiriman')
            ->latest()
            ->get();
    }

    public function updateStatus($id, $status)
    {
        $application = InternshipApplication::findOrFail($id);
        $application->update(['status' => $status]);

        return $application;
    }
}
