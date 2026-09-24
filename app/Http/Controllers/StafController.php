<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStafRequest;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        $role = $request->get('role', '');
        $search = strtolower(trim($request->get('search', '')));

        $allUsers = self::allUsers();

        $filtered = collect($allUsers)->filter(function ($u) use ($role, $search) {
            if ($role && ($u->role ?? '') !== $role) {
                return false;
            }
            if ($search) {
                $matchName = str_contains(strtolower($u->name ?? ''), $search);
                $matchEmail = str_contains(strtolower($u->email ?? ''), $search);
                $matchNip = str_contains(strtolower($u->nip ?? ''), $search);
                if (! $matchName && ! $matchEmail && ! $matchNip) {
                    return false;
                }
            }

            return true;
        })->values()->all();

        $page = max(1, (int) $request->get('page', 1));
        $users = new LengthAwarePaginator(array_slice($filtered, ($page - 1) * 10, 10), count($filtered), 10, $page, ['path' => $request->url(), 'query' => $request->query()]);

        $counts = [
            'total' => count($allUsers),
            'superadmin' => count(array_filter($allUsers, fn ($u) => ($u->role ?? '') === 'superadmin')),
            'operator' => count(array_filter($allUsers, fn ($u) => ($u->role ?? '') === 'operator')),
            'magang' => count(array_filter($allUsers, fn ($u) => ($u->role ?? '') === 'magang')),
        ];

        return view('operator.staf.index', compact('users', 'role', 'search', 'counts'));
    }

    /**
     * Semua akun — DB bila ada baris, fallback mock.
     */
    public static function allUsers(): array
    {
        return User::orderBy('name')->get()->all();
    }

    public static function findUser($id): ?User
    {
        if (empty($id) || ! is_numeric($id)) {
            return null;
        }

        return User::find((int) $id);
    }

    /**
     * Tambah Akun Staf Baru.
     */
    public function store(StoreStafRequest $request)
    {
        $this->authorize('create', User::class);
        $validated = $request->validated();

        try {
            $user = DB::transaction(fn () => User::create([
                'name' => trim($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'role' => $validated['role'],
                'nip' => trim($validated['nip'] ?? '') ?: null,
                'jabatan' => trim($validated['jabatan'] ?? '') ?: null,
                'bidang_wilayah' => $validated['bidang_wilayah'] ?? null,
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]));
            AuditService::record('tambah_akun', 'user', $user->id, "Akun baru: {$user->name} ({$user->role})");
            Log::info('Akun staf dibuat', ['id' => $user->id, 'by' => auth()->id()]);
        } catch (\Throwable $e) {
            Log::error('Gagal buat akun', ['err' => $e->getMessage()]);

            return back()->withInput()->withErrors(['email' => 'Gagal membuat akun.']);
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
        if (! $user) {
            return response()->json(['message' => 'Akun tidak ditemukan.'], 404);
        }

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
        if (! $user) {
            return redirect()->route('operator.staf.index')->with('warning', 'Akun tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'role' => 'required|in:'.implode(',', self::ROLES),
            'nip' => 'nullable|string|max:50',
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

        DB::transaction(fn () => User::where('id', $id)->update([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'nip' => trim($validated['nip'] ?? '') ?: null,
            'jabatan' => trim($validated['jabatan'] ?? '') ?: null,
        ]));
        AuditService::record('ubah_akun', 'user', $id, "Akun diperbarui: {$validated['name']} ({$validated['role']})");
        Log::info('Akun staf diubah', ['id' => $id, 'by' => auth()->id()]);

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
            $user = User::findOrFail($id);
            DB::transaction(fn () => $user->forceFill(['password' => Hash::make($baru)])->save());
            AuditService::record('reset_password', 'user', $id, "Password direset untuk: {$user->name}");
            Log::info('Password staf direset', ['id' => $id, 'by' => auth()->id()]);
        } catch (\Throwable $e) {
            Log::error('Gagal reset password', ['id' => $id, 'err' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Gagal mereset password.'], 422);
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
            $user = User::findOrFail($id);

            if ((int) $id === (int) Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menonaktifkan akun sendiri.'], 422);
            }
            $baru = ! (bool) $user->is_active;
            if (! $baru && ($user->role ?? '') === 'superadmin' && self::countSuperadmin() <= 1) {
                return response()->json(['success' => false, 'message' => 'Minimal harus ada 1 superadmin aktif.'], 422);
            }
            $user->forceFill(['is_active' => $baru])->save();
            AuditService::record(
                $baru ? 'aktifkan_akun' : 'nonaktifkan_akun', 'user', $id,
                'Akun '.($baru ? 'diaktifkan' : 'dinonaktifkan').": {$user->name}"
            );

            return response()->json([
                'success' => true, 'is_active' => $baru,
                'message' => $baru ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal toggle status akun', ['id' => $id, 'err' => $e->getMessage()]);

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
        $this->authorize('delete', $target);
        DB::transaction(fn () => User::where('id', $id)->delete());
        AuditService::record('hapus_akun', 'user', $id, "Akun dihapus: {$nama}");
        Log::info('Akun staf dihapus', ['id' => $id, 'by' => auth()->id()]);

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
        $aksi = $request->get('aksi', '');
        $search = strtolower(trim($request->get('search', '')));

        $q = ActivityLog::with('user')->orderByDesc('id');
        if ($aksi) {
            $q->where('aksi', $aksi);
        }
        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_aktor', 'like', "%{$search}%")
                    ->orWhere('detail', 'like', "%{$search}%");
            });
        }
        $logs = $q->paginate(15)->withQueryString();
        $aksis = ActivityLog::select('aksi')->distinct()->orderBy('aksi')->pluck('aksi')->all();

        return view('operator.staf.aktivitas', compact('logs', 'aksis', 'aksi', 'search'));
    }

    protected static function countSuperadmin(): int
    {
        return User::where('role', 'superadmin')->where('is_active', true)->count();
    }
}
