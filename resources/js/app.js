/**
 * app.js — Entry Point JavaScript SIM-EVAL (NFR-07)
 * Menginisialisasi modul sesuai halaman yang aktif.
 * Mengikuti Multi-Layer Architecture: Blade View → JS Modules → ApiHelper.
 */

import '../css/app.css';

import Chart from 'chart.js/auto';
window.Chart = Chart;

import ReauthModal   from './modules/ReauthModal.js';
import QuizEngine    from './modules/QuizEngine.js';
import ApiHelper     from './api/ApiHelper.js';

// =============================================
//  INIT: HALAMAN UMUM (Sidebar, Topbar, etc.)
// =============================================
document.addEventListener('DOMContentLoaded', () => {

    // --- Sidebar Mobile Toggle ---
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebar        = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            sidebarOverlay?.classList.toggle('hidden');
        });
        sidebarOverlay?.addEventListener('click', () => {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.add('hidden');
        });
    }

    // =============================================
    //  OPERATOR PANEL: Kegiatan Modal
    // =============================================
    if (document.getElementById('kegiatanModalOverlay')) {
        initKegiatanModal();
        ReauthModal.init();
    }

    // =============================================
    //  OPERATOR PANEL: Lokasi Modal
    // =============================================
    if (document.getElementById('lokasiModalOverlay')) {
        initLokasiModal();
    }

    // =============================================
    //  OPERATOR PANEL: Bank Soal Modal
    // =============================================
    if (document.getElementById('soalModalOverlay')) {
        initBankSoalModal();
    }

    // =============================================
    //  PARTICIPANT: Welcome — Kode Join + QR
    // =============================================
    if (document.getElementById('joinForm')) {
        initJoinPage();
    }

    // =============================================
    //  PARTICIPANT: Waiting Room Polling
    // =============================================
    if (document.getElementById('waitingRoom')) {
        initWaitingRoom();
    }

    // =============================================
    //  PARTICIPANT: Quiz Engine
    // =============================================
    if (document.getElementById('quizWrapper')) {
        QuizEngine.init();
    }

    // =============================================
    //  DETAIL KEGIATAN: QR Code + Copy
    // =============================================
    if (document.getElementById('qrCodeDisplay')) {
        initQRCode();
    }
});

// ─────────────────────────────────────────────────────────────────────────────
//  KEGIATAN MODAL (Tambah / Edit)
// ─────────────────────────────────────────────────────────────────────────────
function initKegiatanModal() {
    const overlay    = document.getElementById('kegiatanModalOverlay');
    const openBtns   = [document.getElementById('btnTambahKegiatan'), document.getElementById('btnTambahKegiatanEmpty')];
    const closeBtns  = [document.getElementById('kegiatanModalClose'), document.getElementById('kegiatanModalCancelBtn')];
    const form       = document.getElementById('kegiatanForm');
    const titleEl    = document.getElementById('kegiatanModalTitle');

    openBtns.forEach(btn => btn?.addEventListener('click', () => {
        form?.reset();
        document.getElementById('kegiatanFormMethod').value = 'POST';
        if (titleEl) titleEl.textContent = 'Tambah Kegiatan';
        // Reset action ke store route
        if (form) form.action = form.dataset.storeUrl ?? form.action;
        window.resetLokasiCombobox?.();
        overlay?.classList.add('active');
    }));

    closeBtns.forEach(btn => btn?.addEventListener('click', () => overlay?.classList.remove('active')));
    overlay?.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.remove('active'); });

    // Expose editKegiatan ke global
    window.editKegiatan = async (id) => {
        const result = await ApiHelper.get(`/operator/kegiatan/${id}/edit`);
        if (result?.error) return;
        if (titleEl) titleEl.textContent = 'Edit Kegiatan';
        document.getElementById('kegiatanFormMethod').value = 'PUT';
        document.getElementById('kegiatanFormId').value = id;
        document.getElementById('f_nama').value    = result.nama_kegiatan ?? '';
        if (typeof window.setLokasiComboboxValue === 'function') {
            window.setLokasiComboboxValue(result.lokasi_id, result.lokasi?.nama_lokasi ?? '');
        }
        document.getElementById('f_tanggal').value = result.tanggal ?? '';
        document.getElementById('f_durasi').value  = result.durasi_menit ?? 30;
        document.getElementById('f_catatan').value = result.catatan ?? '';
        overlay?.classList.add('active');
    };

    // Btn Tambah Lokasi Inline (FR-01b)
    document.getElementById('btnAddLokasiInline')?.addEventListener('click', () => {
        const lokasiOverlay = document.getElementById('lokasiModalOverlay');
        if (lokasiOverlay) lokasiOverlay.classList.add('active');
    });

    // Tipe soal Post-Test auto-select saat Post-Test dipilih
    document.getElementById('fs_tipe')?.addEventListener('change', (e) => {
        const section = document.getElementById('samainPreTestSection');
        if (section) section.style.display = e.target.value === 'posttest' ? 'flex' : 'none';
    });

    // Salin soal Pre-Test (FR-28b)
    document.getElementById('btnCopyPreTest')?.addEventListener('click', async () => {
        const preId = document.getElementById('fs_copyPre')?.value;
        if (!preId) return;
        const result = await ApiHelper.get(`/operator/bank-soal/${preId}/soal`);
        if (result?.error) return;
        // Inject soal ke SoalBuilder di form
        if (typeof SoalBuilder !== 'undefined') {
            SoalBuilder.loadFromData(result.soal);
        }
    });
}

// ─────────────────────────────────────────────────────────────────────────────
//  LOKASI MODAL
// ─────────────────────────────────────────────────────────────────────────────
function initLokasiModal() {
    const overlay   = document.getElementById('lokasiModalOverlay');
    const openBtns  = [document.getElementById('btnTambahLokasi'), document.getElementById('btnTambahLokasiEmpty')];
    const closeBtns = [document.getElementById('lokasiModalClose'), document.getElementById('lokasiModalCancelBtn')];

    openBtns.forEach(btn => btn?.addEventListener('click', () => {
        document.getElementById('lokasiForm')?.reset();
        document.getElementById('lokasiFormMethod').value = 'POST';
        document.getElementById('lokasiModalTitle').textContent = 'Tambah Lokasi';
        overlay?.classList.add('active');
    }));

    closeBtns.forEach(btn => btn?.addEventListener('click', () => overlay?.classList.remove('active')));
    overlay?.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('active'); });

    ReauthModal.init();

    window.editLokasi = async (id) => {
        const result = await ApiHelper.get(`/operator/lokasi/${id}/edit`);
        if (result?.error) return;
        document.getElementById('lokasiFormMethod').value = 'PUT';
        document.getElementById('lokasiFormId').value = id;
        document.getElementById('fl_nama').value      = result.nama_lokasi ?? '';
        document.getElementById('fl_alamat').value    = result.alamat ?? '';
        document.getElementById('fl_kecamatan').value = result.kecamatan ?? '';
        document.getElementById('fl_jenis').value     = result.jenis_sasaran ?? 'sekolah';
        document.getElementById('lokasiModalTitle').textContent = 'Edit Lokasi';
        overlay?.classList.add('active');
    };

    // Filter tabel real-time
    document.getElementById('filterLokasi')?.addEventListener('input', (e) => {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('#lokasiTable tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    });
}

// ─────────────────────────────────────────────────────────────────────────────
//  BANK SOAL MODAL
// ─────────────────────────────────────────────────────────────────────────────
function initBankSoalModal() {
    const overlay   = document.getElementById('soalModalOverlay');
    ReauthModal.init();
    if (!overlay) return;

    const openBtn   = document.getElementById('btnTambahSoal');
    const closeBtns = [document.getElementById('soalModalClose'), document.getElementById('soalModalCancelBtn')];

    openBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('soalForm')?.reset();
        document.getElementById('soalFormMethod').value = 'POST';
        document.getElementById('soalModalTitle').textContent = 'Tambah Paket Soal';
        document.getElementById('samainPreTestSection').style.display = 'none';
        overlay?.classList.add('active');
    });

    closeBtns.forEach(btn => btn?.addEventListener('click', () => overlay?.classList.remove('active')));
    overlay?.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('active'); });

    window.editSoal = async (id) => {
        const result = await ApiHelper.get(`/operator/bank-soal/${id}/edit`);
        if (result?.error) return;
        document.getElementById('soalFormMethod').value = 'PUT';
        document.getElementById('soalFormId').value = id;
        document.getElementById('fs_nama').value    = result.nama_paket ?? '';
        document.getElementById('fs_tipe').value    = result.tipe ?? 'pretest';
        document.getElementById('fs_durasi').value  = result.durasi ?? 30;
        document.getElementById('fs_acak').checked  = !!result.acak_urutan;
        document.getElementById('soalModalTitle').textContent = 'Edit Paket Soal';
        overlay?.classList.add('active');
    };
}

// ─────────────────────────────────────────────────────────────────────────────
//  JOIN PAGE — Kode Join Input Otomatis + QR
// ─────────────────────────────────────────────────────────────────────────────
function initJoinPage() {
    // Auto focus & move antar input kode join
    const codeInputs = document.querySelectorAll('.join-code-char');
    const hiddenInput = document.getElementById('kodeJoinHidden');

    codeInputs.forEach((input, i) => {
        input.addEventListener('input', () => {
            input.value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            if (input.value && i < codeInputs.length - 1) codeInputs[i + 1].focus();
            // Gabungkan ke hidden input
            if (hiddenInput) hiddenInput.value = [...codeInputs].map(ci => ci.value).join('');
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && i > 0) codeInputs[i - 1].focus();
        });
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData.getData('text') ?? '').toUpperCase().replace(/[^A-Z0-9]/g, '');
            [...codeInputs].forEach((ci, idx) => { ci.value = paste[idx] ?? ''; });
            if (hiddenInput) hiddenInput.value = paste.slice(0, 6);
            codeInputs[Math.min(paste.length, codeInputs.length - 1)].focus();
        });
    });

    // QR Scanner toggle
    document.getElementById('btnScanQR')?.addEventListener('click', () => {
        document.getElementById('qrScannerContainer')?.classList.remove('hidden');
    });
    document.getElementById('btnStopQR')?.addEventListener('click', () => {
        document.getElementById('qrScannerContainer')?.classList.add('hidden');
    });
}

// ─────────────────────────────────────────────────────────────────────────────
//  WAITING ROOM — Polling Status Sesi
// ─────────────────────────────────────────────────────────────────────────────
function initWaitingRoom() {
    const trigger  = document.getElementById('sessionStatusTrigger');
    if (!trigger) return;

    const pollUrl     = document.getElementById('waitingRoom')?.dataset.pollUrl;
    const redirectUrl = trigger.dataset.redirectUrl;

    const poll = async () => {
        const result = await ApiHelper.get(pollUrl);
        if (!result?.error && result?.status_changed) {
            window.location.href = redirectUrl;
            return;
        }
        // Update jumlah peserta yang bergabung
        if (result?.waiting_count !== undefined) {
            const el = document.getElementById('waitingCount');
            if (el) el.textContent = `${result.waiting_count} bergabung`;
        }
    };

    // Poll setiap 3 detik
    setInterval(poll, 3000);
}

// ─────────────────────────────────────────────────────────────────────────────
//  QR CODE GENERATOR (Detail Kegiatan)
// ─────────────────────────────────────────────────────────────────────────────
function initQRCode() {
    const kodeJoin = document.querySelector('[data-kode]')?.dataset?.kode;
    if (!kodeJoin) return;

    // Generate QR via Google Charts API (no external lib needed)
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodeURIComponent(window.location.origin + '/join?kode=' + kodeJoin)}&bgcolor=f1f5f9&color=0a1628&format=svg`;
    const display = document.getElementById('qrCodeDisplay');
    if (display) {
        display.innerHTML = `<img src="${qrUrl}" alt="QR Code" style="width:160px;height:160px;border-radius:8px;" />`;
    }

    // Copy kode join
    document.getElementById('btnCopyKode')?.addEventListener('click', () => {
        navigator.clipboard.writeText(kodeJoin).then(() => {
            const btn = document.getElementById('btnCopyKode');
            if (btn) { btn.textContent = '✓ Tersalin!'; setTimeout(() => { btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Salin Kode`; }, 2000); }
        });
    });
}
