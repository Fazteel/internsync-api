<?php

namespace App\Repositories\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class AdminDashboardRepository
{
    public function countStudents()
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'Siswa'))->count();
    }

    public function countTeachers()
    {
        return User::whereHas('roles', fn($q) => $q->whereIn('name', ['Pembimbing', 'Koordinator', 'Hubin']))->count();
    }

    public function countIndustries()
    {
        return DB::table('m_industries')->count();
    }

    public function getSettingValue($key)
    {
        return DB::table('m_settings')->where('setting_key', $key)->value('setting_value');
    }

    public function getAuditLogs()
    {
        return AuditLog::with('user:id,name')->orderBy('created_at', 'desc')->get();
    }
}
