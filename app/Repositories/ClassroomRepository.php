<?php

namespace App\Repositories;

use App\Models\Classroom;

class ClassroomRepository extends BaseRepository
{
    public function __construct(Classroom $model)
    {
        parent::__construct($model);
    }

    public function getAllWithMajor()
    {
        return $this->model->with('major')->get();
    }
}