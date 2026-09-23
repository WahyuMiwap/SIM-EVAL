/**
 * OmrWorkbench.js — Alur pindai kamera LJK gabungan (Tahap OMR).
 * Kamera → jepret → cek blur → baca vision → REVIEW WAJIB → konfirmasi → simpan.
 * Tidak ada nilai yang tersimpan tanpa lewat layar review.
 */

import OmrCapture from './OmrCapture.js';

const OmrWorkbench = {
    _cfg: null,
    _canvas: null,       // citra jepretan terakhir
    _reading: null,      // hasil OmrVision.readSheet
    _libsLoading: null,

    init(cfg) {
        this._cfg = cfg;
        this._bind();
    },

    _bind() {
        document.getElementById('btnOmrCamera')?.addEventListener('click', () => this.toggleCamera());
        document.getElementById('btnOmrSnap')?.addEventListener('click', () => this.snapAndRead());
        document.getElementById('btnOmrConfirm')?.addEventListener('click', () => this.confirmAndSave());
        document.getElementById('btnOmrCancelReview')?.addEventListener('click', () => this.closeReview());
    },

    // ── Kamera ───────────────────────────────────────────────
    async toggleCamera() {
        const video = document.getElementById('omrVideo');
        const btn = document.getElementById('btnOmrCamera');
        const placeholder = document.getElementById('scannerPlaceholder');
        if (OmrCapture.active) {
            OmrCapture.stop();
            if (video) { video.classList.add('hidden'); video.srcObject = null; }
            placeholder?.classList.remove('hidden');
            if (btn) btn.querySelector('span:last-child').textContent = 'Nyalakan Kamera';
            return;
        }
        const ok = await OmrCapture.start(video);
        if (!ok) {
            alert('Kamera tidak dapat diakses. Izinkan akses kamera browser lalu coba lagi.');
            return;
        }
        video.classList.remove('hidden');
        placeholder?.classList.add('hidden');
        if (btn) btn.querySelector('span:last-child').textContent = 'Matikan Kamera';
    },

    // ── Jepret + baca ────────────────────────────────────────
    async snapAndRead() {
        const statusEl = document.getElementById('omrReadStatus');
        const setStatus = (t) => { if (statusEl) statusEl.textContent = t; };

        if (!OmrCapture.active) {
            setStatus('Nyalakan kamera dulu.');
            return;
        }
        const canvas = OmrCapture.snap();
        if (!canvas) { setStatus('Gagal menjepret. Coba lagi.'); return; }

        const sharp = OmrCapture.sharpness(canvas);
        if (sharp < 60) {
            setStatus('Foto buram — tahan HP stabil dan coba lagi.');
            if (typeof playOMRBeep === 'function') { /* tanpa beep gagal */ }
            return;
        }

        setStatus('Memuat pustaka vision (sekali saja)…');
        let OmrVision;
        try {
            OmrVision = (await import('./OmrVision.js')).default;
        } catch (e) {
            setStatus('Gagal memuat pustaka vision. Periksa koneksi lalu coba lagi.');
            return;
        }

        setStatus('Membaca lembar…');
        try {
            const count = parseInt(document.getElementById('omrStageCount')?.value, 10) || 10;
            const reading = await OmrVision.readSheet(canvas, count);
            this._canvas = canvas;
            this._reading = reading;
            this.openReview();
            setStatus('');
        } catch (e) {
            console.error(e);
            setStatus('Gagal membaca. Pastikan lembar penuh dalam bingkai dan cahaya cukup.');
        }
    },

    // ── Review wajib ─────────────────────────────────────────
    openReview() {
        const r = this._reading;
        if (!r) return;
        const box = document.getElementById('omrReview');
        const list = document.getElementById('omrReviewList');
        if (!box || !list) return;

        // Prefill stage dari QR bila ada
        const qrStage = (r.qr?.s || '').toLowerCase();
        const stageSel = document.getElementById('omrStage');
        if (stageSel && (qrStage === 'pre' || qrStage === 'post' || qrStage === 'pretest' || qrStage === 'posttest')) {
            stageSel.value = qrStage.startsWith('post') ? 'posttest' : 'pretest';
        }

        list.innerHTML = r.blocks.map((b, i) => this._reviewRow(b, i)).join('');
        list.querySelectorAll('[data-cycle]').forEach(btn => {
            btn.addEventListener('click', () => this._cycleAnswer(parseInt(btn.dataset.cycle, 10)));
        });

        const qrInfo = document.getElementById('omrQrInfo');
        if (qrInfo) {
            qrInfo.textContent = r.qr
                ? `QR: paket ${r.qr.p ?? '?'} · ${r.qr.s ?? '?'} · lembar ${r.qr.l ?? '?'}/${r.qr.t ?? '?'}${r.framed ? '' : ' · tanpa bingkai (akurasi menurun)'}`
                : 'QR tidak terbaca — pastikan stage Pre/Post dipilih manual.';
        }

        box.classList.remove('hidden');
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    },

    _reviewRow(b, i) {
        const conf = Math.round((b.confidence || 0) * 100);
        const badge = !b.jawaban
            ? '<span class="badge badge-gray">Kosong</span>'
            : b.flags.includes('GANDA')
                ? `<span class="badge badge-yellow">Ganda? ${b.jawaban} (${conf}%)</span>`
                : conf >= 45
                    ? `<span class="badge badge-green">${b.jawaban} (${conf}%)</span>`
                    : `<span class="badge badge-yellow">${b.jawaban || '?'} (${conf}%) cek</span>`;
        return `
        <div class="flex items-center gap-3 px-3 py-2 rounded-xl border border-slate-200/70 dark:border-slate-800" data-row="${i}">
            ${b.crop ? `<img src="${b.crop}" alt="Blok ${b.nomor}" class="w-16 h-10 object-cover rounded-lg border border-slate-200 dark:border-slate-700 flex-shrink-0">` : ''}
            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold flex-shrink-0">${b.nomor}</span>
            <button type="button" data-cycle="${i}" title="Klik untuk koreksi"
                    class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-300 text-sm font-black flex-shrink-0">${b.jawaban || '–'}</button>
            <span class="flex-1">${badge}</span>
        </div>`;
    },

    _cycleAnswer(i) {
        const b = this._reading?.blocks?.[i];
        if (!b) return;
        const order = [null, 'A', 'B', 'C', 'D'];
        const next = order[(order.indexOf(b.jawaban) + 1) % order.length];
        b.jawaban = next;
        b.flags = next ? ['KOREKSI-MANUAL'] : ['KOSONG'];
        b.confidence = next ? 1 : 0;
        const list = document.getElementById('omrReviewList');
        if (list) {
            const tmp = document.createElement('div');
            tmp.innerHTML = this._reviewRow(b, i);
            const old = list.querySelector(`[data-row="${i}"]`);
            if (old) old.replaceWith(tmp.firstElementChild);
            tmp.firstElementChild?.querySelector('[data-cycle]')?.addEventListener('click', () => this._cycleAnswer(i));
        }
    },

    closeReview() {
        document.getElementById('omrReview')?.classList.add('hidden');
    },

    // ── Konfirmasi + simpan ──────────────────────────────────
    async confirmAndSave() {
        const r = this._reading;
        const btn = document.getElementById('btnOmrConfirm');
        if (!r || btn?.disabled) return;

        const nameEl = document.getElementById('omrName');
        const kelasEl = document.getElementById('omrKelas');
        const stageEl = document.getElementById('omrStage');
        const nama = (nameEl?.value || '').trim();
        const kelas = (kelasEl?.value || '').trim();
        const stage = stageEl?.value === 'posttest' ? 'posttest' : 'pretest';
        if (!nama || !kelas) {
            alert('Isi nama dan kelas/kelompok siswa dulu.');
            (nama ? kelasEl : nameEl)?.focus();
            return;
        }

        btn.disabled = true;
        btn.querySelector('span:last-child').textContent = 'Menyimpan…';
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const res = await fetch(this._cfg.omrSubmitUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    nama, kelas, stage,
                    serial: r.qr?.serial ?? null,
                    min_confidence: Math.min(...r.blocks.map(b => b.confidence || 0)),
                    answers: r.blocks.map(b => ({ nomor: b.nomor, jawaban: b.jawaban })),
                }),
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok || json.success === false) throw new Error(json.message || 'Gagal menyimpan.');
            this.closeReview();
            if (typeof playOMRBeep === 'function') playOMRBeep();
            const feed = document.getElementById('omrConfirmNote');
            if (feed) feed.textContent = `Tersimpan: ${json.participant?.name ?? nama} — skor ${json.score ?? '—'}.`;
            // Segarkan daftar riwayat bila ada fungsi tabelnya
            if (typeof window.refreshOmrFeed === 'function') window.refreshOmrFeed();
            else window.location.reload();
        } catch (e) {
            alert(e.message);
        } finally {
            btn.disabled = false;
            btn.querySelector('span:last-child').textContent = 'Konfirmasi & Simpan';
        }
    },
};

export default OmrWorkbench;
