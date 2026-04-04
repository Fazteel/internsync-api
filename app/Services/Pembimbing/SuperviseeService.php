<?php

namespace App\Services\Pembimbing;

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
        $internships = $this->repository->getByPembimbing($pembimbingId);

        return $internships->map(function ($intern) {
            $start = Carbon::parse($intern->start_date)->startOfDay();
            $end = Carbon::parse($intern->end_date)->startOfDay();
            $now = Carbon::now()->startOfDay();

            $totalMonths = max(1, intval($start->diffInMonths($end)));
            $monthsCompleted = 0;

            if ($now->gt($start)) {
                $monthsCompleted = min($totalMonths, intval($start->diffInMonths($now)));
            }

            $status = 'Menunggu';
            if ($intern->status === 'active') {
                $status = 'Aktif';
                if ($now->gt($end)) {
                    $status = 'Selesai';
                }
            } elseif ($intern->status === 'cancelled') {
                $status = 'Bermasalah';
            }

            return [
                'id' => $intern->id,
                'nis' => $intern->student->nis ?? '-',
                'name' => $intern->student->user->name ?? '-',
                'major' => $intern->student->major->major_name ?? $intern->student->jurusan ?? '-',
                'industry' => $intern->industry->name ?? '-',
                'duration' => $totalMonths,
                'monthsCompleted' => $monthsCompleted,
                'status' => $status
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

        if ($internship->status === 'active') {
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
            'name' => $internship->student->user->name ?? '-',
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
}
