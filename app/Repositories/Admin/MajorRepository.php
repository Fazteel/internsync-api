<?php

namespace App\Repositories\Admin;

use App\Models\Major;
use App\Repositories\BaseRepository;

class MajorRepository extends BaseRepository
{
    public function __construct(Major $model)
    {
        parent::__construct($model);
    }
}