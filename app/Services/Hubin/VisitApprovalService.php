<?php

namespace App\Services\Hubin;

use App\Models\IndustryVisit;
use App\Repositories\Hubin\VisitApprovalRepository;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VisitApprovalService
{
    protected $repository;

    public function __construct(VisitApprovalRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllVisits()
    {
        $visits = $this->repository->getPembimbingVisit();

        return $visits->map(function ($visit) {
            return [
                'id' => $visit->id,
                'teacherName' => $visit->pembimbing->name ?? 'Tanpa Nama',
                'industry' => $visit->industry->name ?? '-',
                'plannedDate' => Carbon::parse($visit->planned_date)->translatedFormat('d M Y'),
                'purpose' => $visit->purpose,
                'status' => ucfirst($visit->status),
                'feedback' => $visit->feedback,
                'file_path' => $visit->file_path
            ];
        });
    }

    public function generateSPPD($id)
    {
        $visit = IndustryVisit::with(['pembimbing', 'industry'])->findOrFail($id);

        if ($visit->status !== 'approved') {
            throw new \Exception("Hanya pengajuan yang telah Disetujui yang dapat dicetak!");
        }

        if ($visit->file_path && Storage::disk('public')->exists($visit->file_path)) {
            return $visit;
        }

        $data = [
            'visit' => $visit,
            'tanggalSurat' => Carbon::now()->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView('pdf.sppd', $data);

        $fileName = 'SPPD_' . Str::slug($visit->pembimbing->name) . '_' . time() . '.pdf';
        $filePath = 'sppd/' . $fileName;

        Storage::disk('public')->put($filePath, $pdf->output());

        $visit->update(['file_path' => $filePath]);

        return $visit;
    }

    public function processApproval($id, $status, $feedback = null)
    {
        $dbStatus = ($status === 'Approved') ? 'approved' : 'rejected';

        $updateData = [
            'status' => $dbStatus,
            'feedback' => ($status === 'Rejected') ? $feedback : null
        ];

        return $this->repository->update($id, $updateData);
    }
}