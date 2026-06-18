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
            'completed_internships' => Internship::where('status', 'selesai')->count(),
            'ongoing_internships' => Internship::where('status', 'aktif')->count(),
        ];
    }

    public function getDistributionData()
    {
        $industries = Industry::whereHas('internships')
            ->with([
                'internships.student.major',
                'internships.student.classroom',
                'internships.evaluations' => function ($query) {
                    $query->where('type', '=', 'final');
                }
            ])
            ->get();

        return $industries->map(function ($industry) {
            $totalStudents = $industry->internships->count();
            $completedCount = $industry->internships->where('status', 'selesai')->count();
            
            // Calculate average final score
            $scores = $industry->internships->flatMap(function ($internship) {
                return $internship->evaluations;
            })->pluck('score')->filter();
            
            $avgScore = $scores->count() > 0 ? round($scores->average(), 1) : null;
            
            // Get list of unique major names
            $majors = $industry->internships->map(function ($internship) {
                return $internship->student->major->major_name ?? null;
            })->filter()->unique()->values()->implode(', ');
            
            // Prepare students list
            $students = $industry->internships->map(function ($internship) {
                $finalEval = $internship->evaluations->first();
                return [
                    'nis' => $internship->student->nis,
                    'name' => $internship->student->name,
                    'class_name' => $internship->student->classroom->name ?? $internship->student->kelas,
                    'major_name' => $internship->student->major->major_name ?? $internship->student->jurusan,
                    'status' => $internship->status,
                    'score' => $finalEval ? $finalEval->score : null
                ];
            })->sortBy('name')->values()->all();

            return (object) [
                'industry_name' => $industry->name,
                'major_name' => $majors ?: '-',
                'total_students' => $totalStudents,
                'completed_count' => $completedCount,
                'avg_score' => $avgScore,
                'students' => $students
            ];
        });
    }
}
