@extends('layouts.app')

@section('title', 'Master Lokasi')
@section('page-title', 'Master Lokasi')
@section('page-subtitle', 'Kelola daftar lokasi kegiatan sosialisasi P2M')

@php
    $fSearch = $search ?? '';
    $fJenis  = $jenis ?? '';
    $jenisLabels = [
        'sekolah'    => 'Sekolah',
        'kampus'     => 'Kampus',
        'masyarakat' => 'Masyarakat',
        'lapas'      => 'Lapas',
        'instansi'   => 'Instansi',
    ];
@endphp

@section('content')

{{-- ── Table Card (Soft Rounded-2xl — Mirip Tampilan Kegiatan) ── --}}
<div class="glass mb-6 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs overflow-visible" style="padding: 0;">

    {{-- Card Header Soft --}}
    <div class="kg-header flex items-center justify-between p-4 md:p-5 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 truncate">Daftar Lokasi Binaan</h2>
                <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5 truncate" id="totalCount">{{ $lokasi->total() ?? 0 }} lokasi terdaftar &middot; dipakai sebagai master combobox di form Kegiatan</p>
            </div>
        </div>
        <button type="button" 
                class="btn btn-primary rounded-xl shadow-xs inline-flex items-center justify-center gap-1.5 flex-shrink-0 kg-btn-add" 
                id="btnTambahLokasi"
                title="Tambah Lokasi Baru">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="kg-btn-add-label hidden sm:inline">Tambah Lokasi</span>
        </button>
    </div>

    {{-- ── Filter Bar Soft (Mirip Kegiatan & Bank Soal) ─────────── --}}
    <div class="kg-filterbar relative z-20 p-3.5 md:p-4 bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 flex-wrap">
        {{-- Search Input with SVG Icon --}}
        <div class="kg-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="kg-search-icon" width="14" height="14" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="lokSearch" type="text" class="kg-search-input rounded-xl border border-slate-200/70 dark:border-slate-700"
                   placeholder="Cari lokasi, kecamatan, alamat..."
                   value="{{ $fSearch }}">
        </div>

        {{-- Filter Jenis Sasaran (Tombol Icon Filter Compact) --}}
        <div class="dash-period-dropdown-wrap relative" id="lokasiFilterWrap">
            <button type="button" 
                    id="lokasiFilterBtn" 
                    class="kg-filter-icon-btn" 
                    aria-haspopup="true" 
                    aria-expanded="false" 
                    title="Filter jenis sasaran">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span id="lokasiFilterDot" class="kg-filter-active-dot {{ empty($fJenis) ? 'hidden' : '' }}"></span>
            </button>

            {{-- Floating Dropdown Menu --}}
            <div id="lokasiFilterMenu" class="dash-period-menu hidden" role="menu" style="width: 270px; max-height: 400px; overflow-y: auto;">
                <div class="dash-period-section-label">Jenis Sasaran</div>

                <button type="button" class="dash-period-item dropdown-jenis-item {{ empty($fJenis) ? 'active' : '' }}" data-jenis="" data-label="Semua Jenis Sasaran" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Semua Jenis Sasaran</span>
                        <span class="dash-period-item-sub">Tampilkan seluruh lokasi binaan</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary {{ empty($fJenis) ? '' : 'hidden' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                @php
                    $jenisOptions = [
                        'sekolah'    => ['Sekolah', 'SMA, SMK, MA & sederajat'],
                        'kampus'     => ['Kampus', 'Universitas & perguruan tinggi'],
                        'masyarakat' => ['Masyarakat', 'Komunitas & warga binaan P2M'],
                        'lapas'      => ['Lapas', 'Lapas, Rutan & Balai Pemasyarakatan'],
                        'instansi'   => ['Instansi', 'Instansi pemerintah, BUMN & swasta'],
                    ];
                @endphp
                @foreach($jenisOptions as $jKey => $jInfo)
                <button type="button" class="dash-period-item dropdown-jenis-item {{ $fJenis === $jKey ? 'active' : '' }}" data-jenis="{{ $jKey }}" data-label="{{ $jInfo[0] }}" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">{{ $jInfo[0] }}</span>
                        <span class="dash-period-item-sub">{{ $jInfo[1] }}</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary {{ $fJenis === $jKey ? '' : 'hidden' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Active Filter Chip --}}
        <div class="kg-active-chip {{ (empty($fJenis) && empty($fSearch)) ? 'hidden' : '' }}" id="activeChip">
            <span id="activeChipText">
                @if($fJenis && isset($jenisLabels[$fJenis]))
                    {{ $jenisLabels[$fJenis] }}
                @elseif($fSearch)
                    "{{ $fSearch }}"
                @endif
            </span>
            <button type="button" id="btnClearFilter" class="kg-chip-clear" aria-label="Hapus filter" title="Reset filter">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Table & Cards Soft ─────────────────────────────────── --}}
    @if($lokasi->isEmpty())
    <div class="empty-state py-12 text-center">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="font-semibold text-sm text-slate-700 dark:text-slate-200">Belum ada lokasi ditemukan</p>
        <p class="text-xs text-slate-400 mt-0.5">Tambahkan lokasi binaan baru untuk digunakan sebagai master kegiatan sosialisasi</p>
        <button type="button" class="btn btn-primary btn-sm rounded-xl mt-3 shadow-xs inline-flex items-center gap-1.5" id="btnTambahLokasiEmpty">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Lokasi Baru</span>
        </button>
    </div>
    @else
    {{-- Tampilan Desktop: Tabel Data (> 768px) --}}
    <div class="kg-desktop-table-wrap">
        <table class="data-table w-full text-left" id="lokasiTable">
            <thead>
                <tr>
                    <th class="py-3 px-4" style="width: 44px;">#</th>
                    <th class="py-3 px-4">Nama Lokasi</th>
                    <th class="py-3 px-4">Alamat</th>
                    <th class="py-3 px-4">Kecamatan</th>
                    <th class="py-3 px-4">Jenis Sasaran</th>
                    <th class="py-3 px-4">Kegiatan</th>
                    <th class="py-3 px-4" style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="lokasiTbody" class="divide-y divide-slate-100 dark:divide-slate-800/70">
            @foreach($lokasi as $i => $lok)
            @php
                $rawJenis = strtolower($lok->jenis_sasaran ?? 'sekolah');
                $searchContent = strtolower(($lok->nama_lokasi ?? '') . ' ' . ($lok->kecamatan ?? '') . ' ' . ($lok->alamat ?? '') . ' ' . $rawJenis);
                $dipakai = ($lok->events_count ?? $lok->kegiatan_count ?? 0) > 0;
            @endphp
            <tr data-nama="{{ $searchContent }}"
                data-jenis="{{ $rawJenis }}"
                class="hover:bg-blue-50/40 dark:hover:bg-slate-800/40 transition-colors cursor-pointer group"
                onclick="editLokasi({{ $lok->id }})"
                title="Klik untuk melihat atau mengedit detail lokasi ini">
                
                {{-- Index --}}
                <td class="py-3.5 px-4 text-xs text-slate-400 font-mono">
                    {{ $lokasi->firstItem() + $i }}
                </td>

                {{-- Nama Lokasi --}}
                <td class="py-3.5 px-4">
                    <span class="font-semibold text-slate-800 dark:text-slate-100 group-hover:text-primary transition-colors block text-sm">
                        {{ $lok->nama_lokasi }}
                    </span>
                </td>

                {{-- Alamat --}}
                <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <span class="truncate max-w-[200px]">{{ $lok->alamat ?? '—' }}</span>
                    </div>
                </td>

                {{-- Kecamatan --}}
                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>{{ $lok->kecamatan ?? '—' }}</span>
                    </div>
                </td>

                {{-- Jenis Sasaran (teks redup, tanpa badge warna) --}}
                <td class="py-3.5 px-4 whitespace-nowrap text-xs text-slate-500 dark:text-slate-400">
                    @if($rawJenis === 'masyarakat' || $rawJenis === 'komunitas')
                        Masyarakat
                    @else
                        {{ ucfirst($rawJenis) }}
                    @endif
                </td>

                {{-- Kegiatan Terkait --}}
                <td class="py-3.5 px-4 text-xs font-semibold text-slate-700 dark:text-slate-200 whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $lok->events_count ?? $lok->kegiatan_count ?? 0 }}x Kegiatan</span>
                    </div>
                </td>

                {{-- Aksi --}}
                <td class="py-3.5 px-4" onclick="event.stopPropagation()">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button"
                                class="btn btn-secondary btn-icon rounded-xl"
                                style="width: 2.125rem; height: 2.125rem;"
                                onclick="event.stopPropagation(); editLokasi({{ $lok->id }})"
                                title="Edit Informasi Lokasi">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>

                        @if($dipakai)
                        <button type="button"
                                class="btn btn-secondary btn-icon rounded-xl opacity-40 cursor-not-allowed"
                                style="width: 2.125rem; height: 2.125rem;"
                                disabled
                                title="Tidak dapat dihapus — masih dipakai {{ $lok->events_count ?? $lok->kegiatan_count ?? 0 }} kegiatan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        @elseif(auth()->user()?->role !== 'magang')
                        <button type="button"
                                class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                                style="width: 2.125rem; height: 2.125rem;"
                                data-reauth-action="{{ route('operator.lokasi.destroy', $lok->id) }}"
                                data-reauth-id="{{ $lok->id }}"
                                data-reauth-label="{{ $lok->nama_lokasi }}"
                                onclick="event.stopPropagation(); ReauthModal.open(this)"
                                title="Hapus Lokasi">
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

    {{-- Tampilan Mobile / PWA: List Card Interaktif (<= 768px) --}}
    <div class="kg-mobile-list divide-y divide-slate-100 dark:divide-slate-800" id="lokasiMobileList">
        @foreach($lokasi as $i => $lok)
        @php
            $rawJenis = strtolower($lok->jenis_sasaran ?? 'sekolah');
            $searchContent = strtolower(($lok->nama_lokasi ?? '') . ' ' . ($lok->kecamatan ?? '') . ' ' . ($lok->alamat ?? '') . ' ' . $rawJenis);
            $dipakai = ($lok->events_count ?? $lok->kegiatan_count ?? 0) > 0;
            $labelJenis = ($rawJenis === 'masyarakat' || $rawJenis === 'komunitas') ? 'Masyarakat' : ucfirst($rawJenis);
        @endphp
        <div data-nama="{{ $searchContent }}"
             data-jenis="{{ $rawJenis }}"
             class="lokasi-card p-4 hover:bg-blue-50/30 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
             onclick="editLokasi({{ $lok->id }})"
             title="Klik untuk melihat atau mengedit detail lokasi ini">
            
            {{-- Top Row: Icon + Nama Lokasi + Action Buttons --}}
            <div class="flex items-start justify-between gap-2.5">
                <div class="flex items-start gap-3 min-w-0 flex-1">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary border border-blue-100 dark:border-blue-900/50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-snug break-words">
                            {{ $lok->nama_lokasi }}
                        </h3>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                {{ $labelJenis }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-primary">
                                <svg class="w-3 h-3 text-primary/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $lok->events_count ?? $lok->kegiatan_count ?? 0 }}x Kegiatan
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons (Edit + Delete) --}}
                <div class="flex items-center gap-1.5 flex-shrink-0" onclick="event.stopPropagation()">
                    <button type="button"
                            class="btn btn-secondary btn-icon rounded-xl"
                            style="width: 2.125rem; height: 2.125rem;"
                            onclick="event.stopPropagation(); editLokasi({{ $lok->id }})"
                            title="Edit Informasi Lokasi">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>

                    @if($dipakai)
                    <button type="button"
                            class="btn btn-secondary btn-icon rounded-xl opacity-40 cursor-not-allowed"
                            style="width: 2.125rem; height: 2.125rem;"
                            disabled
                            title="Tidak dapat dihapus — masih dipakai {{ $lok->events_count ?? $lok->kegiatan_count ?? 0 }} kegiatan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @elseif(auth()->user()?->role !== 'magang')
                    <button type="button"
                            class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                            style="width: 2.125rem; height: 2.125rem;"
                            data-reauth-action="{{ route('operator.lokasi.destroy', $lok->id) }}"
                            data-reauth-id="{{ $lok->id }}"
                            data-reauth-label="{{ $lok->nama_lokasi }}"
                            onclick="event.stopPropagation(); ReauthModal.open(this)"
                            title="Hapus Lokasi">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Bottom Row: Alamat & Kecamatan --}}
            <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex flex-col gap-1 text-xs text-slate-500 dark:text-slate-400">
                <div class="flex items-start gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <span class="line-clamp-2">{{ $lok->alamat ?: '—' }}</span>
                </div>
                @if($lok->kecamatan)
                <div class="flex items-center gap-1.5 text-[11px] text-slate-400 ml-5">
                    <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Kecamatan {{ $lok->kecamatan }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination Soft (server-render awal; ditulis ulang JS-AJAX tiap fetch) --}}
    <div id="lokasiPagination">
    @if($lokasi->hasPages())
    <div class="px-5 py-3.5 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
        <p class="text-xs text-slate-400" id="paginationInfo">
            Menampilkan {{ $lokasi->firstItem() }}–{{ $lokasi->lastItem() }} dari {{ $lokasi->total() }} lokasi
        </p>
        <div class="flex gap-1.5" id="paginationBtns">
            @if($lokasi->onFirstPage())
                <span class="page-btn rounded-lg opacity-40 cursor-not-allowed">‹</span>
            @else
                <button type="button" data-page="{{ $lokasi->currentPage() - 1 }}" class="page-btn rounded-lg">‹</button>
            @endif
            @foreach($lokasi->getUrlRange(max(1, $lokasi->currentPage()-2), min($lokasi->lastPage(), $lokasi->currentPage()+2)) as $page => $url)
                @if($page == $lokasi->currentPage())
                    <span class="page-btn rounded-lg active">{{ $page }}</span>
                @else
                    <button type="button" data-page="{{ $page }}" class="page-btn rounded-lg">{{ $page }}</button>
                @endif
            @endforeach
            @if($lokasi->hasMorePages())
                <button type="button" data-page="{{ $lokasi->currentPage() + 1 }}" class="page-btn rounded-lg">›</button>
            @else
                <span class="page-btn rounded-lg opacity-40 cursor-not-allowed">›</span>
            @endif
        </div>
    </div>
    @endif
    </div>
    @endif
</div>

{{-- ── MODAL POPUP: Tambah & Edit Detail Lokasi ─────────────── --}}
<div class="modal-overlay" id="lokasiModalOverlay">
    <div class="modal-box rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl p-5 sm:p-6 max-w-lg w-full mx-4">
        <div class="modal-header pb-4 mb-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center border border-blue-100 dark:border-blue-900/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100" id="lokasiModalTitle">Tambah Lokasi</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Master data lokasi sasaran sosialisasi P2M</p>
                </div>
            </div>
            <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" id="lokasiModalClose">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="lokasiForm" method="POST" action="{{ route('operator.lokasi.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="lokasiFormMethod" value="POST">
            <input type="hidden" name="id" id="lokasiFormId">

            {{-- Nama Lokasi --}}
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="fl_nama">
                    Nama Lokasi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_lokasi" id="fl_nama"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                       placeholder="cth: SMA Negeri 1 Surabaya" required>
            </div>

            {{-- Alamat --}}
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="fl_alamat">
                    Alamat Lengkap
                </label>
                <input type="text" name="alamat" id="fl_alamat"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                       placeholder="Jl. Wijaya Kusuma No. 48">
            </div>

            {{-- Kecamatan & Jenis Sasaran (Grid 2 Kolom) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="fl_kecamatan">
                        Kecamatan
                    </label>
                    <input type="text" name="kecamatan" id="fl_kecamatan"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                           placeholder="cth: Genteng">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="fl_jenis">
                        Jenis Sasaran
                    </label>
                    <div class="relative">
                        <select name="jenis_sasaran" id="fl_jenis"
                                class="w-full appearance-none px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-9 cursor-pointer shadow-xs">
                             <option value="sekolah">Sekolah</option>
                             <option value="kampus">Kampus</option>
                             <option value="masyarakat">Masyarakat / Komunitas</option>
                             <option value="komunitas">Komunitas</option>
                             <option value="lapas">Lapas / Rutan</option>
                             <option value="instansi">Instansi</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 flex items-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Modal Actions --}}
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" class="btn btn-secondary btn-sm rounded-xl px-4" id="lokasiModalCancelBtn">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary btn-sm rounded-xl px-4 shadow-xs">
                    Simpan Lokasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Styles Scoped untuk Lokasi (Mirip Kegiatan & Bank Soal) ─────────── --}}
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
    width: 240px;
    transition: border-color 0.18s ease, box-shadow 0.18s ease;
}
.kg-search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67,97,238,0.08);
}
.kg-search-input::placeholder { color: var(--text-xmuted); }

/* Filter Icon Button & Dropdown Menu */
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
.dash-period-dropdown-wrap {
    position: relative;
    flex-shrink: 0;
}
.dash-period-menu {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
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
    padding: 0.35rem 0.65rem 0.25rem;
}
.dash-period-item {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.45rem 0.65rem;
    border-radius: var(--r-md);
    border: none;
    background: transparent;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
    text-align: left;
}
.dash-period-item:hover {
    background: var(--bg-alt);
}
.dash-period-item.active {
    background: var(--primary-light);
}
.dash-period-item.active .dash-period-item-title {
    color: var(--primary);
    font-weight: 700;
}
.dash-period-item-title {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.25;
}
.dash-period-item-sub {
    font-size: 0.6875rem;
    color: var(--text-muted);
    margin-top: 1px;
}

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
    align-items: center;
    color: var(--primary);
    padding: 0;
    opacity: 0.7;
    transition: opacity 0.15s;
}
.kg-chip-clear:hover { opacity: 1; }

/* Desktop vs Mobile Card List */
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
.lokasi-card {
    -webkit-tap-highlight-color: transparent;
}
.lokasi-card:active {
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
    .dash-period-menu {
        right: 0;
        left: auto;
        width: 270px;
        max-width: calc(100vw - 2rem);
    }
}
</style>

@endsection

@push('scripts')
<script>
(function () {
    // ── Tabel Master AJAX (tanpa reload): search + jenis + pagination ──
    const indexUrl = "{{ route('operator.lokasi.index') }}";
    const destroyBase = "{{ url('operator/lokasi') }}";
    const isMagang = {{ (auth()->user()?->role === 'magang') ? 'true' : 'false' }};

    let currentSearch = String(@json($fSearch) || '');
    let currentJenis  = String(@json($fJenis) || '').toLowerCase();
    let currentPage   = parseInt(@json($lokasi->currentPage()), 10) || 1;
    let fetchSeq = 0;
    let searchTimer = null;

    const tbody              = document.getElementById('lokasiTbody');
    const mobileList         = document.getElementById('lokasiMobileList');
    const paginationBox      = document.getElementById('lokasiPagination');
    const searchInput        = document.getElementById('lokSearch');
    const totalCountEl       = document.getElementById('totalCount');
    const lokasiFilterWrap   = document.getElementById('lokasiFilterWrap');
    const lokasiFilterBtn    = document.getElementById('lokasiFilterBtn');
    const lokasiFilterMenu   = document.getElementById('lokasiFilterMenu');
    const lokasiFilterDot    = document.getElementById('lokasiFilterDot');
    const dropdownJenisItems = document.querySelectorAll('.dropdown-jenis-item');
    const activeChip         = document.getElementById('activeChip');
    const activeChipText     = document.getElementById('activeChipText');
    const btnClearFilter     = document.getElementById('btnClearFilter');

    const jenisLabelMap = {
        'sekolah': 'Sekolah',
        'kampus': 'Kampus',
        'masyarakat': 'Masyarakat',
        'lapas': 'Lapas',
        'instansi': 'Instansi'
    };

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function badgeJenis(raw) {
        const j = String(raw || 'sekolah').toLowerCase();
        const label = (j === 'masyarakat' || j === 'komunitas')
            ? 'Masyarakat'
            : j.charAt(0).toUpperCase() + j.slice(1);
        return `<span class="text-xs text-slate-500 dark:text-slate-400">${esc(label)}</span>`;
    }

    // ── Dropdown Toggle & Click-Outside Helpers ──────────────────
    function toggleLokasiFilter(e) {
        if (e) e.stopPropagation();
        if (!lokasiFilterMenu) return;
        const isHidden = lokasiFilterMenu.classList.contains('hidden');
        if (isHidden) {
            lokasiFilterMenu.classList.remove('hidden');
            lokasiFilterBtn?.classList.add('active');
            lokasiFilterBtn?.setAttribute('aria-expanded', 'true');
        } else {
            closeLokasiFilter();
        }
    }

    function closeLokasiFilter() {
        if (lokasiFilterMenu && !lokasiFilterMenu.classList.contains('hidden')) {
            lokasiFilterMenu.classList.add('hidden');
            lokasiFilterBtn?.classList.remove('active');
            lokasiFilterBtn?.setAttribute('aria-expanded', 'false');
        }
    }

    lokasiFilterBtn?.addEventListener('click', toggleLokasiFilter);

    dropdownJenisItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.stopPropagation();
            const val = (this.getAttribute('data-jenis') || '').toLowerCase();

            currentJenis = val;
            currentPage = 1;

            dropdownJenisItems.forEach(i => {
                const iVal = (i.getAttribute('data-jenis') || '').toLowerCase();
                const isSelected = iVal === currentJenis;
                i.classList.toggle('active', isSelected);
                const check = i.querySelector('.dash-period-check');
                if (check) check.classList.toggle('hidden', !isSelected);
            });

            closeLokasiFilter();
            updateActiveChip();
            fetchTable();
        });
    });

    document.addEventListener('click', function (e) {
        if (lokasiFilterWrap && !lokasiFilterWrap.contains(e.target)) {
            closeLokasiFilter();
        }
    });

    // ── Render baris tabel desktop ───────────────────────────────
    function rowHtml(lok, nomor) {
        const eventsCount = parseInt(lok.events_count ?? 0, 10) || 0;
        const dipakai = eventsCount > 0;
        const hapusBtn = dipakai
            ? `<button type="button" class="btn btn-secondary btn-icon rounded-xl opacity-40 cursor-not-allowed"
                   style="width: 2.125rem; height: 2.125rem;" disabled
                   title="Tidak dapat dihapus — masih dipakai ${eventsCount} kegiatan">
                   <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
               </button>`
            : (!isMagang
                ? `<button type="button" class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                       style="width: 2.125rem; height: 2.125rem;"
                       data-reauth-action="${destroyBase}/${lok.id}"
                       data-reauth-id="${lok.id}"
                       data-reauth-label="${esc(lok.nama_lokasi)}"
                       onclick="event.stopPropagation(); ReauthModal.open(this)" title="Hapus Lokasi">
                       <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                   </button>`
                : '');

        return `<tr class="hover:bg-blue-50/40 dark:hover:bg-slate-800/40 transition-colors cursor-pointer group"
                    onclick="editLokasi(${lok.id})" title="Klik untuk melihat atau mengedit detail lokasi ini">
            <td class="py-3.5 px-4 text-xs text-slate-400 font-mono">${nomor}</td>
            <td class="py-3.5 px-4"><span class="font-semibold text-slate-800 dark:text-slate-100 group-hover:text-primary transition-colors block text-sm">${esc(lok.nama_lokasi)}</span></td>
            <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300"><span class="truncate max-w-[200px] inline-block">${esc(lok.alamat || '—')}</span></td>
            <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">${esc(lok.kecamatan || '—')}</td>
            <td class="py-3.5 px-4 whitespace-nowrap">${badgeJenis(lok.jenis_sasaran)}</td>
            <td class="py-3.5 px-4 text-xs font-semibold text-slate-700 dark:text-slate-200 whitespace-nowrap">${eventsCount}x Kegiatan</td>
            <td class="py-3.5 px-4" onclick="event.stopPropagation()">
                <div class="flex items-center justify-end gap-1.5">
                    <button type="button" class="btn btn-secondary btn-icon rounded-xl" style="width: 2.125rem; height: 2.125rem;"
                            onclick="event.stopPropagation(); editLokasi(${lok.id})" title="Edit Informasi Lokasi">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    ${hapusBtn}
                </div>
            </td>
        </tr>`;
    }

    // ── Render Card Mobile / PWA ──────────────────────────────────
    function cardHtml(lok) {
        const eventsCount = parseInt(lok.events_count ?? 0, 10) || 0;
        const dipakai = eventsCount > 0;
        const rawJenis = String(lok.jenis_sasaran || 'sekolah').toLowerCase();
        const labelJenis = (rawJenis === 'masyarakat' || rawJenis === 'komunitas')
            ? 'Masyarakat'
            : rawJenis.charAt(0).toUpperCase() + rawJenis.slice(1);

        const hapusBtn = dipakai
            ? `<button type="button" class="btn btn-secondary btn-icon rounded-xl opacity-40 cursor-not-allowed"
                   style="width: 2.125rem; height: 2.125rem;" disabled
                   title="Tidak dapat dihapus — masih dipakai ${eventsCount} kegiatan">
                   <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
               </button>`
            : (!isMagang
                ? `<button type="button" class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                       style="width: 2.125rem; height: 2.125rem;"
                       data-reauth-action="${destroyBase}/${lok.id}"
                       data-reauth-id="${lok.id}"
                       data-reauth-label="${esc(lok.nama_lokasi)}"
                       onclick="event.stopPropagation(); ReauthModal.open(this)" title="Hapus Lokasi">
                       <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                   </button>`
                : '');

        const kecHtml = lok.kecamatan
            ? `<div class="flex items-center gap-1.5 text-[11px] text-slate-400 ml-5">
                   <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                   </svg>
                   <span>Kecamatan ${esc(lok.kecamatan)}</span>
               </div>`
            : '';

        return `
        <div class="lokasi-card p-4 hover:bg-blue-50/30 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
             onclick="editLokasi(${lok.id})"
             title="Klik untuk melihat atau mengedit detail lokasi ini">
            <div class="flex items-start justify-between gap-2.5">
                <div class="flex items-start gap-3 min-w-0 flex-1">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary border border-blue-100 dark:border-blue-900/50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-snug break-words">
                            ${esc(lok.nama_lokasi)}
                        </h3>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                ${esc(labelJenis)}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-primary">
                                <svg class="w-3 h-3 text-primary/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                ${eventsCount}x Kegiatan
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0" onclick="event.stopPropagation()">
                    <button type="button" class="btn btn-secondary btn-icon rounded-xl" style="width: 2.125rem; height: 2.125rem;"
                            onclick="event.stopPropagation(); editLokasi(${lok.id})" title="Edit Informasi Lokasi">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    ${hapusBtn}
                </div>
            </div>
            <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex flex-col gap-1 text-xs text-slate-500 dark:text-slate-400">
                <div class="flex items-start gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <span class="line-clamp-2">${esc(lok.alamat || '—')}</span>
                </div>
                ${kecHtml}
            </div>
        </div>`;
    }

    // ── Render pagination window ±2 ──────────────────────────────
    function pageWindow(current, last) {
        const pages = new Set([1, last]);
        for (let p = current - 2; p <= current + 2; p++) {
            if (p >= 1 && p <= last) pages.add(p);
        }
        return Array.from(pages).sort((a, b) => a - b);
    }

    function renderPagination(meta) {
        if (!paginationBox) return;
        const cur = meta.current_page, last = meta.last_page;
        if (last <= 1) { paginationBox.innerHTML = ''; return; }

        let btns = cur > 1
            ? `<button type="button" data-page="${cur - 1}" class="page-btn rounded-lg">‹</button>`
            : `<span class="page-btn rounded-lg opacity-40 cursor-not-allowed">‹</span>`;

        let prev = 0;
        pageWindow(cur, last).forEach(p => {
            if (p - prev > 1) btns += `<span class="px-1 text-xs text-slate-400">…</span>`;
            btns += p === cur
                ? `<span class="page-btn rounded-lg active">${p}</span>`
                : `<button type="button" data-page="${p}" class="page-btn rounded-lg">${p}</button>`;
            prev = p;
        });

        btns += cur < last
            ? `<button type="button" data-page="${cur + 1}" class="page-btn rounded-lg">›</button>`
            : `<span class="page-btn rounded-lg opacity-40 cursor-not-allowed">›</span>`;

        const from = meta.from ?? 0, to = meta.to ?? 0;
        paginationBox.innerHTML = `
            <div class="px-5 py-3.5 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                <p class="text-xs text-slate-400">Menampilkan ${from}–${to} dari ${meta.total} lokasi</p>
                <div class="flex gap-1.5 items-center">${btns}</div>
            </div>`;
    }

    // ── Fetch utama ──────────────────────────────────────────────
    async function fetchTable() {
        const seq = ++fetchSeq;
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-xs text-slate-400">Memuat data lokasi…</td></tr>`;
        }
        if (mobileList) {
            mobileList.innerHTML = `<div class="py-10 text-center text-xs text-slate-400">Memuat data lokasi…</div>`;
        }
        const params = new URLSearchParams({ ajax: '1', page: String(currentPage) });
        if (currentSearch) params.set('search', currentSearch);
        if (currentJenis) params.set('jenis_sasaran', currentJenis);

        try {
            const res = await fetch(`${indexUrl}?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('fetch gagal');
            const json = await res.json();
            if (seq !== fetchSeq) return; // abaikan respons basi
            const rows = json.data || [];
            const meta = json.meta || { current_page: 1, last_page: 1, from: 0, to: 0, total: rows.length };
            const startNo = (meta.from ?? 1);

            if (tbody) {
                tbody.innerHTML = rows.length
                    ? rows.map((l, i) => rowHtml(l, startNo + i)).join('')
                    : `<tr><td colspan="7" class="py-12 text-center">
                           <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Tidak ada lokasi yang cocok</p>
                           <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci atau jenis sasaran lain</p>
                           <button type="button" class="btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary btnResetFilterAjax">Reset Filter Pencarian</button>
                       </td></tr>`;
            }

            if (mobileList) {
                mobileList.innerHTML = rows.length
                    ? rows.map(l => cardHtml(l)).join('')
                    : `<div class="py-12 px-4 text-center">
                           <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                               <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                               </svg>
                           </div>
                           <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Tidak ada lokasi yang cocok</p>
                           <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci atau jenis sasaran lain</p>
                           <button type="button" class="btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary btnResetFilterAjax">Reset Filter</button>
                       </div>`;
            }

            document.querySelectorAll('.btnResetFilterAjax').forEach(btn => {
                btn.addEventListener('click', resetAllFilters);
            });

            renderPagination(meta);
            if (totalCountEl) {
                totalCountEl.textContent = `${meta.total} lokasi terdaftar · dipakai sebagai master combobox di form Kegiatan`;
            }
        } catch (e) {
            if (seq !== fetchSeq) return;
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="7" class="py-12 text-center">
                    <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Gagal memuat data</p>
                    <p class="text-xs text-slate-400 mt-1">Periksa koneksi lalu coba lagi — data sebelumnya tidak hilang.</p>
                    <button type="button" class="btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary btnRetryFetch">Coba Lagi</button>
                </td></tr>`;
            }
            if (mobileList) {
                mobileList.innerHTML = `<div class="py-12 px-4 text-center">
                    <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Gagal memuat data</p>
                    <p class="text-xs text-slate-400 mt-1">Periksa koneksi lalu coba lagi.</p>
                    <button type="button" class="btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary btnRetryFetch">Coba Lagi</button>
                </div>`;
            }
            document.querySelectorAll('.btnRetryFetch').forEach(btn => {
                btn.addEventListener('click', fetchTable);
            });
        }
    }

    // ── Chip + reset ─────────────────────────────────────────────
    function updateActiveChip() {
        if (!activeChip || !activeChipText) return;
        const labels = [];
        if (currentJenis && jenisLabelMap[currentJenis]) {
            labels.push(jenisLabelMap[currentJenis]);
        }
        if (currentSearch) {
            labels.push(`"${currentSearch}"`);
        }
        if (labels.length > 0) {
            activeChipText.textContent = labels.join(' · ');
            activeChip.classList.remove('hidden');
        } else {
            activeChip.classList.add('hidden');
        }
        if (lokasiFilterDot) {
            lokasiFilterDot.classList.toggle('hidden', !currentJenis);
        }
    }

    function resetAllFilters() {
        currentSearch = '';
        currentJenis  = '';
        currentPage   = 1;
        if (searchInput) searchInput.value = '';
        dropdownJenisItems.forEach(i => {
            const val = (i.getAttribute('data-jenis') || '').toLowerCase();
            const isSelected = val === '';
            i.classList.toggle('active', isSelected);
            const check = i.querySelector('.dash-period-check');
            if (check) check.classList.toggle('hidden', !isSelected);
        });
        closeLokasiFilter();
        updateActiveChip();
        fetchTable();
    }

    // ── Search: debounce 300ms, ikut pindah halaman via server ───
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                currentSearch = this.value.trim();
                currentPage = 1;
                updateActiveChip();
                fetchTable();
            }, 300);
        });
        // Enter: langsung cari tanpa menunggu debounce
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimer);
                currentSearch = this.value.trim();
                currentPage = 1;
                updateActiveChip();
                fetchTable();
            }
        });
    }

    btnClearFilter?.addEventListener('click', resetAllFilters);

    // ── Klik tombol halaman (delegasi, tanpa reload) ─────────────
    paginationBox?.addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-page]');
        if (!btn) return;
        e.preventDefault();
        currentPage = parseInt(btn.dataset.page, 10) || 1;
        fetchTable();
        const scrollTarget = document.querySelector('.glass') || document.getElementById('lokasiTable');
        scrollTarget?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Initial: hormati query URL bila ada (server sudah me-rendernya)
    updateActiveChip();

    // ── Modal Popup Logic (Tambah & Edit Detail Lokasi) ──────────
    const overlay     = document.getElementById('lokasiModalOverlay');
    const form        = document.getElementById('lokasiForm');
    const title       = document.getElementById('lokasiModalTitle');
    const methodInput = document.getElementById('lokasiFormMethod');
    const storeUrl    = "{{ route('operator.lokasi.store') }}";

    function openModal(mode, data) {
        if (mode === 'edit' && data) {
            title.textContent = 'Edit Detail Lokasi';
            form.action = "{{ url('operator/lokasi') }}/" + data.id;
            methodInput.value = 'PUT';
            document.getElementById('lokasiFormId').value = data.id;
            document.getElementById('fl_nama').value = data.nama_lokasi || '';
            document.getElementById('fl_alamat').value = data.alamat || '';
            document.getElementById('fl_kecamatan').value = data.kecamatan || '';
            document.getElementById('fl_jenis').value = data.jenis_sasaran || 'sekolah';
        } else {
            title.textContent = 'Tambah Lokasi Baru';
            form.action = storeUrl;
            methodInput.value = 'POST';
            form.reset();
            document.getElementById('lokasiFormId').value = '';
            document.getElementById('fl_jenis').value = 'sekolah';
        }
        overlay.classList.add('active');
        setTimeout(() => document.getElementById('fl_nama')?.focus(), 150);
    }

    function closeModal() {
        overlay.classList.remove('active');
    }

    document.getElementById('btnTambahLokasi')?.addEventListener('click', () => openModal('create'));
    document.getElementById('btnTambahLokasiEmpty')?.addEventListener('click', () => openModal('create'));
    document.getElementById('lokasiModalClose')?.addEventListener('click', closeModal);
    document.getElementById('lokasiModalCancelBtn')?.addEventListener('click', closeModal);

    overlay?.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            closeModal();
        }
    });

    window.editLokasi = async function (id) {
        try {
            const res = await fetch("{{ url('operator/lokasi') }}/" + id + "/edit", {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error();
            const data = await res.json();
            openModal('edit', data);
        } catch (e) {
            alert('Gagal memuat data lokasi.');
        }
    };
})();
</script>
@endpush
