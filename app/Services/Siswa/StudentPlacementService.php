<?php

namespace App\Services\Siswa;

use App\Repositories\Siswa\StudentPlacementRepository;
use Carbon\Carbon;

class StudentPlacementService
{
    protected $repo;
    public function __construct(StudentPlacementRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getPlacementDetails($userId)
    {
        $student = $this->repo->getStudentPlacement($userId);
        if (!$student || !$student->internship) throw new \Exception('Data penempatan tidak ditemukan.', 404);

        $intern = $student->internship;
        $letter = $intern->letters->where('status', 'approved')->first();

        return [
            'status' => $intern->status === 'active' ? 'Aktif' : ($intern->status === 'cancelled' ? 'Dibatalkan' : 'Menunggu Validasi'),
            'durasi' => $intern->duration_month ? $intern->duration_month . ' Bulan' : '-',
            'tanggalMulai' => $intern->start_date ? Carbon::parse($intern->start_date)->translatedFormat('d F Y') : 'Belum Ditentukan',
            'tanggalSelesai' => $intern->end_date ? Carbon::parse($intern->end_date)->translatedFormat('d F Y') : 'Belum Ditentukan',
            'raw_start_date' => $intern->start_date,
            'raw_end_date' => $intern->end_date,
            'industri' => [
                'nama' => $intern->industry->name ?? 'Belum Diplot',
                'alamat' => $intern->industry->address ?? '-',
                'pembimbingLapangan' => $intern->industry->hr_name ?? '-',
                'kontak' => $intern->industry->hr_phone ?? '-'
            ],
            'guruPembimbing' => [
                'nama' => $intern->pembimbing->name ?? 'Belum Diplot',
                'kontak' => $intern->pembimbing->phone ?? '-'
            ],
            'suratUrl' => $letter ? asset('storage/' . $letter->file_path) : null
        ];
    }
}
