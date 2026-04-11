<?php

namespace App\Repositories\Koordinator;

use App\Models\Internship;
use App\Models\InternshipApplication;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class KoordinatorInternshipRepository
{
    public function getApplicationsByType($type)
    {
        $query = InternshipApplication::with(['industry', 'pembimbing.teacher', 'students'])
            ->where('coordinator_id', Auth::id());

        if ($type === 'pengajuan') {
            $query->whereIn('status', ['draft', 'menunggu_acc_pengajuan', 'ditolak', 'batal']);
        } elseif ($type === 'pengiriman') {
            $query->whereIn('status', ['pengajuan', 'menunggu_acc_pengiriman']);
        } elseif ($type === 'riwayat') {
            $query->whereIn('status', ['pengiriman']);
        }

        return $query->latest()->get();
    }

    public function findApplication($id)
    {
        return InternshipApplication::with('students')->findOrFail($id);
    }

    public function findWithDetails($id)
    {
        return InternshipApplication::with([
            'industry',
            'pembimbing',
            'students.user',
            'students.major'
        ])->findOrFail($id);
    }

    public function withdrawInternship($studentId)
    {
        Student::where('id', $studentId)->update(['is_pkl' => false]);
        return Internship::where('student_id', $studentId)->delete();
    }

    public function extendIndividualInternship($internshipId, $newEndDate)
    {
        return Internship::where('id', $internshipId)->update([
            'end_date' => $newEndDate,
            'is_extended' => true
        ]);
    }

    public function extendBatchInternships($applicationId, $newEndDate)
    {
        Internship::where('application_id', $applicationId)->update([
            'end_date' => $newEndDate,
            'is_extended' => true
        ]);

        return InternshipApplication::where('id', $applicationId)->update([
            'final_end_date' => $newEndDate
        ]);
    }
}
