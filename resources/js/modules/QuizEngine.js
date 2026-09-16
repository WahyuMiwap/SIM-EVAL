/**
 * QuizEngine.js — Logika Kuis Peserta (FR-24c)
 * Mengelola pengacakan soal per peserta (mode online),
 * navigasi soal, pencatatan jawaban, dan submit ke ApiHelper.
 */

import ApiHelper from '../api/ApiHelper.js';
import TimerManager from './TimerManager.js';
import { OfflineStore } from './OfflineStore.js';

const QuizEngine = {
    _soal       : [],    // Array soal sesuai urutan tampil (sudah diacak)
    _answers    : {},    // { [soalId]: 'A'|'B'|'C'|'D' }
    _current    : 0,     // Index soal yang sedang ditampilkan
    _sessionId  : null,
    _quizType   : null,  // 'pretest' | 'posttest'
    _submitUrl  : null,

    /**
     * Inisialisasi Quiz Engine dari data JSON di halaman
     */
    init() {
        const dataEl = document.getElementById('quizData');
        if (!dataEl) return;

        const config = JSON.parse(dataEl.textContent);
        this._sessionId = config.sessionId;
        this._quizType  = config.quizType;
        this._submitUrl = config.submitUrl;

        // FR-24c: Acak urutan soal per peserta (seed berdasarkan sessionId agar konsisten jika reload)
        this._soal = this._shuffleWithSeed(config.soal, this._sessionId);

        // Restore jawaban offline jika ada
        this._restoreOfflineAnswers();

        // Render soal pertama
        this._renderSoal(0);

        // Mulai timer
        TimerManager.start(config.durasi, {
            onWarning: () => { /* Warning modal sudah ditangani TimerManager */ },
            onExpired: () => this._autoSubmit(),
        });

        // Bind navigasi
        this._bindNavigation();

        // Update total
        const totalEl = document.getElementById('totalSoalNum');
        if (totalEl) totalEl.textContent = this._soal.length;
    },

    /**
     * Fisher-Yates shuffle dengan seed (deterministik per peserta)
     * Soal yang sama di layar berbeda = urutan berbeda (FR-24c)
     */
    _shuffleWithSeed(arr, seed) {
        const copy = [...arr];
        let s = this._hashSeed(String(seed));
        for (let i = copy.length - 1; i > 0; i--) {
            s = (s * 1664525 + 1013904223) & 0xffffffff;
            const j = Math.abs(s) % (i + 1);
            [copy[i], copy[j]] = [copy[j], copy[i]];
        }
        return copy;
    },

    _hashSeed(str) {
        let h = 0;
        for (let i = 0; i < str.length; i++) {
            h = (Math.imul(31, h) + str.charCodeAt(i)) | 0;
        }
        return h;
    },

    /** Render soal ke DOM */
    _renderSoal(index) {
        const soal = this._soal[index];
        if (!soal) return;
        this._current = index;

        // Animasi transisi
        const card = document.getElementById('questionCard');
        if (card) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(8px)';
            setTimeout(() => {
                card.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }

        // Nomor soal (tampilkan nomor asli dari soal, bukan index shuffle)
        const numEl = document.getElementById('questionNumber');
        if (numEl) numEl.textContent = index + 1;

        const currentEl = document.getElementById('currentSoalNum');
        if (currentEl) currentEl.textContent = index + 1;

        // Teks pertanyaan
        const textEl = document.getElementById('questionText');
        if (textEl) textEl.textContent = soal.pertanyaan;

        // Opsi jawaban (A, B, C, D — tidak diacak, FR-24c)
        ['A', 'B', 'C', 'D'].forEach(opt => {
            const btn = document.getElementById(`option${opt}`);
            if (!btn) return;
            const textSpan = btn.querySelector('.option-text');
            if (textSpan) textSpan.textContent = soal[`opsi_${opt.toLowerCase()}`] ?? '';

            // Tandai jawaban yang sudah dipilih
            btn.classList.toggle('selected', this._answers[soal.id] === opt);
            btn.onclick = () => this._selectAnswer(soal.id, opt);
        });

        // Update nav grid
        this._updateNavGrid();
        this._updateNavButtons();

        // Tampilkan/sembunyikan tombol Submit di soal terakhir
        const submitSection = document.getElementById('submitSection');
        if (submitSection) {
            submitSection.classList.toggle('hidden', index < this._soal.length - 1);
        }

        // Update progress
        const answered = Object.keys(this._answers).length;
        const progressFill = document.getElementById('progressFill');
        if (progressFill) {
            progressFill.style.width = `${(answered / this._soal.length) * 100}%`;
        }
        const answeredEl = document.getElementById('answeredCount');
        if (answeredEl) answeredEl.textContent = `${answered} terjawab`;
    },

    /** Pilih jawaban */
    _selectAnswer(soalId, option) {
        this._answers[soalId] = option;

        // Simpan ke IndexedDB sebagai backup offline (NFR-04)
        if (!navigator.onLine) {
            OfflineStore.saveAnswer(this._sessionId, soalId, option, this._quizType);
        }

        this._renderSoal(this._current);
    },

    /** Bind tombol Next / Prev / Nav Grid */
    _bindNavigation() {
        document.getElementById('btnNext')?.addEventListener('click', () => {
            if (this._current < this._soal.length - 1) this._renderSoal(this._current + 1);
        });

        document.getElementById('btnPrev')?.addEventListener('click', () => {
            if (this._current > 0) this._renderSoal(this._current - 1);
        });

        document.getElementById('btnSubmitQuiz')?.addEventListener('click', () => this._submit());
        document.getElementById('btnSubmitWarning')?.addEventListener('click', () => this._submit());

        // Nav grid click
        document.querySelectorAll('[data-nav]').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.nav);
                this._renderSoal(idx);
            });
        });
    },

    _updateNavButtons() {
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        if (btnPrev) btnPrev.disabled = this._current === 0;
        if (btnNext) btnNext.style.display = this._current >= this._soal.length - 1 ? 'none' : 'inline-flex';
    },

    _updateNavGrid() {
        this._soal.forEach((soal, i) => {
            const btn = document.getElementById(`navBtn${i}`);
            if (!btn) return;
            const answered = !!this._answers[soal.id];
            btn.className = [
                'w-8 h-8 rounded-lg text-xs font-bold transition-all duration-150',
                i === this._current
                    ? 'bg-blue-600 border border-blue-500 text-white'
                    : answered
                        ? 'bg-blue-500/20 border border-blue-500/40 text-blue-300'
                        : 'bg-white/5 border border-white/8 text-slate-500 hover:border-blue-500/50 hover:text-slate-300',
            ].join(' ');
        });
    },

    /** Restore jawaban offline dari IndexedDB */
    async _restoreOfflineAnswers() {
        const saved = await OfflineStore.getAnswers(this._sessionId);
        saved.forEach(a => { this._answers[a.soalId] = a.jawaban; });
        if (saved.length > 0) this._renderSoal(this._current);
    },

    /** Auto-submit paksa (FR-24b) */
    _autoSubmit() {
        TimerManager.stop();
        const warningOverlay = document.getElementById('warningTimerOverlay');
        if (warningOverlay) warningOverlay.classList.remove('active');
        this._submit(true);
    },

    /** Submit jawaban via ApiHelper */
    async _submit(isAutoSubmit = false) {
        TimerManager.stop();

        const payload = {
            session_id  : this._sessionId,
            quiz_type   : this._quizType,
            answers     : this._answers,
            auto_submit : isAutoSubmit,
        };

        const result = await ApiHelper.post(this._submitUrl, payload, true /* offline fallback */);

        // Bersihkan offline store jika berhasil sync
        if (!result?.offline) {
            await OfflineStore.clearAnswers(this._sessionId);
        }

        // Tampilkan done screen
        const doneScreen = document.getElementById('doneScreen');
        if (doneScreen) {
            doneScreen.classList.remove('hidden');
            const scoreEl = document.getElementById('scoreDisplay');
            if (scoreEl) scoreEl.textContent = result?.score ?? '—';
        }

        // Jika pre-test, redirect ke waiting room post-test
        if (this._quizType === 'pretest' && !result?.offline) {
            setTimeout(() => {
                window.location.href = result?.next_url ?? '/waiting/posttest';
            }, 3500);
        }
    },
};

export default QuizEngine;
