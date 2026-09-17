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
        'all'  => 'Semua Waktu', '1d' => '1 Hari Terakhir', '7d' => '7 Hari Terakhir',
        '30d'  => '30 Hari Terakhir', '90d' => '3 Bulan Terakhir',
        '180d' => '6 Bulan Terakhir', '365d' => '1 Tahun Terakhir', 'custom' => 'Rentang Kustom',
    ];
@endphp

@section('content')

{{-- ── Table Card ────────────────────────────────────────────── --}}
<div class="glass animate-fade-in" style="padding: 0; overflow: visible;">

    {{-- Card Header --}}
    <div class="kg-header">
        <div>
            <p class="kg-header-title">Semua Kegiatan</p>
            <p class="kg-header-count" id="totalCount">{{ $kegiatan->total() ?? 0 }} kegiatan ditemukan</p>
        </div>
        <button class="btn btn-primary btn-sm" id="btnTambahKegiatan">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kegiatan
        </button>
    </div>

    {{-- ── Filter Bar ────────────────────────────────────────── --}}
    <div class="kg-filterbar">
        {{-- Search --}}
        <div class="kg-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="kg-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="kgSearch" type="text" class="kg-search-input"
                   placeholder="Cari nama kegiatan..."
                   value="{{ $fSearch }}">
        </div>

        {{-- Period Dropdown --}}
        <div class="kg-dropdown-wrap" id="periodDropdownWrap">
            <button class="kg-dropdown-trigger {{ $fPeriod !== 'all' ? 'open' : '' }}" id="periodTrigger">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span id="periodLabel">{{ $periodLabels[$fPeriod] ?? 'Semua Waktu' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 kg-caret" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div class="kg-dropdown-panel {{ $fPeriod !== 'all' ? 'open' : '' }}" id="periodPanel">
                <div class="kg-dropdown-list">
                    @foreach(['all' => 'Semua Waktu', '1d' => '1 Hari Terakhir', '7d' => '7 Hari Terakhir', '30d' => '30 Hari Terakhir', '90d' => '3 Bulan Terakhir', '180d' => '6 Bulan Terakhir', '365d' => '1 Tahun Terakhir'] as $val => $label)
                    <button class="kg-dropdown-item {{ $fPeriod === $val ? 'active' : '' }}"
                            data-period="{{ $val }}" data-label="{{ $label }}">{{ $label }}</button>
                    @endforeach
                    <div class="kg-dropdown-divider"></div>
                    {{-- Custom Date Range --}}
                    <div class="kg-custom-range">
                        <p class="kg-custom-range-label">Rentang Kustom</p>
                        <div class="kg-date-inputs">
                            <div class="kg-date-field">
                                <label>Dari</label>
                                <input type="date" id="dateFrom" class="kg-date-input" value="{{ $fFrom }}">
                            </div>
                            <div class="kg-date-sep">–</div>
                            <div class="kg-date-field">
                                <label>Sampai</label>
                                <input type="date" id="dateTo" class="kg-date-input" value="{{ $fTo }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mode Filter --}}
        <div class="kg-pill-group">
            <button class="kg-pill {{ $fMode === '' ? 'active' : '' }}" data-mode="">Semua</button>
            <button class="kg-pill {{ $fMode === 'digital' ? 'active' : '' }}" data-mode="digital">Digital</button>
            <button class="kg-pill {{ $fMode === 'kertas' ? 'active' : '' }}" data-mode="kertas">Kertas</button>
        </div>

        {{-- Active filter chip --}}
        <div class="kg-active-chip {{ $fPeriod === 'all' ? 'hidden' : '' }}" id="activeChip">
            <span id="activeChipText">{{ $periodLabels[$fPeriod] ?? '' }}</span>
            <button id="btnClearPeriod" class="kg-chip-clear">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Table ─────────────────────────────────────────────── --}}
    @if($kegiatan->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--text-muted)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="font-semibold text-sm" style="color:var(--text-secondary)">Belum ada kegiatan</p>
        <p class="text-xs" style="color:var(--text-muted)">Tambahkan kegiatan pertama untuk memulai</p>
        <button class="btn btn-primary btn-sm mt-2" id="btnTambahKegiatanEmpty">Tambah Sekarang</button>
    </div>
    @else
    <div style="overflow-x:auto;">
        <table class="data-table" id="kgTable">
            <thead>
                <tr>
                    <th>Nama Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Mode</th>
                    <th>Status</th>
                    <th>Peserta</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="kgTbody">
            @foreach($kegiatan as $k)
            <tr data-tanggal="{{ $k->tanggal ?? '' }}" data-mode="{{ $k->mode ?? '' }}" data-nama="{{ strtolower($k->nama_kegiatan) }}">
                {{-- Nama --}}
                <td>
                    <a href="{{ route('operator.kegiatan.detail', $k->id) }}"
                       class="font-semibold hover:underline" style="color:var(--text-primary); font-size:0.875rem;">
                        {{ $k->nama_kegiatan }}
                    </a>
                    <p class="font-semibold text-xs mt-0.5 tracking-wider" style="color:var(--text-muted);">{{ $k->kode_join ?? '—' }}</p>
                </td>
                {{-- Lokasi --}}
                <td style="color:var(--text-secondary); font-size:0.8125rem;">{{ $k->lokasi->nama_lokasi ?? '—' }}</td>
                {{-- Tanggal --}}
                <td style="white-space:nowrap; font-size:0.8125rem; color:var(--text-secondary)">
                    {{ isset($k->tanggal) ? \Carbon\Carbon::parse($k->tanggal)->format('d M Y') : '—' }}
                </td>
                {{-- Mode --}}
                <td>
                    @if(($k->mode ?? '') === 'digital')
                        <span class="badge badge-blue">Digital</span>
                    @else
                        <span class="badge badge-gray">Kertas</span>
                    @endif
                </td>
                {{-- Status --}}
                <td>
                    @php $s = $k->status ?? 'menunggu'; @endphp
                    @if($s === 'selesai')      <span class="badge badge-green">Selesai</span>
                    @elseif($s === 'pretest')  <span class="badge badge-cyan">Pre-Test</span>
                    @elseif($s === 'posttest') <span class="badge badge-purple">Post-Test</span>
                    @elseif($s === 'jeda')     <span class="badge badge-yellow">Jeda</span>
                    @else                      <span class="badge badge-gray">Menunggu</span>
                    @endif
                </td>
                {{-- Peserta --}}
                <td style="font-size:0.8125rem; font-weight:600; color:var(--text-secondary)">
                    {{ $k->jumlah_peserta ?? $k->peserta_count ?? 0 }}
                </td>
                {{-- Aksi --}}
                <td>
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{ route('operator.kegiatan.detail', $k->id) }}" class="btn btn-secondary btn-sm" title="Detail">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <button class="btn btn-secondary btn-sm" onclick="editKegiatan({{ $k->id }})" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button class="btn btn-sm kg-btn-danger"
                            data-reauth-action="{{ route('operator.kegiatan.destroy', $k->id) }}"
                            data-reauth-id="{{ $k->id }}"
                            data-reauth-label="{{ $k->nama_kegiatan }}"
                            onclick="ReauthModal.open(this)" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
         </div>
    </div>

    {{-- Pagination --}}
    @if($kegiatan->hasPages())
    <div class="px-5 py-3 flex items-center justify-between" style="border-top:1px solid var(--border);">
        <p class="text-xs" style="color:var(--text-muted)">
            Menampilkan {{ $kegiatan->firstItem() }}–{{ $kegiatan->lastItem() }} dari {{ $kegiatan->total() }}
        </p>
        <div class="flex gap-1">
            @if($kegiatan->onFirstPage())
                <span class="page-btn" style="opacity:.4;cursor:not-allowed;">‹</span>
            @else
                <a href="{{ $kegiatan->previousPageUrl() }}" class="page-btn">‹</a>
            @endif
            @foreach($kegiatan->getUrlRange(1, $kegiatan->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-btn {{ $page == $kegiatan->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($kegiatan->hasMorePages())
                <a href="{{ $kegiatan->nextPageUrl() }}" class="page-btn">›</a>
            @else
                <span class="page-btn" style="opacity:.4;cursor:not-allowed;">›</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>{{-- /glass --}}

{{-- ── MODAL: Tambah / Edit ─────────────────────────────────── --}}
<div class="modal-overlay" id="kegiatanModalOverlay">
    <div class="modal-box" id="kegiatanModalBox" data-store-url="{{ route('operator.kegiatan.store') }}">
        <div class="modal-header">
            <p class="modal-title" id="kegiatanModalTitle">Tambah Kegiatan</p>
            <button class="btn btn-secondary btn-icon" id="kegiatanModalClose">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="kegiatanForm" method="POST" action="{{ route('operator.kegiatan.store') }}">
            @csrf
            <input type="hidden" name="_method" id="kegiatanFormMethod" value="POST">
            <input type="hidden" name="id" id="kegiatanFormId">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label" for="f_nama">Nama Kegiatan <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nama_kegiatan" id="f_nama" class="form-input" placeholder="cth: Sosialisasi Anti Narkoba — SMAN 1" required>
                </div>
                <div class="form-group" style="position: relative;">
                    <label class="form-label" for="f_lokasi_search">Lokasi</label>
                    <div class="kg-combobox-wrap" id="lokasiComboboxWrap">
                        <div class="kg-combobox-input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="kg-combobox-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input type="text" id="f_lokasi_search" class="form-input kg-combobox-input" placeholder="Cari nama lokasi..." autocomplete="off">
                        </div>


                        {{-- Hidden inputs: ID lokasi terdaftar atau nama lokasi baru --}}
                        <input type="hidden" name="lokasi_id" id="f_lokasi_id">
                        <input type="hidden" name="nama_lokasi_baru" id="f_nama_lokasi_baru">

                        {{-- Dropdown Hasil Filter Instan --}}
                        <div class="kg-combobox-menu hidden" id="lokasiComboboxMenu">
                            <div class="kg-combobox-list" id="lokasiComboboxList"></div>
                        </div>
                    </div>
                    {{-- Hint tambah lokasi baru (muncul di bawah kolom jika tidak ada hasil) --}}
                    <div id="lokasiAddHint" class="kg-add-hint hidden">
                        Lokasi tidak ditemukan, tambahkan lokasi?&nbsp;<button type="button" class="kg-add-link" id="lokasiAddBtn">tambah</button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="f_tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="f_tanggal" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label" for="f_durasi">Durasi (menit)</label>
                    <input type="number" name="durasi_menit" id="f_durasi" class="form-input" value="30" min="5" max="180">
                </div>
                <div class="form-group">
                    <label class="form-label" for="f_mode">Mode Pelaksanaan</label>
                    <select name="mode" id="f_mode" class="form-input">
                        <option value="digital">Digital (Aplikasi)</option>
                        <option value="kertas">Kertas (OMR)</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label" for="f_catatan">Catatan (opsional)</label>
                    <textarea name="catatan" id="f_catatan" class="form-input" rows="2" placeholder="Tambahkan catatan singkat..."></textarea>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-2">
                <button type="button" class="btn btn-secondary" id="kegiatanModalCancelBtn">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kegiatan</button>
            </div>
        </form>
    </div>
</div>

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

/* Dropdown Panel */
.kg-dropdown-panel {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    z-index: 200;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    box-shadow: var(--shadow-lg);
    width: 240px;
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
.kg-dropdown-panel.open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.kg-dropdown-list { padding: 0.375rem; }
.kg-dropdown-item {
    display: block;
    width: 100%;
    text-align: left;
    padding: 0.475rem 0.75rem;
    font-size: 0.8125rem;
    color: var(--text-secondary);
    font-family: var(--font-sans);
    background: none;
    border: none;
    border-radius: var(--r-sm);
    cursor: pointer;
    transition: background 0.12s ease, color 0.12s ease;
    white-space: nowrap;
}
.kg-dropdown-item:hover { background: var(--bg-alt); color: var(--text-primary); }
.kg-dropdown-item.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }

.kg-dropdown-divider {
    height: 1px;
    background: var(--border);
    margin: 0.375rem 0;
}

/* Custom Date Range inside dropdown */
.kg-custom-range {
    padding: 0.625rem 0.75rem 0.5rem;
}
.kg-custom-range-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}
.kg-date-inputs {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}
.kg-date-field {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}
.kg-date-field label {
    font-size: 0.68rem;
    font-weight: 500;
    color: var(--text-muted);
}
.kg-date-input {
    background: var(--bg-alt);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    padding: 0.3rem 0.45rem;
    font-size: 0.75rem;
    color: var(--text-primary);
    font-family: var(--font-sans);
    width: 100%;
    outline: none;
    transition: border-color 0.15s;
}
.kg-date-input:focus { border-color: var(--primary); }
.kg-date-sep {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 1.1rem;
    flex-shrink: 0;
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
    // ── URL-based filter navigation ─────────────────────────────
    // Reads current URL params and navigates with new values applied.
    function navigate(overrides) {
        const params = new URLSearchParams(window.location.search);
        params.delete('page'); // reset pagination on filter change
        Object.entries(overrides).forEach(([k, v]) => {
            if (v === '' || v === null || v === undefined) {
                params.delete(k);
            } else {
                params.set(k, v);
            }
        });
        window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    }

    // ── Search: debounce 400ms then navigate ─────────────────────
    const searchInput = document.getElementById('kgSearch');
    if (searchInput) {
        let searchTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => navigate({ search: this.value.trim() }), 400);
        });
    }

    // ── Period dropdown open/close ────────────────────────────────
    let panelOpen = false;
    const periodTrigger = document.getElementById('periodTrigger');
    const periodPanel   = document.getElementById('periodPanel');

    if (periodTrigger) {
        periodTrigger.addEventListener('click', function () {
            panelOpen = !panelOpen;
            periodPanel.classList.toggle('open', panelOpen);
            periodTrigger.classList.toggle('open', panelOpen);
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const wrap = document.getElementById('periodDropdownWrap');
        if (wrap && !wrap.contains(e.target) && panelOpen) {
            panelOpen = false;
            periodPanel?.classList.remove('open');
            periodTrigger?.classList.remove('open');
        }
    });

    // ── Period items ──────────────────────────────────────────────
    document.querySelectorAll('.kg-dropdown-item[data-period]').forEach(btn => {
        btn.addEventListener('click', function () {
            const period = this.dataset.period;
            if (period !== 'custom') {
                navigate({ period });
            }
            // For 'custom', wait for date inputs
        });
    });

    // Custom date range inputs
    const dateFrom = document.getElementById('dateFrom');
    const dateTo   = document.getElementById('dateTo');
    if (dateFrom) {
        dateFrom.addEventListener('change', () => {
            navigate({ period: 'custom', date_from: dateFrom.value, date_to: dateTo?.value || '' });
        });
    }
    if (dateTo) {
        dateTo.addEventListener('change', () => {
            navigate({ period: 'custom', date_from: dateFrom?.value || '', date_to: dateTo.value });
        });
    }

    // ── Mode pills ────────────────────────────────────────────────
    document.querySelectorAll('.kg-pill[data-mode]').forEach(btn => {
        btn.addEventListener('click', function () {
            navigate({ mode: this.dataset.mode });
        });
    });

    // ── Clear period chip ─────────────────────────────────────────
    const btnClearPeriod = document.getElementById('btnClearPeriod');
    if (btnClearPeriod) {
        btnClearPeriod.addEventListener('click', () => {
            navigate({ period: null, date_from: null, date_to: null });
        });
    }

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
