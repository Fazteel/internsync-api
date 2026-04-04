<?php

namespace App\Repositories\Pembimbing;

use App\Models\Internship;
use App\Models\Evaluation;

class EvaluationRepository
{
    public function getByPembimbing($pembimbingId)
    {
        return Internship::with(['student.user', 'industry', 'evaluations' => function ($q) {
            $q->where('type', 'final');
        }])
            ->where('pembimbing_id', $pembimbingId)
            ->get();
    }

    public function findById($id)
    {
        return Internship::findOrFail($id);
    }

    public function updateInternshipStatus($internship, $status)
    {
        $internship->status = $status;
        return $internship->save();
    }

    public function saveFinalEvaluation($internshipId, $pembimbingId, $data)
    {
        return Evaluation::updateOrCreate(
            [
                'internship_id' => $internshipId,
                'type' => 'final'
            ],
            [
                'evaluator_id' => $pembimbingId,
                'evaluation_date' => now(),
                'score' => $data['score'],
                'description' => $data['notes'],
            ]
        );
    }
}
