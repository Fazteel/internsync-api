<?php

namespace App\Imports;

use App\Services\MajorService;
use App\Services\ClassroomService;
use App\Services\AcademicYearService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\MajorImport;
use App\Imports\ClassroomImport;
use App\Imports\AcademicYearImport;

class MasterDataImport implements WithMultipleSheets
{
    protected $majorService;
    protected $classroomService;
    protected $academicYearService;

    public function __construct(MajorService $majorService, ClassroomService $classroomService, AcademicYearService $academicYearService) {
        $this->majorService = $majorService;
        $this->classroomService = $classroomService;
        $this->academicYearService = $academicYearService;
    }

    public function sheets(): array
    {
       return [
           0 => new MajorImport($this->majorService),
           1 => new ClassroomImport($this->classroomService),
           2 => new AcademicYearImport($this->academicYearService),
       ];
    }
}