<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MasterDataImport;
use App\Models\AuditLog;
use App\Services\Admin\AcademicYearService;
use App\Services\Admin\ClassroomService;
use App\Services\Admin\MajorService;

class MasterImportController extends Controller
{
    protected $majorService;
    protected $classroomService;
    protected $academicYearService;

    public function __construct(
        MajorService $majorService,
        ClassroomService $classroomService,
        AcademicYearService $academicYearService
    ) {
        $this->majorService = $majorService;
        $this->classroomService = $classroomService;
        $this->academicYearService = $academicYearService;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new MasterDataImport(
                $this->majorService,
                $this->classroomService,
                $this->academicYearService
            ), $request->file('file'));

            AuditLog::record(
                'master_data',
                'import',
                'Mengimpor data master dari file Excel'
            );

            return response()->json([
                'message' => 'Import master data sukses dieksekusi!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Import Gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}
