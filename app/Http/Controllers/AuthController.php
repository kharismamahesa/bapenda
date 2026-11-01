<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses autentikasi pengguna.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $input = trim($request->input('username'));
        $password = $request->input('password');

        // Cari pengguna berdasarkan email, nama, atau prefix email
        $user = User::where('email', $input)
            ->orWhere('name', $input)
            ->orWhere('email', $input . '@bapenda.go.id')
            ->first();

        $credentials = [
            'email' => $user ? $user->email : $input,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Fleksibilitas password default (password atau password123)
        if ($password === 'password' || $password === 'password123') {
            $altPassword = $password === 'password' ? 'password123' : 'password';
            $altCredentials = [
                'email' => $credentials['email'],
                'password' => $altPassword,
            ];
            if (Auth::attempt($altCredentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
        }

        return back()->withErrors([
            'username' => 'Username/Email atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Logout pengguna dari sesi aplikasi.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
