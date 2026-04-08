<?php

namespace App\Repositories\Admin;

use App\Models\Teacher;

class TeacherRepository
{
    public function getAll($search, $role)
    {
        return Teacher::with('user.roles')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            })
            ->when($role && $role !== 'All', function ($q) use ($role) {
                $q->whereHas('user.roles', fn($query) => $query->where('name', $role));
            })->get();
    }
}
