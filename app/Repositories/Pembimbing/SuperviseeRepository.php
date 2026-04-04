<?php

namespace App\Repositories\Pembimbing;

use App\Models\Internship;

class SuperviseeRepository
{
    public function getByPembimbing($pembimbingId)
    {
        return Internship::with(['student.user', 'student.major', 'industry'])
            ->where('pembimbing_id', $pembimbingId)
            ->get();
    }

    public function findByIdAndPembimbing($id, $pembimbingId)
    {
        return Internship::with(['student.user', 'student.major', 'industry'])
            ->where('pembimbing_id', $pembimbingId)
            ->findOrFail($id);
    }
}