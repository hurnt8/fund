/* ═══════════════════════════════════════════════
   Credixa Invest — Service Worker
   Cache-first pour les assets statiques
   ═══════════════════════════════════════════════ */
const CACHE = 'credixa-v1';

const PRECACHE = [
  '/assets/vendors/fontawesome/css/all.min.css',
  '/assets/vendors/bootstrap/css/bootstrap.min.css',
  '/assets/vendors/bootstrap/js/bootstrap.bundle.min.js',
  '/assets/images/favicons/favicon.png',
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE)
      .then(cache => cache.addAll(PRECACHE))
      .then(() => self.skipWaiting())
      .catch(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(keys => Promise.all(
        keys.filter(k => k !== CACHE).map(k => caches.delete(k))
      ))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  const isStatic = /\.(css|js|woff2?|ttf|eot|otf|png|jpg|jpeg|gif|svg|ico|webp)(\?.*)?$/.test(url.pathname);
  if (isStatic) {
    event.respondWith(
      caches.match(event.request).then(cached => {
        if (cached) return cached;
        return fetch(event.request).then(response => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE).then(c => c.put(event.request, clone));
          }
          return response;
        }).catch(() => cached);
      })
    );
    return;
  }
  const accept = event.request.headers.get('Accept') || '';
  if (accept.includes('text/html')) {
    event.respondWith(fetch(event.request).catch(() => caches.match(event.request)));
  }
});
