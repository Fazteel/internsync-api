<?php
namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Industry;
use App\Models\Internship;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PlacementController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with(['user', 'internship.industry', 'internship.pembimbing'])->get()->map(function ($student) {
            if ($student->internship && $student->internship->industry_id) {
                $status = 'Sudah Ditempatkan';
            } elseif ($student->internship && $student->internship->pembimbing_id) {
                $status = 'Sudah Diplot';
            } else {
                $status = 'Belum Ditempatkan';
            }
            return [
                'id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->user->name ?? 'Tanpa Nama',
                'major' => $student->jurusan ?? '-',
                'industry_id' => $student->internship->industry_id ?? null,
                'industry' => $student->internship->industry->name ?? null,
                'duration' => $student->internship->duration_month ?? null,
                'startDate' => $student->internship->start_date ?? null,
                'supervisor_id' => $student->internship->pembimbing_id ?? null,
                'supervisor_name' => $student->internship->pembimbing->name ?? null,
                'status' => $status
            ];
        });

        return response()->json($students);
    }

    public function industries()
    {
        $industries = Industry::where('is_active', true)->select('id', 'name', 'kuota_siswa')->get();
        return response()->json($industries);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:m_students,id',
            'industry_id' => 'required|exists:m_industries,id',
            'duration' => 'required|in:3,6',
            'start_date' => 'required|date'
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = $startDate->copy()->addMonths((int)$validated['duration']);

        $internship = Internship::updateOrCreate(
            ['student_id' => $validated['student_id']],
            [
                'industry_id' => $validated['industry_id'],
                'duration_month' => $validated['duration'],
                'start_date' => $validated['start_date'],
                'end_date' => $endDate->format('Y-m-d'),
                'coordinator_id' => Auth::id(),
                'status' => 'Pending'
            ]
        );

        return response()->json([
            'message' => 'Data penempatan berhasil disimpan!', 
            'data' => $internship
        ]);
    }
}