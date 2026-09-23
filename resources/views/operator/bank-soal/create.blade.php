@extends('layouts.app')

@section('title', 'Tambah Paket Soal Baru — Bank Soal')
@section('page-title', 'Tambah Paket Soal')
@section('page-subtitle', 'Susun instrumen tes dan butir soal evaluasi baru untuk kegiatan P2M')

@section('content')

{{-- ── Breadcrumb ─────────────────────────────────────────────── --}}
<div class="flex items-center gap-2 text-xs mb-4 text-slate-400">
    <a href="{{ route('operator.bank-soal.index') }}" class="hover:text-primary transition-colors font-medium text-slate-600 dark:text-slate-300">Bank Soal</a>
    <span class="opacity-40">/</span>
    <span class="font-semibold text-slate-800 dark:text-slate-100">Tambah Paket Soal Baru</span>
</div>

<form id="formCreateSoal" method="POST" action="{{ route('operator.bank-soal.store') }}">
    @csrf
    {{-- Default tipe dan durasi diset otomatis untuk kompatibilitas database --}}
    <input type="hidden" name="tipe" value="umum">
    <input type="hidden" name="durasi" value="{{ old('durasi', 30) }}">

    {{-- ── Header Card & Actions ─────────────────────────────────── --}}
    <div class="glass rounded-2xl border border-slate-200/70 dark:border-slate-800 p-4 md:p-5 mb-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm md:text-base font-bold text-slate-800 dark:text-slate-100">
                        Buat Paket Instrumen Soal Baru
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Tuliskan butir soal pilihan ganda beserta kunci jawaban untuk digunakan dalam evaluasi peserta.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('operator.bank-soal.index') }}" class="btn btn-secondary btn-sm rounded-xl">
                    Batal
                </a>
                <button type="submit" formaction="{{ route('operator.bank-soal.store', ['lagi' => 1]) }}" class="btn btn-secondary btn-sm rounded-xl shadow-2xs" title="Simpan lalu buat paket berikutnya">
                    <span>Simpan &amp; Buat Lagi</span>
                </button>
                <button type="submit" class="btn btn-primary btn-sm rounded-xl shadow-xs inline-flex items-center gap-1.5" id="btnSubmitCreate">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Paket Soal</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── SECTION 1: Informasi Umum Paket ───────────────────────── --}}
    <div class="glass rounded-2xl border border-slate-200/70 dark:border-slate-800 p-5 md:p-6 mb-5 shadow-xs">
        <div class="flex items-center gap-2.5 pb-3 mb-4 border-b border-slate-100 dark:border-slate-800">
            <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-primary font-bold text-xs flex items-center justify-center flex-shrink-0">1</span>
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100">Informasi Umum Paket</h3>
                <p class="text-xs text-slate-400 mt-0.5">Identitas nama instrumen tes dan topik materi evaluasi.</p>
            </div>
        </div>

        <div class="space-y-4 text-xs">
            {{-- Nama Paket --}}
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="nama_paket">
                    Nama Paket Soal <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_paket" id="nama_paket" class="form-input w-full rounded-xl text-xs border border-slate-200/80 dark:border-slate-700"
                    value="{{ old('nama_paket') }}" required
                    placeholder="Contoh: Instrumen Evaluasi Pemahaman P4GN Remaja & Pelajar">
                <p class="text-[11px] text-slate-400 mt-1">Nama ini akan muncul pada opsi pemilihan paket soal saat membuat kegiatan sosialisasi.</p>
            </div>

            {{-- Tema Materi --}}
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="tema">
                    Tema Sosialisasi
                </label>
                <div id="temaDropdownWrapper" class="relative w-full">
                    <input type="text" name="tema" id="tema"
                        class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-10 shadow-2xs"
                        style="height: 40px;"
                        value="{{ old('tema') }}" maxlength="100" autocomplete="off"
                        placeholder="cth: P4GN Pelajar, Ketahanan Keluarga, Bahaya Narkoba">
                    <button type="button" id="btnToggleTemaSuggestions"
                        style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); z-index: 10;"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer p-1 flex items-center justify-center"
                        title="Tampilkan daftar saran tema">
                        <svg id="temaCaret" class="w-4 h-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Floating Suggestions Card --}}
                    <div id="temaSuggestionsMenu"
                         class="hidden absolute left-0 right-0 z-50 mt-1.5 max-h-80 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl text-xs py-1 divide-y divide-slate-100/70 dark:divide-slate-800/70">
                        @forelse(($temas ?? []) as $t)
                        <button type="button"
                                data-tema="{{ $t }}"
                                class="tema-suggestion-item w-full text-left px-3.5 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors cursor-pointer group">
                            <span class="block font-semibold text-slate-800 dark:text-slate-100 text-xs group-hover:text-primary transition-colors">{{ $t }}</span>
                            <span class="block text-[11px] text-slate-400 font-normal mt-0.5">Pilih tema materi evaluasi ini</span>
                        </button>
                        @empty
                        <div class="px-3.5 py-2.5 text-center text-slate-400 text-xs">
                            Belum ada saran tema tersimpan. Bebas ketik tema baru.
                        </div>
                        @endforelse
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Bebas ketik baru — atau klik panah untuk memilih tema yang sudah terdaftar sebelumnya.</p>
            </div>
        </div>
    </div>

    {{-- ── SECTION 2: Daftar Butir Soal (Builder) ─────────────────── --}}
    <div class="glass rounded-2xl border border-slate-200/70 dark:border-slate-800 p-5 md:p-6 mb-5 shadow-xs">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-primary font-bold text-xs flex items-center justify-center flex-shrink-0">2</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100">Daftar Butir Pertanyaan</h3>
                        <span id="questionCounterBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/50 dark:border-blue-900/40">1 Soal</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Tuliskan pertanyaan pilihan ganda (A, B, C, D) dan klik huruf untuk menentukan kunci jawaban yang benar.</p>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm rounded-xl inline-flex items-center gap-1.5 shadow-2xs font-semibold text-xs" onclick="addNewQuestionBlock()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Butir Soal</span>
            </button>
        </div>

        {{-- Container Butir Soal --}}
        <div id="questionsBuilderContainer" class="space-y-4">
            {{-- Inisialisasi 1 butir pertanyaan default --}}
            <div class="bs-edit-card rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white/70 dark:bg-slate-900/50 p-4 md:p-5 transition-all shadow-2xs" data-index="0">
                <div class="flex items-center justify-between mb-3.5 pb-2.5 border-b border-slate-100 dark:border-slate-800/80">
                    <div class="flex items-center gap-2">
                        <span class="bs-q-num-edit px-2.5 py-1 rounded-lg bg-primary text-white text-xs font-bold font-mono">#1</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Butir Pertanyaan</span>
                        <span class="bs-current-key-badge text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800/40">
                            Kunci: A
                        </span>
                    </div>
                    <button type="button" class="bs-btn-del-item p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors" title="Hapus butir pertanyaan ini" onclick="removeQuestionBlock(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                {{-- Teks Pertanyaan --}}
                <div class="mb-3.5">
                    <label class="block font-semibold text-xs text-slate-700 dark:text-slate-300 mb-1.5">Teks Pertanyaan <span class="text-rose-500">*</span></label>
                    <textarea name="soal[0][pertanyaan]" class="form-input w-full rounded-xl text-xs border border-slate-200/80 dark:border-slate-700 p-3 leading-relaxed" rows="2" placeholder="Tuliskan butir soal atau pertanyaan evaluasi di sini..." required></textarea>
                </div>

                {{-- Opsi Pilihan Jawaban (A, B, C, D) --}}
                <div>
                    <label class="block font-semibold text-xs text-slate-700 dark:text-slate-300 mb-2">Pilihan Jawaban &amp; Kunci Jawaban <span class="text-slate-400 font-normal">(Klik huruf untuk memilih kunci)</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="bs-edit-option-row is-selected-key border border-slate-200/80 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 p-2 flex items-center gap-2.5 transition-all">
                            <label class="bs-radio-key cursor-pointer flex items-center justify-center flex-shrink-0" title="Klik untuk jadikan kunci jawaban A">
                                <input type="radio" name="soal[0][kunci]" value="A" checked onchange="onKeyChange(this)" class="sr-only">
                                <span class="bs-opt-letter w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition-all">A</span>
                            </label>
                            <input type="text" name="soal[0][opsi][A]" class="bs-opt-input flex-1 text-xs text-slate-800 dark:text-slate-100 bg-transparent border-none outline-none p-1 placeholder:text-slate-400" placeholder="Pilihan jawaban A" required>
                        </div>

                        <div class="bs-edit-option-row border border-slate-200/80 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 p-2 flex items-center gap-2.5 transition-all">
                            <label class="bs-radio-key cursor-pointer flex items-center justify-center flex-shrink-0" title="Klik untuk jadikan kunci jawaban B">
                                <input type="radio" name="soal[0][kunci]" value="B" onchange="onKeyChange(this)" class="sr-only">
                                <span class="bs-opt-letter w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition-all">B</span>
                            </label>
                            <input type="text" name="soal[0][opsi][B]" class="bs-opt-input flex-1 text-xs text-slate-800 dark:text-slate-100 bg-transparent border-none outline-none p-1 placeholder:text-slate-400" placeholder="Pilihan jawaban B" required>
                        </div>

                        <div class="bs-edit-option-row border border-slate-200/80 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 p-2 flex items-center gap-2.5 transition-all">
                            <label class="bs-radio-key cursor-pointer flex items-center justify-center flex-shrink-0" title="Klik untuk jadikan kunci jawaban C">
                                <input type="radio" name="soal[0][kunci]" value="C" onchange="onKeyChange(this)" class="sr-only">
                                <span class="bs-opt-letter w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition-all">C</span>
                            </label>
                            <input type="text" name="soal[0][opsi][C]" class="bs-opt-input flex-1 text-xs text-slate-800 dark:text-slate-100 bg-transparent border-none outline-none p-1 placeholder:text-slate-400" placeholder="Pilihan jawaban C" required>
                        </div>

                        <div class="bs-edit-option-row border border-slate-200/80 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 p-2 flex items-center gap-2.5 transition-all">
                            <label class="bs-radio-key cursor-pointer flex items-center justify-center flex-shrink-0" title="Klik untuk jadikan kunci jawaban D">
                                <input type="radio" name="soal[0][kunci]" value="D" onchange="onKeyChange(this)" class="sr-only">
                                <span class="bs-opt-letter w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition-all">D</span>
                            </label>
                            <input type="text" name="soal[0][opsi][D]" class="bs-opt-input flex-1 text-xs text-slate-800 dark:text-slate-100 bg-transparent border-none outline-none p-1 placeholder:text-slate-400" placeholder="Pilihan jawaban D" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Question Button at Bottom --}}
        <div class="pt-4 mt-4">
            <button type="button" class="btn btn-secondary w-full py-3 rounded-xl border-dashed border-2 border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-primary hover:border-primary transition-all flex items-center justify-center gap-2" onclick="addNewQuestionBlock()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Tambah Butir Pertanyaan Baru</span>
            </button>
        </div>
    </div>

    {{-- Bottom Submit Bar --}}
    <div class="glass rounded-2xl border border-slate-200/70 dark:border-slate-800 p-4 md:p-5 flex items-center justify-between shadow-xs">
        <a href="{{ route('operator.bank-soal.index') }}" class="btn btn-secondary btn-sm rounded-xl">
            Batal
        </a>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="submit" formaction="{{ route('operator.bank-soal.store', ['lagi' => 1]) }}" class="btn btn-secondary btn-sm rounded-xl shadow-2xs" title="Simpan lalu buat paket berikutnya">
                <span>Simpan &amp; Buat Lagi</span>
            </button>
            <button type="submit" class="btn btn-primary btn-sm rounded-xl shadow-xs inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan Paket Soal</span>
            </button>
        </div>
    </div>
</form>

{{-- ── Styles Scoped ───────────────────────────────────────────── --}}
<style>
/* Option Key Row Styling */
.bs-edit-option-row {
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
}
.bs-edit-option-row:hover {
    border-color: var(--primary);
}
.bs-edit-option-row.is-selected-key {
    border-color: #10B981 !important;
    background: rgba(16, 185, 129, 0.06) !important;
    box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.3);
}

.bs-opt-letter {
    background: #F1F5F9;
    color: #475569;
    border: 1px solid #E2E8F0;
    transition: all 0.18s ease;
}
:is(.dark) .bs-opt-letter {
    background: #1E293B;
    color: #94A3B8;
    border-color: #334155;
}
.bs-edit-option-row.is-selected-key .bs-opt-letter {
    background: #10B981 !important;
    color: #FFFFFF !important;
    border-color: #10B981 !important;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}
</style>

{{-- ── Scripts ─────────────────────────────────────────────────── --}}
<script>
function updateQuestionCounter() {
    const cards = document.querySelectorAll('#questionsBuilderContainer .bs-edit-card');
    const badge = document.getElementById('questionCounterBadge');
    if (badge) {
        badge.textContent = `${cards.length} Soal`;
    }
}

function onKeyChange(radio) {
    const card = radio.closest('.bs-edit-card');
    if (!card) return;
    card.querySelectorAll('.bs-edit-option-row').forEach(row => row.classList.remove('is-selected-key'));
    radio.closest('.bs-edit-option-row')?.classList.add('is-selected-key');

    const keyBadge = card.querySelector('.bs-current-key-badge');
    if (keyBadge) {
        keyBadge.textContent = `Kunci: ${radio.value}`;
    }
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
    updateQuestionCounter();
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
    div.className = 'bs-edit-card rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white/70 dark:bg-slate-900/50 p-4 md:p-5 transition-all shadow-2xs';
    div.setAttribute('data-index', newIdx);
    div.innerHTML = `
        <div class="flex items-center justify-between mb-3.5 pb-2.5 border-b border-slate-100 dark:border-slate-800/80">
            <div class="flex items-center gap-2">
                <span class="bs-q-num-edit px-2.5 py-1 rounded-lg bg-primary text-white text-xs font-bold font-mono">#${newIdx + 1}</span>
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Butir Pertanyaan Baru</span>
                <span class="bs-current-key-badge text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800/40">
                    Kunci: A
                </span>
            </div>
            <button type="button" class="bs-btn-del-item p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors" title="Hapus butir pertanyaan ini" onclick="removeQuestionBlock(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

        <div class="mb-3.5">
            <label class="block font-semibold text-xs text-slate-700 dark:text-slate-300 mb-1.5">Teks Pertanyaan <span class="text-rose-500">*</span></label>
            <textarea name="soal[${newIdx}][pertanyaan]" class="form-input w-full rounded-xl text-xs border border-slate-200/80 dark:border-slate-700 p-3 leading-relaxed" rows="2" placeholder="Tuliskan butir soal atau pertanyaan evaluasi di sini..." required></textarea>
        </div>

        <div>
            <label class="block font-semibold text-xs text-slate-700 dark:text-slate-300 mb-2">Pilihan Jawaban &amp; Kunci Jawaban <span class="text-slate-400 font-normal">(Klik huruf untuk memilih kunci)</span></label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="bs-edit-option-row is-selected-key border border-slate-200/80 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 p-2 flex items-center gap-2.5 transition-all">
                    <label class="bs-radio-key cursor-pointer flex items-center justify-center flex-shrink-0" title="Klik untuk jadikan kunci jawaban A">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="A" checked onchange="onKeyChange(this)" class="sr-only">
                        <span class="bs-opt-letter w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition-all">A</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][A]" class="bs-opt-input flex-1 text-xs text-slate-800 dark:text-slate-100 bg-transparent border-none outline-none p-1 placeholder:text-slate-400" placeholder="Pilihan jawaban A" required>
                </div>

                <div class="bs-edit-option-row border border-slate-200/80 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 p-2 flex items-center gap-2.5 transition-all">
                    <label class="bs-radio-key cursor-pointer flex items-center justify-center flex-shrink-0" title="Klik untuk jadikan kunci jawaban B">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="B" onchange="onKeyChange(this)" class="sr-only">
                        <span class="bs-opt-letter w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition-all">B</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][B]" class="bs-opt-input flex-1 text-xs text-slate-800 dark:text-slate-100 bg-transparent border-none outline-none p-1 placeholder:text-slate-400" placeholder="Pilihan jawaban B" required>
                </div>

                <div class="bs-edit-option-row border border-slate-200/80 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 p-2 flex items-center gap-2.5 transition-all">
                    <label class="bs-radio-key cursor-pointer flex items-center justify-center flex-shrink-0" title="Klik untuk jadikan kunci jawaban C">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="C" onchange="onKeyChange(this)" class="sr-only">
                        <span class="bs-opt-letter w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition-all">C</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][C]" class="bs-opt-input flex-1 text-xs text-slate-800 dark:text-slate-100 bg-transparent border-none outline-none p-1 placeholder:text-slate-400" placeholder="Pilihan jawaban C" required>
                </div>

                <div class="bs-edit-option-row border border-slate-200/80 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 p-2 flex items-center gap-2.5 transition-all">
                    <label class="bs-radio-key cursor-pointer flex items-center justify-center flex-shrink-0" title="Klik untuk jadikan kunci jawaban D">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="D" onchange="onKeyChange(this)" class="sr-only">
                        <span class="bs-opt-letter w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs transition-all">D</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][D]" class="bs-opt-input flex-1 text-xs text-slate-800 dark:text-slate-100 bg-transparent border-none outline-none p-1 placeholder:text-slate-400" placeholder="Pilihan jawaban D" required>
                </div>
            </div>
        </div>
    `;

    container.appendChild(div);
    updateQuestionCounter();
    div.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ── Tema Suggestions Dropdown Controller ────────────────────────
(function () {
    const input     = document.getElementById('tema');
    const menu      = document.getElementById('temaSuggestionsMenu');
    const toggleBtn = document.getElementById('btnToggleTemaSuggestions');
    const caret     = document.getElementById('temaCaret');
    const items     = document.querySelectorAll('.tema-suggestion-item');
    const wrapper   = document.getElementById('temaDropdownWrapper');

    if (!input || !menu) return;

    function filterItems() {
        const query = (input.value || '').trim().toLowerCase();
        let visibleCount = 0;
        items.forEach(item => {
            const val = (item.getAttribute('data-tema') || '').toLowerCase();
            if (!query || val.includes(query)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        return visibleCount;
    }

    function openMenu() {
        const count = filterItems();
        if (count > 0 || items.length === 0) {
            menu.classList.remove('hidden');
            if (caret) caret.classList.add('rotate-180');
        }
    }

    function closeMenu() {
        menu.classList.add('hidden');
        if (caret) caret.classList.remove('rotate-180');
    }

    function toggleMenu() {
        if (menu.classList.contains('hidden')) {
            openMenu();
        } else {
            closeMenu();
        }
    }

    input.addEventListener('input', () => {
        openMenu();
    });

    input.addEventListener('focus', () => {
        openMenu();
    });

    toggleBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleMenu();
    });

    items.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const val = item.getAttribute('data-tema') || '';
            input.value = val;
            closeMenu();
            input.focus();
        });
    });

    document.addEventListener('click', (e) => {
        if (wrapper && !wrapper.contains(e.target)) {
            closeMenu();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMenu();
        }
    });
})();
</script>

@endsection
