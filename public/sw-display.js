const CACHE_NAME = 'display-kua-v1';

// Aset statis yang WAJIB di-cache saat pertama kali diakses.
// Menyimpan menggunakan absolute URL path berdasarkan origin
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

    // 1. API DATA (JSON) -> Network First, Fallback to LocalStorage (sudah dihandle display-engine.js)
    // Sebaiknya SW tidak men-cache respons API JSON di Cache API browser agar
    // tidak tabrakan dengan mekanisme LocalStorage yang lebih cerdas di display-engine.js.
    if (requestUrl.pathname.includes('/api/') || requestUrl.pathname.includes('/api_keuangan/') || requestUrl.pathname.includes('/check_update/')) {
        event.respondWith(
            fetch(event.request).catch(() => {
                // Return fallback JSON response silently to avoid Uncaught TypeError in console
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

    // 2. HALAMAN DISPLAY HTML (/display/1, /display/keuangan/...) -> Network First, Fallback to Cache
    // Kenapa Network First? Supaya kalau koneksi jalan, dapat layout HTML/pengaturan terbaru.
    // Kalau koneksi mati, berikan salinan halaman terakhir dari cache!
    if (requestUrl.pathname.match(/\/display\/\d+/) || requestUrl.pathname.includes('/display/keuangan/')) {
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    // Kalau dapet response valid dari server, simpan ke cache
                    if (networkResponse.ok) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    // JIKA OFFLINE -> Ambil dari cache!
                    console.log('[ServiceWorker] Offline, mengambil halaman Display dari cache:', requestUrl.href);
                    return caches.match(event.request);
                })
        );
        return;
    }

    // 3. GAMBAR & ASET STATIS (/assets/..., /uploads/...) -> Cache First, Fallback to Network
    // Gunakan regex yang lebih kuat untuk menangkap string path dimanapun posisinya
    if (requestUrl.pathname.match(/\/assets\//) || requestUrl.pathname.match(/\/uploads\//) || requestUrl.pathname.endsWith('.jpg') || requestUrl.pathname.endsWith('.png') || requestUrl.pathname.endsWith('.css') || requestUrl.pathname.endsWith('.js')) {
        event.respondWith(
            caches.match(event.request, { ignoreSearch: true }).then((cachedResponse) => {
                // Return cache jika ada
                if (cachedResponse) {
                    return cachedResponse;
                }

                // Jika tidak ada di cache, fetch dari network
                return fetch(event.request).then((networkResponse) => {
                    // Jika sukses mengunduh gambar/aset baru, tambahkan ke cache diam-diam
                    if (networkResponse.ok) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    // Jika offline dan request adalah GAMBAR, kirim gambar fallback
                    if (event.request.destination === 'image') {
                        // Coba pakai relative match
                        return caches.match('./assets/img/default-masjid.jpg', { ignoreSearch: true });
                    }

                    // Fallback jika asset lain (css/js) gagal diload
                    return new Response('Asset not available offline.', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: new Headers({
                            'Content-Type': 'text/plain'
                        })
                    });
                });
            })
        );
        return;
    }

    // 4. LAIN-LAIN -> Stale-While-Revalidate
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            const fetchPromise = fetch(event.request).then((networkResponse) => {
                if (networkResponse.ok) {
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, networkResponse.clone());
                    });
                }
                return networkResponse;
            }).catch(() => {
                // Return fallback response instead of null to prevent TypeError
                return new Response('Network error occurred.', {
                    status: 503,
                    statusText: 'Service Unavailable',
                    headers: new Headers({
                        'Content-Type': 'text/plain'
                    })
                });
            });

            return cachedResponse || fetchPromise;
        })
    );
});
