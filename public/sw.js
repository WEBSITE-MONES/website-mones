const CACHE_NAME = 'pmones-v1.0.5'; 
const urlsToCache = [
  '/',
  '/login',
  '/loginn',
  '/register',
  '/dashboard',
  '/dashboard/dashboard',
  '/landingpage/beranda',
  '/landingpage/pelaporan',
  '/LandingPage/assets/icons/icon-72x72.png',
  '/LandingPage/assets/icons/icon-192x192.png',
  '/LandingPage/assets/icons/icon-512x512.png',
  '/LandingPage/assets/css/main.css',
  '/LandingPage/assets/vendor/bootstrap/css/bootstrap.min.css',
  '/login/css/style.css',
  '/login/assets/Logo_Pelindo_1.png',
  '/login/assets/Logo_Pelindo_2.png',
  '/login/assets/M_logo.png',
  '/offline.html' 
];

// Install
self.addEventListener('install', event => {
  console.log('✅ SW Installing... v1.0.5');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('📦 Caching files...');
        return cache.addAll(urlsToCache.map(url => new Request(url, {credentials: 'same-origin'})));
      })
      .catch(err => console.error('❌ Cache error:', err))
  );
  self.skipWaiting(); 
});

// Activate
self.addEventListener('activate', event => {
  console.log('✅ SW Activated v1.0.5');
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

// Fetch - Network First with timeout, fallback to Cache
self.addEventListener('fetch', event => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') return;
  
  // Skip chrome-extension and non-http requests
  if (!event.request.url.startsWith('http')) return;

  // Skip API calls that should always be fresh
  if (event.request.url.includes('/api/')) {
    return event.respondWith(fetch(event.request));
  }

  event.respondWith(
    // Try network first with 3s timeout
    Promise.race([
      fetch(event.request)
        .then(response => {
          // Clone response untuk di-cache
          if (response.status === 200) {
            const responseClone = response.clone();
            caches.open(CACHE_NAME).then(cache => {
              cache.put(event.request, responseClone);
            });
          }
          return response;
        }),
      new Promise((_, reject) =>
        setTimeout(() => reject(new Error('timeout')), 3000)
      )
    ])
    .catch(() => {
      // Network failed or timeout, try cache
      return caches.match(event.request)
        .then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }
          
          // If HTML page not in cache, show offline page
          if (event.request.headers.get('accept').includes('text/html')) {
            return caches.match('/offline.html');
          }
          
          // Return a basic offline response for other resources
          return new Response('Offline', {
            status: 503,
            statusText: 'Service Unavailable',
            headers: new Headers({
              'Content-Type': 'text/plain'
            })
          });
        });
    })
  );
});

// Handle messages from clients
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    event.waitUntil(
      caches.keys().then(cacheNames => {
        return Promise.all(
          cacheNames.map(cache => caches.delete(cache))
        );
      })
    );
  }
});