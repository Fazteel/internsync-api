<?php

namespace App\Services\Hubin;

use App\Repositories\Hubin\ReportRepository;
use App\Exports\Hubin\MasterReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    protected $repository;

    public function __construct(ReportRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getMasterReportData()
    {
        return [
            'summary' => $this->repository->getSummaryStats(),
            'distribution' => $this->repository->getDistributionData()
        ];
    }

    public function exportExcel()
    {
        $data = $this->repository->getDistributionData();
        if (ob_get_clean()) ob_end_clean();
        return Excel::download(new MasterReportExport($data), 'Master_Rekap_PKL.xlsx');
    }

    public function exportPdf()
    {
        $data = ['distribution' => $this->repository->getDistributionData()];
        $pdf = Pdf::loadView('pdf.master-report', $data);

        if (ob_get_clean()) ob_end_clean();

        return $pdf->download('Master_Rekap_PKL.pdf');
    }
}
