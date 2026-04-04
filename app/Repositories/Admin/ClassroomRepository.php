<?php

namespace App\Repositories\Admin;

use App\Models\Classroom;
use App\Repositories\BaseRepository;

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