const CACHE_NAME = 'pmones-v1.0.4'; // Increment version
const urlsToCache = [
  '/',
  '/landingpage/beranda',
  '/landingpage/pelaporan',
  '/LandingPage/assets/icons/icon-72x72.png',
  '/LandingPage/assets/icons/icon-192x192.png',
  '/LandingPage/assets/icons/icon-512x512.png',
  '/LandingPage/assets/css/main.css',
  '/LandingPage/assets/vendor/bootstrap/css/bootstrap.min.css',
  '/offline.html' 
];

// Install
self.addEventListener('install', event => {
  console.log('✅ SW Installing... v1.0.4');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('📦 Caching files...');
        return cache.addAll(urlsToCache);
      })
      .catch(err => console.error('❌ Cache error:', err))
  );
  self.skipWaiting(); 
});

// Activate
self.addEventListener('activate', event => {
  console.log('✅ SW Activated v1.0.4');
  event.waitUntil(
    caches.keys().then(cacheNames => 
      Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            console.log('🗑️ Deleting old cache:', cache);
            return caches.delete(cache);
          }
        })
      )
    )
  );
  return self.clients.claim(); 
});

// Fetch - Network First, fallback to Cache
self.addEventListener('fetch', event => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') return;
  
  // Skip chrome-extension and non-http requests
  if (!event.request.url.startsWith('http')) return;

  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Clone response untuk di-cache
        const responseClone = response.clone();
        
        // Cache successful responses
        if (response.status === 200) {
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseClone);
          });
        }
        
        return response;
      })
      .catch(() => {
        // Network failed, try cache
        return caches.match(event.request)
          .then(cachedResponse => {
            if (cachedResponse) {
              return cachedResponse;
            }
            
            // If HTML page not in cache, show offline page
            if (event.request.headers.get('accept').includes('text/html')) {
              return caches.match('/offline.html');
            }
          });
      })
  );
});