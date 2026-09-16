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

    {{-- Filter Presets (Satu-satunya Kontrol Rentang Waktu) --}}
    <div class="dash-filter-group">
        <div class="dash-filter-presets">
            <button id="fBulan" onclick="setFilterPreset('bulan_ini')" class="dash-preset-btn active">
                Bulan ini
            </button>
            <button id="fTahun" onclick="setFilterPreset('tahun_ini')" class="dash-preset-btn">
                Tahun ini
            </button>
        </div>
        <div class="dash-date-pill">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span id="filterDesc">September 2026</span>
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
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="6" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="18" r="1.5"/>
                </svg>
            </button>
        </div>

        <div class="flex items-baseline justify-between pt-1">
            <h3 class="dash-stat-number" id="sKegiatan">
                14
            </h3>
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
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="6" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="18" r="1.5"/>
                </svg>
            </button>
        </div>

        <div class="flex items-baseline justify-between pt-1">
            <h3 class="dash-stat-number" id="sPeserta">
                1.248
            </h3>
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

{{-- ─── Row 2: Diagram Evaluasi Utama (Full Width 100%) ─────────────── --}}
<div class="dash-card mb-5">
    {{-- Card Header: Hanya Judul & Dropdown Bentuk Diagram --}}
    <div class="flex items-center justify-between gap-3 mb-4 pb-3 border-b border-gray-100 dark:border-gray-800">
        <div>
            <h3 class="dash-card-title flex items-center gap-2">
                <span>Tren Evaluasi Sosialisasi</span>
                <span class="dash-chip">
                    N-Gain & Skor
                </span>
            </h3>
            <p class="dash-card-sub">Analisis skor pemahaman Pre-Test & Post-Test per sesi evaluasi</p>
        </div>

        {{-- Dropdown Pilih Bentuk Diagram (Hanya Garis atau Batang) --}}
        <div class="relative">
            <select id="chartTypeSelect" onchange="switchChartType(this.value)" class="dash-custom-select font-medium">
                <option value="line" selected>📈 Diagram Garis</option>
                <option value="bar">📊 Diagram Batang</option>
            </select>
        </div>
    </div>

    {{-- Container Canvas Chart.js (Full Width, Sangat Lapang & Jelas) --}}
    <div class="dash-canvas-container" style="position: relative; height: 330px; width: 100%;">
        <canvas id="evalCanvas"></canvas>
    </div>
</div>

{{-- ─── Row 3: Kalender Agenda & Riwayat Kegiatan Lengkap ─────── --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">
    {{-- Kalender Agenda (7 cols) --}}
    <div class="lg:col-span-7 dash-card">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h4 class="dash-card-title text-sm">Kalender Agenda P2M</h4>
                <p class="dash-card-sub">Jadwal pelaksanaan sosialisasi & evaluasi</p>
            </div>
            <div class="flex items-center gap-2 text-[11px] font-medium">
                <span class="flex items-center gap-1"><em class="w-2 h-2 rounded-full bg-emerald-500 inline-block not-italic"></em>Selesai</span>
                <span class="flex items-center gap-1"><em class="w-2 h-2 rounded-full bg-amber-500 inline-block not-italic"></em>Aktif</span>
                <span class="flex items-center gap-1"><em class="w-2 h-2 rounded-full inline-block not-italic" style="background: var(--primary);"></em>Mendatang</span>
            </div>
        </div>
        <div id="dashCalendar" class="text-xs"></div>
    </div>

    {{-- Kegiatan Terbaru Table (5 cols) --}}
    <div class="lg:col-span-5 dash-card flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h4 class="dash-card-title text-sm">Kegiatan Terbaru</h4>
                    <p class="dash-card-sub">Riwayat pelaksanaan terkini</p>
                </div>
                <a href="{{ route('operator.kegiatan.index') }}" class="text-xs hover:underline" style="color: var(--primary);">
                    Semua →
                </a>
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
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.dash-filter-presets {
    display: inline-flex;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 3px;
    gap: 2px;
    box-shadow: var(--shadow-xs);
}
.dash-preset-btn {
    font-family: var(--font-sans);
    padding: 0.35rem 0.85rem;
    border-radius: calc(var(--r-md) - 2px);
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--text-secondary);
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
}
.dash-preset-btn:hover {
    color: var(--text-primary);
}
.dash-preset-btn.active {
    background: var(--primary);
    color: #ffffff;
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(67, 97, 238, 0.25);
}
.dash-date-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 0.35rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary);
    box-shadow: var(--shadow-xs);
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

/* FullCalendar Theme Overrides */
#dashCalendar .fc-toolbar-title {
    font-size: 0.85rem !important;
    font-weight: 700 !important;
    font-family: var(--font-display) !important;
    color: var(--text-primary) !important;
}
#dashCalendar .fc-button {
    background: var(--surface) !important;
    border: 1px solid var(--border) !important;
    color: var(--text-secondary) !important;
    font-size: 0.72rem !important;
    border-radius: var(--r-sm) !important;
    padding: 0.25rem 0.5rem !important;
    box-shadow: none !important;
    font-family: var(--font-sans) !important;
    font-weight: 500 !important;
}
#dashCalendar .fc-button-active {
    background: var(--primary) !important;
    color: #fff !important;
    border-color: var(--primary) !important;
}
#dashCalendar .fc-event {
    border-radius: var(--r-xs) !important;
    border: none !important;
    font-size: 0.68rem !important;
    font-weight: 600 !important;
    padding: 1px 4px !important;
}
#dashCalendar td, #dashCalendar th {
    border-color: var(--border) !important;
}
</style>

{{-- ─── Script Chart.js Terbundel Lokal (100% Reliable & Smooth) ── --}}
<script>
(function() {
    // ── Dataset SIM-EVAL P2M BNN ────────────────────────────────
    const EVAL_DATA = {
        bulan_ini: {
            categories: ['10 Sep', '11 Sep', '12 Sep', '13 Sep', '14 Sep', '15 Sep'],
            // Kurva gelombang halus persis foto referensi (2.6k -> 2.25k -> 3.75k -> 2.9k -> 3.45k)
            lineScores: [2600, 3100, 2250, 3750, 2900, 3450],
            barPre:     [2400, 2800, 2100, 3200, 2600, 3000],
            barPost:    [3000, 3600, 2700, 4200, 3400, 3900],
            preAvg:     [45, 50, 48, 55, 52, 58],
            postAvg:    [82, 88, 80, 92, 85, 94],
            gainVal:    [0.67, 0.76, 0.61, 0.82, 0.69, 0.85],
        },
        tahun_ini: {
            categories: ['Jan', 'Mar', 'Mei', 'Jul', 'Sep', 'Nov'],
            lineScores: [2100, 2500, 2900, 3300, 3600, 3900],
            barPre:     [1900, 2200, 2600, 2900, 3200, 3500],
            barPost:    [2400, 2900, 3400, 3800, 4100, 4400],
            preAvg:     [40, 42, 44, 46, 48, 52],
            postAvg:    [78, 81, 84, 87, 89, 93],
            gainVal:    [0.63, 0.67, 0.71, 0.76, 0.78, 0.85],
        }
    };

    let currentPeriod = 'bulan_ini';
    let currentType   = 'line'; // DEFAULT SAAT BUKA WEB: DIAGRAM GARIS
    let chartInstance = null;

    function isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }

    // ── Helper Warna Sesuai Design Tokens SIM-EVAL ──────────────
    function getThemeColors() {
        const isDark = isDarkMode();
        return {
            accent:       '#4361EE',       // Indigo/Blue BNN
            accentLight:  'rgba(67, 97, 238, 0.32)',
            accentFaded:  'rgba(67, 97, 238, 0.00)',
            secondaryBar: isDark ? 'rgba(67, 97, 238, 0.35)' : '#C7D2FE',
            text:         isDark ? '#94A3B8' : '#64748B',
            grid:         isDark ? 'rgba(51, 65, 85, 0.45)' : 'rgba(228, 230, 239, 0.7)',
            cardBg:       isDark ? '#1E293B' : '#FFFFFF',
            border:       isDark ? '#334155' : '#E4E6EF'
        };
    }

    // ── Render / Update Grafik Chart.js ─────────────────────────
    function initOrUpdateChart() {
        const canvas = document.getElementById('evalCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const d = EVAL_DATA[currentPeriod];
        const colors = getThemeColors();

        // Buat gradasi biru halus vertikal persis gambar referensi
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, colors.accentLight);
        gradient.addColorStop(0.85, 'rgba(67, 97, 238, 0.04)');
        gradient.addColorStop(1, colors.accentFaded);

        // Siapkan Konfigurasi Dataset
        let datasets = [];
        if (currentType === 'line') {
            datasets = [{
                label: 'Skor Efektivitas',
                data: d.lineScores,
                borderColor: colors.accent,
                borderWidth: 2.75,
                backgroundColor: gradient,
                fill: true,
                tension: 0.42, // Smooth Spline Curve persis foto
                cubicInterpolationMode: 'monotone',
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: colors.accent,
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2,
            }];
        } else {
            datasets = [
                {
                    label: 'Pre-Test',
                    data: d.barPre,
                    backgroundColor: colors.secondaryBar,
                    borderColor: 'transparent',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.6,
                },
                {
                    label: 'Post-Test',
                    data: d.barPost,
                    backgroundColor: colors.accent,
                    borderColor: 'transparent',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.6,
                }
            ];
        }

        // Opsi Chart.js dengan Desain Minimalist Modern Flat & Animasi Smooth Cubic-Bezier
        const chartConfig = {
            type: currentType === 'line' ? 'line' : 'bar',
            data: {
                labels: d.categories,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                // Animasi Smooth Cubic-Bezier (Tidak Ada Bounce!)
                animation: {
                    duration: 750,
                    easing: 'easeInOutCubic',
                },
                transitions: {
                    active: {
                        animation: {
                            duration: 350,
                            easing: 'easeInOutCubic',
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: currentType === 'bar',
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: colors.text,
                            font: { family: 'Inter', size: 11, weight: 500 },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            padding: 15
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: colors.cardBg,
                        titleColor: isDarkMode() ? '#F8FAFC' : '#111827',
                        bodyColor: colors.text,
                        borderColor: colors.border,
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 700 },
                        bodyFont: { family: 'Inter', size: 11, weight: 500 },
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                const idx = context.dataIndex;
                                if (currentType === 'line') {
                                    const pre  = d.preAvg ? d.preAvg[idx] : 45;
                                    const post = d.postAvg ? d.postAvg[idx] : 85;
                                    const gain = d.gainVal ? d.gainVal[idx] : 0.74;
                                    return [
                                        ` Skor: ${context.parsed.y.toLocaleString('id-ID')}`,
                                        ` Pre-Test: ${pre} | Post-Test: ${post}`,
                                        ` N-Gain: ${gain} (Tinggi)`
                                    ];
                                }
                                return ` ${context.dataset.label}: ${context.parsed.y.toLocaleString('id-ID')} poin`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: colors.text,
                            font: { family: 'Inter', size: 11, weight: 500 }
                        },
                        border: { display: false }
                    },
                    y: {
                        min: 1000,
                        max: 4200,
                        ticks: {
                            stepSize: 1000,
                            color: colors.text,
                            font: { family: 'Inter', size: 11, weight: 500 },
                            callback: function(val) {
                                return val.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: colors.grid,
                            drawBorder: false,
                        },
                        border: { display: false }
                    }
                }
            }
        };

        // Jika chart sudah ada, destroy lalu buat dengan animasi cubic-bezier mulus
        if (chartInstance) {
            chartInstance.destroy();
        }
        chartInstance = new Chart(ctx, chartConfig);
    }

    // ── Dropdown Switcher Bentuk Diagram (Garis / Batang) ─────────
    window.switchChartType = function(type) {
        currentType = type;
        const selectEl = document.getElementById('chartTypeSelect');
        if (selectEl && selectEl.value !== type) {
            selectEl.value = type;
        }
        initOrUpdateChart();
    };

    // ── Filter Global Presets (Bulan ini / Tahun ini) ─────────────
    window.setFilterPreset = function(preset) {
        const fBulan = document.getElementById('fBulan');
        const fTahun = document.getElementById('fTahun');
        if (preset === 'bulan_ini') {
            fBulan?.classList.add('active');
            fTahun?.classList.remove('active');
            document.getElementById('filterDesc').textContent = 'September 2026';
            document.getElementById('sKegiatan').textContent  = '14';
            document.getElementById('sPeserta').textContent   = '1.248';
            currentPeriod = 'bulan_ini';
        } else {
            fTahun?.classList.add('active');
            fBulan?.classList.remove('active');
            document.getElementById('filterDesc').textContent = 'Tahun 2026';
            document.getElementById('sKegiatan').textContent  = '48';
            document.getElementById('sPeserta').textContent   = '4.850';
            currentPeriod = 'tahun_ini';
        }
        initOrUpdateChart();
    };

    // ── FullCalendar ─────────────────────────────────────────────
    function initCalendar() {
        const calEl = document.getElementById('dashCalendar');
        if (!calEl || typeof FullCalendar === 'undefined') return;
        const events = [
            { title: 'SMAN 1 Surabaya',       start: '2026-09-05', color: '#22c55e' },
            { title: 'Workshop Ketahanan',     start: '2026-09-10', color: '#22c55e' },
            { title: 'Komunitas Pemuda',       start: '2026-09-14', end: '2026-09-16', color: '#f59e0b' },
            { title: 'BUMD PDAM Sby',          start: '2026-09-22', color: '#4361EE' },
            { title: 'Tes Urin Petrokimia',    start: '2026-09-28', color: '#4361EE' }
        ];
        const cal = new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            initialDate: '2026-09-01',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events,
            height: 'auto',
            eventClick: function(info) {
                alert(`📌 ${info.event.title}\n📅 ${info.event.startStr}`);
            }
        });
        cal.render();
    }

    // ── Bootstrapping Saat DOM Siap ──────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        // Cek ketersediaan Chart.js (dari bundle Vite atau global)
        if (typeof Chart !== 'undefined') {
            initOrUpdateChart();
        } else {
            const checkTimer = setInterval(() => {
                if (typeof Chart !== 'undefined') {
                    clearInterval(checkTimer);
                    initOrUpdateChart();
                }
            }, 50);
        }

        initCalendar();
    });

    // Sinkronkan tema grafik saat tombol toggle dark mode ditekan
    const themeBtn = document.getElementById('themeToggleBtn');
    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            setTimeout(() => {
                initOrUpdateChart();
            }, 60);
        });
    }
})();
</script>
@endsection
