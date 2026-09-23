@extends('layouts.app')

@section('title', 'Edit Paket Soal — ' . ($paket->nama_paket ?? 'Bank Soal'))
@section('page-title', 'Edit Paket Soal')
@section('page-subtitle', 'Perbarui instrumen tes dan butir pertanyaan evaluasi P2M')

@section('content')

{{-- ── Breadcrumb ─────────────────────────────────────────────── --}}
<div class="bs-breadcrumb">
    <a href="{{ route('operator.bank-soal.index') }}" class="bs-bc-link">Bank Soal</a>
    <span class="bs-bc-sep">/</span>
    <a href="{{ route('operator.bank-soal.detail', $paket->id) }}" class="bs-bc-link truncate">{{ $paket->nama_paket }}</a>
    <span class="bs-bc-sep">/</span>
    <span class="bs-bc-current">Edit</span>
</div>


<form id="formEditSoal" method="POST" action="{{ route('operator.bank-soal.update', $paket->id) }}">
    @csrf
    @method('PUT')

    {{-- ── Header Actions ─────────────────────────────────────────── --}}
    <div class="glass animate-fade-in" style="border-radius: var(--r-xl); background: var(--surface); border: 1px solid var(--border); padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm);">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-display font-bold text-lg" style="color: var(--text-primary); margin: 0 0 0.2rem;">
                    Edit Paket Soal
                </h2>
                <p class="text-xs" style="color: var(--text-muted); margin: 0;">
                    Perubahan butir soal akan langsung berlaku untuk kegiatan sosialisasi mendatang.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('operator.bank-soal.detail', $paket->id) }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" id="btnSubmitEdit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden Tipe Paket & Durasi (Mempertahankan nilai sebelumnya atau default untuk kompatibilitas DB) --}}
    <input type="hidden" name="tipe" value="{{ old('tipe', $paket->tipe ?? 'umum') }}">
    <input type="hidden" name="durasi" value="{{ old('durasi', $paket->durasi ?? 30) }}">

    {{-- ── SECTION 1: Informasi Paket ─────────────────────────────── --}}
    <div class="glass mb-6 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs p-5 md:p-6">
        <div class="flex items-center gap-3 pb-3 mb-5 border-b border-slate-100 dark:border-slate-800">
            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center font-bold text-xs border border-blue-100 dark:border-blue-900/50">
                1
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100">
                    Informasi Umum Paket
                </h3>
                <p class="text-xs text-slate-400">
                    Tentukan nama paket dan tema materi instrumen evaluasi.
                </p>
            </div>
        </div>

        <div class="space-y-4">
            {{-- Row 1: Nama Paket Soal (Full Width) --}}
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1.5" for="nama_paket">
                    Nama Paket Soal <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_paket" id="nama_paket"
                    class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-2xs"
                    value="{{ old('nama_paket', $paket->nama_paket) }}" required
                    placeholder="Contoh: Pre-Test Anti Narkoba Pelajar SMA">
            </div>

            {{-- Row 2: Tema Sosialisasi --}}
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1.5" for="tema">
                    Tema Materi Sosialisasi
                </label>
                <div id="temaDropdownWrapper" class="relative w-full">
                    <input type="text" name="tema" id="tema"
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-10 shadow-2xs"
                        value="{{ old('tema', $paket->tema ?? '') }}" maxlength="100" autocomplete="off"
                        placeholder="cth: P4GN Pelajar, Ketahanan Keluarga, Lingkungan Kerja">
                    <button type="button" id="btnToggleTemaSuggestions"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1"
                        title="Tampilkan daftar saran tema">
                        <svg id="temaCaret" class="w-4 h-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Floating Suggestions Card --}}
                    <div id="temaSuggestionsMenu"
                         class="hidden absolute left-0 right-0 z-50 mt-1.5 max-h-60 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl text-xs py-1 divide-y divide-slate-100/70 dark:divide-slate-800/70">
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
                <p class="text-[11px] text-slate-400 mt-1">Bebas ketik baru — atau klik panah untuk memilih tema yang sudah terdaftar.</p>
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
                    Pilih radio button di samping opsi jawaban untuk menentukan kunci jawaban yang benar.
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
            @foreach ($soalList as $qIdx => $soal)
            <div class="bs-edit-card" data-index="{{ $qIdx }}">
                {{-- Question Card Header --}}
                <div class="flex items-center justify-between mb-3 pb-2" style="border-bottom: 1px dashed var(--border);">
                    <div class="flex items-center gap-2">
                        <span class="bs-q-num-edit">#{{ $qIdx + 1 }}</span>
                        <span class="text-xs font-semibold" style="color: var(--text-secondary);">Pertanyaan Pilihan Ganda</span>
                    </div>
                    <button type="button" class="bs-btn-del-item" title="Hapus butir pertanyaan ini" onclick="removeQuestionBlock(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                {{-- Pertanyaan Text --}}
                <div class="form-group mb-3">
                    <label class="form-label text-xs">Teks Pertanyaan</label>
                    <textarea name="soal[{{ $qIdx }}][pertanyaan]" class="form-input" rows="2" placeholder="Tuliskan butir pertanyaan..." required>{{ $soal->pertanyaan }}</textarea>
                </div>

                {{-- Options A, B, C, D --}}
                <div class="form-group mb-0">
                    <label class="form-label text-xs mb-1.5">Opsi Pilihan Jawaban &amp; Kunci Jawaban</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                        @foreach (['A', 'B', 'C', 'D'] as $opt)
                        @php $isKey = ($soal->kunci === $opt); @endphp
                        <div class="bs-edit-option-row {{ $isKey ? 'is-selected-key' : '' }}">
                            <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                                <input type="radio" name="soal[{{ $qIdx }}][kunci]" value="{{ $opt }}" {{ $isKey ? 'checked' : '' }} onchange="onKeyChange(this)">
                                <span class="bs-opt-letter">{{ $opt }}</span>
                            </label>
                            <input type="text" name="soal[{{ $qIdx }}][opsi][{{ $opt }}]" class="form-input bs-opt-input"
                                value="{{ $soal->opsi[$opt] ?? '' }}" placeholder="Opsi jawaban {{ $opt }}" required>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
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
        <a href="{{ route('operator.bank-soal.detail', $paket->id) }}" class="btn btn-secondary">
            Batal
        </a>
        <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Simpan Perubahan</span>
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
.bs-btn-del-item {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0.35rem;
    border-radius: var(--r-sm);
    color: #ef4444;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}
.bs-btn-del-item:hover {
    background: rgba(239, 68, 68, 0.12) !important;
    color: #dc2626 !important;
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
            <button type="button" class="bs-btn-del-item" title="Hapus butir pertanyaan ini" onclick="removeQuestionBlock(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

        <div class="form-group mb-3">
            <label class="form-label text-xs">Teks Pertanyaan</label>
            <textarea name="soal[${newIdx}][pertanyaan]" class="form-input" rows="2" placeholder="Tuliskan butir pertanyaan..." required></textarea>
        </div>

        <div class="form-group mb-0">
            <label class="form-label text-xs mb-1.5">Opsi Pilihan Jawaban &amp; Kunci Jawaban</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                <div class="bs-edit-option-row is-selected-key">
                    <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="A" checked onchange="onKeyChange(this)">
                        <span class="bs-opt-letter">A</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][A]" class="form-input bs-opt-input" placeholder="Opsi jawaban A" required>
                </div>
                <div class="bs-edit-option-row">
                    <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="B" onchange="onKeyChange(this)">
                        <span class="bs-opt-letter">B</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][B]" class="form-input bs-opt-input" placeholder="Opsi jawaban B" required>
                </div>
                <div class="bs-edit-option-row">
                    <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="C" onchange="onKeyChange(this)">
                        <span class="bs-opt-letter">C</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][C]" class="form-input bs-opt-input" placeholder="Opsi jawaban C" required>
                </div>
                <div class="bs-edit-option-row">
                    <label class="bs-radio-key" title="Jadikan Kunci Jawaban Benar">
                        <input type="radio" name="soal[${newIdx}][kunci]" value="D" onchange="onKeyChange(this)">
                        <span class="bs-opt-letter">D</span>
                    </label>
                    <input type="text" name="soal[${newIdx}][opsi][D]" class="form-input bs-opt-input" placeholder="Opsi jawaban D" required>
                </div>
            </div>
        </div>
    `;

    container.appendChild(div);
    div.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
