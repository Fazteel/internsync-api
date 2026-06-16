<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:identifier|nullable|email',
            'identifier' => 'required_without:email|nullable|string',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        $remember = $request->boolean('remember');

        $user = $request->filled('email')
            ? User::where('email', $request->email)->first()
            : User::findByIdentifier($request->identifier);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email/NIS/NIP atau password salah.'], 401);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user->load(['roles', 'student', 'teacher']),
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['roles', 'student', 'teacher']);

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
