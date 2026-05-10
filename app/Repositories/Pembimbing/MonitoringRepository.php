<?php

namespace App\Repositories\Pembimbing;

use App\Models\IndustryVisit;
use App\Models\Internship;
use App\Models\Evaluation;

class MonitoringRepository
{
    public function getApprovedVisits($pembimbingId)
    {
        return IndustryVisit::with('industry')
            ->where('pembimbing_id', $pembimbingId)
            ->where('status', 'approved')
            ->orderBy('planned_date', 'desc')
            ->get();
    }

    public function getStudentsForVisit($visitId, $pembimbingId)
    {
        $visit = IndustryVisit::findOrFail($visitId);

        return Internship::with(['student.user', 'evaluations' => function ($q) use ($visitId) {
            $q->where('visit_request_id', $visitId)->where('type', 'monthly');
        }])
            ->where('industry_id', $visit->industry_id)
            ->where('pembimbing_id', $pembimbingId)
            ->whereIn('status', ['aktif', 'selesai'])
            ->get();
    }

    public function saveBulkMonitoring($visitId, $pembimbingId, $evaluationsData)
    {
        foreach ($evaluationsData as $data) {
            Evaluation::updateOrCreate(
                [
                    'internship_id' => $data['internship_id'],
                    'visit_request_id' => $visitId,
                    'type' => 'monthly'
                ],
                [
                    'evaluator_id' => $pembimbingId,
                    'evaluation_date' => now(),
                    'score' => null,
                    'description' => $data['notes'] ?? '',
                ]
            );
        }
        return true;
    }
}
