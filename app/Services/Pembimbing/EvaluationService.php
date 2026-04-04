<?php

namespace App\Services\Pembimbing;

use App\Repositories\Pembimbing\EvaluationRepository;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EvaluationService
{
    protected $repository;

    public function __construct(EvaluationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getStudentEvaluations($pembimbingId)
    {
        $internships = $this->repository->getByPembimbing($pembimbingId);

        return $internships->map(function ($intern) {
            $finalEval = $intern->evaluations->first();
            $end = Carbon::parse($intern->end_date)->startOfDay();
            $now = Carbon::now()->startOfDay();

            $status = ($intern->status === 'completed' || ($intern->status === 'active' && $now->gt($end)))
                ? 'Selesai' : 'Aktif';

            return [
                'internship_id' => $intern->id,
                'name' => $intern->student->user->name ?? 'Tanpa Nama',
                'nis' => $intern->student->nis ?? '-',
                'industry' => $intern->industry->name ?? '-',
                'status' => $status,
                'evaluationScore' => $finalEval ? $finalEval->score : null,
                'evaluationNotes' => $finalEval ? $finalEval->description : '',
            ];
        });
    }

    public function processEvaluation($pembimbingId, array $data)
    {
        return DB::transaction(function () use ($pembimbingId, $data) {
            $internship = $this->repository->findById($data['internship_id']);

            if ($internship->pembimbing_id !== $pembimbingId) {
                throw new Exception('Akses ditolak! Ini bukan siswa bimbingan Anda.');
            }

            $end = Carbon::parse($internship->end_date)->startOfDay();
            $now = Carbon::now()->startOfDay();
            if ($internship->status === 'active' && $now->lt($end)) {
                throw new Exception('Siswa masih dalam masa magang aktif. Belum bisa dievaluasi.');
            }

            $this->repository->saveFinalEvaluation($data['internship_id'], $pembimbingId, $data);
            $this->repository->updateInternshipStatus($internship, 'completed');

            return true;
        });
    }
}
