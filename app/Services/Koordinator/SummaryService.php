<?php

namespace App\Services\Koordinator;

use App\Repositories\Koordinator\SummaryRepository;
use App\Exports\Koordinator\SummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SummaryService
{
    protected $repository;

    public function __construct(SummaryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getFormattedSummary($filters)
    {
        $data = $this->repository->getSummaryData($filters);

        return $data->map(function ($intern) {
            $eval = $intern->evaluations->first();

            $statusMap = [
                'active' => 'Aktif',
                'completed' => 'Selesai',
                'cancelled' => 'Bermasalah',
                'pending' => 'Menunggu'
            ];

            return [
                'id' => $intern->id,
                'nis' => $intern->student->nis ?? '-',
                'name' => $intern->student->name ?? '-',
                'major' => $intern->student->jurusan ?? '-',
                'industry' => $intern->industry->name ?? 'Belum Diplot',
                'supervisor' => $intern->pembimbing->teacher->name ?? 'Belum Ditunjuk',
                'status' => $statusMap[$intern->status] ?? $intern->status,
                'finalScore' => $eval ? $eval->score : null,
            ];
        });
    }

    public function exportExcel($filters)
    {
        $data = $this->getFormattedSummary($filters);
        if (ob_get_contents()) ob_end_clean();
        return Excel::download(new SummaryExport($data), 'Rekapitulasi_PKL_Siswa.xlsx');
    }

    public function exportStudentPdf($id)
    {
        $intern = $this->repository->getSummaryData(['id' => $id])->first();
        if (!$intern) throw new \Exception("Data tidak ditemukan.");

        $eval = $intern->evaluations->where('type', 'final')->first();

        $data = [
            'name'      => optional(optional($intern->student)->user)->name ?? 'Siswa Tanpa Nama',
            'nis'       => optional($intern->student)->nis ?? '-',
            'industry'  => optional($intern->industry)->name ?? 'Belum Diplot',
            'start_date' => $intern->start_date ?? '-',
            'end_date'  => $intern->end_date ?? '-',
            'score'     => $eval->score ?? 0,
            'notes'     => $eval->description ?? 'Kaga ada catatan.',
            'evaluator' => optional($intern->pembimbing)->name ?? 'Pembimbing Gaib',
            'date'      => now()->translatedFormat('d F Y')
        ];

        $pdf = Pdf::loadView('pdf.rapor-siswa', $data);
        if (ob_get_contents()) ob_end_clean();
        return $pdf->download("Rapor_PKL_{$data['name']}.pdf");
    }
}
