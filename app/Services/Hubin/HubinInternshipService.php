<?php

namespace App\Services\Hubin;

use App\Repositories\Hubin\HubinInternshipRepository;
use App\Models\InternshipApplication;
use App\Models\Internship;
use App\Models\Notification;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HubinInternshipService
{
    protected $repo;

    public function __construct(HubinInternshipRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getPendingApplications()
    {
        return $this->repo->getPendingApplications();
    }

    public function getPendingPlacements()
    {
        return $this->repo->getPendingPlacements();
    }

    public function processApplication($id, $actionData)
    {
        $newStatus = $actionData['action'] === 'approve' ? 'pengajuan' : 'ditolak';
        $application = $this->repo->updateStatus($id, $newStatus);

        $application->load(['industry', 'students']);
        $industryName = $application->industry->name ?? 'Industri Tujuan';

        if ($newStatus === 'pengajuan') {
            $this->generateApplicationCertificate($application);

            Notification::send($application->coordinator_id, 'Pengajuan Disetujui', "Pengajuan ke {$industryName} telah disetujui oleh Hubin. Silakan lanjutkan ke tahap pengiriman.", 'success');
            foreach ($application->students as $student) {
                Notification::send($student->user_id, 'Pengajuan Disetujui', "Pengajuan magang di {$industryName} telah disetujui Hubin.", 'success');
            }
        } elseif ($newStatus === 'ditolak') {
            Notification::send($application->coordinator_id, 'Pengajuan Ditolak', "Pengajuan ke {$industryName} ditolak. Silakan periksa kembali data pengajuan.", 'error');
        }

        return $application;
    }

    public function processPlacement($id, $actionData)
    {
        return DB::transaction(function () use ($id, $actionData) {
            $newStatus = $actionData['action'] === 'approve' ? 'pengiriman' : 'ditolak';
            $application = $this->repo->updateStatus($id, $newStatus);

            $application->load(['industry', 'students']);
            $industryName = $application->industry->name ?? 'Industri Tujuan';

            if ($newStatus === 'pengiriman') {
                $studentIds = [];

                foreach ($application->students as $student) {
                    Internship::create([
                        'application_id' => $application->id,
                        'student_id'     => $student->id,
                        'industry_id'    => $application->industry_id,
                        'pembimbing_id'  => $application->pembimbing_id,
                        'start_date'     => $application->departure_date,
                        'end_date'       => $application->final_end_date,
                        'status'         => 'aktif'
                    ]);

                    $studentIds[] = $student->id;

                    Notification::send($student->user_id, 'Penempatan Resmi', "Selamat! Anda resmi diberangkatkan PKL ke {$industryName}.", 'success');
                }

                if (!empty($studentIds)) {
                    Student::whereIn('id', $studentIds)->update(['is_pkl' => true]);
                }

                $this->generatePlacementCertificate($application);
                $this->generateHandoverCertificate($application);

                Notification::send($application->coordinator_id, 'Pengiriman Selesai', "Pengiriman ke {$industryName} telah resmi. Dokumen sudah dapat diunduh.", 'success');
                Notification::send($application->pembimbing_id, 'Penempatan Ditetapkan', "Pengiriman siswa ke {$industryName} telah disetujui. Siapkan rencana kunjungan Anda.", 'success');
            } elseif ($newStatus === 'ditolak') {
                Notification::send($application->coordinator_id, 'Pengiriman Ditolak', "Pengiriman ke {$industryName} ditolak oleh Hubin.", 'error');
            }

            return $application;
        });
    }

    private function getSharedLetterData($application, $perihal)
    {
        Carbon::setLocale('id');
        $application->loadMissing(['industry', 'pembimbing.teacher', 'students']);
        $settings = DB::table('m_settings')->pluck('setting_value', 'setting_key')->toArray();
        $verifyUrl = url('/verify-dokumen/' . $application->application_number);
        $qrBase64 = null;

        try {
            $logoString = '';
            if (!empty($settings['school_logo'])) {
                $logoParts = explode(',', $settings['school_logo']);
                $logoString = base64_decode(end($logoParts));
            }

            if ($logoString) {
                $qrCode = QrCode::format('png')->mergeString($logoString, 0.2)->errorCorrection('H')->size(120)->generate($verifyUrl);
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
            }
        }

        $hubin = Auth::user()->teacher;
        $now = Carbon::now();

        return [
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

            'nomor_surat'    => $application->application_number,
            'perihal'        => $perihal,
            'tanggal_surat'  => $now->isoFormat('D MMMM YYYY'),
            'hari_ini'       => $now->isoFormat('dddd'),
            'tanggal_ini'    => $now->isoFormat('D'),
            'bulan_ini'      => $now->isoFormat('MMMM'),
            'tahun_ini'      => $now->isoFormat('YYYY'),

            'industri'       => $application->industry->name ?? '-',
            'alamat_industri' => $application->industry->address ?? '-',
            'pembimbing'     => $application->pembimbing->teacher->name ?? 'Belum Diatur',
            'phone_pembimbing' => $application->pembimbing->teacher->phone ?? '-',
            'wakasek_name'   => $hubin->name ?? 'Hubin SMK PGRI Telagasari',
            'wakasek_nip'   => $hubin->nip ?? '-',
            'students'       => $application->students,

            'start_date'     => $application->suggested_start_date ? Carbon::parse($application->suggested_start_date)->isoFormat('D MMMM YYYY') : '-',
            'end_date'       => $application->suggested_end_date ? Carbon::parse($application->suggested_end_date)->isoFormat('D MMMM YYYY') : '-',
            'actual_start'   => $application->departure_date ? Carbon::parse($application->departure_date)->isoFormat('D MMMM YYYY') : '-',
            'actual_end'     => $application->final_end_date ? Carbon::parse($application->final_end_date)->isoFormat('D MMMM YYYY') : '-',
        ];
    }

    private function generateApplicationCertificate($application)
    {
        $data = $this->getSharedLetterData($application, 'Pengajuan Calon Siswa Prakerin');

        $referenceStudent = $application->students->first();
        if ($referenceStudent) {
            $className = $referenceStudent->kelas;
            $data['grade_name'] = str_contains($className, 'XII') ? 3 : (str_contains($className, 'XI') ? 2 : 1);
            $data['semester_count'] = ($data['grade_name'] * 2) - 1;
        }

        $pdf = Pdf::loadView('pdf.surat_pengajuan', $data);
        $fileName = 'Surat_Pengajuan_' . str_replace('/', '-', $application->application_number) . '.pdf';
        $path = 'letters/pengajuan/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());
        $application->update(['application_letter_path' => $path]);
        return $path;
    }

    public function generatePlacementCertificate($application)
    {
        $data = $this->getSharedLetterData($application, 'Pengiriman Siswa Peserta Prakerin');

        $pdf = Pdf::loadView('pdf.surat_pengiriman', $data);
        $fileName = 'Surat_Pengiriman_' . str_replace('/', '-', $application->application_number) . '.pdf';
        $path = 'letters/pengiriman/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());
        $application->update(['placement_letter_path' => $path]);
        return $path;
    }

    public function generateHandoverCertificate($application)
    {
        $data = $this->getSharedLetterData($application, 'Berita Acara Serah Terima Siswa Prakerin');

        $pdf = Pdf::loadView('pdf.berita_acara', $data);
        $fileName = 'Berita_Acara_' . str_replace('/', '-', $application->application_number) . '.pdf';
        $path = 'letters/berita_acara/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());
        $application->update(['ba_path' => $path]);
        return $path;
    }
}
