// ==================== PELAPORAN PROGRESS HARIAN JS - FIXED ====================

// Set tanggal default ke hari ini
document.getElementById('tanggal').valueAsDate = new Date();

// Global variables
let userLocation = null;
let uploadedFiles = [];
let currentGPSCoords = null;

// OpenWeather API Key
const OPENWEATHER_API_KEY = 'eb63c86a920d5776c62b7dd6641e95c0';

// ==================== MODERN ALERT HELPER ====================
function showAlert(icon, title, text) {
  Swal.fire({
    icon: icon,
    title: title,
    text: text,
    confirmButtonColor: '#1d6ba8',
    confirmButtonText: 'OK'
  });
}

function showConfirm(title, text, confirmCallback, cancelCallback) {
  Swal.fire({
    title: title,
    text: text,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#1d6ba8',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Kamera 📸',
    cancelButtonText: 'Galeri 🖼️'
  }).then((result) => {
    if (result.isConfirmed) {
      confirmCallback();
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      if (cancelCallback) cancelCallback();
    }
  });
}

function showSuccess(title, text) {
  Swal.fire({
    icon: 'success',
    title: title,
    text: text,
    confirmButtonColor: '#1d6ba8',
    timer: 3000
  });
}

// ==================== GET LOCATION FROM GPS (LIVE) ====================
async function getUserLocation() {
  const weatherInfo = document.getElementById('weatherInfo');
  if (weatherInfo) {
    weatherInfo.innerHTML = '<div class="loading-spinner"></div><div class="weather-details"><p>⏳ Mengambil data GPS & cuaca...</p></div>';
  }
  
  if (!navigator.geolocation) {
    showAlert('warning', 'GPS Tidak Didukung', 'Browser Anda tidak mendukung GPS. Menggunakan lokasi default.');
    useFallbackLocation();
    return;
  }

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      const lat = position.coords.latitude;
      const lon = position.coords.longitude;
      const accuracy = position.coords.accuracy;
      
      currentGPSCoords = {
        latitude: lat,
        longitude: lon,
        accuracy: accuracy,
        timestamp: new Date().toISOString()
      };
      
      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lon;
      
      try {
        const geoResponse = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`,
          {
            headers: {
              'User-Agent': 'P-Mones-App'
            }
          }
        );
        
        if (geoResponse.ok) {
          const geoData = await geoResponse.json();
          const address = geoData.address;
          
          const city = address.city || address.town || address.village || address.county || 'Unknown';
          const region = address.state || address.province || '';
          const country = address.country || 'Indonesia';
          
          userLocation = {
            city: city,
            region: region,
            country: country,
            latitude: lat,
            longitude: lon
          };
        }
        
      } catch (error) {
        console.error('Reverse geocoding error:', error);
      }
      
      getWeatherDataLive(lat, lon);
    },
    (error) => {
      console.error('GPS Error:', error);
      
      let errorMsg = '';
      switch(error.code) {
        case error.PERMISSION_DENIED:
          errorMsg = 'GPS diblokir. Izinkan akses lokasi di browser Anda.';
          break;
        case error.POSITION_UNAVAILABLE:
          errorMsg = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
          break;
        case error.TIMEOUT:
          errorMsg = 'GPS timeout. Mencoba lagi...';
          break;
      }
      
      showAlert('warning', 'GPS Error', errorMsg + ' Menggunakan lokasi fallback.');
      useFallbackLocation();
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
}

// ==================== FALLBACK LOCATION (IP-based) ====================
async function useFallbackLocation() {
  try {
    const response = await fetch('https://ipapi.co/json/');
    const data = await response.json();
    
    if (data.error) {
      throw new Error(data.reason || 'IP API Error');
    }
    
    userLocation = {
      city: data.city || 'Makassar',
      region: data.region || 'Sulawesi Selatan',
      country: data.country_name || 'Indonesia',
      latitude: data.latitude || -5.1477,
      longitude: data.longitude || 119.4327
    };
    
    currentGPSCoords = {
      latitude: userLocation.latitude,
      longitude: userLocation.longitude,
      accuracy: null,
      timestamp: new Date().toISOString()
    };
    
    document.getElementById('latitude').value = userLocation.latitude;
    document.getElementById('longitude').value = userLocation.longitude;
    
    getWeatherDataLive(userLocation.latitude, userLocation.longitude);
    
  } catch (error) {
    console.error('Fallback location error:', error);
    
    userLocation = {
      city: 'Makassar',
      region: 'Sulawesi Selatan',
      country: 'Indonesia',
      latitude: -5.1477,
      longitude: 119.4327
    };
    
    currentGPSCoords = {
      latitude: -5.1477,
      longitude: 119.4327,
      accuracy: null,
      timestamp: new Date().toISOString()
    };
    
    document.getElementById('latitude').value = -5.1477;
    document.getElementById('longitude').value = 119.4327;
    
    getWeatherDataLive(-5.1477, 119.4327);
  }
}

// ==================== GET WEATHER DATA LIVE (OpenWeather API) ====================
async function getWeatherDataLive(lat, lon) {
  try {
    const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&lang=id&appid=${OPENWEATHER_API_KEY}`;
    
    const response = await fetch(url);
    const data = await response.json();
    
    if (data.cod !== 200) {
      throw new Error(data.message || 'Weather API Error');
    }
    
    const temperature = Math.round(data.main.temp);
    const description = data.weather[0].description;
    const humidity = data.main.humidity;
    const iconCode = data.weather[0].icon;
    const weatherIcon = getWeatherIcon(iconCode);
    
    document.getElementById('weatherInfo').innerHTML = `
      <div class="weather-icon">${weatherIcon}</div>
      <div class="weather-details">
        <h5>${temperature}°C - ${description}</h5>
        <p>Kelembaban: ${humidity}% | Lokasi: ${userLocation ? userLocation.city : 'Unknown'}</p>
      </div>
    `;
    
    document.getElementById('cuaca_suhu').value = temperature;
    document.getElementById('cuaca_deskripsi').value = description;
    document.getElementById('cuaca_kelembaban').value = humidity;
    
  } catch (error) {
    console.error('Error getting weather:', error);
    document.getElementById('weatherInfo').innerHTML = `
      <div class="weather-icon">⛅</div>
      <div class="weather-details">
        <h5>Data cuaca tidak tersedia</h5>
        <p>${error.message || 'Gagal mengambil data cuaca'}</p>
      </div>
    `;
  }
}

// ==================== WEATHER ICON MAPPER ====================
function getWeatherIcon(iconCode) {
  const iconMap = {
    '01d': '☀️', '01n': '🌙',
    '02d': '⛅', '02n': '☁️',
    '03d': '☁️', '03n': '☁️',
    '04d': '☁️', '04n': '☁️',
    '09d': '🌧️', '09n': '🌧️',
    '10d': '🌦️', '10n': '🌧️',
    '11d': '⛈️', '11n': '⛈️',
    '13d': '❄️', '13n': '❄️',
    '50d': '🌫️', '50n': '🌫️'
  };
  return iconMap[iconCode] || '⛅';
}

// ==================== PHOTO UPLOAD WITH GPS EMBED ====================
const uploadArea = document.getElementById('uploadArea');
const fotoInput = document.getElementById('fotoInput');
const photoPreview = document.getElementById('photoPreview');

// FIXED: Upload area click handler
uploadArea.addEventListener('click', () => {
  // Check if camera is available
  if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    // Show choice dialog
    showConfirm(
      'Pilih Sumber Foto',
      'Ambil foto menggunakan kamera atau pilih dari galeri?',
      () => openCamera(),      // Confirm = Kamera
      () => fotoInput.click()   // Cancel = Galeri
    );
  } else {
    // No camera support, directly open file picker
    fotoInput.click();
  }
});

uploadArea.addEventListener('dragover', (e) => {
  e.preventDefault();
  uploadArea.style.background = '#e8f4f8';
});

uploadArea.addEventListener('dragleave', () => {
  uploadArea.style.background = '#f8fbfd';
});

uploadArea.addEventListener('drop', (e) => {
  e.preventDefault();
  uploadArea.style.background = '#f8fbfd';
  handleFiles(e.dataTransfer.files);
});

fotoInput.addEventListener('change', (e) => {
  handleFiles(e.target.files);
});

// ==================== OPEN CAMERA - FIXED ====================
async function openCamera() {
  try {
    // Request camera access
    const stream = await navigator.mediaDevices.getUserMedia({ 
      video: { 
        facingMode: 'environment', // Use back camera on mobile
        width: { ideal: 1920 },
        height: { ideal: 1080 }
      } 
    });
    
    // Create video element
    const video = document.createElement('video');
    video.srcObject = stream;
    video.autoplay = true;
    video.playsInline = true; // Important for iOS
    video.style.width = '100%';
    video.style.maxWidth = '600px';
    video.style.borderRadius = '10px';
    
    // Create modal
    const modal = document.createElement('div');
    modal.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.95);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      padding: 20px;
    `;
    
    // Create capture button
    const captureBtn = document.createElement('button');
    captureBtn.innerHTML = '📸 Ambil Foto';
    captureBtn.style.cssText = `
      margin-top: 20px;
      padding: 15px 40px;
      font-size: 18px;
      background: #47b2e4;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    `;
    
    // Create close button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '❌ Tutup';
    closeBtn.style.cssText = `
      margin-top: 10px;
      padding: 10px 30px;
      font-size: 16px;
      background: #6c757d;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    `;
    
    modal.appendChild(video);
    modal.appendChild(captureBtn);
    modal.appendChild(closeBtn);
    document.body.appendChild(modal);
    
    // Capture button click handler
    captureBtn.addEventListener('click', () => {
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0);
      
      canvas.toBlob(async (blob) => {
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        const gpsInfo = currentGPSCoords ? 
          `_GPS_${currentGPSCoords.latitude.toFixed(6)}_${currentGPSCoords.longitude.toFixed(6)}` : 
          '';
        const fileName = `photo_${timestamp}${gpsInfo}.jpg`;
        
        const file = new File([blob], fileName, { type: 'image/jpeg' });
        
        // Attach GPS and weather data
        file.gpsData = currentGPSCoords;
        file.weatherData = {
          temperature: document.getElementById('cuaca_suhu').value,
          description: document.getElementById('cuaca_deskripsi').value,
          humidity: document.getElementById('cuaca_kelembaban').value
        };
        
        uploadedFiles.push(file);
        displayPhoto(file);
        
        // Stop camera stream
        stream.getTracks().forEach(track => track.stop());
        document.body.removeChild(modal);
        
        showSuccess('Berhasil!', 'Foto berhasil diambil dengan GPS coordinates');
      }, 'image/jpeg', 0.9);
    });
    
    // Close button click handler
    closeBtn.addEventListener('click', () => {
      stream.getTracks().forEach(track => track.stop());
      document.body.removeChild(modal);
    });
    
  } catch (error) {
    console.error('Camera error:', error);
    
    let errorMessage = 'Tidak dapat mengakses kamera.';
    if (error.name === 'NotAllowedError') {
      errorMessage = 'Akses kamera ditolak. Silakan izinkan akses kamera di pengaturan browser.';
    } else if (error.name === 'NotFoundError') {
      errorMessage = 'Kamera tidak ditemukan pada perangkat ini.';
    } else if (error.name === 'NotReadableError') {
      errorMessage = 'Kamera sedang digunakan oleh aplikasi lain.';
    }
    
    showAlert('error', 'Kamera Error', errorMessage + ' Gunakan galeri sebagai gantinya.');
    fotoInput.click();
  }
}

// ==================== HANDLE FILES ====================
function handleFiles(files) {
  for (let file of files) {
    if (file.type.startsWith('image/') && file.size <= 5242880) {
      // Attach GPS data if not already attached
      if (currentGPSCoords && !file.gpsData) {
        file.gpsData = currentGPSCoords;
        file.weatherData = {
          temperature: document.getElementById('cuaca_suhu').value,
          description: document.getElementById('cuaca_deskripsi').value,
          humidity: document.getElementById('cuaca_kelembaban').value
        };
      }
      
      uploadedFiles.push(file);
      displayPhoto(file);
    } else {
      showAlert('error', 'File Tidak Valid', 'File harus berupa gambar dan maksimal 5MB!');
    }
  }
}

// ==================== DISPLAY PHOTO ====================
function displayPhoto(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    const div = document.createElement('div');
    div.className = 'preview-item';
    
    const gpsLabel = file.gpsData ? 
      `<small style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.7); color:white; padding:3px; font-size:9px;">
        📍 ${file.gpsData.latitude.toFixed(4)}, ${file.gpsData.longitude.toFixed(4)}
      </small>` : '';
    
    div.innerHTML = `
      <img src="${e.target.result}" alt="Preview">
      ${gpsLabel}
      <button type="button" class="remove-photo" onclick="removePhoto(this, '${file.name}')">
        <i class="bi bi-x"></i>
      </button>
    `;
    photoPreview.appendChild(div);
    photoPreview.classList.add('active');
  };
  reader.readAsDataURL(file);
}

// ==================== REMOVE PHOTO ====================
function removePhoto(btn, fileName) {
  uploadedFiles = uploadedFiles.filter(f => f.name !== fileName);
  btn.parentElement.remove();
  if (uploadedFiles.length === 0) {
    photoPreview.classList.remove('active');
  }
}

// ==================== FORM SUBMIT HANDLER ====================
document.getElementById('progressForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  if (uploadedFiles.length < 2) {
    showAlert('warning', 'Foto Kurang', 'Minimal upload 2 foto dokumentasi!');
    return;
  }

  const formData = new FormData(this);
  
  // Append photos with GPS data
  uploadedFiles.forEach((file, index) => {
    formData.append(`foto_${index}`, file);
    
    if (file.gpsData) {
      formData.append(`foto_${index}_gps_lat`, file.gpsData.latitude);
      formData.append(`foto_${index}_gps_lon`, file.gpsData.longitude);
      formData.append(`foto_${index}_gps_accuracy`, file.gpsData.accuracy || '');
      formData.append(`foto_${index}_gps_timestamp`, file.gpsData.timestamp);
    }
    
    if (file.weatherData) {
      formData.append(`foto_${index}_weather`, JSON.stringify(file.weatherData));
    }
  });

  const submitBtn = this.querySelector('.btn-submit');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim...';
  submitBtn.disabled = true;

  // Show loading
  Swal.fire({
    title: 'Mengirim Laporan...',
    html: 'Mohon tunggu, data sedang diproses',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Simulate API call (replace with actual endpoint)
  fetch('/app/progress-harian/store', {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    }
  })
  .then(response => response.json())
  .then(data => {
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    
    if(data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Laporan progress harian berhasil dikirim dengan GPS & weather data!',
        confirmButtonColor: '#1d6ba8'
      }).then(() => {
        resetForm(false);
      });
    } else {
      showAlert('error', 'Gagal', data.message || 'Terjadi kesalahan saat mengirim laporan');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    showAlert('error', 'Error', 'Gagal mengirim laporan. Silakan coba lagi.');
  });
});

// ==================== RESET FORM ====================
function resetForm(needConfirm = true) {
  if (needConfirm) {
    showConfirm(
      'Konfirmasi',
      'Yakin ingin membatalkan? Semua data akan dihapus.',
      () => {
        document.getElementById('progressForm').reset();
        uploadedFiles = [];
        photoPreview.innerHTML = '';
        photoPreview.classList.remove('active');
        document.getElementById('tanggal').valueAsDate = new Date();
        getUserLocation();
      },
      null
    );
  } else {
    document.getElementById('progressForm').reset();
    uploadedFiles = [];
    photoPreview.innerHTML = '';
    photoPreview.classList.remove('active');
    document.getElementById('tanggal').valueAsDate = new Date();
    getUserLocation();
  }
}

// ==================== INITIALIZE ON LOAD ====================
window.addEventListener('DOMContentLoaded', function() {
  getUserLocation();
  console.log('📍 Pelaporan Progress');
});