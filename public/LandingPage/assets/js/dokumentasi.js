// ==================== DOKUMENTASI JS (PWA Ready) ====================

// Global variables
let photos = [];
let currentPhotoIndex = 0;
let map = null;
let touchStartX = 0;
let touchEndX = 0;

// ==================== INITIALIZE ====================
window.addEventListener('DOMContentLoaded', function() {
  loadPhotos();
  setupEventListeners();
  setupSwipeGestures();
  setupLazyLoading();
  console.log('📸 Dokumentasi PWA Ready!');
});

// ==================== LOAD PHOTOS FROM API ====================
async function loadPhotos() {
  try {
    const response = await fetch('/app/dokumentasi', {
      headers: {
        'Accept': 'application/json'
      }
    });
    
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      throw new Error('API belum tersedia');
    }
    
    const data = await response.json();
    
    if (data.success) {
      photos = data.data;
      renderPhotos();
      updateStats();
    }
    
  } catch (error) {
    console.warn('⚠️ Using dummy data:', error.message);
    loadDummyData();
  }
}

// ==================== DUMMY DATA ====================
function loadDummyData() {
  photos = [
    {
      id: 1,
      url: 'https://images.unsplash.com/photo-1581094271901-8022df4466f9?w=800',
      thumbnail: 'https://images.unsplash.com/photo-1581094271901-8022df4466f9?w=400',
      title: 'Pemasangan Fender',
      date: '2025-10-28',
      time: '08:30',
      gps: { lat: -5.1477, lon: 119.4327, accuracy: 8 },
      weather: { temp: 28, desc: 'Cerah', icon: '☀️', humidity: 75 },
      project: 'revitalisasi',
      projectName: 'Revitalisasi Pelabuhan'
    },
    {
      id: 2,
      url: 'https://images.unsplash.com/photo-1590856029826-c7a73142bbf1?w=800',
      thumbnail: 'https://images.unsplash.com/photo-1590856029826-c7a73142bbf1?w=400',
      title: 'Progress Pengecoran',
      date: '2025-10-28',
      time: '10:15',
      gps: { lat: -5.1480, lon: 119.4330, accuracy: 12 },
      weather: { temp: 29, desc: 'Berawan', icon: '⛅', humidity: 70 },
      project: 'revitalisasi',
      projectName: 'Revitalisasi Pelabuhan'
    },
    {
      id: 3,
      url: 'https://images.unsplash.com/photo-1597008641621-cefdcf718025?w=800',
      thumbnail: 'https://images.unsplash.com/photo-1597008641621-cefdcf718025?w=400',
      title: 'Instalasi Crane',
      date: '2025-10-28',
      time: '14:45',
      gps: { lat: -5.1456, lon: 119.4310, accuracy: 10 },
      weather: { temp: 27, desc: 'Hujan Ringan', icon: '🌧️', humidity: 85 },
      project: 'dermaga',
      projectName: 'Dermaga Baru'
    },
    {
      id: 4,
      url: 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800',
      thumbnail: 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400',
      title: 'Quality Check',
      date: '2025-10-28',
      time: '16:20',
      gps: { lat: -5.1465, lon: 119.4315, accuracy: 9 },
      weather: { temp: 26, desc: 'Berawan', icon: '⛅', humidity: 78 },
      project: 'revitalisasi',
      projectName: 'Revitalisasi Pelabuhan'
    },
    {
      id: 5,
      url: 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=800',
      thumbnail: 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=400',
      title: 'Struktur Beton',
      date: '2025-10-27',
      time: '09:00',
      gps: { lat: -5.1470, lon: 119.4320, accuracy: 11 },
      weather: { temp: 30, desc: 'Cerah', icon: '☀️', humidity: 68 },
      project: 'dermaga',
      projectName: 'Dermaga Baru'
    },
    {
      id: 6,
      url: 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=800',
      thumbnail: 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=400',
      title: 'Pengecatan Gudang',
      date: '2025-10-27',
      time: '11:30',
      gps: { lat: -5.1520, lon: 119.4280, accuracy: 15 },
      weather: { temp: 31, desc: 'Cerah', icon: '☀️', humidity: 65 },
      project: 'gudang',
      projectName: 'Gudang Logistik'
    }
  ];
  
  renderPhotos();
  updateStats();
}

// ==================== RENDER PHOTOS ====================
function renderPhotos() {
  const container = document.querySelector('#viewTimeline .row');
  if (!container) return;
  
  // Group by date
  const grouped = photos.reduce((groups, photo) => {
    const date = photo.date;
    if (!groups[date]) groups[date] = [];
    groups[date].push(photo);
    return groups;
  }, {});
  
  // Render each day group
  Object.keys(grouped).sort().reverse().forEach(date => {
    const dayPhotos = grouped[date];
    // Implementation depends on your HTML structure
  });
}

// ==================== UPDATE STATS ====================
function updateStats() {
  const totalPhotos = photos.length;
  const uniqueLocations = new Set(photos.map(p => `${p.gps.lat},${p.gps.lon}`)).size;
  const activeProjects = new Set(photos.map(p => p.project)).size;
  
  document.getElementById('totalPhotos').textContent = totalPhotos;
  document.getElementById('uniqueLocations').textContent = uniqueLocations;
  document.getElementById('activeProjects').textContent = activeProjects;
}

// ==================== SETUP EVENT LISTENERS ====================
function setupEventListeners() {
  // Filter chips
  document.querySelectorAll('.filter-chip').forEach(chip => {
    chip.addEventListener('click', function() {
      document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      filterPhotos(this.dataset.filter);
    });
  });
  
  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    const lightbox = document.getElementById('lightbox');
    if (lightbox && lightbox.classList.contains('active')) {
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') prevPhoto();
      if (e.key === 'ArrowRight') nextPhoto();
    }
  });
  
  // Lightbox background click
  const lightbox = document.getElementById('lightbox');
  if (lightbox) {
    lightbox.addEventListener('click', (e) => {
      if (e.target.id === 'lightbox') closeLightbox();
    });
  }
}

// ==================== SWIPE GESTURES (PWA) ====================
function setupSwipeGestures() {
  const lightbox = document.getElementById('lightbox');
  if (!lightbox) return;
  
  lightbox.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
  });
  
  lightbox.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
  });
}

function handleSwipe() {
  const swipeThreshold = 50;
  const diff = touchStartX - touchEndX;
  
  if (Math.abs(diff) > swipeThreshold) {
    if (diff > 0) {
      nextPhoto(); // Swipe left
    } else {
      prevPhoto(); // Swipe right
    }
  }
}

// ==================== LAZY LOADING IMAGES ====================
function setupLazyLoading() {
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.classList.remove('skeleton');
          observer.unobserve(img);
        }
      });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
      imageObserver.observe(img);
    });
  }
}

// ==================== TAB SWITCHING ====================
function switchTab(tab) {
  // Reset all tabs
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  
  // Hide all views
  document.getElementById('viewTimeline').style.display = 'none';
  document.getElementById('viewMap').style.display = 'none';
  document.getElementById('viewGrid').style.display = 'none';
  
  // Show selected
  if (tab === 'timeline') {
    document.getElementById('tabTimeline').classList.add('active');
    document.getElementById('viewTimeline').style.display = 'block';
  } else if (tab === 'map') {
    document.getElementById('tabMap').classList.add('active');
    document.getElementById('viewMap').style.display = 'block';
    setTimeout(() => {
      initMap();
      if (map) map.invalidateSize();
    }, 100);
  } else if (tab === 'grid') {
    document.getElementById('tabGrid').classList.add('active');
    document.getElementById('viewGrid').style.display = 'block';
  }
}

// ==================== INITIALIZE MAP ====================
function initMap() {
  if (!map && typeof L !== 'undefined') {
    map = L.map('map').setView([-5.1477, 119.4327], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19
    }).addTo(map);
    
    // Add markers
    photos.forEach((photo, index) => {
      const marker = L.marker([photo.gps.lat, photo.gps.lon]).addTo(map);
      marker.bindPopup(`
        <div style="text-align: center; min-width: 200px;">
          <img src="${photo.thumbnail}" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:8px;">
          <strong>${photo.title}</strong><br>
          <small>${photo.weather.temp}°C ${photo.weather.icon}</small>
        </div>
      `);
      marker.on('click', () => openLightbox(index));
    });
  }
}

// ==================== FILTER PHOTOS ====================
function filterPhotos(filter) {
  // Implementation for filtering photos by project type
  console.log('Filter by:', filter);
  // Re-render photos based on filter
}

// ==================== LIGHTBOX FUNCTIONS ====================
function openLightbox(index) {
  currentPhotoIndex = index;
  updateLightbox();
  document.getElementById('lightbox').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  document.getElementById('lightbox').classList.remove('active');
  document.body.style.overflow = 'auto';
}

function updateLightbox() {
  const photo = photos[currentPhotoIndex];
  if (!photo) return;
  
  document.getElementById('lightboxImage').src = photo.url;
  document.getElementById('lightboxTitle').textContent = photo.title;
  
  const dateObj = new Date(photo.date);
  const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  const dayName = days[dateObj.getDay()];
  const dateStr = `${dayName}, ${dateObj.getDate()} ${months[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
  
  document.getElementById('lightboxDate').textContent = `${dateStr} • ${photo.time} WIB`;
}

function prevPhoto() {
  currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
  updateLightbox();
}

function nextPhoto() {
  currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
  updateLightbox();
}

// ==================== EXPORT FUNCTIONS FOR GLOBAL SCOPE ====================
window.switchTab = switchTab;
window.openLightbox = openLightbox;
window.closeLightbox = closeLightbox;
window.prevPhoto = prevPhoto;
window.nextPhoto = nextPhoto;
