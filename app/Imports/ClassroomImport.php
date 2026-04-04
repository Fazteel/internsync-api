<?php

namespace App\Imports;

use App\Models\Major;
use App\Services\Admin\ClassroomService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClassroomImport implements ToCollection, WithHeadingRow
{
    protected $classroomService;
    public $successCount = 0;
    public $failCount = 0;

    public function __construct(ClassroomService $classroomService) {
        $this->classroomService = $classroomService;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['nama_kelas']) || empty($row['kode_jurusan'])) {
                $this->failCount++;
                continue;
            }

            $major = Major::where('major_code', $row['kode_jurusan'])->first();
            
            if (!$major) {
                Log::error('Kode jurusan kagak ketemu di DB: ' . $row['kode_jurusan']);
                $this->failCount++;
                continue;
            }

            try {
                $this->classroomService->create([
                    'name' => $row['nama_kelas'],
                    'major_id' => $major->id,
                    'is_active' => true
                ]);
                $this->successCount++;
            } catch (\Exception $e) {
                $this->failCount++;
                Log::error('Gagal import kelas: ' . $e->getMessage());
            }
        }
    }
}