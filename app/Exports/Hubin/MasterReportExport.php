<?php

namespace App\Exports\Hubin;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Nama Industri',
            'Kompetensi Keahlian (Jurusan)',
            'Total Siswa',
            'Selesai Magang',
            'Rata-rata Nilai'
        ];
    }

    public function map($row): array
    {
        return [
            $row->industry_name,
            $row->major_name,
            $row->total_students,
            $row->completed_count,
            $row->avg_score ?? '-'
        ];
    }
}