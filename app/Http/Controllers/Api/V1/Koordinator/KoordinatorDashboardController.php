<?php

namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Industry;
use App\Models\Internship;
use Illuminate\Http\Request;

class KoordinatorDashboardController extends Controller
{
    public function stats()
    {
        $totalSiswaAll = Student::count();
        
        $belumDitempatkan = Student::whereDoesntHave('internship', function($q) {
            $q->whereNotNull('industry_id');
        })->count();
        
        $totalAktif = $totalSiswaAll - $belumDitempatkan;
        
        $industriAktif = Industry::where('is_active', true)->count();

        $recentStudents = Student::with(['user', 'internship.industry'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($student) {
                $status = 'Belum Ditempatkan';
                if ($student->internship && $student->internship->status === 'completed') {
                    $status = 'Selesai';
                } elseif ($student->internship && $student->internship->industry_id) {
                    $status = 'Aktif';
                }
                
                return [
                    'id' => $student->id,
                    'nis' => $student->nis,
                    'name' => $student->user->name ?? 'Tanpa Nama',
                    'major' => $student->jurusan ?? '-',
                    'industry' => $student->internship->industry->name ?? 'Belum Ada',
                    'status' => $status
                ];
            });

        $chartData = array_fill(0, 12, 0);
        $internships = Internship::whereNotNull('start_date')
            ->whereYear('start_date', date('Y'))
            ->get();
            
        foreach ($internships as $internship) {
            $monthIndex = (int)date('n', strtotime($internship->start_date)) - 1;
            $chartData[$monthIndex]++;
        }

        return response()->json([
            'metrics' => [
                'total_aktif' => $totalAktif,
                'belum_ditempatkan' => $belumDitempatkan,
                'industri_aktif' => $industriAktif
            ],
            'table' => $recentStudents,
            'chart' => [
                'name' => 'Diberangkatkan',
                'data' => $chartData
            ]
        ]);
    }
}