/* ═══════════════════════════════════════════════════════════════
   Credixa — Service Worker v8
   Cache-first pour assets, Network-first pour HTML
   Push Notifications VAPID
   ═══════════════════════════════════════════════════════════════ */
const CACHE = 'credixa-v8';
const ICON  = '/images/icon-192.png';
const BADGE = '/images/icon-badge.png';
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
            .then(keys => Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k))))
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

    /* Assets Vite — Cache-First */
    if (url.pathname.startsWith('/build/assets/')) {
        e.respondWith(
            caches.open(CACHE).then(c =>
                c.match(e.request).then(cached => {
                    if (cached) return cached;
                    return fetch(e.request).then(resp => {
                        if (resp.ok) c.put(e.request, resp.clone());
                        return resp;
                    });
                })
            )
        );
        return;
    }

    /* Images et fonts statiques — Cache-First */
    if (/\.(png|jpg|jpeg|gif|svg|ico|webp|woff2?|ttf|eot|otf)(\?.*)?$/.test(url.pathname)) {
        e.respondWith(
            caches.match(e.request).then(cached => {
                if (cached) return cached;
                return fetch(e.request).then(resp => {
                    if (resp.ok) caches.open(CACHE).then(c => c.put(e.request, resp.clone()));
                    return resp;
                }).catch(() => cached);
            })
        );
        return;
    }

    /* Tout le reste — Network-First avec fallback cache puis shell */
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

/* ── Push Notifications ─────────────────────────────────────────── */
self.addEventListener('push', e => {
    let data = { title: 'Credixa', body: '' };
    try { data = e.data ? e.data.json() : data; } catch (_) {}

    e.waitUntil(
        self.registration.showNotification(data.title || 'Credixa', {
            body:     data.body  || '',
            icon:     ICON,
            badge:    BADGE,
            vibrate:  [200, 100, 200],
            tag:      data.tag || 'credixa-notif',
            renotify: true,
            data:     { url: data.url || '/app/notifications' },
        })
    );
});

self.addEventListener('notificationclick', e => {
    e.notification.close();
    const target = (e.notification.data && e.notification.data.url) || '/app/notifications';
    e.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            for (const c of list) {
                if (c.url.includes('/app') && 'focus' in c) {
                    c.navigate(target);
                    return c.focus();
                }
            }
            if (clients.openWindow) return clients.openWindow(target);
        })
    );
});
