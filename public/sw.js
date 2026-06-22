const CACHE = 'credixa-v7';
const SHELL = ['/app', '/login'];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE)
            .then(c => c.addAll(SHELL))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys.filter(k => k !== CACHE).map(k => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;

    const url = new URL(e.request.url);

    /* Ignorer extensions navigateur */
    if (!url.protocol.startsWith('http')) return;

    /* NE JAMAIS cacher storage/ — fichiers dynamiques uploadés */
    if (url.pathname.startsWith('/storage/')) return;

    /* Assets Vite hashés — cache-first (immutables) */
    if (url.pathname.startsWith('/build/assets/')) {
        e.respondWith(
            caches.open(CACHE).then(c =>
                c.match(e.request).then(cached => {
                    if (cached) return cached;
                    return fetch(e.request).then(resp => {
                        if (resp.ok) {
                            const clone = resp.clone();
                            c.put(e.request, clone);
                        }
                        return resp;
                    });
                })
            )
        );
        return;
    }

    /* Tout le reste — network-first, fallback cache */
    e.respondWith(
        fetch(e.request)
            .then(resp => {
                if (resp.ok) {
                    const clone = resp.clone();
                    caches.open(CACHE).then(c => c.put(e.request, clone));
                }
                return resp;
            })
            .catch(() =>
                caches.match(e.request)
                    .then(cached => cached || caches.match('/app'))
            )
    );
});
