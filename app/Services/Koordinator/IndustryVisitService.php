<?php

namespace App\Services\Koordinator;

use App\Models\Industry;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use App\Repositories\Koordinator\IndustryVisitRepository;

class IndustryVisitService
{
    protected $repository;

    public function __construct(IndustryVisitRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getVisitsByCoordinator($coordinatorId)
    {
        $visits = $this->repository->getVisitsByCoordinator($coordinatorId);

        return $visits->map(function ($trip) {
            return [
                'id' => $trip->id,
                'pembimbing_name' => $trip->pembimbing->teacher->name ?? '-',
                'industry' => $trip->industry->name ?? '-',
                'plannedDate' => Carbon::parse($trip->planned_date)->translatedFormat('d M Y'),
                'purpose' => $trip->purpose,
                'status' => ucfirst($trip->status),
                'feedback' => $trip->feedback,
            ];
        });
    }

    public function getFormOptions($coordinatorId)
    {
        $internships = $this->repository->getInternshipsByCoordinator($coordinatorId);
        $pembimbings = $internships->pluck('pembimbing')->unique('id')->values()->filter()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->teacher->name ?? $user->name ?? 'Tanpa Nama',
            ];
        });

        return [
            'industries' => $internships->pluck('industry')->unique('id')->values()->filter(),
            'pembimbings' => $pembimbings
        ];
    }

    public function createVisit($coordinatorId, array $data)
    {
        $payload = [
            'coordinator_id' => $coordinatorId,
            'pembimbing_id' => $data['pembimbing_id'],
            'industry_id' => $data['industry_id'],
            'planned_date' => $data['planned_date'],
            'purpose' => $data['purpose'],
            'status' => 'pending'
        ];

        $result = $this->repository->saveVisit($payload);

        $industry = Industry::find($data['industry_id']);
        $pembimbing = User::find($data['pembimbing_id']);
        $koordinator = User::find($coordinatorId);

        if ($industry && $pembimbing) {
            $hubinUsers = User::whereHas('roles', fn($q) => $q->where('name', 'hubin'))->get();
            foreach ($hubinUsers as $hubin) {
                Notification::send(
                    $hubin->id,
                    'Pengajuan SPPD Baru',
                    "Koordinator {$koordinator->name} mengajukan perjalanan dinas untuk {$pembimbing->name} ke {$industry->name}.",
                    'warning'
                );
            }

            Notification::send(
                $pembimbing->id,
                'Jadwal Kunjungan Industri',
                "Koordinator telah menjadwalkan Anda untuk kunjungan ke {$industry->name} pada tanggal " . Carbon::parse($data['planned_date'])->format('d/m/Y') . ". Menunggu ACC Hubin.",
                'info'
            );
        }

        return $result;
    }
}
