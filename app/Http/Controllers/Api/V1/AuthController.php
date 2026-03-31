<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            
            $request->session()->regenerate();
            $user = User::find(Auth::id())->load(['roles', 'student']);

            return response()->json([
                'message' => 'Login berhasil',
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'Email atau password salah.'], 401);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['roles', 'student']);
        
        return response()->json($user);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout berhasil']);
    }
}