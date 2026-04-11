<?php

namespace App\Services\Pembimbing;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Pembimbing\LogbookMonitoringRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LogbookMonitoringService
{
    protected $repository;

    public function __construct(LogbookMonitoringRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getLogbooks($pembimbingId, $studentId = null)
    {
        $logbooks = $this->repository->getByPembimbing($pembimbingId, $studentId);

        return $logbooks->map(fn($log) => [
            'id' => $log->id,
            'student_id' => $log->internship->student_id ?? 0,
            'studentName' => $log->internship->student->user->name ?? $log->internship->student->name ?? '-',
            'nis' => $log->internship->student->nis ?? '-',
            'industry' => $log->internship->industry->name ?? '-',
            'date' => Carbon::parse($log->date)->translatedFormat('d M Y'),
            'created_at' => Carbon::parse($log->created_at)->format('Y-m-d'),
            'activity' => $log->activity,
            'attachment' => $log->file_path ? basename($log->file_path) : '-',
            'attachment_url' => $log->file_path ? asset('storage/' . $log->file_path) : null,
        ]);
    }

    public function exportPdf($pembimbingId, $studentId = null)
    {
        $logbooks = $this->repository->getByPembimbing($pembimbingId, $studentId);

        if ($logbooks->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data logbook untuk diexport.'], 404);
        }

        $settings = DB::table('m_settings')->pluck('setting_value', 'setting_key')->toArray();
        $verifyUrl = url('/verify-dokumen/logbook-' . time());
        $qrBase64 = null;

        try {
            $logoString = '';
            if (!empty($settings['school_logo'])) {
                $logoParts = explode(',', $settings['school_logo']);
                $logoString = base64_decode(end($logoParts));
            }

            if ($logoString) {
                $qrCode = QrCode::format('png')->mergeString($logoString, 0.2)->errorCorrection('H')->size(100)->generate($verifyUrl);
            } else {
                $qrCode = QrCode::format('png')->size(100)->generate($verifyUrl);
            }
            $qrBase64 = 'data:image/png;base64,' . base64_encode($qrCode);
        } catch (\Exception $e) {
            Log::error('QR Code Error: ' . $e->getMessage());
            try {
                $qrCodeSvg = QrCode::size(100)->generate($verifyUrl);
                $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
            } catch (\Exception $fallbackError) {
                Log::error('QR Fallback Error: ' . $fallbackError->getMessage());
            }
        }

        $pembimbing = User::with('teacher')->find($pembimbingId);
        $namaPembimbing = $pembimbing->teacher->name ?? $pembimbing->name ?? 'Pembimbing';
        $nipPembimbing = $pembimbing->teacher->nip ?? '-';

        $data = [
            'title' => 'Laporan Logbook Siswa Magang',
            'date' => now()->translatedFormat('d F Y'),
            'logbooks' => $logbooks,

            'yayasan_name'    => $settings['yayasan_name'] ?? 'YAYASAN PEMBINA LEMBAGA PENDIDIKAN...',
            'school_name'     => $settings['school_name'] ?? 'SMK PGRI TELAGASARI',
            'qr_signature'    => $qrBase64,
            'pembimbing_name' => $namaPembimbing,
            'pembimbing_nip'  => $nipPembimbing,
        ];

        $pdf = Pdf::loadView('pdf.logbook_report', $data);

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Laporan_Logbook_' . now()->format('YmdHis') . '.pdf"');
    }
}
