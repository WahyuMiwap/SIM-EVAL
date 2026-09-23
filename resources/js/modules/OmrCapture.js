/**
 * OmrCapture.js — Capture kamera HP untuk LJK (Tahap OMR).
 * - getUserMedia + panduan bingkai + tolak foto blur otomatis.
 * - Mengembalikan citra (canvas) siap diproses OmrVision.
 * - Tanpa dependensi berat; OpenCV hanya dimuat di OmrVision (lazy).
 */

const OmrCapture = {
    _stream: null,
    _video: null,

    /**
     * Nyalakan kamera ke elemen <video> yang diberikan.
     * @returns {Promise<boolean>} true bila kamera jalan.
     */
    async start(videoEl) {
        this.stop();
        if (!navigator.mediaDevices?.getUserMedia) return false;
        try {
            this._stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1920 }, height: { ideal: 1080 } },
                audio: false,
            });
            this._video = videoEl;
            videoEl.srcObject = this._stream;
            await videoEl.play();
            return true;
        } catch (e) {
            this.stop();
            return false;
        }
    },

    stop() {
        try {
            this._stream?.getTracks().forEach(t => t.stop());
        } catch (e) {}
        this._stream = null;
        this._video = null;
    },

    get active() {
        return !!this._stream;
    },

    /**
     * Jepret frame ke canvas (diskala, maks 1600px sisi panjang).
     */
    snap(maxSide = 1600) {
        const v = this._video;
        if (!v || !v.videoWidth) return null;
        const scale = Math.min(1, maxSide / Math.max(v.videoWidth, v.videoHeight));
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(v.videoWidth * scale);
        canvas.height = Math.round(v.videoHeight * scale);
        canvas.getContext('2d').drawImage(v, 0, 0, canvas.width, canvas.height);
        return canvas;
    },

    /**
     * Estimasi ketajaman (varian Laplacian) 0..∞ — di bawah ambang = blur.
     * Murni piksel canvas, tanpa OpenCV.
     */
    sharpness(canvas) {
        try {
            const w = canvas.width, h = canvas.height;
            const ctx = canvas.getContext('2d', { willReadFrequently: true });
            const step = Math.max(1, Math.floor(Math.min(w, h) / 200));
            const data = ctx.getImageData(0, 0, w, h).data;
            const gray = (x, y) => {
                const i = (y * w + x) * 4;
                return (data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114);
            };
            let sum = 0, sumSq = 0, n = 0;
            for (let y = step; y < h - step; y += step) {
                for (let x = step; x < w - step; x += step) {
                    const lap = gray(x - step, y) + gray(x + step, y)
                        + gray(x, y - step) + gray(x, y + step) - 4 * gray(x, y);
                    sum += lap; sumSq += lap * lap; n++;
                }
            }
            if (!n) return 0;
            const mean = sum / n;
            return sumSq / n - mean * mean;
        } catch (e) {
            return 0;
        }
    },
};

export default OmrCapture;
