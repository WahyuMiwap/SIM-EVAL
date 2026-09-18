@extends('layouts.app')

@section('title', 'Tata Kelola Akun & Hak Akses')
@section('page-title', 'Tata Kelola Pengguna')
@section('page-subtitle', 'Kelola akun Super Admin, Staf Operator, dan Anak Magang P2M BNN Kota Surabaya')

@section('content')

@php
    $currentRole = session('current_role', 'superadmin');
@endphp

{{-- ─── Role Switcher Simulator Banner ────────────────────────── --}}
<div class="mb-5 p-4 rounded-xl border flex flex-wrap items-center justify-between gap-3"
     style="background: linear-gradient(135deg, rgba(67, 97, 238, 0.06), rgba(99, 102, 241, 0.03)); border-color: rgba(67, 97, 238, 0.2);">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-white shadow-sm"
             style="background: var(--primary);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-primary">Simulasi Multi-Role Aktif:</span>
                <span class="badge {{ $currentRole === 'superadmin' ? 'badge-primary' : ($currentRole === 'operator' ? 'badge-info' : 'badge-warning') }} font-semibold text-xs uppercase px-2.5 py-0.5 rounded-full">
                    {{ $currentRole === 'superadmin' ? 'Super Admin' : ($currentRole === 'operator' ? 'Staf Operator' : 'Anak Magang') }}
                </span>
            </div>
            <p class="text-xs text-muted mt-0.5">Uji dan evaluasi langsung antarmuka serta hak akses setiap peran pengguna sesuai alur sistem.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs text-muted font-medium">Beralih Tampilan:</span>
        <form action="{{ route('operator.staf.switch-role', 'superadmin') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn btn-sm {{ $currentRole === 'superadmin' ? 'btn-primary' : 'btn-secondary' }}">
                Super Admin
            </button>
        </form>
        <form action="{{ route('operator.staf.switch-role', 'operator') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn btn-sm {{ $currentRole === 'operator' ? 'btn-primary' : 'btn-secondary' }}">
                Staf Operator
            </button>
        </form>
        <form action="{{ route('operator.staf.switch-role', 'magang') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn btn-sm {{ $currentRole === 'magang' ? 'btn-primary' : 'btn-secondary' }}">
                Anak Magang
            </button>
        </form>
    </div>
</div>

{{-- ─── Stat Cards ────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="dash-card p-4 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-primary bg-primary/10 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        <div>
            <span class="text-xs text-muted font-medium">Total Akun Terdaftar</span>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $counts['total'] }}</h3>
        </div>
    </div>

    <div class="dash-card p-4 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-purple-600 bg-purple-500/10 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <div>
            <span class="text-xs text-muted font-medium">Super Admin</span>
            <h3 class="text-xl font-bold text-purple-600 dark:text-purple-400 tabular-nums">{{ $counts['superadmin'] }}</h3>
        </div>
    </div>

    <div class="dash-card p-4 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-blue-600 bg-blue-500/10 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <span class="text-xs text-muted font-medium">Staf Operator P2M</span>
            <h3 class="text-xl font-bold text-blue-600 dark:text-blue-400 tabular-nums">{{ $counts['operator'] }}</h3>
        </div>
    </div>

    <div class="dash-card p-4 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-amber-600 bg-amber-500/10 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
        </div>
        <div>
            <span class="text-xs text-muted font-medium">Anak Magang</span>
            <h3 class="text-xl font-bold text-amber-600 dark:text-amber-400 tabular-nums">{{ $counts['magang'] }}</h3>
        </div>
    </div>
</div>

{{-- ─── Toolbar Filter & Tambah ───────────────────────────────── --}}
<div class="dash-card p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('operator.staf.index') }}"
           class="btn btn-sm {{ empty($role) ? 'btn-primary' : 'btn-secondary' }}">
            Semua ({{ $counts['total'] }})
        </a>
        <a href="{{ route('operator.staf.index', ['role' => 'superadmin']) }}"
           class="btn btn-sm {{ $role === 'superadmin' ? 'btn-primary' : 'btn-secondary' }}">
            Super Admin ({{ $counts['superadmin'] }})
        </a>
        <a href="{{ route('operator.staf.index', ['role' => 'operator']) }}"
           class="btn btn-sm {{ $role === 'operator' ? 'btn-primary' : 'btn-secondary' }}">
            Staf Operator ({{ $counts['operator'] }})
        </a>
        <a href="{{ route('operator.staf.index', ['role' => 'magang']) }}"
           class="btn btn-sm {{ $role === 'magang' ? 'btn-primary' : 'btn-secondary' }}">
            Anak Magang ({{ $counts['magang'] }})
        </a>
    </div>

    <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('operator.staf.index') }}" class="relative flex-1 md:w-64">
            @if($role)
                <input type="hidden" name="role" value="{{ $role }}">
            @endif
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Cari nama, email, NIP/NIM..."
                   class="form-control text-xs pl-8 pr-3 py-1.5 w-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </form>

        <button type="button" onclick="openModal('modalAddUser')" class="btn btn-sm btn-primary flex items-center gap-1.5 whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pengguna
        </button>
    </div>
</div>

{{-- ─── Data Table ────────────────────────────────────────────── --}}
<div class="dash-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800 text-xs font-semibold text-muted uppercase">
                    <th class="py-3 px-4 w-12">No</th>
                    <th class="py-3 px-4">Nama Lengkap & Email</th>
                    <th class="py-3 px-4">Peran (Role)</th>
                    <th class="py-3 px-4">NIP / NIM</th>
                    <th class="py-3 px-4">Jabatan / Institusi</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                @forelse($users as $index => $u)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="py-3 px-4 text-muted tabular-nums">{{ $users->firstItem() + $index }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs text-white"
                                 style="background: {{ $u->role === 'superadmin' ? '#7C3AED' : ($u->role === 'operator' ? '#2563EB' : '#D97706') }};">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $u->name }}</p>
                                <p class="text-muted text-[11px]">{{ $u->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        @if($u->role === 'superadmin')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> Super Admin
                            </span>
                        @elseif($u->role === 'operator')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Staf Operator
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Anak Magang
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-muted tabular-nums">
                        {{ $u->nip ?? '—' }}
                    </td>
                    <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                        {{ $u->jabatan ?? '—' }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        <button type="button"
                                onclick="toggleUserActive({{ $u->id }}, this)"
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase cursor-pointer transition-colors {{ $u->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' }}">
                            {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            {{-- Reset Password Modal Trigger --}}
                            <button type="button"
                                    onclick="openResetPasswordModal({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                    class="btn btn-secondary btn-icon btn-sm"
                                    title="Reset Kata Sandi">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </button>

                            {{-- Delete User Button --}}
                            <button type="button"
                                    onclick="deleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                    class="btn btn-danger btn-icon btn-sm"
                                    title="Hapus Pengguna">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-muted">
                        Tidak ada data akun pengguna yang sesuai dengan filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="p-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- ─── Modal Tambah Pengguna ──────────────────────────────────── --}}
<div id="modalAddUser" class="modal-backdrop hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div class="dash-card w-full max-w-lg p-6 relative">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-4">
            <div>
                <h3 class="font-bold text-base text-gray-900 dark:text-white">Tambah Akun Pengguna</h3>
                <p class="text-xs text-muted">Daftarkan akun staf baru, operator, atau mahasiswa magang.</p>
            </div>
            <button type="button" onclick="closeModal('modalAddUser')" class="text-muted hover:text-gray-900 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('operator.staf.store') }}">
            @csrf
            <div class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" required placeholder="misal: Wahyu Prasetyo, S.Kom" class="form-control text-xs w-full">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Email Kedinasan <span class="text-danger">*</span></label>
                        <input type="email" name="email" required placeholder="nama@bnnsurabaya.go.id" class="form-control text-xs w-full">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Peran Akun <span class="text-danger">*</span></label>
                        <select name="role" required class="form-control text-xs w-full">
                            <option value="operator">Staf Operator P2M</option>
                            <option value="magang">Anak Magang (Input Kertas)</option>
                            <option value="superadmin">Super Admin (Akses Penuh)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">NIP / NIM</label>
                        <input type="text" name="nip" placeholder="1990xxxx / 0820xxxx" class="form-control text-xs w-full">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Jabatan / Instansi</label>
                        <input type="text" name="jabatan" placeholder="Penyuluh Narkoba / Mahasiswa" class="form-control text-xs w-full">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Kata Sandi Awal <span class="text-danger">*</span></label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" class="form-control text-xs w-full">
                </div>
            </div>

            <div class="mt-6 pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalAddUser')" class="btn btn-sm btn-secondary">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Modal Reset Password ──────────────────────────────────── --}}
<div id="modalResetPassword" class="modal-backdrop hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div class="dash-card w-full max-w-sm p-5 relative">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-3">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white">Reset Kata Sandi</h3>
            <button type="button" onclick="closeModal('modalResetPassword')" class="text-muted hover:text-gray-900 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <p class="text-xs text-muted mb-3" id="resetUserPrompt">Setel ulang kata sandi pengguna terpilih.</p>

        <form id="formResetPassword" onsubmit="submitResetPassword(event)">
            <input type="hidden" id="resetUserId">
            <div class="mb-4">
                <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Kata Sandi Baru</label>
                <input type="password" id="newPasswordInput" required minlength="6" placeholder="Masukkan sandi baru" class="form-control text-xs w-full">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalResetPassword')" class="btn btn-sm btn-secondary">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan Sandi Baru</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openResetPasswordModal(id, name) {
        document.getElementById('resetUserId').value = id;
        document.getElementById('resetUserPrompt').textContent = `Setel ulang kata sandi akun untuk: ${name}`;
        document.getElementById('newPasswordInput').value = '';
        openModal('modalResetPassword');
    }

    async function submitResetPassword(e) {
        e.preventDefault();
        const id = document.getElementById('resetUserId').value;
        const newPassword = document.getElementById('newPasswordInput').value;

        try {
            const res = await fetch(`/operator/staf/${id}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ new_password: newPassword })
            });

            const data = await res.json();
            if (data.success) {
                closeModal('modalResetPassword');
                alert(data.message);
            } else {
                alert('Gagal mereset kata sandi.');
            }
        } catch (err) {
            alert('Terjadi kesalahan jaringan.');
        }
    }

    async function toggleUserActive(id, btn) {
        try {
            const res = await fetch(`/operator/staf/${id}/toggle-active`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await res.json();
            if (data.success) {
                if (data.is_active) {
                    btn.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase cursor-pointer transition-colors bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400';
                    btn.textContent = 'Aktif';
                } else {
                    btn.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase cursor-pointer transition-colors bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400';
                    btn.textContent = 'Nonaktif';
                }
            }
        } catch (err) {
            alert('Gagal mengubah status akun.');
        }
    }

    async function deleteUser(id, name) {
        if (!confirm(`Apakah Anda yakin ingin menghapus akun pengguna "${name}"?`)) return;

        try {
            const res = await fetch(`/operator/staf/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert('Gagal menghapus pengguna.');
            }
        } catch (err) {
            alert('Terjadi kesalahan koneksi.');
        }
    }
</script>
@endpush

@endsection
