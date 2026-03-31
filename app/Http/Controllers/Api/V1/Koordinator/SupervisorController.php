<?php
namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Internship;
use App\Models\Student;

class SupervisorController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'internship.industry', 'internship.pembimbing'])->get()->map(function ($student) {
            return [
                'id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->user->name,
                'major' => $student->jurusan ?? '-',
                'industry' => $student->internship->industry->name ?? 'Belum Ada Penempatan',
                'supervisor_id' => $student->internship->pembimbing_id ?? null,
                'supervisor_name' => $student->internship->pembimbing->name ?? null, 
                'status' => ($student->internship && $student->internship->pembimbing_id) ? 'Sudah Diplot' : 'Belum Diplot'
            ];
        });
        return response()->json($students);
    }

    public function teachers()
    {
        $teachers = User::whereHas('roles', function($q) {
            $q->where('name', 'Pembimbing');
        })->select('id', 'name')->get();
        
        return response()->json($teachers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:m_students,id',
            'pembimbing_id' => 'required|exists:m_users,id',
        ]);

        $internship = Internship::updateOrCreate(
            ['student_id' => $request->student_id],
            ['pembimbing_id' => $request->pembimbing_id]
        );

        return response()->json(['message' => 'Guru pembimbing berhasil ditugaskan!']);
    }
}