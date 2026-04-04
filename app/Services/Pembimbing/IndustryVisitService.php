<?php

namespace App\Services\Pembimbing;

use App\Models\IndustryVisit;
use App\Models\Internship;
use Carbon\Carbon;
use App\Repositories\Pembimbing\IndustryVisitRepository;

class IndustryVisitService
{
    protected $repository;

    public function __construct(IndustryVisitRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getVisitsByPembimbing($pembimbingId)
    {
        $visits = $this->repository->getVisitByPembimbing($pembimbingId);

        return $visits->map(function ($trip) {
            return [
                'id' => $trip->id,
                'industry' => $trip->industry->name ?? '-',
                'plannedDate' => Carbon::parse($trip->planned_date)->translatedFormat('d M Y'),
                'purpose' => $trip->purpose,
                'status' => ucfirst($trip->status),
                'feedback' => $trip->feedback,
                'file_path' => $trip->file_path
            ];
        });
    }

    public function getAssignedIndustries($pembimbingId)
    {
        $internships = $this->repository->getInternshipsByPembimbing($pembimbingId);
        return $internships->pluck('industry')
            ->unique('id')
            ->values();
    }

    public function createVisit($pembimbingId, array $data)
    {
        $payload = [
            'pembimbing_id' => $pembimbingId,
            'industry_id' => $data['industry_id'],
            'planned_date' => $data['planned_date'],
            'purpose' => $data['purpose'],
            'status' => 'pending'
        ];

        return $this->repository->saveVisit($payload);
    }

    public function getVisitFileUrl($id, $pembimbingId)
    {
        $visit = $this->repository->findVisitByIdAndPembimbing($id, $pembimbingId);

        if ($visit->status !== 'approved') {
            throw new \Exception('Surat belum diterbitkan oleh Hubin.');
        }

        if (!$visit->file_path) {
            throw new \Exception('File dokumen tidak ditemukan.');
        }

        return asset('storage/' . $visit->file_path);
    }
}