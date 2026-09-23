@extends('layouts.app')

@section('title', 'Daftar Kegiatan')
@section('page-title', 'Daftar Kegiatan')
@section('page-subtitle', 'Kelola seluruh kegiatan sosialisasi P2M')

@php
    $filters  = $filters  ?? ['search' => '', 'mode' => '', 'period' => 'all', 'dateFrom' => '', 'dateTo' => ''];
    $fSearch  = $filters['search']   ?? '';
    $fMode    = $filters['mode']     ?? '';
    $fPeriod  = $filters['period']   ?? 'all';
    $fFrom    = $filters['dateFrom'] ?? '';
    $fTo      = $filters['dateTo']   ?? '';
    $periodLabels = [
        'all'  => 'Semua Waktu', '1d' => 'Hari Ini', '7d' => '7 Hari Terakhir',
        '30d'  => '30 Hari Terakhir', '90d' => '3 Bulan Terakhir',
        '180d' => '6 Bulan Terakhir', '365d' => '1 Tahun Terakhir', 'month' => 'Bulan Ini', 'custom' => 'Rentang Kustom',
    ];

    $displayLabel = 'Semua Waktu';
    if (($fPeriod === 'custom' || $fFrom || $fTo) && ($fFrom || $fTo)) {
        if ($fFrom && $fTo) {
            $fromFormatted = \Carbon\Carbon::parse($fFrom)->translatedFormat('d M Y');
            $toFormatted   = \Carbon\Carbon::parse($fTo)->translatedFormat('d M Y');
            $displayLabel  = ($fFrom === $fTo) ? $fromFormatted : "{$fromFormatted} – {$toFormatted}";
        } elseif ($fFrom) {
            $displayLabel = 'Mulai ' . \Carbon\Carbon::parse($fFrom)->translatedFormat('d M Y');
        } elseif ($fTo) {
            $displayLabel = 'Sampai ' . \Carbon\Carbon::parse($fTo)->translatedFormat('d M Y');
        }
    } elseif (isset($periodLabels[$fPeriod]) && $fPeriod !== 'all') {
        $displayLabel = $periodLabels[$fPeriod];
    }
@endphp

@section('content')



{{-- ── Table Card (Soft Rounded-2xl) ────────────────────────── --}}
<div class="glass mb-6 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs overflow-visible" style="padding: 0;">

    {{-- Card Header Soft --}}
    <div class="kg-header flex items-center justify-between p-3.5 sm:p-4 md:p-5 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 truncate">Semua Kegiatan Sosialisasi</h2>
                <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5 truncate" id="totalCount">{{ $kegiatan->total() ?? 0 }} kegiatan ditemukan</p>
            </div>
        </div>
        <a href="{{ route('operator.kegiatan.create') }}" 
           class="btn btn-primary rounded-xl shadow-xs inline-flex items-center justify-center gap-1.5 flex-shrink-0 kg-btn-add" 
           id="btnTambahKegiatan"
           title="Tambah Kegiatan Baru">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="kg-btn-add-label hidden sm:inline">Tambah Kegiatan</span>
        </a>
    </div>

    {{-- ── Filter Bar Soft ────────────────────────────────────── --}}
    <div class="kg-filterbar p-3.5 md:p-4 bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 flex-wrap">
        {{-- Search --}}
        <div class="kg-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="kg-search-icon" width="14" height="14" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="kgSearch" type="text" class="kg-search-input rounded-xl border border-slate-200/70 dark:border-slate-700"
                   placeholder="Cari nama kegiatan..."
                   value="{{ $fSearch }}">
        </div>

        {{-- Filter Periode Waktu (Tombol Icon Filter di Sisi Kanan Searchbar) --}}
        <div class="dash-period-dropdown-wrap relative" id="kgPeriodWrap">
            <button type="button" 
                    id="kgPeriodBtn" 
                    class="kg-filter-icon-btn" 
                    aria-haspopup="true" 
                    aria-expanded="false" 
                    title="Filter rentang waktu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span id="kgPeriodActiveDot" class="kg-filter-active-dot hidden"></span>
            </button>

            {{-- Floating Dropdown Menu --}}
            <div id="kgPeriodMenu" class="dash-period-menu hidden" role="menu">
                <div class="dash-period-section-label">Pilihan Cepat</div>

                {{-- 0. Semua Waktu --}}
                <button type="button" class="dash-period-item active" id="optSemuaWaktu" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Semua Waktu</span>
                        <span class="dash-period-item-sub">Seluruh riwayat kegiatan</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                {{-- 1. Bulan Ini --}}
                <button type="button" class="dash-period-item" id="optBulanIni" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Bulan Ini</span>
                        <span class="dash-period-item-sub">{{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                {{-- 2. Bulan Lalu --}}
                @php
                    $prevMonthCarbon = \Carbon\Carbon::now()->subMonth();
                @endphp
                <button type="button" class="dash-period-item" id="optBulanLalu" role="menuitem" data-month="{{ $prevMonthCarbon->month - 1 }}" data-year="{{ $prevMonthCarbon->year }}">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Bulan Lalu</span>
                        <span class="dash-period-item-sub">{{ $prevMonthCarbon->isoFormat('MMMM Y') }}</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                {{-- 3. Tahun Ini --}}
                <button type="button" class="dash-period-item" id="optTahunIni" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Tahun Ini</span>
                        <span class="dash-period-item-sub">Kegiatan tahun {{ date('Y') }}</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                <div class="dash-period-divider"></div>

                {{-- 4. Pilih Bulan & Tahun Spesifik (Collapsible Form) --}}
                <button type="button" class="dash-period-custom-toggle" id="btnToggleCustomPeriod">
                    <span class="flex items-center gap-2 text-xs font-semibold" style="color: var(--text-secondary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        Pilih Bulan &amp; Tahun Lain
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" id="customPeriodCaret" class="w-3 h-3 opacity-60 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="customPeriodForm" class="dash-period-custom-form hidden">
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <div>
                            <label class="text-[11px] font-semibold block mb-1" style="color: var(--text-muted);">Bulan</label>
                            <select id="selCustomMonth" class="form-input text-xs py-1 px-2" style="height: 32px; padding-top: 2px; padding-bottom: 2px;">
                                @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $idx => $mName)
                                    <option value="{{ $idx }}" {{ date('n') == ($idx + 1) ? 'selected' : '' }}>{{ $mName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold block mb-1" style="color: var(--text-muted);">Tahun</label>
                            <select id="selCustomYear" class="form-input text-xs py-1 px-2" style="height: 32px; padding-top: 2px; padding-bottom: 2px;">
                                @for($y = (int)date('Y') + 1; $y >= 2023; $y--)
                                    <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="button" id="btnApplyCustom" class="btn btn-primary btn-sm w-full mt-2.5 text-xs py-1.5 font-medium">
                        Terapkan Periode
                    </button>
                </div>
            </div>
        </div>

        {{-- Active filter chip --}}
        <div class="kg-active-chip hidden" id="activeChip">
            <span id="activeChipText">Semua Waktu</span>
            <button id="btnClearPeriod" class="kg-chip-clear" aria-label="Hapus filter tanggal" title="Reset filter">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Table Soft ─────────────────────────────────────────── --}}
    @if($kegiatan->isEmpty())
    <div class="empty-state py-12 text-center">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="font-semibold text-sm text-slate-700 dark:text-slate-200">Belum ada kegiatan ditemukan</p>
        <p class="text-xs text-slate-400 mt-0.5">Tambahkan kegiatan pertama untuk mulai melakukan monitoring evaluasi</p>
        <a href="{{ route('operator.kegiatan.create') }}" class="btn btn-primary btn-sm rounded-xl mt-3 shadow-xs inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Kegiatan Baru</span>
        </a>
    </div>
    @else
    {{-- Tampilan Desktop: Tabel Data --}}
    <div class="kg-desktop-table-wrap">
        <table class="data-table w-full text-left" id="kgTable">
            <thead>
                <tr>
                    <th class="py-3 px-4">Nama Kegiatan & Sesi</th>
                    <th class="py-3 px-4">Lokasi Binaan</th>
                    <th class="py-3 px-4">Tanggal</th>
                    <th class="py-3 px-4">Peserta</th>
                    <th class="py-3 px-4" style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="kgTbody" class="divide-y divide-slate-100 dark:divide-slate-800/70">
            @foreach($kegiatan as $k)
            <tr data-tanggal="{{ $k->tanggal ?? '' }}"
                data-nama="{{ strtolower($k->nama_kegiatan) }} {{ strtolower($k->lokasi->nama_lokasi ?? '') }} {{ strtolower($k->kode_join ?? '') }}"
                class="hover:bg-blue-50/40 dark:hover:bg-slate-800/40 transition-colors cursor-pointer group"
                onclick="window.location='{{ route('operator.kegiatan.detail', $k->id) }}'"
                title="Klik untuk membuka detail kegiatan ini">
                {{-- Nama & PIN Sesi --}}
                <td class="py-3.5 px-4">
                    <span class="font-semibold text-slate-800 dark:text-slate-100 group-hover:text-primary transition-colors block text-sm">
                        {{ $k->nama_kegiatan }}
                    </span>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="text-[11px] text-slate-400">PIN Sesi:</span>
                        <span class="text-xs font-mono font-bold tracking-wider text-blue-600 dark:text-blue-400">
                            {{ $k->kode_join ?? '—' }}
                        </span>
                    </div>
                </td>

                {{-- Lokasi Binaan --}}
                <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $k->lokasi->nama_lokasi ?? '—' }}</span>
                    </div>
                </td>

                {{-- Tanggal Pelaksanaan --}}
                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ isset($k->tanggal) ? \Carbon\Carbon::parse($k->tanggal)->isoFormat('D MMMM Y') : '—' }}</span>
                    </div>
                </td>

                {{-- Jumlah Peserta --}}
                <td class="py-3.5 px-4 text-xs font-semibold text-slate-700 dark:text-slate-200">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>{{ $k->peserta_count ?? 0 }} Siswa</span>
                    </div>
                </td>

                {{-- Aksi: Edit + Hapus (Stop propagation agar klik tombol tidak memicu navigasi baris) --}}
                <td class="py-3.5 px-4" onclick="event.stopPropagation()">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{ route('operator.kegiatan.edit', $k->id) }}"
                           class="btn btn-secondary btn-icon rounded-xl" style="width: 2.125rem; height: 2.125rem;"
                           title="Edit Informasi Kegiatan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>

                        @if(auth()->user()?->role !== 'magang')
                        <button type="button"
                            class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                            style="width: 2.125rem; height: 2.125rem;"
                            data-reauth-action="{{ route('operator.kegiatan.destroy', $k->id) }}"
                            data-reauth-id="{{ $k->id }}"
                            data-reauth-label="{{ $k->nama_kegiatan }}"
                            onclick="ReauthModal.open(this)" title="Hapus Kegiatan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tampilan Mobile / PWA: List Card Interaktif --}}
    <div class="kg-mobile-list divide-y divide-slate-100 dark:divide-slate-800" id="kgMobileList">
        @foreach($kegiatan as $k)
        <div data-tanggal="{{ $k->tanggal ?? '' }}"
             data-nama="{{ strtolower($k->nama_kegiatan) }} {{ strtolower($k->lokasi->nama_lokasi ?? '') }} {{ strtolower($k->kode_join ?? '') }}"
             class="kg-mobile-card p-4 hover:bg-blue-50/30 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
             onclick="window.location='{{ route('operator.kegiatan.detail', $k->id) }}'">
            
            {{-- Top Row: Nama & Quick Actions --}}
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-snug break-words">
                        {{ $k->nama_kegiatan }}
                    </h3>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="text-[11px] text-slate-400">PIN Sesi:</span>
                        <span class="text-xs font-mono font-bold tracking-wider text-blue-600 dark:text-blue-400 px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/60 border border-blue-100 dark:border-blue-900/40">
                            {{ $k->kode_join ?? '—' }}
                        </span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-1 flex-shrink-0" onclick="event.stopPropagation()">
                    <a href="{{ route('operator.kegiatan.edit', $k->id) }}"
                       class="btn btn-secondary btn-icon rounded-xl" style="width: 2rem; height: 2rem;"
                       title="Edit Informasi Kegiatan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>

                    @if(auth()->user()?->role !== 'magang')
                    <button type="button"
                        class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                        style="width: 2rem; height: 2rem;"
                        data-reauth-action="{{ route('operator.kegiatan.destroy', $k->id) }}"
                        data-reauth-id="{{ $k->id }}"
                        data-reauth-label="{{ $k->nama_kegiatan }}"
                        onclick="ReauthModal.open(this)" title="Hapus Kegiatan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Metadata Row: Lokasi, Peserta, Tanggal --}}
            <div class="grid grid-cols-2 gap-2 mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-xs text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-1.5 truncate">
                    <svg class="w-3.5 h-3.5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="truncate">{{ $k->lokasi->nama_lokasi ?? '—' }}</span>
                </div>
                <div class="flex items-center gap-1.5 truncate justify-end">
                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $k->peserta_count ?? 0 }} Siswa</span>
                </div>
                <div class="flex items-center gap-1.5 col-span-2 text-[11px] text-slate-400">
                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ isset($k->tanggal) ? \Carbon\Carbon::parse($k->tanggal)->isoFormat('dddd, D MMMM Y') : '—' }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination Soft --}}
    @if($kegiatan->hasPages())
    <div class="px-5 py-3.5 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 dark:border-slate-800">
        <p class="text-xs text-slate-400">
            Menampilkan {{ $kegiatan->firstItem() }}–{{ $kegiatan->lastItem() }} dari {{ $kegiatan->total() }} kegiatan
        </p>
        <div class="flex gap-1.5">
            @if($kegiatan->onFirstPage())
                <span class="page-btn rounded-lg opacity-40 cursor-not-allowed">‹</span>
            @else
                <a href="{{ $kegiatan->previousPageUrl() }}" class="page-btn rounded-lg">‹</a>
            @endif
            @foreach($kegiatan->getUrlRange(1, $kegiatan->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-btn rounded-lg {{ $page == $kegiatan->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($kegiatan->hasMorePages())
                <a href="{{ $kegiatan->nextPageUrl() }}" class="page-btn rounded-lg">›</a>
            @else
                <span class="page-btn rounded-lg opacity-40 cursor-not-allowed">›</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>{{-- /glass --}}

{{-- Modal dihapus — Tambah & Edit sekarang menggunakan halaman terpisah --}}

{{-- ── Styles ───────────────────────────────────────────────── --}}
<style>
/* Card Header */
.kg-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.125rem 1.375rem;
    border-bottom: 1px solid var(--border);
}
.kg-header-title { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); }
.kg-header-count { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.125rem; }

/* Tombol Tambah Kegiatan: Teks di desktop, icon + di mobile */
.kg-btn-add {
    font-size: 0.8125rem;
    padding: 0.42rem 0.85rem;
    height: 2.25rem;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Filter Bar */
.kg-filterbar {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.75rem 1.375rem;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}

/* Search */
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

/* Dropdown Wrapper */
.kg-dropdown-wrap {
    position: relative;
}
.kg-dropdown-trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    font-family: var(--font-sans);
    transition: border-color 0.18s ease, color 0.18s ease;
    white-space: nowrap;
}
.kg-dropdown-trigger:hover { border-color: var(--border-strong); color: var(--text-primary); }
.kg-dropdown-trigger.open { border-color: var(--primary); color: var(--primary); }
.kg-caret { transition: transform 0.2s ease; flex-shrink: 0; }
.kg-dropdown-trigger.open .kg-caret { transform: rotate(180deg); }

/* ── Filter Waktu Dropdown Elegan (Konsisten dengan Dashboard) ── */
.dash-period-dropdown-wrap {
    position: relative;
    display: inline-block;
}

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

.dash-period-trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    padding: 0.42rem 0.85rem;
    color: var(--text-primary);
    cursor: pointer;
    box-shadow: var(--shadow-xs);
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
    font-family: var(--font-sans);
    font-size: 0.8125rem;
}

.dash-period-trigger:hover {
    border-color: var(--primary);
    background: var(--bg-alt);
    box-shadow: var(--shadow-sm);
}

.dash-period-trigger:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 1px;
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

.dash-period-divider {
    height: 1px;
    background: var(--border);
    margin: 0.35rem 0;
}

.dash-period-custom-toggle {
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
}

.dash-period-custom-toggle:hover {
    background: var(--bg-alt);
}

.dash-period-custom-form {
    padding: 0.55rem 0.65rem 0.45rem;
    background: var(--bg-alt);
    border-radius: var(--r-md);
    margin-top: 0.25rem;
    border: 1px solid var(--border);
}

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
.kg-mobile-card {
    -webkit-tap-highlight-color: transparent;
}
.kg-mobile-card:active {
    background-color: rgba(67, 97, 238, 0.05);
}

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

/* Mode Pill Group */
.kg-pill-group {
    display: inline-flex;
    background: var(--bg-alt);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 3px;
    gap: 2px;
}
.kg-pill {
    padding: 0.25rem 0.75rem;
    border-radius: calc(var(--r-md) - 2px);
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--text-muted);
    background: none;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: var(--font-sans);
}
.kg-pill:hover { color: var(--text-primary); }
.kg-pill.active { background: var(--surface); color: var(--text-primary); box-shadow: var(--shadow-xs); }

/* Active filter chip */
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
    color: var(--primary);
    padding: 0;
    opacity: 0.7;
}
.kg-chip-clear:hover { opacity: 1; }

/* Danger btn */
.kg-btn-danger {
    background: var(--danger-light);
    color: var(--danger);
    border: 1.5px solid rgba(239,68,68,0.2);
}
.kg-btn-danger:hover { background: var(--danger); color: #fff; }

/* ── Instant Search-First Combobox Lokasi ──────────────────── */
.kg-combobox-wrap {
    position: relative;
    width: 100%;
}
.kg-combobox-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.kg-combobox-icon {
    position: absolute;
    left: 0.65rem;
    width: 14px;
    height: 14px;
    color: var(--text-muted);
    pointer-events: none;
}
.kg-combobox-input {
    width: 100%;
    padding-left: 2rem !important;
}
.kg-combobox-menu {
    position: absolute;
    top: calc(100% + 2px);
    left: 0;
    right: 0;
    z-index: 250;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    max-height: 200px;
    overflow-y: auto;
    animation: kgComboFade 0.08s ease-out;
}
@keyframes kgComboFade {
    from { opacity: 0; transform: translateY(-2px); }
    to { opacity: 1; transform: translateY(0); }
}
.kg-combobox-menu.hidden {
    display: none !important;
}
.kg-combobox-list {
    padding: 0.25rem 0;
    display: flex;
    flex-direction: column;
}
.kg-combobox-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.45rem 0.75rem;
    font-size: 0.8125rem;
    color: var(--text-secondary);
    cursor: pointer;
    transition: background 0.08s ease;
    border-radius: 0;
}
.kg-combobox-item:hover, .kg-combobox-item.active {
    background: var(--bg-alt);
    color: var(--text-primary);
}
.kg-combobox-item.selected {
    background: var(--primary-light);
    color: var(--primary);
    font-weight: 600;
}
/* Hint tambah lokasi di bawah input */
.kg-add-hint {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.3rem;
    font-size: 0.75rem;
    color: var(--text-muted);
    line-height: 1.4;
}
.kg-add-hint.hidden {
    display: none !important;
}
.kg-add-hint .kg-add-link {
    color: var(--primary);
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    text-underline-offset: 2px;
    background: none;
    border: none;
    padding: 0;
    font-size: 0.75rem;
    font-family: inherit;
    transition: opacity 0.1s;
}
.kg-add-hint .kg-add-link:hover {
    opacity: 0.75;
}
/* ── Toast Notification ─────────────────────────────────── */
#kgToastContainer {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    pointer-events: none;
}
.kg-toast {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.65rem 1rem;
    border-radius: var(--r-md);
    font-size: 0.8125rem;
    font-weight: 500;
    color: #fff;
    min-width: 220px;
    max-width: 320px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    pointer-events: auto;
    animation: kgToastIn 0.2s cubic-bezier(0.34,1.26,0.64,1) forwards;
    transition: opacity 0.25s, transform 0.25s;
}
.kg-toast.out {
    opacity: 0;
    transform: translateX(8px);
}
.kg-toast.success { background: #10b981; }
.kg-toast.info    { background: var(--primary); }
@keyframes kgToastIn {
    from { opacity: 0; transform: translateX(8px); }
    to   { opacity: 1; transform: translateX(0); }
}
</style>
<div id="kgToastContainer"></div>

{{-- ── Scripts ──────────────────────────────────────────────── --}}
<script>
(function () {
    // ── Client-Side In-Place Filter Engine (Tanpa Reload / Tanpa Pindah Route) ──
    let currentSearch = '';
    let currentPeriod = 'all'; // 'all', 'month', 'prev_month', 'year', 'custom'
    let customMonth   = null;
    let customYear    = new Date().getFullYear();

    const MONTH_NAMES = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    const searchInput    = document.getElementById('kgSearch');
    const totalCountEl   = document.getElementById('totalCount');
    const activeChip     = document.getElementById('activeChip');
    const activeChipText = document.getElementById('activeChipText');
    const btnClearPeriod = document.getElementById('btnClearPeriod');

    // ── Dropdown Filter Periode (Konsisten dengan Dashboard) ─────
    const periodMenu    = document.getElementById('kgPeriodMenu');
    const periodBtn     = document.getElementById('kgPeriodBtn');
    const periodCaret   = document.getElementById('kgPeriodCaret');
    const currentLabel  = document.getElementById('kgPeriodCurrentLabel');
    const optSemuaWaktu = document.getElementById('optSemuaWaktu');
    const optBulanIni   = document.getElementById('optBulanIni');
    const optBulanLalu  = document.getElementById('optBulanLalu');
    const optTahunIni   = document.getElementById('optTahunIni');
    const customToggle  = document.getElementById('btnToggleCustomPeriod');
    const customPanel   = document.getElementById('customPeriodForm');
    const customCaret   = document.getElementById('customPeriodCaret');

    function togglePeriodDropdown() {
        if (!periodMenu) return;
        const isHidden = periodMenu.classList.contains('hidden');
        if (isHidden) {
            periodMenu.classList.remove('hidden');
            periodCaret?.classList.add('rotate-180');
            periodBtn?.setAttribute('aria-expanded', 'true');
        } else {
            closePeriodDropdown();
        }
    }

    function closePeriodDropdown() {
        if (!periodMenu || periodMenu.classList.contains('hidden')) return;
        periodMenu.classList.add('hidden');
        periodCaret?.classList.remove('rotate-180');
        periodBtn?.setAttribute('aria-expanded', 'false');
    }

    function setActivePeriodItem(activeItem, displayTitle) {
        [optSemuaWaktu, optBulanIni, optBulanLalu, optTahunIni].forEach(el => {
            el?.classList.remove('active');
            el?.querySelector('.dash-period-check')?.classList.add('hidden');
        });
        if (activeItem) {
            activeItem.classList.add('active');
            activeItem.querySelector('.dash-period-check')?.classList.remove('hidden');
        }
        if (currentLabel && displayTitle) {
            currentLabel.textContent = displayTitle;
        }
    }

    // ── Fungsi Filter Baris Tabel & Mobile Cards (Live In-Place) ──
    function applyFilter() {
        const rows = document.querySelectorAll('#kgTbody tr[data-tanggal]');
        const mobileCards = document.querySelectorAll('#kgMobileList .kg-mobile-card[data-tanggal]');
        let visibleCount = 0;

        const now = new Date();
        const currentYearStr = String(now.getFullYear());
        const currentMonthStr = String(now.getMonth() + 1).padStart(2, '0');
        const currentMonthPrefix = `${currentYearStr}-${currentMonthStr}`; // misal: "2026-09"

        const prevDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        const prevYearStr = String(prevDate.getFullYear());
        const prevMonthStr = String(prevDate.getMonth() + 1).padStart(2, '0');
        const prevMonthPrefix = `${prevYearStr}-${prevMonthStr}`;

        const checkMatch = (nama, tanggal) => {
            const matchSearch = !currentSearch || nama.includes(currentSearch);
            let matchPeriod = true;
            if (currentPeriod === 'month') {
                matchPeriod = tanggal.startsWith(currentMonthPrefix);
            } else if (currentPeriod === 'prev_month') {
                matchPeriod = tanggal.startsWith(prevMonthPrefix);
            } else if (currentPeriod === 'year') {
                matchPeriod = tanggal.startsWith(currentYearStr);
            } else if (currentPeriod === 'custom' && customMonth !== null && customYear !== null) {
                const targetPrefix = `${customYear}-${String(customMonth + 1).padStart(2, '0')}`;
                matchPeriod = tanggal.startsWith(targetPrefix);
            }
            return matchSearch && matchPeriod;
        };

        rows.forEach(row => {
            const rowNama = (row.getAttribute('data-nama') || '').toLowerCase();
            const rowTanggal = (row.getAttribute('data-tanggal') || '').trim();

            if (checkMatch(rowNama, rowTanggal)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        mobileCards.forEach(card => {
            const cardNama = (card.getAttribute('data-nama') || '').toLowerCase();
            const cardTanggal = (card.getAttribute('data-tanggal') || '').trim();

            if (checkMatch(cardNama, cardTanggal)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Update counter teks
        if (totalCountEl) {
            totalCountEl.textContent = `${visibleCount} kegiatan ditemukan`;
        }

        // Tampilkan/sembunyikan pesan jika tidak ada data yang cocok
        let noResultsRow = document.getElementById('noResultsRow');
        let noResultsMobile = document.getElementById('noResultsMobile');

        if (visibleCount === 0 && (rows.length > 0 || mobileCards.length > 0)) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noResultsRow';
                noResultsRow.innerHTML = `
                    <td colspan="5" class="py-12 text-center text-slate-400">
                        <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Tidak ada kegiatan yang cocok</p>
                        <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci atau rentang waktu lain</p>
                        <button type="button" class="btnResetFilter btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary">
                            Reset Filter Pencarian
                        </button>
                    </td>
                `;
                document.getElementById('kgTbody')?.appendChild(noResultsRow);
            } else {
                noResultsRow.style.display = '';
            }

            if (!noResultsMobile) {
                noResultsMobile = document.createElement('div');
                noResultsMobile.id = 'noResultsMobile';
                noResultsMobile.className = 'py-10 px-4 text-center text-slate-400';
                noResultsMobile.innerHTML = `
                    <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Tidak ada kegiatan yang cocok</p>
                    <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci atau rentang waktu lain</p>
                    <button type="button" class="btnResetFilter btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary">
                        Reset Filter Pencarian
                    </button>
                `;
                document.getElementById('kgMobileList')?.appendChild(noResultsMobile);
            } else {
                noResultsMobile.style.display = '';
            }

            document.querySelectorAll('.btnResetFilter').forEach(btn => {
                btn.onclick = resetAllFilters;
            });
        } else {
            if (noResultsRow) noResultsRow.style.display = 'none';
            if (noResultsMobile) noResultsMobile.style.display = 'none';
        }
    }

    // ── Update Tampilan Chip Filter Aktif & Indikator Tombol ──────
    function updateActiveChip() {
        const activeDot = document.getElementById('kgPeriodActiveDot');
        const now = new Date();
        if (currentPeriod === 'all') {
            periodBtn?.classList.remove('active');
            activeDot?.classList.add('hidden');
            if (activeChip) activeChip.classList.add('hidden');
        } else {
            periodBtn?.classList.add('active');
            activeDot?.classList.remove('hidden');
            if (activeChip && activeChipText) {
                if (currentPeriod === 'month') {
                    activeChipText.textContent = `Bulan ini (${MONTH_NAMES[now.getMonth()]})`;
                } else if (currentPeriod === 'prev_month') {
                    const prev = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                    activeChipText.textContent = `Bulan lalu (${MONTH_NAMES[prev.getMonth()]})`;
                } else if (currentPeriod === 'year') {
                    activeChipText.textContent = `Tahun ${now.getFullYear()}`;
                } else if (currentPeriod === 'custom' && customMonth !== null) {
                    activeChipText.textContent = `${MONTH_NAMES[customMonth]} ${customYear}`;
                }
                activeChip.classList.remove('hidden');
            }
        }
    }

    function resetPeriodFilter() {
        currentPeriod = 'all';
        customMonth = null;
        periodBtn?.classList.remove('active');
        document.getElementById('kgPeriodActiveDot')?.classList.add('hidden');
        setActivePeriodItem(optSemuaWaktu, 'Semua Waktu');
        updateActiveChip();
    }

    function resetAllFilters() {
        currentSearch = '';
        if (searchInput) searchInput.value = '';
        resetPeriodFilter();
        applyFilter();
    }

    // ── Search Input: Instant Live Filter (Tanpa Reload) ─────────
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearch = this.value.trim().toLowerCase();
            applyFilter();
        });
    }

    // ── Dropdown Event Listeners ─────────────────────────────────
    periodBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        togglePeriodDropdown();
    });

    periodMenu?.addEventListener('click', (e) => e.stopPropagation());

    optSemuaWaktu?.addEventListener('click', () => {
        currentPeriod = 'all';
        customMonth = null;
        setActivePeriodItem(optSemuaWaktu, 'Semua Waktu');
        closePeriodDropdown();
        updateActiveChip();
        applyFilter();
    });

    optBulanIni?.addEventListener('click', () => {
        const now = new Date();
        currentPeriod = 'month';
        customMonth = null;
        setActivePeriodItem(optBulanIni, `Bulan Ini (${MONTH_NAMES[now.getMonth()]} ${now.getFullYear()})`);
        closePeriodDropdown();
        updateActiveChip();
        applyFilter();
    });

    optBulanLalu?.addEventListener('click', () => {
        const now = new Date();
        const prev = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        currentPeriod = 'prev_month';
        customMonth = null;
        setActivePeriodItem(optBulanLalu, `Bulan Lalu (${MONTH_NAMES[prev.getMonth()]} ${prev.getFullYear()})`);
        closePeriodDropdown();
        updateActiveChip();
        applyFilter();
    });

    optTahunIni?.addEventListener('click', () => {
        const now = new Date();
        currentPeriod = 'year';
        customMonth = null;
        setActivePeriodItem(optTahunIni, `Tahun Ini (${now.getFullYear()})`);
        closePeriodDropdown();
        updateActiveChip();
        applyFilter();
    });

    customToggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (customPanel) {
            const isClosed = customPanel.classList.contains('hidden');
            customPanel.classList.toggle('hidden');
            customCaret?.classList.toggle('rotate-180', isClosed);
        }
    });

    document.getElementById('btnApplyCustom')?.addEventListener('click', () => {
        const m = parseInt(document.getElementById('selCustomMonth').value, 10);
        const y = parseInt(document.getElementById('selCustomYear').value, 10);
        currentPeriod = 'custom';
        customMonth = m;
        customYear = y;
        setActivePeriodItem(null, `${MONTH_NAMES[m]} ${y}`);
        closePeriodDropdown();
        updateActiveChip();
        applyFilter();
    });

    document.addEventListener('click', () => {
        closePeriodDropdown();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closePeriodDropdown();
        }
    });

    btnClearPeriod?.addEventListener('click', () => {
        resetPeriodFilter();
        applyFilter();
    });

    // ── Modal: Tambah / Edit Kegiatan ─────────────────────────────
    const overlay    = document.getElementById('kegiatanModalOverlay');
    const closeBtn   = document.getElementById('kegiatanModalClose');
    const cancelBtn  = document.getElementById('kegiatanModalCancelBtn');
    const modalTitle = document.getElementById('kegiatanModalTitle');
    const form       = document.getElementById('kegiatanForm');
    const methodInp  = document.getElementById('kegiatanFormMethod');
    const idInp      = document.getElementById('kegiatanFormId');

    function openModal() { overlay?.classList.add('active'); }
    function closeModal() {
        overlay?.classList.remove('active');
        form?.reset();
        if (methodInp)  methodInp.value  = 'POST';
        if (idInp)      idInp.value      = '';
        if (modalTitle) modalTitle.textContent = 'Tambah Kegiatan';
        if (form)       form.action = document.getElementById('kegiatanModalBox')?.dataset.storeUrl || '';
        window.resetLokasiCombobox?.();
    }

    // Open via "Tambah Kegiatan" buttons (any button with class that triggers modal)
    document.querySelectorAll('#btnTambahKegiatan, #btnTambahKegiatanEmpty').forEach(btn => {
        btn?.addEventListener('click', openModal);
    });
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
    });

    // Edit kegiatan — called from table row Edit button
    window.editKegiatan = function (id) {
        const editUrl = `/operator/kegiatan/${id}/edit`;
        fetch(editUrl)
            .then(r => r.json())
            .then(data => {
                if (modalTitle) modalTitle.textContent = 'Edit Kegiatan';
                if (methodInp)  methodInp.value = 'PUT';
                if (idInp)      idInp.value = data.id;

                const box = document.getElementById('kegiatanModalBox');
                if (form && box) form.action = box.dataset.storeUrl.replace('/operator/kegiatan', `/operator/kegiatan/${data.id}`);

                // Populate fields
                const f = (n) => document.getElementById(n);
                if (f('f_nama'))    f('f_nama').value    = data.nama_kegiatan || '';
                if (f('f_tanggal')) f('f_tanggal').value = data.tanggal || '';
                if (f('f_durasi'))  f('f_durasi').value  = data.durasi_menit || 30;
                if (f('f_mode'))    f('f_mode').value    = data.mode || 'digital';
                if (f('f_catatan')) f('f_catatan').value = data.catatan || '';

                window.setLokasiComboboxValue?.(data.lokasi_id, data.lokasi_nama || '');
                openModal();
            })
            .catch(() => showToast('Gagal memuat data kegiatan', 'info'));
    };

    // ── Toast helper ─────────────────────────────────────────────
    function showToast(message, type = 'success', duration = 3000) {
        const container = document.getElementById('kgToastContainer');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `kg-toast ${type}`;
        const icon = type === 'success'
            ? `<svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
        toast.innerHTML = `${icon}<span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('out');
            setTimeout(() => toast.remove(), 280);
        }, duration);
    }
    // Expose for combobox
    window.kgShowToast = showToast;

    // ── Instant Search-First Combobox Lokasi ──────────────────────
    let masterLokasi     = @json($lokasiList ?? []);
    let activeComboIndex = -1;

    const comboWrap     = document.getElementById('lokasiComboboxWrap');
    const comboInput    = document.getElementById('f_lokasi_search');
    const comboMenu     = document.getElementById('lokasiComboboxMenu');
    const comboList     = document.getElementById('lokasiComboboxList');
    const comboIdInput  = document.getElementById('f_lokasi_id');
    const comboBaruInput = document.getElementById('f_nama_lokasi_baru');

    const addHint   = document.getElementById('lokasiAddHint');
    const addBtn    = document.getElementById('lokasiAddBtn');
    let   pendingNewName = '';
    let   addHintTimer   = null;

    function hideAddHint() {
        clearTimeout(addHintTimer); addHintTimer = null;
        if (addHint) addHint.classList.add('hidden');
        pendingNewName = '';
    }

    function scheduleAddHint(name) {
        if (addHintTimer) clearTimeout(addHintTimer);
        const trimmed = (name || '').trim();
        if (!trimmed) { hideAddHint(); return; }
        pendingNewName = trimmed;
        addHintTimer = setTimeout(() => {
            const currentVal = comboInput ? comboInput.value.trim() : '';
            if (currentVal && pendingNewName && addHint) {
                const matchFound = masterLokasi.some(l => l.nama_lokasi.toLowerCase() === currentVal.toLowerCase());
                if (!matchFound) addHint.classList.remove('hidden');
                else hideAddHint();
            } else {
                hideAddHint();
            }
        }, 3000);
    }

    function renderLokasiDropdown(query) {
        if (!comboList) return;
        const q = query.trim().toLowerCase();
        comboList.innerHTML = '';
        activeComboIndex = -1;
        if (!q) { comboMenu?.classList.add('hidden'); hideAddHint(); return; }

        const startsWith = masterLokasi.filter(l => l.nama_lokasi.toLowerCase().startsWith(q));
        const contains   = masterLokasi.filter(l => !l.nama_lokasi.toLowerCase().startsWith(q) && l.nama_lokasi.toLowerCase().includes(q));
        const matches    = [...startsWith, ...contains];

        if (matches.length > 0) {
            hideAddHint();
            matches.forEach(lok => {
                const item = document.createElement('div');
                item.className = 'kg-combobox-item';
                if (comboIdInput && comboIdInput.value == lok.id) item.classList.add('selected');
                let highlighted = escapeHtml(lok.nama_lokasi);
                const regex = new RegExp(`(${escapeRegExp(q)})`, 'gi');
                highlighted = highlighted.replace(regex, '<span style="font-weight:600;color:var(--primary);">$1</span>');
                item.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="flex-1 truncate">${highlighted}</span>`;
                item.addEventListener('click', () => selectExistingLokasi(lok));
                comboList.appendChild(item);
            });
            comboMenu?.classList.remove('hidden');
        } else {
            comboMenu?.classList.add('hidden');
            if (!masterLokasi.some(l => l.nama_lokasi.toLowerCase() === q)) scheduleAddHint(query.trim());
            else hideAddHint();
        }
    }

    function selectExistingLokasi(lok) {
        if (!comboInput) return;
        comboInput.value = lok.nama_lokasi;
        if (comboIdInput)    comboIdInput.value   = lok.id;
        if (comboBaruInput)  comboBaruInput.value = '';
        comboMenu?.classList.add('hidden');
        hideAddHint();
    }

    function selectNewLokasi(name) {
        if (!name || !comboInput) return;
        comboInput.value = name;
        if (comboIdInput)    comboIdInput.value   = '';
        if (comboBaruInput)  comboBaruInput.value = name;
        comboMenu?.classList.add('hidden');
        hideAddHint();
        if (!masterLokasi.some(l => l.nama_lokasi.toLowerCase() === name.toLowerCase())) {
            masterLokasi.push({ id: 'new_' + Date.now(), nama_lokasi: name });
        }
        showToast(`Lokasi "${name}" akan didaftarkan saat disimpan`);
    }

    if (addBtn) addBtn.addEventListener('click', () => { if (pendingNewName) selectNewLokasi(pendingNewName); });

    if (comboInput) {
        comboInput.addEventListener('input', function () {
            hideAddHint();
            if (comboIdInput && this.value !== comboInput._lastSelected) {
                comboIdInput.value = '';
                if (comboBaruInput) comboBaruInput.value = '';
            }
            if (!this.value.trim()) { comboMenu?.classList.add('hidden'); return; }
            renderLokasiDropdown(this.value);
        });

        comboInput.addEventListener('focus', function () {
            if (this.value.trim()) renderLokasiDropdown(this.value);
        });

        comboInput.addEventListener('keydown', function (e) {
            if (!comboList) return;
            const items = comboList.querySelectorAll('.kg-combobox-item');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (items.length) { activeComboIndex = (activeComboIndex + 1) % items.length; updateActiveComboItem(items); }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (items.length) { activeComboIndex = (activeComboIndex - 1 + items.length) % items.length; updateActiveComboItem(items); }
            } else if (e.key === 'Enter') {
                if (activeComboIndex >= 0 && activeComboIndex < items.length) { e.preventDefault(); items[activeComboIndex].click(); }
            } else if (e.key === 'Escape') {
                comboMenu?.classList.add('hidden'); hideAddHint();
            }
        });

        document.addEventListener('click', function (e) {
            const hintEl = document.getElementById('lokasiAddHint');
            if (comboWrap && !comboWrap.contains(e.target) && !(hintEl && hintEl.contains(e.target))) {
                comboMenu?.classList.add('hidden');
            }
        });

        const kgForm = document.getElementById('kegiatanForm');
        if (kgForm) {
            kgForm.addEventListener('submit', function () {
                if (comboInput && !comboIdInput?.value && comboInput.value.trim()) {
                    if (comboBaruInput) comboBaruInput.value = comboInput.value.trim();
                }
            });
        }
    }

    function updateActiveComboItem(items) {
        items.forEach((item, idx) => {
            item.classList.toggle('active', idx === activeComboIndex);
            if (idx === activeComboIndex) item.scrollIntoView({ block: 'nearest' });
        });
    }

    function escapeHtml(str) { const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }
    function escapeRegExp(str) { return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

    window.resetLokasiCombobox = function () {
        if (!comboInput) return;
        comboInput.value = '';
        if (comboIdInput)    comboIdInput.value   = '';
        if (comboBaruInput)  comboBaruInput.value = '';
        comboMenu?.classList.add('hidden');
        hideAddHint();
    };

    window.setLokasiComboboxValue = function (id, name) {
        if (!comboInput) return;
        if (name) {
            comboInput.value = name;
            comboInput._lastSelected = name;
            if (comboIdInput)    comboIdInput.value   = id ?? '';
            if (comboBaruInput)  comboBaruInput.value = '';
            comboMenu?.classList.add('hidden');
            hideAddHint();
        } else {
            window.resetLokasiCombobox();
        }
    };
})();
</script>

@endsection

