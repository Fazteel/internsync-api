<?php

namespace App\Exports\Koordinator;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SummaryExport implements FromCollection, WithHeadings, WithMapping
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
        return ['Nama Siswa', 'NIS', 'Jurusan', 'Industri', 'Pembimbing', 'Status', 'Nilai Akhir'];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['nis'],
            $row['major'],
            $row['industry'],
            $row['supervisor'],
            $row['status'],
            $row['finalScore'] ?? '-'
        ];
    }
}
