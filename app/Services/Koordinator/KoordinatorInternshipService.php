<?php

namespace App\Services\Koordinator;

use App\Models\InternshipApplication;
use App\Models\Internship;
use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use App\Repositories\Koordinator\KoordinatorInternshipRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KoordinatorInternshipService
{
    protected $repo;

    public function __construct(KoordinatorInternshipRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getDetail($id)
    {
        return $this->repo->findWithDetails($id);
    }

    private function generateApplicationNumber()
    {
        $year = date('Y');
        $count = InternshipApplication::whereYear('created_at', $year)->count() + 1;
        $number = str_pad($count, 3, '0', STR_PAD_LEFT);
        return "{$number}/SATDIK-SMK/II.03/G.{$year}";
    }

    public function storeApplication($data, $action)
    {
        return DB::transaction(function () use ($data, $action) {
            $startDate = Carbon::now()->addDay();
            $endDate = $startDate->copy()->addDays(90);

            $status = ($action === 'pengajuan') ? 'menunggu_acc_pengajuan' : 'draft';

            $appNumber = isset($data['id'])
                ? InternshipApplication::find($data['id'])->application_number
                : $this->generateApplicationNumber();

            $application = InternshipApplication::updateOrCreate(
                ['id' => $data['id'] ?? null],
                [
                    'application_number' => $appNumber,
                    'coordinator_id' => Auth::id(),
                    'industry_id' => $data['industry_id'],
                    'pembimbing_id' => $data['pembimbing_id'],
                    'suggested_start_date' => $startDate,
                    'suggested_end_date' => $endDate,
                    'status' => $status,
                ]
            );

            $application->students()->sync($data['student_ids']);

            if ($action === 'pengajuan') {
                $application->load(['industry', 'students']);
                $industryName = $application->industry->name ?? 'Industri Tujuan';

                Notification::send($data['pembimbing_id'], 'Penugasan Pembimbing', "Anda telah ditunjuk sebagai pembimbing siswa di {$industryName}.", 'info');

                foreach ($application->students as $student) {
                    Notification::send($student->user_id, 'Status Pengajuan', "Keberangkatan anda di {$industryName} telah diajukan dan menunggu ACC Hubin.", 'info');
                }

                $hubins = User::role('Hubin')->get();
                foreach ($hubins as $hubin) {
                    Notification::send($hubin->id, 'Pengajuan Baru', "Terdapat pengajuan keberangkatan baru untuk industri {$industryName}.", 'warning');
                }
            }

            return $application;
        });
    }

    public function placementProcess($id, $data, $action)
    {
        return DB::transaction(function () use ($id, $data, $action) {
            $app = InternshipApplication::findOrFail($id);

            $start = Carbon::parse($data['departure_date']);
            if ($data['duration_option'] === 'custom') {
                $end = Carbon::parse($data['final_end_date']);
            } else {
                $months = $data['duration_option'] === '3_bulan' ? 3 : 6;
                $end = $start->copy()->addMonths($months);
            }

            $newStatus = ($action === 'pengiriman') ? 'menunggu_acc_pengiriman' : 'pengiriman';

            $app->update([
                'departure_date' => $data['departure_date'],
                'duration_option' => $data['duration_option'],
                'final_end_date' => $end,
                'status' => $newStatus
            ]);

            if ($action === 'pengiriman') {
                $app->load(['industry']);
                $industryName = $app->industry->name ?? 'Industri Tujuan';

                $hubins = User::role('Hubin')->get();
                foreach ($hubins as $hubin) {
                    Notification::send($hubin->id, 'Pengiriman Baru', "Terdapat dokumen pengiriman siswa ke industri {$industryName} yang menunggu ACC.", 'warning');
                }
            }

            return $app;
        });
    }

    public function extendPlacement($data)
    {
        return DB::transaction(function () use ($data) {
            $type = $data['type'];
            $id = $data['id'];

            if ($type === 'individual') {
                $internship = Internship::where('student_id', $id)->firstOrFail();
                $currentEnd = Carbon::parse($internship->end_date);
                $id = $internship->id;
            } else {
                $application = InternshipApplication::findOrFail($id);
                $currentEnd = Carbon::parse($application->final_end_date);
            }

            if ($data['duration_option'] === 'custom') {
                $newEnd = Carbon::parse($data['custom_end_date']);
            } else {
                $months = $data['duration_option'] === '3_bulan' ? 3 : 6;
                $newEnd = $currentEnd->copy()->addMonths($months);
            }

            if ($type === 'individual') {
                $this->repo->extendIndividualInternship($id, $newEnd);

                $internship->refresh();
                $student = Student::find($internship->student_id);

                Notification::send($student->user_id, 'Perpanjangan PKL', "Masa PKL Anda telah diperpanjang secara individual hingga " . $newEnd->translatedFormat('d M Y') . ".", 'info');
                Notification::send($internship->pembimbing_id, 'Perpanjangan Siswa', "Masa PKL siswa {$student->name} diperpanjang hingga " . $newEnd->translatedFormat('d M Y') . ".", 'info');
            } else {
                $this->repo->extendBatchInternships($id, $newEnd);

                $internships = Internship::with('student')->where('application_id', $id)->get();
                foreach ($internships as $intern) {
                    Notification::send($intern->student->user_id, 'Perpanjangan PKL', "Masa PKL kelompok Anda diperpanjang hingga " . $newEnd->translatedFormat('d M Y') . ".", 'info');
                }

                Notification::send($application->pembimbing_id, 'Perpanjangan Kelompok', "Masa PKL kelompok bimbingan Anda diperpanjang hingga " . $newEnd->translatedFormat('d M Y') . ".", 'info');
            }

            return true;
        });
    }

    public function withdrawPlacement($studentId)
    {
        return DB::transaction(function () use ($studentId) {
            $student = Student::with('user')->findOrFail($studentId);
            $internship = Internship::where('student_id', $studentId)->first();

            if ($internship) {
                $pembimbingId = $internship->pembimbing_id;

                $this->repo->withdrawInternship($studentId);

                Notification::send(
                    $student->user_id,
                    'Penarikan Penempatan',
                    "Penempatan PKL Anda telah dicabut oleh Koordinator. Status Anda kini tidak aktif PKL.",
                    'error'
                );

                Notification::send(
                    $pembimbingId,
                    'Penarikan Siswa',
                    "Siswa bernama {$student->name} telah ditarik dari penempatan industrinya.",
                    'warning'
                );
            }
            return true;
        });
    }
}
