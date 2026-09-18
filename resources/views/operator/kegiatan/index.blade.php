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

{{-- ── Table Card ────────────────────────────────────────────── --}}
<div class="glass animate-fade-in" style="padding: 0; overflow: visible;">

    {{-- Card Header --}}
    <div class="kg-header">
        <div>
            <p class="kg-header-title">Semua Kegiatan</p>
            <p class="kg-header-count" id="totalCount">{{ $kegiatan->total() ?? 0 }} kegiatan ditemukan</p>
        </div>
        <a href="{{ route('operator.kegiatan.create') }}" class="btn btn-primary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kegiatan
        </a>
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

        {{-- 3 Filter Waktu (Persis seperti di Dashboard BNN: Bulan ini, Tahun ini, dan Pilih Bulan & Tahun) --}}
        <div class="dash-filter-group relative" id="periodFilterGroup">
            <div class="dash-filter-presets">
                <button id="fBulan" class="dash-preset-btn {{ $fPeriod === 'month' ? 'active' : '' }}" type="button">
                    Bulan ini
                </button>
                <button id="fTahun" class="dash-preset-btn {{ $fPeriod === 'year' ? 'active' : '' }}" type="button">
                    Tahun ini
                </button>
                <button id="fCustom" class="dash-preset-btn flex items-center gap-1.5 {{ ($fPeriod === 'custom' || $fFrom || $fTo) ? 'active' : '' }}" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span id="fCustomLabel">{{ ($fPeriod === 'custom' && $fFrom) ? $displayLabel : 'Pilih Bulan & Tahun' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" id="fCustomCaret" class="w-3 h-3 opacity-60 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            {{-- Popover Pemilih Bulan & Tahun (Persis Desain Dashboard) --}}
            <div id="popoverMonthYear" class="popover-dropdown hidden">
                <div class="popover-card">
                    {{-- Header Popover: Tahun dengan Input Langsung & Tombol Navigasi --}}
                    <div class="popover-header">
                        <span class="popover-title">Pilih Waktu</span>
                        <div class="popover-year-nav">
                            <button type="button" id="popPrevYear" class="popover-nav-btn" title="Tahun sebelumnya">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <input type="number" id="popYearInput" class="popover-year-input" value="2026" min="2000" max="2099" title="Ketik tahun langsung (contoh: 2026)">
                            <button type="button" id="popNextYear" class="popover-nav-btn" title="Tahun berikutnya">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Grid 12 Bulan (3 Kolom x 4 Baris) --}}
                    <div class="popover-month-grid" id="popMonthGrid">
                        <!-- Dirender via JS (Jan s/d Des) -->
                    </div>

                    {{-- Footer Popover --}}
                    <div class="popover-footer">
                        <button type="button" id="popBtnAll" class="popover-foot-action" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:0.75rem;padding:0;">
                            Semua Waktu
                        </button>
                        <button type="button" id="popBtnClose" class="popover-foot-close" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:0.75rem;padding:0;">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active filter chip --}}
        <div class="kg-active-chip {{ (($fPeriod === 'all' || $fPeriod === 'month') && !$fFrom && !$fTo) ? 'hidden' : '' }}" id="activeChip">
            <span id="activeChipText">{{ $displayLabel }}</span>
            <button id="btnClearPeriod" class="kg-chip-clear" aria-label="Hapus filter tanggal">
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
        <a href="{{ route('operator.kegiatan.create') }}" class="btn btn-primary btn-sm mt-2">Tambah Sekarang</a>
    </div>
    @else
    <div style="overflow-x:auto;">
        <table class="data-table" id="kgTable">
            <thead>
                <tr>
                    <th>Nama Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Peserta</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="kgTbody">
            @foreach($kegiatan as $k)
            <tr data-tanggal="{{ $k->tanggal ?? '' }}" data-nama="{{ strtolower($k->nama_kegiatan) }}">
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
                {{-- Status --}}
                <td>
                    @php $s = $k->status ?? 'menunggu'; @endphp
                    @if($s === 'selesai')         <span class="badge badge-green">Selesai</span>
                    @elseif($s === 'berlangsung') <span class="badge badge-cyan">Berlangsung</span>
                    @else                         <span class="badge badge-gray">Dijadwalkan</span>
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
                        <a href="{{ route('operator.kegiatan.edit', $k->id) }}" class="btn btn-secondary btn-sm" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
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

/* ─── Dashboard-Matching Filter & 12-Month Popover ─────────── */
.dash-filter-group {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.dash-filter-presets {
    display: inline-flex;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 0.6rem;
    padding: 4px;
    gap: 4px;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
}
.dash-preset-btn {
    font-family: var(--font-sans);
    padding: 0.4rem 0.9rem;
    border-radius: calc(0.6rem - 3px);
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--text-muted);
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.dash-preset-btn:hover {
    color: var(--text-primary);
    background: rgba(0, 0, 0, 0.03);
}
.dash-preset-btn.active {
    background: #ffffff;
    color: var(--primary);
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.03);
}

/* Popover Dropdown (Month & Year) */
.popover-dropdown {
    position: absolute;
    left: 0;
    top: calc(100% + 8px);
    z-index: 300;
    width: 290px;
}
.popover-dropdown.hidden {
    display: none !important;
}
.popover-card {
    background: var(--surface, #ffffff);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: var(--r-xl, 16px);
    padding: 1rem;
    box-shadow: 0 12px 36px -4px rgba(17, 24, 39, 0.14), 0 4px 12px -2px rgba(17, 24, 39, 0.06);
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    user-select: none;
}
.popover-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.65rem;
    border-bottom: 1px solid var(--border, #e2e8f0);
}
.popover-title {
    font-family: var(--font-display, 'Plus Jakarta Sans', sans-serif);
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-primary, #111827);
}
.popover-year-nav {
    display: flex;
    align-items: center;
    gap: 4px;
}
.popover-nav-btn {
    width: 26px;
    height: 26px;
    border-radius: var(--r-sm, 6px);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted, #9ca3af);
    background: transparent;
    border: 1px solid var(--border, #e2e8f0);
    cursor: pointer;
    transition: all 0.15s ease;
}
.popover-nav-btn:hover {
    color: var(--text-primary, #111827);
    background: var(--surface-2, #f9fafb);
    border-color: var(--border-strong, #cbd0df);
}

/* Year Input (Langsung Ketik Tahun & UX Nyaman) */
.popover-year-input {
    width: 58px;
    height: 26px;
    font-family: var(--font-display, 'Plus Jakarta Sans', sans-serif);
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--primary, #4361ee);
    background: var(--primary-light, #eef1ff);
    border: 1.5px solid transparent;
    border-radius: var(--r-sm, 6px);
    text-align: center;
    padding: 0 4px;
    outline: none;
    transition: all 0.15s ease;
    -moz-appearance: textfield;
}
.popover-year-input::-webkit-outer-spin-button,
.popover-year-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.popover-year-input:hover {
    border-color: var(--primary, #4361ee);
}
.popover-year-input:focus {
    border-color: var(--primary, #4361ee);
    background: #ffffff;
    box-shadow: 0 0 0 2.5px rgba(67, 97, 238, 0.2);
}

/* 12-Month Grid (3 cols x 4 rows) */
.popover-month-grid {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 6px !important;
}
.pop-month-btn {
    font-family: var(--font-sans, 'Inter', sans-serif);
    padding: 0.45rem 0.25rem;
    border-radius: var(--r-md, 8px);
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--text-secondary, #4b5563);
    background: var(--surface-2, #f9fafb);
    border: 1px solid var(--border, #e2e8f0);
    cursor: pointer;
    text-align: center;
    transition: all 0.15s ease;
    box-sizing: border-box;
}
.pop-month-btn:hover {
    background: var(--primary-light, #eef1ff);
    color: var(--primary, #4361ee);
    border-color: var(--primary-light, #eef1ff);
}
.pop-month-btn.active {
    background: var(--primary, #4361ee) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border-color: var(--primary, #4361ee) !important;
    box-shadow: 0 2px 6px rgba(67, 97, 238, 0.35);
}

.popover-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 0.65rem;
    border-top: 1px solid var(--border, #e2e8f0);
    font-size: 0.6875rem;
    font-family: var(--font-sans, 'Inter', sans-serif);
}
.popover-foot-action {
    color: var(--text-muted, #6b7280);
    font-weight: 500;
    transition: color 0.15s ease;
}
.popover-foot-action:hover {
    color: var(--primary, #4361ee);
}
.popover-foot-close {
    color: var(--text-muted, #9ca3af);
    font-weight: 500;
    transition: color 0.15s ease;
}
.popover-foot-close:hover {
    color: var(--text-primary, #111827);
}

/* Dark mode overrides */
:is(.dark) .dash-filter-presets {
    background: rgba(255,255,255,0.03);
    border-color: rgba(255,255,255,0.05);
}
:is(.dark) .dash-preset-btn:hover {
    background: rgba(255,255,255,0.04);
    color: var(--text-primary);
}
:is(.dark) .dash-preset-btn.active {
    background: var(--surface);
    color: var(--primary-light);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
}
:is(.dark) .popover-card {
    background: var(--surface, #1e293b);
    border-color: var(--border, #334155);
    box-shadow: 0 12px 36px -4px rgba(0, 0, 0, 0.5);
}
:is(.dark) .popover-year-input {
    background: rgba(67, 97, 238, 0.15);
    color: #93c5fd;
}
:is(.dark) .popover-year-input:focus {
    background: #0f172a;
    border-color: #60a5fa;
}
:is(.dark) .pop-month-btn {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.08);
    color: var(--text-secondary, #cbd5e1);
}
:is(.dark) .pop-month-btn:hover {
    background: var(--primary-light, rgba(67, 97, 238, 0.15));
    color: #ffffff;
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

    // ── Mode Waktu & Popover Pemilih Bulan/Tahun (Persis Dashboard BNN) ───
    const fPeriod = @json($fPeriod);
    const fFrom   = @json($fFrom);
    const fTo     = @json($fTo);

    // Hitung tahun & bulan aktif saat ini
    const SHORT_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    let popoverYear = 2026;
    let activeSelectedMonth = null;
    let activeSelectedYear  = null;

    if (fFrom) {
        const parts = fFrom.split('-');
        if (parts.length === 3) {
            popoverYear = parseInt(parts[0], 10);
            activeSelectedMonth = parseInt(parts[1], 10) - 1;
            activeSelectedYear  = popoverYear;
        }
    } else if (fPeriod === 'month') {
        const now = new Date();
        activeSelectedMonth = now.getMonth();
        activeSelectedYear  = now.getFullYear();
        popoverYear         = activeSelectedYear;
    }

    const popMonthGrid     = document.getElementById('popMonthGrid');
    const popYearInput     = document.getElementById('popYearInput');
    const popPrevYear      = document.getElementById('popPrevYear');
    const popNextYear      = document.getElementById('popNextYear');
    const popoverMonthYear = document.getElementById('popoverMonthYear');
    const fCustomCaret     = document.getElementById('fCustomCaret');

    function renderPopoverMonthGrid() {
        if (!popMonthGrid) return;
        let html = '';
        SHORT_MONTHS.forEach((mName, idx) => {
            const isSelected = (idx === activeSelectedMonth && popoverYear === activeSelectedYear);
            html += `<button type="button" class="pop-month-btn ${isSelected ? 'active' : ''}" data-month="${idx}">
                ${mName}
            </button>`;
        });
        popMonthGrid.innerHTML = html;

        popMonthGrid.querySelectorAll('.pop-month-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const m = parseInt(this.getAttribute('data-month'), 10);
                selectMonthYear(m, popoverYear);
            });
        });
    }

    function updateYearInput(val) {
        popoverYear = val;
        if (popYearInput) popYearInput.value = val;
        renderPopoverMonthGrid();
    }

    function togglePopover() {
        if (!popoverMonthYear) return;
        const isHidden = popoverMonthYear.classList.contains('hidden');
        if (isHidden) {
            if (popYearInput) popYearInput.value = popoverYear;
            renderPopoverMonthGrid();
            popoverMonthYear.classList.remove('hidden');
            fCustomCaret?.classList.add('rotate-180');
        } else {
            closePopover();
        }
    }

    function closePopover() {
        if (popoverMonthYear && !popoverMonthYear.classList.contains('hidden')) {
            popoverMonthYear.classList.add('hidden');
            fCustomCaret?.classList.remove('rotate-180');
        }
    }

    // Pilih Bulan & Tahun spesifik -> navigasi ke rentang tanggal bulan tersebut
    function selectMonthYear(month, year) {
        const mStr = String(month + 1).padStart(2, '0');
        const startDate = `${year}-${mStr}-01`;
        const lastDay   = new Date(year, month + 1, 0).getDate();
        const endDate   = `${year}-${mStr}-${String(lastDay).padStart(2, '0')}`;
        closePopover();
        navigate({ period: 'custom', date_from: startDate, date_to: endDate });
    }

    // Event listener tombol 1 & 2: Bulan ini & Tahun ini
    document.getElementById('fBulan')?.addEventListener('click', function () {
        closePopover();
        navigate({ period: 'month', date_from: null, date_to: null });
    });

    document.getElementById('fTahun')?.addEventListener('click', function () {
        closePopover();
        navigate({ period: 'year', date_from: null, date_to: null });
    });

    // Event listener tombol ke-3: Buka popover
    document.getElementById('fCustom')?.addEventListener('click', function (e) {
        e.stopPropagation();
        togglePopover();
    });

    // Klik di dalam card popover jangan menutup popover
    popoverMonthYear?.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    // Tombol Previous Year (<)
    popPrevYear?.addEventListener('click', function (e) {
        e.stopPropagation();
        updateYearInput(popoverYear - 1);
    });

    // Tombol Next Year (>)
    popNextYear?.addEventListener('click', function (e) {
        e.stopPropagation();
        updateYearInput(popoverYear + 1);
    });

    // Input Tahun Langsung (UX Cepat: Ketik 2024, 2025, 2026 dll)
    if (popYearInput) {
        popYearInput.addEventListener('input', function () {
            const val = parseInt(this.value, 10);
            if (val >= 1990 && val <= 2099) {
                popoverYear = val;
                renderPopoverMonthGrid();
            }
        });
        popYearInput.addEventListener('change', function () {
            const val = parseInt(this.value, 10);
            if (val >= 1990 && val <= 2099) {
                updateYearInput(val);
            } else {
                this.value = popoverYear;
            }
        });
    }

    // Tombol di footer popover
    document.getElementById('popBtnAll')?.addEventListener('click', function () {
        closePopover();
        navigate({ period: 'all', date_from: null, date_to: null });
    });

    document.getElementById('popBtnClose')?.addEventListener('click', function () {
        closePopover();
    });

    // Klik di luar menutup popover
    document.addEventListener('click', function () {
        closePopover();
    });

    // Inisialisasi awal render grid popover
    renderPopoverMonthGrid();

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

