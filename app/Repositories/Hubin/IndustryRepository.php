<?php

namespace App\Repositories\Hubin;

use App\Models\Industry;

class IndustryRepository
{
    public function list($search = null)
    {
        $query = Industry::query();
        if ($search) {
            $query->where('name', 'like', "%$search%")->orWhere('hr_name', 'like', "%$search%");
        }
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function find($id)
    {
        return Industry::findOrFail($id);
    }

    public function create(array $data)
    {
        return Industry::create($data);
    }

    public function update($id, array $data)
    {
        $industry = $this->find($id);
        $industry->update($data);
        return $industry;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}
