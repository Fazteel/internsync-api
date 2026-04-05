<?php

namespace App\Repositories\Siswa;

use App\Models\Permission;

class PermissionRepository
{
    public function create(array $data)
    {
        return Permission::create($data);
    }

    public function getByInternship($internshipId)
    {
        return Permission::where('internship_id', $internshipId)->latest('start_date')->get();
    }

    public function getByPembimbing($pembimbingId)
    {
        return Permission::whereHas('internship', function ($q) use ($pembimbingId) {
            $q->where('pembimbing_id', $pembimbingId);
        })->with('internship.student.user')->latest('start_date')->get();
    }

    public function updateStatus($id, $status)
    {
        $permission = Permission::findOrFail($id);
        $permission->update(['status' => $status]);
        return $permission;
    }
}
