<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\MajorImport;
use App\Imports\ClassroomImport;
use App\Imports\AcademicYearImport;
use App\Services\Admin\AcademicYearService;
use App\Services\Admin\ClassroomService;
use App\Services\Admin\MajorService;

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