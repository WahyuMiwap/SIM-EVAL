<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Batasi akses berdasarkan peran + status aktif.
     * Pemakaian: ->middleware('role:superadmin') atau ('role:superadmin,operator').
     * Magang yang dilarang hapus/kelola-akun akan menerima 403 JSON atau redirect login.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sesi berakhir. Silakan login ulang.'], 401);
            }
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akun dinonaktifkan. Hubungi superadmin.'], 403);
            }
            return redirect()->route('login')->withErrors(['email' => 'Akun dinonaktifkan. Hubungi superadmin.']);
        }

        if (!empty($roles) && !in_array($user->role, $roles, true)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak untuk peran Anda.'], 403);
            }
            abort(403, 'Akses ditolak untuk peran Anda.');
        }

        return $next($request);
    }
}
