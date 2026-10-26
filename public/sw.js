const CACHE_NAME = 'amil-gsc-v2';
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/logo_gsc.png'
];

// Install event - caching basic shell
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

// Activate event - cleanup old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch event - network first with cache fallback
self.addEventListener('fetch', (event) => {
  // Hanya proses GET requests
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // Jangan cache request API dan live actions
  if (url.pathname.includes('/livewire/') || url.pathname.includes('/api/')) {
    return;
  }

  // Khusus navigasi halaman: coba jaringan dulu, fallback ke cache jika offline
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.match(event.request) || caches.match('/');
      })
    );
    return;
  }

  // Aset statis: coba cache dulu, jika tidak ada ambil dari jaringan
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse;
      }
      return fetch(event.request).then((networkResponse) => {
        return networkResponse;
      });
    })
  );
});
