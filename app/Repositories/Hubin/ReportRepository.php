<?php

namespace App\Repositories\Hubin;

use App\Models\Internship;
use App\Models\Industry;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function getSummaryStats()
    {
        return [
            'total_students' => Internship::count(),
            'active_industries' => Industry::where('is_active', true)->count(),
            'completed_internships' => Internship::where('status', 'completed')->count(),
            'ongoing_internships' => Internship::where('status', 'active')->count(),
        ];
    }

    public function getDistributionData()
    {
        return Internship::join('m_industries', 'tr_internships.industry_id', '=', 'm_industries.id')
            ->join('m_students', 'tr_internships.student_id', '=', 'm_students.id')
            // JOIN PAKE KODE JURUSAN LU YANG AJAIB ITU
            ->leftJoin('m_majors', 'm_students.jurusan', '=', 'm_majors.major_code')
            ->leftJoin('tr_evaluations', function($join) {
                $join->on('tr_internships.id', '=', 'tr_evaluations.internship_id')
                     ->where('tr_evaluations.type', '=', 'final');
            })
            ->select(
                'm_industries.name as industry_name',
                'm_majors.major_name', // SEKARANG MUNCUL NAMA ASLINYA, BUKAN KODE DOANG!
                DB::raw('count(tr_internships.id) as total_students'),
                DB::raw('sum(case when tr_internships.status = "completed" then 1 else 0 end) as completed_count'),
                DB::raw('round(avg(tr_evaluations.score), 1) as avg_score')
            )
            ->whereNull('tr_internships.deleted_at')
            // JANGAN LUPA MASUKIN KE GROUP BY BIAR MYSQL KAGA NGAMBEK LAGI
            ->groupBy('m_industries.id', 'm_industries.name', 'm_majors.major_name') 
            ->get();
    }
}