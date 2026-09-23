@extends('layouts.app')

@section('page-title', 'Dashboard Evaluasi')
@section('page-subtitle', 'Monitoring efektivitas sosialisasi Pre-Test & Post-Test BNN Kota Surabaya')

@section('content')

{{-- ─── Header Section & Filter Global ───────────────────────── --}}
<div class="dash-header-bar">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <h2 class="dash-heading-lg">
                Selamat datang, {{ strtok(auth()->user()?->name ?? 'Staf', ' ') }}
            </h2>
        </div>
        <p class="dash-subtext">
            Pantauan evaluasi sosialisasi &amp; performa N-Gain BNN Kota Surabaya
        </p>
    </div>

    {{-- Filter Periode Waktu (Dropdown Elegan & Responsif) --}}
    <div class="dash-period-dropdown-wrap relative" id="dashPeriodWrap">
        <button type="button" 
                id="dashPeriodBtn" 
                class="dash-period-trigger" 
                aria-haspopup="true" 
                aria-expanded="false" 
                title="Pilih rentang waktu evaluasi">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span id="dashPeriodCurrentLabel" class="dash-period-label font-medium text-xs sm:text-sm">Bulan Ini ({{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }})</span>
            <svg xmlns="http://www.w3.org/2000/svg" id="dashPeriodCaret" class="dash-period-caret w-3.5 h-3.5 opacity-60 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Floating Dropdown Menu --}}
        <div id="dashPeriodMenu" class="dash-period-menu hidden" role="menu">
            <div class="dash-period-section-label">Pilihan Cepat</div>

            {{-- 1. Bulan Ini --}}
            <button type="button" class="dash-period-item active" id="optBulanIni" role="menuitem">
                <div class="flex flex-col text-left">
                    <span class="dash-period-item-title">Bulan Ini</span>
                    <span class="dash-period-item-sub">{{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}</span>
                </div>
                <svg class="dash-period-check w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <span class="dash-period-item-sub">Semua kegiatan tahun {{ date('Y') }}</span>
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
</div>

{{-- ─── Row 1: Empat Kartu Metrik Evaluasi P2M BNN (Slider Looping di Mobile, Grid 4-kolom di Desktop) ─── --}}
<div class="dash-metrics-slider-section mb-5">
    <div class="dash-metrics-viewport" id="dashMetricsViewport">
        <div class="dash-metrics-track" id="dashMetricsTrack">

            {{-- Card 1: Total Kegiatan --}}
            <div class="dash-metric-slide">
                <div class="dash-card dash-metric-card h-full">
                    <div class="flex items-start justify-between">
                        <div class="dash-metric-icon-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="dash-metric-body mt-3">
                        <span class="dash-metric-label">Total Sekolah Binaan</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="dash-stat-number tabular-nums" id="sKegiatan">{{ $stats['bulan_ini']['sekolah']['value'] ?? 0 }}</h3>
                            <span class="text-xs text-muted font-medium">sekolah</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Total Peserta --}}
            <div class="dash-metric-slide">
                <div class="dash-card dash-metric-card h-full">
                    <div class="flex items-start justify-between">
                        <div class="dash-metric-icon-wrap dash-metric-icon--cyan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="dash-metric-body mt-3">
                        <span class="dash-metric-label">Total Siswa Tersosialisasi</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="dash-stat-number tabular-nums" id="sPeserta">{{ $stats['bulan_ini']['peserta']['value'] ?? 0 }}</h3>
                            <span class="text-xs text-muted font-medium">siswa</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Rata-rata Skor N-Gain (Efektivitas Inti BNN) --}}
            <div class="dash-metric-slide">
                <div class="dash-card dash-metric-card h-full">
                    <div class="flex items-start justify-between">
                        <div class="dash-metric-icon-wrap dash-metric-icon--emerald">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <span id="sGainBadge" class="badge {{ $stats['bulan_ini']['nGain']['badgeColor'] ?? 'badge-green' }} text-[10px]">{{ $stats['bulan_ini']['nGain']['badge'] ?? 'Efektif' }}</span>
                    </div>
                    <div class="dash-metric-body mt-3">
                        <span class="dash-metric-label">Rata-rata N-Gain se-Surabaya</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="dash-stat-number tabular-nums text-emerald-600 dark:text-emerald-400" id="sGain">{{ $stats['bulan_ini']['nGain']['value'] ?? '—' }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Tingkat Pemahaman Audien --}}
            <div class="dash-metric-slide">
                <div class="dash-card dash-metric-card h-full">
                    <div class="flex items-start justify-between">
                        <div class="dash-metric-icon-wrap dash-metric-icon--purple">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="dash-metric-body mt-3">
                        <span class="dash-metric-label">Persentase Kategori Tinggi</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="dash-stat-number tabular-nums" id="sEfektivitas">{{ $stats['bulan_ini']['tinggi']['value'] ?? '—' }}</h3>
                            <span class="text-xs text-muted font-medium">paham</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Carousel Controls (Hanya tampil di mobile/tablet < 1024px) --}}
    <div class="dash-metrics-controls lg:hidden">
        <button type="button" id="btnPrevMetric" class="dash-metric-nav-btn" aria-label="Kartu Sebelumnya" title="Sebelumnya">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="dash-metrics-dots" id="dashMetricsDots">
            <button type="button" class="dash-metric-dot active" data-index="0" aria-label="Slide 1"></button>
            <button type="button" class="dash-metric-dot" data-index="1" aria-label="Slide 2"></button>
            <button type="button" class="dash-metric-dot" data-index="2" aria-label="Slide 3"></button>
            <button type="button" class="dash-metric-dot" data-index="3" aria-label="Slide 4"></button>
        </div>

        <button type="button" id="btnNextMetric" class="dash-metric-nav-btn" aria-label="Kartu Berikutnya" title="Berikutnya">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
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
                    <span class="cal-day-big" id="calDayBig">{{ date('j') }}</span>
                    <div class="cal-month-wrap">
                        <span class="cal-month-name" id="calMonthName">{{ \Carbon\Carbon::now()->isoFormat('MMMM') }}</span>
                        <span class="cal-year-name" id="calYearName">{{ date('Y') }}</span>
                    </div>
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
            <div class="mb-2.5 pb-2 border-b border-gray-100 dark:border-gray-800">
                <h4 class="dash-card-title text-sm">Distribusi Pemahaman Audien</h4>
                <p class="dash-card-sub">Klasifikasi skor N-Gain peserta sosialisasi</p>
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
                <span class="font-semibold text-gray-700 dark:text-gray-300">Standar Evaluasi BNN</span>
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

/* ── Filter Waktu Dropdown Elegan ── */
.dash-period-dropdown-wrap {
    position: relative;
    display: inline-block;
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
    right: 0;
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

.dash-period-item-icon {
    font-size: 1.1rem;
    line-height: 1;
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

@media (max-width: 640px) {
    .dash-header-bar {
        align-items: flex-start;
    }
    .dash-period-dropdown-wrap {
        width: auto;
        align-self: flex-start;
    }
    .dash-period-trigger {
        width: auto;
        max-width: 100%;
        justify-content: flex-start;
        padding: 0.38rem 0.75rem;
    }
    .dash-period-menu {
        left: 0;
        right: auto;
        width: 275px;
        max-width: calc(100vw - 2rem);
    }
}

/* ── Metric Cards Carousel / Grid Responsive ────────────────── */
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

    // ── Sumber metrik: server bila tersedia, mock bila tidak ──
    // Server menghitung dari data riil (DB bila ada, mock service bila kosong).
    const SERVER_METRICS = @json(['bulan_ini' => $stats['bulan_ini'] ?? null, 'tahun_ini' => $stats['tahun_ini'] ?? null]);
    function metricsSrc() {
        if (SERVER_METRICS && SERVER_METRICS.bulan_ini && SERVER_METRICS.tahun_ini) return SERVER_METRICS;
        return MockData.DASHBOARD_METRICS_DATA;
    }

    const _today = new Date();
    let calYear     = _today.getFullYear();
    let calMonth    = _today.getMonth();
    let selectedDay = _today.getDate();
    let currentMode = 'bulan_ini';
    let popoverYear = _today.getFullYear();

    const todayReal = new Date();

    // ── Sumber data kalender & terbaru: server bila tersedia ──
    // Diisi fetchServerData(); selama belum ada, pakai mock frontend.
    let serverCalendar = null;
    let serverRecent = null;
    function calEventSrc() { return serverCalendar || MockData.CALENDAR_EVENTS || []; }

    function getEventsForDate(y, m, d) {
        const key = `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        return calEventSrc().filter(e => e.date === key);
    }

    // ── Render Rincian Kegiatan Terbaru di Tabel Sisi Kanan ────────
    function renderRecentActivities() {
        const listEl = document.getElementById('recentActivitiesList');
        if (!listEl) return;

        const activities = serverRecent || MockData.RECENT_ACTIVITIES || [];
        let html = '';

        activities.forEach(act => {
            const gainDisplay = act.n_gain !== null && act.n_gain !== undefined
                ? `<span class="font-bold text-emerald-600 dark:text-emerald-400">${Number(act.n_gain)}</span>`
                : `<span class="text-gray-400 font-medium">—</span>`;

            html += `
                <div class="py-2.5 flex items-center justify-between hover:bg-gray-50/60 dark:hover:bg-gray-800/30 px-1 rounded transition-colors">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 font-sans">${escHtml(act.nama)}</p>
                        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-gray-400 font-sans">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">${escHtml(act.tanggal_short)}</span>
                            <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                            <span><span class="font-semibold text-gray-700 dark:text-gray-300">${Number(act.peserta) || 0}</span> Peserta</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-0.5">
                        <span class="badge ${escHtml(act.status_badge)} text-[10px] font-sans">${escHtml(act.status)}</span>
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

    function escHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    // ── Render Breakdown Distribusi N-Gain ─────────────────────────
    function renderDistribution(distData) {
        const container = document.getElementById('distributionContainer');
        if (!container) return;

        const items = distData || MockData.DASHBOARD_METRICS_DATA.bulan_ini.distribusi;
        let html = '';

        items.forEach(item => {
            const pct = Math.max(0, Math.min(100, Number(item.percent) || 0));
            html += `
                <div>
                    <div class="flex items-center justify-between text-xs font-sans mb-1">
                        <span class="font-medium text-gray-700 dark:text-gray-300">${escHtml(item.label)}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-muted tabular-nums">${(Number(item.count) || 0).toLocaleString('id-ID')} siswa</span>
                            <span class="font-bold text-gray-900 dark:text-white tabular-nums">${pct}%</span>
                        </div>
                    </div>
                    <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500" style="width: ${pct}%; background: ${escHtml(item.color)};"></div>
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
                ? `<div class="flex items-center gap-1"><span class="text-gray-400">N-Gain:</span><span class="font-bold text-emerald-600 dark:text-emerald-400">${Number(evt.nGain)} (${escHtml(evt.kategoriGain)})</span></div>`
                : '';

            eventsHtml += `
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 hover:border-gray-200 dark:hover:border-gray-700 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h5 class="text-xs font-bold text-gray-900 dark:text-white font-sans tracking-tight line-clamp-1">${escHtml(evt.title)}</h5>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-sans mt-0.5">${escHtml(evt.category)}</p>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0" style="background: ${escHtml(evt.color)}"></span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-2.5 pt-2 border-t border-gray-200/50 dark:border-gray-700/50 text-[11px] text-gray-500 dark:text-gray-400 font-sans">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>${escHtml(evt.time)}</span>
                        </div>
                        <div class="flex items-center gap-1.5 min-w-0">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate">${escHtml(evt.location)}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>${escHtml(evt.participants)}</span>
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
                        <span class="badge ${escHtml(events[0].badgeClass)} text-[10px] font-sans flex-shrink-0">${escHtml(events[0].status)}</span>
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

        // 1. Total Sekolah Binaan (angka master, sama tiap periode)
        const elKegiatan = document.getElementById('sKegiatan');
        if (elKegiatan) elKegiatan.textContent = data.sekolah ? data.sekolah.value : data.kegiatan.value;

        // 2. Total Siswa Tersosialisasi
        const elPeserta = document.getElementById('sPeserta');
        if (elPeserta) elPeserta.textContent = data.peserta.value;

        // 3. N-Gain
        const elGain = document.getElementById('sGain');
        const elGainBadge = document.getElementById('sGainBadge');
        if (elGain) elGain.textContent = data.nGain.value;
        if (elGainBadge) {
            elGainBadge.textContent = data.nGain.badge;
            elGainBadge.className = `badge ${data.nGain.badgeColor} text-[10px]`;
        }

        // 4. Persentase Kategori Tinggi (paham)
        const elEfektivitas = document.getElementById('sEfektivitas');
        if (elEfektivitas) elEfektivitas.textContent = data.tinggi ? data.tinggi.value : data.efektivitas.value;

        // Render Distribution Bars
        renderDistribution(data.distribusi);
    }

    // ── Dropdown Filter Periode Waktu ─────────────────────────────
    const periodMenu   = document.getElementById('dashPeriodMenu');
    const periodBtn    = document.getElementById('dashPeriodBtn');
    const periodCaret  = document.getElementById('dashPeriodCaret');
    const currentLabel = document.getElementById('dashPeriodCurrentLabel');
    const optBulanIni  = document.getElementById('optBulanIni');
    const optBulanLalu = document.getElementById('optBulanLalu');
    const optTahunIni  = document.getElementById('optTahunIni');
    const customToggle = document.getElementById('btnToggleCustomPeriod');
    const customPanel  = document.getElementById('customPeriodForm');
    const customCaret  = document.getElementById('customPeriodCaret');

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
        [optBulanIni, optBulanLalu, optTahunIni].forEach(el => el?.classList.remove('active'));
        if (activeItem) activeItem.classList.add('active');
        if (currentLabel && displayTitle) {
            currentLabel.textContent = displayTitle;
        }
    }

    // ── Ambil metrik + kalender + terbaru dari server (tanpa reload) ──
    const DASHBOARD_DATA_URL = "{{ route('operator.dashboard.data') }}";
    async function fetchServerData(mode, month, year) {
        try {
            const params = new URLSearchParams({ mode });
            if (mode === 'custom') { params.set('month', String(month + 1)); params.set('year', String(year)); }
            const res = await fetch(`${DASHBOARD_DATA_URL}?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('fetch gagal');
            const json = await res.json();
            if (json.metrics) updateMetricsUI(json.metrics);
            if (Array.isArray(json.calendar) && json.calendar.length) serverCalendar = json.calendar;
            if (Array.isArray(json.recent)) { serverRecent = json.recent; renderRecentActivities(); }
            renderCalendar();
            return true;
        } catch (e) {
            return false;
        }
    }

    // ── Pilih Bulan & Tahun Khusus ────────────────────────────────
    function selectCustomMonthYear(month, year, customTitle) {
        currentMode = 'custom';
        calMonth    = month;
        calYear     = year;

        const labelText = customTitle || `${MockData.MONTH_NAMES[calMonth]} ${calYear}`;
        setActivePeriodItem(null, labelText);

        fetchServerData('custom', month, year).then(ok => {
            if (ok) {
                snapCalendarToFirstEvent();
                return;
            }
            const metricsFn = MockData.DASHBOARD_METRICS_DATA.getCustomData;
            const metricsData = typeof metricsFn === 'function'
                ? metricsFn(month, year)
                : MockData.DASHBOARD_METRICS_DATA.bulan_ini;

            updateMetricsUI(metricsData);
            snapCalendarToFirstEvent();
        });
    }

    function snapCalendarToFirstEvent() {
        const evts = calEventSrc().filter(e => {
            const parts = (e.date || '').split('-');
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
        closePeriodDropdown();

        const today = new Date();
        if (mode === 'bulan_ini') {
            calMonth    = today.getMonth();
            calYear     = today.getFullYear();
            selectedDay = today.getDate();

            setActivePeriodItem(optBulanIni, `Bulan Ini (${MockData.MONTH_NAMES[calMonth]} ${calYear})`);

            fetchServerData('bulan_ini').then(ok => {
                if (!ok) { updateMetricsUI(metricsSrc().bulan_ini); renderCalendar(); }
            });
        } else if (mode === 'tahun_ini') {
            calYear  = today.getFullYear();

            setActivePeriodItem(optTahunIni, `Tahun Ini (${calYear})`);

            fetchServerData('tahun_ini').then(ok => {
                if (!ok) { updateMetricsUI(metricsSrc().tahun_ini); renderCalendar(); }
            });
        }
    }

    // ── Bootstrapping Saat DOM Siap ──────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        // Sinkronisasi data awal (server bila ada)
        updateMetricsUI(metricsSrc().bulan_ini);
        renderRecentActivities();
        renderCalendar();
        fetchServerData('bulan_ini');

        // Dropdown Trigger Toggle
        periodBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            togglePeriodDropdown();
        });

        // Don't close dropdown when clicking inside menu
        periodMenu?.addEventListener('click', (e) => e.stopPropagation());

        // Preset 1: Bulan Ini
        optBulanIni?.addEventListener('click', () => {
            setPreset('bulan_ini');
        });

        // Preset 2: Bulan Lalu
        optBulanLalu?.addEventListener('click', () => {
            const prevM = parseInt(optBulanLalu.getAttribute('data-month'), 10);
            const prevY = parseInt(optBulanLalu.getAttribute('data-year'), 10);
            selectCustomMonthYear(prevM, prevY, `Bulan Lalu (${MockData.MONTH_NAMES[prevM]} ${prevY})`);
            setActivePeriodItem(optBulanLalu, `Bulan Lalu (${MockData.MONTH_NAMES[prevM]} ${prevY})`);
            closePeriodDropdown();
        });

        // Preset 3: Tahun Ini
        optTahunIni?.addEventListener('click', () => {
            setPreset('tahun_ini');
        });

        // Toggle Custom Form
        customToggle?.addEventListener('click', (e) => {
            e.stopPropagation();
            if (customPanel) {
                const isClosed = customPanel.classList.contains('hidden');
                customPanel.classList.toggle('hidden');
                customCaret?.classList.toggle('rotate-180', isClosed);
            }
        });

        // Apply Custom Month/Year
        document.getElementById('btnApplyCustom')?.addEventListener('click', () => {
            const m = parseInt(document.getElementById('selCustomMonth').value, 10);
            const y = parseInt(document.getElementById('selCustomYear').value, 10);
            selectCustomMonthYear(m, y, `${MockData.MONTH_NAMES[m]} ${y}`);
            closePeriodDropdown();
        });

        // Tutup dropdown jika klik di luar
        document.addEventListener('click', () => {
            closePeriodDropdown();
        });

        // ESC untuk tutup dropdown
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closePeriodDropdown();
            }
        });

        // ── Carousel Kartu Metrik dengan Looping (Mobile & Tablet) ──
        let currentMetricIndex = 0;
        const totalMetricSlides = 4;
        const metricTrack = document.getElementById('dashMetricsTrack');
        const metricViewport = document.getElementById('dashMetricsViewport');
        const metricDots = document.querySelectorAll('.dash-metric-dot');
        const btnPrevMetric = document.getElementById('btnPrevMetric');
        const btnNextMetric = document.getElementById('btnNextMetric');

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
    });
})();
</script>
@endsection
