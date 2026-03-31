<?php

namespace App\Repositories;

use App\Models\Major;

class MajorRepository extends BaseRepository
{
    public function __construct(Major $model)
    {
        parent::__construct($model);
    }
}