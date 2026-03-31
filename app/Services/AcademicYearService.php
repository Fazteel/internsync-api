<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Repositories\AcademicYearRepository;

class AcademicYearService extends BaseService
{
    protected $academicYearRepository;

    public function __construct(AcademicYearRepository $academicYearRepository)
    {
        parent::__construct($academicYearRepository);
        $this->academicYearRepository = $academicYearRepository;
    }

    public function create(array $data)
    {
        if (!empty($data['is_active']) && $data['is_active']) {
            AcademicYear::query()->update(['is_active' => false]);
        }

        return $this->academicYearRepository->create($data);
    }

    public function update($id, array $data)
    {
        if (!empty($data['is_active']) && $data['is_active']) {
            AcademicYear::query()->update(['is_active' => false]);
        }

        return $this->academicYearRepository->update($id, $data);
    }
}