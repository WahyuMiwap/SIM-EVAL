@extends('layouts.app')

@section('title', 'Tambah Kegiatan Sosialisasi')
@section('page-title', 'Tambah Kegiatan Baru')
@section('page-subtitle', 'Daftarkan agenda sosialisasi dan evaluasi P4GN')

@section('content')

{{-- ── Breadcrumb Navigasi Halus (Soft Minimalist) ─────────────── --}}
<nav class="kf-breadcrumb flex items-center gap-1.5 mb-4 text-xs">
    <a href="{{ route('operator.kegiatan.index') }}" class="text-slate-500 hover:text-primary transition-colors flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Daftar Kegiatan</span>
    </a>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-slate-700 dark:text-slate-300 font-medium">Tambah Kegiatan Baru</span>
</nav>

{{-- ── Layout Grid 2 Kolom (Form Utama & Side Card) ────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    {{-- Kolom Kiri: Form Utama (Span 2) --}}
    <div class="lg:col-span-2 glass rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs overflow-hidden">
        {{-- Header Form --}}
        <div class="p-4 md:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Informasi Agenda Sosialisasi</h2>
                <p class="text-xs text-slate-400 mt-0.5">Lengkapi formulir untuk membuka sesi evaluasi dan kuis digital</p>
            </div>
        </div>

        {{-- Form Action --}}
        <form method="POST" action="{{ route('operator.kegiatan.store') }}" class="p-4 md:p-6 space-y-4">
            @csrf

            {{-- 1. Nama Kegiatan --}}
            <div>
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="f_nama">
                    Nama Kegiatan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_kegiatan" id="f_nama" required
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                       placeholder="cth: Sosialisasi P4GN — SMAN 1 Surabaya"
                       value="{{ old('nama_kegiatan') }}">
                @error('nama_kegiatan')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-400 mt-1">Nama yang deskriptif dan mudah dikenali oleh staf dan peserta.</p>
            </div>

            {{-- Baris 2 Kolom: Lokasi & Tanggal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Lokasi Kegiatan --}}
                <div>
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="lokasi_search">
                        Lokasi / Sekolah Binaan <span class="text-rose-500">*</span>
                    </label>
                    @php
                        $oldLokasiId = old('lokasi_id');
                        $oldLokasiLabel = '';
                        foreach (($lokasiList ?? []) as $lok) {
                            if ((string) $lok->id === (string) $oldLokasiId) {
                                $oldLokasiLabel = $lok->nama_lokasi . ' (' . ucfirst($lok->jenis_sasaran ?? 'Sekolah') . ')';
                                break;
                            }
                        }
                        $lokasiInitial = collect($lokasiList ?? [])->take(100)->map(fn($l) => [
                            'id' => $l->id,
                            'nama_lokasi' => $l->nama_lokasi,
                            'jenis_sasaran' => $l->jenis_sasaran ?? 'sekolah',
                            'kecamatan' => $l->kecamatan ?? null,
                        ])->values()->all();
                    @endphp
                    <x-lokasi-combobox name="lokasi_id" inputId="lokasi_search"
                        :selectedId="$oldLokasiId" :selectedLabel="$oldLokasiLabel"
                        :initial="$lokasiInitial" :required="true" />
                    @error('lokasi_id')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Pelaksanaan --}}
                <div>
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="f_tanggal">
                        Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal" id="f_tanggal" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer shadow-xs"
                           value="{{ old('tanggal', date('Y-m-d')) }}">
                    <p class="text-[11px] text-slate-400 mt-1">Tanggal pelaksanaan sosialisasi & asesmen.</p>
                </div>
            </div>

            {{-- Blok Paket Soal (partial bersama edit): segmented pill + dropdown --}}
            @php
                $paketSamaInput = old('paket_sama', '1');
                $paketSama = $paketSamaInput === '1' || $paketSamaInput === 1 || $paketSamaInput === true;
            @endphp
            @include('operator.kegiatan._paket-field', [
                'paketList'    => $paketList ?? [],
                'paketSama'    => $paketSama,
                'paketUtamaId' => old('paket_utama', old('pretest_package_id', request('paket_utama', 1))),
                'pretestId'    => old('pretest_package_id', 1),
                'posttestId'   => old('posttest_package_id', 2),
            ])

            {{-- Durasi Pengerjaan --}}
            <div>
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="f_durasi">
                    Durasi Sesi Ujian
                </label>
                <div class="flex items-center gap-2.5">
                    <div class="w-36 max-w-full">
                        <input type="number" name="durasi_menit" id="f_durasi"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-2xs"
                               value="{{ old('durasi_menit', 30) }}" min="5" max="180">
                    </div>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 select-none">Menit</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Batas waktu pengerjaan untuk peserta kuis digital.</p>
            </div>

            {{-- Catatan Kegiatan (Opsional) --}}
            <div>
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="f_catatan">
                    Catatan Pelaksanaan (Opsional)
                </label>
                <textarea name="catatan" id="f_catatan" rows="3"
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                          placeholder="Tambahkan catatan singkat (misal: target peserta kelas X-1 sampai X-3, penanggung jawab BK)...">{{ old('catatan') }}</textarea>
            </div>

            {{-- Tombol Aksi Form --}}
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                <a href="{{ route('operator.kegiatan.index') }}"
                   class="btn btn-secondary btn-sm rounded-xl px-4 py-2 text-xs">
                    Batal
                </a>
                <button type="submit"
                        class="btn btn-primary btn-sm rounded-xl px-5 py-2 text-xs font-semibold shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan & Buka Meja Kerja</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Kolom Kanan: Info & Panduan Soft --}}
    <div class="space-y-4">
        {{-- Card 1: Kode Join Otomatis --}}
        <div class="glass p-4 md:p-5 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs">
            <div class="flex items-center gap-2.5 mb-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-100">Kode PIN Sesi Otomatis</h3>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-3">
                Sistem akan membuat <strong>6 digit kode PIN unik</strong> secara otomatis saat Anda menekan tombol simpan.
            </p>
            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-800 rounded-xl flex items-center justify-between">
                <span class="text-[11px] text-slate-500">Contoh Format PIN:</span>
                <span class="text-xs font-mono font-bold tracking-wider text-blue-600 dark:text-blue-400">
                    SMAN01
                </span>
            </div>
        </div>

        {{-- Card 2: Panduan Operasional Meja Kerja --}}
        <div class="glass p-4 md:p-5 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs">
            <div class="flex items-center gap-2.5 mb-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 border border-emerald-100 dark:border-emerald-900/50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-100">Alur Kerja Meja Kerja</h3>
            </div>
            <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-2 leading-relaxed">
                <li class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">1</span>
                    <span>Setelah tersimpan, Anda langsung diarahkan ke Meja Kerja evaluasi kegiatan ini.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">2</span>
                    <span>Pilih metode input sesuai kondisi: <strong>Keyboard Spreadsheet</strong>, <strong>Scanner OMR</strong>, atau <strong>Kuis HP Peserta</strong>.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">3</span>
                    <span>Nilai N-Gain terhitung instan dan dapat diunduh ke format Excel dinas.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- Modal Quick-Add Lokasi (dipicu dari combobox) --}}
@include('components.lokasi-quickadd-modal')

<style>
.switch-container {
    position: relative;
    display: inline-block;
    width: 36px;
    height: 20px;
    flex-shrink: 0;
    cursor: pointer;
    vertical-align: middle;
}
.switch-container input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    margin: 0;
    pointer-events: none;
}
.switch-track {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #CBD5E1;
    border-radius: 9999px;
    transition: background-color 0.2s ease-in-out;
}
.dark .switch-track {
    background-color: #475569;
}
.switch-thumb {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    background-color: #FFFFFF;
    border-radius: 9999px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    transition: transform 0.2s ease-in-out;
    pointer-events: none;
}
.switch-container input[type="checkbox"]:checked + .switch-track {
    background-color: var(--primary, #4361EE) !important;
}
.switch-container input[type="checkbox"]:checked + .switch-track + .switch-thumb {
    transform: translateX(16px) !important;
}
</style>

@endsection
