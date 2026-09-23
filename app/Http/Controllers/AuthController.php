<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        $accounts = [];
        try {
            if (Schema::hasTable('users')) {
                $accounts = User::where('is_active', true)
                    ->orderByRaw("CASE role WHEN 'superadmin' THEN 0 WHEN 'operator' THEN 1 ELSE 2 END")
                    ->orderBy('name')
                    ->get(['id', 'name', 'email', 'role', 'jabatan'])
                    ->all();
            }
        } catch (\Throwable $e) {
        }
        if (empty($accounts)) {
            foreach (\App\Services\MockDataService::getUsers() as $u) {
                if (!($u->is_active ?? true)) continue;
                $accounts[] = (object)[
                    'id' => $u->id, 'name' => $u->name, 'email' => $u->email,
                    'role' => $u->role, 'jabatan' => $u->jabatan ?? null,
                ];
            }
        }

        return view('auth.login', compact('accounts'));
    }

    /**
     * Proses login email + password (throttle 5x/menit).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Masukkan email akun.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Masukkan password.',
        ]);

        $email = strtolower(trim($request->input('email')));

        try {
            $user = Schema::hasTable('users')
                ? User::where('email', $email)->first()
                : null;
        } catch (\Throwable $e) {
            $user = null;
        }

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        if (!$user->is_active) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun dinonaktifkan. Hubungi superadmin.']);
        }

        Auth::login($user, (bool) $request->boolean('remember'));
        $request->session()->regenerate();
        try {
            if (Schema::hasColumn('users', 'last_login_at')) {
                $user->forceFill(['last_login_at' => now()])->save();
            }
        } catch (\Throwable $e) {
        }

        try {
            \App\Services\AuditService::record('login', 'user', $user->id, "Login: {$user->name}");
        } catch (\Throwable $e) {
        }

        return redirect()->intended(route('operator.dashboard'))
            ->with('success', 'Selamat datang kembali, ' . strtok($user->name, ' ') . '!');
    }

    /**
     * Logout — cabut sesi penuh.
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                \App\Services\AuditService::record('logout', 'user', $user->id, "Logout: {$user->name}");
            }
        } catch (\Throwable $e) {
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('current_role');

        return redirect()->route('login');
    }
}
