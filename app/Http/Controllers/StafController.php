<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class StafController extends Controller
{
    /**
     * Daftar Staf & Tata Kelola Pengguna
     */
    public function index(Request $request)
    {
        $role   = $request->get('role', '');
        $search = strtolower(trim($request->get('search', '')));

        $allUsers = MockDataService::getUsers();

        $filtered = collect($allUsers)->filter(function ($u) use ($role, $search) {
            if ($role && $u->role !== $role) {
                return false;
            }
            if ($search) {
                $matchName  = str_contains(strtolower($u->name), $search);
                $matchEmail = str_contains(strtolower($u->email), $search);
                $matchNip   = str_contains(strtolower($u->nip ?? ''), $search);
                if (!$matchName && !$matchEmail && !$matchNip) return false;
            }
            return true;
        })->values()->all();

        $users = MockDataService::paginate($filtered, 10);

        $counts = [
            'total'      => count($allUsers),
            'superadmin' => count(array_filter($allUsers, fn($u) => $u->role === 'superadmin')),
            'operator'   => count(array_filter($allUsers, fn($u) => $u->role === 'operator')),
            'magang'     => count(array_filter($allUsers, fn($u) => $u->role === 'magang')),
        ];

        return view('operator.staf.index', compact('users', 'role', 'search', 'counts'));
    }

    /**
     * Tambah Akun Staf Baru
     */
    public function store(Request $request)
    {
        return redirect()->route('operator.staf.index')
            ->with('success', 'Akun staf operasional baru berhasil dibuat (Mock Mode).');
    }

    /**
     * Reset Password Staf
     */
    public function resetPassword(Request $request, $id)
    {
        return redirect()->route('operator.staf.index')
            ->with('success', 'Password berhasil direset ke standar kedinasan: BNN123456');
    }

    /**
     * Ubah Status Aktif Staf
     */
    public function toggleActive(Request $request, $id)
    {
        return redirect()->route('operator.staf.index')
            ->with('success', 'Status keaktifan staf berhasil diperbarui.');
    }

    /**
     * Hapus Staf (JSON untuk ReauthModal)
     */
    public function destroy($id)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Akun staf berhasil dihapus.',
            ]);
        }
        return redirect()->route('operator.staf.index')
            ->with('success', 'Akun staf berhasil dihapus.');
    }

    /**
     * Beralih Peran Simulasi (Super Admin / Operator / Magang)
     */
    public function switchRole(Request $request, $role)
    {
        if (in_array($role, ['superadmin', 'operator', 'magang'])) {
            session(['current_role' => $role]);
        }
        return back()->with('success', 'Berhasil beralih ke simulasi peran: ' . ucfirst($role));
    }
}
