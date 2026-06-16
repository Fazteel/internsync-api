<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use App\Imports\UsersImport;
use App\Jobs\ProcessUserActivation;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuthMail;

class UserController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new UsersImport;
            Excel::import($import, $request->file('file'));

            AuditLog::record(
                'm_users',
                'import',
                "Mengimpor data pengguna dari Excel. Sukses: {$import->successCount}, Gagal: {$import->failCount}"
            );

            return response()->json([
                'message' => 'Proses import selesai',
                'success' => $import->successCount,
                'failed' => $import->failCount
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal import data: ' . $e->getMessage()], 500);
        }
    }

    public function resendActivationEmail($id)
    {
        $user = User::findOrFail($id);

        ProcessUserActivation::dispatch($user);

        return response()->json(['message' => 'Email aktivasi berhasil dikirim ulang ke ' . $user->email]);
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $token = Str::random(60);
        DB::table('tr_password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $link = env('FRONTEND_URL', 'http://localhost:5173') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        Mail::to($user->email)->send(new AuthMail($user, $link, 'reset'));

        AuditLog::record(
            'm_users',
            'reset-password',
            "Mengirimkan link reset password untuk pengguna {$user->name} ({$user->email})"
        );

        return response()->json(['message' => 'Link reset password berhasil dikirim ke email pengguna.']);
    }
}
