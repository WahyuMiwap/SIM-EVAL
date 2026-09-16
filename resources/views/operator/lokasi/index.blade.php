@extends('layouts.app')

@section('title', 'Master Lokasi')
@section('page-title', 'Master Lokasi')
@section('page-subtitle', 'Kelola daftar lokasi kegiatan sosialisasi')

@section('content')

<div class="glass animate-fade-in" style="padding: 0; overflow: hidden;">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--border);">
        <div>
            <p class="section-title">Daftar Lokasi</p>
            <p class="section-desc">{{ $lokasi->total() ?? 0 }} lokasi terdaftar</p>
        </div>
        <div class="flex items-center gap-2">
            <input type="text" id="filterLokasi" class="form-input" style="width: 220px; height: 34px; font-size: 0.8125rem; padding: 0.375rem 0.75rem;" placeholder="Cari lokasi...">
            <button class="btn btn-primary btn-sm" id="btnTambahLokasi">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah Lokasi
            </button>
        </div>
    </div>

    {{-- Table --}}
    @if($lokasi->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--text-muted);">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
        </div>
        <p class="font-semibold text-sm" style="color: var(--text-secondary);">Belum ada lokasi</p>
        <button class="btn btn-primary btn-sm mt-2" id="btnTambahLokasiEmpty">Tambah Lokasi</button>
    </div>
    @else
    <div style="overflow-x: auto;">
        <table class="data-table" id="lokasiTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Lokasi</th>
                    <th>Alamat</th>
                    <th>Kecamatan</th>
                    <th>Jenis Sasaran</th>
                    <th>Kegiatan</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($lokasi as $i => $lok)
            <tr>
                <td style="color: var(--text-muted); font-size: 0.8125rem; width: 40px;">{{ $lokasi->firstItem() + $i }}</td>
                <td>
                    <p class="font-semibold text-sm" style="color: var(--text-primary);">{{ $lok->nama_lokasi }}</p>
                </td>
                <td class="text-sm" style="color: var(--text-secondary);">{{ $lok->alamat ?? '—' }}</td>
                <td class="text-sm" style="color: var(--text-secondary);">{{ $lok->kecamatan ?? '—' }}</td>
                <td>
                    @php $jenis = $lok->jenis_sasaran ?? 'sekolah'; @endphp
                    @if($jenis === 'sekolah')   <span class="badge badge-blue">Sekolah</span>
                    @elseif($jenis === 'lapas')  <span class="badge badge-yellow">Lapas</span>
                    @elseif($jenis === 'komunitas') <span class="badge badge-green">Komunitas</span>
                    @else                        <span class="badge badge-gray">{{ $jenis }}</span>
                    @endif
                </td>
                <td style="font-size: 0.8125rem; font-weight: 600; color: var(--text-secondary);">
                    {{ $lok->kegiatan_count ?? 0 }}x
                </td>
                <td>
                    <div class="flex items-center justify-end gap-1.5">
                        <button class="btn btn-secondary btn-sm" onclick="editLokasi({{ $lok->id }})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button class="btn btn-sm"
                            style="background: var(--danger-light); color: var(--danger); border: 1.5px solid rgba(239,68,68,0.2);"
                            data-reauth-action="{{ route('operator.lokasi.destroy', $lok->id) }}"
                            data-reauth-id="{{ $lok->id }}"
                            data-reauth-label="{{ $lok->nama_lokasi }}"
                            onclick="ReauthModal.open(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($lokasi->hasPages())
    <div class="px-5 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border);">
        <p class="text-xs" style="color: var(--text-muted);">
            Menampilkan {{ $lokasi->firstItem() }}–{{ $lokasi->lastItem() }} dari {{ $lokasi->total() }}
        </p>
        <div class="flex gap-1">
            @if($lokasi->onFirstPage())
                <span class="page-btn" style="opacity: 0.4; cursor: not-allowed;">‹</span>
            @else
                <a href="{{ $lokasi->previousPageUrl() }}" class="page-btn">‹</a>
            @endif
            @foreach($lokasi->getUrlRange(1, $lokasi->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-btn {{ $page == $lokasi->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($lokasi->hasMorePages())
                <a href="{{ $lokasi->nextPageUrl() }}" class="page-btn">›</a>
            @else
                <span class="page-btn" style="opacity: 0.4; cursor: not-allowed;">›</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

{{-- ── MODAL: Tambah / Edit Lokasi ──────────────────── --}}
<div class="modal-overlay" id="lokasiModalOverlay">
    <div class="modal-box">
        <div class="modal-header">
            <p class="modal-title" id="lokasiModalTitle">Tambah Lokasi</p>
            <button class="btn btn-secondary btn-icon" id="lokasiModalClose">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="lokasiForm" method="POST" action="{{ route('operator.lokasi.store') }}">
            @csrf
            <input type="hidden" name="_method" id="lokasiFormMethod" value="POST">
            <input type="hidden" name="id" id="lokasiFormId">

            <div class="form-group">
                <label class="form-label" for="fl_nama">Nama Lokasi <span style="color: var(--danger);">*</span></label>
                <input type="text" name="nama_lokasi" id="fl_nama" class="form-input" placeholder="cth: SMA Negeri 1 Surabaya" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="fl_alamat">Alamat</label>
                <input type="text" name="alamat" id="fl_alamat" class="form-input" placeholder="Jl. contoh No. 1">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label" for="fl_kecamatan">Kecamatan</label>
                    <input type="text" name="kecamatan" id="fl_kecamatan" class="form-input" placeholder="cth: Genteng">
                </div>
                <div class="form-group">
                    <label class="form-label" for="fl_jenis">Jenis Sasaran</label>
                    <select name="jenis_sasaran" id="fl_jenis" class="form-input">
                        <option value="sekolah">Sekolah / Kampus</option>
                        <option value="lapas">Lapas / Rutan</option>
                        <option value="komunitas">Komunitas / Umum</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2 justify-end mt-2">
                <button type="button" class="btn btn-secondary" id="lokasiModalCancelBtn">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>

@endsection
