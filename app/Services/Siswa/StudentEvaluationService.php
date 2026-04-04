<?php

namespace App\Services\Siswa;

use App\Repositories\Siswa\StudentEvaluationRepository;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentEvaluationService
{
    protected $repository;

    public function __construct(StudentEvaluationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getData($userId)
    {
        $intern = $this->repository->getEvaluationByStudent($userId);
        if (!$intern) return null;

        $eval = $intern->evaluations->where('type', 'final')->first();

        return [
            'isEvaluated' => (bool)$eval,
            'name' => $intern->student->user->name ?? 'Tanpa Nama',
            'nis' => $intern->student->nis ?? '-',
            'score' => $eval->score ?? 0,
            'grade' => $this->calculateGrade($eval->score ?? 0),
            'evaluator' => $intern->pembimbing->name ?? 'Belum Ditentukan',
            'date' => $eval ? Carbon::parse($eval->evaluation_date)->translatedFormat('d F Y') : '-',
            'industry' => $intern->industry->name ?? '-',
            'notes' => $eval->description ?? '',
            'internship_id' => $intern->id
        ];
    }

    private function calculateGrade($score)
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        return 'D';
    }

    public function generatePdf($userId)
    {
        $data = $this->getData($userId);
        if (!$data || !$data['isEvaluated']) throw new \Exception("Evaluasi belum tersedia.");

        $pdf = Pdf::loadView('pdf.lembar-penilaian', $data);
        return $pdf->output();
    }
}