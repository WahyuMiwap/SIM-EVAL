<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Halaman login + daftar akun yang dapat masuk (fase internal/UAT).
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('operator.dashboard');
        }

        $accounts = User::where('is_active', true)
            ->orderByRaw("CASE role WHEN 'superadmin' THEN 0 WHEN 'operator' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'jabatan'])
            ->all();

        return view('auth.login', compact('accounts'));
    }

    /**
     * Proses login email + password (throttle 5x/menit).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string',
        ], [
            'email.required' => 'Masukkan email akun.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Masukkan password.',
        ]);

        $email = strtolower(trim($request->input('email')));

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            Log::warning('Login gagal', ['email' => $email, 'ip' => $request->ip()]);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        if (! $user->is_active) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun dinonaktifkan. Hubungi superadmin.']);
        }

        Auth::login($user, (bool) $request->boolean('remember'));
        $request->session()->regenerate();

        AuditService::record('login', 'user', $user->id, "Login: {$user->name}");
        Log::info('Login berhasil', ['user_id' => $user->id, 'ip' => $request->ip()]);

        return redirect()->intended(route('operator.dashboard'))
            ->with('success', 'Selamat datang kembali, '.strtok($user->name, ' ').'!');
    }

    /**
     * Logout — cabut sesi penuh.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditService::record('logout', 'user', $user->id, "Logout: {$user->name}");
            Log::info('Logout', ['user_id' => $user->id]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('current_role');

        return redirect()->route('login');
    }
}
