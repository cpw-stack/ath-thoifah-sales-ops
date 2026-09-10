const CACHE_NAME = 'ath-thoifah-v2';
const urlsToCache = [
    '/',
    '/login',
    '/salesman/home',
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js',
    'https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    // Jangan cache API atau upload file, biarkan lewat
    if (event.request.method !== 'GET') return;

    event.respondWith(
        caches.match(event.request).then(response => {
            // Jika ada di cache, gunakan. Jika tidak, fetch ke network.
            return response || fetch(event.request).catch(() => caches.match('/salesman/home'));
        })
    );
});