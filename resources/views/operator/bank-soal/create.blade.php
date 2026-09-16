@extends('layouts.app')

@section('title', 'Tambah Paket Soal Baru — Bank Soal')
@section('page-title', 'Tambah Paket Soal')
@section('page-subtitle', 'Susun instrumen tes dan butir soal evaluasi baru untuk kegiatan P2M')

@section('content')

{{-- ── Breadcrumb ─────────────────────────────────────────────── --}}
<div class="bs-breadcrumb">
    <a href="{{ route('operator.bank-soal.index') }}" class="bs-bc-link">Bank Soal</a>
    <span class="bs-bc-sep">/</span>
    <span class="bs-bc-current">Tambah Paket Soal Baru</span>
</div>

<form id="formCreateSoal" method="POST" action="{{ route('operator.bank-soal.store') }}">
    @csrf

    {{-- ── Header Actions ─────────────────────────────────────────── --}}
    <div class="glass animate-fade-in" style="border-radius: var(--r-xl); background: var(--surface); border: 1px solid var(--border); padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm);">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-display font-bold text-lg" style="color: var(--text-primary); margin: 0 0 0.2rem;">
                    Buat Paket Soal Baru
                </h2>
                <p class="text-xs" style="color: var(--text-muted); margin: 0;">
                    Tuliskan butir soal pilihan ganda beserta kunci jawaban untuk digunakan dalam evaluasi peserta.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('operator.bank-soal.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" id="btnSubmitCreate">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Paket Soal</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── SECTION 1: Informasi Paket ─────────────────────────────── --}}
    <div class="glass" style="border-radius: var(--r-xl); background: var(--surface); border: 1px solid var(--border); padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm);">
        <h3 class="font-display font-bold text-base mb-1" style="color: var(--text-primary);">
            Informasi Umum Paket
        </h3>
        <p class="text-xs text-muted mb-4" style="color: var(--text-muted);">
            Identitas nama paket instrumen tes dan alokasi waktu pengerjaan.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group md:col-span-2">
                <label class="form-label" for="nama_paket">
                    Nama Paket Soal <span style="color:var(--danger)">*</span>
                </label>
                <input type="text" name="nama_paket" id="nama_paket" class="form-input"
                    value="{{ old('nama_paket') }}" required
                    placeholder="Contoh: Pre-Test Anti Narkoba Pelajar SMA / Remaja">
                <p class="text-xs text-muted mt-1" style="color: var(--text-muted);">Nama ini akan muncul pada opsi pemilihan paket soal kegiatan.</p>
            </div>

            <div class="form-group">
                <label class="form-label" for="durasi">
                    Durasi Pengerjaan (Menit) <span style="color:var(--danger)">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input type="number" name="durasi" id="durasi" class="form-input"
                        value="{{ old('durasi', 30) }}" min="5" max="180" required>
                    <span class="text-xs" style="color: var(--text-muted);">Menit</span>
                </div>
                <p class="text-xs text-muted mt-1" style="color: var(--text-muted);">Waktu hitung mundur bagi peserta digital.</p>
            </div>
        </div>
    </div>

    {{-- ── SECTION 2: Daftar Butir Soal (Builder) ─────────────────── --}}
    <div class="glass" style="border-radius: var(--r-xl); background: var(--surface); border: 1px solid var(--border); padding: 1.5rem; box-shadow: var(--shadow-sm);">
        <div class="flex items-center justify-between pb-3 mb-4" style="border-bottom: 1px solid var(--border); flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 class="font-display font-bold text-base" style="color: var(--text-primary); margin: 0 0 0.15rem;">
                    Daftar Butir Pertanyaan
                </h3>
                <p class="text-xs" style="color: var(--text-muted); margin: 0;">
                    Pilih tombol radio (A, B, C, atau D) untuk menetapkan kunci jawaban yang benar.
                </p>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="addNewQuestionBlock()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Butir Soal</span>
            </button>
        </div>

        {{-- Container Butir Soal --}}
        <div id="questionsBuilderContainer" class="space-y-4">
            {{-- Inisialisasi 1 butir pertanyaan default --}}
            <div class="bs-edit-card" data-index="0">
                <div class="flex items-center justify-between mb-3 pb-2" style="border-bottom: 1px dashed var(--border);">
                    <div class="flex items-center gap-2">
                        <span class="bs-q-num-edit">#1</span>
                        <span class="text-xs font-semibold" style="color: var(--text-secondary);">Pertanyaan Pilihan Ganda</span>
                    </div>
                    <button type="button" class="btn btn-secondary btn-icon btn-sm bs-btn-del-item" title="Hapus butir ini" onclick="removeQuestionBlock(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label text-xs">Teks Pertanyaan</label>
                    <textarea name="soal[0][pertanyaan]" class="form-input" rows="2" placeholder="Tuliskan butir pertanyaan di sini..." required></textarea>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label text-xs mb-1.5">Opsi Pilihan Jawaban &amp; Kunci Jawaban</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                        <div class="bs-edit-option-row is-selected-key">
                            <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                                <input type="radio" name="soal[0][kunci]" value="A" checked onchange="onKeyChange(this)">
                                <span class="bs-opt-letter">A</span>
                            </label>
                            <input type="text" name="soal[0][opsi][A]" class="form-input bs-opt-input" placeholder="Tuliskan pilihan jawaban A" required>
                        </div>
                        <div class="bs-edit-option-row">
                            <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                                <input type="radio" name="soal[0][kunci]" value="B" onchange="onKeyChange(this)">
                                <span class="bs-opt-letter">B</span>
                            </label>
                            <input type="text" name="soal[0][opsi][B]" class="form-input bs-opt-input" placeholder="Tuliskan pilihan jawaban B" required>
                        </div>
                        <div class="bs-edit-option-row">
                            <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                                <input type="radio" name="soal[0][kunci]" value="C" onchange="onKeyChange(this)">
                                <span class="bs-opt-letter">C</span>
                            </label>
                            <input type="text" name="soal[0][opsi][C]" class="form-input bs-opt-input" placeholder="Tuliskan pilihan jawaban C" required>
                        </div>
                        <div class="bs-edit-option-row">
                            <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                                <input type="radio" name="soal[0][kunci]" value="D" onchange="onKeyChange(this)">
                                <span class="bs-opt-letter">D</span>
                            </label>
                            <input type="text" name="soal[0][opsi][D]" class="form-input bs-opt-input" placeholder="Tuliskan pilihan jawaban D" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Question Button at Bottom --}}
        <div class="pt-4 mt-4 text-center">
            <button type="button" class="btn btn-secondary w-full" style="border-style: dashed; padding: 0.75rem;" onclick="addNewQuestionBlock()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Tambah Butir Soal Baru</span>
            </button>
        </div>
    </div>

    {{-- Bottom Submit Bar --}}
    <div class="flex items-center justify-between pt-5 mt-5">
        <a href="{{ route('operator.bank-soal.index') }}" class="btn btn-secondary">
            Batal
        </a>
        <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Simpan Paket Soal</span>
        </button>
    </div>
</form>

{{-- ── Styles ─────────────────────────────────────────────────── --}}
<style>
/* Breadcrumb */
.bs-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    margin-bottom: 1.25rem;
    color: var(--text-muted);
}
.bs-bc-link {
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 500;
}
.bs-bc-link:hover { color: var(--primary); }
.bs-bc-sep { opacity: 0.4; }
.bs-bc-current { font-weight: 600; color: var(--text-primary); }

/* Edit Question Card */
.bs-edit-card {
    background: var(--bg-alt);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    padding: 1.25rem;
    position: relative;
    transition: all 0.15s ease;
}
.bs-edit-card:hover {
    border-color: rgba(67,97,238,0.25);
}
.bs-q-num-edit {
    background: var(--primary);
    color: #fff;
    font-weight: 700;
    font-size: 0.75rem;
    padding: 0.2rem 0.6rem;
    border-radius: var(--r-xs);
}
.bs-btn-del-item:hover {
    background: var(--danger-light);
}

/* Option Row */
.bs-edit-option-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 0.35rem 0.5rem;
    transition: all 0.15s ease;
}
.bs-edit-option-row.is-selected-key {
    border-color: #22C55E;
    background: rgba(34, 197, 94, 0.05);
}
.bs-radio-key {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
    padding: 0.2rem 0.4rem;
    border-radius: var(--r-xs);
    flex-shrink: 0;
}
.bs-radio-key input[type="radio"] {
    accent-color: #16A34A;
    cursor: pointer;
}
.bs-opt-letter {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-primary);
}
.bs-opt-input {
    border: none !important;
    background: transparent !important;
    padding: 0.3rem 0.4rem !important;
    font-size: 0.8125rem !important;
    height: auto !important;
    box-shadow: none !important;
}
</style>

{{-- ── Scripts ─────────────────────────────────────────────────── --}}
<script>
function onKeyChange(radio) {
    const card = radio.closest('.bs-edit-card');
    if (!card) return;
    card.querySelectorAll('.bs-edit-option-row').forEach(row => row.classList.remove('is-selected-key'));
    radio.closest('.bs-edit-option-row')?.classList.add('is-selected-key');
}

function removeQuestionBlock(btn) {
    const card = btn.closest('.bs-edit-card');
    const container = document.getElementById('questionsBuilderContainer');
    if (container.querySelectorAll('.bs-edit-card').length <= 1) {
        alert('Paket soal minimal harus memiliki 1 butir pertanyaan.');
        return;
    }
    card.remove();
    reindexQuestions();
}

function reindexQuestions() {
    const cards = document.querySelectorAll('#questionsBuilderContainer .bs-edit-card');
    cards.forEach((card, idx) => {
        card.setAttribute('data-index', idx);
        const numBadge = card.querySelector('.bs-q-num-edit');
        if (numBadge) numBadge.textContent = `#${idx + 1}`;

        // Reindex inputs
        const textarea = card.querySelector('textarea');
        if (textarea) textarea.name = `soal[${idx}][pertanyaan]`;

        ['A', 'B', 'C', 'D'].forEach(opt => {
            const radio = card.querySelector(`input[value="${opt}"]`);
            if (radio) radio.name = `soal[${idx}][kunci]`;
            const optInput = card.querySelector(`input[name*="[opsi][${opt}]"]`);
            if (optInput) optInput.name = `soal[${idx}][opsi][${opt}]`;
        });
    });
}

function addNewQuestionBlock() {
    const container = document.getElementById('questionsBuilderContainer');
    const newIdx = container.querySelectorAll('.bs-edit-card').length;

    const div = document.createElement('div');
    div.className = 'bs-edit-card';
    div.setAttribute('data-index', newIdx);
    div.innerHTML = `
        <div class="flex items-center justify-between mb-3 pb-2" style="border-bottom: 1px dashed var(--border);">
            <div class="flex items-center gap-2">
                <span class="bs-q-num-edit">#${newIdx + 1}</span>
                <span class="text-xs font-semibold" style="color: var(--text-secondary);">Pertanyaan Pilihan Ganda Baru</span>
            </div>
            <button type="button" class="btn btn-secondary btn-icon btn-sm bs-btn-del-item" title="Hapus butir pertanyaan ini" onclick="removeQuestionBlock(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

        <div class="form-group mb-3">
            <label class="form-label text-xs">Teks Pertanyaan</label>
            <textarea name="soal[${newIdx}][pertanyaan]" class="form-input" rows="2" placeholder="Tuliskan butir pertanyaan di sini..." required></textarea>
        </div>

        <div class="form-group mb-0">
            <label class="form-label text-xs mb-1.5">Opsi Pilihan Jawaban &amp; Kunci Jawaban</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                <div class="bs-edit-option-row is-selected-key">
                    <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="A" checked onchange="onKeyChange(this)">
                        <span class="bs-opt-letter">A</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][A]" class="form-input bs-opt-input" placeholder="Tuliskan pilihan jawaban A" required>
                </div>
                <div class="bs-edit-option-row">
                    <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="B" onchange="onKeyChange(this)">
                        <span class="bs-opt-letter">B</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][B]" class="form-input bs-opt-input" placeholder="Tuliskan pilihan jawaban B" required>
                </div>
                <div class="bs-edit-option-row">
                    <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="C" onchange="onKeyChange(this)">
                        <span class="bs-opt-letter">C</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][C]" class="form-input bs-opt-input" placeholder="Tuliskan pilihan jawaban C" required>
                </div>
                <div class="bs-edit-option-row">
                    <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="D" onchange="onKeyChange(this)">
                        <span class="bs-opt-letter">D</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][D]" class="form-input bs-opt-input" placeholder="Tuliskan pilihan jawaban D" required>
                </div>
            </div>
        </div>
    `;

    container.appendChild(div);
    div.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>

@endsection
