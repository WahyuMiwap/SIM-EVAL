@extends('layouts.app')

@section('page-title', 'Dashboard Evaluasi')
@section('page-subtitle', 'Monitoring efektivitas sosialisasi Pre-Test & Post-Test BNN Kota Surabaya')

@section('content')

{{-- ─── Header Section & Filter Global ───────────────────────── --}}
<div class="dash-header-bar">
    <div>
        <h2 class="dash-heading-lg">
            Overview Kegiatan
        </h2>
        <p class="dash-subtext">
            Pantauan evaluasi sosialisasi & performa N-Gain BNN Kota Surabaya hari ini
        </p>
    </div>

    {{-- 3 Filter Waktu (Bulan ini, Tahun ini, dan Pilih Bulan & Tahun) --}}
    <div class="dash-filter-group relative">
        <div class="dash-filter-presets">
            <button id="fBulan" class="dash-preset-btn active" type="button">
                Bulan ini
            </button>
            <button id="fTahun" class="dash-preset-btn" type="button">
                Tahun ini
            </button>
            <button id="fCustom" class="dash-preset-btn flex items-center gap-1.5" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span id="fCustomLabel">Pilih Bulan & Tahun</span>
                <svg xmlns="http://www.w3.org/2000/svg" id="fCustomCaret" class="w-3 h-3 opacity-60 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        {{-- Popover Pemilih Bulan & Tahun --}}
        <div id="popoverMonthYear" class="popover-dropdown hidden">
            <div class="popover-card">
                {{-- Header Popover: Tahun --}}
                <div class="popover-header">
                    <span class="popover-title">Pilih Periode</span>
                    <div class="popover-year-nav">
                        <button type="button" id="popPrevYear" class="popover-nav-btn" title="Tahun sebelumnya">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span id="popYearLabel" class="popover-year-badge">2026</span>
                        <button type="button" id="popNextYear" class="popover-nav-btn" title="Tahun berikutnya">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Grid 12 Bulan (3 Kolom x 4 Baris) --}}
                <div class="popover-month-grid" id="popMonthGrid">
                    <!-- Dirender via JS (Jan s/d Des) -->
                </div>

                {{-- Footer: Shortcut & Close --}}
                <div class="popover-footer">
                    <button type="button" id="popBtnCurrentMonth" class="popover-foot-action">Bulan Sekarang</button>
                    <button type="button" id="popBtnClose" class="popover-foot-close">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Row 1: Dua Kartu Metrik Utama (Total Kegiatan & Total Peserta) ─── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

    {{-- Card 1: Total Kegiatan --}}
    <div class="dash-card">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2.5">
                <div class="dash-icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 font-sans">Total Kegiatan</span>
            </div>
            <button type="button" class="text-gray-300 hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-400 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
            </button>
        </div>

        <div class="flex items-baseline justify-between pt-1">
            <h3 class="dash-stat-number" id="sKegiatan">14</h3>
            <div class="flex items-center gap-1.5">
                <span class="badge-growth-up">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span class="font-semibold">12.5%</span>
                </span>
                <span class="text-[11px] text-gray-400 font-sans">dari bulan lalu</span>
            </div>
        </div>
    </div>

    {{-- Card 2: Total Peserta --}}
    <div class="dash-card">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2.5">
                <div class="dash-icon-box" style="background: var(--info-light); color: var(--info);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 font-sans">Total Peserta</span>
            </div>
            <button type="button" class="text-gray-300 hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-400 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
            </button>
        </div>

        <div class="flex items-baseline justify-between pt-1">
            <h3 class="dash-stat-number" id="sPeserta">1.248</h3>
            <div class="flex items-center gap-1.5">
                <span class="badge-growth-up">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span class="font-semibold">8.2%</span>
                </span>
                <span class="text-[11px] text-gray-400 font-sans">Target <span class="font-semibold">1.500</span></span>
            </div>
        </div>
    </div>
</div>

{{-- ─── Row 2: Kalender Agenda & Riwayat Kegiatan Lengkap ─────── --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">
    {{-- Kalender Agenda Minimalist (7 cols) --}}
    <div class="lg:col-span-7 dash-card">
        {{-- Header Kalender: Tanggal besar (dikontrol oleh filter global) --}}
        <div class="cal-header">
            <div class="cal-date-display">
                <span class="cal-day-big" id="calDayBig">17</span>
                <div class="cal-month-wrap">
                    <span class="cal-month-name" id="calMonthName">September</span>
                    <span class="cal-year-name" id="calYearName">2026</span>
                </div>
            </div>
            <div class="cal-legend-row">
                <span class="cal-dot" style="background:#22c55e"></span><span class="cal-legend-text">Selesai</span>
                <span class="cal-dot" style="background:#f59e0b"></span><span class="cal-legend-text">Aktif</span>
                <span class="cal-dot" style="background:var(--primary)"></span><span class="cal-legend-text">Mendatang</span>
            </div>
        </div>

        {{-- Grid Kalender --}}
        <div id="dashCalendar">
            {{-- Dirender via JS --}}
        </div>

        {{-- Detail Acara Terpilih --}}
        <div id="calEventDetail" class="cal-event-detail-wrap mt-4 pt-3.5 border-t border-gray-100 dark:border-gray-800">
            {{-- Dirender secara dinamis via JS saat tanggal dipilih --}}
        </div>
    </div>

    {{-- Kegiatan Terbaru Table (5 cols) --}}
    <div class="lg:col-span-5 dash-card flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h4 class="dash-card-title text-sm">Kegiatan Terbaru</h4>
                    <p class="dash-card-sub">Riwayat pelaksanaan 5 kegiatan terkini</p>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 font-sans">SMAN 1 Surabaya</p>
                        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-gray-400 font-sans">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">05 Sep</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span><span class="font-semibold text-gray-700 dark:text-gray-300">65</span> Peserta</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span class="px-1.5 py-0.2 rounded bg-gray-100 dark:bg-gray-800 text-[10px] text-gray-500 font-medium">Digital</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-0.5">
                        <span class="badge badge-green text-[10px] font-sans">Selesai</span>
                        <p class="text-[11px] text-gray-400 font-sans flex items-center gap-1 mt-0.5">
                            <span>Gain:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">0.78</span>
                        </p>
                    </div>
                </div>

                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 font-sans">Kec. Tegalsari</p>
                        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-gray-400 font-sans">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">10 Sep</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span><span class="font-semibold text-gray-700 dark:text-gray-300">40</span> Peserta</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span class="px-1.5 py-0.2 rounded bg-gray-100 dark:bg-gray-800 text-[10px] text-gray-500 font-medium">Hybrid</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-0.5">
                        <span class="badge badge-green text-[10px] font-sans">Selesai</span>
                        <p class="text-[11px] text-gray-400 font-sans flex items-center gap-1 mt-0.5">
                            <span>Gain:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">0.71</span>
                        </p>
                    </div>
                </div>

                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 font-sans">Kel. Jambangan</p>
                        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-gray-400 font-sans">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">14 Sep</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span><span class="font-semibold text-gray-700 dark:text-gray-300">80</span> Peserta</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span class="px-1.5 py-0.2 rounded bg-gray-100 dark:bg-gray-800 text-[10px] text-gray-500 font-medium">Digital</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-0.5">
                        <span class="badge badge-yellow text-[10px] font-sans">Aktif</span>
                        <p class="text-[11px] text-gray-400 font-sans flex items-center gap-1 mt-0.5">
                            <span>Gain:</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400">0.68</span>
                        </p>
                    </div>
                </div>

                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 font-sans">Aula PDAM Sby</p>
                        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-gray-400 font-sans">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">22 Sep</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span><span class="font-semibold text-gray-700 dark:text-gray-300">50</span> Peserta</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span class="px-1.5 py-0.2 rounded bg-gray-100 dark:bg-gray-800 text-[10px] text-gray-500 font-medium">Digital</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-0.5">
                        <span class="badge badge-blue text-[10px] font-sans">Mendatang</span>
                        <p class="text-[11px] text-gray-400 font-sans flex items-center gap-1 mt-0.5">
                            <span>Gain:</span>
                            <span class="text-gray-400 font-medium">—</span>
                        </p>
                    </div>
                </div>

                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 font-sans">Lapas Kelas I Surabaya</p>
                        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-gray-400 font-sans">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">28 Sep</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span><span class="font-semibold text-gray-700 dark:text-gray-300">120</span> Peserta</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span class="px-1.5 py-0.2 rounded bg-gray-100 dark:bg-gray-800 text-[10px] text-gray-500 font-medium">Kertas</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-0.5">
                        <span class="badge badge-blue text-[10px] font-sans">Mendatang</span>
                        <p class="text-[11px] text-gray-400 font-sans flex items-center gap-1 mt-0.5">
                            <span>Gain:</span>
                            <span class="text-gray-400 font-medium">—</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('operator.kegiatan.index') }}" class="w-full py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-semibold text-center block transition-colors" style="border-radius: var(--r-md);">
                Kelola Seluruh Kegiatan
            </a>
        </div>
    </div>
</div>

{{-- ─── Styles Scoped Sesuai Prinsip Desain & Design Tokens ──── --}}
<style>
/* Typography & Layout Tokens */
.dash-header-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}
.dash-heading-lg {
    font-family: var(--font-display);
    font-size: 1.35rem;
    font-weight: 700;
    letter-spacing: -0.025em;
    color: var(--text-primary);
}
.dash-subtext {
    font-family: var(--font-sans);
    font-size: 0.8125rem;
    color: var(--text-muted);
}
.dash-card-title {
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--text-primary);
}
.dash-card-sub {
    font-family: var(--font-sans);
    font-size: 0.75rem;
    color: var(--text-muted);
}
.dash-stat-number {
    font-family: var(--font-display);
    font-size: 2.125rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--text-primary);
    line-height: 1;
}

/* Card Styling: Minimalist Modern Flat */
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

.dash-icon-box {
    width: 34px;
    height: 34px;
    border-radius: var(--r-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--primary-light);
    color: var(--primary);
}

.dash-chip {
    display: inline-block;
    padding: 0.15rem 0.55rem;
    border-radius: var(--r-full);
    font-size: 0.65rem;
    font-weight: 600;
    background: var(--primary-light);
    color: var(--primary);
}

.badge-growth-up {
    display: inline-flex;
    align-items: center;
    gap: 2px;
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--success);
    background: var(--success-light);
    padding: 2px 7px;
    border-radius: var(--r-full);
}

/* Filter Controls */
.dash-filter-group {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.dash-filter-presets {
    display: inline-flex;
    background: #f1f5f9; /* modern pill container bg */
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
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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

/* Popover Dropdown */
.popover-dropdown {
    position: absolute;
    right: 0;
    top: calc(100% + 8px);
    z-index: 100;
    width: 280px;
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
    font-size: 0.75rem;
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
.popover-year-badge {
    font-family: var(--font-display, 'Plus Jakarta Sans', sans-serif);
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--primary, #4361ee);
    background: var(--primary-light, #eef1ff);
    padding: 2px 8px;
    border-radius: var(--r-sm, 6px);
}
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
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
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
    font-weight: 600;
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.15s ease;
}
.popover-foot-action:hover {
    color: var(--primary, #4361ee);
}
.popover-foot-close {
    color: var(--text-muted, #9ca3af);
    font-weight: 500;
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.15s ease;
}
.popover-foot-close:hover {
    color: var(--text-primary, #111827);
}

/* Dark mode overrides (fallback) */
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
:is(.dark) .pop-month-btn {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.08);
    color: var(--text-secondary, #cbd5e1);
}
:is(.dark) .pop-month-btn:hover {
    background: var(--primary-light, rgba(67, 97, 238, 0.15));
    color: #ffffff;
}

/* Custom Dropdown Select */
.dash-custom-select {
    appearance: none;
    font-family: var(--font-sans);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.45rem center;
    background-repeat: no-repeat;
    background-size: 1.25em 1.25em;
    padding: 0.35rem 1.8rem 0.35rem 0.75rem;
    font-size: 0.75rem;
    border-radius: var(--r-md);
    border: 1px solid var(--border);
    background-color: var(--surface);
    color: var(--text-primary);
    cursor: pointer;
    outline: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.dash-custom-select:hover {
    border-color: var(--border-strong);
}
.dash-custom-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--primary-light);
}

/* ── Custom Minimalist Calendar ─────────────────────────────── */

/* Header: tanggal besar + navigasi */
.cal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding-bottom: 0.75rem;
    margin-bottom: 0.75rem;
    border-bottom: 1px solid var(--border);
}
.cal-date-display {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}
.cal-day-big {
    font-family: var(--font-display);
    font-size: 3rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.05em;
    color: var(--text-primary);
}
.cal-month-wrap {
    display: flex;
    flex-direction: column;
    gap: 0;
}
.cal-month-name {
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.01em;
    line-height: 1.2;
}
.cal-year-name {
    font-family: var(--font-sans);
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--text-muted);
    line-height: 1.2;
}
.cal-nav-legend {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.4rem;
}
.cal-legend-row {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}
.cal-dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}
.cal-legend-text {
    font-family: var(--font-sans);
    font-size: 0.65rem;
    font-weight: 500;
    color: var(--text-muted);
}
.cal-nav-btns {
    display: flex;
    gap: 4px;
}
.cal-nav-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: var(--r-sm);
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.18s ease;
}
.cal-nav-btn svg {
    width: 14px;
    height: 14px;
}
.cal-nav-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-light);
}

/* Grid kalender */
.cal-grid-wrap {
    width: 100%;
}
.cal-row {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}
.cal-head-row {
    margin-bottom: 2px;
}
.cal-cell {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.25rem 0;
    min-height: 38px;
    position: relative;
}
.cal-head-cell {
    font-family: var(--font-sans);
    font-size: 0.65rem;
    font-weight: 600;
    color: var(--text-muted);
    letter-spacing: 0.04em;
    text-transform: uppercase;
    min-height: 24px;
}
.cal-date-num {
    font-family: var(--font-sans);
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text-secondary);
    line-height: 1;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.15s ease;
}
.cal-date-cell {
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.15s ease;
}
.cal-date-cell:hover {
    background: rgba(67, 97, 238, 0.06);
}
.cal-date-cell:hover .cal-date-num {
    background: var(--primary-light);
    color: var(--primary);
}
.cal-weekend .cal-date-num {
    color: var(--primary);
    opacity: 0.65;
}
.cal-today .cal-date-num {
    border: 1.5px solid var(--primary);
    color: var(--primary);
    font-weight: 700;
}
.cal-date-cell.cal-selected .cal-date-num {
    background: var(--primary) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    box-shadow: 0 3px 8px rgba(67, 97, 238, 0.35);
}
.cal-empty {
    pointer-events: none;
}

/* Event dots */
.cal-dots {
    display: flex;
    gap: 2px;
    margin-top: 2px;
    justify-content: center;
}
.cal-event-dot {
    display: inline-block;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    flex-shrink: 0;
}
</style>



{{-- ─── Script Kalender & Filter Presets ────────────────────────── --}}
<script>
(function() {
    // ── Data Acara Kalender ─────────────────────────────────────────
    const CAL_EVENTS = [
        {
            date: '2026-09-05',
            color: '#22c55e',
            status: 'Selesai',
            badgeClass: 'badge-green',
            title: 'Sosialisasi P4GN SMAN 1 Surabaya',
            category: 'Pendidikan / Remaja',
            time: '08:30 - 11:30 WIB',
            location: 'Aula SMAN 1 Surabaya',
            participants: '65 Siswa'
        },
        {
            date: '2026-09-10',
            color: '#22c55e',
            status: 'Selesai',
            badgeClass: 'badge-green',
            title: 'Workshop Ketahanan Keluarga Anti Narkoba',
            category: 'Keluarga / Masyarakat',
            time: '09:00 - 12:00 WIB',
            location: 'Kec. Tegalsari Surabaya',
            participants: '40 Warga'
        },
        {
            date: '2026-09-14',
            color: '#f59e0b',
            status: 'Aktif',
            badgeClass: 'badge-yellow',
            title: 'Pembinaan & Edukasi Komunitas Pemuda Bersinar',
            category: 'Komunitas Pemuda',
            time: '13:00 - 15:30 WIB',
            location: 'Kel. Jambangan Surabaya',
            participants: '80 Pemuda'
        },
        {
            date: '2026-09-17',
            color: '#f59e0b',
            status: 'Aktif',
            badgeClass: 'badge-yellow',
            title: 'Monitoring & Evaluasi Pelaksanaan P4GN Triwulan III',
            category: 'Internal BNN & Mitra',
            time: '09:30 - 12:00 WIB',
            location: 'Ruang Rapat BNN Kota Surabaya',
            participants: '25 Peserta'
        },
        {
            date: '2026-09-22',
            color: '#4361EE',
            status: 'Mendatang',
            badgeClass: 'badge-blue',
            title: 'Sosialisasi Bahaya Narkoba Lingkungan Kerja PDAM Surya Sembada',
            category: 'Instansi BUMD',
            time: '08:00 - 12:00 WIB',
            location: 'Kantor Pusat PDAM Surabaya',
            participants: '120 Karyawan'
        },
        {
            date: '2026-09-28',
            color: '#4361EE',
            status: 'Mendatang',
            badgeClass: 'badge-blue',
            title: 'Pemeriksaan & Deteksi Dini Tes Urin Berkala',
            category: 'Dunia Usaha / Swasta',
            time: '08:30 - 13:30 WIB',
            location: 'Kawasan Industri Rungkut Surabaya',
            participants: '200 Pekerja'
        },
        // Desember 2026 (Sesuai request user: "mau bulan desember")
        {
            date: '2026-12-05',
            color: '#22c55e',
            status: 'Selesai',
            badgeClass: 'badge-green',
            title: 'Sosialisasi Bahaya Narkoba Akhir Semester — SMK N 2 Surabaya',
            category: 'Pendidikan / Remaja',
            time: '08:30 - 11:00 WIB',
            location: 'Auditorium SMK N 2 Surabaya',
            participants: '95 Siswa'
        },
        {
            date: '2026-12-12',
            color: '#f59e0b',
            status: 'Aktif',
            badgeClass: 'badge-yellow',
            title: 'Rapat Koordinasi Evaluasi Tahunan Relawan P4GN Surabaya',
            category: 'Masyarakat & Penggiat',
            time: '09:00 - 12:30 WIB',
            location: 'Gedung Graha Sawunggaling Surabaya',
            participants: '110 Peserta'
        },
        {
            date: '2026-12-22',
            color: '#4361EE',
            status: 'Mendatang',
            badgeClass: 'badge-blue',
            title: 'Kampanye Terpadu Libur Nataru Bersinar (Bersih Narkoba)',
            category: 'Masyarakat Umum / Transportasi',
            time: '08:00 - 14:00 WIB',
            location: 'Terminal Purabaya & Stasiun Gubeng',
            participants: '350 Sasaran'
        }
    ];

    const DAY_NAMES      = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    const DAY_FULL_NAMES = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const MONTH_NAMES    = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    let calYear     = 2026;
    let calMonth    = 8;
    let selectedDay = 17; // Tanggal default terpilih (Hari ini)
    const todayReal = new Date();

    function getEventsForDate(y, m, d) {
        const key = `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        return CAL_EVENTS.filter(e => e.date === key);
    }

    function renderEventDetail(y, m, d) {
        const detailEl = document.getElementById('calEventDetail');
        if (!detailEl) return;

        const dateObj = new Date(y, m, d);
        const dayName = DAY_FULL_NAMES[dateObj.getDay()];
        const formattedDate = `${dayName}, ${d} ${MONTH_NAMES[m]} ${y}`;
        const events = getEventsForDate(y, m, d);

        if (events.length === 0) {
            detailEl.innerHTML = `
                <div class="py-3 px-3.5 rounded-xl bg-gray-50/70 dark:bg-gray-800/40 border border-dashed border-gray-200 dark:border-gray-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 font-sans">${formattedDate}</p>
                            <p class="text-[11px] text-gray-400 font-sans">Tidak ada agenda kegiatan terjadwal pada tanggal ini.</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-gray-400 dark:text-gray-500 font-sans hidden sm:inline-block">Pilih tanggal bertitik</span>
                </div>
            `;
            return;
        }

        let eventsHtml = '';
        events.forEach(evt => {
            eventsHtml += `
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 hover:border-gray-200 dark:hover:border-gray-700 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h5 class="text-xs font-bold text-gray-900 dark:text-white font-sans tracking-tight line-clamp-1">${evt.title}</h5>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-sans mt-0.5">${evt.category}</p>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0" style="background: ${evt.color}"></span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-2.5 pt-2 border-t border-gray-200/50 dark:border-gray-700/50 text-[11px] text-gray-500 dark:text-gray-400 font-sans">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>${evt.time}</span>
                        </div>
                        <div class="flex items-center gap-1.5 min-w-0">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate">${evt.location}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>${evt.participants}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        detailEl.innerHTML = `
            <div>
                <div class="flex items-center justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200 font-sans truncate">
                            Agenda · ${formattedDate}
                        </span>
                        <span class="badge ${events[0].badgeClass} text-[10px] font-sans flex-shrink-0">${events[0].status}</span>
                    </div>
                    <a href="{{ route('operator.kegiatan.index') }}" class="text-[11px] font-semibold text-primary hover:underline font-sans inline-flex items-center gap-1 flex-shrink-0">
                        Lihat kegiatan
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="space-y-2">
                    ${eventsHtml}
                </div>
            </div>
        `;
    }

    function renderCalendar() {
        const calEl = document.getElementById('dashCalendar');
        if (!calEl) return;

        const firstDay    = new Date(calYear, calMonth, 1).getDay();
        const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
        const today = (todayReal.getFullYear() === calYear && todayReal.getMonth() === calMonth)
            ? todayReal.getDate() : -1;

        if (selectedDay > daysInMonth) selectedDay = daysInMonth;

        const dayBigEl    = document.getElementById('calDayBig');
        const monthNameEl = document.getElementById('calMonthName');
        const yearNameEl  = document.getElementById('calYearName');
        if (dayBigEl)    dayBigEl.textContent    = selectedDay;
        if (monthNameEl) monthNameEl.textContent = MONTH_NAMES[calMonth];
        if (yearNameEl)  yearNameEl.textContent  = calYear;

        let html = '<div class="cal-grid-wrap">';
        html += '<div class="cal-row cal-head-row">';
        DAY_NAMES.forEach(d => { html += `<div class="cal-cell cal-head-cell">${d}</div>`; });
        html += '</div>';

        let cellCount = 0;
        html += '<div class="cal-row">';
        for (let i = 0; i < firstDay; i++) { html += '<div class="cal-cell cal-empty"></div>'; cellCount++; }

        for (let d = 1; d <= daysInMonth; d++) {
            if (cellCount > 0 && cellCount % 7 === 0) html += '</div><div class="cal-row">';
            const events     = getEventsForDate(calYear, calMonth, d);
            const isToday    = d === today;
            const isSelected = d === selectedDay;
            const isWeekend  = ((firstDay + d - 1) % 7 === 0) || ((firstDay + d - 1) % 7 === 6);
            const dots       = events.map(e => `<span class="cal-event-dot" style="background:${e.color}"></span>`).join('');
            
            html += `<div class="cal-cell cal-date-cell${isToday?' cal-today':''}${isSelected?' cal-selected':''}${isWeekend?' cal-weekend':''}" data-day="${d}">
                <span class="cal-date-num">${d}</span>
                ${dots ? `<div class="cal-dots">${dots}</div>` : ''}
            </div>`;
            cellCount++;
        }
        const rem = cellCount % 7;
        if (rem !== 0) for (let i = rem; i < 7; i++) html += '<div class="cal-cell cal-empty"></div>';
        html += '</div></div>';
        calEl.innerHTML = html;

        // Pasang event listener klik pada tanggal
        calEl.querySelectorAll('.cal-date-cell').forEach(cell => {
            cell.addEventListener('click', function() {
                const day = parseInt(this.getAttribute('data-day'), 10);
                if (!day) return;
                selectedDay = day;

                // Update visual selection
                calEl.querySelectorAll('.cal-date-cell').forEach(c => c.classList.remove('cal-selected'));
                this.classList.add('cal-selected');

                // Update header angka besar
                if (dayBigEl) dayBigEl.textContent = selectedDay;

                // Render detail acara di bawah kalender
                renderEventDetail(calYear, calMonth, selectedDay);
            });
        });

        // Render detail acara untuk tanggal yang terpilih
        renderEventDetail(calYear, calMonth, selectedDay);
    }

    // ── Mode Waktu Aktif: 'bulan_ini' | 'tahun_ini' | 'custom' ───
    let currentMode = 'bulan_ini';
    let popoverYear = 2026;

    // ── Render Grid 12 Bulan Popover ─────────────────────────────
    function renderPopoverMonthGrid() {
        const grid = document.getElementById('popMonthGrid');
        if (!grid) return;
        const SHORT_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        let html = '';
        SHORT_MONTHS.forEach((mName, idx) => {
            const isSelected = (idx === calMonth && popoverYear === calYear && currentMode === 'custom');
            html += `<button type="button" class="pop-month-btn ${isSelected ? 'active' : ''}" data-month="${idx}">
                ${mName}
            </button>`;
        });
        grid.innerHTML = html;

        grid.querySelectorAll('.pop-month-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const m = parseInt(this.getAttribute('data-month'), 10);
                selectCustomMonthYear(m, popoverYear);
                closePopover();
            });
        });
    }

    function togglePopover() {
        const pop = document.getElementById('popoverMonthYear');
        const caret = document.getElementById('fCustomCaret');
        if (!pop) return;
        const isHidden = pop.classList.contains('hidden');
        if (isHidden) {
            popoverYear = calYear;
            document.getElementById('popYearLabel').textContent = popoverYear;
            renderPopoverMonthGrid();
            pop.classList.remove('hidden');
            caret?.classList.add('rotate-180');
        } else {
            closePopover();
        }
    }

    function closePopover() {
        const pop = document.getElementById('popoverMonthYear');
        const caret = document.getElementById('fCustomCaret');
        if (pop && !pop.classList.contains('hidden')) {
            pop.classList.add('hidden');
            caret?.classList.remove('rotate-180');
        }
    }

    // ── Pilih Bulan & Tahun Khusus (Kustom) ──────────────────────
    function selectCustomMonthYear(month, year) {
        currentMode = 'custom';
        calMonth    = month;
        calYear     = year;

        // Update button visual states
        document.getElementById('fBulan')?.classList.remove('active');
        document.getElementById('fTahun')?.classList.remove('active');
        document.getElementById('fCustom')?.classList.add('active');

        // Update label tombol ke-3
        const labelEl = document.getElementById('fCustomLabel');
        if (labelEl) {
            labelEl.textContent = `${MONTH_NAMES[calMonth]} ${calYear}`;
        }

        // Update metric values
        const sKegiatan = document.getElementById('sKegiatan');
        const sPeserta  = document.getElementById('sPeserta');
        if (year === 2026 && month === 11) { // Desember 2026
            if (sKegiatan) sKegiatan.textContent = '19';
            if (sPeserta)  sPeserta.textContent  = '1.680';
        } else if (year === 2026 && month === 8) { // September 2026
            if (sKegiatan) sKegiatan.textContent = '14';
            if (sPeserta)  sPeserta.textContent  = '1.248';
        } else {
            const baseKegiatan = 10 + (month % 5) + (year === 2026 ? 2 : 0);
            const basePeserta  = 850 + (month * 50) + (year === 2026 ? 100 : 0);
            if (sKegiatan) sKegiatan.textContent = baseKegiatan.toString();
            if (sPeserta)  sPeserta.textContent  = basePeserta.toLocaleString('id-ID');
        }

        // Tentukan selectedDay
        const evts = CAL_EVENTS.filter(e => {
            const parts = e.date.split('-');
            return parseInt(parts[0], 10) === calYear && parseInt(parts[1], 10) === (calMonth + 1);
        });
        if (evts.length > 0) {
            selectedDay = parseInt(evts[0].date.split('-')[2], 10);
        } else {
            selectedDay = 1;
        }

        renderCalendar();
    }

    // ── Pilih Preset: 'bulan_ini' atau 'tahun_ini' ───────────────
    function setPreset(mode) {
        currentMode = mode;
        closePopover();

        const fBulan   = document.getElementById('fBulan');
        const fTahun   = document.getElementById('fTahun');
        const fCustom  = document.getElementById('fCustom');
        const labelEl  = document.getElementById('fCustomLabel');
        const sKegiatan = document.getElementById('sKegiatan');
        const sPeserta  = document.getElementById('sPeserta');

        // Reset label tombol ke-3 ke default
        if (labelEl) labelEl.textContent = 'Pilih Bulan & Tahun';

        if (mode === 'bulan_ini') {
            fBulan?.classList.add('active');
            fTahun?.classList.remove('active');
            fCustom?.classList.remove('active');

            calMonth    = 8; // September
            calYear     = 2026;
            selectedDay = 17; // Hari ini

            if (sKegiatan) sKegiatan.textContent = '14';
            if (sPeserta)  sPeserta.textContent  = '1.248';
        } else if (mode === 'tahun_ini') {
            fTahun?.classList.add('active');
            fBulan?.classList.remove('active');
            fCustom?.classList.remove('active');

            calYear  = 2026;
            // Tampilkan akumulasi tahunan
            if (sKegiatan) sKegiatan.textContent = '48';
            if (sPeserta)  sPeserta.textContent  = '4.850';
        }

        renderCalendar();
    }

    // ── Bootstrapping Saat DOM Siap ──────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        renderCalendar();

        // 3 Tombol Utama
        document.getElementById('fBulan')?.addEventListener('click', () => setPreset('bulan_ini'));
        document.getElementById('fTahun')?.addEventListener('click', () => setPreset('tahun_ini'));
        document.getElementById('fCustom')?.addEventListener('click', (e) => {
            e.stopPropagation();
            togglePopover();
        });

        // Popover Controls
        document.getElementById('popoverMonthYear')?.addEventListener('click', (e) => e.stopPropagation());

        document.getElementById('popPrevYear')?.addEventListener('click', () => {
            popoverYear--;
            document.getElementById('popYearLabel').textContent = popoverYear;
            renderPopoverMonthGrid();
        });

        document.getElementById('popNextYear')?.addEventListener('click', () => {
            popoverYear++;
            document.getElementById('popYearLabel').textContent = popoverYear;
            renderPopoverMonthGrid();
        });

        document.getElementById('popBtnCurrentMonth')?.addEventListener('click', () => {
            setPreset('bulan_ini');
        });

        document.getElementById('popBtnClose')?.addEventListener('click', () => {
            closePopover();
        });

        // Tutup popover jika klik di luar
        document.addEventListener('click', () => {
            closePopover();
        });
    });
})();
</script>
@endsection
