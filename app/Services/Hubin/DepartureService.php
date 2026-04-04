<?php

namespace App\Services\Hubin;

use App\Repositories\Hubin\DepartureRepository;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DepartureService
{
    protected $repository;
    public function __construct(DepartureRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDepartureList()
    {
        return $this->repository->getAllWithRelations()->map(function ($intern) {
            return [
                'id' => $intern->id,
                'nis' => $intern->student->nis ?? '-',
                'studentName' => $intern->student->user->name ?? '-',
                'major' => $intern->student->major->major_name ?? ($intern->student->jurusan ?? '-'),
                'industry' => $intern->industry->name ?? '-',
                'startDate' => $intern->start_date ? Carbon::parse($intern->start_date)->translatedFormat('d F Y') : '-',
                'endDate' => $intern->end_date ? Carbon::parse($intern->end_date)->translatedFormat('d F Y') : '-',
                'status' => $intern->status === 'active' ? 'Disetujui' : ($intern->status === 'cancelled' ? 'Dibatalkan' : 'Menunggu'),
                'industry_id' => $intern->industry_id,
                'pembimbing_id' => $intern->pembimbing_id,
                'has_letter' => $intern->letters->count() > 0,
            ];
        });
    }

    public function verifyDeparture($id, $action)
    {
        if ($action === 'approve') {
            $this->repository->updateInternship($id, ['status' => 'active']);
            return 'Keberangkatan berhasil disetujui. Surat pengantar siap dicetak.';
        }
        $this->repository->updateInternship($id, [
            'industry_id' => null,
            'start_date' => null,
            'end_date' => null,
            'duration_month' => null,
            'status' => 'Pending'
        ]);
        return 'Penempatan berhasil dibatalkan. Siswa kembali ke status Belum Ditempatkan.';
    }

    public function generateLetter($id)
    {
        $internship = $this->repository->findInternship($id);
        if ($internship->status !== 'active') throw new \Exception('Hanya status Disetujui yang bisa dicetak!', 400);
        if ($this->repository->checkExistingLetter($id)) throw new \Exception('Surat pengantar sudah dibuat.', 400);

        $pdf = Pdf::loadView('pdf.surat-pengantar', [
            'tanggalSurat' => Carbon::now()->translatedFormat('d F Y'),
            'namaPerusahaan' => $internship->industry->name,
            'alamatPerusahaan' => $internship->industry->address ?? '-',
            'hrName' => $internship->industry->hr_name ?? 'HRD Manager',
            'namaSiswa' => $internship->student->user->name,
            'nisSiswa' => $internship->student->nis,
            'jurusanSiswa' => $internship->student->major->major_name ?? $internship->student->jurusan,
            'tanggalMulai' => Carbon::parse($internship->start_date)->translatedFormat('d F Y'),
            'durasi' => $internship->duration_month,
        ]);

        $fileName = 'Surat_Pengantar_' . Str::slug($internship->student->user->name) . '_' . time() . '.pdf';
        $filePath = 'surat_pengantar/' . $fileName;
        Storage::disk('public')->put($filePath, $pdf->output());

        $letterNumber = '421.5/' . str_pad($internship->id, 3, '0', STR_PAD_LEFT) . '/HUBIN/' . date('Y');
        $this->repository->createLetter([
            'internship_id' => $internship->id,
            'letter_number' => $letterNumber,
            'status' => 'approved',
            'file_path' => $filePath,
        ]);

        return $letterNumber;
    }
}
