@extends('layouts.app')

@section('title', 'Detail Kegiatan')
@section('page-title', $kegiatan->nama_kegiatan ?? 'Detail Kegiatan')
@section('page-subtitle', 'Detail dan pengelolaan kegiatan')

@section('content')

{{-- Breadcrumb --}}
<nav class="kd-breadcrumb">
    <a href="{{ route('operator.kegiatan.index') }}" class="kd-bread-link">Daftar Kegiatan</a>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 kd-bread-sep" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="kd-bread-current">{{ $kegiatan->nama_kegiatan }}</span>
</nav>

{{-- Page Header --}}
<div class="kd-page-header">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="kd-page-title">{{ $kegiatan->nama_kegiatan }}</h1>
            @php $s = $kegiatan->status ?? 'menunggu'; @endphp
            @if($s === 'selesai')         <span class="badge badge-green">Selesai</span>
            @elseif($s === 'berlangsung') <span class="badge badge-cyan">Berlangsung</span>
            @else                         <span class="badge badge-gray">Menunggu</span>
            @endif
        </div>
        <p class="kd-page-sub">
            {{ $kegiatan->lokasi->nama_lokasi ?? '—' }} &nbsp;·&nbsp;
            {{ isset($kegiatan->tanggal) ? \Carbon\Carbon::parse($kegiatan->tanggal)->isoFormat('dddd, D MMMM Y') : '—' }}
        </p>
    </div>
    <div class="kd-page-actions">
        <a href="{{ route('operator.kegiatan.edit', $kegiatan->id) }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Kegiatan
        </a>
        <a href="{{ route('operator.kegiatan.export', $kegiatan->id) }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Excel
        </a>
        <button class="btn kd-btn-danger" id="btnHapusKegiatan" data-id="{{ $kegiatan->id }}" data-nama="{{ $kegiatan->nama_kegiatan }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Hapus
        </button>
    </div>
</div>

{{-- Row 1: Info + Sesi --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Info Kegiatan --}}
    <div class="glass kd-card lg:col-span-2">
        <div class="kd-card-header">
            <div class="kd-card-icon"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div><p class="kd-card-title">Informasi Kegiatan</p><p class="kd-card-sub">Detail pelaksanaan</p></div>
        </div>
        <div class="kd-info-grid">
            <div class="kd-info-item">
                <span class="kd-info-label">Kode Join</span>
                <span class="kd-info-code">{{ $kegiatan->kode_join }}</span>
            </div>
            <div class="kd-info-item">
                <span class="kd-info-label">Status</span>
                <span class="kd-info-value">
                    @if($s === 'selesai')         <span class="badge badge-green">Selesai</span>
                    @elseif($s === 'berlangsung') <span class="badge badge-cyan">Berlangsung</span>
                    @else                         <span class="badge badge-gray">Menunggu</span>
                    @endif
                </span>
            </div>
            <div class="kd-info-item">
                <span class="kd-info-label">Durasi Sesi</span>
                <span class="kd-info-value">{{ $kegiatan->durasi_menit ?? 30 }} menit</span>
            </div>
            <div class="kd-info-item">
                <span class="kd-info-label">Total Peserta</span>
                <span class="kd-info-value">{{ $kegiatan->peserta_count ?? 0 }} orang</span>
            </div>
            <div class="kd-info-item">
                <span class="kd-info-label">Lokasi</span>
                <span class="kd-info-value">{{ $kegiatan->lokasi->nama_lokasi ?? '—' }}</span>
            </div>
            <div class="kd-info-item">
                <span class="kd-info-label">Tanggal</span>
                <span class="kd-info-value">{{ isset($kegiatan->tanggal) ? \Carbon\Carbon::parse($kegiatan->tanggal)->format('d M Y') : '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Kontrol Sesi --}}
    <div class="glass kd-card">
        <div class="kd-card-header">
            <div class="kd-card-icon kd-icon-primary"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg></div>
            <div><p class="kd-card-title">Kontrol Sesi</p><p class="kd-card-sub">Mulai / selesaikan ujian</p></div>
        </div>
        <div class="kd-sesi-body">
            <div class="kd-qr-section">
                <div id="qrCodeDisplay" class="kd-qr-box"><span style="font-size:0.75rem;color:var(--text-muted)">Loading...</span></div>
                <p class="text-xs mt-3" style="color:var(--text-muted)">Kode Join</p>
                <p class="kd-kode-join">{{ $kegiatan->kode_join }}</p>
                <button class="btn btn-secondary btn-sm mt-3 w-full" id="btnCopyKode" data-kode="{{ $kegiatan->kode_join }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Salin Kode
                </button>
            </div>
            <div style="height:1px;background:var(--border);margin:0.5rem 0;"></div>
            <div class="kd-sesi-btns">
                @if($s === 'menunggu')
                    <button class="btn btn-primary w-full" id="btnMulaiKegiatan" data-id="{{ $kegiatan->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Mulai Kegiatan
                    </button>
                @elseif($s === 'berlangsung')
                    <button class="btn btn-success w-full" id="btnSelesaiKegiatan" data-id="{{ $kegiatan->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Selesaikan Kegiatan
                    </button>
                @else
                    <div class="kd-status-done">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Kegiatan Selesai
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Row 2: Live Monitor + Scan Soal --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Live Monitor --}}
    <div class="glass kd-card">
        <div class="kd-card-header">
            <div class="kd-card-icon kd-icon-info"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            <div class="flex-1"><p class="kd-card-title">Live Monitor Peserta</p><p class="kd-card-sub">Real-time kehadiran</p></div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-xs font-semibold" id="onlineCount" style="color:var(--success)">0 online</span>
            </div>
        </div>
        <div style="padding:1rem 1.25rem;">
            <div class="grid grid-cols-5 gap-2 max-h-48 overflow-y-auto" id="participantGrid">
                <div class="col-span-full flex items-center justify-center py-8 text-sm" style="color:var(--text-muted)">Menunggu peserta bergabung...</div>
            </div>
        </div>
        <div class="kd-card-footer">
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span> Mengerjakan</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background:var(--warning)"></span> Menunggu</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background:var(--primary)"></span> Selesai</span>
        </div>
    </div>

    {{-- Scan Soal Hybrid --}}
    <div class="glass kd-card">
        <div class="kd-card-header">
            <div class="kd-card-icon kd-icon-warning"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg></div>
            <div class="flex-1"><p class="kd-card-title">Scan Soal Hybrid</p><p class="kd-card-sub">Scan lembar jawaban kertas (OMR)</p></div>
            @if($kegiatan->mode === 'kertas')<span class="badge badge-yellow">Kertas</span>
            @else<span class="badge badge-blue">Digital</span>@endif
        </div>
        <div class="kd-scan-tabs">
            <button class="kd-scan-tab active" data-tab="camera">Kamera</button>
            <button class="kd-scan-tab" data-tab="upload">Upload Foto</button>
        </div>
        <div id="panelCamera" class="kd-scan-panel">
            <div class="kd-scanner-viewport">
                <video id="omrVideo" class="w-full h-full object-cover" autoplay muted playsinline></video>
                <div class="kd-scanner-placeholder" id="scannerPlaceholder">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--text-xmuted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                    <span>Kamera belum aktif</span>
                </div>
            </div>
            <button class="btn btn-primary w-full mt-3" id="btnStartScan">Mulai Kamera</button>
        </div>
        <div id="panelUpload" class="kd-scan-panel hidden">
            <label class="kd-upload-zone" for="inputFotoScan">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--text-xmuted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <p class="text-sm font-medium" style="color:var(--text-secondary)">Klik atau seret foto lembar jawaban</p>
                <p class="text-xs" style="color:var(--text-muted)">JPG, PNG — maks. 10 MB</p>
                <input type="file" id="inputFotoScan" accept="image/*" class="hidden">
            </label>
            <div class="kd-upload-preview hidden" id="uploadPreview">
                <img id="previewImg" src="" alt="Preview" class="kd-preview-img">
                <button class="btn btn-primary w-full mt-2" id="btnScanUpload">Proses Scan OMR</button>
            </div>
        </div>
        <div class="kd-scan-result hidden" id="omrResult">
            <div class="kd-scan-result-header">Hasil Deteksi OMR</div>
            <div id="omrResultContent" class="kd-scan-result-body"></div>
        </div>
    </div>
</div>

{{-- Row 3: Tabel Hasil --}}
<div class="glass kd-card mb-5">
    <div class="kd-card-header">
        <div class="kd-card-icon kd-icon-success"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
        <div class="flex-1"><p class="kd-card-title">Hasil & Rekap Peserta</p><p class="kd-card-sub">Skor pre-test, post-test, dan N-Gain</p></div>
        <a href="{{ route('operator.kegiatan.export', $kegiatan->id) }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download Excel
        </a>
    </div>
    @php
    $hasilPeserta = [
        (object)['nama'=>'Ahmad Fauzi',    'pretest'=>60,'posttest'=>85,'ngain'=>0.63,'status'=>'selesai'],
        (object)['nama'=>'Budi Santoso',   'pretest'=>45,'posttest'=>75,'ngain'=>0.55,'status'=>'selesai'],
        (object)['nama'=>'Citra Dewi',     'pretest'=>70,'posttest'=>90,'ngain'=>0.67,'status'=>'selesai'],
        (object)['nama'=>'Dedi Kurniawan', 'pretest'=>50,'posttest'=>null,'ngain'=>null,'status'=>'pretest'],
        (object)['nama'=>'Eva Marlina',    'pretest'=>null,'posttest'=>null,'ngain'=>null,'status'=>'menunggu'],
    ];
    @endphp
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr>
                <th>#</th><th>Nama Peserta</th><th>Pre-Test</th><th>Post-Test</th><th>N-Gain</th><th>Kategori</th><th>Status</th>
            </tr></thead>
            <tbody>
            @foreach($hasilPeserta as $i => $p)
            <tr>
                <td style="color:var(--text-muted);font-size:0.8rem;">{{ $i+1 }}</td>
                <td><span style="font-weight:600;font-size:0.875rem;color:var(--text-primary)">{{ $p->nama }}</span></td>
                <td>@if($p->pretest !== null)<span class="kd-score">{{ $p->pretest }}</span>@else<span style="color:var(--text-xmuted)">—</span>@endif</td>
                <td>@if($p->posttest !== null)<span class="kd-score">{{ $p->posttest }}</span>@else<span style="color:var(--text-xmuted)">—</span>@endif</td>
                <td>
                    @if($p->ngain !== null)
                        <span class="kd-ngain {{ $p->ngain >= 0.7 ? 'kd-ngain-high' : ($p->ngain >= 0.3 ? 'kd-ngain-mid' : 'kd-ngain-low') }}">{{ number_format($p->ngain,2) }}</span>
                    @else<span style="color:var(--text-xmuted)">—</span>@endif
                </td>
                <td>
                    @if($p->ngain !== null)
                        @if($p->ngain >= 0.7)<span class="badge badge-green">Tinggi</span>
                        @elseif($p->ngain >= 0.3)<span class="badge badge-yellow">Sedang</span>
                        @else<span class="badge badge-gray">Rendah</span>@endif
                    @else<span style="color:var(--text-xmuted)">—</span>@endif
                </td>
                <td>
                    @if($p->status === 'selesai')<span class="badge badge-green">Selesai</span>
                    @elseif($p->status === 'pretest')<span class="badge badge-cyan">Pre-Test</span>
                    @else<span class="badge badge-gray">Menunggu</span>@endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="kd-stats-row">
        <div class="kd-stat-chip"><span class="kd-stat-chip-label">Rata-rata Pre-Test</span><span class="kd-stat-chip-val">56.3</span></div>
        <div class="kd-stat-chip"><span class="kd-stat-chip-label">Rata-rata Post-Test</span><span class="kd-stat-chip-val">83.3</span></div>
        <div class="kd-stat-chip kd-stat-chip--primary"><span class="kd-stat-chip-label">Rata-rata N-Gain</span><span class="kd-stat-chip-val">0.62</span></div>
        <div class="kd-stat-chip"><span class="kd-stat-chip-label">Peserta Selesai</span><span class="kd-stat-chip-val">3 / 5</span></div>
    </div>
</div>

{{-- Danger Zone --}}
<div class="glass kd-card kd-danger-zone mb-5">
    <div class="kd-card-header">
        <div class="kd-card-icon kd-icon-danger"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
        <div><p class="kd-card-title" style="color:var(--danger)">Zona Berbahaya</p><p class="kd-card-sub">Tindakan ini tidak dapat dibatalkan</p></div>
    </div>
    <div class="kd-danger-content">
        <div>
            <p class="text-sm font-semibold" style="color:var(--text-primary)">Hapus Kegiatan Ini</p>
            <p class="text-xs mt-0.5" style="color:var(--text-muted)">Semua data peserta, skor, dan hasil scan akan terhapus permanen.</p>
        </div>
        <button class="btn kd-btn-danger" id="btnHapusKegiatan2" data-id="{{ $kegiatan->id }}" data-nama="{{ $kegiatan->nama_kegiatan }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Hapus Kegiatan
        </button>
    </div>
</div>

{{-- Confirm Delete --}}
<div class="kd-confirm-overlay hidden" id="confirmDeleteOverlay">
    <div class="kd-confirm-box">
        <div class="kd-confirm-icon"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
        <h3 class="kd-confirm-title">Hapus Kegiatan?</h3>
        <p class="kd-confirm-desc">Kegiatan <strong id="confirmDeleteName"></strong> akan dihapus permanen beserta seluruh data peserta dan hasil ujian.</p>
        <div class="kd-confirm-actions">
            <button class="btn btn-secondary" id="btnCancelDelete">Batal</button>
            <form method="POST" action="{{ route('operator.kegiatan.destroy', $kegiatan->id) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn kd-btn-danger">Ya, Hapus Sekarang</button>
            </form>
        </div>
    </div>
</div>

<style>
.kd-breadcrumb{display:flex;align-items:center;gap:.375rem;margin-bottom:1.25rem;font-size:.8125rem}
.kd-bread-link{display:inline-flex;align-items:center;gap:.35rem;color:var(--text-muted);text-decoration:none;transition:color .15s}
.kd-bread-link:hover{color:var(--text-primary)}
.kd-bread-sep{color:var(--text-xmuted)}
.kd-bread-current{color:var(--text-secondary);font-weight:500}
.kd-page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap}
.kd-page-title{font-family:var(--font-display);font-size:1.375rem;font-weight:800;color:var(--text-primary);letter-spacing:-.02em;line-height:1.2}
.kd-page-sub{margin-top:.3rem;font-size:.8125rem;color:var(--text-muted)}
.kd-page-actions{display:flex;align-items:center;gap:.625rem;flex-wrap:wrap;flex-shrink:0}
.kd-card{padding:0;overflow:hidden}
.kd-card-header{display:flex;align-items:center;gap:.75rem;padding:1rem 1.25rem;border-bottom:1px solid var(--border)}
.kd-card-icon{width:34px;height:34px;border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;background:var(--bg-alt);color:var(--text-muted)}
.kd-icon-primary{background:var(--primary-light);color:var(--primary)}
.kd-icon-info{background:var(--info-light);color:var(--info)}
.kd-icon-warning{background:#fffbeb;color:#d97706}
.kd-icon-success{background:var(--success-light);color:var(--success)}
.kd-icon-danger{background:var(--danger-light);color:var(--danger)}
.kd-card-title{font-size:.875rem;font-weight:700;color:var(--text-primary);font-family:var(--font-display)}
.kd-card-sub{font-size:.75rem;color:var(--text-muted);margin-top:.1rem}
.kd-card-footer{display:flex;align-items:center;gap:1rem;padding:.625rem 1.25rem;border-top:1px solid var(--border);font-size:.75rem;color:var(--text-muted)}
.kd-info-grid{display:grid;grid-template-columns:1fr 1fr}
.kd-info-item{display:flex;flex-direction:column;gap:.2rem;padding:.875rem 1.25rem;border-bottom:1px solid var(--border)}
.kd-info-item:nth-child(odd){border-right:1px solid var(--border)}
.kd-info-item:nth-last-child(-n+2){border-bottom:none}
.kd-info-label{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted)}
.kd-info-value{font-size:.875rem;font-weight:500;color:var(--text-primary)}
.kd-info-code{font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:var(--primary);letter-spacing:.15em}
.kd-sesi-body{padding:1.25rem;display:flex;flex-direction:column;gap:0}
.kd-qr-section{display:flex;flex-direction:column;align-items:center;padding-bottom:1rem}
.kd-qr-box{width:130px;height:130px;border-radius:var(--r-lg);background:var(--bg-alt);border:1px solid var(--border);display:flex;align-items:center;justify-content:center}
.kd-kode-join{font-family:var(--font-display);font-size:1.75rem;font-weight:900;letter-spacing:.15em;color:var(--primary);margin-top:.25rem}
.kd-sesi-btns{padding-top:1rem;display:flex;flex-direction:column;gap:.625rem}
.kd-status-done{display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.625rem;border-radius:var(--r-md);background:var(--success-light);color:var(--success);font-size:.875rem;font-weight:600}
.kd-scan-tabs{display:flex;padding:0 1.25rem;border-bottom:1px solid var(--border)}
.kd-scan-tab{display:inline-flex;align-items:center;gap:.4rem;padding:.625rem .875rem;font-size:.8125rem;font-weight:500;color:var(--text-muted);background:none;border:none;border-bottom:2px solid transparent;margin-bottom:-1px;cursor:pointer;transition:color .15s,border-color .15s;font-family:var(--font-sans)}
.kd-scan-tab:hover{color:var(--text-primary)}
.kd-scan-tab.active{color:var(--primary);border-bottom-color:var(--primary);font-weight:600}
.kd-scan-panel{padding:1rem 1.25rem}
.kd-scanner-viewport{position:relative;width:100%;height:185px;background:var(--bg-alt);border-radius:var(--r-lg);overflow:hidden;border:1px solid var(--border)}
.kd-scanner-placeholder{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;font-size:.8125rem;color:var(--text-muted)}
.kd-upload-zone{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:2rem 1rem;border:2px dashed var(--border);border-radius:var(--r-lg);cursor:pointer;transition:border-color .15s,background .15s;text-align:center}
.kd-upload-zone:hover{border-color:var(--primary);background:var(--primary-light)}
.kd-preview-img{width:100%;max-height:160px;object-fit:contain;border-radius:var(--r-md);border:1px solid var(--border)}
.kd-scan-result{margin:0 1.25rem 1.25rem;border-radius:var(--r-lg);border:1px solid var(--border);overflow:hidden}
.kd-scan-result-header{display:flex;align-items:center;gap:.5rem;padding:.625rem .875rem;font-size:.8125rem;font-weight:600;color:var(--text-secondary);background:var(--bg-alt);border-bottom:1px solid var(--border)}
.kd-scan-result-body{padding:.75rem .875rem;font-size:.8125rem;color:var(--text-secondary)}
.kd-score{font-weight:700;font-size:.875rem;color:var(--text-primary)}
.kd-ngain{display:inline-block;font-weight:700;font-size:.875rem;padding:.1rem .5rem;border-radius:var(--r-full)}
.kd-ngain-high{background:var(--success-light);color:var(--success)}
.kd-ngain-mid{background:#fffbeb;color:#d97706}
.kd-ngain-low{background:var(--danger-light);color:var(--danger)}
.kd-stats-row{display:flex;align-items:center;border-top:1px solid var(--border);flex-wrap:wrap}
.kd-stat-chip{flex:1;display:flex;flex-direction:column;gap:.2rem;padding:.875rem 1.25rem;border-right:1px solid var(--border);min-width:110px}
.kd-stat-chip:last-child{border-right:none}
.kd-stat-chip--primary .kd-stat-chip-val{color:var(--primary)}
.kd-stat-chip-label{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted)}
.kd-stat-chip-val{font-family:var(--font-display);font-size:1.25rem;font-weight:800;color:var(--text-primary);letter-spacing:-.02em}
.kd-danger-zone{border-color:rgba(239,68,68,.2)!important}
.kd-danger-content{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.25rem;flex-wrap:wrap}
.kd-btn-danger{background:var(--danger-light);color:var(--danger);border:1.5px solid rgba(239,68,68,.25)}
.kd-btn-danger:hover{background:var(--danger);color:#fff}
.kd-confirm-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);backdrop-filter:blur(4px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem}
.kd-confirm-overlay.hidden{display:none}
.kd-confirm-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);box-shadow:var(--shadow-xl);padding:2rem;max-width:420px;width:100%;text-align:center}
.kd-confirm-icon{width:52px;height:52px;border-radius:50%;background:var(--danger-light);color:var(--danger);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem}
.kd-confirm-title{font-family:var(--font-display);font-size:1.125rem;font-weight:800;color:var(--text-primary);margin-bottom:.5rem}
.kd-confirm-desc{font-size:.875rem;color:var(--text-muted);line-height:1.6;margin-bottom:1.5rem}
.kd-confirm-actions{display:flex;justify-content:center;gap:.75rem;flex-wrap:wrap}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Copy kode join
    document.getElementById('btnCopyKode')?.addEventListener('click', function () {
        navigator.clipboard.writeText(this.dataset.kode).then(() => {
            const orig = this.innerHTML; this.textContent = 'Disalin!';
            setTimeout(() => { this.innerHTML = orig; }, 2000);
        });
    });
    // Tabs
    document.querySelectorAll('.kd-scan-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.kd-scan-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('panelCamera').classList.toggle('hidden', this.dataset.tab !== 'camera');
            document.getElementById('panelUpload').classList.toggle('hidden', this.dataset.tab !== 'upload');
        });
    });
    // Kamera
    let stream = null;
    document.getElementById('btnStartScan')?.addEventListener('click', async function () {
        if (stream) {
            stream.getTracks().forEach(t => t.stop()); stream = null;
            document.getElementById('omrVideo').srcObject = null;
            document.getElementById('scannerPlaceholder').style.display = '';
            this.textContent = 'Mulai Kamera'; return;
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            document.getElementById('omrVideo').srcObject = stream;
            document.getElementById('scannerPlaceholder').style.display = 'none';
            this.textContent = 'Stop Kamera';
        } catch(e) { alert('Kamera tidak dapat diakses: ' + e.message); }
    });
    // Upload preview
    document.getElementById('inputFotoScan')?.addEventListener('change', function () {
        const file = this.files[0]; if (!file) return;
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('previewImg').src = e.target.result; document.getElementById('uploadPreview').classList.remove('hidden'); };
        reader.readAsDataURL(file);
    });
    // Simulasi scan
    document.getElementById('btnScanUpload')?.addEventListener('click', function () {
        document.getElementById('omrResult').classList.remove('hidden');
        document.getElementById('omrResultContent').innerHTML = `<table style="width:100%;border-collapse:collapse;font-size:.8125rem;">
            <thead><tr style="border-bottom:1px solid var(--border)">
                <th style="padding:.4rem .5rem;text-align:left;color:var(--text-muted)">Soal</th>
                <th style="padding:.4rem .5rem;text-align:left;color:var(--text-muted)">Jawaban</th>
                <th style="padding:.4rem .5rem;text-align:left;color:var(--text-muted)">Kepercayaan</th>
            </tr></thead>
            <tbody>${Array.from({length:5},(_,i)=>`<tr style="border-bottom:1px solid var(--border)">
                <td style="padding:.4rem .5rem;color:var(--text-muted)">No. ${i+1}</td>
                <td style="padding:.4rem .5rem;font-weight:700;color:var(--primary)">${['A','B','C','D'][Math.floor(Math.random()*4)]}</td>
                <td style="padding:.4rem .5rem;color:var(--success)">9${Math.floor(Math.random()*9)}%</td>
            </tr>`).join('')}</tbody>
        </table>`;
    });
    // Delete confirm
    function openDelete(nama) {
        document.getElementById('confirmDeleteName').textContent = nama;
        document.getElementById('confirmDeleteOverlay').classList.remove('hidden');
    }
    document.getElementById('btnCancelDelete')?.addEventListener('click', () => document.getElementById('confirmDeleteOverlay').classList.add('hidden'));
    document.getElementById('confirmDeleteOverlay')?.addEventListener('click', function(e) { if(e.target===this) this.classList.add('hidden'); });
    ['btnHapusKegiatan','btnHapusKegiatan2'].forEach(id => document.getElementById(id)?.addEventListener('click', function() { openDelete(this.dataset.nama); }));
    // QR placeholder
    const qrEl = document.getElementById('qrCodeDisplay');
    if (qrEl) qrEl.innerHTML = `<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="width:90%;height:90%;opacity:.15">
        <rect x="10" y="10" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6"/><rect x="17" y="17" width="16" height="16" fill="currentColor"/>
        <rect x="60" y="10" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6"/><rect x="67" y="17" width="16" height="16" fill="currentColor"/>
        <rect x="10" y="60" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6"/><rect x="17" y="67" width="16" height="16" fill="currentColor"/>
        <rect x="60" y="55" width="6" height="6" fill="currentColor"/><rect x="72" y="55" width="6" height="6" fill="currentColor"/>
        <rect x="60" y="67" width="18" height="6" fill="currentColor"/><rect x="84" y="67" width="6" height="6" fill="currentColor"/>
        <rect x="72" y="79" width="18" height="6" fill="currentColor"/><rect x="60" y="85" width="6" height="6" fill="currentColor"/>
    </svg>`;
});
</script>

@endsection
