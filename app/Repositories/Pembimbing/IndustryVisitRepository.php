<?php

namespace App\Repositories\Pembimbing;

use App\Models\IndustryVisit;
use App\Models\Internship;

class IndustryVisitRepository
{
    public function getVisitByPembimbing($pembimbingId)
    {
        return IndustryVisit::with('industry')
            ->where('pembimbing_id', $pembimbingId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getInternshipsByPembimbing($pembimbingId)
    {
        return Internship::with('industry')
            ->where('pembimbing_id', $pembimbingId)
            ->whereHas('industry')
            ->get();
    }

    public function saveVisit(array $data)
    {
        return IndustryVisit::create($data);
    }

    public function findVisitByIdAndPembimbing($id, $pembimbingId)
    {
        return IndustryVisit::where('id', $id)
            ->where('pembimbing_id', $pembimbingId)
            ->firstOrFail();
    }
}