<?php

namespace App\Services\Admin;

use App\Repositories\Admin\MajorRepository;
use App\Services\BaseService;

class MajorService extends BaseService
{
    protected $majorRepository;

    public function __construct(MajorRepository $majorRepository)
    {
        parent::__construct($majorRepository);
        $this->majorRepository = $majorRepository;
    }
}