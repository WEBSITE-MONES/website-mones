document.getElementById('tanggal').valueAsDate = new Date();
let userLocation = null;
let uploadedFiles = [];
let currentGPSCoords = null;
let currentLocationName = null;

function showAlert(icon, title, text) {
  Swal.fire({
    icon: icon,
    title: title,
    text: text,
    confirmButtonColor: '#2c5aa0',
    confirmButtonText: 'OK'
  });
}

function showConfirm(title, text, confirmCallback, cancelCallback) {
  Swal.fire({
    title: title,
    text: text,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#2c5aa0',
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
    confirmButtonColor: '#2c5aa0',
    timer: 3000
  });
}

async function getLocationNameFromCoords(lat, lon) {
  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1&accept-language=id`,
      {
        headers: {
          'User-Agent': 'P-Mones-App/1.0'
        }
      }
    );
    
    if (response.ok) {
      const data = await response.json();
      const address = data.address || {};
      
      const parts = [];
      
      if (address.road || address.street) {
        parts.push(address.road || address.street);
      }
      
      if (address.suburb || address.village || address.neighbourhood) {
        parts.push(address.suburb || address.village || address.neighbourhood);
      }
      
      if (address.city || address.town || address.county) {
        parts.push(address.city || address.town || address.county);
      }
      
      if (address.state) {
        parts.push(address.state);
      }
      
      if (parts.length > 0) {
        return parts.join(', ');
      }
      
      if (data.display_name) {
        return data.display_name.length > 80 
          ? data.display_name.substring(0, 77) + '...' 
          : data.display_name;
      }
    }
    
  } catch (error) {
    console.error('Reverse geocoding error:', error);
  }
  
  // Fallback ke koordinat
  return `${lat.toFixed(4)}, ${lon.toFixed(4)}`;
}

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
      
      console.log('🔍 Mencari nama lokasi...');
      currentLocationName = await getLocationNameFromCoords(lat, lon);
      console.log('📍 Lokasi ditemukan:', currentLocationName);
      
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
            longitude: lon,
            locationName: currentLocationName 
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

// ==================== FALLBACK LOCATION  ====================
async function useFallbackLocation() {
  try {
    const response = await fetch('https://ipapi.co/json/');
    const data = await response.json();
    
    if (data.error) {
      throw new Error(data.reason || 'IP API Error');
    }
    
    const lat = data.latitude || -5.1477;
    const lon = data.longitude || 119.4327;

    currentLocationName = await getLocationNameFromCoords(lat, lon);
    
    userLocation = {
      city: data.city || 'Makassar',
      region: data.region || 'Sulawesi Selatan',
      country: data.country_name || 'Indonesia',
      latitude: lat,
      longitude: lon,
      locationName: currentLocationName
    };
    
    currentGPSCoords = {
      latitude: lat,
      longitude: lon,
      accuracy: null,
      timestamp: new Date().toISOString()
    };
    
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lon;
    
    getWeatherDataLive(lat, lon);
    
  } catch (error) {
    console.error('Fallback location error:', error);
    
    const lat = -5.1477;
    const lon = 119.4327;
    
    currentLocationName = await getLocationNameFromCoords(lat, lon);
    
    userLocation = {
      city: 'Makassar',
      region: 'Sulawesi Selatan',
      country: 'Indonesia',
      latitude: lat,
      longitude: lon,
      locationName: currentLocationName
    };
    
    currentGPSCoords = {
      latitude: lat,
      longitude: lon,
      accuracy: null,
      timestamp: new Date().toISOString()
    };
    
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lon;
    
    getWeatherDataLive(lat, lon);
  }
}

async function getWeatherDataLive(lat, lon) {
  try {
    const url = `/dashboard/sidebar-weather?lat=${lat}&lon=${lon}`;
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      }
    });

    if (!response.ok) {
      throw new Error(`HTTP Error: ${response.status}`);
    }
    
    const data = await response.json();
    
    // Check if error response
    if (data.error) {
      throw new Error(data.error);
    }
    
    const temperature = Math.round(data.temperature);
    const description = data.description;
    const humidity = data.humidity;
    const iconCode = data.icon;
    const weatherIcon = getWeatherIcon(iconCode);

    const displayLocation = currentLocationName || (userLocation ? userLocation.city : 'Unknown');
    
    document.getElementById('weatherInfo').innerHTML = `
      <div class="weather-icon">${weatherIcon}</div>
      <div class="weather-details">
        <h5>${temperature}°C - ${description}</h5>
        <p>Kelembaban: ${humidity}% |  ${displayLocation}</p>
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

uploadArea.addEventListener('click', () => {
  if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    showConfirm(
      'Pilih Sumber Foto',
      'Ambil foto menggunakan kamera atau pilih dari galeri?',
      () => openCamera(),
      () => fotoInput.click()
    );
  } else {
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

// ==================== OPEN CAMERA ====================
async function openCamera() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ 
      video: { 
        facingMode: 'environment',
        width: { ideal: 1920 },
        height: { ideal: 1080 }
      } 
    });
    
    const video = document.createElement('video');
    video.srcObject = stream;
    video.autoplay = true;
    video.playsInline = true;
    video.style.width = '100%';
    video.style.maxWidth = '600px';
    video.style.borderRadius = '10px';
    
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
    
    const captureBtn = document.createElement('button');
    captureBtn.innerHTML = '📸 Ambil Foto';
    captureBtn.style.cssText = `
      margin-top: 20px;
      padding: 15px 40px;
      font-size: 18px;
      background: #2c5aa0;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s ease;
    `;
    
    captureBtn.addEventListener('mouseenter', () => {
      captureBtn.style.background = '#1d4278';
    });
    
    captureBtn.addEventListener('mouseleave', () => {
      captureBtn.style.background = '#2c5aa0';
    });
    
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = ' Tutup';
    closeBtn.style.cssText = `
      margin-top: 10px;
      padding: 10px 30px;
      font-size: 16px;
      background: #6c757d;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    `;
    
    closeBtn.addEventListener('mouseenter', () => {
      closeBtn.style.background = '#5a6268';
    });
    
    closeBtn.addEventListener('mouseleave', () => {
      closeBtn.style.background = '#6c757d';
    });
    
    modal.appendChild(video);
    modal.appendChild(captureBtn);
    modal.appendChild(closeBtn);
    document.body.appendChild(modal);
    
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
        
        file.gpsData = currentGPSCoords;
        file.locationName = currentLocationName;
        file.weatherData = {
          temperature: document.getElementById('cuaca_suhu').value,
          description: document.getElementById('cuaca_deskripsi').value,
          humidity: document.getElementById('cuaca_kelembaban').value
        };
        
        uploadedFiles.push(file);
        displayPhoto(file);
        
        stream.getTracks().forEach(track => track.stop());
        document.body.removeChild(modal);
        
        showSuccess('Berhasil!', `Foto berhasil diambil di ${currentLocationName || 'lokasi saat ini'}`);
      }, 'image/jpeg', 0.9);
    });
    
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

async function handleFiles(files) {
  for (let file of files) {
    if (file.type.startsWith('image/') && file.size <= 5242880) {
      if (currentGPSCoords && !file.gpsData) {
        file.gpsData = currentGPSCoords;
        file.locationName = currentLocationName; 
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

function displayPhoto(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    const div = document.createElement('div');
    div.className = 'preview-item';
    
    const gpsLabel = file.locationName ? 
      `<small style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.8); color:white; padding:5px; font-size:10px; line-height:1.3;">
        ${file.locationName}
      </small>` : 
      (file.gpsData ? 
        `<small style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.7); color:white; padding:3px; font-size:9px;">
          ${file.gpsData.latitude.toFixed(4)}, ${file.gpsData.longitude.toFixed(4)}
        </small>` : '');
    
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

function removePhoto(btn, fileName) {
  uploadedFiles = uploadedFiles.filter(f => f.name !== fileName);
  btn.parentElement.remove();
  if (uploadedFiles.length === 0) {
    photoPreview.classList.remove('active');
  }
}

document.getElementById('progressForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Validasi foto minimal 2
  if (uploadedFiles.length < 2) {
    showAlert('warning', 'Foto Kurang', 'Minimal upload 2 foto dokumentasi!');
    return;
  }

  if (!document.getElementById('latitude').value || !document.getElementById('longitude').value) {
    showAlert('error', 'GPS Belum Siap', 'Mohon tunggu hingga GPS coordinates terdeteksi!');
    return;
  }

  const formData = new FormData(this);
  if (currentLocationName) {
    formData.append('location_name', currentLocationName);
  }
  
  // Append photos with GPS data
  uploadedFiles.forEach((file, index) => {
    formData.append(`foto_${index}`, file);
    
    if (file.gpsData) {
      formData.append(`foto_${index}_gps_lat`, file.gpsData.latitude);
      formData.append(`foto_${index}_gps_lon`, file.gpsData.longitude);
      formData.append(`foto_${index}_gps_accuracy`, file.gpsData.accuracy || '');
      formData.append(`foto_${index}_gps_timestamp`, file.gpsData.timestamp);
    }
    
    if (file.locationName) {
      formData.append(`foto_${index}_location_name`, file.locationName);
    }
    
    if (file.weatherData) {
      formData.append(`foto_${index}_weather`, JSON.stringify(file.weatherData));
    }
  });

  const submitBtn = this.querySelector('.btn-submit');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim...';
  submitBtn.disabled = true;

  Swal.fire({
    title: 'Mengirim Laporan...',
    html: 'Mohon tunggu, data sedang diproses',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  fetch('/landingpage/api/progress-harian/store', {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    }
  })
  .then(response => {
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      throw new Error('Server tidak mengembalikan JSON');
    }
    return response.json();
  })
  .then(data => {
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    
    if(data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: `Laporan berhasil dikirim dari ${currentLocationName || 'lokasi Anda'}!`,
        confirmButtonColor: '#2c5aa0'
      }).then(() => {
        window.location.href = '/landingpage/pelaporan';
      });
    } else {
      throw new Error(data.message || 'Terjadi kesalahan saat mengirim laporan');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error.message || 'Terjadi kesalahan saat mengirim laporan. Silakan coba lagi.'
    });
  });
});

function resetForm(needConfirm = true) {
  if (needConfirm) {
    Swal.fire({
      title: 'Konfirmasi',
      text: 'Yakin ingin membatalkan? Semua data akan dihapus.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#2c5aa0',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Batalkan',
      cancelButtonText: 'Tidak'
    }).then((result) => {
      if (result.isConfirmed) {
        performReset();
      }
    });
  } else {
    performReset();
  }
}

function performReset() {
  document.getElementById('progressForm').reset();
  uploadedFiles = [];
  photoPreview.innerHTML = '';
  photoPreview.classList.remove('active');
  document.getElementById('tanggal').valueAsDate = new Date();
  getUserLocation();
}

// ==================== INITIALIZE ON LOAD ====================
window.addEventListener('DOMContentLoaded', function() {
  console.log('📍 Initializing Pelaporan Progress (Secure Mode)...');
  getUserLocation(); 
  console.log('GPS & Weather data loading via secure backend...');
});