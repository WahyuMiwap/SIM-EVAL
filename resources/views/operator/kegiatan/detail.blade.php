@extends('layouts.app')

@section('title', 'Detail Kegiatan & Rekapitulasi — ' . $kegiatan->nama_kegiatan)
@section('page-title', $kegiatan->nama_kegiatan)
@section('page-subtitle', 'Detail kegiatan evaluasi pre-test & post-test Seksi P2M BNN Kota Surabaya')

@section('content')

@php
    $currentRole = auth()->user()?->role ?? 'operator';
@endphp



{{-- ─── Breadcrumb Navigasi Halus (Gaya Soft SIM-EVAL) ────────────── --}}
<nav class="kf-breadcrumb flex items-center gap-1.5 mb-3.5 text-xs">
    <a href="{{ route('operator.kegiatan.index') }}" class="text-slate-500 hover:text-primary transition-colors flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Daftar Kegiatan</span>
    </a>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $kegiatan->nama_kegiatan }}</span>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-slate-400">Detail Kegiatan</span>
</nav>

{{-- ─── 1. Header Cockpit & Kartu Metrik Soft (Rounded-2xl, Tanpa Garis Keras) ─ --}}
<div class="glass mb-5 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs overflow-hidden">
    {{-- Baris Atas: Identitas Kegiatan & Tombol Aksi --}}
    <div class="p-4 md:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        {{-- Icon Soft & Metadata --}}
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 tracking-tight">{{ $kegiatan->nama_kegiatan }}</h2>
                    <span class="badge badge-blue">PIN: {{ $kegiatan->kode_join }}</span>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mt-1 flex-wrap">
                    <span class="flex items-center gap-1 font-medium text-slate-600 dark:text-slate-300">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $kegiatan->lokasi->nama_lokasi ?? 'Lokasi Binaan' }}
                    </span>
                    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $kegiatan->tanggal ? \Carbon\Carbon::parse($kegiatan->tanggal)->isoFormat('dddd, D MMMM Y') : '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Action Buttons Soft --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('operator.kegiatan.export', $kegiatan->id) }}" class="btn btn-primary btn-sm rounded-xl shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Unduh Hasil Rekap (Excel)</span>
            </a>

            <a href="{{ route('operator.kegiatan.edit', $kegiatan->id) }}"
               class="btn btn-secondary btn-icon rounded-xl" style="width: 2.125rem; height: 2.125rem;"
               title="Edit Informasi Kegiatan">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>

            @if(auth()->user()?->role !== 'magang')
            <button type="button" onclick="confirmDeleteKegiatan()"
                    class="btn btn-secondary btn-icon rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30" style="width: 2.125rem; height: 2.125rem;"
                    title="Hapus Kegiatan">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
            @endif
        </div>
    </div>

    {{-- ─── Kontrol Sesi: Status + Fase Pre/Post (pintu masuk siswa) ─── --}}
    @php
        $evStatus = strtolower($kegiatan->status ?? 'dijadwalkan');
        $evFase = strtoupper($kegiatan->status_fase ?? 'DRAFT');
        $faseLabels = ['DRAFT' => 'Persiapan', 'PRE_ACTIVE' => 'Pre-Test dibuka', 'MATERIAL_PAUSED' => 'Jeda materi',
            'POST_ACTIVE' => 'Post-Test dibuka', 'COMPLETED' => 'Sesi selesai'];
    @endphp
    <div class="px-4 md:px-5 py-3 bg-slate-50/60 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center gap-2.5"
         id="kontrolSesi" data-event-id="{{ $kegiatan->id }}"
         data-status-url="{{ route('operator.kegiatan.status', $kegiatan->id) }}"
         data-fase-url="{{ route('operator.kegiatan.fase', $kegiatan->id) }}">
        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kontrol Sesi:</span>
        @if($evStatus === 'selesai')
            <span class="badge badge-green">Selesai</span>
        @elseif($evStatus === 'berlangsung')
            <span class="badge badge-cyan">Berlangsung</span>
        @else
            <span class="badge badge-gray">Dijadwalkan</span>
        @endif
        <span class="badge badge-blue">{{ $faseLabels[$evFase] ?? $evFase }}</span>

        @if(auth()->user()?->role !== 'magang')
        <span class="flex items-center gap-2 ml-auto flex-wrap">
        @if($evStatus === 'dijadwalkan')
            <button type="button" onclick="kirimStatusSesi('berlangsung')" class="btn btn-primary btn-sm rounded-xl shadow-xs">Mulai Sesi</button>
        @elseif($evStatus === 'berlangsung' && $evFase === 'DRAFT')
            <button type="button" onclick="kirimFaseSesi('PRE_ACTIVE')" class="btn btn-primary btn-sm rounded-xl shadow-xs">Buka Pre-Test</button>
        @elseif($evStatus === 'berlangsung' && $evFase === 'PRE_ACTIVE')
            <button type="button" onclick="kirimFaseSesi('MATERIAL_PAUSED')" class="btn btn-secondary btn-sm rounded-xl">Jeda Materi</button>
        @elseif($evStatus === 'berlangsung' && $evFase === 'MATERIAL_PAUSED')
            <button type="button" onclick="kirimFaseSesi('POST_ACTIVE')" class="btn btn-primary btn-sm rounded-xl shadow-xs">Buka Post-Test</button>
        @elseif($evStatus === 'berlangsung' && $evFase === 'POST_ACTIVE')
            <button type="button" onclick="selesaikanSesi()" class="btn btn-primary btn-sm rounded-xl shadow-xs">Selesaikan Sesi</button>
        @endif
        </span>
        @endif
    </div>
    <script>
    (function () {
        const box = document.getElementById('kontrolSesi');
        if (!box) return;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        async function patch(url, body) {
            const res = await fetch(url, {
                method: 'PATCH',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(body),
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok || json.success === false) throw new Error(json.message || 'Gagal mengubah sesi.');
            window.location.reload();
        }
        window.kirimFaseSesi = function (fase) { patch(box.dataset.faseUrl, { fase }).catch(e => alert(e.message)); };
        window.kirimStatusSesi = function (status) { patch(box.dataset.statusUrl, { status }).catch(e => alert(e.message)); };
        window.selesaikanSesi = async function () {
            try {
                await patch(box.dataset.faseUrl, { fase: 'COMPLETED' });
            } catch (e) { /* lanjut ubah status walau fase gagal */ }
            patch(box.dataset.statusUrl, { status: 'selesai' }).catch(e => alert(e.message));
        };
    })();
    </script>

    {{-- Baris Bawah: 4 Indikator Kinerja Soft Bento (Micro-Cards Terpisah) --}}
    <div class="p-3 md:p-4 bg-slate-50/60 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-800/80">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            {{-- Metrik 1: Total Peserta --}}
            <div class="p-3 bg-white dark:bg-slate-800/90 rounded-xl border border-slate-200/60 dark:border-slate-700/50 shadow-xs flex flex-col justify-between min-h-[72px]">
                <div class="flex items-start justify-between gap-1 mb-1">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block truncate">Total Peserta</span>
                    <span class="badge badge-gray text-[9.5px] shrink-0">SPJ</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-bold text-slate-800 dark:text-white tabular-nums" id="statTotalPeserta">{{ $stats['total'] }}</span>
                    <span class="text-xs text-slate-400">Siswa</span>
                </div>
            </div>

            {{-- Metrik 2: Rata-rata Pre-Test --}}
            <div class="p-3 bg-white dark:bg-slate-800/90 rounded-xl border border-slate-200/60 dark:border-slate-700/50 shadow-xs flex flex-col justify-between min-h-[72px]">
                <div class="flex items-start justify-between gap-1 mb-1">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block truncate">Rata-rata Pre-Test</span>
                    <span class="badge badge-gray text-[9.5px] shrink-0">Baseline</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-bold text-slate-800 dark:text-white tabular-nums" id="statAvgPre">{{ $stats['avg_pre'] }}</span>
                    <span class="text-xs text-slate-400">/ 100</span>
                </div>
            </div>

            {{-- Metrik 3: Rata-rata Post-Test --}}
            <div class="p-3 bg-white dark:bg-slate-800/90 rounded-xl border border-slate-200/60 dark:border-slate-700/50 shadow-xs flex flex-col justify-between min-h-[72px]">
                <div class="flex items-start justify-between gap-1 mb-1">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block truncate">Rata-rata Post-Test</span>
                    <span class="badge badge-gray text-[9.5px] shrink-0">Evaluasi</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-bold text-slate-800 dark:text-white tabular-nums" id="statAvgPost">{{ $stats['avg_post'] }}</span>
                    <span class="text-xs text-slate-400">/ 100</span>
                </div>
            </div>

            {{-- Metrik 4: Efektivitas N-Gain --}}
            <div class="p-3 bg-white dark:bg-slate-800/90 rounded-xl border border-slate-200/60 dark:border-slate-700/50 shadow-xs flex flex-col justify-between min-h-[72px]">
                <div class="flex items-start justify-between gap-1 mb-1">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block truncate">Efektivitas N-Gain</span>
                    <span class="badge badge-gray text-[9.5px] shrink-0" id="statCategoryBadge">
                        {{ $stats['avg_gain'] >= 0.7 ? 'Tinggi' : ($stats['avg_gain'] >= 0.3 ? 'Sedang' : 'Rendah') }}
                    </span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-bold text-slate-800 dark:text-white tabular-nums" id="statAvgGain">{{ $stats['avg_gain'] }}</span>
                    <span class="text-xs text-slate-400">skor</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── 2. Workspace View Dropdown & Status Toolbar ─────── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3.5 relative z-30">
    {{-- Dropdown Navigasi Tab Workspace (Gaya UI sama persis dengan Combobox Sasaran) --}}
    <div class="relative z-30" id="tabDropdownContainer" style="width: 250px;">
        <button type="button"
                id="tabDropdownTrigger"
                onclick="toggleTabDropdown()"
                class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer flex items-center justify-between text-left shadow-2xs"
                style="height: 38px;">
            <span class="truncate font-medium text-slate-700 dark:text-slate-200" id="currentTabLabel">
                Detail Kegiatan (Spreadsheet)
            </span>
            <span class="text-slate-400 flex items-center transition-transform duration-200 flex-shrink-0 ml-1.5" id="tabDropdownChevron">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </span>
        </button>

        {{-- Floating Dropdown Card (Divide-y dengan Subtitle persis Gambar 2) --}}
        <div id="tabDropdownMenu"
             class="hidden absolute left-0 z-50 mt-1.5 w-64 sm:w-72 max-h-80 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl text-xs py-1 divide-y divide-slate-100/70 dark:divide-slate-800/70">
            
            {{-- Item 1: Detail Kegiatan (Spreadsheet) --}}
            <button type="button"
                    id="optSpreadsheet"
                    onclick="selectTabFromDropdown('tabSpreadsheet')"
                    class="tab-dropdown-option w-full text-left px-3.5 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors cursor-pointer group bg-slate-50/90 dark:bg-slate-800/60">
                <span class="block font-semibold text-slate-800 dark:text-slate-100 text-xs transition-colors">Detail Kegiatan (Spreadsheet)</span>
                <span class="block text-[11px] text-slate-400 font-normal mt-0.5">Transkripsi nilai & input manual</span>
            </button>

            {{-- Item 2: Simulator Scanner OMR --}}
            <button type="button"
                    id="optOMR"
                    onclick="selectTabFromDropdown('tabOMR')"
                    class="tab-dropdown-option w-full text-left px-3.5 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors cursor-pointer group">
                <span class="block font-semibold text-slate-800 dark:text-slate-100 text-xs transition-colors">Simulator Scanner OMR</span>
                <span class="block text-[11px] text-slate-400 font-normal mt-0.5">Pindai lembar LJK otomatis</span>
            </button>

            {{-- Item 3: Live Monitor Digital PWA --}}
            <button type="button"
                    id="optDigital"
                    onclick="selectTabFromDropdown('tabDigital')"
                    class="tab-dropdown-option w-full text-left px-3.5 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors cursor-pointer group">
                <span class="block font-semibold text-slate-800 dark:text-slate-100 text-xs transition-colors">Live Monitor Digital PWA</span>
                <span class="block text-[11px] text-slate-400 font-normal mt-0.5">Pantau ujian siswa live & PIN/QR</span>
            </button>
        </div>
    </div>

    {{-- Status Simpan Otomatis --}}
    <div id="saveStatusIndicator" class="text-xs font-medium text-slate-600 dark:text-slate-300 flex items-center gap-1.5 opacity-0 transition-opacity bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200/70 dark:border-slate-700" style="height: 38px;">
        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
        <span>Perubahan tersimpan otomatis</span>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- TAB 1: MEJA KERJA SPREADSHEET (SOFT FLUSH CELL INPUT)            --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div id="tabSpreadsheet" class="tab-panel">
    {{-- Sub-Toolbar Meja Kerja: Pintasan & Tombol Tambah --}}
    <div class="flex items-center justify-between gap-3 mb-2.5 px-1">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Lembar Transkripsi Nilai</span>
            <span id="tableCountBadge" class="text-xs text-slate-400">({{ count($participants) }} Partisipan)</span>
        </div>

        <div class="flex items-center gap-3">
            {{-- Petunjuk Fisik Keyboard Micro-UI (Soft Style) --}}
            <div class="hidden md:flex items-center gap-1.5 text-[11px] text-slate-400">
                <span class="px-1.5 py-0.5 text-[10px] font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700 rounded text-slate-600 dark:text-slate-300">Enter</span>
                <span>bawah</span>
                <span>&bull;</span>
                <span class="px-1.5 py-0.5 text-[10px] font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700 rounded text-slate-600 dark:text-slate-300">Tab</span>
                <span>kolom</span>
                <span>&bull;</span>
                <span class="px-1.5 py-0.5 text-[10px] font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700 rounded text-slate-600 dark:text-slate-300">↓</span>
                <span>tambah</span>
            </div>

            {{-- Tombol Tambah Baris Siswa (Soft Blue Rounded-xl) --}}
            <button type="button" onclick="addNewParticipantRow()" class="btn btn-primary btn-sm rounded-xl shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Baris Siswa</span>
            </button>
        </div>
    </div>

    {{-- Tabel Grid Spreadsheet Profesional (Soft Card Container) --}}
    <div class="glass overflow-x-auto p-0 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs">
        <table class="w-full text-left text-xs border-collapse" id="spreadsheetTable">
            <thead>
                <tr class="bg-slate-50/70 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10.5px] border-b border-slate-100 dark:border-slate-800 tracking-wider">
                    <th class="py-3 px-3 w-12 text-center">No</th>
                    <th class="py-3 px-3 min-w-[220px]">Nama Lengkap Siswa</th>
                    <th class="py-3 px-3 w-32">Kelas / Tingkat</th>
                    <th class="py-3 px-3 w-28 text-center text-slate-700 dark:text-slate-200 font-bold">Pre-Test (0-100)</th>
                    <th class="py-3 px-3 w-28 text-center text-slate-700 dark:text-slate-200 font-bold">Post-Test (0-100)</th>
                    <th class="py-3 px-3 w-28 text-center text-slate-700 dark:text-slate-200 font-extrabold">N-Gain</th>
                    <th class="py-3 px-3 w-36 text-center">Kategori Pemahaman</th>
                    <th class="py-3 px-3 w-14 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-sans" id="spreadsheetBody">
                @forelse($participants as $idx => $p)
                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors group" data-id="{{ $p->id }}">
                    <td class="py-2.5 px-3 text-center text-slate-400 tabular-nums row-no font-medium">{{ $idx + 1 }}</td>
                    
                    {{-- Nama Siswa (Flush Input) --}}
                    <td class="py-1 px-2">
                        <input type="text"
                               class="w-full bg-transparent px-2.5 py-1.5 text-xs text-slate-800 dark:text-slate-100 font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 rounded-lg transition-all input-name"
                               value="{{ $p->name }}"
                               placeholder="Nama Siswa..."
                               onchange="autoSaveRow(this)"
                               onkeydown="handleKeyNavigation(event, this, 'name')">
                    </td>

                    {{-- Kelas (Flush Input) --}}
                    <td class="py-1 px-2">
                        <input type="text"
                               class="w-full bg-transparent px-2.5 py-1.5 text-xs text-slate-600 dark:text-slate-300 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 rounded-lg transition-all input-class"
                               value="{{ $p->class_grade }}"
                               placeholder="cth: XI-A"
                               onchange="autoSaveRow(this)"
                               onkeydown="handleKeyNavigation(event, this, 'class')">
                    </td>

                    {{-- Nilai Pre-Test (Flush Center Input Soft Tint) --}}
                    <td class="py-1 px-2">
                        <input type="number" min="0" max="100" step="1"
                               class="w-full bg-blue-50/30 dark:bg-blue-950/20 px-2 py-1.5 text-center text-xs font-bold text-blue-600 dark:text-blue-400 tabular-nums focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-400/30 rounded-lg transition-all input-pre"
                               value="{{ $p->pretest_score }}"
                               placeholder="—"
                               oninput="calculateRowGain(this)"
                               onchange="autoSaveRow(this)"
                               onkeydown="handleKeyNavigation(event, this, 'pre')">
                    </td>

                    {{-- Nilai Post-Test (Flush Center Input Soft Tint) --}}
                    <td class="py-1 px-2">
                        <input type="number" min="0" max="100" step="1"
                               class="w-full bg-emerald-50/30 dark:bg-emerald-950/20 px-2 py-1.5 text-center text-xs font-bold text-emerald-600 dark:text-emerald-400 tabular-nums focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 rounded-lg transition-all input-post"
                               value="{{ $p->posttest_score }}"
                               placeholder="—"
                               oninput="calculateRowGain(this)"
                               onchange="autoSaveRow(this)"
                               onkeydown="handleKeyNavigation(event, this, 'post')">
                    </td>

                    {{-- N-Gain --}}
                    <td class="py-2.5 px-3 text-center font-bold tabular-nums text-xs text-slate-800 dark:text-slate-100 col-gain">
                        {{ $p->n_gain !== null ? number_format($p->n_gain, 2) : '—' }}
                    </td>

                    {{-- Kategori Pemahaman (Soft Badge Pills) --}}
                    <td class="py-2.5 px-3 text-center col-category">
                        @if($p->category === 'paham')
                            <span class="badge badge-green">Paham</span>
                        @elseif($p->category === 'cukup')
                            <span class="badge badge-yellow">Cukup</span>
                        @elseif($p->category === 'kurang')
                            <span class="badge badge-red">Kurang</span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>

                    {{-- Aksi Hapus Soft --}}
                    <td class="py-2.5 px-3 text-center">
                        <button type="button" onclick="deleteParticipantRow(this, {{ $p->id }})"
                                class="btn btn-ghost btn-icon text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg"
                                style="width: 1.75rem; height: 1.75rem;" title="Hapus Siswa">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr id="emptyRowPlaceholder">
                    <td colspan="8" class="py-12 text-center text-slate-400">
                        Belum ada baris siswa. Klik <strong>"+ Tambah Baris Siswa"</strong> untuk mulai menginput nilai lembar kertas.
                    </td>
                </tr>
                @endforelse
            </tbody>

            {{-- ─── Pinned Table Summary Footer Soft ─────────────────── --}}
            <tfoot>
                <tr class="bg-slate-50/80 dark:bg-slate-800/80 border-t border-slate-200/80 dark:border-slate-700 font-semibold text-xs">
                    <td class="py-3 px-3 text-center text-slate-400">&Sigma;</td>
                    <td class="py-3 px-3 uppercase tracking-wider text-slate-700 dark:text-slate-200 font-bold text-[10.5px]">RATA-RATA & REKAP KELAS</td>
                    <td class="py-3 px-3 text-slate-500 font-medium" id="footerTotalPeserta">{{ $stats['total'] }} Siswa</td>
                    <td class="py-3 px-3 text-center text-blue-600 dark:text-blue-400 font-extrabold tabular-nums" id="footerAvgPre">{{ $stats['avg_pre'] }}</td>
                    <td class="py-3 px-3 text-center text-emerald-600 dark:text-emerald-400 font-extrabold tabular-nums" id="footerAvgPost">{{ $stats['avg_post'] }}</td>
                    <td class="py-3 px-3 text-center font-black text-slate-800 dark:text-white tabular-nums text-sm" id="footerAvgGain">{{ $stats['avg_gain'] }}</td>
                    <td class="py-3 px-3 text-center" id="footerAvgCat">
                        <span class="badge {{ $stats['avg_gain'] >= 0.7 ? 'badge-green' : ($stats['avg_gain'] >= 0.3 ? 'badge-yellow' : 'badge-red') }}">
                            {{ $stats['avg_gain'] >= 0.7 ? 'Paham' : ($stats['avg_gain'] >= 0.3 ? 'Cukup' : 'Kurang') }}
                        </span>
                    </td>
                    <td class="py-3 px-3 text-center"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- TAB 2: CONTINUOUS OMR SCANNER SIMULATOR (AUDIO BEEP & FLASH)     --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div id="tabOMR" class="tab-panel hidden">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        {{-- Kamera / Reticle Viewport & Meja Pindai --}}
        <div class="lg:col-span-7">
            <div class="glass p-4 sm:p-5 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs">
                {{-- Viewport Kamera Vertikal (Proporsi A4 Portrait) --}}
                <div class="relative w-full max-w-[420px] mx-auto bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 flex items-center justify-center shadow-inner"
                     id="scannerViewport"
                     style="aspect-ratio: 3 / 4; width: 100%; min-height: 400px; max-height: 520px;">
                    {{-- Video kamera (disembunyikan sampai dinyalakan) --}}
                    <video id="omrVideo" class="absolute inset-0 w-full h-full object-cover hidden" playsinline muted></video>

                    {{-- State saat kamera belum aktif (Placeholder informatif tanpa tabrakan teks) --}}
                    <div class="absolute inset-0 flex items-center justify-center text-center p-6 z-0" id="scannerPlaceholder">
                        <div class="max-w-xs">
                            <div class="w-14 h-14 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center justify-center mx-auto mb-3.5 text-blue-400 shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <circle cx="12" cy="13" r="3" stroke-width="1.6" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-100">Kamera Scanner OMR</h4>
                            <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                                Arahkan kamera ke lembar jawaban vertikal bertanda silang (X) atau jalankan simulasi di bawah.
                            </p>
                        </div>
                    </div>

                    {{-- Reticle Frame Target Box (Kotak Bidik Vertikal Proporsi A4 dengan Corner Brackets) --}}
                    <div id="reticleFrame"
                         class="relative z-10 border-2 border-dashed border-white/40 rounded-2xl transition-all duration-200 pointer-events-none"
                         style="width: 80%; height: 84%;">
                        {{-- 4 Corner Accent Markers (Aksen Sudut Khas Scanner Dokumen Cerdas) --}}
                        <div class="absolute -top-1 -left-1 border-t-2 border-l-2 border-blue-400 rounded-tl-lg" style="width: 1.125rem; height: 1.125rem;"></div>
                        <div class="absolute -top-1 -right-1 border-t-2 border-r-2 border-blue-400 rounded-tr-lg" style="width: 1.125rem; height: 1.125rem;"></div>
                        <div class="absolute -bottom-1 -left-1 border-b-2 border-l-2 border-blue-400 rounded-bl-lg" style="width: 1.125rem; height: 1.125rem;"></div>
                        <div class="absolute -bottom-1 -right-1 border-b-2 border-r-2 border-blue-400 rounded-br-lg" style="width: 1.125rem; height: 1.125rem;"></div>
                    </div>

                    {{-- Floating Guidance Pill di Bawah Viewport (Tidak Menumpuk di Tengah) --}}
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-950/85 backdrop-blur-md text-[11px] text-slate-200 border border-white/10 shadow-md pointer-events-none whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <rect x="6" y="3" width="12" height="18" rx="2" stroke-width="1.8"/>
                            <line x1="9" y1="8" x2="15" y2="8" stroke-width="1.5"/>
                            <line x1="9" y1="12" x2="15" y2="12" stroke-width="1.5"/>
                        </svg>
                        <span>Posisikan lembar OMR vertikal di dalam kotak</span>
                    </div>

                    {{-- Top-Left: Format Kertas Badge --}}
                    <div class="absolute top-3 left-3 z-20 flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-950/75 backdrop-blur-md text-[10.5px] font-medium border border-white/10 text-slate-300 shadow-sm pointer-events-none">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        <span>Format A4 Vertikal</span>
                    </div>

                    {{-- Top-Right: Sound Indicator Badge --}}
                    <div class="absolute top-3 right-3 z-20 flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-950/75 backdrop-blur-md text-[10.5px] font-medium border border-white/10 text-emerald-400 shadow-sm pointer-events-none">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-semibold">Beep 880Hz</span>
                    </div>
                </div>

                {{-- Kontrol Aksi & Pengaturan Lembar --}}
                <div class="max-w-[420px] mx-auto mt-4 space-y-3">
                    {{-- Baris 1: Tombol Utama Kamera (2 Kolom Seimbang) --}}
                    <div class="grid grid-cols-2 gap-2.5">
                        <button type="button" id="btnOmrCamera" class="btn btn-secondary rounded-xl py-2.5 px-3 flex items-center justify-center gap-2 text-xs font-semibold shadow-xs transition-all active:scale-[0.98]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            </svg>
                            <span>Nyalakan Kamera</span>
                        </button>

                        <button type="button" id="btnOmrSnap" class="btn btn-primary rounded-xl py-2.5 px-3 flex items-center justify-center gap-2 text-xs font-semibold shadow-xs transition-all active:scale-[0.98]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="12" r="3.2" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            </svg>
                            <span>Jepret & Baca</span>
                        </button>
                    </div>

                    {{-- Baris 2: Panduan Status Realtime --}}
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 px-1">
                        <span id="omrReadStatus" class="truncate font-medium">Nyalakan kamera, posisikan lembar dalam kotak, lalu jepret.</span>
                        <span class="text-[10px] text-slate-400 shrink-0 ml-2">Auto-Center ON</span>
                    </div>

                    {{-- Baris 3: Toolbar Mode Simulasi (Demo Tanpa Kamera) --}}
                    <div class="p-2.5 rounded-xl bg-slate-50/90 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-600 dark:text-slate-300">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="font-semibold">Mode Simulasi Demo:</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
                            <button type="button" onclick="triggerSingleOMRScan()" class="btn btn-ghost btn-sm text-xs rounded-lg border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 py-1.5 px-3 font-medium text-slate-700 dark:text-slate-200 shadow-2xs">
                                <span>+ 1 Lembar</span>
                            </button>
                            <button type="button" onclick="toggleContinuousOMR()" id="btnContinuousScan" class="btn btn-ghost btn-sm text-xs rounded-lg border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 py-1.5 px-3 font-medium text-slate-700 dark:text-slate-200 flex items-center justify-center gap-1.5 shadow-2xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span id="txtContinuousScan">Batch Otomatis</span>
                            </button>
                        </div>
                    </div>

                    {{-- Baris 4: Form Identitas & Pengaturan Lembar --}}
                    <div class="pt-3.5 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-[11px] font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Identitas & Opsi Lembar</h4>
                            <span class="text-[10.5px] text-slate-400">P2M OMR Engine</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div class="sm:col-span-2">
                                <label for="omrName" class="block text-[10.5px] font-medium text-slate-500 dark:text-slate-400 mb-1">Nama Siswa / Partisipan</label>
                                <input type="text" id="omrName" list="omrRoster" placeholder="Ketik 2–3 huruf nama siswa..."
                                       autocomplete="off"
                                       class="w-full h-9.5 px-3 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 dark:text-slate-100 shadow-2xs">
                                <datalist id="omrRoster">
                                    @foreach($participants as $rp)
                                    <option value="{{ $rp->name }}">{{ $rp->class_grade ?? '' }}</option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label for="omrKelas" class="block text-[10.5px] font-medium text-slate-500 dark:text-slate-400 mb-1">Kelas / Kelompok</label>
                                <input type="text" id="omrKelas" placeholder="cth: X MIPA 1 / Reguler"
                                       class="w-full h-9.5 px-3 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 dark:text-slate-100 shadow-2xs">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="omrStage" class="block text-[10.5px] font-medium text-slate-500 dark:text-slate-400 mb-1">Fase Ujian</label>
                                    <select id="omrStage"
                                            class="w-full h-9.5 px-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 dark:text-slate-100 cursor-pointer shadow-2xs">
                                        <option value="pretest">Pre-Test</option>
                                        <option value="posttest">Post-Test</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="omrStageCount" class="block text-[10.5px] font-medium text-slate-500 dark:text-slate-400 mb-1">Jumlah Soal</label>
                                    <select id="omrStageCount" title="Jumlah soal per lembar"
                                            class="w-full h-9.5 px-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 dark:text-slate-100 cursor-pointer shadow-2xs">
                                        <option value="10" selected>10 Soal</option>
                                        <option value="15">15 Soal</option>
                                        <option value="20">20 Soal</option>
                                        <option value="25">25 Soal</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Panel Review Wajib (Muncul Setelah Baca) --}}
                    <div id="omrReview" class="hidden rounded-2xl border border-blue-200 dark:border-blue-900 bg-blue-50/50 dark:bg-blue-950/20 p-4">
                        <div class="flex items-center justify-between mb-1">
                            <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100">Verifikasi Hasil Pindaian</h5>
                            <span class="text-[11px] text-slate-500" id="omrQrInfo"></span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">Periksa yang bertanda kuning. Klik angka untuk koreksi. Tidak ada nilai tersimpan sebelum Konfirmasi.</p>
                        <div id="omrReviewList" class="space-y-1.5 max-h-72 overflow-y-auto"></div>
                        <div class="flex items-center justify-end gap-2 mt-3">
                            <button type="button" id="btnOmrCancelReview" class="btn btn-secondary btn-sm rounded-xl">Batal</button>
                            <button type="button" id="btnOmrConfirm" class="btn btn-primary btn-sm rounded-xl shadow-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Konfirmasi & Simpan</span>
                            </button>
                        </div>
                        <p class="text-[11px] font-semibold mt-2" style="color: var(--primary);" id="omrConfirmNote"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Feed Riwayat Scan Lembar --}}
        <div class="lg:col-span-5 flex flex-col">
            <div class="glass p-4 sm:p-5 flex-1 flex flex-col rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-slate-800">
                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Riwayat Lembar Terpindai</h4>
                    <span class="text-[11px] text-slate-400 font-medium" id="scanCounter">0 lembar tercatat</span>
                </div>

                <div class="flex-1 overflow-y-auto max-h-96 space-y-2 text-xs" id="scanFeedList">
                    <div class="text-center py-12 text-slate-400" id="scanFeedEmpty">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/50 flex items-center justify-center mx-auto mb-2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="font-medium text-slate-600 dark:text-slate-300">Belum ada lembar yang dipindai.</p>
                        <p class="text-[11px] mt-0.5 text-slate-400">Posisikan lembar di kotak bidik lalu klik tombol pindai.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- TAB 3: DIGITAL PWA & LIVE MONITOR                                --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div id="tabDigital" class="tab-panel hidden">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
        {{-- Proyektor Card (QR Code & PIN) --}}
        <div class="md:col-span-5 glass p-6 flex flex-col items-center justify-center text-center rounded-2xl border border-slate-200/70 shadow-xs">
            <span class="badge badge-blue mb-2">Tampilkan di Layar Proyektor Aula</span>
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">Akses Ujian Digital Peserta</h3>

            {{-- QR Code Canvas --}}
            <div class="p-3.5 bg-white rounded-2xl border border-slate-200/70 inline-block mb-4 shadow-xs">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url('/join?kode=' . $kegiatan->kode_join)) }}"
                     alt="QR Code Sesi" width="160" height="160" loading="lazy" class="w-40 h-40 object-contain rounded-xl"
                     onerror="this.closest('div').insertAdjacentHTML('beforeend','<p class=\'text-[11px] text-slate-400\'>QR butuh internet — gunakan PIN manual di bawah.</p>');this.remove();">
            </div>

            <p class="text-xs text-slate-400 mb-1">Atau masukkan 6 Karakter PIN Sesi:</p>
            <div class="font-mono font-extrabold text-3xl tracking-widest text-primary px-6 py-2.5 bg-blue-50/70 dark:bg-blue-950/40 rounded-xl mb-4 border border-blue-200/80 dark:border-blue-900">
                {{ $kegiatan->kode_join }}
            </div>

            <a href="{{ route('participant.welcome') }}" target="_blank" class="btn btn-secondary btn-sm rounded-xl">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Portal Siswa (Tab Baru)</span>
            </a>
        </div>

        {{-- Live Attendance Monitor --}}
        <div class="md:col-span-7 glass p-5 flex flex-col rounded-2xl border border-slate-200/70 shadow-xs">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Live Monitoring Peserta Digital</h4>
                    <p class="text-xs text-slate-400">Pantau status pengerjaan peserta secara real-time</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnRefreshDigital" class="btn btn-secondary btn-sm rounded-xl" title="Muat ulang daftar peserta digital">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span id="digitalCount">0</span>
                    </button>
                    <span class="badge badge-green">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Sesi Terhubung
                    </span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto max-h-80 space-y-2 text-xs" id="digitalList">
                @forelse($participants as $p)
                <div class="p-3 rounded-xl border border-slate-200/60 flex items-center justify-between bg-white dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-primary font-bold flex items-center justify-center text-xs border border-blue-100">
                            {{ strtoupper(substr($p->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $p->name }}</p>
                            <p class="text-[11px] text-slate-400">{{ $p->class_grade ?? 'Reguler' }} &bull; Jalur: {{ strtoupper($p->input_method ?? 'Digital') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs tabular-nums text-slate-500 font-medium">
                            Pre: <strong class="text-slate-800 dark:text-white">{{ $p->pretest_score ?? '—' }}</strong> |
                            Post: <strong class="text-slate-800 dark:text-white">{{ $p->posttest_score ?? '—' }}</strong>
                        </span>
                        @if($p->status === 'selesai')
                            <span class="badge badge-green">Selesai</span>
                        @elseif($p->status === 'mengerjakan')
                            <span class="badge badge-blue">Mengerjakan</span>
                        @else
                            <span class="badge badge-yellow">Menunggu</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-slate-400">
                    Belum ada peserta yang bergabung ke sesi ini.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Live refresh daftar peserta digital (10 detik, hanya saat tab terlihat).
    // ID event dibaca dari DOM (bukan global EVENT_ID) agar tidak tergantung
    // urutan blok skrip.
    const list = document.getElementById('digitalList');
    const countEl = document.getElementById('digitalCount');
    const eventId = document.getElementById('kontrolSesi')?.dataset.eventId || null;
    if (!list || !eventId) return;

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    function statusBadge(st) {
        if (st === 'selesai') return '<span class="badge badge-green">Selesai</span>';
        if (st === 'pretest' || st === 'posttest' || st === 'mengerjakan') return '<span class="badge badge-blue">Mengerjakan</span>';
        return '<span class="badge badge-yellow">Menunggu</span>';
    }
    async function refreshDigital() {
        try {
            const res = await fetch(`/operator/kegiatan/${eventId}/peserta-digital`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const json = await res.json();
            const rows = json.data || [];
            if (countEl) countEl.textContent = rows.length;
            if (!rows.length) return; // pertahankan render server (mock / kosong)
            list.innerHTML = rows.map(p => `
                <div class="p-3 rounded-xl border border-slate-200/60 flex items-center justify-between bg-white dark:bg-slate-800/60">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-primary font-bold flex items-center justify-center text-xs border border-blue-100">${esc((p.name || '?').charAt(0).toUpperCase())}</div>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-slate-200">${esc(p.name)}</p>
                            <p class="text-[11px] text-slate-400">${esc(p.class_grade || '-')} &bull; Jalur: DIGITAL</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs tabular-nums text-slate-500 font-medium">Pre: <strong class="text-slate-800 dark:text-white">${p.pretest_score ?? '—'}</strong> | Post: <strong class="text-slate-800 dark:text-white">${p.posttest_score ?? '—'}</strong></span>
                        ${statusBadge(p.status)}
                    </div>
                </div>`).join('');
        } catch (e) { /* abaikan — coba lagi periodenya */ }
    }
    document.getElementById('btnRefreshDigital')?.addEventListener('click', refreshDigital);
    setInterval(() => {
        const tab = document.getElementById('tabDigital');
        if (tab && !tab.classList.contains('hidden') && !document.hidden) refreshDigital();
    }, 10000);
});
</script>

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
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
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

    // ─── Tab Switching & Dropdown Logic ─────────────────────────
    const tabMeta = {
        tabSpreadsheet: {
            label: 'Detail Kegiatan (Spreadsheet)',
            optId: 'optSpreadsheet'
        },
        tabOMR: {
            label: 'Simulator Scanner OMR',
            optId: 'optOMR'
        },
        tabDigital: {
            label: 'Live Monitor Digital PWA',
            optId: 'optDigital'
        }
    };

    function toggleTabDropdown(forceClose = false) {
        const menu = document.getElementById('tabDropdownMenu');
        const chevron = document.getElementById('tabDropdownChevron');
        if (!menu) return;

        const isExpanded = !menu.classList.contains('hidden');
        if (forceClose || isExpanded) {
            menu.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        } else {
            menu.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        }
    }
    window.toggleTabDropdown = toggleTabDropdown;

    function selectTabFromDropdown(tabId) {
        switchMethodTab(tabId);
        toggleTabDropdown(true);
    }
    window.selectTabFromDropdown = selectTabFromDropdown;

    function switchMethodTab(tabId) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));

        const targetPanel = document.getElementById(tabId);
        if (targetPanel) targetPanel.classList.remove('hidden');

        const meta = tabMeta[tabId];
        if (meta) {
            const labelEl = document.getElementById('currentTabLabel');
            if (labelEl) labelEl.textContent = meta.label;

            document.querySelectorAll('.tab-dropdown-option').forEach(opt => {
                opt.classList.remove('bg-slate-50/90', 'dark:bg-slate-800/60');
            });

            const activeOpt = document.getElementById(meta.optId);
            if (activeOpt) {
                activeOpt.classList.add('bg-slate-50/90', 'dark:bg-slate-800/60');
            }
        }
    }
    window.switchMethodTab = switchMethodTab;

    // Close dropdown on click outside
    document.addEventListener('click', (e) => {
        const container = document.getElementById('tabDropdownContainer');
        if (container && !container.contains(e.target)) {
            toggleTabDropdown(true);
        }
    });

    // Close dropdown on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            toggleTabDropdown(true);
        }
    });

    // ─── Input Validation & Clamping ────────────────────────────
    function flashInvalidBorder(el) {
        el.style.boxShadow = '0 0 0 2px #EF4444';
        setTimeout(() => {
            el.style.boxShadow = '';
        }, 800);
    }

    // ─── N-Gain Live Calculator & Validation ─────────────────────
    function calculateRowGain(inputEl) {
        const tr = inputEl.closest('tr');
        const preInput = tr.querySelector('.input-pre');
        const postInput = tr.querySelector('.input-post');
        const gainCol = tr.querySelector('.col-gain');
        const catCol = tr.querySelector('.col-category');

        // Validasi real-time nilai 0-100
        if (inputEl.value !== '') {
            let val = parseFloat(inputEl.value);
            if (val < 0) {
                inputEl.value = 0;
                flashInvalidBorder(inputEl);
            } else if (val > 100) {
                inputEl.value = 100;
                flashInvalidBorder(inputEl);
            }
        }

        const pre = parseFloat(preInput.value);
        const post = parseFloat(postInput.value);

        if (!isNaN(pre) && !isNaN(post)) {
            let gain = 0;
            if (pre >= 100) {
                gain = (post >= 100) ? 1.00 : 0.00;
            } else {
                gain = (post - pre) / (100 - pre);
                if (gain < 0) gain = 0;
            }
            gainCol.textContent = gain.toFixed(2);

            if (gain >= 0.7) {
                catCol.innerHTML = '<span class="badge badge-green">Paham</span>';
            } else if (gain >= 0.3) {
                catCol.innerHTML = '<span class="badge badge-yellow">Cukup</span>';
            } else {
                catCol.innerHTML = '<span class="badge badge-red">Kurang</span>';
            }
        } else {
            gainCol.textContent = '—';
            catCol.innerHTML = '<span class="text-slate-400">—</span>';
        }

        recalculateOverallStats();
    }

    // ─── Dynamic 4 Stat Chips & Footer Recalculator ──────────────
    function recalculateOverallStats() {
        const rows = document.querySelectorAll('#spreadsheetBody tr:not(#emptyRowPlaceholder)');
        const totalPeserta = rows.length;

        let totalPre = 0, countPre = 0;
        let totalPost = 0, countPost = 0;
        let totalGain = 0, countGain = 0;

        rows.forEach(tr => {
            const preInput = tr.querySelector('.input-pre');
            const postInput = tr.querySelector('.input-post');
            const pre = preInput && preInput.value !== '' ? parseFloat(preInput.value) : NaN;
            const post = postInput && postInput.value !== '' ? parseFloat(postInput.value) : NaN;

            if (!isNaN(pre)) { totalPre += pre; countPre++; }
            if (!isNaN(post)) { totalPost += post; countPost++; }

            if (!isNaN(pre) && !isNaN(post)) {
                let g = 0;
                if (pre >= 100) {
                    g = (post >= 100) ? 1.00 : 0.00;
                } else {
                    g = Math.max(0, (post - pre) / (100 - pre));
                }
                totalGain += g;
                countGain++;
            }
        });

        const avgPre = countPre > 0 ? (totalPre / countPre).toFixed(1) : '0';
        const avgPost = countPost > 0 ? (totalPost / countPost).toFixed(1) : '0';
        const avgGain = countGain > 0 ? (totalGain / countGain).toFixed(2) : '0.00';

        // Update Top Stat Chips
        const elTotal = document.getElementById('statTotalPeserta');
        const elPre = document.getElementById('statAvgPre');
        const elPost = document.getElementById('statAvgPost');
        const elGain = document.getElementById('statAvgGain');
        const elCat = document.getElementById('statCategoryBadge');
        const elTableCount = document.getElementById('tableCountBadge');

        if (elTotal) elTotal.textContent = totalPeserta;
        if (elPre) elPre.textContent = avgPre;
        if (elPost) elPost.textContent = avgPost;
        if (elGain) elGain.textContent = avgGain;
        if (elTableCount) elTableCount.textContent = `(${totalPeserta} Partisipan)`;

        // Update Footer Pinned Row
        const fTotal = document.getElementById('footerTotalPeserta');
        const fPre = document.getElementById('footerAvgPre');
        const fPost = document.getElementById('footerAvgPost');
        const fGain = document.getElementById('footerAvgGain');
        const fCat = document.getElementById('footerAvgCat');

        if (fTotal) fTotal.textContent = `${totalPeserta} Siswa`;
        if (fPre) fPre.textContent = avgPre;
        if (fPost) fPost.textContent = avgPost;
        if (fGain) fGain.textContent = avgGain;

        const numGain = parseFloat(avgGain);
        let catText = 'Kurang';
        let pCatText = 'Kurang';
        let badgeClass = 'badge-red';
        if (numGain >= 0.7) {
            catText = 'Tinggi';
            pCatText = 'Paham';
            badgeClass = 'badge-green';
        } else if (numGain >= 0.3) {
            catText = 'Sedang';
            pCatText = 'Cukup';
            badgeClass = 'badge-yellow';
        }

        if (elCat) {
            elCat.className = `badge ${badgeClass} text-[10px]`;
            elCat.textContent = catText;
        }

        if (fCat) {
            fCat.innerHTML = `<span class="badge ${badgeClass}">${pCatText}</span>`;
        }
    }

    // ─── Sequential Row Number Reindexing ─────────────────────────
    function reindexRowNumbers() {
        const rows = document.querySelectorAll('#spreadsheetBody tr:not(#emptyRowPlaceholder)');
        rows.forEach((tr, idx) => {
            const noCell = tr.querySelector('.row-no');
            if (noCell) noCell.textContent = idx + 1;
        });
    }

    // ─── Auto-Save Row via AJAX ──────────────────────────────────
    let saveTimeout = null;
    function autoSaveRow(inputEl) {
        const tr = inputEl.closest('tr');
        const rowId = tr.dataset.id || '';
        const name = tr.querySelector('.input-name')?.value || 'Peserta';
        const classGrade = tr.querySelector('.input-class')?.value || '-';
        const pre = tr.querySelector('.input-pre')?.value || 0;
        const post = tr.querySelector('.input-post')?.value || 0;

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
                if (data.success && data.participant) {
                    tr.dataset.id = data.participant.id;
                    showSaveIndicator();
                    recalculateOverallStats();
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

    // ─── Keyboard Navigation (Enter, Tab, ArrowDown/Up) ──────────
    function handleKeyNavigation(e, inputEl, fieldType) {
        const tr = inputEl.closest('tr');

        if (e.key === 'Enter') {
            e.preventDefault();
            const nextTr = tr.nextElementSibling;
            if (nextTr && !nextTr.id.includes('emptyRowPlaceholder')) {
                const target = nextTr.querySelector(`.input-${fieldType}`);
                if (target) {
                    target.focus();
                    target.select();
                }
            } else {
                addNewParticipantRow(fieldType);
            }
        } else if (e.key === 'Tab' && !e.shiftKey && fieldType === 'post') {
            const nextTr = tr.nextElementSibling;
            if (!nextTr || nextTr.id.includes('emptyRowPlaceholder')) {
                e.preventDefault();
                addNewParticipantRow('name');
            }
        } else if (e.key === 'ArrowDown') {
            const nextTr = tr.nextElementSibling;
            if (nextTr && !nextTr.id.includes('emptyRowPlaceholder')) {
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

        const count = tbody.querySelectorAll('tr:not(#emptyRowPlaceholder)').length + 1;
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors group animate-fade-in';
        tr.dataset.id = '';

        tr.innerHTML = `
            <td class="py-2.5 px-3 text-center text-slate-400 tabular-nums row-no font-medium">${count}</td>
            <td class="py-1 px-2">
                <input type="text"
                       class="w-full bg-transparent px-2.5 py-1.5 text-xs text-slate-800 dark:text-slate-100 font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 rounded-lg transition-all input-name"
                       value="Siswa ${count}"
                       placeholder="Nama Siswa..."
                       onchange="autoSaveRow(this)"
                       onkeydown="handleKeyNavigation(event, this, 'name')">
            </td>
            <td class="py-1 px-2">
                <input type="text"
                       class="w-full bg-transparent px-2.5 py-1.5 text-xs text-slate-600 dark:text-slate-300 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 rounded-lg transition-all input-class"
                       value="Reguler"
                       placeholder="cth: XI-A"
                       onchange="autoSaveRow(this)"
                       onkeydown="handleKeyNavigation(event, this, 'class')">
            </td>
            <td class="py-1 px-2">
                <input type="number" min="0" max="100" step="1"
                       class="w-full bg-blue-50/30 dark:bg-blue-950/20 px-2 py-1.5 text-center text-xs font-bold text-blue-600 dark:text-blue-400 tabular-nums focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-400/30 rounded-lg transition-all input-pre"
                       placeholder="—"
                       oninput="calculateRowGain(this)"
                       onchange="autoSaveRow(this)"
                       onkeydown="handleKeyNavigation(event, this, 'pre')">
            </td>
            <td class="py-1 px-2">
                <input type="number" min="0" max="100" step="1"
                       class="w-full bg-emerald-50/30 dark:bg-emerald-950/20 px-2 py-1.5 text-center text-xs font-bold text-emerald-600 dark:text-emerald-400 tabular-nums focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 rounded-lg transition-all input-post"
                       placeholder="—"
                       oninput="calculateRowGain(this)"
                       onchange="autoSaveRow(this)"
                       onkeydown="handleKeyNavigation(event, this, 'post')">
            </td>
            <td class="py-2.5 px-3 text-center font-bold tabular-nums text-xs text-slate-800 dark:text-slate-100 col-gain">—</td>
            <td class="py-2.5 px-3 text-center col-category"><span class="text-slate-400">—</span></td>
            <td class="py-2.5 px-3 text-center">
                <button type="button" onclick="deleteParticipantRow(this, null)"
                        class="btn btn-ghost btn-icon text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg"
                        style="width: 1.75rem; height: 1.75rem;" title="Hapus Siswa">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        // Auto save initial row
        autoSaveRow(tr.querySelector('.input-name'));
        reindexRowNumbers();
        recalculateOverallStats();

        const targetInput = tr.querySelector(`.input-${focusField}`);
        if (targetInput) {
            targetInput.focus();
            if (focusField === 'name') targetInput.select();
        }
    }

    // ─── Delete Participant Row ──────────────────────────────────
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
                if (data.success) {
                    tr.remove();
                    checkEmptyState();
                    reindexRowNumbers();
                    recalculateOverallStats();
                }
            } catch (err) {
                alert('Gagal menghapus baris peserta.');
            }
        } else {
            tr.remove();
            checkEmptyState();
            reindexRowNumbers();
            recalculateOverallStats();
        }
    }

    function checkEmptyState() {
        const tbody = document.getElementById('spreadsheetBody');
        const rows = tbody.querySelectorAll('tr:not(#emptyRowPlaceholder)');
        if (rows.length === 0) {
            tbody.innerHTML = `
                <tr id="emptyRowPlaceholder">
                    <td colspan="8" class="py-12 text-center text-slate-400">
                        Belum ada baris siswa. Klik <strong>"+ Tambah Baris Siswa"</strong> untuk mulai menginput nilai lembar kertas.
                    </td>
                </tr>
            `;
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
            if (data.success && data.participant) {
                scanCount++;
                document.getElementById('scanCounter').textContent = `${scanCount} lembar berhasil dicatat`;

                const emptyPlaceholder = document.getElementById('scanFeedEmpty');
                if (emptyPlaceholder) emptyPlaceholder.remove();

                const list = document.getElementById('scanFeedList');
                const item = document.createElement('div');
                item.className = 'p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 flex items-center justify-between animate-fade-in';
                item.innerHTML = `
                    <div>
                        <p class="font-bold text-slate-800 dark:text-slate-100">${data.participant.name}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pre: ${data.participant.pretest_score} | Post: ${data.participant.posttest_score} | N-Gain: ${parseFloat(data.participant.n_gain).toFixed(2)}</p>
                    </div>
                    <span class="badge badge-green">✓ Tersimpan</span>
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
            btn.className = 'btn btn-secondary btn-sm rounded-xl';
            txt.textContent = 'Mulai Pindai Berkelanjutan (Batch Auto-Scan)';
        } else {
            btn.className = 'btn btn-danger btn-sm rounded-xl';
            txt.textContent = 'Hentikan Pindai Otomatis';
            triggerSingleOMRScan();
            continuousInterval = setInterval(() => {
                triggerSingleOMRScan();
            }, 2500);
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

    // ─── Explicit Window Scope Binding ───────────────────────────
    window.switchMethodTab = switchMethodTab;
    window.toggleTabDropdown = toggleTabDropdown;
    window.selectTabFromDropdown = selectTabFromDropdown;
    window.addNewParticipantRow = addNewParticipantRow;
    window.deleteParticipantRow = deleteParticipantRow;
    window.autoSaveRow = autoSaveRow;
    window.calculateRowGain = calculateRowGain;
    window.handleKeyNavigation = handleKeyNavigation;
    window.triggerSingleOMRScan = triggerSingleOMRScan;
    window.toggleContinuousOMR = toggleContinuousOMR;
    window.confirmDeleteKegiatan = confirmDeleteKegiatan;
    window.playOMRBeep = playOMRBeep;
    window.flashReticleGreen = flashReticleGreen;
    window.recalculateOverallStats = recalculateOverallStats;
    window.reindexRowNumbers = reindexRowNumbers;
</script>
@endpush

@endsection
