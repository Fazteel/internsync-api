<?php

namespace App\Repositories\Koordinator;

use App\Models\IndustryVisit;
use App\Models\Internship;

class IndustryVisitRepository
{
    public function getVisitsByCoordinator($coordinatorId)
    {
        return IndustryVisit::with(['industry', 'pembimbing.teacher'])
            ->where('coordinator_id', $coordinatorId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getInternshipsByCoordinator($coordinatorId)
    {
        return Internship::with(['industry', 'pembimbing.teacher'])
            ->whereHas('application', function ($q) use ($coordinatorId) {
                $q->where('coordinator_id', $coordinatorId);
            })
            ->get();
    }

    public function saveVisit(array $data)
    {
        return IndustryVisit::create($data);
    }
}
