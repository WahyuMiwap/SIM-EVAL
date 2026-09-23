@extends('layouts.app')

@section('title', 'Bank Soal')
@section('page-title', 'Bank Soal')
@section('page-subtitle', 'Kelola paket instrumen tes Pre-Test dan Post-Test kegiatan P2M')

@php
    $fSearch = $search ?? '';
    $fTema   = $tema ?? '';
@endphp

@section('content')

{{-- ── Table Card (Soft Rounded-2xl — Seragam dengan Operator / Kegiatan / Lokasi) ── --}}
<div class="glass mb-6 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs overflow-visible" style="padding: 0;">

    {{-- Card Header Soft --}}
    <div class="kg-header flex items-center justify-between p-4 md:p-5 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 truncate">Daftar Paket Instrumen Soal</h2>
                <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5 truncate" id="totalCount">{{ $bankSoal->total() ?? 0 }} paket terdaftar &middot; instrumen evaluasi pre-test dan post-test P2M</p>
            </div>
        </div>
        <a href="{{ route('operator.bank-soal.create') }}" 
           class="btn btn-primary rounded-xl shadow-xs inline-flex items-center justify-center gap-1.5 flex-shrink-0 kg-btn-add" 
           id="btnTambahSoal"
           title="Tambah Paket Soal">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="kg-btn-add-label hidden sm:inline">Tambah Paket Soal</span>
        </a>
    </div>

    {{-- ── Filter Bar Soft (Persis Kegiatan & Staf) ───────────── --}}
    <div class="kg-filterbar relative z-20 p-3.5 md:p-4 bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 flex-wrap">
        {{-- Search Input with SVG Icon --}}
        <div class="kg-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="kg-search-icon" width="14" height="14" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="filterSoal" type="text" class="kg-search-input rounded-xl border border-slate-200/70 dark:border-slate-700"
                   placeholder="Cari nama paket soal, tema..."
                   value="{{ $fSearch }}">
        </div>

        {{-- Filter Tema Materi (Tombol Icon Filter di Sisi Kanan Searchbar) --}}
        <div class="dash-period-dropdown-wrap relative" id="bankSoalFilterWrap">
            <button type="button" 
                    id="bankSoalFilterBtn" 
                    class="kg-filter-icon-btn {{ (!empty($fTema)) ? 'active' : '' }}" 
                    aria-haspopup="true" 
                    aria-expanded="false" 
                    title="Filter tema materi sosialisasi">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span id="bankSoalActiveDot" class="kg-filter-active-dot {{ empty($fTema) ? 'hidden' : '' }}"></span>
            </button>

            {{-- Floating Dropdown Menu --}}
            <div id="bankSoalFilterMenu" class="dash-period-menu hidden" role="menu" style="width: 280px; max-height: 420px; overflow-y: auto;">
                <div class="dash-period-section-label">Tema Materi Sosialisasi</div>

                <button type="button" class="dash-period-item filter-tema-item {{ empty($fTema) ? 'active' : '' }}" data-tema="" role="menuitem">
                    <div class="flex flex-col text-left">
                        <span class="dash-period-item-title">Semua Tema Materi</span>
                        <span class="dash-period-item-sub">Seluruh topik sosialisasi P2M</span>
                    </div>
                    <svg class="dash-period-check w-4 h-4 text-primary {{ empty($fTema) ? '' : 'hidden' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                @if(!empty($temas) && count($temas) > 0)
                    @foreach($temas as $t)
                    <button type="button" class="dash-period-item filter-tema-item {{ $fTema === $t ? 'active' : '' }}" data-tema="{{ $t }}" role="menuitem">
                        <div class="flex flex-col text-left">
                            <span class="dash-period-item-title">{{ $t }}</span>
                            <span class="dash-period-item-sub">Materi spesifik evaluasi P2M</span>
                        </div>
                        <svg class="dash-period-check w-4 h-4 text-primary {{ $fTema === $t ? '' : 'hidden' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Active Filter Chip --}}
        <div class="kg-active-chip {{ (empty($fTema) && empty($fSearch)) ? 'hidden' : '' }}" id="activeChip">
            <span id="activeChipText"></span>
            <button type="button" id="btnClearFilter" class="kg-chip-clear" aria-label="Hapus filter" title="Reset filter">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Table & Cards Soft ─────────────────────────────────── --}}
    @if($bankSoal->isEmpty())
    <div class="empty-state py-12 text-center">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="font-semibold text-sm text-slate-700 dark:text-slate-200">Belum ada paket soal ditemukan</p>
        <p class="text-xs text-slate-400 mt-0.5">Mulai dengan membuat paket instrumen tes untuk kegiatan sosialisasi</p>
        <a href="{{ route('operator.bank-soal.create') }}" class="btn btn-primary btn-sm rounded-xl mt-3 shadow-xs inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Paket Soal</span>
        </a>
    </div>
    @else
    {{-- Tampilan Desktop: Tabel Data (> 768px) --}}
    <div class="kg-desktop-table-wrap" style="min-height: 380px;">
        <table class="data-table w-full text-left" id="bankSoalTable">
            <thead>
                <tr>
                    <th class="py-3 px-4 text-center" style="width: 50px;">No</th>
                    <th class="py-3 px-4">Nama Paket Soal & Rincian</th>
                    <th class="py-3 px-4 text-center" style="width: 130px;">Jumlah Soal</th>
                    <th class="py-3 px-4" style="width: 150px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="bankSoalTbody" class="divide-y divide-slate-100 dark:divide-slate-800/70">
            @foreach($bankSoal as $index => $item)
            @php
                $rawTema   = strtolower($item->tema ?? '');
                $searchStr = strtolower(($item->nama_paket ?? '') . ' ' . $rawTema . ' ' . ($item->deskripsi ?? ''));
                $dipakai   = $item->events_count ?? 0;
            @endphp
            <tr data-search="{{ $searchStr }}"
                data-tema="{{ $rawTema }}"
                class="soal-row hover:bg-blue-50/40 dark:hover:bg-slate-800/40 transition-colors cursor-pointer group"
                onclick="window.location='{{ route('operator.bank-soal.detail', $item->id) }}'"
                title="Klik untuk membuka butir soal paket ini">
                
                {{-- No --}}
                <td class="py-3.5 px-4 text-center text-xs text-slate-400 font-mono">
                    {{ ($bankSoal->currentPage() - 1) * $bankSoal->perPage() + $index + 1 }}
                </td>

                {{-- Nama Paket & Metadata --}}
                <td class="py-3.5 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary border border-blue-100 dark:border-blue-900/50 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-800 dark:text-slate-100 group-hover:text-primary transition-colors block text-sm">
                                {{ $item->nama_paket }}
                            </span>
                            <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5 flex-wrap">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Dibuat: {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMM Y') }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1.5 flex-wrap">
                                @if(!empty($item->tema))
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded font-medium text-[10px] bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/50 dark:border-slate-700">{{ $item->tema }}</span>
                                @endif
                                @if($dipakai > 0)
                                    @if(!empty($item->tema))
                                        <span class="text-slate-300 dark:text-slate-700">&middot;</span>
                                    @endif
                                    <span title="Paket ini dipakai {{ $dipakai }} kegiatan">Dipakai {{ $dipakai }} kegiatan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>

                {{-- Jumlah Soal --}}
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary bg-blue-50/70 dark:bg-blue-950/40 px-2.5 py-1 rounded-lg border border-blue-100 dark:border-blue-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $item->soal_count ?? 0 }} Soal</span>
                    </div>
                </td>

                {{-- Aksi --}}
                <td class="py-3.5 px-4" onclick="event.stopPropagation()">
                    <div class="flex items-center justify-end gap-1.5">
                        {{-- Pratinjau / Butir Soal --}}
                        <a href="{{ route('operator.bank-soal.detail', $item->id) }}"
                           class="btn btn-secondary btn-icon rounded-xl"
                           style="width: 2.125rem; height: 2.125rem;"
                           title="Lihat Butir Soal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>

                        {{-- Edit Paket --}}
                        <a href="{{ route('operator.bank-soal.edit', $item->id) }}"
                           class="btn btn-secondary btn-icon rounded-xl"
                           style="width: 2.125rem; height: 2.125rem;"
                           title="Edit Informasi Paket Soal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>

                        {{-- Hapus Paket --}}
                        @if($dipakai > 0)
                        <button type="button"
                                class="btn btn-secondary btn-icon rounded-xl opacity-40 cursor-not-allowed"
                                style="width: 2.125rem; height: 2.125rem;"
                                disabled
                                title="Tidak dapat dihapus — masih dipakai {{ $dipakai }} kegiatan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        @elseif(auth()->user()?->role !== 'magang')
                        <button type="button"
                                class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                                style="width: 2.125rem; height: 2.125rem;"
                                data-reauth-action="{{ route('operator.bank-soal.destroy', $item->id) }}"
                                data-reauth-label="Paket Soal: {{ $item->nama_paket }}"
                                data-reauth-id="{{ $item->id }}"
                                onclick="event.stopPropagation(); ReauthModal.open(this)"
                                title="Hapus Paket Soal">
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
    <div class="kg-mobile-list divide-y divide-slate-100 dark:divide-slate-800" id="bankSoalMobileList">
        @foreach($bankSoal as $item)
        @php
            $rawTema   = strtolower($item->tema ?? '');
            $searchStr = strtolower(($item->nama_paket ?? '') . ' ' . $rawTema . ' ' . ($item->deskripsi ?? ''));
            $dipakai   = $item->events_count ?? 0;
        @endphp
        <div data-search="{{ $searchStr }}"
             data-tema="{{ $rawTema }}"
             class="soal-card p-4 hover:bg-blue-50/30 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
             onclick="window.location='{{ route('operator.bank-soal.detail', $item->id) }}'">
            
            {{-- Header: Icon + Nama Paket + Action Buttons --}}
            <div class="flex items-start justify-between gap-2.5">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary border border-blue-100 dark:border-blue-900/50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-snug break-words">
                            {{ $item->nama_paket }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Dibuat: {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMM Y') }}
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1 flex-shrink-0" onclick="event.stopPropagation()">
                    <a href="{{ route('operator.bank-soal.detail', $item->id) }}"
                       class="btn btn-secondary btn-icon rounded-xl"
                       style="width: 2rem; height: 2rem;"
                       title="Lihat Butir Soal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('operator.bank-soal.edit', $item->id) }}"
                       class="btn btn-secondary btn-icon rounded-xl"
                       style="width: 2rem; height: 2rem;"
                       title="Edit Informasi Paket Soal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    @if($dipakai > 0)
                    <button type="button"
                            class="btn btn-secondary btn-icon rounded-xl opacity-40 cursor-not-allowed"
                            style="width: 2rem; height: 2rem;"
                            disabled
                            title="Tidak dapat dihapus — masih dipakai {{ $dipakai }} kegiatan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @elseif(auth()->user()?->role !== 'magang')
                    <button type="button"
                            class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                            style="width: 2rem; height: 2rem;"
                            data-reauth-action="{{ route('operator.bank-soal.destroy', $item->id) }}"
                            data-reauth-label="Paket Soal: {{ $item->nama_paket }}"
                            data-reauth-id="{{ $item->id }}"
                            onclick="event.stopPropagation(); ReauthModal.open(this)"
                            title="Hapus Paket Soal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Badges Row: Tema, Jumlah Soal, Pemakaian --}}
            <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">

                @if(!empty($item->tema))
                <span class="inline-flex items-center px-2 py-0.5 rounded-md font-medium text-[11px] bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {{ $item->tema }}
                </span>
                @endif

                <span class="inline-flex items-center gap-1 font-semibold text-[11px] text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $item->soal_count ?? 0 }} Soal
                </span>

                @if($dipakai > 0)
                <span class="text-slate-300 dark:text-slate-700">&middot;</span>
                <span class="text-[11px] text-slate-400">Dipakai {{ $dipakai }} kegiatan</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination Soft (Mirip Lokasi & Kegiatan) --}}
    @if($bankSoal->hasPages())
    <div class="px-5 py-3.5 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
        <p class="text-xs text-slate-400">
            Menampilkan {{ $bankSoal->firstItem() }}–{{ $bankSoal->lastItem() }} dari {{ $bankSoal->total() }} paket soal
        </p>
        <div class="flex gap-1.5">
            @if($bankSoal->onFirstPage())
                <span class="page-btn rounded-lg opacity-40 cursor-not-allowed">‹</span>
            @else
                <a href="{{ $bankSoal->previousPageUrl() }}" class="page-btn rounded-lg">‹</a>
            @endif
            @foreach($bankSoal->getUrlRange(max(1, $bankSoal->currentPage()-2), min($bankSoal->lastPage(), $bankSoal->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-btn rounded-lg {{ $page == $bankSoal->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($bankSoal->hasMorePages())
                <a href="{{ $bankSoal->nextPageUrl() }}" class="page-btn rounded-lg">›</a>
            @else
                <span class="page-btn rounded-lg opacity-40 cursor-not-allowed">›</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

{{-- ── Styles Scoped (Mirip Lokasi & Kegiatan) ───────────────── --}}
<style>
/* Card Header */
.kg-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.125rem 1.375rem;
    border-bottom: 1px solid var(--border);
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

.dash-period-menu {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    width: 280px;
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
        width: 280px;
        max-width: calc(100vw - 2rem);
    }
}
</style>

@push('scripts')
<script>
(function () {
    // ── Client-Side Live Filter Engine (Seragam dengan Kegiatan & Tata Kelola Pengguna) ──
    let currentSearch = String(@json($fSearch) || '').toLowerCase();
    let currentTema   = String(@json($fTema) || '').toLowerCase();

    const searchInput         = document.getElementById('filterSoal');
    const totalCountEl        = document.getElementById('totalCount');
    const filterWrap          = document.getElementById('bankSoalFilterWrap');
    const filterBtn           = document.getElementById('bankSoalFilterBtn');
    const filterMenu          = document.getElementById('bankSoalFilterMenu');
    const filterActiveDot     = document.getElementById('bankSoalActiveDot');
    const filterTemaItems     = document.querySelectorAll('.filter-tema-item');

    const activeChip          = document.getElementById('activeChip');
    const activeChipText      = document.getElementById('activeChipText');
    const btnClearFilter      = document.getElementById('btnClearFilter');

    // ── Dropdown Toggle & Close Listeners ────────────────────────
    filterBtn?.addEventListener('click', function (e) {
        e.stopPropagation();
        const isHidden = filterMenu?.classList.contains('hidden');
        filterMenu?.classList.toggle('hidden', !isHidden);
        filterBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
    });

    document.addEventListener('click', function (e) {
        if (filterMenu && !filterMenu.classList.contains('hidden')) {
            if (!filterWrap?.contains(e.target)) {
                filterMenu.classList.add('hidden');
                filterBtn?.setAttribute('aria-expanded', 'false');
            }
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && filterMenu && !filterMenu.classList.contains('hidden')) {
            filterMenu.classList.add('hidden');
            filterBtn?.setAttribute('aria-expanded', 'false');
        }
    });

    // ── Dropdown Tema Selection ──────────────────────────────────
    filterTemaItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            currentTema = (this.getAttribute('data-tema') || '').toLowerCase();
            updateDropdownUI();
            filterMenu?.classList.add('hidden');
            filterBtn?.setAttribute('aria-expanded', 'false');
            updateActiveChip();
            applyFilter();
        });
    });

    function updateDropdownUI() {
        // Tema items checkmark
        filterTemaItems.forEach(item => {
            const isSelected = (item.getAttribute('data-tema') || '').toLowerCase() === currentTema;
            item.classList.toggle('active', isSelected);
            const check = item.querySelector('.dash-period-check');
            if (check) check.classList.toggle('hidden', !isSelected);
        });

        // Active indicator on button
        const isFiltered = currentTema !== '';
        filterBtn?.classList.toggle('active', isFiltered);
        filterActiveDot?.classList.toggle('hidden', !isFiltered);
    }

    // ── Live Filter Table & Mobile Cards ─────────────────────────
    function applyFilter() {
        const prevScrollY = window.scrollY;
        const rows = document.querySelectorAll('#bankSoalTbody tr.soal-row');
        const cards = document.querySelectorAll('#bankSoalMobileList > div.soal-card');
        let visibleCount = 0;

        const checkMatch = (el) => {
            const searchContent = (el.getAttribute('data-search') || '').toLowerCase();
            const rowTema       = (el.getAttribute('data-tema') || '').toLowerCase();

            const matchSearch = !currentSearch || searchContent.includes(currentSearch);
            const matchTema   = !currentTema || rowTema === currentTema;

            return matchSearch && matchTema;
        };

        rows.forEach(row => {
            if (checkMatch(row)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        cards.forEach(card => {
            if (checkMatch(card)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Update counter teks
        if (totalCountEl) {
            totalCountEl.textContent = `${visibleCount} paket ditemukan \u00B7 instrumen evaluasi pre-test dan post-test P2M`;
        }

        // Tampilkan/sembunyikan pesan jika tidak ada hasil pada tabel
        let emptySearchRow = document.getElementById('soalSearchEmpty');
        if (visibleCount === 0 && rows.length > 0) {
            if (!emptySearchRow) {
                emptySearchRow = document.createElement('tr');
                emptySearchRow.id = 'soalSearchEmpty';
                emptySearchRow.innerHTML = `
                    <td colspan="4" class="py-12 text-center text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Tidak ada paket soal yang cocok</p>
                        <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci atau tema materi lain</p>
                        <button type="button" id="btnResetFilter" class="btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary shadow-2xs">
                            Reset Filter Pencarian
                        </button>
                    </td>
                `;
                document.getElementById('bankSoalTbody')?.appendChild(emptySearchRow);
                document.getElementById('btnResetFilter')?.addEventListener('click', resetAllFilters);
            } else {
                emptySearchRow.style.display = '';
            }
        } else if (emptySearchRow) {
            emptySearchRow.style.display = 'none';
        }

        // Tampilkan/sembunyikan pesan jika tidak ada hasil pada kartu mobile
        let emptySearchMobile = document.getElementById('soalSearchMobile');
        if (visibleCount === 0 && cards.length > 0) {
            if (!emptySearchMobile) {
                emptySearchMobile = document.createElement('div');
                emptySearchMobile.id = 'soalSearchMobile';
                emptySearchMobile.className = 'py-12 text-center text-slate-400 p-4';
                emptySearchMobile.innerHTML = `
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Tidak ada paket soal yang cocok</p>
                    <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci atau tema materi lain</p>
                    <button type="button" id="btnResetFilterMobile" class="btn btn-secondary btn-sm rounded-xl mt-3 text-xs font-semibold text-primary shadow-2xs">
                        Reset Filter Pencarian
                    </button>
                `;
                document.getElementById('bankSoalMobileList')?.appendChild(emptySearchMobile);
                document.getElementById('btnResetFilterMobile')?.addEventListener('click', resetAllFilters);
            } else {
                emptySearchMobile.style.display = '';
            }
        } else if (emptySearchMobile) {
            emptySearchMobile.style.display = 'none';
        }

        updateDropdownUI();
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

    // ── Update Chip Filter Aktif ─────────────────────────────────
    function updateActiveChip() {
        if (!activeChip || !activeChipText) return;
        const labels = [];
        if (currentTema) {
            labels.push(currentTema);
        }
        if (currentSearch) {
            labels.push(`"${currentSearch}"`);
        }

        if (labels.length > 0) {
            activeChipText.textContent = labels.join(' \u00B7 ');
            activeChip.classList.remove('hidden');
        } else {
            activeChip.classList.add('hidden');
        }
    }

    function resetAllFilters() {
        currentSearch = '';
        currentTema   = '';
        if (searchInput) searchInput.value = '';
        updateDropdownUI();
        updateActiveChip();
        applyFilter();
    }

    // ── Input Search Event ───────────────────────────────────────
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearch = this.value.trim().toLowerCase();
            updateActiveChip();
            applyFilter();
        });
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') e.preventDefault();
        });
    }

    btnClearFilter?.addEventListener('click', resetAllFilters);

    // Initial filter if params loaded
    updateDropdownUI();
    if (currentSearch || currentTema) {
        updateActiveChip();
        applyFilter();
    }
})();
</script>
@endpush

@endsection
