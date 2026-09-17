@extends('layouts.app')

@section('title', 'Tambah Kegiatan')
@section('page-title', 'Tambah Kegiatan')
@section('page-subtitle', 'Buat kegiatan sosialisasi baru')

@section('content')

{{-- Breadcrumb --}}
<nav class="kf-breadcrumb">
    <a href="{{ route('operator.kegiatan.index') }}" class="kf-bread-link">Daftar Kegiatan</a>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--text-xmuted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span style="color:var(--text-secondary);font-weight:500;">Tambah Kegiatan</span>
</nav>

<div class="kf-layout">
    {{-- Form Card --}}
    <div class="glass kf-card">
        <div class="kf-card-header">
            <div class="kf-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <p class="kf-card-title">Data Kegiatan</p>
                <p class="kf-card-sub">Isi semua kolom yang diperlukan</p>
            </div>
        </div>

        <form method="POST" action="{{ route('operator.kegiatan.store') }}" class="kf-form-body">
            @csrf

            {{-- Nama Kegiatan --}}
            <div class="kf-field kf-field--full">
                <label class="kf-label" for="f_nama">Nama Kegiatan <span class="kf-required">*</span></label>
                <input type="text" name="nama_kegiatan" id="f_nama" class="kf-input" placeholder="cth: Sosialisasi Anti Narkoba — SMAN 1 Surabaya" required>
                <p class="kf-hint">Nama yang deskriptif dan mudah dikenali.</p>
            </div>

            {{-- Lokasi --}}
            <div class="kf-field">
                <label class="kf-label" for="f_lokasi">Lokasi Kegiatan</label>
                <select name="lokasi_id" id="f_lokasi" class="kf-input">
                    <option value="">— Pilih lokasi —</option>
                    @foreach($lokasiList ?? [] as $lok)
                        <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal --}}
            <div class="kf-field">
                <label class="kf-label" for="f_tanggal">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal" id="f_tanggal" class="kf-input">
            </div>

            {{-- Mode --}}
            <div class="kf-field">
                <label class="kf-label" for="f_mode">Mode Pelaksanaan</label>
                <select name="mode" id="f_mode" class="kf-input">
                    <option value="digital">Digital (Aplikasi PWA)</option>
                    <option value="kertas">Kertas (OMR Scan)</option>
                </select>
            </div>

            {{-- Durasi --}}
            <div class="kf-field">
                <label class="kf-label" for="f_durasi">Durasi Sesi (menit)</label>
                <input type="number" name="durasi_menit" id="f_durasi" class="kf-input" value="30" min="5" max="180">
                <p class="kf-hint">Waktu pengerjaan untuk setiap sesi ujian.</p>
            </div>

            {{-- Catatan --}}
            <div class="kf-field kf-field--full">
                <label class="kf-label" for="f_catatan">Catatan (opsional)</label>
                <textarea name="catatan" id="f_catatan" class="kf-input kf-textarea" rows="3" placeholder="Tambahkan catatan singkat tentang kegiatan ini..."></textarea>
            </div>

            {{-- Actions --}}
            <div class="kf-actions">
                <a href="{{ route('operator.kegiatan.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Kegiatan
                </button>
            </div>
        </form>
    </div>

    {{-- Side Info --}}
    <div class="kf-side">
        <div class="glass kf-info-card">
            <p class="kf-info-title">Mode Pelaksanaan</p>
            <div class="kf-info-item">
                <span class="badge badge-blue" style="margin-bottom:.5rem">Digital (PWA)</span>
                <p class="kf-info-desc">Peserta mengerjakan ujian langsung dari browser smartphone atau laptop mereka. Tidak perlu lembar kertas.</p>
            </div>
            <div style="height:1px;background:var(--border);margin:.875rem 0;"></div>
            <div class="kf-info-item">
                <span class="badge badge-gray" style="margin-bottom:.5rem">Kertas (OMR)</span>
                <p class="kf-info-desc">Peserta mengisi lembar jawaban fisik. Hasilnya discan menggunakan fitur Scan Soal Hybrid di halaman detail.</p>
            </div>
        </div>
        <div class="glass kf-info-card mt-4">
            <p class="kf-info-title">Kode Join</p>
            <p class="kf-info-desc">Kode join akan dibuat otomatis setelah kegiatan disimpan. Bagikan kode ini ke peserta untuk bergabung.</p>
        </div>
    </div>
</div>

<style>
.kf-breadcrumb{display:flex;align-items:center;gap:.375rem;margin-bottom:1.25rem;font-size:.8125rem}
.kf-bread-link{color:var(--text-muted);text-decoration:none;transition:color .15s}
.kf-bread-link:hover{color:var(--text-primary)}
.kf-layout{display:grid;grid-template-columns:1fr 280px;gap:1.25rem;align-items:start}
@media(max-width:900px){.kf-layout{grid-template-columns:1fr}}
.kf-card{padding:0;overflow:hidden}
.kf-card-header{display:flex;align-items:center;gap:.75rem;padding:1rem 1.25rem;border-bottom:1px solid var(--border)}
.kf-card-icon{width:34px;height:34px;border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;background:var(--primary-light);color:var(--primary)}
.kf-card-title{font-size:.875rem;font-weight:700;color:var(--text-primary);font-family:var(--font-display)}
.kf-card-sub{font-size:.75rem;color:var(--text-muted);margin-top:.1rem}
.kf-form-body{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;padding:1.25rem}
.kf-field{display:flex;flex-direction:column;gap:.375rem}
.kf-field--full{grid-column:1/-1}
.kf-label{font-size:.8rem;font-weight:600;color:var(--text-secondary);letter-spacing:.01em}
.kf-required{color:var(--danger)}
.kf-input{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r-md);padding:.55rem .875rem;font-size:.875rem;color:var(--text-primary);font-family:var(--font-sans);outline:none;transition:border-color .18s,box-shadow .18s;width:100%}
.kf-input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(67,97,238,.08)}
.kf-textarea{resize:vertical;min-height:80px}
.kf-hint{font-size:.73rem;color:var(--text-muted)}
.kf-actions{grid-column:1/-1;display:flex;justify-content:flex-end;gap:.75rem;padding-top:.25rem;border-top:1px solid var(--border);margin-top:.25rem}
.kf-info-card{padding:1.125rem}
.kf-info-title{font-size:.8125rem;font-weight:700;color:var(--text-primary);font-family:var(--font-display);margin-bottom:.75rem}
.kf-info-item{display:flex;flex-direction:column}
.kf-info-desc{font-size:.78rem;color:var(--text-muted);line-height:1.5}
</style>

@endsection
