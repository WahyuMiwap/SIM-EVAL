/**
 * TimerManager.js — Countdown Timer (FR-24b)
 * Mengelola timer hitung mundur, peringatan 15 detik terakhir,
 * dan auto-submit paksa saat waktu mencapai 00:00.
 */

const TimerManager = {
    _intervalId   : null,
    _remaining    : 0,      // detik tersisa
    _warningShown : false,
    _onTick       : null,   // callback setiap detik
    _onWarning    : null,   // callback saat 15 detik tersisa
    _onExpired    : null,   // callback saat waktu habis

    /**
     * Mulai timer
     * @param {number} durasiMenit - durasi dalam menit
     * @param {object} callbacks - { onTick, onWarning, onExpired }
     */
    start(durasiMenit, callbacks = {}) {
        this.stop(); // Hentikan timer sebelumnya jika ada
        this._remaining    = durasiMenit * 60;
        this._warningShown = false;
        this._onTick       = callbacks.onTick    ?? null;
        this._onWarning    = callbacks.onWarning ?? null;
        this._onExpired    = callbacks.onExpired ?? null;

        this._intervalId = setInterval(() => this._tick(), 1000);
        this._tick(); // Langsung render pertama kali
    },

    /** Hentikan timer */
    stop() {
        if (this._intervalId) {
            clearInterval(this._intervalId);
            this._intervalId = null;
        }
    },

    /** Sisa waktu dalam detik */
    getRemaining() {
        return this._remaining;
    },

    /** Format waktu ke MM:SS */
    _format(seconds) {
        const m = Math.floor(seconds / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    },

    _tick() {
        if (this._remaining <= 0) {
            this.stop();
            this._onExpired?.();
            return;
        }

        // Update timer display
        const formatted = this._format(this._remaining);
        const el = document.getElementById('timerDisplay');
        if (el) {
            el.textContent = formatted;
            el.className = 'timer-display';
            if (this._remaining <= 15)  el.classList.add('danger');
            else if (this._remaining <= 60) el.classList.add('warning');
        }

        // Warning di 15 detik (FR-24b)
        if (this._remaining === 15 && !this._warningShown) {
            this._warningShown = true;
            this._onWarning?.(this._remaining);
            this._showWarningModal();
        }

        // Callback tick
        this._onTick?.(this._remaining, formatted);

        this._remaining--;
    },

    /** Tampilkan warning modal saat 15 detik tersisa */
    _showWarningModal() {
        const overlay = document.getElementById('warningTimerOverlay');
        if (!overlay) return;
        overlay.classList.add('active');

        let countdown = 15;
        const countdownEl = document.getElementById('warningCountdown');
        const progressEl  = document.getElementById('warningProgressBar');

        const warningInterval = setInterval(() => {
            countdown--;
            if (countdownEl) countdownEl.textContent = countdown;
            if (progressEl)  progressEl.style.width = `${(countdown / 15) * 100}%`;

            if (countdown <= 0) {
                clearInterval(warningInterval);
                overlay.classList.remove('active');
            }
        }, 1000);
    },
};

export default TimerManager;
