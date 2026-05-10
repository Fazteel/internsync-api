<?php

namespace App\Services\Hubin;

use App\Models\IndustryVisit;
use App\Models\Notification;
use App\Repositories\Hubin\VisitApprovalRepository;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VisitApprovalService
{
    protected $repository;

    public function __construct(VisitApprovalRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllVisits()
    {
        $visits = $this->repository->getPembimbingVisit();

        return $visits->map(function ($visit) {
            return [
                'id' => $visit->id,
                'teacherName' => $visit->pembimbing->teacher->name ?? 'Tanpa Nama',
                'industry' => $visit->industry->name ?? '-',
                'plannedDate' => Carbon::parse($visit->planned_date)->translatedFormat('d M Y'),
                'purpose' => $visit->purpose,
                'status' => ucfirst($visit->status),
                'feedback' => $visit->feedback,
                'file_path' => $visit->file_path
            ];
        });
    }

    public function generateSPPD($id)
    {
        Carbon::setLocale('id');
        $visit = IndustryVisit::with(['pembimbing.teacher', 'industry'])->findOrFail($id);

        if ($visit->status !== 'approved') {
            throw new \Exception("Hanya pengajuan yang telah Disetujui yang dapat dicetak!");
        }

        if ($visit->file_path && Storage::disk('public')->exists($visit->file_path)) {
            return $visit;
        }

        $settings = DB::table('m_settings')->pluck('setting_value', 'setting_key')->toArray();
        $date = Carbon::parse($visit->planned_date);

        $data = [
            'visit' => $visit,
            'tanggalSurat' => Carbon::now()->translatedFormat('d F Y'),
            'hari' => $date->translatedFormat('l'),
            'tanggalBerangkat' => $date->translatedFormat('d F Y'),
            'yayasan_name' => $settings['yayasan_name'] ?? 'YAYASAN PEMBINA LEMBAGA PENDIDIKAN DASAR DAN MENENGAH PGRI KABUPATEN KARAWANG',
            'school_name' => $settings['school_name'] ?? 'SMK PGRI TELAGASARI',
            'school_address' => $settings['school_address'] ?? 'Jl. Syech Quro Telagasari Desa Talagasari Kec. Telagasari Kab. Karawang 41381',
            'school_logo'    => $settings['school_logo'] ?? '',
            'kepsek_name' => $settings['kepsek_name'] ?? 'Kepala Sekolah',
            'kepsek_nip' => $settings['kepsek_nip'] ?? '-',
        ];

        $pdf = Pdf::loadView('pdf.sppd', $data);

        $fileName = 'SPPD_' . Str::slug($visit->pembimbing->name) . '_' . time() . '.pdf';
        $filePath = 'sppd/' . $fileName;

        Storage::disk('public')->put($filePath, $pdf->output());
        $visit->update(['file_path' => $filePath]);

        if ($visit->pembimbing_id) {
            Notification::send(
                $visit->pembimbing_id,
                'Dokumen SPPD Diterbitkan',
                "Surat Perintah Perjalanan Dinas (SPPD) untuk kunjungan Anda ke {$visit->industry->name} telah diterbitkan dan dapat diunduh melalui sistem.",
                'info'
            );
        }

        return $visit;
    }

    public function processApproval($id, $status, $feedback = null)
    {
        $dbStatus = ($status === 'Approved') ? 'approved' : 'rejected';

        $updateData = [
            'status' => $dbStatus,
            'feedback' => ($status === 'Rejected') ? $feedback : null
        ];

        $this->repository->update($id, $updateData);

        if ($dbStatus === 'approved') {
            $this->generateSPPD($id);
        }

        $visit = IndustryVisit::with('industry')->find($id);

        if ($visit && $visit->pembimbing_id) {
            $statusTeks = ($status === 'approved') ? 'Disetujui' : 'Ditolak';
            $tipe = ($status === 'approved') ? 'success' : 'danger';
            $pesan = "Pengajuan perjalanan dinas ({$visit->purpose}) ke {$visit->industry->name} telah {$statusTeks} oleh pihak Hubin.";

            if ($status === 'Rejected' && $feedback) {
                $pesan .= " Catatan: {$feedback}";
            }

            Notification::send($visit->pembimbing_id, 'Status Pengajuan Kunjungan', $pesan, $tipe);
        }
    }
}
