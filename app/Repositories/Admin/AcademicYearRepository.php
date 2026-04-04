<?php

namespace App\Repositories\Admin;

use App\Models\AcademicYear;
use App\Repositories\BaseRepository;

class AcademicYearRepository extends BaseRepository
{
    public function __construct(AcademicYear $model)
    {
        parent::__construct($model);
    }
}