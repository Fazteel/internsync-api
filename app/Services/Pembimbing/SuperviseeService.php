<?php

namespace App\Services\Pembimbing;

use App\Models\Notification;
use App\Models\Permission;
use App\Repositories\Pembimbing\SuperviseeRepository;
use Carbon\Carbon;

class SuperviseeService
{
    protected $repository;

    public function __construct(SuperviseeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getList($pembimbingId)
    {
        Carbon::setLocale('id');
        $internships = $this->repository->getByPembimbing($pembimbingId);

        return $internships->map(function ($intern) {
            $start = Carbon::parse($intern->start_date)->startOfDay();
            $end = Carbon::parse($intern->end_date)->startOfDay();
            $now = Carbon::now()->startOfDay();

            $totalDays = max(1, $start->diffInDays($end));

            $daysPassed = 0;
            if ($now->gt($start)) {
                $daysPassed = min($totalDays, $start->diffInDays($now));
            }

            $filledLogbooksCount = $intern->logbooks()->count();

            $progressPercent = round(($filledLogbooksCount / $totalDays) * 100);
            if ($progressPercent > 100) $progressPercent = 100;

            $durationLabel = $totalDays >= 30
                ? round($totalDays / 30) . " Bulan"
                : $totalDays . " Hari";

            $passedLabel = $filledLogbooksCount . " Logbook";

            $lastLog = $intern->logbooks()->latest('date')->first();
            $isFlagged = false;

            if ($intern->status === 'aktif') {
                $checkStart = $lastLog ? Carbon::parse($lastLog->date)->addDay() : $start;
                $workDaysSinceLastLog = $checkStart->diffInWeekdays($now);

                $approvedPermissions = Permission::where('internship_id', $intern->id)
                    ->where('status', 'approved')
                    ->get();

                $totalPermittedDays = 0;
                foreach ($approvedPermissions as $p) {
                    $totalPermittedDays += Carbon::parse($p->start_date)->diffInWeekdays(Carbon::parse($p->end_date)->addDay());
                }

                if (max(0, $workDaysSinceLastLog - $totalPermittedDays) >= 3) {
                    $isFlagged = true;
                }
            }

            $status = 'Menunggu';
            if ($intern->status === 'aktif') {
                $status = $now->gt($end) ? 'Selesai' : 'Aktif';
            } elseif ($intern->status === 'cancelled') {
                $status = 'Bermasalah';
            }

            return [
                'id' => $intern->id,
                'nis' => $intern->student->nis ?? '-',
                'name' => $intern->student->name ?? '-',
                'major' => $intern->student->major->major_name ?? $intern->student->jurusan ?? '-',
                'industry' => $intern->industry->name ?? '-',
                'status' => $status,
                'is_flagged' => $isFlagged,
                'departure_date' => $intern->start_date,
                'final_end_date' => $intern->end_date,
                'duration_label' => $durationLabel,
                'passed_label' => $passedLabel,
                'progress_percent' => $progressPercent
            ];
        });
    }

    public function getDetail($id, $pembimbingId)
    {
        $internship = $this->repository->findByIdAndPembimbing($id, $pembimbingId);

        $start = Carbon::parse($internship->start_date)->startOfDay();
        $end = Carbon::parse($internship->end_date)->startOfDay();
        $now = Carbon::now()->startOfDay();

        $totalMonths = max(1, intval($start->diffInMonths($end)));
        $totalDays = intval($start->diffInDays($end)) + 1;

        $status = 'Menunggu';
        $progressPercent = 0;

        if ($internship->status === 'aktif') {
            $status = 'Aktif';
            if ($now->gt($end)) {
                $status = 'Selesai';
                $progressPercent = 100;
            } else {
                $dayPassed = intval($start->diffInDays($now)) + 1;
                $progressPercent = round($dayPassed / $totalDays) * 100;
                $progressPercent = min(100, max(0, $progressPercent));
            }
        } elseif ($internship->status === 'cancelled') {
            $status = 'Bermasalah';
        }

        return [
            'id' => $internship->id,
            'nis' => $internship->student->nis ?? '-',
            'name' => $internship->student->name ?? '-',
            'major' => $internship->student->major->major_name ?? $internship->student->jurusan ?? '-',
            'industry' => $internship->industry->name ?? '-',
            'address' => $internship->industry->address ?? '-',
            'duration' => $totalMonths,
            'status' => $status,
            'phone' => $internship->student->user->phone ?? '-',
            'email' => $internship->student->user->email ?? '-',
            'startDate' => $internship->start_date ? Carbon::parse($internship->start_date)->translatedFormat('d F Y') : '-',
            'endDate' => $internship->end_date ? Carbon::parse($internship->end_date)->translatedFormat('d F Y') : '-',
            'progressPercent' => $progressPercent
        ];
    }

    public function terminateInternship($id, $pembimbingId, $reason)
    {
        $internship = $this->repository->findByIdAndPembimbing($id, $pembimbingId);

        if (!$internship) {
            throw new \Exception('Data magang tidak ditemukan.', 404);
        }

        $internship->update([
            'status' => 'cancelled',
            'cancelled_reason' => $reason
        ]);

        if ($internship->coordinator_id) {
            Notification::send(
                $internship->coordinator_id,
                'Siswa Bermasalah',
                "Siswa {$internship->student->user->name} ditarik karena: {$reason}",
                'error'
            );
        }

        return $internship;
    }
}
