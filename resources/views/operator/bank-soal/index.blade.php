@extends('layouts.app')

@section('title', 'Bank Soal')
@section('page-title', 'Bank Soal')
@section('page-subtitle', 'Kelola paket soal Pre-Test dan Post-Test kegiatan P2M')

@section('content')

{{-- ── Main Container ─────────────────────────────────────────── --}}
<div class="glass animate-fade-in" style="padding: 0; overflow: hidden; border-radius: var(--r-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--border); background: var(--surface);">

    {{-- Header & Toolbar --}}
    <div class="bs-toolbar">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="bs-heading font-display">Paket Soal</h2>
                <span class="badge badge-blue font-mono" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">
                    {{ $bankSoal->total() }} Paket
                </span>
            </div>
            <p class="bs-subheading">Daftar paket instrumen tes evaluasi pemahaman sosialisasi P2M</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Filter / Search Input --}}
            <div class="bs-search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="bs-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="filterSoal" class="form-input bs-search-input" placeholder="Cari nama paket soal...">
            </div>

            {{-- Tambah Paket Soal Button --}}
            <button class="btn btn-primary btn-sm bs-btn-add" id="btnTambahSoal">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Paket Soal</span>
            </button>
        </div>
    </div>

    {{-- ── Table ─────────────────────────────────────────────────── --}}
    <div class="overflow-x-auto">
        <table class="data-table" id="bankSoalTable">
            <thead>
                <tr>
                    <th style="width: 55px; text-align: center;">No</th>
                    <th>Nama Paket Soal</th>
                    <th style="width: 170px; text-align: center;">Jumlah Soal</th>
                    <th style="width: 140px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bankSoal as $index => $item)
                <tr class="soal-row">
                    {{-- No --}}
                    <td class="text-center font-mono" style="color: var(--text-muted); font-size: 0.8125rem;">
                        {{ ($bankSoal->currentPage() - 1) * $bankSoal->perPage() + $index + 1 }}
                    </td>

                    {{-- Nama Paket --}}
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="bs-icon-box">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="bs-item-title">{{ $item->nama_paket }}</p>
                                <div class="bs-item-meta">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Dibuat: {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMM Y') }}
                                    </span>
                                    <span class="bs-dot">&bull;</span>
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $item->durasi ?? 30 }} menit
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Jumlah Soal --}}
                    <td class="text-center">
                        <span class="bs-badge-soal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-mono font-semibold">{{ $item->soal_count ?? 0 }}</span> Soal
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td>
                        <div class="flex items-center justify-end gap-1.5">
                            {{-- Pratinjau / Lihat Soal --}}
                            <button type="button" class="btn btn-secondary btn-sm btn-icon" title="Lihat Isi Paket Soal"
                                onclick="previewSoal({{ $item->id }}, '{{ addslashes($item->nama_paket) }}', {{ $item->soal_count ?? 0 }}, {{ $item->durasi ?? 30 }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>

                            {{-- Edit --}}
                            <button type="button" class="btn btn-secondary btn-sm btn-icon" title="Edit Paket Soal" onclick="editSoal({{ $item->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>

                            {{-- Hapus (dengan ReauthModal kedinasan) --}}
                            <button type="button" class="btn btn-sm btn-icon bs-btn-delete" title="Hapus Paket Soal"
                                data-reauth-action="{{ route('operator.bank-soal.destroy', $item->id) }}"
                                data-reauth-label="Paket Soal: {{ $item->nama_paket }}"
                                data-reauth-id="{{ $item->id }}"
                                onclick="ReauthModal.open(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-16">
                        <div class="empty-state" style="padding: 2rem 1rem;">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--text-muted);">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="font-semibold text-sm" style="color: var(--text-secondary); margin: 0.5rem 0 0.25rem;">Belum ada paket soal</p>
                            <p class="text-xs" style="color: var(--text-muted); margin-bottom: 1rem;">Mulai dengan membuat paket instrumen tes untuk kegiatan sosialisasi.</p>
                            <button class="btn btn-primary btn-sm" onclick="document.getElementById('btnTambahSoal')?.click()">
                                Tambah Paket Soal
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse

                {{-- Row jika pencarian kosong --}}
                <tr id="soalSearchEmpty" style="display: none;">
                    <td colspan="4" class="text-center py-12">
                        <p class="text-sm font-semibold" style="color: var(--text-secondary); margin: 0 0 0.25rem;">Tidak ada paket soal yang cocok</p>
                        <p class="text-xs" style="color: var(--text-muted);">Silakan periksa kembali kata kunci pencarian Anda.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($bankSoal->hasPages())
    <div class="bs-pagination-bar">
        <p class="text-xs" style="color: var(--text-muted);">
            Menampilkan {{ $bankSoal->firstItem() }}–{{ $bankSoal->lastItem() }} dari {{ $bankSoal->total() }} paket soal
        </p>
        <div class="pagination">
            @if ($bankSoal->onFirstPage())
                <span class="page-btn opacity-30 cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $bankSoal->previousPageUrl() }}" class="page-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif
            @foreach ($bankSoal->getUrlRange(max(1, $bankSoal->currentPage()-2), min($bankSoal->lastPage(), $bankSoal->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-btn {{ $page == $bankSoal->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if ($bankSoal->hasMorePages())
                <a href="{{ $bankSoal->nextPageUrl() }}" class="page-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="page-btn opacity-30 cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ── Modal Tambah / Edit Paket Soal ────────────────────────── --}}
<div class="modal-overlay" id="soalModalOverlay">
    <div class="modal-box" style="max-width: 620px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title font-display" id="soalModalTitle">Tambah Paket Soal</h3>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0.15rem 0 0;">Kelola judul instrumen tes dan butir soal kegiatan P2M</p>
            </div>
            <button type="button" class="btn btn-secondary btn-icon" id="soalModalClose">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="soalForm" method="POST" action="{{ route('operator.bank-soal.store') }}">
            @csrf
            <input type="hidden" name="_method" id="soalFormMethod" value="POST">
            <input type="hidden" name="id" id="soalFormId">

            {{-- Hidden element to support app.js without errors --}}
            <input type="hidden" name="tipe" id="fs_tipe" value="umum">
            <input type="checkbox" name="acak_urutan" id="fs_acak" value="1" style="display: none;">

            <div class="bs-form-stack">
                {{-- Nama Paket --}}
                <div class="form-group">
                    <label class="form-label" for="fs_nama">
                        Nama Paket Soal <span style="color:var(--danger)">*</span>
                    </label>
                    <input type="text" name="nama_paket" id="fs_nama" class="form-input" placeholder="Contoh: Pre-Test Anti Narkoba Pelajar SMA" required>
                    <p class="pf-field-hint" style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.25rem;">Nama paket ini akan dipilih saat menyusun kegiatan evaluasi.</p>
                </div>

                {{-- Durasi --}}
                <div class="form-group">
                    <label class="form-label" for="fs_durasi">
                        Estimasi Durasi Pengerjaan (Menit)
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="durasi" id="fs_durasi" class="form-input font-mono" value="30" min="5" max="120" style="max-width: 140px;">
                        <span style="font-size: 0.8125rem; color: var(--text-muted);">Menit</span>
                    </div>
                </div>

                {{-- Container Soal Builder --}}
                <div class="form-group" style="margin-top: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                    <div class="flex items-center justify-between mb-2.5">
                        <div>
                            <label class="form-label" style="margin-bottom: 0;">Butir Pertanyaan</label>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.1rem 0 0;">Tambahkan soal pilihan ganda untuk paket ini</p>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="btnAddSoal" onclick="tambahItemSoalDemo()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Soal
                        </button>
                    </div>

                    <div id="soalList" class="space-y-2.5">
                        <div class="bs-empty-soal" id="soalListEmpty">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--text-muted); margin-bottom: 0.35rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p style="font-size: 0.8125rem; font-weight: 500; color: var(--text-secondary); margin: 0;">Belum ada butir pertanyaan</p>
                            <p style="font-size: 0.72rem; color: var(--text-muted); margin: 0.2rem 0 0;">Klik tombol <strong>Tambah Soal</strong> untuk menyusun pertanyaan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" id="soalModalCancelBtn">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Paket Soal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal Preview Paket Soal ──────────────────────────────── --}}
<div class="modal-overlay" id="previewModalOverlay">
    <div class="modal-box" style="max-width: 580px; max-height: 85vh; overflow-y: auto;">
        <div class="modal-header">
            <div class="flex items-center gap-2.5">
                <div class="bs-icon-box" style="width:34px;height:34px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="modal-title font-display" style="font-size: 1rem;">Pratinjau Paket Soal</h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.1rem 0 0;">Detail ringkas butir instrumen tes</p>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-icon" onclick="closePreviewModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div style="padding: 1.25rem 0;">
            <div class="bs-preview-header">
                <h4 id="pvTitle" class="font-display" style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin: 0 0 0.5rem;">—</h4>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="badge badge-blue" id="pvCountBadge">10 Soal</span>
                    <span class="badge badge-gray" id="pvDurasiBadge">Durasi: 30 Menit</span>
                    <span class="badge badge-green">P2M BNN</span>
                </div>
            </div>

            <div class="bs-preview-body" style="margin-top: 1.25rem;">
                <p style="font-size: 0.8125rem; font-weight: 600; color: var(--text-primary); margin: 0 0 0.75rem;">Contoh Butir Soal Terdaftar:</p>
                <div class="bs-preview-questions" id="pvQuestionsList">
                    {{-- Diisi secara dinamis --}}
                </div>
            </div>
        </div>

        <div class="modal-footer" style="padding-top: 1rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <button type="button" class="btn btn-secondary" onclick="closePreviewModal()">Tutup</button>
            <button type="button" class="btn btn-primary btn-sm" id="pvEditBtn">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Paket Ini
            </button>
        </div>
    </div>
</div>

{{-- ── Styles ─────────────────────────────────────────────────── --}}
<style>
/* Toolbar */
.bs-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.125rem 1.375rem;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}
.bs-heading {
    font-size: 1.125rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -0.02em;
    margin: 0;
}
.bs-subheading {
    font-size: 0.8125rem;
    color: var(--text-muted);
    margin: 0.2rem 0 0;
}

/* Search */
.bs-search-wrap {
    position: relative;
    width: 240px;
}
@media (max-width: 640px) {
    .bs-search-wrap { width: 100%; }
    .bs-btn-add { width: 100%; justify-content: center; }
}
.bs-search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 14px;
    height: 14px;
    color: var(--text-muted);
    pointer-events: none;
}
.bs-search-input {
    padding-left: 2.25rem !important;
    height: 36px;
    font-size: 0.8125rem;
    background: var(--bg-alt);
    border-color: var(--border);
}
.bs-search-input:focus {
    background: var(--surface);
}

/* Table Elements */
.bs-icon-box {
    width: 38px;
    height: 38px;
    border-radius: var(--r-md);
    background: var(--primary-light);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(67,97,238,0.15);
}
.bs-item-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.2rem;
    line-height: 1.35;
}
.bs-item-meta {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.75rem;
    color: var(--text-muted);
}
.bs-dot {
    opacity: 0.5;
}

/* Jumlah Soal Badge */
.bs-badge-soal {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.35rem 0.8rem;
    border-radius: var(--r-full);
    background: rgba(67,97,238,0.08);
    color: var(--primary);
    border: 1px solid rgba(67,97,238,0.22);
    font-size: 0.8125rem;
    font-weight: 500;
}

/* Buttons */
.bs-btn-delete {
    background: var(--danger-light);
    color: var(--danger);
    border: 1.5px solid rgba(239,68,68,0.2);
    transition: all 0.15s ease;
}
.bs-btn-delete:hover {
    background: var(--danger);
    color: #fff;
    border-color: var(--danger);
}

/* Pagination */
.bs-pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.875rem 1.375rem;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
    gap: 0.75rem;
}

/* Empty Questions in modal */
.bs-empty-soal {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem 1rem;
    background: var(--bg-alt);
    border: 1.5px dashed var(--border);
    border-radius: var(--r-lg);
    text-align: center;
}

/* Preview Modal Elements */
.bs-preview-header {
    background: var(--bg-alt);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    padding: 1rem 1.125rem;
}
.bs-preview-item {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 0.75rem 1rem;
    margin-bottom: 0.6rem;
    font-size: 0.8125rem;
}
.bs-preview-item-num {
    font-weight: 700;
    color: var(--primary);
    margin-right: 0.35rem;
}
.bs-preview-item-text {
    color: var(--text-primary);
    font-weight: 500;
}
.bs-preview-options {
    margin-top: 0.4rem;
    padding-left: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    color: var(--text-secondary);
    font-size: 0.75rem;
}
</style>

{{-- ── Scripts ─────────────────────────────────────────────────── --}}
<script>
(function() {
    // ── Client-side Live Search ─────────────────────────────────
    const filterInput = document.getElementById('filterSoal');
    if (filterInput) {
        filterInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#bankSoalTable tbody tr.soal-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const title = row.querySelector('.bs-item-title')?.textContent.toLowerCase() || '';
                const match = title.includes(query);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            const emptySearchRow = document.getElementById('soalSearchEmpty');
            if (emptySearchRow) {
                emptySearchRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
            }
        });
    }

    // ── Preview Modal Handler ───────────────────────────────────
    window.previewSoal = function(id, nama, count, durasi) {
        document.getElementById('pvTitle').textContent = nama;
        document.getElementById('pvCountBadge').textContent = `${count} Soal`;
        document.getElementById('pvDurasiBadge').textContent = `Durasi: ${durasi} Menit`;

        // Action button redirect to edit
        const editBtn = document.getElementById('pvEditBtn');
        if (editBtn) {
            editBtn.onclick = function() {
                closePreviewModal();
                if (typeof window.editSoal === 'function') window.editSoal(id);
            };
        }

        // Mock list of questions for realistic preview
        const qContainer = document.getElementById('pvQuestionsList');
        if (qContainer) {
            const sampleQuestions = [
                {
                    q: 'Apa yang dimaksud dengan bahaya penyalahgunaan Narkoba terhadap susunan syaraf pusat?',
                    opts: ['A. Meningkatkan daya konsentrasi jangka panjang', 'B. Merusak fungsi kognitif, emosi, dan koordinasi motorik', 'C. Menghilangkan rasa haus dan lapar secara alami', 'D. Meningkatkan sistem kekebalan tubuh']
                },
                {
                    q: 'Undang-Undang Republik Indonesia yang mengatur tentang Narkotika adalah...',
                    opts: ['A. UU No. 35 Tahun 2009', 'B. UU No. 22 Tahun 1997', 'C. UU No. 5 Tahun 1997', 'D. UU No. 36 Tahun 2009']
                },
                {
                    q: 'Langkah awal yang paling tepat jika mengetahui teman atau keluarga terindikasi penyalahgunaan narkoba adalah...',
                    opts: ['A. Menjauhi dan mengucilkannya dari pergaulan', 'B. Melaporkan ke pihak berwajib untuk dipenjara', 'C. Mendukung untuk melapor ke IPWL / BNN guna mendapatkan rehabilitasi', 'D. Membiarkan hingga menyadari sendiri']
                }
            ];

            let html = '';
            sampleQuestions.forEach((item, idx) => {
                html += `
                    <div class="bs-preview-item">
                        <div>
                            <span class="bs-preview-item-num">#${idx + 1}</span>
                            <span class="bs-preview-item-text">${item.q}</span>
                        </div>
                        <div class="bs-preview-options">
                            ${item.opts.map(opt => `<span>${opt}</span>`).join('')}
                        </div>
                    </div>
                `;
            });

            if (count > 3) {
                html += `<p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: 0.5rem;">... dan ${count - 3} butir pertanyaan lainnya dalam paket ini.</p>`;
            }

            qContainer.innerHTML = html;
        }

        document.getElementById('previewModalOverlay')?.classList.add('active');
    };

    window.closePreviewModal = function() {
        document.getElementById('previewModalOverlay')?.classList.remove('active');
    };

    // Close preview modal on backdrop click
    const pvOverlay = document.getElementById('previewModalOverlay');
    if (pvOverlay) {
        pvOverlay.addEventListener('click', function(e) {
            if (e.target === pvOverlay) closePreviewModal();
        });
    }

    // ── Helper Tambah Soal Demo di Modal ─────────────────────────
    window.tambahItemSoalDemo = function() {
        const emptyBox = document.getElementById('soalListEmpty');
        if (emptyBox) emptyBox.style.display = 'none';

        const soalList = document.getElementById('soalList');
        const num = soalList.querySelectorAll('.bs-builder-item').length + 1;

        const item = document.createElement('div');
        item.className = 'bs-builder-item';
        item.style.cssText = 'background: var(--bg-alt); border: 1px solid var(--border); border-radius: var(--r-md); padding: 0.75rem; margin-bottom: 0.5rem;';
        item.innerHTML = `
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.35rem;">
                <span style="font-size:0.75rem; font-weight:700; color:var(--primary);">Butir Soal #${num}</span>
                <button type="button" class="btn btn-secondary btn-icon" style="width:24px;height:24px;padding:0;" onclick="this.closest('.bs-builder-item').remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <input type="text" name="soal_teks[]" class="form-input" style="font-size:0.8125rem; padding: 0.35rem 0.6rem; margin-bottom: 0.35rem;" placeholder="Tuliskan pertanyaan...">
            <input type="text" name="kunci_jawaban[]" class="form-input" style="font-size:0.75rem; padding: 0.3rem 0.6rem;" placeholder="Kunci jawaban benar...">
        `;
        soalList.appendChild(item);
    };
})();
</script>

@endsection
