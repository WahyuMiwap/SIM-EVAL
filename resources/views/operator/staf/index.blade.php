@extends('layouts.app')

@section('title', 'Tata Kelola Pengguna & Hak Akses')
@section('page-title', 'Tata Kelola Pengguna')
@section('page-subtitle', 'Kelola akun Super Admin, Staf Operator, dan Anak Magang P2M BNN Kota Surabaya')

@section('content')

@php
    $currentRole = auth()->user()?->role ?? 'superadmin';
    $roleLabels = ['superadmin' => 'Super Admin', 'operator' => 'Staf Operator', 'magang' => 'Anak Magang'];
@endphp

{{-- ─── 1. Panel Peran Aktif + Riwayat Aktivitas ──────────────── --}}
<div class="glass rounded-2xl border border-slate-200/70 dark:border-slate-800 p-4 md:p-5 mb-5 flex flex-wrap items-center justify-between gap-4 shadow-xs">
    <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div>
            <div class="text-xs text-slate-500 dark:text-slate-400">
                Akses Aktif: <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $roleLabels[$currentRole] ?? $currentRole }}</span>
                <span class="text-slate-300 dark:text-slate-600 mx-1.5">&middot;</span>
                <span class="text-slate-600 dark:text-slate-300">{{ auth()->user()?->name }}</span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Kelola akun dan hak akses pengguna sistem SIM-EVAL P2M BNN Kota Surabaya.</p>
        </div>
    </div>
    <a href="{{ route('operator.staf.activity') }}" class="btn btn-secondary btn-sm rounded-xl inline-flex items-center gap-1.5 shadow-2xs">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Riwayat Aktivitas</span>
    </a>
</div>

{{-- ─── 2. Row KPI Metrik Akun Pengguna (Slider Looping di Mobile, Grid 4-kolom di Desktop) ─── --}}
<div class="dash-metrics-slider-section mb-5">
    <div class="dash-metrics-viewport" id="stafMetricsViewport">
        <div class="dash-metrics-track" id="stafMetricsTrack">

            {{-- Card 1: Total Akun Terdaftar --}}
            <div class="dash-metric-slide">
                <div class="dash-card dash-metric-card h-full">
                    <div class="flex items-start justify-between">
                        <div class="dash-metric-icon-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="dash-metric-body mt-3">
                        <span class="dash-metric-label">Total Akun Terdaftar</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="dash-stat-number tabular-nums">{{ $counts['total'] }}</h3>
                            <span class="text-xs text-muted font-medium">akun</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Total Super Admin --}}
            <div class="dash-metric-slide">
                <div class="dash-card dash-metric-card h-full">
                    <div class="flex items-start justify-between">
                        <div class="dash-metric-icon-wrap dash-metric-icon--purple">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                    <div class="dash-metric-body mt-3">
                        <span class="dash-metric-label">Super Admin</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="dash-stat-number tabular-nums">{{ $counts['superadmin'] }}</h3>
                            <span class="text-xs text-muted font-medium">pengguna</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Total Staf Operator --}}
            <div class="dash-metric-slide">
                <div class="dash-card dash-metric-card h-full">
                    <div class="flex items-start justify-between">
                        <div class="dash-metric-icon-wrap dash-metric-icon--cyan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="dash-metric-body mt-3">
                        <span class="dash-metric-label">Staf Operator P2M</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="dash-stat-number tabular-nums">{{ $counts['operator'] }}</h3>
                            <span class="text-xs text-muted font-medium">staf</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Total Anak Magang --}}
            <div class="dash-metric-slide">
                <div class="dash-card dash-metric-card h-full">
                    <div class="flex items-start justify-between">
                        <div class="dash-metric-icon-wrap dash-metric-icon--amber">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                    </div>
                    <div class="dash-metric-body mt-3">
                        <span class="dash-metric-label">Anak Magang</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="dash-stat-number tabular-nums">{{ $counts['magang'] }}</h3>
                            <span class="text-xs text-muted font-medium">siswa/mhs</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Carousel Controls (Hanya tampil di mobile/tablet < 1024px) --}}
    <div class="dash-metrics-controls lg:hidden">
        <button type="button" id="btnPrevStafMetric" class="dash-metric-nav-btn" aria-label="Kartu Sebelumnya" title="Sebelumnya">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="dash-metrics-dots" id="stafMetricsDots">
            <button type="button" class="dash-metric-dot active" data-index="0" aria-label="Slide 1"></button>
            <button type="button" class="dash-metric-dot" data-index="1" aria-label="Slide 2"></button>
            <button type="button" class="dash-metric-dot" data-index="2" aria-label="Slide 3"></button>
            <button type="button" class="dash-metric-dot" data-index="3" aria-label="Slide 4"></button>
        </div>

        <button type="button" id="btnNextStafMetric" class="dash-metric-nav-btn" aria-label="Kartu Berikutnya" title="Berikutnya">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>

{{-- ─── 3. Unified Table Card (Soft Rounded-2xl — Seragam dengan Kegiatan) ── --}}
<div class="glass mb-6 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs overflow-visible" style="padding: 0; min-height: 480px;">

    {{-- Card Header Soft --}}
    <div class="kg-header flex items-center justify-between p-4 md:p-5 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 truncate">Daftar Pengguna Sistem</h2>
                <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5 truncate" id="totalCount">{{ $users->total() ?? count($users) }} akun terdaftar &middot; hak akses operasional P2M</p>
            </div>
        </div>
        <button type="button" 
                onclick="openModal('modalAddUser')" 
                class="btn btn-primary rounded-xl shadow-xs inline-flex items-center justify-center gap-1.5 flex-shrink-0 kg-btn-add" 
                id="btnTambahPengguna"
                title="Tambah Pengguna Baru">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="kg-btn-add-label hidden sm:inline">Tambah Pengguna</span>
        </button>
    </div>

    {{-- Filter Bar Soft (Persis Kegiatan) ─────────── --}}
    <div class="kg-filterbar p-3.5 md:p-4 bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 flex-wrap">
        {{-- Search Input with SVG Icon --}}
        <div class="kg-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="kg-search-icon" width="14" height="14" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="stafSearch" type="text" class="kg-search-input rounded-xl border border-slate-200/70 dark:border-slate-700"
                   placeholder="Cari nama pengguna..."
                   value="{{ $search }}">
        </div>

        {{-- Filter Peran Pengguna (Tombol Icon Filter di Sisi Kanan Searchbar) --}}
        <div class="dash-period-dropdown-wrap relative" id="stafRoleWrap">
            <button type="button" 
                    id="stafRoleBtn" 
                    class="kg-filter-icon-btn {{ !empty($role) ? 'active' : '' }}" 
                    aria-haspopup="true" 
                    aria-expanded="false" 
                    title="Filter peran akun">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span id="stafRoleActiveDot" class="kg-filter-active-dot {{ empty($role) ? 'hidden' : '' }}"></span>
            </button>

            {{-- Floating Dropdown Menu --}}
            <div id="stafRoleMenu" class="dash-period-menu hidden" role="menu">
                <div class="dash-period-section-label">Filter Peran Pengguna</div>

                {{-- 0. Semua Peran --}}
                <button type="button" class="dash-period-item role-dropdown-item {{ empty($role) ? 'active' : '' }}" data-role="" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Semua Peran</span>
                        <span class="dash-period-item-sub">Seluruh akun pengguna terdaftar</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary {{ empty($role) ? '' : 'hidden' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                {{-- 1. Super Admin --}}
                <button type="button" class="dash-period-item role-dropdown-item {{ $role === 'superadmin' ? 'active' : '' }}" data-role="superadmin" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Super Admin</span>
                        <span class="dash-period-item-sub">Hak akses penuh & manajemen sistem</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary {{ $role === 'superadmin' ? '' : 'hidden' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                {{-- 2. Staf Operator --}}
                <button type="button" class="dash-period-item role-dropdown-item {{ $role === 'operator' ? 'active' : '' }}" data-role="operator" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Staf Operator</span>
                        <span class="dash-period-item-sub">Operasional kegiatan & entri data P2M</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary {{ $role === 'operator' ? '' : 'hidden' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                {{-- 3. Anak Magang --}}
                <button type="button" class="dash-period-item role-dropdown-item {{ $role === 'magang' ? 'active' : '' }}" data-role="magang" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Anak Magang</span>
                        <span class="dash-period-item-sub">Akses operasional kuesioner & scanner</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary {{ $role === 'magang' ? '' : 'hidden' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Active Filter Chip --}}
        <div class="kg-active-chip {{ (empty($role) && empty($search)) ? 'hidden' : '' }}" id="activeChip">
            <span id="activeChipText"></span>
            <button type="button" id="btnClearFilter" class="kg-chip-clear" aria-label="Hapus filter" title="Reset filter">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Tampilan Desktop: Tabel Data --}}
    <div class="kg-desktop-table-wrap" style="min-height: 380px;">
        <table class="data-table w-full text-left" id="stafTable">
            <thead>
                <tr>
                    <th class="py-3 px-4 w-12 text-center">No</th>
                    <th class="py-3 px-4">Nama Lengkap & Email</th>
                    <th class="py-3 px-4">Peran</th>
                    <th class="py-3 px-4">NIP / NIM</th>
                    <th class="py-3 px-4">Jabatan / Institusi</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4" style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="stafTbody" class="divide-y divide-slate-100 dark:divide-slate-800/70 text-xs">
                @forelse($users as $index => $u)
                <tr class="hover:bg-blue-50/40 dark:hover:bg-slate-800/40 transition-colors"
                    data-search="{{ strtolower($u->name . ' ' . $u->email . ' ' . ($u->nip ?? '') . ' ' . ($u->jabatan ?? '') . ' ' . $u->role . ' ' . ($roleLabels[$u->role] ?? '')) }}"
                    data-role="{{ $u->role }}">
                    <td class="py-3 px-4 text-slate-400 text-center tabular-nums">{{ $users->firstItem() + $index }}</td>
                    <td class="py-3 px-4">
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $u->name }}</p>
                            <p class="text-slate-400 text-[11px] mt-0.5">{{ $u->email }}</p>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        @if($u->role === 'superadmin')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold text-[11px] bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/50">Super Admin</span>
                        @elseif($u->role === 'operator')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold text-[11px] bg-cyan-50 text-cyan-700 dark:bg-cyan-950/60 dark:text-cyan-300 border border-cyan-200/60 dark:border-cyan-800/50">Staf Operator</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold text-[11px] bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/50">Anak Magang</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-slate-500 dark:text-slate-400 tabular-nums">
                        {{ $u->nip ?? '—' }}
                    </td>
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                        {{ $u->jabatan ?? '—' }}
                    </td>
                    <td class="py-3 px-4">
                        <button type="button"
                                onclick="toggleUserActive({{ $u->id }}, this)"
                                class="inline-flex items-center gap-2 text-xs cursor-pointer hover:opacity-75 transition-opacity bg-transparent border-0 p-0"
                                title="Klik untuk ubah status akun">
                            <span class="status-dot w-2 h-2 rounded-full {{ $u->is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }} flex-shrink-0"></span>
                            <span class="status-text font-medium {{ $u->is_active ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-400 dark:text-slate-500' }}">
                                {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </button>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            {{-- Edit Akun --}}
                            <button type="button"
                                    onclick="openEditUserModal({{ $u->id }})"
                                    class="btn btn-secondary btn-icon rounded-xl"
                                    style="width: 2rem; height: 2rem;"
                                    title="Edit Akun">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>

                            {{-- Reset Password Modal Trigger --}}
                            <button type="button"
                                    onclick="openResetPasswordModal({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                    class="btn btn-secondary btn-icon rounded-xl"
                                    style="width: 2rem; height: 2rem;"
                                    title="Reset Kata Sandi">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </button>

                            {{-- Delete User Button --}}
                            <button type="button"
                                    onclick="deleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                    class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 border-slate-200/70 dark:border-slate-700"
                                    style="width: 2rem; height: 2rem;"
                                    title="Hapus Pengguna">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="stafEmptyDefaultRow">
                    <td colspan="7" class="py-8 text-center text-slate-400">
                        Tidak ada data akun pengguna yang sesuai dengan filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tampilan Mobile / PWA: List Card Interaktif (<= 768px) --}}
    <div class="kg-mobile-list divide-y divide-slate-100 dark:divide-slate-800" id="stafMobileList">
        @forelse($users as $index => $u)
        <div class="p-4 hover:bg-blue-50/30 dark:hover:bg-slate-800/30 transition-colors"
             data-search="{{ strtolower($u->name . ' ' . $u->email . ' ' . ($u->nip ?? '') . ' ' . ($u->jabatan ?? '') . ' ' . $u->role . ' ' . ($roleLabels[$u->role] ?? '')) }}"
             data-role="{{ $u->role }}">
            
            {{-- Top: User info & Status Toggle --}}
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-primary font-bold text-xs flex items-center justify-center flex-shrink-0 border border-blue-200/50 dark:border-blue-900/50">
                        {{ strtoupper(substr($u->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-snug truncate">
                            {{ $u->name }}
                        </h3>
                        <p class="text-slate-400 text-xs truncate mt-0.5">{{ $u->email }}</p>
                    </div>
                </div>

                {{-- Status toggle button --}}
                <button type="button"
                        onclick="toggleUserActive({{ $u->id }}, this)"
                        class="inline-flex items-center gap-1.5 text-xs cursor-pointer hover:opacity-75 transition-opacity bg-transparent border-0 p-1 rounded-md flex-shrink-0"
                        title="Klik untuk ubah status akun">
                    <span class="status-dot w-2 h-2 rounded-full {{ $u->is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }} flex-shrink-0"></span>
                    <span class="status-text font-medium {{ $u->is_active ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-400 dark:text-slate-500' }}">
                        {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </button>
            </div>

            {{-- Meta Row: Peran Badge, NIP/NIM, Jabatan --}}
            <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                @if($u->role === 'superadmin')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold text-[11px] bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/50">Super Admin</span>
                @elseif($u->role === 'operator')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold text-[11px] bg-cyan-50 text-cyan-700 dark:bg-cyan-950/60 dark:text-cyan-300 border border-cyan-200/60 dark:border-cyan-800/50">Staf Operator</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold text-[11px] bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/50">Anak Magang</span>
                @endif

                @if($u->nip)
                    <span class="text-slate-300 dark:text-slate-600 text-[11px]">&middot;</span>
                    <span class="text-slate-500 dark:text-slate-400 text-[11px] tabular-nums">NIP/NIM: {{ $u->nip }}</span>
                @endif

                @if($u->jabatan)
                    <span class="text-slate-300 dark:text-slate-600 text-[11px]">&middot;</span>
                    <span class="text-slate-500 dark:text-slate-400 text-[11px]">{{ $u->jabatan }}</span>
                @endif
            </div>

            {{-- Actions Bar --}}
            <div class="flex items-center justify-end gap-1.5 mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/60">
                <button type="button"
                        onclick="openEditUserModal({{ $u->id }})"
                        class="btn btn-secondary btn-sm rounded-xl inline-flex items-center gap-1.5 text-xs py-1 px-2.5"
                        title="Edit Akun">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit</span>
                </button>

                <button type="button"
                        onclick="openResetPasswordModal({{ $u->id }}, '{{ addslashes($u->name) }}')"
                        class="btn btn-secondary btn-sm rounded-xl inline-flex items-center gap-1.5 text-xs py-1 px-2.5"
                        title="Reset Kata Sandi">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Reset Sandi</span>
                </button>

                <button type="button"
                        onclick="deleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                        class="btn btn-secondary btn-sm rounded-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 border-slate-200/70 dark:border-slate-700 inline-flex items-center gap-1.5 text-xs py-1 px-2.5"
                        title="Hapus Pengguna">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus</span>
                </button>
            </div>
        </div>
        @empty
        <div class="py-8 text-center text-slate-400" id="stafMobileEmptyDefault">
            Tidak ada data akun pengguna yang sesuai dengan filter.
        </div>
        @endforelse
    </div>

    @if($users->hasPages())
    <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- ─── 4. Modal Tambah Pengguna ───────────────────────────────── --}}
<div id="modalAddUser" class="modal-backdrop hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div class="w-full max-w-lg p-6 relative rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100">Tambah Akun Pengguna</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftarkan akun staf baru, operator, atau mahasiswa magang.</p>
            </div>
            <button type="button" onclick="closeModal('modalAddUser')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('operator.staf.store') }}">
            @csrf
            <div class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="misal: Wahyu Prasetyo, S.Kom" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Email Kedinasan <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="nama@bnnsurabaya.go.id" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Peran Akun <span class="text-rose-500">*</span></label>
                        <select name="role" required class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                            <option value="operator">Staf Operator P2M</option>
                            <option value="magang">Anak Magang (Input Kertas)</option>
                            <option value="superadmin">Super Admin (Akses Penuh)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">NIP / NIM</label>
                        <input type="text" name="nip" placeholder="1990xxxx / 0820xxxx" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Jabatan / Instansi</label>
                        <input type="text" name="jabatan" placeholder="Penyuluh Narkoba / Mahasiswa" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Kata Sandi Awal <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                </div>
            </div>

            <div class="mt-6 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalAddUser')" class="btn btn-sm btn-secondary rounded-xl">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-xl shadow-xs">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── 5. Modal Reset Password ───────────────────────────────── --}}
<div id="modalResetPassword" class="modal-backdrop hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div class="w-full max-w-sm p-5 relative rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
            <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100">Reset Kata Sandi</h3>
            <button type="button" onclick="closeModal('modalResetPassword')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <p class="text-xs text-slate-400 mb-3" id="resetUserPrompt">Setel ulang kata sandi pengguna terpilih.</p>

        <form id="formResetPassword" onsubmit="submitResetPassword(event)">
            <input type="hidden" id="resetUserId">
            <div class="mb-4">
                <label class="block text-xs font-semibold mb-1 text-slate-700 dark:text-slate-300">Kata Sandi Baru</label>
                <input type="password" id="newPasswordInput" required minlength="6" placeholder="Masukkan sandi baru" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalResetPassword')" class="btn btn-sm btn-secondary rounded-xl">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-xl shadow-xs">Simpan Sandi Baru</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── 5b. Modal Edit Pengguna ───────────────────────────────── --}}
<div id="modalEditUser" class="modal-backdrop hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div class="w-full max-w-lg p-6 relative rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100">Edit Akun Pengguna</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui data dan peran akun.</p>
            </div>
            <button type="button" onclick="closeModal('modalEditUser')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="formEditUser" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="editUserName" required maxlength="100" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="editUserEmail" required maxlength="255" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Peran <span class="text-rose-500">*</span></label>
                        <select name="role" id="editUserRole" required class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                            <option value="operator">Staf Operator</option>
                            <option value="magang">Anak Magang</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">NIP / NIM</label>
                        <input type="text" name="nip" id="editUserNip" maxlength="50" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Jabatan</label>
                        <input type="text" name="jabatan" id="editUserJabatan" maxlength="100" class="form-input w-full rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalEditUser')" class="btn btn-sm btn-secondary rounded-xl">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-xl shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Styles Scoped Standar SIM-EVAL ────────────────────────── --}}
<style>
/* ── Search Input Standar ───────────────────────────────────── */
.kg-search-wrap {
    position: relative;
    flex: 0 0 auto;
}
.kg-search-icon {
    position: absolute;
    left: 0.65rem;
    top: 50%;
    transform: translateY(-50%);
    width: 14px;
    height: 14px;
    color: var(--text-muted);
    pointer-events: none;
}
.kg-search-input {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    padding: 0.375rem 0.75rem 0.375rem 2.125rem;
    font-size: 0.8125rem;
    color: var(--text-primary);
    font-family: var(--font-sans);
    outline: none;
    width: 220px;
    transition: border-color 0.18s ease, box-shadow 0.18s ease;
}
.kg-search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67,97,238,0.08);
}
.kg-search-input::placeholder { color: var(--text-xmuted); }

/* ── Card & Metric Standard (Persis Dashboard) ──────────────── */
.dash-card {
    background: var(--surface) !important;
    border: 1px solid var(--border) !important;
    border-radius: var(--r-xl) !important;
    padding: 1.25rem;
    box-shadow: var(--shadow-xs);
    transition: border-color 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.dash-card:hover {
    border-color: var(--border-strong) !important;
    box-shadow: var(--shadow-sm);
}
.dash-metric-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1.15rem 1.25rem;
}
.dash-metric-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: var(--r-md);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: var(--primary-light);
    color: var(--primary);
}
.dash-metric-icon--cyan {
    background: rgba(6, 182, 212, 0.12);
    color: #06b6d4;
}
.dash-metric-icon--emerald {
    background: rgba(34, 197, 94, 0.12);
    color: #22c55e;
}
.dash-metric-icon--purple {
    background: rgba(168, 85, 247, 0.12);
    color: #a855f7;
}
.dash-metric-icon--amber {
    background: rgba(245, 158, 11, 0.12);
    color: #f59e0b;
}
.dash-metric-label {
    font-family: var(--font-sans);
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
    letter-spacing: 0.01em;
}
.dash-stat-number {
    font-family: var(--font-display);
    font-size: 1.85rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--text-primary);
    line-height: 1;
}

/* ── Metric Cards Carousel / Grid Responsive (Persis Dashboard) ── */
.dash-metrics-slider-section {
    position: relative;
}

@media (min-width: 1024px) {
    .dash-metrics-viewport {
        overflow: visible;
    }
    .dash-metrics-track {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        transform: none !important;
        transition: none !important;
        width: 100% !important;
    }
    .dash-metric-slide {
        width: 100%;
        min-width: 0;
    }
    .dash-metrics-controls {
        display: none !important;
    }
}

@media (max-width: 1023px) {
    .dash-metrics-viewport {
        overflow: hidden;
        width: 100%;
        position: relative;
        border-radius: var(--r-xl);
        touch-action: pan-y pinch-zoom;
        user-select: none;
        cursor: grab;
    }
    .dash-metrics-viewport:active {
        cursor: grabbing;
    }
    .dash-metrics-track {
        display: flex;
        width: 100%;
        transition: transform 0.35s cubic-bezier(0.25, 1, 0.5, 1);
        will-change: transform;
    }
    .dash-metric-slide {
        flex: 0 0 100%;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    .dash-metrics-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-top: 0.75rem;
    }
    .dash-metric-nav-btn {
        width: 32px;
        height: 32px;
        border-radius: var(--r-full);
        background: var(--surface);
        border: 1.5px solid var(--border);
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: var(--shadow-xs);
        transition: all 0.2s ease;
    }
    .dash-metric-nav-btn:hover {
        background: var(--primary-light);
        color: var(--primary);
        border-color: var(--primary);
    }
    .dash-metrics-dots {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .dash-metric-dot {
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background: var(--border-strong);
        border: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dash-metric-dot.active {
        width: 24px;
        background: var(--primary);
        box-shadow: 0 0 8px rgba(67, 97, 238, 0.4);
    }
}

/* ── Filter Icon Button & Dropdown Menu (Persis Kegiatan) ───── */
.kg-filter-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    width: 2.25rem;
    height: 2.25rem;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    color: var(--text-secondary);
    cursor: pointer;
    box-shadow: var(--shadow-xs);
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}
.kg-filter-icon-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--bg-alt);
    box-shadow: var(--shadow-sm);
}
.kg-filter-icon-btn.active,
.kg-filter-icon-btn:focus-visible {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-light);
}
.kg-filter-active-dot {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 7px;
    height: 7px;
    border-radius: 9999px;
    background-color: var(--primary);
    border: 1.5px solid var(--surface);
}
.kg-filter-active-dot.hidden {
    display: none !important;
}

.dash-period-menu {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    width: 275px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    box-shadow: var(--shadow-xl), 0 12px 28px -4px rgba(0, 0, 0, 0.14);
    z-index: 50;
    padding: 0.4rem;
    animation: periodDropdownIn 0.16s ease-out;
}
.dash-period-menu.hidden {
    display: none !important;
}
@keyframes periodDropdownIn {
    from { opacity: 0; transform: translateY(-4px); }
    to   { opacity: 1; transform: translateY(0); }
}
.dash-period-section-label {
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    padding: 0.35rem 0.6rem 0.25rem;
}
.dash-period-item {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.65rem;
    border-radius: var(--r-md);
    border: none;
    background: transparent;
    cursor: pointer;
    transition: background 0.15s ease;
    text-decoration: none;
    margin-bottom: 2px;
}
.dash-period-item:hover {
    background: var(--bg-alt);
}
.dash-period-item.active {
    background: var(--primary-light);
}
.dash-period-item.active .dash-period-item-title {
    color: var(--primary);
    font-weight: 600;
}
.dash-period-item.active .dash-period-check {
    display: block !important;
}
.dash-period-item:not(.active) .dash-period-check {
    display: none !important;
}
.dash-period-item-title {
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--text-primary);
}
.dash-period-item-sub {
    font-size: 0.72rem;
    color: var(--text-muted);
}

/* ── Active Filter Chip ─────────────────────────────────────── */
.kg-active-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: var(--primary-light);
    color: var(--primary);
    border-radius: var(--r-full);
    padding: 0.2rem 0.6rem;
    font-size: 0.75rem;
    font-weight: 600;
}
.kg-active-chip.hidden { display: none !important; }
.kg-chip-clear {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    color: var(--primary);
    padding: 0;
    opacity: 0.7;
    transition: opacity 0.15s;
}
.kg-chip-clear:hover { opacity: 1; }

/* ── Desktop Table vs Mobile Card Visibility ───────────────── */
.kg-desktop-table-wrap {
    display: block;
    overflow-x: auto;
}
.kg-mobile-list {
    display: none;
}
@media (max-width: 768px) {
    .kg-desktop-table-wrap {
        display: none !important;
    }
    .kg-mobile-list {
        display: block !important;
    }
}

/* ── Mobile PWA Responsive Tweaks ──────────────────────────── */
@media (max-width: 640px) {
    .kg-header {
        padding: 0.75rem 0.875rem;
    }
    .kg-btn-add {
        width: 2.125rem !important;
        height: 2.125rem !important;
        padding: 0 !important;
        border-radius: var(--r-md);
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .kg-btn-add-label {
        display: none !important;
    }
    .kg-filterbar {
        padding: 0.65rem 0.875rem;
        gap: 0.5rem;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
    .kg-search-wrap {
        flex: 1 1 auto;
        min-width: 0;
        width: auto;
    }
    .kg-search-input {
        width: 100% !important;
    }
    .dash-period-dropdown-wrap {
        flex-shrink: 0;
    }
    .dash-period-menu {
        right: 0;
        left: auto;
        width: 275px;
        max-width: calc(100vw - 2rem);
    }
}
</style>

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

    async function openEditUserModal(id) {
        try {
            const res = await fetch(`/operator/staf/${id}/edit`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error();
            const u = await res.json();
            document.getElementById('formEditUser').action = `/operator/staf/${u.id}`;
            document.getElementById('editUserName').value = u.name || '';
            document.getElementById('editUserEmail').value = u.email || '';
            document.getElementById('editUserRole').value = u.role || 'operator';
            document.getElementById('editUserNip').value = u.nip || '';
            document.getElementById('editUserJabatan').value = u.jabatan || '';
            openModal('modalEditUser');
        } catch (err) {
            alert('Gagal memuat data akun.');
        }
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
                alert(data.message || 'Gagal mereset kata sandi.');
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
                const dot = btn.querySelector('.status-dot');
                const text = btn.querySelector('.status-text');
                if (data.is_active) {
                    if (dot) dot.className = 'status-dot w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0';
                    if (text) {
                        text.className = 'status-text font-medium text-emerald-600 dark:text-emerald-400 font-semibold';
                        text.textContent = 'Aktif';
                    }
                } else {
                    if (dot) dot.className = 'status-dot w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600 flex-shrink-0';
                    if (text) {
                        text.className = 'status-text font-medium text-slate-400 dark:text-slate-500';
                        text.textContent = 'Nonaktif';
                    }
                }
            } else {
                alert(data.message || 'Gagal mengubah status akun.');
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
                alert(data.message || 'Gagal menghapus pengguna.');
            }
        } catch (err) {
            alert('Terjadi kesalahan koneksi.');
        }
    }

    // ── Live In-Place Instant Search & Role Filter with Dropdown (Persis Kegiatan) ─────
    (function () {
        const searchInput       = document.getElementById('stafSearch');
        const roleWrap          = document.getElementById('stafRoleWrap');
        const roleBtn           = document.getElementById('stafRoleBtn');
        const roleMenu          = document.getElementById('stafRoleMenu');
        const roleActiveDot     = document.getElementById('stafRoleActiveDot');
        const roleDropdownItems = document.querySelectorAll('.role-dropdown-item');
        const activeChip        = document.getElementById('activeChip');
        const activeChipText    = document.getElementById('activeChipText');
        const btnClearFilter    = document.getElementById('btnClearFilter');
        const totalCountEl      = document.getElementById('totalCount');

        let currentSearch = @json($search ?? '');
        let currentRole   = @json($role ?? '');

        const roleLabelMap = {
            'superadmin': 'Super Admin',
            'operator':   'Staf Operator',
            'magang':     'Anak Magang'
        };

        function updateActiveChip() {
            if (!activeChip || !activeChipText) return;
            const chips = [];
            if (currentRole && roleLabelMap[currentRole]) {
                chips.push(`Peran: ${roleLabelMap[currentRole]}`);
            }
            if (currentSearch) {
                chips.push(`"${currentSearch}"`);
            }
            if (chips.length > 0) {
                activeChipText.textContent = chips.join(' \u00B7 ');
                activeChip.classList.remove('hidden');
            } else {
                activeChip.classList.add('hidden');
            }
        }

        function updateRoleButtonUI() {
            const isFiltered = currentRole !== '';
            roleBtn?.classList.toggle('active', isFiltered);
            roleActiveDot?.classList.toggle('hidden', !isFiltered);

            roleDropdownItems.forEach(i => {
                const isActive = (i.getAttribute('data-role') || '') === currentRole;
                i.classList.toggle('active', isActive);
                const check = i.querySelector('.dash-period-check');
                if (check) check.classList.toggle('hidden', !isActive);
            });
        }

        function applyFilter() {
            const prevScrollY = window.scrollY;
            
            // 1. Filter baris tabel desktop
            const tableRows = document.querySelectorAll('#stafTbody tr[data-search]');
            // 2. Filter kartu mobile
            const mobileCards = document.querySelectorAll('#stafMobileList > div[data-search]');
            
            let visibleCount = 0;

            const checkMatch = (el) => {
                const searchData = (el.getAttribute('data-search') || '').toLowerCase();
                const roleData   = (el.getAttribute('data-role') || '').toLowerCase();
                const matchSearch = !currentSearch || searchData.includes(currentSearch);
                const matchRole   = !currentRole || roleData === currentRole;
                return matchSearch && matchRole;
            };

            tableRows.forEach(row => {
                if (checkMatch(row)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            mobileCards.forEach(card => {
                if (checkMatch(card)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            if (totalCountEl) {
                totalCountEl.textContent = `${visibleCount} akun ditemukan \u00B7 hak akses operasional P2M`;
            }

            // Empty state pada tabel desktop
            let noResultsRow = document.getElementById('stafNoResultsRow');
            if (visibleCount === 0 && tableRows.length > 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'stafNoResultsRow';
                    noResultsRow.innerHTML = `
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Tidak ada akun pengguna yang cocok</p>
                            <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian atau filter peran lain</p>
                            <button type="button" id="btnResetStafFilter" class="btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary shadow-2xs">
                                Reset Filter Pencarian
                            </button>
                        </td>
                    `;
                    document.getElementById('stafTbody')?.appendChild(noResultsRow);
                    document.getElementById('btnResetStafFilter')?.addEventListener('click', resetAllFilters);
                } else {
                    noResultsRow.style.display = '';
                }
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }

            // Empty state pada mobile list
            let noResultsMobile = document.getElementById('stafNoResultsMobile');
            if (visibleCount === 0 && mobileCards.length > 0) {
                if (!noResultsMobile) {
                    noResultsMobile = document.createElement('div');
                    noResultsMobile.id = 'stafNoResultsMobile';
                    noResultsMobile.className = 'py-12 text-center text-slate-400 p-4';
                    noResultsMobile.innerHTML = `
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Tidak ada akun yang cocok</p>
                        <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian atau filter peran lain</p>
                        <button type="button" id="btnResetStafFilterMobile" class="btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary shadow-2xs">
                            Reset Filter Pencarian
                        </button>
                    `;
                    document.getElementById('stafMobileList')?.appendChild(noResultsMobile);
                    document.getElementById('btnResetStafFilterMobile')?.addEventListener('click', resetAllFilters);
                } else {
                    noResultsMobile.style.display = '';
                }
            } else if (noResultsMobile) {
                noResultsMobile.style.display = 'none';
            }

            updateRoleButtonUI();
            updateActiveChip();

            if (prevScrollY > 0) {
                requestAnimationFrame(() => {
                    const maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
                    if (window.scrollY !== prevScrollY && prevScrollY <= maxScroll) {
                        window.scrollTo({ top: prevScrollY, behavior: 'instant' });
                    }
                });
            }
        }

        function resetAllFilters() {
            currentSearch = '';
            currentRole   = '';
            if (searchInput) searchInput.value = '';
            updateRoleButtonUI();
            applyFilter();
        }

        // ── Search Input: Instant Live Filter ────────────────────────
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                currentSearch = this.value.trim().toLowerCase();
                applyFilter();
            });
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        }

        // ── Dropdown Toggle & Close Listeners ────────────────────────
        roleBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = roleMenu?.classList.contains('hidden');
            roleMenu?.classList.toggle('hidden', !isHidden);
            roleBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });

        document.addEventListener('click', function(e) {
            if (roleMenu && !roleMenu.classList.contains('hidden')) {
                if (!roleWrap?.contains(e.target)) {
                    roleMenu.classList.add('hidden');
                    roleBtn?.setAttribute('aria-expanded', 'false');
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && roleMenu && !roleMenu.classList.contains('hidden')) {
                roleMenu.classList.add('hidden');
                roleBtn?.setAttribute('aria-expanded', 'false');
            }
        });

        // ── Dropdown Item Selection ──────────────────────────────────
        roleDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                currentRole = this.getAttribute('data-role') || '';
                roleMenu?.classList.add('hidden');
                roleBtn?.setAttribute('aria-expanded', 'false');
                applyFilter();
            });
        });

        btnClearFilter?.addEventListener('click', resetAllFilters);

        // Inisialisasi awal jika ada filter aktif
        if (currentSearch || currentRole) {
            applyFilter();
        } else {
            updateRoleButtonUI();
        }

        // ── Carousel Kartu Metrik dengan Looping (Mobile & Tablet) ──
        let currentMetricIndex = 0;
        const totalMetricSlides = 4;
        const metricTrack = document.getElementById('stafMetricsTrack');
        const metricViewport = document.getElementById('stafMetricsViewport');
        const metricDots = document.querySelectorAll('#stafMetricsDots .dash-metric-dot');
        const btnPrevMetric = document.getElementById('btnPrevStafMetric');
        const btnNextMetric = document.getElementById('btnNextStafMetric');

        function goToMetricSlide(index) {
            if (window.innerWidth >= 1024) return;
            // Looping: metok ke kanan balik ke slide 0, metok ke kiri balik ke slide 3
            currentMetricIndex = ((index % totalMetricSlides) + totalMetricSlides) % totalMetricSlides;
            if (metricTrack) {
                metricTrack.style.transform = `translateX(-${currentMetricIndex * 100}%)`;
            }
            metricDots.forEach((dot, idx) => {
                dot.classList.toggle('active', idx === currentMetricIndex);
            });
        }

        btnNextMetric?.addEventListener('click', () => {
            goToMetricSlide(currentMetricIndex + 1);
        });

        btnPrevMetric?.addEventListener('click', () => {
            goToMetricSlide(currentMetricIndex - 1);
        });

        metricDots.forEach((dot) => {
            dot.addEventListener('click', function() {
                const idx = parseInt(this.getAttribute('data-index'), 10);
                goToMetricSlide(idx);
            });
        });

        // Touch swipe gestures with looping
        let touchStartX = 0;
        let touchStartY = 0;
        let isTouchDragging = false;

        metricViewport?.addEventListener('touchstart', (e) => {
            if (window.innerWidth >= 1024) return;
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            isTouchDragging = true;
        }, { passive: true });

        metricViewport?.addEventListener('touchend', (e) => {
            if (!isTouchDragging || window.innerWidth >= 1024) return;
            isTouchDragging = false;
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            const diffX = touchEndX - touchStartX;
            const diffY = touchEndY - touchStartY;

            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 35) {
                if (diffX < 0) {
                    goToMetricSlide(currentMetricIndex + 1);
                } else {
                    goToMetricSlide(currentMetricIndex - 1);
                }
            }
        }, { passive: true });

        // Mouse drag simulation (berguna saat inspect device di desktop)
        let isMouseDown = false;
        let mouseStartX = 0;

        metricViewport?.addEventListener('mousedown', (e) => {
            if (window.innerWidth >= 1024) return;
            isMouseDown = true;
            mouseStartX = e.clientX;
        });

        window.addEventListener('mouseup', (e) => {
            if (!isMouseDown) return;
            isMouseDown = false;
            const diffX = e.clientX - mouseStartX;
            if (Math.abs(diffX) > 35) {
                if (diffX < 0) {
                    goToMetricSlide(currentMetricIndex + 1);
                } else {
                    goToMetricSlide(currentMetricIndex - 1);
                }
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                if (metricTrack) metricTrack.style.transform = '';
            } else {
                goToMetricSlide(currentMetricIndex);
            }
        });
    })();
</script>
@endpush

@endsection
