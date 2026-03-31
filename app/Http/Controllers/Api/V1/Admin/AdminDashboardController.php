<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function stats()
    {
        $students = User::whereHas('roles', function($q) { 
            $q->where('name', 'Siswa'); 
        })->count();
        
        $teachers = User::whereHas('roles', function($q) { 
            $q->whereIn('name', ['Pembimbing', 'Koordinator', 'Hubin']); 
        })->count();

        $industries = DB::table('m_industries')->count(); 
        
        $regStatus = DB::table('m_settings')->where('setting_key', 'pkl_registration_status')->value('setting_value');

        return response()->json([
            'totalStudents' => $students,
            'totalTeachers' => $teachers,
            'totalIndustries' => $industries,
            'systemStatus' => $regStatus === 'Buka' ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup'
        ]);
    }

    public function logs()
    {
        $logs = AuditLog::with('user:id,name')->orderBy('created_at', 'desc')->get();
        return response()->json($logs);
    }
}