<?php

namespace App\Services;

use App\Repositories\MajorRepository;

class MajorService extends BaseService
{
    protected $majorRepository;

    public function __construct(MajorRepository $majorRepository)
    {
        parent::__construct($majorRepository);
        $this->majorRepository = $majorRepository;
    }
}