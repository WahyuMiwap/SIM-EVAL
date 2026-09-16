@extends('layouts.app')

@section('title', 'Daftar Kegiatan')
@section('page-title', 'Daftar Kegiatan')
@section('page-subtitle', 'Kelola seluruh kegiatan sosialisasi P2M')

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
            <input id="kgSearch" type="text" class="kg-search-input" placeholder="Cari nama kegiatan..." oninput="filterTable()">
        </div>

        {{-- Period Dropdown --}}
        <div class="kg-dropdown-wrap" id="periodDropdownWrap">
            <button class="kg-dropdown-trigger" id="periodTrigger" onclick="togglePeriodDropdown()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span id="periodLabel">Semua Waktu</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 kg-caret" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Dropdown Panel --}}
            <div class="kg-dropdown-panel" id="periodPanel">
                <div class="kg-dropdown-list">
                    <button class="kg-dropdown-item active" onclick="setPeriod('all',    'Semua Waktu')">Semua Waktu</button>
                    <button class="kg-dropdown-item"        onclick="setPeriod('1d',     '1 Hari Terakhir')">1 Hari Terakhir</button>
                    <button class="kg-dropdown-item"        onclick="setPeriod('7d',     '7 Hari Terakhir')">7 Hari Terakhir</button>
                    <button class="kg-dropdown-item"        onclick="setPeriod('30d',    '30 Hari Terakhir')">30 Hari Terakhir</button>
                    <button class="kg-dropdown-item"        onclick="setPeriod('90d',    '3 Bulan Terakhir')">3 Bulan Terakhir</button>
                    <button class="kg-dropdown-item"        onclick="setPeriod('180d',   '6 Bulan Terakhir')">6 Bulan Terakhir</button>
                    <button class="kg-dropdown-item"        onclick="setPeriod('365d',   '1 Tahun Terakhir')">1 Tahun Terakhir</button>
                    <div class="kg-dropdown-divider"></div>
                    {{-- Custom Date Range --}}
                    <div class="kg-custom-range">
                        <p class="kg-custom-range-label">Rentang Kustom</p>
                        <div class="kg-date-inputs">
                            <div class="kg-date-field">
                                <label>Dari</label>
                                <input type="date" id="dateFrom" class="kg-date-input" onchange="setPeriod('custom','')">
                            </div>
                            <div class="kg-date-sep">–</div>
                            <div class="kg-date-field">
                                <label>Sampai</label>
                                <input type="date" id="dateTo" class="kg-date-input" onchange="setPeriod('custom','')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mode Filter --}}
        <div class="kg-pill-group">
            <button class="kg-pill active" onclick="setMode(this,'')">Semua</button>
            <button class="kg-pill" onclick="setMode(this,'digital')">Digital</button>
            <button class="kg-pill" onclick="setMode(this,'kertas')">Kertas</button>
        </div>

        {{-- Active filter chip (shows when period is set) --}}
        <div class="kg-active-chip hidden" id="activeChip">
            <span id="activeChipText"></span>
            <button onclick="clearPeriod()" class="kg-chip-clear">
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
                    <p class="font-mono text-xs mt-0.5" style="color:var(--text-muted);">{{ $k->kode_join ?? '—' }}</p>
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

        {{-- No result row (shown by JS) --}}
        <div id="kgNoResult" class="empty-state hidden" style="border-top:1px solid var(--border);">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--text-muted)">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="font-semibold text-sm" style="color:var(--text-secondary)">Tidak ada kegiatan ditemukan</p>
            <p class="text-xs" style="color:var(--text-muted)">Coba ubah filter atau kata kunci pencarian</p>
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
                <div class="form-group">
                    <label class="form-label" for="f_lokasi">Lokasi</label>
                    <div class="flex gap-1.5">
                        <select name="lokasi_id" id="f_lokasi" class="form-input">
                            <option value="">— Pilih Lokasi —</option>
                            @foreach($lokasiList ?? [] as $lok)
                                <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-secondary btn-sm flex-shrink-0" id="btnAddLokasiInline" title="Tambah Lokasi Baru">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
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
</style>

{{-- ── Scripts ──────────────────────────────────────────────── --}}
<script>
(function() {
    let currentPeriod = 'all';
    let currentMode   = '';
    let panelOpen     = false;

    // ── Dropdown open/close ──────────────────────────────────
    window.togglePeriodDropdown = function() {
        panelOpen = !panelOpen;
        document.getElementById('periodPanel').classList.toggle('open', panelOpen);
        document.getElementById('periodTrigger').classList.toggle('open', panelOpen);
    };

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('periodDropdownWrap');
        if (!wrap.contains(e.target) && panelOpen) {
            panelOpen = false;
            document.getElementById('periodPanel').classList.remove('open');
            document.getElementById('periodTrigger').classList.remove('open');
        }
    });

    // ── Period selection ─────────────────────────────────────
    window.setPeriod = function(period, label) {
        currentPeriod = period;

        // Update all item active states (skip custom range)
        document.querySelectorAll('.kg-dropdown-item').forEach(btn => btn.classList.remove('active'));
        const clicked = event && event.target && event.target.classList.contains('kg-dropdown-item')
            ? event.target : null;
        if (clicked) clicked.classList.add('active');

        // Update label & chip
        const chip = document.getElementById('activeChip');
        const chipText = document.getElementById('activeChipText');
        document.getElementById('periodLabel').textContent = label || 'Rentang Kustom';

        if (period === 'all') {
            chip.classList.add('hidden');
        } else {
            chip.classList.remove('hidden');
            chipText.textContent = label || buildCustomLabel();
        }

        // Close panel (except custom — keep open so user can pick dates)
        if (period !== 'custom') {
            panelOpen = false;
            document.getElementById('periodPanel').classList.remove('open');
            document.getElementById('periodTrigger').classList.remove('open');
        }

        filterTable();
    };

    function buildCustomLabel() {
        const from = document.getElementById('dateFrom').value;
        const to   = document.getElementById('dateTo').value;
        if (from && to)   return `${formatDate(from)} – ${formatDate(to)}`;
        if (from)         return `Dari ${formatDate(from)}`;
        if (to)           return `Sampai ${formatDate(to)}`;
        return 'Rentang Kustom';
    }

    function formatDate(str) {
        if (!str) return '';
        const d = new Date(str);
        return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
    }

    window.clearPeriod = function() {
        currentPeriod = 'all';
        document.getElementById('periodLabel').textContent = 'Semua Waktu';
        document.getElementById('activeChip').classList.add('hidden');
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        document.querySelectorAll('.kg-dropdown-item').forEach(btn => btn.classList.remove('active'));
        document.querySelector('.kg-dropdown-item').classList.add('active'); // first = "Semua Waktu"
        filterTable();
    };

    // ── Mode pill ────────────────────────────────────────────
    window.setMode = function(btn, mode) {
        document.querySelectorAll('.kg-pill').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        currentMode = mode;
        filterTable();
    };

    // ── Search ───────────────────────────────────────────────
    window.filterTable = function() {
        const search = (document.getElementById('kgSearch').value || '').toLowerCase().trim();
        const rows   = document.querySelectorAll('#kgTbody tr');
        const today  = new Date(); today.setHours(23,59,59,999);
        let visible  = 0;

        rows.forEach(row => {
            const tanggal = row.dataset.tanggal;  // YYYY-MM-DD
            const mode    = row.dataset.mode;
            const nama    = row.dataset.nama;

            // Search match
            const matchSearch = !search || nama.includes(search);

            // Mode match
            const matchMode = !currentMode || mode === currentMode;

            // Period match
            let matchPeriod = true;
            if (currentPeriod !== 'all' && tanggal) {
                const d = new Date(tanggal);
                if (currentPeriod === 'custom') {
                    const from = document.getElementById('dateFrom').value;
                    const to   = document.getElementById('dateTo').value;
                    if (from) matchPeriod = matchPeriod && d >= new Date(from);
                    if (to)   matchPeriod = matchPeriod && d <= new Date(to + 'T23:59:59');
                } else {
                    const days = parseInt(currentPeriod);
                    const cutoff = new Date(today);
                    cutoff.setDate(cutoff.getDate() - days);
                    matchPeriod = d >= cutoff && d <= today;
                }
            } else if (currentPeriod !== 'all' && !tanggal) {
                matchPeriod = false;
            }

            const show = matchSearch && matchMode && matchPeriod;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Show/hide empty state
        document.getElementById('kgNoResult').classList.toggle('hidden', visible > 0);
        document.getElementById('totalCount').textContent = visible + ' kegiatan ditemukan';
    };
})();
</script>

@endsection
