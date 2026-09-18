@extends('layouts.app')

@section('title', 'Edit Kegiatan')
@section('page-title', 'Edit Kegiatan')
@section('page-subtitle', 'Ubah data kegiatan sosialisasi')

@section('content')

{{-- Breadcrumb --}}
<nav class="kf-breadcrumb">
    <a href="{{ route('operator.kegiatan.index') }}" class="kf-bread-link">Daftar Kegiatan</a>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--text-xmuted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="kf-bread-link">{{ $kegiatan->nama_kegiatan }}</a>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--text-xmuted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span style="color:var(--text-secondary);font-weight:500;">Edit</span>
</nav>

<div class="kf-layout">
    {{-- Form Card --}}
    <div class="glass kf-card">
        <div class="kf-card-header">
            <div class="kf-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div>
                <p class="kf-card-title">Edit Data Kegiatan</p>
                <p class="kf-card-sub">Perubahan akan langsung tersimpan</p>
            </div>
        </div>

        <form method="POST" action="{{ route('operator.kegiatan.update', $kegiatan->id) }}" class="kf-form-body">
            @csrf
            @method('PUT')

            {{-- Nama Kegiatan --}}
            <div class="kf-field kf-field--full">
                <label class="kf-label" for="f_nama">Nama Kegiatan <span class="kf-required">*</span></label>
                <input type="text" name="nama_kegiatan" id="f_nama" class="kf-input" value="{{ $kegiatan->nama_kegiatan }}" required>
            </div>

            {{-- Lokasi --}}
            <div class="kf-field">
                <label class="kf-label" for="f_lokasi">Lokasi Kegiatan</label>
                <select name="lokasi_id" id="f_lokasi" class="kf-input">
                    <option value="">— Pilih lokasi —</option>
                    @foreach($lokasiList ?? [] as $lok)
                        <option value="{{ $lok->id }}" {{ ($kegiatan->lokasi_id ?? null) == $lok->id ? 'selected' : '' }}>{{ $lok->nama_lokasi }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal --}}
            <div class="kf-field">
                <label class="kf-label" for="f_tanggal">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal" id="f_tanggal" class="kf-input" value="{{ $kegiatan->tanggal ?? '' }}">
            </div>



            {{-- Durasi --}}
            <div class="kf-field">
                <label class="kf-label" for="f_durasi">Durasi Sesi (menit)</label>
                <input type="number" name="durasi_menit" id="f_durasi" class="kf-input" value="{{ $kegiatan->durasi_menit ?? 30 }}" min="5" max="180">
            </div>

            {{-- Catatan --}}
            <div class="kf-field kf-field--full">
                <label class="kf-label" for="f_catatan">Catatan (opsional)</label>
                <textarea name="catatan" id="f_catatan" class="kf-input kf-textarea" rows="3">{{ $kegiatan->catatan ?? '' }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="kf-actions">
                <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Side Info --}}
    <div class="kf-side">
        <div class="glass kf-info-card">
            <p class="kf-info-title">Info Kegiatan</p>
            <div class="kf-info-row">
                <span class="kf-info-key">Kode Join</span>
                <span class="kf-info-val kf-info-code">{{ $kegiatan->kode_join }}</span>
            </div>
            <div class="kf-info-row">
                <span class="kf-info-key">Status</span>
                @php $s = $kegiatan->status ?? 'menunggu'; @endphp
                @if($s === 'selesai')         <span class="badge badge-green">Selesai</span>
                @elseif($s === 'berlangsung') <span class="badge badge-cyan">Berlangsung</span>
                @else                         <span class="badge badge-gray">Dijadwalkan</span>
                @endif
            </div>
            <div class="kf-info-row">
                <span class="kf-info-key">Total Peserta</span>
                <span class="kf-info-val">{{ $kegiatan->peserta_count ?? 0 }} orang</span>
            </div>
        </div>
        <div class="glass kf-info-card mt-4" style="border:1.5px solid rgba(239,68,68,.15);">
            <p class="kf-info-title" style="color:var(--danger)">Perhatian</p>
            <p class="kf-info-desc">Mengubah data kegiatan yang sedang berlangsung dapat mempengaruhi pengalaman peserta yang sedang bergabung.</p>
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
.kf-actions{grid-column:1/-1;display:flex;justify-content:flex-end;gap:.75rem;padding-top:.25rem;border-top:1px solid var(--border);margin-top:.25rem}
.kf-info-card{padding:1.125rem}
.kf-info-title{font-size:.8125rem;font-weight:700;color:var(--text-primary);font-family:var(--font-display);margin-bottom:.75rem}
.kf-info-desc{font-size:.78rem;color:var(--text-muted);line-height:1.5}
.kf-info-row{display:flex;justify-content:space-between;align-items:center;padding:.45rem 0;border-bottom:1px solid var(--border)}
.kf-info-row:last-child{border-bottom:none}
.kf-info-key{font-size:.75rem;color:var(--text-muted);font-weight:500}
.kf-info-val{font-size:.8125rem;font-weight:600;color:var(--text-primary)}
.kf-info-code{font-family:var(--font-display);font-size:1rem;font-weight:800;color:var(--primary);letter-spacing:.1em}
</style>

@endsection
