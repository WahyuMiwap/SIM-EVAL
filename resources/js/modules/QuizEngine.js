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
    _submitting : false, // kunci anti double-submit
    _reviewTimer: null,  // interval hitung mundur modal review
    _reviewLeft : 0,

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

        document.getElementById('btnSubmitQuiz')?.addEventListener('click', () => this._requestSubmit());
        document.getElementById('btnSubmitWarning')?.addEventListener('click', () => this._requestSubmit());
        // btnReviewBack di-bind sekali di _startReviewCountdown (butuh data kosong terkini)
        document.getElementById('btnReviewSend')?.addEventListener('click', () => this._doSubmit(false));

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
                'w-10 h-10 sm:w-11 sm:h-11 rounded-xl text-xs font-bold transition-all duration-150 flex items-center justify-center shrink-0',
                i === this._current
                    ? 'bg-blue-600 border border-blue-500 text-white shadow-sm ring-2 ring-blue-400/30'
                    : answered
                        ? 'bg-blue-50 dark:bg-blue-950/60 border border-blue-300 dark:border-blue-800 text-blue-600 dark:text-blue-400 font-semibold'
                        : 'bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-primary/50 hover:text-primary',
            ].join(' ');
        });

        // Update nomor aktif di header sidebar
        const sidebarActiveNum = document.getElementById('sidebarActiveNum');
        if (sidebarActiveNum) sidebarActiveNum.textContent = this._current + 1;

        // Auto-scroll tombol aktif ke viewport sidebar
        const currentBtn = document.getElementById(`navBtn${this._current}`);
        if (currentBtn) {
            currentBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    },

    /** Restore jawaban offline dari IndexedDB */
    async _restoreOfflineAnswers() {
        const saved = await OfflineStore.getAnswers(this._sessionId);
        saved.forEach(a => { this._answers[a.soalId] = a.jawaban; });
        if (saved.length > 0) this._renderSoal(this._current);
    },

    /** Auto-submit paksa (FR-24b): bypass modal review, kirim apa adanya */
    _autoSubmit() {
        TimerManager.stop();
        const warningOverlay = document.getElementById('warningTimerOverlay');
        if (warningOverlay) warningOverlay.classList.remove('active');
        this._closeReview();
        this._doSubmit(true);
    },

    /** Tahap 1 (manual): buka modal review + jeda paksa 3 detik */
    _requestSubmit() {
        if (this._submitting) return;
        TimerManager.stop();

        const total = this._soal.length;
        const blankIdx = [];
        this._soal.forEach((s, i) => { if (!this._answers[s.id]) blankIdx.push(i + 1); });
        const answered = total - blankIdx.length;

        const summary = document.getElementById('reviewSummary');
        if (summary) summary.textContent = `Terjawab ${answered} dari ${total} soal.`;
        const note = document.getElementById('reviewBlankNote');
        if (note) {
            if (blankIdx.length) {
                note.textContent = `Belum dijawab: No. ${blankIdx.join(', ')} (dihitung salah).`;
                note.classList.remove('hidden');
            } else {
                note.classList.add('hidden');
            }
        }

        const overlay = document.getElementById('reviewOverlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        }

        // Jeda paksa: hitung ulang dari 3 setiap kali modal dibuka
        this._startReviewCountdown();

        // Simpan untuk tombol Kembali (lompat ke kosong pertama bila ada)
        this._reviewBlankIdx = blankIdx;
    },

    /** Tahap 2: tutup modal, batalkan timer, buka kembali bila perlu */
    _closeReview() {
        if (this._reviewTimer) {
            clearInterval(this._reviewTimer);
            this._reviewTimer = null;
        }
        const overlay = document.getElementById('reviewOverlay');
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    },

    _startReviewCountdown() {
        if (this._reviewTimer) clearInterval(this._reviewTimer);
        const btn = document.getElementById('btnReviewSend');
        this._reviewLeft = 3;
        const paint = () => {
            if (!btn) return;
            if (this._reviewLeft > 0) {
                btn.disabled = true;
                btn.textContent = `Kirim (${this._reviewLeft})`;
                btn.classList.add('opacity-50', 'cursor-not-allowed', 'btn-primary');
            } else {
                btn.disabled = false;
                btn.textContent = 'Kirim Jawaban';
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.classList.add('btn-primary');
                if (this._reviewTimer) clearInterval(this._reviewTimer);
                this._reviewTimer = null;
            }
        };
        paint();
        this._reviewTimer = setInterval(() => {
            this._reviewLeft -= 1;
            if (this._reviewLeft <= 0) {
                this._reviewLeft = 0;
                paint();
                return;
            }
            paint();
        }, 1000);

        // Kembali ke Soal: tutup + lompat ke kosong pertama (bila ada)
        const back = document.getElementById('btnReviewBack');
        if (back && !back.dataset.bound) {
            back.dataset.bound = '1';
            back.addEventListener('click', () => {
                const first = (this._reviewBlankIdx && this._reviewBlankIdx[0]) || null;
                this._closeReview();
                if (first) this._renderSoal(first - 1);
            });
        }
    },

    /** Tahap 2 (aktual POST): dijaga anti double-submit */
    async _doSubmit(isAutoSubmit = false) {
        if (this._submitting) return;
        this._submitting = true;
        this._closeReview();
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
