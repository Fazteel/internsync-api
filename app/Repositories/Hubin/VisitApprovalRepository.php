<?php

namespace App\Repositories\Hubin;

use App\Models\IndustryVisit;

class VisitApprovalRepository
{
    public function getPembimbingVisit()
    {
        return IndustryVisit::with(['pembimbing', 'industry'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById($id)
    {
        return IndustryVisit::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $visit = $this->findById($id);
        $visit->update($data);
        return $visit;
    }
}
