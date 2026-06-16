<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\AuthMail;

class PasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)
            ->where(function ($q) use ($request) {
                $q->whereHas('teacher', fn ($sq) => $sq->where('nip', $request->identifier))
                    ->orWhereHas('student', fn ($sq) => $sq->where('nis', $request->identifier));
            })->first();

        if (!$user) {
            return response()->json(['message' => 'Data identitas dan email tidak cocok atau tidak ditemukan!'], 404);
        }

        $token = Str::random(60);
        DB::table('tr_password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $link = env('FRONTEND_URL', 'http://localhost:5173') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        Mail::to($user->email)->send(new AuthMail($user, $link, 'reset'));

        return response()->json(['message' => 'Link reset password berhasil dikirim ke email Anda.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:m_users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $resetRecord = DB::table('tr_password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['message' => 'Token tidak valid atau sudah kedaluwarsa!'], 400);
        }

        DB::table('m_users')->where('email', $request->email)->update([
            'password' => Hash::make($request->password),
            'is_active' => true,
            'updated_at' => now()
        ]);

        DB::table('tr_password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil disimpan! Silakan login.']);
    }
}