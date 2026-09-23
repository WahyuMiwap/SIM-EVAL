<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class StafController extends Controller
{
    public const ROLES = ['superadmin', 'operator', 'magang'];

    /**
     * Daftar Staf & Tata Kelola Pengguna (superadmin).
     * DB dulu, fallback mock (read-only di mock).
     */
    public function index(Request $request)
    {
        $role   = $request->get('role', '');
        $search = strtolower(trim($request->get('search', '')));

        $allUsers = self::allUsers();

        $filtered = collect($allUsers)->filter(function ($u) use ($role, $search) {
            if ($role && ($u->role ?? '') !== $role) return false;
            if ($search) {
                $matchName  = str_contains(strtolower($u->name ?? ''), $search);
                $matchEmail = str_contains(strtolower($u->email ?? ''), $search);
                $matchNip   = str_contains(strtolower($u->nip ?? ''), $search);
                if (!$matchName && !$matchEmail && !$matchNip) return false;
            }
            return true;
        })->values()->all();

        $users = MockDataService::paginate($filtered, 10);

        $counts = [
            'total'      => count($allUsers),
            'superadmin' => count(array_filter($allUsers, fn($u) => ($u->role ?? '') === 'superadmin')),
            'operator'   => count(array_filter($allUsers, fn($u) => ($u->role ?? '') === 'operator')),
            'magang'     => count(array_filter($allUsers, fn($u) => ($u->role ?? '') === 'magang')),
        ];

        return view('operator.staf.index', compact('users', 'role', 'search', 'counts'));
    }

    /**
     * Semua akun — DB bila ada baris, fallback mock.
     */
    public static function allUsers(): array
    {
        try {
            if (Schema::hasTable('users')) {
                $rows = User::orderBy('name')->get();
                if ($rows->isNotEmpty()) return $rows->all();
            }
        } catch (\Throwable $e) {
        }
        return MockDataService::getUsers();
    }

    public static function findUser($id): ?object
    {
        if (empty($id)) return null;
        foreach (self::allUsers() as $u) {
            if ((int) $u->id === (int) $id) return $u;
        }
        return null;
    }

    /**
     * Tambah Akun Staf Baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:users,email',
            'role'     => 'required|in:' . implode(',', self::ROLES),
            'nip'      => 'nullable|string|max:50',
            'jabatan'  => 'nullable|string|max:100',
            'password' => 'required|string|min:6|max:100',
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        try {
            if (!Schema::hasTable('users')) throw new \RuntimeException('mock');
            $user = User::create([
                'name'      => trim($validated['name']),
                'email'     => strtolower(trim($validated['email'])),
                'role'      => $validated['role'],
                'nip'       => trim($validated['nip'] ?? '') ?: null,
                'jabatan'   => trim($validated['jabatan'] ?? '') ?: null,
                'password'  => Hash::make($validated['password']),
                'is_active' => true,
            ]);
            AuditService::record('tambah_akun', 'user', $user->id, "Akun baru: {$user->name} ({$user->role})");
        } catch (\Throwable $e) {
            return redirect()->route('operator.staf.index')
                ->with('warning', 'Mode mock: akun belum tersimpan permanen (aktif penuh setelah database tersambung).');
        }

        return redirect()->route('operator.staf.index')
            ->with('success', "Akun {$user->name} berhasil dibuat.");
    }

    /**
     * Edit Akun (data untuk modal).
     */
    public function edit($id)
    {
        $user = self::findUser($id);
        if (!$user) return response()->json(['message' => 'Akun tidak ditemukan.'], 404);
        return response()->json([
            'id' => $user->id, 'name' => $user->name, 'email' => $user->email,
            'role' => $user->role, 'nip' => $user->nip ?? null,
            'jabatan' => $user->jabatan ?? null, 'is_active' => (bool) ($user->is_active ?? true),
        ]);
    }

    /**
     * Perbarui Akun (termasuk ganti role).
     */
    public function update(Request $request, $id)
    {
        $user = self::findUser($id);
        if (!$user) {
            return redirect()->route('operator.staf.index')->with('warning', 'Akun tidak ditemukan.');
        }

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'role'    => 'required|in:' . implode(',', self::ROLES),
            'nip'     => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
        ]);

        // Larangan: menurunkan role diri sendiri / superadmin terakhir.
        if ((int) $id === (int) Auth::id() && $validated['role'] !== 'superadmin') {
            return back()->withInput()->withErrors(['role' => 'Anda tidak dapat menurunkan peran akun sendiri.']);
        }
        if (($user->role ?? '') === 'superadmin' && $validated['role'] !== 'superadmin'
            && self::countSuperadmin() <= 1) {
            return back()->withInput()->withErrors(['role' => 'Minimal harus ada 1 superadmin aktif.']);
        }

        try {
            if (!Schema::hasTable('users')) throw new \RuntimeException('mock');
            User::where('id', $id)->update([
                'name'    => trim($validated['name']),
                'email'   => strtolower(trim($validated['email'])),
                'role'    => $validated['role'],
                'nip'     => trim($validated['nip'] ?? '') ?: null,
                'jabatan' => trim($validated['jabatan'] ?? '') ?: null,
            ]);
            AuditService::record('ubah_akun', 'user', $id, "Akun diperbarui: {$validated['name']} ({$validated['role']})");
        } catch (\Throwable $e) {
            return redirect()->route('operator.staf.index')
                ->with('warning', 'Mode mock: perubahan belum tersimpan permanen.');
        }

        // Sinkronkan session bila mengubah akun sendiri
        if ((int) $id === (int) Auth::id() && Auth::user()->role !== $validated['role']) {
            Auth::user()->forceFill(['role' => $validated['role']])->save();
        }

        return redirect()->route('operator.staf.index')
            ->with('success', 'Data akun berhasil diperbarui.');
    }

    /**
     * Reset Password Staf (JSON untuk fetch frontend).
     */
    public function resetPassword(Request $request, $id)
    {
        $validated = $request->validate([
            'new_password' => 'nullable|string|min:6|max:100',
        ]);
        $baru = $validated['new_password'] ?? 'BNN123456';

        try {
            if (!Schema::hasTable('users')) throw new \RuntimeException('mock');
            $user = User::findOrFail($id);
            $user->forceFill(['password' => Hash::make($baru)])->save();
            AuditService::record('reset_password', 'user', $id, "Password direset untuk: {$user->name}");
        } catch (\Throwable $e) {
            $fail = $e instanceof \RuntimeException;
            return response()->json([
                'success' => false,
                'message' => $fail
                    ? 'Mode mock: reset belum tersimpan permanen.'
                    : 'Gagal mereset password.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset.',
        ]);
    }

    /**
     * Ubah Status Aktif Staf (JSON untuk fetch frontend).
     */
    public function toggleActive(Request $request, $id)
    {
        try {
            if (!Schema::hasTable('users')) throw new \RuntimeException('mock');
            $user = User::findOrFail($id);

            if ((int) $id === (int) Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menonaktifkan akun sendiri.'], 422);
            }
            $baru = !(bool) $user->is_active;
            if (!$baru && ($user->role ?? '') === 'superadmin' && self::countSuperadmin() <= 1) {
                return response()->json(['success' => false, 'message' => 'Minimal harus ada 1 superadmin aktif.'], 422);
            }
            $user->forceFill(['is_active' => $baru])->save();
            AuditService::record(
                $baru ? 'aktifkan_akun' : 'nonaktifkan_akun', 'user', $id,
                "Akun " . ($baru ? 'diaktifkan' : 'dinonaktifkan') . ": {$user->name}"
            );

            return response()->json([
                'success' => true, 'is_active' => $baru,
                'message' => $baru ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.',
            ]);
        } catch (\Throwable $e) {
            if ($e instanceof \RuntimeException) {
                return response()->json(['success' => false, 'message' => 'Mode mock: perubahan belum tersimpan permanen.'], 422);
            }
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status.'], 422);
        }
    }

    /**
     * Hapus Staf.
     */
    public function destroy($id)
    {
        if ((int) $id === (int) Auth::id()) {
            $msg = 'Tidak dapat menghapus akun sendiri.';
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('operator.staf.index')->with('warning', $msg);
        }

        $target = self::findUser($id);
        if ($target && ($target->role ?? '') === 'superadmin' && self::countSuperadmin() <= 1) {
            $msg = 'Minimal harus ada 1 superadmin. Tambah superadmin dulu sebelum menghapus.';
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('operator.staf.index')->with('warning', $msg);
        }

        $nama = $target->name ?? "ID {$id}";
        try {
            if (!Schema::hasTable('users')) throw new \RuntimeException('mock');
            User::where('id', $id)->delete();
            AuditService::record('hapus_akun', 'user', $id, "Akun dihapus: {$nama}");
        } catch (\Throwable $e) {
            if (!($e instanceof \RuntimeException)) throw $e;
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Mode mock: penghapusan belum tersimpan permanen.'], 422);
            }
            return redirect()->route('operator.staf.index')
                ->with('warning', 'Mode mock: penghapusan belum tersimpan permanen.');
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Akun staf berhasil dihapus.']);
        }
        return redirect()->route('operator.staf.index')->with('success', 'Akun staf berhasil dihapus.');
    }

    /**
     * Riwayat Aktivitas (superadmin) — filter aksi + pencarian aktor.
     */
    public function activity(Request $request)
    {
        $aksi   = $request->get('aksi', '');
        $search = strtolower(trim($request->get('search', '')));

        $logs = [];
        $aksis = [];
        try {
            if (Schema::hasTable('activity_logs')) {
                $q = \App\Models\ActivityLog::with('user')->orderByDesc('id');
                if ($aksi) $q->where('aksi', $aksi);
                if ($search) {
                    $q->where(function ($w) use ($search) {
                        $w->where('nama_aktor', 'like', "%{$search}%")
                          ->orWhere('detail', 'like', "%{$search}%");
                    });
                }
                $logs = $q->paginate(15)->withQueryString();
                $aksis = \App\Models\ActivityLog::select('aksi')->distinct()->orderBy('aksi')->pluck('aksi')->all();
            }
        } catch (\Throwable $e) {
        }

        return view('operator.staf.aktivitas', compact('logs', 'aksis', 'aksi', 'search'));
    }

    protected static function countSuperadmin(): int
    {
        try {
            if (Schema::hasTable('users')) {
                return User::where('role', 'superadmin')->where('is_active', true)->count();
            }
        } catch (\Throwable $e) {
        }
        return count(array_filter(
            MockDataService::getUsers(),
            fn($u) => ($u->role ?? '') === 'superadmin' && ($u->is_active ?? true)
        ));
    }
}
