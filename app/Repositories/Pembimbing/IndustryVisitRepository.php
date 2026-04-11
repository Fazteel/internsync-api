<?php

namespace App\Repositories\Pembimbing;

use App\Models\IndustryVisit;

class IndustryVisitRepository
{
    public function getVisitByPembimbing($pembimbingId)
    {
        return IndustryVisit::with('industry')
            ->where('pembimbing_id', $pembimbingId)
            ->orderBy('planned_date', 'desc')
            ->get();
    }

    public function findVisitByIdAndPembimbing($id, $pembimbingId)
    {
        return IndustryVisit::where('id', $id)
            ->where('pembimbing_id', $pembimbingId)
            ->firstOrFail();
    }
}
