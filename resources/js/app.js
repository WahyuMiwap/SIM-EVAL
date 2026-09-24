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
import { guardAllForms } from './modules/submitGuard.js';
window.ApiHelper = ApiHelper;



// =============================================
//  INIT: HALAMAN UMUM (Sidebar, Topbar, etc.)
// =============================================
document.addEventListener('DOMContentLoaded', () => {
    guardAllForms();

    // --- Sidebar Mobile Toggle ---
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && !sidebarToggle.hasAttribute('onclick')) {
        sidebarToggle.addEventListener('click', (e) => {
            e.preventDefault();
            if (typeof window.toggleSidebarMobile === 'function') {
                window.toggleSidebarMobile();
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            if (typeof window.toggleSidebarMobile === 'function') {
                window.toggleSidebarMobile(false);
            }
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
    //  OPERATOR: OMR Workbench (lazy — hanya di Meja Kerja)
    // =============================================
    if (document.getElementById('btnOmrCamera')) {
        const kontrol = document.getElementById('kontrolSesi');
        const eventId = kontrol?.dataset.eventId || null;
        if (eventId) {
            import('./modules/OmrWorkbench.js')
                .then(m => m.default.init({ omrSubmitUrl: `/operator/kegiatan/${eventId}/omr-submit` }))
                .catch(() => {});
        }
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
//  JOIN PAGE — Kode Join Input Otomatis + Live QR Scanner
// ─────────────────────────────────────────────────────────────────────────────
function initJoinPage() {
    const codeInputs = document.querySelectorAll('.join-code-char');
    if (!codeInputs.length) return;

    const hiddenInput   = document.getElementById('kodeJoinHidden');
    const infoBox       = document.getElementById('eventInfoBox');
    const infoNama      = document.getElementById('eventInfoNama');
    const infoLok       = document.getElementById('eventInfoLokasi');
    const kodeError     = document.getElementById('kodeError');
    const kelompokLabel = document.getElementById('kelompokLabel');
    const pesertaNama   = document.getElementById('pesertaNama');
    let infoTimer = null;

    function updateCharState(input) {
        if (input.value && input.value.trim() !== '') {
            input.classList.add('has-value');
        } else {
            input.classList.remove('has-value');
        }
    }

    function syncHiddenInput() {
        if (hiddenInput) {
            hiddenInput.value = [...codeInputs].map(ci => ci.value.trim()).join('');
        }
    }

    async function lookupEvent() {
        const kode = [...codeInputs].map(ci => ci.value.trim()).join('');
        if (kode.length < 6) {
            infoBox?.classList.add('hidden');
            kodeError?.classList.add('hidden');
            return;
        }
        try {
            const res = await fetch(`/join/info?kode=${encodeURIComponent(kode)}`, {
                headers: { 'Accept': 'application/json' },
            });
            const json = await res.json();
            if (json.found) {
                if (infoNama) infoNama.textContent = json.nama_kegiatan;
                if (infoLok)  infoLok.textContent = json.lokasi;
                infoBox?.classList.remove('hidden');
                kodeError?.classList.add('hidden');
                if (kelompokLabel && json.kelompok_label) {
                    kelompokLabel.textContent = json.kelompok_label;
                }
            } else {
                infoBox?.classList.add('hidden');
                kodeError?.classList.remove('hidden');
            }
        } catch (e) {
            // offline / network error: fallback to server validation on submit
        }
    }

    function scheduleLookup() {
        clearTimeout(infoTimer);
        infoTimer = setTimeout(lookupEvent, 300);
    }

    function setFullCode(code) {
        const clean = (code || '').toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
        codeInputs.forEach((ci, idx) => {
            ci.value = clean[idx] || '';
            updateCharState(ci);
        });
        syncHiddenInput();
        scheduleLookup();
        if (clean.length >= 6) {
            pesertaNama?.focus();
        } else if (clean.length > 0) {
            codeInputs[Math.min(clean.length, codeInputs.length - 1)].focus();
        }
    }

    // Input keyboard navigation & auto-jump
    codeInputs.forEach((input, i) => {
        updateCharState(input);

        input.addEventListener('focus', () => {
            input.select();
        });

        input.addEventListener('input', () => {
            input.value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            updateCharState(input);
            syncHiddenInput();
            scheduleLookup();

            if (input.value && i < codeInputs.length - 1) {
                codeInputs[i + 1].focus();
            } else if (input.value && i === codeInputs.length - 1) {
                // If last box is filled and all 6 are filled, focus nama
                const fullCode = [...codeInputs].map(ci => ci.value).join('');
                if (fullCode.length === 6 && pesertaNama && !pesertaNama.value) {
                    setTimeout(() => pesertaNama?.focus(), 350);
                }
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace') {
                if (!input.value && i > 0) {
                    e.preventDefault();
                    codeInputs[i - 1].value = '';
                    updateCharState(codeInputs[i - 1]);
                    codeInputs[i - 1].focus();
                    syncHiddenInput();
                    scheduleLookup();
                } else if (input.value) {
                    input.value = '';
                    updateCharState(input);
                    syncHiddenInput();
                    scheduleLookup();
                    e.preventDefault();
                }
            } else if (e.key === 'ArrowLeft' && i > 0) {
                e.preventDefault();
                codeInputs[i - 1].focus();
            } else if (e.key === 'ArrowRight' && i < codeInputs.length - 1) {
                e.preventDefault();
                codeInputs[i + 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData.getData('text') ?? '');
            setFullCode(paste);
        });
    });

    // Check query params (?kode=...) on load or pre-filled hiddenInput
    const urlParams = new URLSearchParams(window.location.search);
    const paramKode = urlParams.get('kode') || hiddenInput?.value || '';
    if (paramKode && paramKode.trim().length > 0) {
        setFullCode(paramKode);
    } else {
        // Auto focus first input on desktop / non-touch if empty
        if (window.innerWidth >= 768 && codeInputs[0]) {
            codeInputs[0].focus();
        }
    }

    // Live Camera QR Scanner
    let qrStream = null;
    let qrTimer = null;
    const qrContainer = document.getElementById('qrScannerContainer');
    const qrVideo     = document.getElementById('qrVideo');
    const btnScanQR   = document.getElementById('btnScanQR');
    const btnStopQR   = document.getElementById('btnStopQR');

    async function startQRScanner() {
        if (!navigator.mediaDevices?.getUserMedia) {
            window.toast?.('Fitur pemindai kamera tidak didukung pada peramban ini.', 'error');
            return;
        }

        try {
            qrContainer?.classList.remove('hidden');
            qrStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
            });

            if (qrVideo) {
                qrVideo.srcObject = qrStream;
                await qrVideo.play();
            }

            // Real-time detection via native BarcodeDetector if available
            if ('BarcodeDetector' in window) {
                const detector = new BarcodeDetector({ formats: ['qr_code'] });
                qrTimer = setInterval(async () => {
                    if (!qrVideo || qrVideo.readyState < 2) return;
                    try {
                        const barcodes = await detector.detect(qrVideo);
                        if (barcodes.length > 0) {
                            const raw = barcodes[0].rawValue || '';
                            let parsed = '';
                            try {
                                const u = new URL(raw);
                                parsed = u.searchParams.get('kode') || '';
                            } catch (_) {
                                parsed = raw.trim();
                            }
                            parsed = parsed.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
                            if (parsed.length === 6) {
                                stopQRScanner();
                                setFullCode(parsed);
                            }
                        }
                    } catch (_) {}
                }, 300);
            }
        } catch (err) {
            if (import.meta.env?.DEV) console.warn('QR Scanner Error:', err);
            stopQRScanner();
            window.toast?.('Tidak dapat mengaktifkan kamera. Pastikan izin kamera telah disetujui.', 'error');
        }
    }

    function stopQRScanner() {
        if (qrTimer) {
            clearInterval(qrTimer);
            qrTimer = null;
        }
        if (qrStream) {
            qrStream.getTracks().forEach(track => track.stop());
            qrStream = null;
        }
        if (qrVideo) {
            qrVideo.srcObject = null;
        }
        qrContainer?.classList.add('hidden');
    }

    btnScanQR?.addEventListener('click', startQRScanner);
    btnStopQR?.addEventListener('click', stopQRScanner);
    window.addEventListener('beforeunload', stopQRScanner);
}

// ─────────────────────────────────────────────────────────────────────────────
//  WAITING ROOM — Polling Status Sesi
// ─────────────────────────────────────────────────────────────────────────────
function initWaitingRoom() {
    const trigger  = document.getElementById('sessionStatusTrigger');
    if (!trigger) return;

    const pollUrl     = document.getElementById('waitingRoom')?.dataset.pollUrl;
    const pollRoom    = document.getElementById('waitingRoom')?.dataset.room;
    const redirectUrl = trigger.dataset.redirectUrl;

    const poll = async () => {
        const url = pollRoom ? `${pollUrl}?room=${encodeURIComponent(pollRoom)}` : pollUrl;
        const result = await ApiHelper.get(url);
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
//  QR CODE GENERATOR (Detail Kegiatan) — lokal offline-first, fallback CDN
// ─────────────────────────────────────────────────────────────────────────────
async function initQRCode() {
    const kodeJoin = document.querySelector('[data-kode]')?.dataset?.kode;
    if (!kodeJoin) return;
    const display = document.getElementById('qrCodeDisplay');
    const targetUrl = `${window.location.origin}/join?kode=${kodeJoin}`;
    try {
        const { default: QRCode } = await import('qrcode');
        const canvas = document.createElement('canvas');
        await QRCode.toCanvas(canvas, targetUrl, { width: 160, margin: 1, color: { dark: '#0a1628', light: '#f1f5f9' } });
        canvas.style.cssText = 'width:160px;height:160px;border-radius:8px;';
        if (display) { display.innerHTML = ''; display.appendChild(canvas); }
    } catch (e) {
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodeURIComponent(targetUrl)}&bgcolor=f1f5f9&color=0a1628&format=svg`;
        if (display) display.innerHTML = `<img src="${qrUrl}" alt="QR Code" style="width:160px;height:160px;border-radius:8px;" loading="lazy" onerror="this.outerHTML='<p class=&quot;text-xs&quot;>${kodeJoin}</p>'" />`;
    }

    // Copy kode join
    document.getElementById('btnCopyKode')?.addEventListener('click', () => {
        navigator.clipboard.writeText(kodeJoin).then(() => {
            const btn = document.getElementById('btnCopyKode');
            if (btn) { btn.textContent = '✓ Tersalin!'; setTimeout(() => { btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Salin Kode`; }, 2000); }
        });
    });
}
