const CACHE_NAME = 'ath-thoifah-v3'; // Naikkan versi ke v3 untuk mereset cache lama
const urlsToCache = [
    '/',
    '/login',
    '/salesman/home',
    '/manifest.json',
    '/js/alpine.min.js',         // Asset lokal Alpine.js
    '/js/localforage.min.js',    // Asset lokal LocalForage
    'https://cdn.tailwindcss.com',
    'https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap'
];

// 1. INSTALL: Caching aset dasar (jangan gagal semua jika 1 URL error)
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            // Gunakan Promise.allSettled agar jika 1 file gagal di-cache, yang lain tetap masuk
            return Promise.allSettled(
                urlsToCache.map(url => cache.add(url))
            );
        }).then(() => {
            // Paksa SW baru untuk langsung aktif tanpa menunggu reload
            return self.skipWaiting();
        })
    );
});

// 2. ACTIVATE: Bersihkan cache versi lama
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(name => name !== CACHE_NAME)
                          .map(name => caches.delete(name))
            );
        }).then(() => {
            // Ambil alih kontrol halaman dengan cepat
            return self.clients.claim();
        })
    );
});

// 3. FETCH: Strategi caching pintar
self.addEventListener('fetch', event => {
    // Abaikan request non-GET (seperti POST form upload)
    if (event.request.method !== 'GET') return;

    // Strategi untuk Navigasi (HTML Pages) -> Network First
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Jika online, simpan salinan halaman ke cache
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseClone));
                    return response;
                })
                .catch(() => {
                    // Jika offline, ambil dari cache
                    return caches.match(event.request)
                        .then(cached => cached || caches.match('/salesman/home'));
                })
        );
        return;
    }

    // Strategi untuk Aset Lain (JS, CSS, Gambar, API) -> Cache First
    event.respondWith(
        caches.match(event.request).then(cached => {
            // Jika ada di cache, langsung pakai
            if (cached) return cached;

            // Jika tidak ada, fetch ke network
            return fetch(event.request).then(response => {
                // Jika berhasil di-fetch, simpan ke cache untuk next time
                if (response && response.status === 200) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseClone));
                }
                return response;
            }).catch(() => {
                // Offline dan tidak ada di cache
                return new Response('', { status: 504, statusText: 'Offline' });
            });
        })
    );
});