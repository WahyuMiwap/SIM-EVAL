/**
 * ApiHelper.js — Lapisan Operasi Asinkron (NFR-07)
 * Satu-satunya titik komunikasi ke server/API.
 * Menangani fetch, CSRF, error handling, dan jembatan offline sync ke IndexedDB.
 */

import { OfflineStore } from '../modules/OfflineStore.js';

const ApiHelper = {
    /** CSRF token dari meta tag */
    _csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    },

    /** Base headers untuk semua request */
    _headers(extra = {}) {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this._csrf(),
            ...extra,
        };
    },

    /**
     * GET request
     * @param {string} url
     * @param {object} params - query params
     */
    async get(url, params = {}) {
        const query = new URLSearchParams(params).toString();
        const fullUrl = query ? `${url}?${query}` : url;
        try {
            const res = await fetch(fullUrl, {
                method: 'GET',
                headers: this._headers(),
                credentials: 'same-origin',
            });
            return this._handleResponse(res);
        } catch (err) {
            return this._handleNetworkError(err, { url, method: 'GET', params });
        }
    },

    /**
     * POST request
     * @param {string} url
     * @param {object} data - body payload
     * @param {boolean} offlineFallback - simpan ke IndexedDB jika offline
     */
    async post(url, data = {}, offlineFallback = false) {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: this._headers(),
                credentials: 'same-origin',
                body: JSON.stringify(data),
            });
            return this._handleResponse(res);
        } catch (err) {
            if (offlineFallback && !navigator.onLine) {
                console.warn('[ApiHelper] Offline — menyimpan ke IndexedDB:', url, data);
                await OfflineStore.savePendingRequest({ url, method: 'POST', data, timestamp: Date.now() });
                return { offline: true, queued: true, message: 'Jawaban disimpan offline, akan disinkronkan saat online.' };
            }
            return this._handleNetworkError(err, { url, method: 'POST', data });
        }
    },

    /**
     * PUT / PATCH request
     */
    async put(url, data = {}) {
        try {
            const res = await fetch(url, {
                method: 'PUT',
                headers: this._headers(),
                credentials: 'same-origin',
                body: JSON.stringify({ ...data, _method: 'PUT' }),
            });
            return this._handleResponse(res);
        } catch (err) {
            return this._handleNetworkError(err, { url, method: 'PUT', data });
        }
    },

    /**
     * DELETE request dengan password re-autentikasi (FR-43)
     * @param {string} url
     * @param {string} password - password akun staf aktif
     */
    async delete(url, password) {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: this._headers(),
                credentials: 'same-origin',
                body: JSON.stringify({ _method: 'DELETE', password }),
            });
            return this._handleResponse(res);
        } catch (err) {
            return this._handleNetworkError(err, { url, method: 'DELETE' });
        }
    },

    /**
     * Upload form data (multipart)
     */
    async upload(url, formData) {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': this._csrf(), 'Accept': 'application/json' },
                credentials: 'same-origin',
                body: formData,
            });
            return this._handleResponse(res);
        } catch (err) {
            return this._handleNetworkError(err, { url, method: 'POST' });
        }
    },

    /**
     * Sinkronisasi request offline yang tersimpan di IndexedDB
     * Dipanggil saat koneksi pulih (online event)
     */
    async syncOfflineQueue() {
        if (!navigator.onLine) return;
        const queue = await OfflineStore.getPendingRequests();
        if (!queue.length) return;

        console.log(`[ApiHelper] Menyinkronkan ${queue.length} request offline...`);

        for (const req of queue) {
            try {
                const result = await this.post(req.url, req.data);
                if (result && !result.error) {
                    await OfflineStore.removePendingRequest(req.id);
                    console.log('[ApiHelper] Synced:', req.url);
                }
            } catch (e) {
                console.error('[ApiHelper] Gagal sync:', req.url, e);
            }
        }
    },

    /** Parse response JSON atau text */
    async _handleResponse(res) {
        const contentType = res.headers.get('Content-Type') ?? '';
        if (contentType.includes('application/json')) {
            const json = await res.json();
            if (!res.ok) {
                return { error: true, status: res.status, message: json.message ?? 'Terjadi kesalahan.', data: json };
            }
            return json;
        }
        const text = await res.text();
        if (!res.ok) {
            return { error: true, status: res.status, message: text };
        }
        return { success: true, raw: text };
    },

    /** Error jaringan (no internet / CORS / timeout) */
    _handleNetworkError(err, context) {
        console.error('[ApiHelper] Network Error:', context, err);
        return {
            error: true,
            network: true,
            message: navigator.onLine
                ? 'Server tidak dapat dijangkau. Coba beberapa saat lagi.'
                : 'Tidak ada koneksi internet.',
        };
    },
};

// Auto sync saat koneksi pulih (NFR-04)
window.addEventListener('online', () => {
    console.log('[ApiHelper] Koneksi pulih — memulai sync...');
    ApiHelper.syncOfflineQueue();
});

export default ApiHelper;
