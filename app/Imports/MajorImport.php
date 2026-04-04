<?php

namespace App\Imports;

use App\Services\Admin\MajorService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MajorImport implements ToCollection, WithHeadingRow
{
    protected $majorService;

    public function __construct(MajorService $majorService) {
        $this->majorService = $majorService;
    }

    public $successCount = 0;
    public $failCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['kode_jurusan']) || empty($row['nama_jurusan'])) {
                continue;
            }

            try {
                $this->majorService->create([
                    'major_code' => $row['kode_jurusan'],
                    'major_name' => $row['nama_jurusan'],
                ]);
                $this->successCount++;
            } catch (\Exception $e) {
                Log::error('Gagal import jurusan: ' . $e->getMessage());
            }
        }
    }
}