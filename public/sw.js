/**
 * SIM-EVAL Service Worker (Tahap 4 — PWA peserta).
 * - Aset statis (same-origin GET): cache-first.
 * - API dinamis (POST/submit, polling status): network-only, JANGAN di-cache
 *   (pengiriman jawaban offline sudah diantre QuizEngine via IndexedDB).
 * - Navigasi: network-first, fallback cache bila offline.
 */
const CACHE = 'simeval-pwa-v1';
const STATIC_PATTERNS = [
    '/build/',
    '/favicon.ico',
    '/manifest.webmanifest',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(['/join', '/favicon.ico'])).catch(() => {})
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

function isApiRequest(url) {
    return url.pathname.endsWith('/submit')
        || url.pathname.includes('/waiting/status/')
        || url.pathname.endsWith('/join/info');
}

self.addEventListener('fetch', (event) => {
    const { request } = event;
    if (request.method !== 'GET') return; // POST submit: selalu ke jaringan

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return; // CDN: biarkan browser
    if (isApiRequest(url)) return; // API dinamis: selalu ke jaringan

    const isStatic = STATIC_PATTERNS.some((p) => url.pathname.startsWith(p));

    if (isStatic) {
        // Cache-first untuk aset
        event.respondWith(
            caches.match(request).then((hit) => hit || fetch(request).then((res) => {
                const copy = res.clone();
                caches.open(CACHE).then((cache) => cache.put(request, copy));
                return res;
            }))
        );
        return;
    }

    if (request.mode === 'navigate') {
        // Network-first untuk halaman, fallback cache saat offline
        event.respondWith(
            fetch(request)
                .then((res) => {
                    const copy = res.clone();
                    caches.open(CACHE).then((cache) => cache.put(request, copy));
                    return res;
                })
                .catch(() => caches.match(request).then((hit) => hit || caches.match('/join')))
        );
    }
});
