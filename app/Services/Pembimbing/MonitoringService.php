<?php

namespace App\Services\Pembimbing;

use App\Exports\Pembimbing\MonitoringExport;
use App\Models\Evaluation;
use App\Models\IndustryVisit;
use App\Repositories\Pembimbing\MonitoringRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringService
{
    protected $repo;

    public function __construct(MonitoringRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getVisitsList($pembimbingId)
    {
        $visits = $this->repo->getApprovedVisits($pembimbingId);

        return $visits->map(function ($visit) {
            $isFilled = Evaluation::where('visit_request_id', $visit->id)->exists();
            return [
                'id' => $visit->id,
                'industry' => $visit->industry->name ?? '-',
                'planned_date' => Carbon::parse($visit->planned_date)->translatedFormat('d M Y'),
                'purpose' => $visit->purpose,
                'is_filled' => $isFilled
            ];
        });
    }

    public function getMonitoringForm($visitId, $pembimbingId)
    {
        $internships = $this->repo->getStudentsForVisit($visitId, $pembimbingId);

        return $internships->map(function ($intern) {
            $existingEval = $intern->evaluations->first();

            return [
                'internship_id' => $intern->id,
                'name' => $intern->student->user->name ?? $intern->student->name ?? 'Tanpa Nama',
                'nis' => $intern->student->nis ?? '-',
                'kelas' => $intern->student->kelas ?? '-',
                'notes' => $existingEval ? $existingEval->description : '',
            ];
        });
    }

    public function submitMonitoring($visitId, $pembimbingId, $data)
    {
        if (empty($data['evaluations'])) {
            throw new Exception("Data monitoring kosong! Minimal isi catetan buat satu anak napa.");
        }

        return DB::transaction(function () use ($visitId, $pembimbingId, $data) {
            $this->repo->saveBulkMonitoring($visitId, $pembimbingId, $data['evaluations']);
            return true;
        });
    }

    public function exportMonitoringExcel($visitId, $pembimbingId)
    {
        $visit = IndustryVisit::with('industry', 'pembimbing')->findOrFail($visitId);
        $internships = $this->repo->getStudentsForVisit($visitId, $pembimbingId);

        $data = [
            'visit' => $visit,
            'students' => $internships->map(function ($intern) {
                $eval = $intern->evaluations->first();
                return [
                    'name' => $intern->student->user->name ?? $intern->student->name ?? '-',
                    'gender' => $intern->student->gender ?? 'L/P',
                    'kelas' => $intern->student->kelas ?? '-',
                    'notes' => $eval ? $eval->description : '',
                ];
            }),
        ];

        if (ob_get_contents()) ob_end_clean();
        return Excel::download(new MonitoringExport($data), 'Lembar_Monitoring_' . str_replace(' ', '_', $visit->industry->name) . '.xlsx');
    }
}
