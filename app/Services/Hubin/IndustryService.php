<?php

namespace App\Services\Hubin;

use App\Repositories\Hubin\IndustryRepository;
use Illuminate\Support\Facades\Storage;

class IndustryService
{
    protected $repository;
    public function __construct(IndustryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getIndustries($search)
    {
        return $this->repository->list($search);
    }

    public function createIndustry($data, $file = null)
    {
        if ($file) $data['mou_file'] = $file->store('mou_files', 'public');
        $data['is_active'] = $data['is_active'] ?? true;
        return $this->repository->create($data);
    }

    public function updateIndustry($id, $data, $file = null)
    {
        $industry = $this->repository->find($id);
        if ($file) {
            if ($industry->mou_file) Storage::disk('public')->delete($industry->mou_file);
            $data['mou_file'] = $file->store('mou_files', 'public');
        }
        return $this->repository->update($id, $data);
    }

    public function deleteIndustry($id)
    {
        $industry = $this->repository->find($id);
        if ($industry->mou_file) Storage::disk('public')->delete($industry->mou_file);
        return $this->repository->delete($id);
    }
}
