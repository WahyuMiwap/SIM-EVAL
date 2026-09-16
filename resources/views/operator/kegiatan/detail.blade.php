@extends('layouts.app')

@section('title', 'Detail Kegiatan')
@section('page-title', $kegiatan->nama_kegiatan ?? 'Detail Kegiatan')
@section('page-subtitle', 'Pantau dan kelola sesi kegiatan')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-slate-500 mb-5">
    <a href="{{ route('operator.kegiatan.index') }}" class="hover:text-slate-300 transition-colors">Kegiatan</a>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-slate-300">{{ $kegiatan->nama_kegiatan ?? 'Detail' }}</span>
</div>

{{-- Top Info Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Info Kegiatan --}}
    <div class="glass-solid p-5 lg:col-span-2 animate-fade-in">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-white font-display">{{ $kegiatan->nama_kegiatan }}</h2>
                <p class="text-sm text-slate-400 mt-1 flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    {{ $kegiatan->lokasi->nama_lokasi ?? '—' }} &bull; {{ \Carbon\Carbon::parse($kegiatan->tanggal)->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
            <span class="badge {{ $kegiatan->status === 'selesai' ? 'badge-green' : ($kegiatan->status === 'pretest' || $kegiatan->status === 'posttest' ? 'badge-blue' : 'badge-gray') }}">
                {{ ucfirst($kegiatan->status ?? 'menunggu') }}
            </span>
        </div>

        <div class="detail-grid">
            <div>
                <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Kode Join</p>
                <p class="text-xl font-extrabold font-display text-blue-400 tracking-widest">{{ $kegiatan->kode_join }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Mode</p>
                <p class="text-sm font-semibold text-slate-200">{{ $kegiatan->mode === 'digital' ? '📱 Digital (PWA)' : '📄 Kertas (OMR)' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Durasi Sesi</p>
                <p class="text-sm font-semibold text-slate-200">{{ $kegiatan->durasi_menit ?? 30 }} menit</p>
            </div>
            <div>
                <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Total Peserta</p>
                <p class="text-sm font-semibold text-slate-200">{{ $kegiatan->peserta_count ?? 0 }} orang</p>
            </div>
        </div>

        {{-- Session Control --}}
        <hr class="divider">
        <div class="flex flex-wrap gap-2.5">
            @if($kegiatan->status === 'menunggu')
                <button class="btn btn-primary" id="btnStartPretest" data-id="{{ $kegiatan->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mulai Pre-Test
                </button>
            @elseif($kegiatan->status === 'pretest')
                <button class="btn btn-success" id="btnEndPretest" data-id="{{ $kegiatan->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                    Selesaikan Pre-Test & Jeda
                </button>
            @elseif($kegiatan->status === 'jeda')
                <button class="btn btn-primary" id="btnStartPosttest" data-id="{{ $kegiatan->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                    Mulai Post-Test
                </button>
            @elseif($kegiatan->status === 'posttest')
                <button class="btn btn-success" id="btnEndSession" data-id="{{ $kegiatan->id }}">
                    Selesaikan Kegiatan
                </button>
            @endif

            {{-- Export --}}
            <a href="{{ route('operator.kegiatan.export', $kegiatan->id) }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- QR & Kode Join --}}
    <div class="glass-solid p-5 flex flex-col items-center justify-center text-center animate-fade-in animate-delay-100">
        <p class="text-xs text-slate-500 uppercase tracking-wider mb-3">QR Code Peserta</p>
        <div class="qr-container mb-4">
            <div id="qrCodeDisplay" style="width:160px; height:160px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; border-radius:8px;">
                <span class="text-slate-400 text-xs">Loading...</span>
            </div>
        </div>
        <p class="text-xs text-slate-500 mb-1.5">Kode Join</p>
        <p class="text-3xl font-extrabold font-display tracking-[0.2em] text-blue-400">{{ $kegiatan->kode_join }}</p>
        <p class="text-xs text-slate-600 mt-2">Atau scan QR di atas</p>
        <button class="btn btn-secondary btn-sm mt-3 w-full" id="btnCopyKode" data-kode="{{ $kegiatan->kode_join }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Salin Kode
        </button>
    </div>
</div>

{{-- Bottom Section: Live Monitor + OMR Scanner --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Live Monitor Peserta --}}
    <div class="glass-solid p-5 animate-fade-in animate-delay-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-white font-display text-sm">Live Monitor Peserta</h3>
                <p class="text-xs text-slate-500">Real-time</p>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-xs text-green-400 font-medium" id="onlineCount">0 online</span>
            </div>
        </div>

        {{-- Participant Grid --}}
        <div class="grid grid-cols-4 sm:grid-cols-5 gap-2 max-h-64 overflow-y-auto" id="participantGrid">
            {{-- Diisi oleh JS (LiveMonitor) --}}
            <div class="col-span-full flex items-center justify-center py-8 text-slate-600 text-sm">
                Menunggu peserta bergabung...
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-white/5 text-xs text-slate-500">
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span> Mengerjakan</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Menunggu</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Selesai</span>
        </div>
    </div>

    {{-- OMR Scanner (Engine A) --}}
    @if($kegiatan->mode === 'kertas')
    <div class="glass-solid p-5 animate-fade-in animate-delay-300" id="omrScannerPanel">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-white font-display text-sm">Pemindai OMR</h3>
                <p class="text-xs text-slate-500">Engine A — Tanda Silang (X)</p>
            </div>
            <span class="badge badge-yellow">Mode Kertas</span>
        </div>

        <div class="scanner-viewport mb-3" id="omrViewport">
            <video id="omrVideo" class="w-full h-full object-cover" autoplay muted playsinline></video>
            <div class="scanner-overlay">
                <div class="scanner-frame">
                    <div class="scan-line"></div>
                </div>
            </div>
        </div>

        <div class="flex gap-2 mt-3">
            <button class="btn btn-primary flex-1" id="btnStartScan">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                Mulai Scan
            </button>
            <button class="btn btn-secondary flex-1" id="btnUploadSheet">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Upload Foto
            </button>
        </div>

        {{-- Hasil Scan --}}
        <div class="mt-3 p-3 rounded-xl bg-white/3 border border-white/5 hidden" id="omrResult">
            <p class="text-xs text-slate-500 mb-2">Hasil Deteksi:</p>
            <div id="omrResultContent"></div>
        </div>
    </div>
    @else
    <div class="glass-solid p-5 animate-fade-in animate-delay-300 flex flex-col items-center justify-center text-center" style="min-height: 300px;">
        <div class="w-14 h-14 rounded-full bg-cyan-500/15 flex items-center justify-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <p class="text-sm font-semibold text-slate-300 font-display">Mode Digital Aktif</p>
        <p class="text-xs text-slate-500 mt-1">Peserta mengerjakan langsung via PWA. Tidak ada scan OMR diperlukan.</p>
    </div>
    @endif

</div>

@endsection
