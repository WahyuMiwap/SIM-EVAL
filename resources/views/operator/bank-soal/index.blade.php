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
                <span class="badge badge-blue font-semibold" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">
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

            {{-- Tambah Paket Soal Button (Route ke Halaman Baru) --}}
            <a href="{{ route('operator.bank-soal.create') }}" class="btn btn-primary btn-sm bs-btn-add" id="btnTambahSoal">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Paket Soal</span>
            </a>
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
                    <td class="text-center font-medium" style="color: var(--text-muted); font-size: 0.8125rem;">
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
                                <a href="{{ route('operator.bank-soal.detail', $item->id) }}" class="bs-item-title-link">
                                    <p class="bs-item-title">{{ $item->nama_paket }}</p>
                                </a>
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
                            <span class="font-bold">{{ $item->soal_count ?? 0 }}</span> Soal
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td>
                        <div class="flex items-center justify-end gap-1.5">
                            {{-- Pratinjau / Lihat Butir Soal (Route ke Halaman Detail) --}}
                            <a href="{{ route('operator.bank-soal.detail', $item->id) }}" class="btn btn-secondary btn-sm btn-icon" title="Lihat Butir Soal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            {{-- Edit (Route ke Halaman Edit) --}}
                            <a href="{{ route('operator.bank-soal.edit', $item->id) }}" class="btn btn-secondary btn-sm btn-icon" title="Edit Paket Soal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

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
                            <a href="{{ route('operator.bank-soal.create') }}" class="btn btn-primary btn-sm">
                                Tambah Paket Soal
                            </a>
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
.bs-item-title-link {
    text-decoration: none;
}
.bs-item-title-link:hover .bs-item-title {
    color: var(--primary);
}
.bs-item-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.2rem;
    line-height: 1.35;
    transition: color 0.15s ease;
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
})();
</script>

@endsection
