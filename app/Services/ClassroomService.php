<?php

namespace App\Services;

use App\Repositories\ClassroomRepository;

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