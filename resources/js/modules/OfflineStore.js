/**
 * OfflineStore.js — IndexedDB Manager (NFR-04)
 * Menyimpan soal, jawaban, dan request pending saat koneksi offline.
 * Digunakan oleh ApiHelper untuk offline-first fallback.
 */

const DB_NAME    = 'simeval_offline';
const DB_VERSION = 1;

const STORE_ANSWERS  = 'answers';      // Jawaban peserta saat offline
const STORE_SOAL     = 'soal_cache';  // Cache soal
const STORE_PENDING  = 'pending_requests'; // Queue API requests

let _db = null;

/** Buka koneksi IndexedDB */
async function openDB() {
    if (_db) return _db;
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, DB_VERSION);
        req.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_ANSWERS)) {
                db.createObjectStore(STORE_ANSWERS, { keyPath: 'id', autoIncrement: true });
            }
            if (!db.objectStoreNames.contains(STORE_SOAL)) {
                db.createObjectStore(STORE_SOAL, { keyPath: 'kegiatanId' });
            }
            if (!db.objectStoreNames.contains(STORE_PENDING)) {
                db.createObjectStore(STORE_PENDING, { keyPath: 'id', autoIncrement: true });
            }
        };
        req.onsuccess  = (e) => { _db = e.target.result; resolve(_db); };
        req.onerror    = (e) => reject(e.target.error);
    });
}

/** Helper: Get object store dalam transaction */
async function getStore(storeName, mode = 'readonly') {
    const db = await openDB();
    return db.transaction(storeName, mode).objectStore(storeName);
}

/** Helper: Promisify IDB request */
function idbReq(req) {
    return new Promise((res, rej) => {
        req.onsuccess = (e) => res(e.target.result);
        req.onerror   = (e) => rej(e.target.error);
    });
}

export const OfflineStore = {

    // =====================
    // SOAL CACHE
    // =====================

    /** Simpan paket soal ke cache lokal */
    async cacheSoal(kegiatanId, soalData) {
        const store = await getStore(STORE_SOAL, 'readwrite');
        return idbReq(store.put({ kegiatanId, soalData, cachedAt: Date.now() }));
    },

    /** Ambil soal dari cache */
    async getCachedSoal(kegiatanId) {
        const store = await getStore(STORE_SOAL);
        const result = await idbReq(store.get(kegiatanId));
        return result?.soalData ?? null;
    },

    // =====================
    // JAWABAN OFFLINE
    // =====================

    /** Simpan jawaban peserta saat offline */
    async saveAnswer(sessionId, soalId, jawaban, quizType) {
        const store = await getStore(STORE_ANSWERS, 'readwrite');
        return idbReq(store.put({ sessionId, soalId, jawaban, quizType, savedAt: Date.now() }));
    },

    /** Ambil semua jawaban dari sesi tertentu */
    async getAnswers(sessionId) {
        const store = await getStore(STORE_ANSWERS);
        const all = await idbReq(store.getAll());
        return all.filter(a => a.sessionId === sessionId);
    },

    /** Hapus jawaban setelah berhasil disinkronkan */
    async clearAnswers(sessionId) {
        const store = await getStore(STORE_ANSWERS, 'readwrite');
        const all = await idbReq(store.getAll());
        for (const item of all.filter(a => a.sessionId === sessionId)) {
            await idbReq(store.delete(item.id));
        }
    },

    // =====================
    // PENDING API REQUESTS
    // =====================

    /** Simpan request yang gagal (offline) ke antrian */
    async savePendingRequest(requestData) {
        const store = await getStore(STORE_PENDING, 'readwrite');
        return idbReq(store.add({ ...requestData, queuedAt: Date.now() }));
    },

    /** Ambil semua request pending */
    async getPendingRequests() {
        const store = await getStore(STORE_PENDING);
        return idbReq(store.getAll());
    },

    /** Hapus request pending setelah berhasil disinkronkan */
    async removePendingRequest(id) {
        const store = await getStore(STORE_PENDING, 'readwrite');
        return idbReq(store.delete(id));
    },

    /** Bersihkan seluruh queue */
    async clearAllPending() {
        const store = await getStore(STORE_PENDING, 'readwrite');
        return idbReq(store.clear());
    },
};

export default OfflineStore;
