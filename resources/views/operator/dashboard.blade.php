@extends('layouts.app')

@section('page-title', 'Dashboard Evaluasi')
@section('page-subtitle', 'Monitoring efektivitas sosialisasi Pre-Test & Post-Test BNN Kota Surabaya')

@section('content')

{{-- ─── Header Section & Filter Global ───────────────────────── --}}
<div class="dash-header-bar">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <h2 class="dash-heading-lg">
                Selamat datang, Wahyu
            </h2>
            <span class="dash-live-badge hidden sm:inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistem P2M Aktif
            </span>
        </div>
        <p class="dash-subtext flex items-center gap-2 flex-wrap">
            <span>Pantauan evaluasi sosialisasi & performa N-Gain BNN Kota Surabaya</span>
            <span class="text-gray-300 dark:text-gray-700">&middot;</span>
            <span class="text-primary font-semibold text-xs flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span id="activePeriodDisplay">Periode: September 2026</span>
            </span>
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
                    <span class="popover-title">Pilih Waktu Evaluasi</span>
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

                <div class="popover-footer">
                    <button type="button" id="popBtnCurrentMonth" class="popover-foot-action">
                        Kembali ke Bulan Ini
                    </button>
                    <button type="button" id="popBtnClose" class="popover-foot-close">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Row 1: Empat Kartu Metrik Evaluasi P2M BNN ────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

    {{-- Card 1: Total Kegiatan --}}
    <div class="dash-card dash-metric-card">
        <div class="flex items-start justify-between">
            <div class="dash-metric-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span id="sKegiatanGrowth" class="badge-growth-up">+16.7%</span>
        </div>
        <div class="dash-metric-body mt-2">
            <span class="dash-metric-label">Total Kegiatan</span>
            <div class="flex items-baseline gap-1.5">
                <h3 class="dash-stat-number tabular-nums" id="sKegiatan">14</h3>
                <span class="text-xs text-muted font-medium">kegiatan</span>
            </div>
            <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                <span id="sKegiatanSub">kegiatan terlaksana</span>
                <span id="sKegiatanPeriod" class="text-muted text-[10px]">vs bulan lalu</span>
            </div>
        </div>
    </div>

    {{-- Card 2: Total Peserta --}}
    <div class="dash-card dash-metric-card">
        <div class="flex items-start justify-between">
            <div class="dash-metric-icon-wrap dash-metric-icon--cyan">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span id="sPesertaGrowth" class="badge-growth-up">+23.4%</span>
        </div>
        <div class="dash-metric-body mt-2">
            <span class="dash-metric-label">Total Peserta</span>
            <div class="flex items-baseline gap-1.5">
                <h3 class="dash-stat-number tabular-nums" id="sPeserta">1.248</h3>
                <span class="text-xs text-muted font-medium">peserta</span>
            </div>
            <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                <span id="sPesertaSub">peserta terdaftar</span>
                <span id="sPesertaPeriod" class="text-muted text-[10px]">vs bulan lalu</span>
            </div>
        </div>
    </div>

    {{-- Card 3: Rata-rata Skor N-Gain (Efektivitas Inti BNN) --}}
    <div class="dash-card dash-metric-card">
        <div class="flex items-start justify-between">
            <div class="dash-metric-icon-wrap dash-metric-icon--emerald">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <span id="sGainBadge" class="badge badge-green text-[10px]">Efektif</span>
        </div>
        <div class="dash-metric-body mt-2">
            <span class="dash-metric-label">Rata-rata Skor N-Gain</span>
            <div class="flex items-baseline gap-1.5">
                <h3 class="dash-stat-number tabular-nums text-emerald-600 dark:text-emerald-400" id="sGain">0.72</h3>
                <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400" id="sGainGrowth">+0.06</span>
            </div>
            <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                <span id="sGainSub">kategori efektivitas tinggi</span>
                <span class="text-muted text-[10px]">skor IKU</span>
            </div>
        </div>
    </div>

    {{-- Card 4: Tingkat Pemahaman Audien --}}
    <div class="dash-card dash-metric-card">
        <div class="flex items-start justify-between">
            <div class="dash-metric-icon-wrap dash-metric-icon--purple">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <span id="sEfektivitasGrowth" class="badge-growth-up">+5.2%</span>
        </div>
        <div class="dash-metric-body mt-2">
            <span class="dash-metric-label">Tingkat Pemahaman</span>
            <div class="flex items-baseline gap-1.5">
                <h3 class="dash-stat-number tabular-nums text-purple-600 dark:text-purple-400" id="sEfektivitas">84.6%</h3>
                <span class="text-xs text-muted font-medium">audien</span>
            </div>
            <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                <span id="sEfektivitasSub">kategori paham/cukup</span>
                <span class="text-muted text-[10px]">IKU BNN</span>
            </div>
        </div>
    </div>
</div>

{{-- ─── Row 3: Kalender Agenda & Sisi Kanan (Kegiatan + Distribusi) ─ --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">

    {{-- Kolom Kiri: Kalender Agenda Interaktif (7 cols) --}}
    <div class="lg:col-span-7 dash-card flex flex-col justify-between">
        <div>
            {{-- Header Kalender: Tanggal besar (dikontrol oleh filter global) --}}
            <div class="cal-header">
                <div class="cal-date-display">
                    <span class="cal-day-big" id="calDayBig">17</span>
                    <div class="cal-month-wrap">
                        <span class="cal-month-name" id="calMonthName">September</span>
                        <span class="cal-year-name" id="calYearName">2026</span>
                    </div>
                </div>
                <div class="cal-nav-legend">
                    <div class="cal-legend-row">
                        <span class="cal-dot" style="background:#22c55e"></span><span class="cal-legend-text">Selesai</span>
                        <span class="cal-dot" style="background:#06b6d4"></span><span class="cal-legend-text">Berlangsung</span>
                        <span class="cal-dot" style="background:#f59e0b"></span><span class="cal-legend-text">Aktif</span>
                        <span class="cal-dot" style="background:var(--primary)"></span><span class="cal-legend-text">Mendatang</span>
                    </div>
                    <span class="text-[10px] text-gray-400 font-sans mt-0.5">Klik tanggal bertitik untuk rincian</span>
                </div>
            </div>

            {{-- Grid Kalender --}}
            <div id="dashCalendar">
                {{-- Dirender via JS --}}
            </div>
        </div>

        {{-- Detail Acara Terpilih --}}
        <div id="calEventDetail" class="cal-event-detail-wrap mt-4 pt-3.5 border-t border-gray-100 dark:border-gray-800">
            {{-- Dirender secara dinamis via JS saat tanggal dipilih --}}
        </div>
    </div>

    {{-- Kolom Kanan: Kegiatan Terbaru & Distribusi N-Gain (5 cols) --}}
    <div class="lg:col-span-5 flex flex-col gap-4">

        {{-- Card 1: Kegiatan Terbaru --}}
        <div class="dash-card">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h4 class="dash-card-title text-sm">Kegiatan Terbaru</h4>
                    <p class="dash-card-sub">Riwayat pelaksanaan 5 kegiatan terkini</p>
                </div>
                <span class="text-[11px] font-semibold text-primary font-sans">
                    Pre-Test / Post-Test
                </span>
            </div>

            <div id="recentActivitiesList" class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                {{-- Dirender via JS dari RECENT_ACTIVITIES --}}
            </div>

            <div class="pt-3 mt-2 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('operator.kegiatan.index') }}" class="w-full py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-semibold text-center block transition-colors" style="border-radius: var(--r-md);">
                    Kelola Seluruh Kegiatan &rarr;
                </a>
            </div>
        </div>

        {{-- Card 2: Distribusi Efektivitas & Pemahaman (N-Gain Breakdown) --}}
        <div class="dash-card">
            <div class="flex items-center justify-between mb-2.5 pb-2 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h4 class="dash-card-title text-sm">Distribusi Pemahaman Audien</h4>
                    <p class="dash-card-sub">Klasifikasi skor N-Gain peserta sosialisasi</p>
                </div>
                <span class="dash-chip">Standar Hake</span>
            </div>

            {{-- Progress Bars --}}
            <div id="distributionContainer" class="space-y-3 py-1">
                {{-- Dirender via JS --}}
            </div>

            <div class="mt-3 pt-2.5 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Target IKU Tercapai (&ge; 75%)</span>
                </span>
                <span class="font-semibold text-gray-700 dark:text-gray-300">Format Diktari BNN</span>
            </div>
        </div>

    </div>
</div>

{{-- ─── Styles Scoped Sesuai Prinsip Desain SIM-EVAL ─────────── --}}
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
    font-size: 1.85rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--text-primary);
    line-height: 1;
}

/* Metric Card Layout */
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
    background: rgba(139, 92, 246, 0.12);
    color: #8b5cf6;
}
.dash-metric-body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.dash-metric-label {
    font-family: var(--font-sans);
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
    letter-spacing: 0.01em;
}
.dash-metric-sub {
    font-family: var(--font-sans);
    font-size: 0.6875rem;
    font-weight: 500;
    color: var(--text-secondary);
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

/* ── Custom Minimalist Calendar ─────────────────────────────── */
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
    gap: 0.25rem;
}
.cal-legend-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
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
</style>

{{-- ─── Script Kalender & Filter Presets Terhubung Mock Data ─────── --}}
<script>
(function() {
    // ── Akses Mock Data (dari modul window.DashboardMock atau internal fallback)
    const MockData = window.DashboardMock || {
        MONTH_NAMES: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],
        MONTH_NAMES_SHORT: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        DAY_NAMES: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        DAY_FULL_NAMES: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        CALENDAR_EVENTS: [
            { id: 1, date: '2026-09-05', color: '#22c55e', status: 'Selesai', badgeClass: 'badge-green', title: 'Sosialisasi P4GN SMAN 1 Surabaya', category: 'Pendidikan / Remaja', time: '08:30 - 11:30 WIB', location: 'Aula SMAN 1 Surabaya', participants: '65 Siswa', nGain: 0.78, kategoriGain: 'Tinggi' },
            { id: 2, date: '2026-09-10', color: '#22c55e', status: 'Selesai', badgeClass: 'badge-green', title: 'Workshop Ketahanan Keluarga Anti Narkoba', category: 'Keluarga / Masyarakat', time: '09:00 - 12:00 WIB', location: 'Kec. Tegalsari Surabaya', participants: '40 Warga', nGain: 0.71, kategoriGain: 'Tinggi' },
            { id: 3, date: '2026-09-14', color: '#06b6d4', status: 'Berlangsung', badgeClass: 'badge-cyan', title: 'Pembinaan & Edukasi Komunitas Pemuda Bersinar', category: 'Komunitas Pemuda', time: '13:00 - 15:30 WIB', location: 'Kel. Jambangan Surabaya', participants: '80 Pemuda', nGain: 0.68, kategoriGain: 'Sedang' },
            { id: 4, date: '2026-09-17', color: '#f59e0b', status: 'Aktif', badgeClass: 'badge-yellow', title: 'Monitoring & Evaluasi Pelaksanaan P4GN Triwulan III', category: 'Internal BNN & Mitra', time: '09:30 - 12:00 WIB', location: 'Ruang Rapat BNN Kota Surabaya', participants: '25 Peserta', nGain: null, kategoriGain: '—' },
            { id: 5, date: '2026-09-22', color: '#4361ee', status: 'Dijadwalkan', badgeClass: 'badge-blue', title: 'Sosialisasi Bahaya Narkoba Lingkungan Kerja PDAM Surya Sembada', category: 'Instansi BUMD', time: '08:00 - 12:00 WIB', location: 'Kantor Pusat PDAM Surabaya', participants: '50 Karyawan', nGain: null, kategoriGain: '—' },
            { id: 6, date: '2026-09-28', color: '#4361ee', status: 'Dijadwalkan', badgeClass: 'badge-blue', title: 'Pemeriksaan & Deteksi Dini Tes Urin Berkala', category: 'Dunia Usaha / Swasta', time: '08:30 - 13:30 WIB', location: 'Kawasan Industri Rungkut Surabaya', participants: '120 Pekerja', nGain: null, kategoriGain: '—' },
            { id: 10, date: '2026-12-05', color: '#22c55e', status: 'Selesai', badgeClass: 'badge-green', title: 'Sosialisasi Bahaya Narkoba Akhir Semester — SMK N 2 Surabaya', category: 'Pendidikan / Remaja', time: '08:30 - 11:00 WIB', location: 'Auditorium SMK N 2 Surabaya', participants: '95 Siswa', nGain: 0.75, kategoriGain: 'Tinggi' },
            { id: 11, date: '2026-12-12', color: '#f59e0b', status: 'Aktif', badgeClass: 'badge-yellow', title: 'Rapat Koordinasi Evaluasi Tahunan Relawan P4GN Surabaya', category: 'Masyarakat & Penggiat', time: '09:00 - 12:30 WIB', location: 'Gedung Graha Sawunggaling Surabaya', participants: '110 Peserta', nGain: 0.69, kategoriGain: 'Sedang' },
            { id: 12, date: '2026-12-22', color: '#4361ee', status: 'Mendatang', badgeClass: 'badge-blue', title: 'Kampanye Terpadu Libur Nataru Bersinar (Bersih Narkoba)', category: 'Masyarakat Umum / Transportasi', time: '08:00 - 14:00 WIB', location: 'Terminal Purabaya & Stasiun Gubeng', participants: '350 Sasaran', nGain: null, kategoriGain: '—' }
        ],
        RECENT_ACTIVITIES: [
            { id: 1, nama: 'SMAN 1 Surabaya', tanggal_short: '05 Sep', peserta: 65, status: 'Selesai', status_badge: 'badge-green', n_gain: 0.78, n_gain_kategori: 'Tinggi' },
            { id: 2, nama: 'Kec. Tegalsari', tanggal_short: '10 Sep', peserta: 40, status: 'Selesai', status_badge: 'badge-green', n_gain: 0.71, n_gain_kategori: 'Tinggi' },
            { id: 3, nama: 'Kel. Jambangan', tanggal_short: '14 Sep', peserta: 80, status: 'Berlangsung', status_badge: 'badge-cyan', n_gain: 0.68, n_gain_kategori: 'Sedang' },
            { id: 4, nama: 'Aula PDAM Sby', tanggal_short: '22 Sep', peserta: 50, status: 'Dijadwalkan', status_badge: 'badge-gray', n_gain: null, n_gain_kategori: '—' },
            { id: 5, nama: 'Lapas Kelas I Surabaya', tanggal_short: '28 Sep', peserta: 120, status: 'Dijadwalkan', status_badge: 'badge-gray', n_gain: null, n_gain_kategori: '—' }
        ],
        DASHBOARD_METRICS_DATA: {
            bulan_ini: {
                periodLabel: 'September 2026',
                kegiatan: { value: 14, growth: '+16.7%', sub: 'kegiatan terlaksana', growthLabel: 'vs bulan lalu' },
                peserta: { value: '1.248', growth: '+23.4%', sub: 'peserta terdaftar', growthLabel: 'vs bulan lalu' },
                nGain: { value: '0.72', growth: '+0.06', badge: 'Efektif', badgeColor: 'badge-green', sub: 'kategori efektivitas tinggi' },
                efektivitas: { value: '84.6%', growth: '+5.2%', sub: 'kategori paham/cukup' },
                distribusi: [
                    { label: 'Paham / Tinggi (g ≥ 0.70)', count: 874, percent: 70.0, color: 'var(--success, #22c55e)' },
                    { label: 'Cukup / Sedang (0.30 ≤ g < 0.70)', count: 288, percent: 23.1, color: 'var(--warning, #f59e0b)' },
                    { label: 'Kurang / Rendah (g < 0.30)', count: 86, percent: 6.9, color: 'var(--danger, #ef4444)' }
                ]
            },
            tahun_ini: {
                periodLabel: 'Tahun 2026',
                kegiatan: { value: 48, growth: '+32.4%', sub: 'kegiatan terlaksana tahun ini', growthLabel: 'vs tahun 2025' },
                peserta: { value: '4.850', growth: '+28.1%', sub: 'total penerima sosialisasi', growthLabel: 'vs tahun 2025' },
                nGain: { value: '0.74', growth: '+0.09', badge: 'Efektif Tinggi', badgeColor: 'badge-green', sub: 'rata-rata N-Gain tahunan' },
                efektivitas: { value: '86.2%', growth: '+6.4%', sub: 'tingkat efektivitas rata-rata' },
                distribusi: [
                    { label: 'Paham / Tinggi (g ≥ 0.70)', count: 3492, percent: 72.0, color: 'var(--success, #22c55e)' },
                    { label: 'Cukup / Sedang (0.30 ≤ g < 0.70)', count: 1067, percent: 22.0, color: 'var(--warning, #f59e0b)' },
                    { label: 'Kurang / Rendah (g < 0.30)', count: 291, percent: 6.0, color: 'var(--danger, #ef4444)' }
                ]
            },
            getCustomData(m, y) {
                return this.bulan_ini;
            }
        }
    };

    let calYear     = 2026;
    let calMonth    = 8; // September (0-indexed)
    let selectedDay = 17; // Default terpilih
    let currentMode = 'bulan_ini';
    let popoverYear = 2026;

    const todayReal = new Date();

    function getEventsForDate(y, m, d) {
        const key = `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        return (MockData.CALENDAR_EVENTS || []).filter(e => e.date === key);
    }

    // ── Render Rincian Kegiatan Terbaru di Tabel Sisi Kanan ────────
    function renderRecentActivities() {
        const listEl = document.getElementById('recentActivitiesList');
        if (!listEl) return;

        const activities = MockData.RECENT_ACTIVITIES || [];
        let html = '';

        activities.forEach(act => {
            const gainDisplay = act.n_gain !== null
                ? `<span class="font-bold text-emerald-600 dark:text-emerald-400">${act.n_gain}</span>`
                : `<span class="text-gray-400 font-medium">—</span>`;

            html += `
                <div class="py-2.5 flex items-center justify-between hover:bg-gray-50/60 dark:hover:bg-gray-800/30 px-1 rounded transition-colors">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 font-sans">${act.nama}</p>
                        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-gray-400 font-sans">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">${act.tanggal_short}</span>
                            <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                            <span><span class="font-semibold text-gray-700 dark:text-gray-300">${act.peserta}</span> Peserta</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-0.5">
                        <span class="badge ${act.status_badge} text-[10px] font-sans">${act.status}</span>
                        <p class="text-[11px] text-gray-400 font-sans flex items-center gap-1 mt-0.5">
                            <span>Gain:</span>
                            ${gainDisplay}
                        </p>
                    </div>
                </div>
            `;
        });

        listEl.innerHTML = html;
    }

    // ── Render Breakdown Distribusi N-Gain ─────────────────────────
    function renderDistribution(distData) {
        const container = document.getElementById('distributionContainer');
        if (!container) return;

        const items = distData || MockData.DASHBOARD_METRICS_DATA.bulan_ini.distribusi;
        let html = '';

        items.forEach(item => {
            html += `
                <div>
                    <div class="flex items-center justify-between text-xs font-sans mb-1">
                        <span class="font-medium text-gray-700 dark:text-gray-300">${item.label}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-muted tabular-nums">${item.count.toLocaleString('id-ID')} siswa</span>
                            <span class="font-bold text-gray-900 dark:text-white tabular-nums">${item.percent}%</span>
                        </div>
                    </div>
                    <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500" style="width: ${item.percent}%; background: ${item.color};"></div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // ── Render Detail Acara Kalender ──────────────────────────────
    function renderEventDetail(y, m, d) {
        const detailEl = document.getElementById('calEventDetail');
        if (!detailEl) return;

        const dateObj = new Date(y, m, d);
        const dayName = MockData.DAY_FULL_NAMES[dateObj.getDay()];
        const formattedDate = `${dayName}, ${d} ${MockData.MONTH_NAMES[m]} ${y}`;
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
            const gainRow = evt.nGain !== null && evt.nGain !== undefined
                ? `<div class="flex items-center gap-1"><span class="text-gray-400">N-Gain:</span><span class="font-bold text-emerald-600 dark:text-emerald-400">${evt.nGain} (${evt.kategoriGain})</span></div>`
                : '';

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
                        ${gainRow}
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

    // ── Render Kalender Bulanan ───────────────────────────────────
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
        if (monthNameEl) monthNameEl.textContent = MockData.MONTH_NAMES[calMonth];
        if (yearNameEl)  yearNameEl.textContent  = calYear;

        let html = '<div class="cal-grid-wrap">';
        html += '<div class="cal-row cal-head-row">';
        MockData.DAY_NAMES.forEach(d => { html += `<div class="cal-cell cal-head-cell">${d}</div>`; });
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

        // Pasang listener klik tanggal
        calEl.querySelectorAll('.cal-date-cell').forEach(cell => {
            cell.addEventListener('click', function() {
                const day = parseInt(this.getAttribute('data-day'), 10);
                if (!day) return;
                selectedDay = day;

                calEl.querySelectorAll('.cal-date-cell').forEach(c => c.classList.remove('cal-selected'));
                this.classList.add('cal-selected');

                if (dayBigEl) dayBigEl.textContent = selectedDay;
                renderEventDetail(calYear, calMonth, selectedDay);
            });
        });

        renderEventDetail(calYear, calMonth, selectedDay);
    }

    // ── Update Tampilan Metrik KPI ────────────────────────────────
    function updateMetricsUI(data) {
        if (!data) return;

        // Active period badge in header
        const activePeriod = document.getElementById('activePeriodDisplay');
        if (activePeriod) activePeriod.textContent = `Periode: ${data.periodLabel}`;

        // 1. Total Kegiatan
        const elKegiatan = document.getElementById('sKegiatan');
        const elKegiatanGrowth = document.getElementById('sKegiatanGrowth');
        const elKegiatanSub = document.getElementById('sKegiatanSub');
        const elKegiatanPeriod = document.getElementById('sKegiatanPeriod');
        if (elKegiatan) elKegiatan.textContent = data.kegiatan.value;
        if (elKegiatanGrowth) elKegiatanGrowth.textContent = data.kegiatan.growth;
        if (elKegiatanSub) elKegiatanSub.textContent = data.kegiatan.sub;
        if (elKegiatanPeriod) elKegiatanPeriod.textContent = data.kegiatan.growthLabel;

        // 2. Total Peserta
        const elPeserta = document.getElementById('sPeserta');
        const elPesertaGrowth = document.getElementById('sPesertaGrowth');
        const elPesertaSub = document.getElementById('sPesertaSub');
        const elPesertaPeriod = document.getElementById('sPesertaPeriod');
        if (elPeserta) elPeserta.textContent = data.peserta.value;
        if (elPesertaGrowth) elPesertaGrowth.textContent = data.peserta.growth;
        if (elPesertaSub) elPesertaSub.textContent = data.peserta.sub;
        if (elPesertaPeriod) elPesertaPeriod.textContent = data.peserta.growthLabel;

        // 3. N-Gain
        const elGain = document.getElementById('sGain');
        const elGainGrowth = document.getElementById('sGainGrowth');
        const elGainBadge = document.getElementById('sGainBadge');
        const elGainSub = document.getElementById('sGainSub');
        if (elGain) elGain.textContent = data.nGain.value;
        if (elGainGrowth) elGainGrowth.textContent = data.nGain.growth;
        if (elGainBadge) {
            elGainBadge.textContent = data.nGain.badge;
            elGainBadge.className = `badge ${data.nGain.badgeColor} text-[10px]`;
        }
        if (elGainSub) elGainSub.textContent = data.nGain.sub;

        // 4. Efektivitas
        const elEfektivitas = document.getElementById('sEfektivitas');
        const elEfektivitasGrowth = document.getElementById('sEfektivitasGrowth');
        const elEfektivitasSub = document.getElementById('sEfektivitasSub');
        if (elEfektivitas) elEfektivitas.textContent = data.efektivitas.value;
        if (elEfektivitasGrowth) elEfektivitasGrowth.textContent = data.efektivitas.growth;
        if (elEfektivitasSub) elEfektivitasSub.textContent = data.efektivitas.sub;

        // Render Distribution Bars
        renderDistribution(data.distribusi);
    }

    // ── Render Grid Popover 12 Bulan ──────────────────────────────
    function renderPopoverMonthGrid() {
        const grid = document.getElementById('popMonthGrid');
        if (!grid) return;

        let html = '';
        MockData.MONTH_NAMES_SHORT.forEach((mName, idx) => {
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
            const popYearInp = document.getElementById('popYearInput');
            if (popYearInp) popYearInp.value = popoverYear;
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

    // ── Pilih Bulan & Tahun Khusus ────────────────────────────────
    function selectCustomMonthYear(month, year) {
        currentMode = 'custom';
        calMonth    = month;
        calYear     = year;

        document.getElementById('fBulan')?.classList.remove('active');
        document.getElementById('fTahun')?.classList.remove('active');
        document.getElementById('fCustom')?.classList.add('active');

        const labelEl = document.getElementById('fCustomLabel');
        if (labelEl) {
            labelEl.textContent = `${MockData.MONTH_NAMES[calMonth]} ${calYear}`;
        }

        // Ambil data mock kustom
        const metricsFn = MockData.DASHBOARD_METRICS_DATA.getCustomData;
        const metricsData = typeof metricsFn === 'function'
            ? metricsFn(month, year)
            : MockData.DASHBOARD_METRICS_DATA.bulan_ini;

        updateMetricsUI(metricsData);

        // Cari tanggal pertama yang ada kegiatan di bulan tersebut
        const evts = (MockData.CALENDAR_EVENTS || []).filter(e => {
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

        if (labelEl) labelEl.textContent = 'Pilih Bulan & Tahun';

        if (mode === 'bulan_ini') {
            fBulan?.classList.add('active');
            fTahun?.classList.remove('active');
            fCustom?.classList.remove('active');

            calMonth    = 8; // September
            calYear     = 2026;
            selectedDay = 17;

            updateMetricsUI(MockData.DASHBOARD_METRICS_DATA.bulan_ini);
        } else if (mode === 'tahun_ini') {
            fTahun?.classList.add('active');
            fBulan?.classList.remove('active');
            fCustom?.classList.remove('active');

            calYear  = 2026;
            updateMetricsUI(MockData.DASHBOARD_METRICS_DATA.tahun_ini);
        }

        renderCalendar();
    }

    // ── Bootstrapping Saat DOM Siap ──────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        // Sinkronisasi data awal
        updateMetricsUI(MockData.DASHBOARD_METRICS_DATA.bulan_ini);
        renderRecentActivities();
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

        const popYearInput = document.getElementById('popYearInput');

        document.getElementById('popPrevYear')?.addEventListener('click', () => {
            popoverYear--;
            if (popYearInput) popYearInput.value = popoverYear;
            renderPopoverMonthGrid();
        });

        document.getElementById('popNextYear')?.addEventListener('click', () => {
            popoverYear++;
            if (popYearInput) popYearInput.value = popoverYear;
            renderPopoverMonthGrid();
        });

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
                    popoverYear = val;
                    this.value = val;
                    renderPopoverMonthGrid();
                } else {
                    this.value = popoverYear;
                }
            });
        }

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
