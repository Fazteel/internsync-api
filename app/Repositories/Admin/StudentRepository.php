<?php

namespace App\Repositories\Admin;

use App\Models\Student;

class StudentRepository
{
    public function getAll($search, $isPkl = null)
    {
        return Student::with('user.roles', 'major', 'classroom')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($userQuery) => $userQuery->where('email', 'like', "%{$search}%"));
                });
            })
            ->when($isPkl !== null, function ($q) use ($isPkl) {
                $q->where('is_pkl', $isPkl);
            })
            ->get();
    }
}
