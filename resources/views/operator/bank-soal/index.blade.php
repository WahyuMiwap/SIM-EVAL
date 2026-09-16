@extends('layouts.app')

@section('title', 'Bank Soal')
@section('page-title', 'Bank Soal')
@section('page-subtitle', 'Kelola paket soal Pre-Test dan Post-Test')

@section('content')

<div class="glass-solid animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-5 border-b border-white/5">
        <div>
            <h2 class="text-base font-semibold text-white font-display">Paket Soal</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ $bankSoal->total() }} paket soal tersedia</p>
        </div>
        <button class="btn btn-primary" id="btnTambahSoal">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Paket Soal
        </button>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Paket</th>
                    <th>Tipe</th>
                    <th>Jml Soal</th>
                    <th>Acak Urutan</th>
                    <th>Digunakan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bankSoal as $index => $item)
                <tr>
                    <td class="text-slate-500 text-sm">{{ ($bankSoal->currentPage() - 1) * 10 + $index + 1 }}</td>
                    <td>
                        <p class="font-semibold text-slate-200 text-sm">{{ $item->nama_paket }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">Dibuat: {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMM Y') }}</p>
                    </td>
                    <td>
                        <span class="badge {{ $item->tipe === 'pretest' ? 'badge-blue' : ($item->tipe === 'posttest' ? 'badge-cyan' : 'badge-gray') }}">
                            {{ $item->tipe === 'pretest' ? 'Pre-Test' : ($item->tipe === 'posttest' ? 'Post-Test' : 'Umum') }}
                        </span>
                    </td>
                    <td><span class="badge badge-gray">{{ $item->soal_count ?? 0 }} soal</span></td>
                    <td>
                        @if($item->acak_urutan)
                            <span class="badge badge-green">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Aktif
                            </span>
                        @else
                            <span class="badge badge-gray">Urut Baku</span>
                        @endif
                    </td>
                    <td><span class="badge badge-gray">{{ $item->kegiatan_count ?? 0 }}x</span></td>
                    <td>
                        <div class="flex items-center gap-1.5">
                            {{-- Detail / Print --}}
                            <a href="{{ route('operator.bank-soal.detail', $item->id) }}" class="btn btn-secondary btn-icon" title="Detail / Print Kertas">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            {{-- Edit --}}
                            <button class="btn btn-secondary btn-icon" title="Edit" onclick="editSoal({{ $item->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            {{-- Hapus (FR-43) --}}
                            <button class="btn btn-danger btn-icon" title="Hapus"
                                data-reauth-action="{{ route('operator.bank-soal.destroy', $item->id) }}"
                                data-reauth-label="Paket Soal: {{ $item->nama_paket }}"
                                data-reauth-id="{{ $item->id }}"
                                onclick="ReauthModal.open(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-16">
                        <p class="text-slate-500 text-sm">Belum ada paket soal.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($bankSoal->hasPages())
    <div class="flex items-center justify-between p-5 border-t border-white/5">
        <p class="text-sm text-slate-500">Halaman {{ $bankSoal->currentPage() }} dari {{ $bankSoal->lastPage() }}</p>
        <div class="pagination">
            @if ($bankSoal->onFirstPage())
                <span class="page-btn opacity-30 cursor-not-allowed"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
            @else
                <a href="{{ $bankSoal->previousPageUrl() }}" class="page-btn"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
            @endif
            @foreach ($bankSoal->getUrlRange(max(1, $bankSoal->currentPage()-2), min($bankSoal->lastPage(), $bankSoal->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-btn {{ $page == $bankSoal->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if ($bankSoal->hasMorePages())
                <a href="{{ $bankSoal->nextPageUrl() }}" class="page-btn"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
            @else
                <span class="page-btn opacity-30 cursor-not-allowed"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Modal Tambah / Edit Paket Soal --}}
<div class="modal-overlay" id="soalModalOverlay">
    <div class="modal-box" style="max-width: 680px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h3 class="modal-title font-display" id="soalModalTitle">Tambah Paket Soal</h3>
            <button type="button" class="btn btn-secondary btn-icon" id="soalModalClose">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="soalForm" method="POST" action="{{ route('operator.bank-soal.store') }}">
            @csrf
            <input type="hidden" name="_method" id="soalFormMethod" value="POST">
            <input type="hidden" name="id" id="soalFormId">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div class="form-group sm:col-span-2">
                    <label class="form-label">Nama Paket Soal <span class="text-red-400">*</span></label>
                    <input type="text" name="nama_paket" id="fs_nama" class="form-input" placeholder="cth: Pre-Test Anti Narkoba Kelas X" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipe Soal</label>
                    <select name="tipe" id="fs_tipe" class="form-input">
                        <option value="pretest">Pre-Test</option>
                        <option value="posttest">Post-Test</option>
                        <option value="umum">Umum (dapat digunakan keduanya)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Durasi Maksimal (menit)</label>
                    <input type="number" name="durasi" id="fs_durasi" class="form-input" value="30" min="5" max="120">
                </div>
            </div>

            {{-- Toggle FR-24c: Acak Urutan Soal --}}
            <div class="flex items-start gap-4 p-4 rounded-xl bg-white/3 border border-white/8 mb-4">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-200">Acak Urutan Soal (Mode Digital)</p>
                    <p class="text-xs text-slate-500 mt-1">Jika aktif, urutan soal diacak per peserta pada mode online. Lembar kertas tetap urut baku (FR-24c).</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5">
                    <input type="checkbox" name="acak_urutan" id="fs_acak" value="1" class="sr-only peer" {{ old('acak_urutan') ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            {{-- FR-28b: Samakan dengan Pre-Test --}}
            <div class="flex items-start gap-4 p-4 rounded-xl bg-blue-500/5 border border-blue-500/15 mb-5" id="samainPreTestSection" style="{{ old('tipe') === 'posttest' ? '' : 'display:none;' }}">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-300">Samakan dengan Pre-Test</p>
                    <p class="text-xs text-slate-500 mt-1">Salin struktur & isi soal dari paket Pre-Test yang dipilih. Hemat waktu penyusunan (FR-28b).</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <select name="copy_from_pretest_id" id="fs_copyPre" class="form-input py-2 text-sm" style="width: 180px;">
                        <option value="">-- Pilih Paket Pre-Test --</option>
                        @foreach($pretestList ?? [] as $pre)
                            <option value="{{ $pre->id }}">{{ $pre->nama_paket }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-primary btn-sm" id="btnCopyPreTest">Salin</button>
                </div>
            </div>

            {{-- Daftar Soal Builder --}}
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="form-label mb-0">Daftar Soal</label>
                    <button type="button" class="btn btn-secondary btn-sm" id="btnAddSoal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Soal
                    </button>
                </div>
                <div id="soalList" class="space-y-3">
                    {{-- Soal diisi oleh JS --}}
                    <div class="text-center py-6 text-slate-600 text-sm border border-dashed border-white/10 rounded-xl" id="soalListEmpty">
                        Belum ada soal. Klik "Tambah Soal" untuk memulai.
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" class="btn btn-secondary flex-1" id="soalModalCancelBtn">Batal</button>
                <button type="submit" class="btn btn-primary flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Paket Soal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
