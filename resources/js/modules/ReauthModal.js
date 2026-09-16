/**
 * ReauthModal.js — Modal Re-Autentikasi Password (FR-43)
 * Mengelola pop-up konfirmasi password staf/operator yang sedang login
 * sebelum mengeksekusi aksi penghapusan data (Kegiatan, Lokasi, Bank Soal).
 */

import ApiHelper from '../api/ApiHelper.js';

const ReauthModal = {
    _overlay    : null,
    _box        : null,
    _actionUrl  : null,
    _itemId     : null,

    /** Inisialisasi — panggil saat DOM ready */
    init() {
        this._overlay = document.getElementById('reauthModalOverlay');
        if (!this._overlay) return;

        this._box = document.getElementById('reauthModalBox');

        // Bind internal events
        document.getElementById('reauthCancelBtn')?.addEventListener('click', () => this.close());
        document.getElementById('reauthConfirmBtn')?.addEventListener('click', () => this._confirm());
        document.getElementById('reauthPasswordToggle')?.addEventListener('click', () => this._togglePassword());

        // Tutup ketika klik di luar box
        this._overlay.addEventListener('click', (e) => {
            if (e.target === this._overlay) this.close();
        });

        // Submit saat tekan Enter di input password
        document.getElementById('reauthPassword')?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') this._confirm();
        });
    },

    /**
     * Buka modal
     * @param {HTMLElement} triggerEl - elemen tombol hapus yang memicu modal
     */
    open(triggerEl) {
        if (!this._overlay) this.init();

        this._actionUrl = triggerEl.dataset.reauthAction;
        this._itemId    = triggerEl.dataset.reauthId;
        const label     = triggerEl.dataset.reauthLabel ?? 'item ini';

        // Set label item yang akan dihapus
        const labelEl = document.getElementById('reauthItemLabel');
        if (labelEl) labelEl.textContent = label;

        // Reset state
        const pwEl = document.getElementById('reauthPassword');
        if (pwEl) pwEl.value = '';
        this._hideError();

        // Set hidden fields
        const urlEl = document.getElementById('reauthActionUrl');
        if (urlEl) urlEl.value = this._actionUrl;
        const idEl = document.getElementById('reauthItemId');
        if (idEl) idEl.value = this._itemId;

        // Tampilkan modal
        this._overlay?.classList.add('active');
        setTimeout(() => document.getElementById('reauthPassword')?.focus(), 300);
    },

    /** Tutup modal */
    close() {
        this._overlay?.classList.remove('active');
        this._hideError();
        const pwEl = document.getElementById('reauthPassword');
        if (pwEl) pwEl.value = '';
    },

    /** Konfirmasi — kirim ke ApiHelper.delete (FR-43) */
    async _confirm() {
        const password = document.getElementById('reauthPassword')?.value?.trim();
        if (!password) {
            this._showError('Password tidak boleh kosong.');
            return;
        }

        // Loading state
        const confirmBtn = document.getElementById('reauthConfirmBtn');
        const spinner    = document.getElementById('reauthSpinnerIcon');
        if (confirmBtn) { confirmBtn.disabled = true; confirmBtn.textContent = 'Memproses...'; }
        if (spinner)    spinner.style.display = 'inline-block';

        // Kirim DELETE dengan password via ApiHelper (NFR-07 — semua aksi melewati helper)
        const result = await ApiHelper.delete(this._actionUrl, password);

        if (confirmBtn) { confirmBtn.disabled = false; confirmBtn.textContent = 'Hapus Data'; }
        if (spinner)    spinner.style.display = 'none';

        if (result?.error) {
            // Password salah atau error server
            const errorMsg = result.status === 403
                ? 'Password salah. Coba lagi.'
                : (result.message ?? 'Terjadi kesalahan.');
            this._showError(errorMsg);
            this._shakeBox();
            return;
        }

        // Sukses — tutup modal dan hapus baris dari tabel
        this.close();
        this._removeRowFromTable(this._itemId);

        // Toast notif singkat
        this._showToast('Data berhasil dihapus.', 'success');
    },

    /** Hapus baris tabel dengan id terkait (tanpa reload halaman) */
    _removeRowFromTable(itemId) {
        // Cari tombol hapus dengan data-reauth-id yang sesuai
        const btn = document.querySelector(`[data-reauth-id="${itemId}"]`);
        if (btn) {
            const row = btn.closest('tr');
            if (row) {
                row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                row.style.opacity    = '0';
                row.style.transform  = 'translateX(20px)';
                setTimeout(() => row.remove(), 350);
            }
        }
    },

    _showError(msg) {
        const el = document.getElementById('reauthError');
        if (el) { el.textContent = msg; el.classList.remove('hidden'); }
    },

    _hideError() {
        const el = document.getElementById('reauthError');
        if (el) el.classList.add('hidden');
    },

    _shakeBox() {
        const box = document.getElementById('reauthModalBox');
        if (!box) return;
        box.classList.add('shake');
        setTimeout(() => box.classList.remove('shake'), 600);
    },

    _togglePassword() {
        const pw   = document.getElementById('reauthPassword');
        const icon = document.getElementById('eyeIcon');
        if (!pw) return;
        const isHidden = pw.type === 'password';
        pw.type = isHidden ? 'text' : 'password';
        if (icon) {
            icon.innerHTML = isHidden
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    },

    /** Toast notification sederhana */
    _showToast(message, type = 'success') {
        const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-blue-600' };
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 z-[999] ${colors[type]} text-white text-sm font-medium px-5 py-3 rounded-xl shadow-xl flex items-center gap-2 animate-fade-in`;
        toast.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    },
};

// Expose ke global scope agar bisa dipanggil dari inline onclick di Blade
window.ReauthModal = ReauthModal;

export default ReauthModal;
