<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AuditLog;
use App\Models\AcademicYear;

class AcademicYearController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index()
    {
        return response()->json(
            $this->academicYearService->getAll()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('m_academic_years')->where(
                    fn ($q) => $q->where('semester',$request->semester)
                )
            ],
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'boolean'
        ]);

        $year = $this->academicYearService->create($validated);

        AuditLog::record(
            'm_academic_years',
            'create',
            "Menambahkan tahun ajaran {$year->name} {$year->semester}"
        );

        return response()->json([
            'message' => 'Tahun ajaran berhasil ditambah',
            'data' => $year
        ],201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('m_academic_years')
                ->where(fn ($q) => $q->where('semester',$request->semester))
                ->ignore($id)
            ],
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'boolean'
        ]);

        $year = $this->academicYearService->update($id,$validated);

        AuditLog::record(
            'm_academic_years',
            'update',
            "Memperbarui tahun ajaran {$year->name} {$year->semester}"
        );

        return response()->json([
            'message' => 'Tahun ajaran berhasil diupdate',
            'data' => $year
        ]);
    }

    public function destroy($id)
    {
        $year = AcademicYear::find($id);

        $nama = $year 
            ? "{$year->name} {$year->semester}" 
            : "ID {$id}";

        $this->academicYearService->delete($id);

        AuditLog::record(
            'm_academic_years',
            'delete',
            "Menghapus tahun ajaran {$nama}"
        );

        return response()->json([
            'message' => 'Tahun ajaran berhasil dihapus'
        ]);
    }
}