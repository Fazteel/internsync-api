<?php

namespace App\Services\Admin;

use App\Repositories\Admin\ClassroomRepository;
use App\Services\BaseService;

class ClassroomService extends BaseService
{
    protected $classroomRepository;

    public function __construct(ClassroomRepository $classroomRepository)
    {
        parent::__construct($classroomRepository);
        $this->classroomRepository = $classroomRepository;
    }

    public function getAllClassrooms()
    {
        return $this->classroomRepository->getAllWithMajor();
    }
}