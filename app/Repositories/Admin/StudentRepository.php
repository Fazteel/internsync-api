<?php

namespace App\Repositories\Admin;

use App\Models\Student;

class StudentRepository
{
    public function getAll($search)
    {
        return Student::with('user.roles', 'major', 'classroom')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($query) => $query->where('email', 'like', "%{$search}%"));
            })->get();
    }
}
