@extends('layouts.app')

@section('title', 'Meja Kerja & Rekapitulasi — ' . $kegiatan->nama_kegiatan)
@section('page-title', $kegiatan->nama_kegiatan)
@section('page-subtitle', 'Meja kerja evaluasi pre-test & post-test Seksi P2M BNN Kota Surabaya')

@section('content')

@php
    $s = $kegiatan->status ?? 'dijadwalkan';
    $statusLabel = $s === 'selesai' ? 'Selesai' : ($s === 'berlangsung' ? 'Berlangsung' : 'Dijadwalkan');
    $currentRole = session('current_role', 'operator');
@endphp

{{-- Breadcrumb --}}
<nav class="kd-breadcrumb mb-4 flex items-center gap-2 text-xs text-muted">
    <a href="{{ route('operator.kegiatan.index') }}" class="hover:text-primary transition-colors">Daftar Kegiatan</a>
    <span>/</span>
    <span class="text-gray-700 dark:text-gray-300 font-medium truncate max-w-xs">{{ $kegiatan->nama_kegiatan }}</span>
</nav>

{{-- Page Header Bar --}}
<div class="dash-card p-4 md:p-5 mb-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-lg md:text-xl font-bold font-display text-gray-900 dark:text-white">
                {{ $kegiatan->nama_kegiatan }}
            </h1>
            <span id="headerStatusBadge" class="badge {{ $s === 'selesai' ? 'badge-success' : ($s === 'berlangsung' ? 'badge-info' : 'badge-warning') }} font-semibold text-xs px-2.5 py-0.5 rounded-full">
                {{ $statusLabel }}
            </span>
        </div>
        <p class="text-xs text-muted mt-1 flex items-center gap-2 flex-wrap">
            <span class="flex items-center gap-1 font-medium text-gray-700 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $kegiatan->lokasi->nama_lokasi ?? 'Lokasi Binaan' }}
            </span>
            <span>&bull;</span>
            <span>{{ $kegiatan->tanggal ? \Carbon\Carbon::parse($kegiatan->tanggal)->isoFormat('dddd, D MMMM Y') : '—' }}</span>
            <span>&bull;</span>
            <span>Kode PIN: <strong class="text-primary font-semibold tracking-wider">{{ $kegiatan->kode_join }}</strong></span>
        </p>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        {{-- Export Excel Diktari BNN Button --}}
        <a href="{{ route('operator.kegiatan.export', $kegiatan->id) }}"
           class="btn btn-sm btn-primary flex items-center gap-1.5 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Unduh Format Diktari (Excel)</span>
        </a>

        {{-- Status Toggle Button --}}
        <button type="button" onclick="cycleEventStatus()" class="btn btn-sm btn-secondary flex items-center gap-1.5" id="btnToggleStatus">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span id="btnToggleStatusText">Ubah Status Sesi</span>
        </button>

        <a href="{{ route('operator.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-secondary btn-icon" title="Edit Informasi">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>

        <button type="button" onclick="confirmDeleteKegiatan()" class="btn btn-sm btn-danger btn-icon" title="Hapus Kegiatan">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    </div>
</div>

{{-- ─── 4 Stat Summary Chips ────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <div class="dash-card p-3 flex flex-col justify-center">
        <span class="text-[11px] font-semibold uppercase tracking-wider text-muted">Total Peserta</span>
        <div class="flex items-baseline gap-2 mt-1">
            <span class="text-xl font-bold text-gray-900 dark:text-white tabular-nums" id="statTotalPeserta">{{ $stats['total'] }}</span>
            <span class="text-xs text-muted">orang</span>
        </div>
    </div>

    <div class="dash-card p-3 flex flex-col justify-center">
        <span class="text-[11px] font-semibold uppercase tracking-wider text-muted">Rata-rata Pre-Test</span>
        <div class="flex items-baseline gap-2 mt-1">
            <span class="text-xl font-bold text-blue-600 dark:text-blue-400 tabular-nums" id="statAvgPre">{{ $stats['avg_pre'] }}</span>
            <span class="text-xs text-muted">/ 100</span>
        </div>
    </div>

    <div class="dash-card p-3 flex flex-col justify-center">
        <span class="text-[11px] font-semibold uppercase tracking-wider text-muted">Rata-rata Post-Test</span>
        <div class="flex items-baseline gap-2 mt-1">
            <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums" id="statAvgPost">{{ $stats['avg_post'] }}</span>
            <span class="text-xs text-muted">/ 100</span>
        </div>
    </div>

    <div class="dash-card p-3 flex flex-col justify-center border-l-4" style="border-left-color: var(--primary);">
        <span class="text-[11px] font-semibold uppercase tracking-wider text-primary">Rata-rata N-Gain Efektivitas</span>
        <div class="flex items-baseline gap-2 mt-1">
            <span class="text-xl font-extrabold text-primary tabular-nums" id="statAvgGain">{{ $stats['avg_gain'] }}</span>
            <span class="badge {{ $stats['avg_gain'] >= 0.7 ? 'badge-success' : ($stats['avg_gain'] >= 0.3 ? 'badge-warning' : 'badge-danger') }} text-[10px] uppercase font-bold py-0.5 px-1.5 rounded" id="statCategoryBadge">
                {{ $stats['avg_gain'] >= 0.7 ? 'Tinggi' : ($stats['avg_gain'] >= 0.3 ? 'Sedang' : 'Rendah') }}
            </span>
        </div>
    </div>
</div>

{{-- ─── 3 Metode Rekap Navigation Tabs ────────────────────────── --}}
<div class="dash-card mb-5 overflow-hidden">
    <div class="flex border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 px-3 pt-2 gap-1 overflow-x-auto">
        <button type="button" onclick="switchMethodTab('tabSpreadsheet')" id="tabBtnSpreadsheet"
                class="method-tab active px-4 py-2.5 text-xs font-semibold rounded-t-lg transition-all flex items-center gap-2 border-b-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Metode A: Meja Kerja Spreadsheet (Keyboard Input)</span>
            <span class="badge badge-primary text-[10px] px-1.5 py-0.2 rounded-full">Anak Magang / Operator</span>
        </button>

        <button type="button" onclick="switchMethodTab('tabOMR')" id="tabBtnOMR"
                class="method-tab px-4 py-2.5 text-xs font-semibold rounded-t-lg transition-all flex items-center gap-2 border-b-2 text-muted hover:text-gray-900 dark:hover:text-white border-transparent">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            </svg>
            <span>Metode B: Continuous OMR Scanner Simulator</span>
            <span class="badge badge-warning text-[10px] px-1.5 py-0.2 rounded-full">Kamera Audio Beep</span>
        </button>

        <button type="button" onclick="switchMethodTab('tabDigital')" id="tabBtnDigital"
                class="method-tab px-4 py-2.5 text-xs font-semibold rounded-t-lg transition-all flex items-center gap-2 border-b-2 text-muted hover:text-gray-900 dark:hover:text-white border-transparent">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span>Metode C: Digital PWA & Live Monitor</span>
            <span class="badge badge-info text-[10px] px-1.5 py-0.2 rounded-full">QR & PIN Online</span>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: MEJA KERJA SPREADSHEET (KEYBOARD-DRIVEN INPUT)          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tabSpreadsheet" class="tab-panel p-4">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>Meja Kerja Transkripsi Nilai Lembar Jawaban</span>
                    <span id="saveStatusIndicator" class="text-[11px] font-normal text-emerald-600 dark:text-emerald-400 flex items-center gap-1 opacity-0 transition-opacity">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Tersimpan otomatis
                    </span>
                </h3>
                <p class="text-xs text-muted mt-0.5">
                    <strong>Pintasan Keyboard:</strong> Tekan <kbd class="px-1.5 py-0.5 text-[10px] bg-gray-100 dark:bg-gray-800 border rounded">Enter</kbd> untuk turun ke baris bawah, <kbd class="px-1.5 py-0.5 text-[10px] bg-gray-100 dark:bg-gray-800 border rounded">Tab</kbd> untuk pindah ke Post-Test, atau <kbd class="px-1.5 py-0.5 text-[10px] bg-gray-100 dark:bg-gray-800 border rounded">Panah Bawah</kbd> pada baris akhir untuk otomatis membuat baris siswa baru.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="addNewParticipantRow()" class="btn btn-sm btn-primary flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Tambah Baris Siswa</span>
                </button>
            </div>
        </div>

        {{-- Spreadsheet Grid Table --}}
        <div class="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <table class="w-full text-left text-xs border-collapse" id="spreadsheetTable">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800/80 text-gray-700 dark:text-gray-200 uppercase font-semibold text-[11px] border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2.5 px-3 w-12 text-center">No</th>
                        <th class="py-2.5 px-3 min-w-[220px]">Nama Lengkap Siswa</th>
                        <th class="py-2.5 px-3 w-32">Kelas / Tingkat</th>
                        <th class="py-2.5 px-3 w-28 text-center bg-blue-50/50 dark:bg-blue-950/20">Pre-Test (0-100)</th>
                        <th class="py-2.5 px-3 w-28 text-center bg-emerald-50/50 dark:bg-emerald-950/20">Post-Test (0-100)</th>
                        <th class="py-2.5 px-3 w-28 text-center font-bold">N-Gain</th>
                        <th class="py-2.5 px-3 w-32 text-center">Kategori Pemahaman</th>
                        <th class="py-2.5 px-3 w-16 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-sans" id="spreadsheetBody">
                    @forelse($participants as $idx => $p)
                    <tr class="hover:bg-blue-50/20 dark:hover:bg-blue-950/10 transition-colors" data-id="{{ $p->id }}">
                        <td class="py-2 px-3 text-center text-muted tabular-nums row-no">{{ $idx + 1 }}</td>
                        <td class="py-2 px-3">
                            <input type="text"
                                   class="form-control text-xs py-1 px-2.5 w-full font-medium input-name"
                                   value="{{ $p->name }}"
                                   placeholder="Nama Siswa..."
                                   onchange="autoSaveRow(this)"
                                   onkeydown="handleKeyNavigation(event, this, 'name')">
                        </td>
                        <td class="py-2 px-3">
                            <input type="text"
                                   class="form-control text-xs py-1 px-2.5 w-full input-class"
                                   value="{{ $p->class_grade }}"
                                   placeholder="cth: XI-A"
                                   onchange="autoSaveRow(this)"
                                   onkeydown="handleKeyNavigation(event, this, 'class')">
                        </td>
                        <td class="py-2 px-3 bg-blue-50/30 dark:bg-blue-950/10">
                            <input type="number" min="0" max="100" step="1"
                                   class="form-control text-xs py-1 px-2 text-center font-bold text-blue-600 dark:text-blue-400 tabular-nums input-pre"
                                   value="{{ $p->pretest_score }}"
                                   placeholder="—"
                                   oninput="calculateRowGain(this)"
                                   onchange="autoSaveRow(this)"
                                   onkeydown="handleKeyNavigation(event, this, 'pre')">
                        </td>
                        <td class="py-2 px-3 bg-emerald-50/30 dark:bg-emerald-950/10">
                            <input type="number" min="0" max="100" step="1"
                                   class="form-control text-xs py-1 px-2 text-center font-bold text-emerald-600 dark:text-emerald-400 tabular-nums input-post"
                                   value="{{ $p->posttest_score }}"
                                   placeholder="—"
                                   oninput="calculateRowGain(this)"
                                   onchange="autoSaveRow(this)"
                                   onkeydown="handleKeyNavigation(event, this, 'post')">
                        </td>
                        <td class="py-2 px-3 text-center font-bold tabular-nums text-sm col-gain">
                            {{ $p->n_gain !== null ? number_format($p->n_gain, 2) : '—' }}
                        </td>
                        <td class="py-2 px-3 text-center col-category">
                            @if($p->category === 'paham')
                                <span class="badge badge-success text-[10px] px-2 py-0.5 rounded font-bold uppercase">Paham (Tinggi)</span>
                            @elseif($p->category === 'cukup')
                                <span class="badge badge-warning text-[10px] px-2 py-0.5 rounded font-bold uppercase">Cukup (Sedang)</span>
                            @elseif($p->category === 'kurang')
                                <span class="badge badge-danger text-[10px] px-2 py-0.5 rounded font-bold uppercase">Kurang (Rendah)</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-center">
                            <button type="button" onclick="deleteParticipantRow(this, {{ $p->id }})"
                                    class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 dark:hover:bg-rose-950/40" title="Hapus Siswa">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRowPlaceholder">
                        <td colspan="8" class="py-8 text-center text-muted">
                            Belum ada baris siswa. Klik <strong>"+ Tambah Baris Siswa"</strong> untuk mulai menginput nilai lembar kertas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: CONTINUOUS OMR SCANNER SIMULATOR (AUDIO BEEP & FLASH)   --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tabOMR" class="tab-panel p-4 hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            {{-- Kamera / Reticle Viewport --}}
            <div class="lg:col-span-7">
                <div class="relative w-full aspect-video bg-gray-950 rounded-xl overflow-hidden border-2 border-gray-800 shadow-inner flex items-center justify-center" id="scannerViewport">
                    {{-- Video / Mock Stream Canvas --}}
                    <div class="absolute inset-0 flex items-center justify-center text-center p-4" id="scannerPlaceholder">
                        <div>
                            <div class="w-14 h-14 rounded-full bg-gray-800 flex items-center justify-center mx-auto mb-2 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-300">Scanner OMR Siap Aktif</p>
                            <p class="text-xs text-gray-500 mt-1 max-w-xs">Arahkan kamera ke lembar jawaban kertas siswa atau gunakan simulasi continous scanner di bawah.</p>
                        </div>
                    </div>

                    {{-- Reticle Frame Target Box (Akan flash hijau 200ms saat lembar berhasil dipindai) --}}
                    <div id="reticleFrame" class="relative z-10 w-3/4 h-3/4 border-2 border-dashed border-white/40 rounded-lg transition-all duration-200 pointer-events-none flex items-center justify-center">
                        <span class="text-[11px] text-white/50 tracking-wider uppercase font-semibold">Posisikan Lembar OMR di Kotak Ini</span>
                    </div>

                    {{-- Sound Indicator Badge --}}
                    <div class="absolute top-3 right-3 z-20 flex items-center gap-1.5 px-2 py-1 rounded bg-black/60 text-[11px] text-emerald-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Audio Beep: 880Hz Aktif
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2.5">
                    <button type="button" onclick="triggerSingleOMRScan()" class="btn btn-primary flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        </svg>
                        <span>Pindai 1 Lembar (Trigger Scan)</span>
                    </button>

                    <button type="button" onclick="toggleContinuousOMR()" id="btnContinuousScan" class="btn btn-secondary flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span id="txtContinuousScan">Mulai Pindai Berkelanjutan (Batch Auto-Scan)</span>
                    </button>
                </div>
            </div>

            {{-- Feed Riwayat Scan Lembar --}}
            <div class="lg:col-span-5 flex flex-col">
                <div class="dash-card p-3 flex-1 flex flex-col">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-100 dark:border-gray-800">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Riwayat Lembar Terpindai</h4>
                        <span class="text-[11px] text-muted font-medium" id="scanCounter">0 lembar berhasil dicatat</span>
                    </div>

                    <div class="flex-1 overflow-y-auto max-h-72 space-y-2 text-xs" id="scanFeedList">
                        <div class="text-center py-10 text-muted" id="scanFeedEmpty">
                            <p>Belum ada lembar yang dipindai pada sesi ini.</p>
                            <p class="text-[11px] mt-1">Gunakan tombol di sebelah kiri untuk merekam lembar jawaban.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: DIGITAL PWA & LIVE MONITOR                              --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tabDigital" class="tab-panel p-4 hidden">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
            {{-- Proyektor Card (QR Code & PIN) --}}
            <div class="md:col-span-5 dash-card p-6 flex flex-col items-center justify-center text-center bg-gradient-to-b from-blue-50/50 to-transparent dark:from-blue-950/20">
                <span class="text-xs font-bold uppercase tracking-wider text-primary mb-1">Tampilkan di Layar Proyektor Aula</span>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Akses Ujian Digital Peserta</h3>

                {{-- Mock QR Code Canvas --}}
                <div class="p-3 bg-white rounded-xl shadow-md border border-gray-200 inline-block mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('participant.welcome') . '?pin=' . $kegiatan->kode_join) }}"
                         alt="QR Code Sesi" class="w-40 h-40 object-contain rounded">
                </div>

                <p class="text-xs text-muted mb-1">Atau masukkan 6 Karakter PIN Sesi:</p>
                <div class="font-display font-extrabold text-3xl tracking-widest text-primary px-4 py-1.5 bg-primary/10 rounded-lg">
                    {{ $kegiatan->kode_join }}
                </div>

                <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('participant.welcome') }}" target="_blank" class="btn btn-sm btn-secondary flex items-center gap-1 text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Buka Portal Siswa (Tab Baru)
                    </a>
                </div>
            </div>

            {{-- Live Attendance Monitor --}}
            <div class="md:col-span-7 dash-card p-4 flex flex-col">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Live Monitoring Peserta Digital</h4>
                        <p class="text-xs text-muted">Pantau status peserta yang sedang mengerjakan secara digital</p>
                    </div>
                    <span class="badge badge-success text-xs font-semibold px-2.5 py-0.5 rounded-full flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Real-time Active
                    </span>
                </div>

                <div class="flex-1 overflow-y-auto max-h-80 space-y-2 text-xs">
                    @forelse($participants as $p)
                    <div class="p-2.5 rounded-lg border border-gray-100 dark:border-gray-800 flex items-center justify-between bg-white dark:bg-gray-800/40">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($p->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $p->name }}</p>
                                <p class="text-[11px] text-muted">{{ $p->class_grade ?? 'Reguler' }} &bull; Metode: {{ strtoupper($p->input_method ?? 'Digital') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs tabular-nums text-muted font-medium">
                                Pre: <strong class="text-gray-900 dark:text-white">{{ $p->pretest_score ?? '—' }}</strong> |
                                Post: <strong class="text-gray-900 dark:text-white">{{ $p->posttest_score ?? '—' }}</strong>
                            </span>
                            @if($p->status === 'selesai')
                                <span class="badge badge-success text-[10px] px-2 py-0.5 rounded">Selesai</span>
                            @elseif($p->status === 'mengerjakan')
                                <span class="badge badge-info text-[10px] px-2 py-0.5 rounded">Mengerjakan</span>
                            @else
                                <span class="badge badge-warning text-[10px] px-2 py-0.5 rounded">Menunggu</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 text-muted">
                        Belum ada peserta yang bergabung ke sesi ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const EVENT_ID = {{ $kegiatan->id }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ─── Web Audio API Beep (880Hz A5 note) ──────────────────────
    let audioCtx = null;
    function playOMRBeep() {
        try {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime); // 880 Hz
            gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.15);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.15);
        } catch (e) {
            console.log('Audio API not allowed before interaction', e);
        }
    }

    // ─── Visual Reticle Flash (200ms) ────────────────────────────
    function flashReticleGreen() {
        const frame = document.getElementById('reticleFrame');
        if (!frame) return;
        frame.style.borderColor = '#22c55e';
        frame.style.boxShadow = '0 0 20px rgba(34, 197, 94, 0.8)';
        setTimeout(() => {
            frame.style.borderColor = 'rgba(255, 255, 255, 0.4)';
            frame.style.boxShadow = 'none';
        }, 200);
    }

    // ─── Tab Switching Logic ─────────────────────────────────────
    function switchMethodTab(tabId) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.method-tab').forEach(b => {
            b.classList.remove('active', 'border-primary', 'text-primary');
            b.classList.add('text-muted', 'border-transparent');
        });

        document.getElementById(tabId).classList.remove('hidden');
        const activeBtn = tabId === 'tabSpreadsheet' ? document.getElementById('tabBtnSpreadsheet') :
                          (tabId === 'tabOMR' ? document.getElementById('tabBtnOMR') : document.getElementById('tabBtnDigital'));

        if (activeBtn) {
            activeBtn.classList.add('active', 'border-primary', 'text-primary');
            activeBtn.classList.remove('text-muted', 'border-transparent');
        }
    }

    // ─── N-Gain Live Calculator ──────────────────────────────────
    function calculateRowGain(inputEl) {
        const tr = inputEl.closest('tr');
        const preInput = tr.querySelector('.input-pre');
        const postInput = tr.querySelector('.input-post');
        const gainCol = tr.querySelector('.col-gain');
        const catCol = tr.querySelector('.col-category');

        const pre = parseFloat(preInput.value);
        const post = parseFloat(postInput.value);

        if (!isNaN(pre) && !isNaN(post)) {
            if (pre >= 100) {
                gainCol.textContent = '1.00';
                catCol.innerHTML = '<span class="badge badge-success text-[10px] px-2 py-0.5 rounded font-bold uppercase">Paham (Tinggi)</span>';
                return;
            }
            let gain = (post - pre) / (100 - pre);
            if (gain < 0) gain = 0;
            gainCol.textContent = gain.toFixed(2);

            if (gain >= 0.7) {
                catCol.innerHTML = '<span class="badge badge-success text-[10px] px-2 py-0.5 rounded font-bold uppercase">Paham (Tinggi)</span>';
            } else if (gain >= 0.3) {
                catCol.innerHTML = '<span class="badge badge-warning text-[10px] px-2 py-0.5 rounded font-bold uppercase">Cukup (Sedang)</span>';
            } else {
                catCol.innerHTML = '<span class="badge badge-danger text-[10px] px-2 py-0.5 rounded font-bold uppercase">Kurang (Rendah)</span>';
            }
        }
    }

    // ─── Auto-Save Row via AJAX ──────────────────────────────────
    let saveTimeout = null;
    function autoSaveRow(inputEl) {
        const tr = inputEl.closest('tr');
        const rowId = tr.dataset.id || '';
        const name = tr.querySelector('.input-name').value;
        const classGrade = tr.querySelector('.input-class').value;
        const pre = tr.querySelector('.input-pre').value;
        const post = tr.querySelector('.input-post').value;

        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`/operator/kegiatan/${EVENT_ID}/peserta`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        id: rowId,
                        name: name,
                        class_grade: classGrade,
                        pretest_score: pre,
                        posttest_score: post,
                        input_method: 'manual'
                    })
                });
                const data = await res.json();
                if (data.success) {
                    tr.dataset.id = data.participant.id;
                    showSaveIndicator();
                }
            } catch (err) {
                console.error('Gagal menyimpan baris', err);
            }
        }, 300);
    }

    function showSaveIndicator() {
        const ind = document.getElementById('saveStatusIndicator');
        if (ind) {
            ind.style.opacity = '1';
            setTimeout(() => { ind.style.opacity = '0'; }, 1500);
        }
    }

    // ─── Keyboard Navigation (Enter, Tab, ArrowDown) ────────────
    function handleKeyNavigation(e, inputEl, fieldType) {
        const tr = inputEl.closest('tr');
        const tbody = tr.parentElement;

        if (e.key === 'Enter') {
            e.preventDefault();
            const nextTr = tr.nextElementSibling;
            if (nextTr) {
                const target = nextTr.querySelector(`.input-${fieldType}`);
                if (target) target.focus();
            } else {
                addNewParticipantRow(fieldType);
            }
        } else if (e.key === 'ArrowDown') {
            const nextTr = tr.nextElementSibling;
            if (nextTr) {
                const target = nextTr.querySelector(`.input-${fieldType}`);
                if (target) {
                    e.preventDefault();
                    target.focus();
                }
            } else if (fieldType === 'post' || fieldType === 'pre' || fieldType === 'name') {
                e.preventDefault();
                addNewParticipantRow(fieldType);
            }
        } else if (e.key === 'ArrowUp') {
            const prevTr = tr.previousElementSibling;
            if (prevTr) {
                const target = prevTr.querySelector(`.input-${fieldType}`);
                if (target) {
                    e.preventDefault();
                    target.focus();
                }
            }
        }
    }

    // ─── Add Blank Participant Row ───────────────────────────────
    function addNewParticipantRow(focusField = 'name') {
        const tbody = document.getElementById('spreadsheetBody');
        const placeholder = document.getElementById('emptyRowPlaceholder');
        if (placeholder) placeholder.remove();

        const count = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-blue-50/20 dark:hover:bg-blue-950/10 transition-colors animate-fade-in';
        tr.dataset.id = '';

        tr.innerHTML = `
            <td class="py-2 px-3 text-center text-muted tabular-nums row-no">${count}</td>
            <td class="py-2 px-3">
                <input type="text"
                       class="form-control text-xs py-1 px-2.5 w-full font-medium input-name"
                       value="Siswa ${count}"
                       placeholder="Nama Siswa..."
                       onchange="autoSaveRow(this)"
                       onkeydown="handleKeyNavigation(event, this, 'name')">
            </td>
            <td class="py-2 px-3">
                <input type="text"
                       class="form-control text-xs py-1 px-2.5 w-full input-class"
                       value="Reguler"
                       placeholder="cth: XI-A"
                       onchange="autoSaveRow(this)"
                       onkeydown="handleKeyNavigation(event, this, 'class')">
            </td>
            <td class="py-2 px-3 bg-blue-50/30 dark:bg-blue-950/10">
                <input type="number" min="0" max="100" step="1"
                       class="form-control text-xs py-1 px-2 text-center font-bold text-blue-600 dark:text-blue-400 tabular-nums input-pre"
                       placeholder="—"
                       oninput="calculateRowGain(this)"
                       onchange="autoSaveRow(this)"
                       onkeydown="handleKeyNavigation(event, this, 'pre')">
            </td>
            <td class="py-2 px-3 bg-emerald-50/30 dark:bg-emerald-950/10">
                <input type="number" min="0" max="100" step="1"
                       class="form-control text-xs py-1 px-2 text-center font-bold text-emerald-600 dark:text-emerald-400 tabular-nums input-post"
                       placeholder="—"
                       oninput="calculateRowGain(this)"
                       onchange="autoSaveRow(this)"
                       onkeydown="handleKeyNavigation(event, this, 'post')">
            </td>
            <td class="py-2 px-3 text-center font-bold tabular-nums text-sm col-gain">—</td>
            <td class="py-2 px-3 text-center col-category"><span class="text-muted">—</span></td>
            <td class="py-2 px-3 text-center">
                <button type="button" onclick="deleteParticipantRow(this, null)"
                        class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 dark:hover:bg-rose-950/40" title="Hapus Siswa">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        // Auto save initial row to DB
        autoSaveRow(tr.querySelector('.input-name'));

        const targetInput = tr.querySelector(`.input-${focusField}`);
        if (targetInput) {
            targetInput.focus();
            if (focusField === 'name') targetInput.select();
        }
    }

    async function deleteParticipantRow(btn, id) {
        const tr = btn.closest('tr');
        if (id) {
            if (!confirm('Hapus baris peserta ini?')) return;
            try {
                const res = await fetch(`/operator/kegiatan/${EVENT_ID}/peserta/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                const data = await res.json();
                if (data.success) tr.remove();
            } catch (err) {
                alert('Gagal menghapus baris peserta.');
            }
        } else {
            tr.remove();
        }
    }

    // ─── OMR Scanner Actions ─────────────────────────────────────
    let scanCount = 0;
    async function triggerSingleOMRScan() {
        playOMRBeep();
        flashReticleGreen();

        try {
            const res = await fetch(`/operator/kegiatan/${EVENT_ID}/omr-scan`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    class_grade: 'Reguler'
                })
            });
            const data = await res.json();
            if (data.success) {
                scanCount++;
                document.getElementById('scanCounter').textContent = `${scanCount} lembar berhasil dicatat`;

                const emptyPlaceholder = document.getElementById('scanFeedEmpty');
                if (emptyPlaceholder) emptyPlaceholder.remove();

                const list = document.getElementById('scanFeedList');
                const item = document.createElement('div');
                item.className = 'p-2 rounded bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 flex items-center justify-between animate-fade-in';
                item.innerHTML = `
                    <div>
                        <p class="font-bold text-gray-900 dark:text-gray-100">${data.participant.name}</p>
                        <p class="text-[10px] text-muted">Pre: ${data.participant.pretest_score} | Post: ${data.participant.posttest_score} | N-Gain: ${parseFloat(data.participant.n_gain).toFixed(2)}</p>
                    </div>
                    <span class="badge badge-success text-[10px] font-bold px-1.5 py-0.5 rounded">✓ Tersimpan</span>
                `;
                list.prepend(item);
            }
        } catch (e) {
            console.error('Scan error', e);
        }
    }

    let continuousInterval = null;
    function toggleContinuousOMR() {
        const btn = document.getElementById('btnContinuousScan');
        const txt = document.getElementById('txtContinuousScan');

        if (continuousInterval) {
            clearInterval(continuousInterval);
            continuousInterval = null;
            btn.className = 'btn btn-secondary flex items-center gap-2';
            txt.textContent = 'Mulai Pindai Berkelanjutan (Batch Auto-Scan)';
        } else {
            btn.className = 'btn btn-danger flex items-center gap-2';
            txt.textContent = 'Hentikan Pindai Otomatis';
            triggerSingleOMRScan();
            continuousInterval = setInterval(() => {
                triggerSingleOMRScan();
            }, 2500);
        }
    }

    // ─── Status Lifecycle Toggle ─────────────────────────────────
    async function cycleEventStatus() {
        const current = '{{ $s }}';
        let next = 'berlangsung';
        if (current === 'berlangsung') next = 'selesai';
        else if (current === 'selesai') next = 'dijadwalkan';

        try {
            const res = await fetch(`/operator/kegiatan/${EVENT_ID}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ status: next })
            });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            }
        } catch (e) {
            alert('Gagal mengubah status kegiatan.');
        }
    }

    async function confirmDeleteKegiatan() {
        if (!confirm('Apakah Anda yakin ingin menghapus seluruh data kegiatan dan seluruh pesertanya?')) return;
        try {
            const res = await fetch(`/operator/kegiatan/${EVENT_ID}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = "{{ route('operator.kegiatan.index') }}";
            }
        } catch (e) {
            alert('Gagal menghapus kegiatan.');
        }
    }
</script>
@endpush

@endsection
