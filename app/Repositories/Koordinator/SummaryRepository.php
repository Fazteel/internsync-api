<?php

namespace App\Repositories\Koordinator;

use App\Models\Internship;
use Illuminate\Support\Facades\DB;

class SummaryRepository
{
    public function getSummaryData($filters = [])
    {
        $query = Internship::with(['student.user', 'industry', 'pembimbing', 'evaluations' => function ($q) {
            $q->where('type', 'final');
        }])
            ->join('m_students', 'tr_internships.student_id', '=', 'm_students.id')
            ->join('m_users', 'm_students.user_id', '=', 'm_users.id')
            ->leftJoin('m_majors', 'm_students.jurusan', '=', 'm_majors.major_code')
            ->select('tr_internships.*');

        if (!empty($filters['id'])) {
            $query->where('tr_internships.id', $filters['id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('m_users.name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('m_students.nis', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['major']) && $filters['major'] !== 'All') {
            $query->where('m_students.jurusan', $filters['major']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $query->where('tr_internships.status', strtolower($filters['status']));
        }

        return $query->latest('tr_internships.created_at')->get();
    }
}
