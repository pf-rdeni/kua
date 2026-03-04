const CACHE_NAME = 'display-kua-v2';

// Aset statis yang WAJIB di-cache saat pertama kali diakses.
// Hanya aset untuk halaman display (TV), BUKAN admin/backend!
const STATIC_ASSETS = [
    './assets/display/css/display-style.css',
    './assets/display/js/praytimes.js',
    './assets/display/js/display-engine.js',
    // Fallback jika gambar hilang
    './assets/img/default-masjid.jpg',
    './assets/img/logo-kemenag.png',
    './favicon.ico',
];

// Install Event: Cache aset statis awal
self.addEventListener('install', (event) => {
    // Memaksa Service Worker baru untuk langsung aktif (tidak menunggu page refresh)
    self.skipWaiting();

    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[ServiceWorker] Caching Static Assets');
            // Gunakan catch agar jika 1 file gagal, yang lain tetap jalan
            return Promise.all(
                STATIC_ASSETS.map(url => {
                    return cache.add(url).catch(err => console.warn('[ServiceWorker] Gagal cache static:', url, err));
                })
            );
        })
    );
});

// Activate Event: Bersihkan cache lama jika versi berubah
self.addEventListener('activate', (event) => {
    // Ambil kontrol semua client (tabs) sesegera mungkin
    event.waitUntil(self.clients.claim());

    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(
                keyList.map((key) => {
                    if (key !== CACHE_NAME) {
                        console.log('[ServiceWorker] Menghapus cache lama:', key);
                        return caches.delete(key);
                    }
                })
            );
        })
    );
});

// Fetch Event: Mencegat request dari browser
self.addEventListener('fetch', (event) => {
    // Abaikan request selain GET (seperti POST untuk form)
    if (event.request.method !== 'GET') return;

    const requestUrl = new URL(event.request.url);

    // 1. HALAMAN ADMIN & AUTH -> JANGAN di-intercept SW sama sekali!
    // Ini PALING PENTING: memastikan setiap refresh langsung ambil dari server.
    // Tanpa ini, perubahan kode PHP/CSS/JS di backend tidak langsung terlihat
    // karena browser melayani dari cache SW.
    if (requestUrl.pathname.startsWith('/admin') ||
        requestUrl.pathname.startsWith('/kua/admin') ||
        requestUrl.pathname.startsWith('/auth') ||
        requestUrl.pathname.startsWith('/kua/auth')) {
        return; // SW tidak ikut campur, biarkan browser fetch langsung ke server
    }

    // 2. API DATA (JSON) -> Network First, Fallback kosong (dihandle display-engine.js via LocalStorage)
    if (requestUrl.pathname.includes('/api/') ||
        requestUrl.pathname.includes('/api_keuangan/') ||
        requestUrl.pathname.includes('/check_update/')) {
        event.respondWith(
            fetch(event.request).catch(() => {
                return new Response(JSON.stringify({
                    success: false,
                    message: "Offline / Service Unavailable",
                    offline: true
                }), {
                    status: 503,
                    headers: { 'Content-Type': 'application/json' }
                });
            })
        );
        return;
    }

    // 3. HALAMAN DISPLAY HTML (/display/1, /display/keuangan/...) -> Network First, Fallback to Cache
    if (requestUrl.pathname.match(/\/display\/\d+/) || requestUrl.pathname.includes('/display/keuangan/')) {
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    if (networkResponse.ok) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    console.log('[ServiceWorker] Offline, mengambil halaman Display dari cache:', requestUrl.href);
                    return caches.match(event.request);
                })
        );
        return;
    }

    // 4. ASET DISPLAY SAJA (/assets/display/) -> Cache First, Fallback to Network
    // CATATAN: Sengaja HANYA folder assets/display/ yang di-cache.
    // Folder assets/plugins/, assets/dist/ (AdminLTE), dll. TIDAK dicache
    // supaya perubahan CSS/JS di backend langsung terlihat.
    if (requestUrl.pathname.includes('/assets/display/')) {
        event.respondWith(
            caches.match(event.request, { ignoreSearch: true }).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request).then((networkResponse) => {
                    if (networkResponse.ok) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    return new Response('Asset not available offline.', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: new Headers({ 'Content-Type': 'text/plain' })
                    });
                });
            })
        );
        return;
    }

    // 5. GAMBAR UPLOADS (/uploads/) -> Cache First, Fallback to Network (dan fallback gambar default)
    if (requestUrl.pathname.includes('/uploads/')) {
        event.respondWith(
            caches.match(event.request, { ignoreSearch: true }).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request).then((networkResponse) => {
                    if (networkResponse.ok) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    if (event.request.destination === 'image') {
                        return caches.match('./assets/img/default-masjid.jpg', { ignoreSearch: true });
                    }
                    return new Response('Asset not available offline.', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: new Headers({ 'Content-Type': 'text/plain' })
                    });
                });
            })
        );
        return;
    }

    // 6. SEMUA REQUEST LAIN -> Network First (tidak dicache, langsung dari server)
    // Ini memastikan halaman-halaman lain selalu segar dari server
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
