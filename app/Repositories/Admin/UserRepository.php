<?php

namespace App\Repositories\Admin;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function getAllUsersWithRoles($search = null, $role = null)
    {
        return $this->model->with('roles', 'student.major', 'student.classroom')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($role && $role !== 'All', function ($q) use ($role) {
                $q->whereHas('roles', function ($query) use ($role) {
                    $query->where('name', $role);
                });
            })
            ->get();
    }

    public function syncRoles($userId, array $roleIds)
    {
        $user = $this->find($userId);
        $user->roles()->sync($roleIds);
        return $user;
    }
}