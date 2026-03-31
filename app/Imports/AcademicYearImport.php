<?php

namespace App\Imports;

use App\Services\AcademicYearService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AcademicYearImport implements ToCollection, WithHeadingRow
{
    protected $academicYearService;

    public $successCount = 0;
    public $failCount = 0;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            if(empty($row['tahun_ajaran']) || empty($row['semester'])){
                $this->failCount++;
                continue;
            }

            $semester = ucfirst(strtolower($row['semester']));

            if(!in_array($semester,['Ganjil','Genap'])){
                $this->failCount++;
                continue;
            }

            try{

                $this->academicYearService->create([
                    'name' => $row['tahun_ajaran'],
                    'semester' => $semester,
                    'is_active' => true
                ]);

                $this->successCount++;

            }catch(\Exception $e){

                $this->failCount++;

                Log::error(
                    'Import Academic Year gagal : '.$e->getMessage()
                );

            }
        }
    }
}