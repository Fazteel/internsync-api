<?php

namespace App\Services\Hubin;

use App\Repositories\Hubin\InternshipApprovalRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InternshipApprovalService
{
    protected $repo;

    public function __construct(InternshipApprovalRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getPendingApplications()
    {
        return $this->repo->getPendingApplications();
    }

    public function processApplication($id, $actionData)
    {
        $newStatus = $actionData['action'] === 'approve' ? 'pengajuan' : 'ditolak';

        $application = $this->repo->updateStatus($id, $newStatus);

        if ($newStatus === 'pengajuan') {
            $this->generateApplicationLetter($application);
        }

        return $application;
    }

    private function generateApplicationLetter($application)
    {
        $application->load(['industry', 'pembimbing.teacher', 'students']);

        $settings = DB::table('m_settings')->pluck('setting_value', 'setting_key')->toArray();

        $referenceStudent = $application->students->first();
        if (!$referenceStudent) return null;

        $className = $referenceStudent->kelas;
        $gradeNumeric = str_contains($className, 'XII') ? 3 : (str_contains($className, 'XI') ? 2 : 1);
        $activeSemester = 'Ganjil';
        $semesterNumeric = ($gradeNumeric * 2) - ($activeSemester === 'Ganjil' ? 1 : 0);

        $verifyUrl = url('/verify-dokumen/' . $application->application_number);
        $qrBase64 = null;

        try {
            $logoString = '';
            if (!empty($settings['school_logo'])) {
                $logoParts = explode(',', $settings['school_logo']);
                $logoString = base64_decode(end($logoParts));
            }

            if ($logoString) {
                $qrCode = QrCode::format('png')
                    ->mergeString($logoString, 0.2)
                    ->errorCorrection('H')
                    ->size(120)
                    ->generate($verifyUrl);
            } else {
                $qrCode = QrCode::format('png')->size(120)->generate($verifyUrl);
            }

            $qrBase64 = 'data:image/png;base64,' . base64_encode($qrCode);
        } catch (\Exception $e) {
            Log::error('QR Code Error: ' . $e->getMessage());
            try {
                $qrCodeSvg = QrCode::size(120)->generate($verifyUrl);
                $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
            } catch (\Exception $fallbackError) {
                Log::error('QR Fallback Error: ' . $fallbackError->getMessage());
                $qrBase64 = null;
            }
        }

        $hubin = Auth::user()->teacher;

        $data = [
            'nomor_surat'    => $application->application_number,
            'grade_name'     => $gradeNumeric,
            'semester_count' => $semesterNumeric,
            'perihal'        => 'Pengajuan Calon Siswa Prakerin',
            'tanggal_surat'  => Carbon::now()->isoFormat('D MMMM YYYY'),
            'industri'       => $application->industry->name,
            'alamat_industri' => $application->industry->address,
            'pembimbing'     => $application->pembimbing->teacher->name ?? 'Belum Diatur',
            'phone_pembimbing' => $application->pembimbing->teacher->phone ?? '-',
            'students'       => $application->students,
            'start_date'     => Carbon::parse($application->suggested_start_date)->isoFormat('D MMMM'),
            'end_date'       => Carbon::parse($application->suggested_end_date)->isoFormat('D MMMM YYYY'),
            'wakasek_name'   => $hubin->name ?? 'Hubin SMK PGRI Telagasari',

            'yayasan_name'   => $settings['yayasan_name'] ?? 'YAYASAN PEMBINA LEMBAGA PENDIDIKAN...',
            'school_name'    => $settings['school_name'] ?? 'SMK PGRI TELAGASARI',
            'accreditation'  => $settings['accreditation'] ?? 'A',
            'npsn'           => $settings['npsn'] ?? '-',
            'nss'            => $settings['nss'] ?? '-',
            'school_address' => $settings['school_address'] ?? '-',
            'school_phone'   => $settings['school_phone'] ?? '-',
            'support_email'  => $settings['support_email'] ?? '-',
            'school_website' => $settings['school_website'] ?? '-',
            'school_logo'    => $settings['school_logo'] ?? '',

            'qr_signature'   => $qrBase64,
        ];

        $pdf = Pdf::loadView('pdf.surat_pengajuan', $data);

        $fileName = 'Surat_Pengajuan_' . str_replace('/', '-', $application->application_number) . '.pdf';
        $path = 'letters/pengajuan/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());
        $application->update(['application_letter_path' => $path]);

        return $path;
    }
}
