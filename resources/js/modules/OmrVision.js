/**
 * OmrVision.js — Pembaca LJK gabungan via kamera (Tahap OMR).
 * - Lazy-load OpenCV.js + jsQR hanya saat dipakai (bundle utama tak membengkak).
 * - Deteksi bingkai per blok soal → argmax gelap 4 kotak → confidence margin.
 * - Disetel untuk silang PENSIL/BOLPOIN: ambang relatif, bukan absolut.
 * - Ambang di bawah ini nilai awal — WAJIB ditune dari lembar uji nyata (F5).
 */

export const OMR_TUNE = {
    CONF_HIGH: 0.45,   // margin >= ini → otomatis
    CONF_LOW: 0.12,    // margin < ini → tolak / minta isi manual
    DARK_FLOOR: 0.06,  // fill di bawah ini = kosong (pensil tipis tetap lolos via margin)
    DOUBLE_MARGIN: 0.08, // selisih juara 1-2 di bawah ini = ganda
    WARP_W: 794,       // kanvas standar rasio A4
    WARP_H: 1123,
};

const OmrVision = {
    _cvPromise: null,
    _jsqr: null,

    async ensureLibs() {
        if (!this._cvPromise) {
            this._cvPromise = (async () => {
                const mod = await import('@techstark/opencv-js');
                const cv = mod.default ?? mod;
                // Tunggu runtime WASM siap
                if (cv.then) await cv;
                else if (cv.ready) await cv.ready;
                else await new Promise(res => {
                    if (cv.Mat) return res();
                    cv.onRuntimeInitialized = res;
                    setTimeout(res, 15000); // pengaman
                });
                return cv;
            })();
        }
        if (!this._jsqr) {
            const mod = await import('jsqr');
            this._jsqr = mod.default ?? mod;
        }
        const [cv] = await Promise.all([this._cvPromise]);
        return { cv, jsQR: this._jsqr };
    },

    /**
     * Decode QR dari kanvas (upright; capture terpandu).
     * @returns {object|null} payload JSON atau {raw}
     */
    async decodeQR(canvas) {
        try {
            const { jsQR } = await this.ensureLibs();
            const scale = Math.min(1, 800 / Math.max(canvas.width, canvas.height));
            const w = Math.max(1, Math.round(canvas.width * scale));
            const h = Math.max(1, Math.round(canvas.height * scale));
            const tmp = document.createElement('canvas');
            tmp.width = w; tmp.height = h;
            const ctx = tmp.getContext('2d', { willReadFrequently: true });
            ctx.drawImage(canvas, 0, 0, w, h);
            const found = jsQR(ctx.getImageData(0, 0, w, h).data, w, h);
            if (!found?.data) return null;
            try { return JSON.parse(found.data); } catch (e) { return { raw: found.data }; }
        } catch (e) {
            return null;
        }
    },

    /**
     * Baca satu lembar: kembalikan jawaban per blok + confidence + crop.
     * @param {HTMLCanvasElement} canvas citra jepretan
     * @param {number} expectedCount jumlah blok yang diharapkan (default 10)
     */
    async readSheet(canvas, expectedCount = 10) {
        const { cv } = await this.ensureLibs();
        this._cv = cv; // untuk helper ROI (crop)
        const qr = await this.decodeQR(canvas).catch(() => null);

        const src = cv.imread(canvas);
        const mats = [src];
        try {
            const gray = new cv.Mat(); mats.push(gray);
            cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);

            // 1. Bingkai lembar: quad terbesar → warp ke A4 standar
            const warped = this._warpSheet(cv, gray, canvas);
            const work = warped.mat; mats.push(work);
            const framed = warped.framed;

            // 2. Threshold adaptif (tahan bayangan/cahaya kuning aula)
            const bin = new cv.Mat(); mats.push(bin);
            cv.adaptiveThreshold(work, bin, 255,
                cv.ADAPTIVE_THRESH_GAUSSIAN_C, cv.THRESH_BINARY_INV, 51, 9);

            // 3. Blok soal: kontur persegi lebar-pendek, urut atas→bawah
            const blocks = this._findBlocks(cv, bin, expectedCount);

            // 4. Per blok: 4 kotak → fill-ratio → argmax
            const out = [];
            for (let i = 0; i < expectedCount; i++) {
                const b = blocks[i] || null;
                if (!b) {
                    out.push({ nomor: i + 1, jawaban: null, confidence: 0, flags: ['UNREAD'], fills: [], crop: null });
                    continue;
                }
                const cells = this._findCells(cv, bin, b);
                const fills = cells.map(c => this._fillRatio(bin, c));
                const verdict = this._judge(fills);
                out.push({
                    nomor: i + 1,
                    jawaban: verdict.jawaban,
                    confidence: verdict.confidence,
                    flags: verdict.flags,
                    fills,
                    crop: this._cropOf(work, b),
                });
            }

            // Preview warp untuk debug ringan (opsional, bisa diabaikan pemanggil)
            let preview = null;
            try {
                const pc = document.createElement('canvas');
                pc.width = work.cols; pc.height = work.rows;
                cv.imshow(pc, work);
                preview = pc.toDataURL('image/jpeg', 0.6);
            } catch (e) {}

            return { qr, framed, blocks: out, preview, tune: { ...OMR_TUNE } };
        } finally {
            mats.forEach(m => { try { m.delete(); } catch (e) {} });
        }
    },

    /** Warp lembar ke kanvas A4 standar via quad terbesar. */
    _warpSheet(cv, gray, canvas) {
        const bin = new cv.Mat();
        try {
            cv.adaptiveThreshold(gray, bin, 255,
                cv.ADAPTIVE_THRESH_GAUSSIAN_C, cv.THRESH_BINARY_INV, 101, 5);
            const contours = new cv.MatVector();
            const hier = new cv.Mat();
            try {
                cv.findContours(bin, contours, hier, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE);
                const imgArea = gray.rows * gray.cols;
                let best = null, bestArea = imgArea * 0.2;
                for (let i = 0; i < contours.size(); i++) {
                    const cnt = contours.get(i);
                    const peri = cv.arcLength(cnt, true);
                    const approx = new cv.Mat();
                    cv.approxPolyDP(cnt, approx, 0.02 * peri, true);
                    const area = cv.contourArea(approx);
                    if (approx.rows === 4 && area > bestArea && area < imgArea * 0.99) {
                        if (best) best.delete();
                        best = approx; bestArea = area;
                    } else {
                        approx.delete();
                    }
                    cnt.delete();
                }
                if (best) {
                    const pts = [];
                    for (let i = 0; i < 4; i++) {
                        pts.push({ x: best.data32S[i * 2], y: best.data32S[i * 2 + 1] });
                    }
                    best.delete();
                    const ordered = this._orderQuad(pts);
                    const W = OMR_TUNE.WARP_W, H = OMR_TUNE.WARP_H;
                    const srcTri = cv.matFromArray(4, 1, cv.CV_32FC2, [
                        ordered[0].x, ordered[0].y, ordered[1].x, ordered[1].y,
                        ordered[2].x, ordered[2].y, ordered[3].x, ordered[3].y,
                    ]);
                    const dstTri = cv.matFromArray(4, 1, cv.CV_32FC2, [0, 0, W, 0, W, H, 0, H]);
                    const M = cv.getPerspectiveTransform(srcTri, dstTri);
                    const warped = new cv.Mat();
                    const dsize = new cv.Size(W, H);
                    cv.warpPerspective(gray, warped, M, dsize, cv.INTER_LINEAR, cv.BORDER_REPLICATE);
                    srcTri.delete(); dstTri.delete(); M.delete();
                    return { mat: warped, framed: true };
                }
            } finally {
                contours.delete(); hier.delete();
            }
        } finally {
            bin.delete();
        }
        // Fallback: tanpa warp (capture sudah terpandu) — tandai tak-berbingkai
        const fallback = new cv.Mat();
        const W = OMR_TUNE.WARP_W, H = OMR_TUNE.WARP_H;
        const dsize = new cv.Size(W, H);
        const tmp = document.createElement('canvas');
        tmp.width = W; tmp.height = H;
        tmp.getContext('2d').drawImage(canvas, 0, 0, W, H);
        cv.imread(tmp, fallback);
        const g = new cv.Mat();
        cv.cvtColor(fallback, g, cv.COLOR_RGBA2GRAY);
        fallback.delete();
        return { mat: g, framed: false };
    },

    /** Urutkan 4 titik: TL, TR, BR, BL. */
    _orderQuad(pts) {
        const s = pts.map(p => ({ p, v: p.x + p.y }));
        const d = pts.map(p => ({ p, v: p.x - p.y }));
        const tl = s.reduce((a, b) => (a.v < b.v ? a : b)).p;
        const br = s.reduce((a, b) => (a.v > b.v ? a : b)).p;
        const tr = d.reduce((a, b) => (a.v > b.v ? a : b)).p;
        const bl = d.reduce((a, b) => (a.v < b.v ? a : b)).p;
        return [tl, tr, br, bl];
    },

    /** Blok soal: rects lebar (w>55% kanvas) & pendek (h 3–13% kanvas). */
    _findBlocks(cv, bin, expectedCount) {
        const contours = new cv.MatVector();
        const hier = new cv.Mat();
        const out = [];
        try {
            cv.findContours(bin, contours, hier, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE);
            const W = bin.cols, H = bin.rows;
            for (let i = 0; i < contours.size(); i++) {
                const cnt = contours.get(i);
                const r = cv.boundingRect(cnt);
                cnt.delete();
                if (r.width < W * 0.55) continue;
                if (r.height < H * 0.03 || r.height > H * 0.14) continue;
                out.push({ x: r.x, y: r.y, w: r.width, h: r.height });
            }
        } finally {
            contours.delete(); hier.delete();
        }
        out.sort((a, b) => a.y - b.y);
        return out.slice(0, expectedCount);
    },

    /** 4 kotak jawaban dalam blok: kontur kotak kecil, susun grid 2×2. */
    _findCells(cv, bin, block) {
        const roi = bin.roi(new cv.Rect(block.x, block.y, block.w, block.h));
        const contours = new cv.MatVector();
        const hier = new cv.Mat();
        const cells = [];
        try {
            cv.findContours(roi, contours, hier, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE);
            for (let i = 0; i < contours.size(); i++) {
                const cnt = contours.get(i);
                const r = cv.boundingRect(cnt);
                cnt.delete();
                const area = r.width * r.height;
                if (area < 120 || area > 4000) continue;
                const sq = Math.min(r.width, r.height) / Math.max(r.width, r.height);
                if (sq < 0.6) continue;
                cells.push({ x: r.x, y: r.y, w: r.width, h: r.height, cx: r.x + r.width / 2, cy: r.y + r.height / 2 });
            }
        } finally {
            roi.delete(); contours.delete(); hier.delete();
        }
        cells.sort((a, b) => a.cy - b.cy);
        const top = cells.slice(0, 2).sort((a, b) => a.cx - b.cx);
        const rest = cells.slice(2).sort((a, b) => a.cx - b.cx);
        const grid = [...top, ...rest].slice(0, 4);
        // Petakan posisi → A,B / C,D
        const labels = ['A', 'B', 'C', 'D'];
        return grid.map((c, i) => ({ ...c, opt: labels[i] }));
    },

    /** Rasio piksel gelap dalam sel (citra biner INV: putih = tinta). */
    _fillRatio(bin, cell) {
        try {
            const pad = 2;
            const x = Math.max(0, cell.x + pad), y = Math.max(0, cell.y + pad);
            const w = Math.max(1, cell.w - pad * 2), h = Math.max(1, cell.h - pad * 2);
            const roi = bin.roi(new cv.Rect(x, y, w, h));
            const n = cv.countNonZero(roi);
            const total = roi.rows * roi.cols;
            roi.delete();
            return total ? n / total : 0;
        } catch (e) {
            return 0;
        }
    },

    /** Vonis: argmax relatif + margin confidence + flag. */
    _judge(fills) {
        const labels = ['A', 'B', 'C', 'D'];
        const order = fills
            .map((f, i) => ({ i, f }))
            .sort((a, b) => b.f - a.f);
        const f1 = order.length ? order[0].f : 0;
        const f2 = order.length > 1 ? order[1].f : 0;

        if (!order.length || f1 < OMR_TUNE.DARK_FLOOR) {
            return { jawaban: null, confidence: 0, flags: ['KOSONG'] };
        }
        const gap = (f1 - f2) / Math.max(f1, 1e-6);
        if (gap < OMR_TUNE.DOUBLE_MARGIN && f2 >= OMR_TUNE.DARK_FLOOR) {
            return { jawaban: labels[order[0].i] ?? null, confidence: gap, flags: ['GANDA'] };
        }
        const confidence = Math.min(1, gap);
        if (confidence < OMR_TUNE.CONF_LOW) {
            return { jawaban: labels[order[0].i] ?? null, confidence, flags: ['RAGU'] };
        }
        return { jawaban: labels[order[0].i] ?? null, confidence, flags: [] };
    },

    /** Crop blok (grayscale) untuk thumbnail review. */
    _cropOf(workMat, block) {
        const cv = this._cv;
        if (!cv) return null;
        try {
            const roi = workMat.roi(new cv.Rect(block.x, block.y, block.w, block.h));
            const c = document.createElement('canvas');
            c.width = roi.cols; c.height = roi.rows;
            const cv2 = { imshow: null };
            // cv.imshow butuh instance cv — panggil via mat资格: gunakan trik toDataURL manual
            const tmp = document.createElement('canvas');
            tmp.width = roi.cols; tmp.height = roi.rows;
            const ctx = tmp.getContext('2d');
            const img = ctx.createImageData(roi.cols, roi.rows);
            const data = roi.data;
            for (let i = 0; i < data.length; i++) {
                const v = data[i];
                img.data[i * 4] = v; img.data[i * 4 + 1] = v;
                img.data[i * 4 + 2] = v; img.data[i * 4 + 3] = 255;
            }
            ctx.putImageData(img, 0, 0);
            roi.delete();
            void cv2;
            return tmp.toDataURL('image/jpeg', 0.7);
        } catch (e) {
            return null;
        }
    },
};

export default OmrVision;
