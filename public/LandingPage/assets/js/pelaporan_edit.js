// ==================== PELAPORAN EDIT JS ====================

// Global variables
let uploadedNewFiles = [];
let reportId = null;
let deletedPhotoIds = [];

// ==================== GET REPORT ID FROM URL ====================
function getReportIdFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get('id');
}

// ==================== LOAD REPORT DATA ====================
async function loadReportData() {
  reportId = getReportIdFromURL();
  
  if (!reportId) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'ID Laporan tidak ditemukan!'
    }).then(() => {
      window.location.href = 'ringkasan.html';
    });
    return;
  }

  // Show loading
  Swal.fire({
    title: 'Memuat Data...',
    html: 'Mohon tunggu',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  try {
    // Fetch data from API
    const response = await fetch(`/app/progress-harian/${reportId}`);
    const data = await response.json();

    if (!data.success) {
      throw new Error(data.message || 'Gagal memuat data');
    }

    // Populate form with data
    populateForm(data.data);
    
    Swal.close();

  } catch (error) {
    console.error('Error loading report:', error);
    Swal.fire({
      icon: 'error',
      title: 'Gagal Memuat Data',
      text: error.message || 'Terjadi kesalahan saat memuat data laporan'
    }).then(() => {
      window.location.href = 'ringkasan.html';
    });
  }
}

// ==================== POPULATE FORM ====================
function populateForm(data) {
  // Report ID
  document.getElementById('reportId').value = data.id;

  // Informasi Dasar
  document.getElementById('tanggal').value = data.tanggal;
  document.getElementById('pelapor').value = data.pelapor;
  document.getElementById('pekerjaan').value = data.pekerjaan;

  // GPS Location (readonly)
  document.getElementById('latitude').value = data.latitude;
  document.getElementById('longitude').value = data.longitude;
  document.getElementById('locationDetail').textContent = 
    `Lat: ${data.latitude}, Lon: ${data.longitude} - ${data.lokasi_nama || 'Unknown'}`;

  // Progress Pekerjaan
  document.getElementById('jenis_pekerjaan').value = data.jenis_pekerjaan;
  document.getElementById('volume').value = data.volume || '';
  document.getElementById('satuan').value = data.satuan || '';
  document.getElementById('deskripsi').value = data.deskripsi;

  // Sumber Daya
  document.getElementById('jumlah_pekerja').value = data.jumlah_pekerja || '';
  document.getElementById('alat_berat').value = data.alat_berat || '';
  document.getElementById('material').value = data.material || '';

  // Cuaca & Lapangan
  document.getElementById('cuaca_suhu').value = data.cuaca_suhu;
  document.getElementById('cuaca_deskripsi').value = data.cuaca_deskripsi;
  document.getElementById('cuaca_kelembaban').value = data.cuaca_kelembaban;
  document.getElementById('savedWeather').textContent = 
    `${data.cuaca_deskripsi}, ${data.cuaca_suhu}°C, Kelembaban: ${data.cuaca_kelembaban}%`;

  document.getElementById('jam_kerja').value = data.jam_kerja || '';
  document.getElementById('kondisi_lapangan').value = data.kondisi_lapangan || 'normal';
  document.getElementById('kendala').value = data.kendala || '';
  document.getElementById('solusi').value = data.solusi || '';

  // Rencana Besok
  document.getElementById('rencana_besok').value = data.rencana_besok;

  // Load existing photos
  if (data.fotos && data.fotos.length > 0) {
    loadExistingPhotos(data.fotos);
  } else {
    document.getElementById('existingPhotoPreview').innerHTML = 
      '<p class="text-muted">Belum ada foto</p>';
  }
}

// ==================== LOAD EXISTING PHOTOS ====================
function loadExistingPhotos(photos) {
  const container = document.getElementById('existingPhotoPreview');
  container.innerHTML = '';

  photos.forEach(photo => {
    const div = document.createElement('div');
    div.className = 'preview-item';
    div.dataset.photoId = photo.id;
    
    const gpsLabel = photo.gps_lat && photo.gps_lon ? 
      `<small style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.7); color:white; padding:3px; font-size:9px;">
        📍 ${parseFloat(photo.gps_lat).toFixed(4)}, ${parseFloat(photo.gps_lon).toFixed(4)}
      </small>` : '';
    
    div.innerHTML = `
      <img src="${photo.url}" alt="Photo">
      ${gpsLabel}
      <button type="button" class="remove-photo" onclick="deleteExistingPhoto(${photo.id}, this)">
        <i class="bi bi-x"></i>
      </button>
    `;
    
    container.appendChild(div);
  });
}

// ==================== DELETE EXISTING PHOTO ====================
function deleteExistingPhoto(photoId, btn) {
  Swal.fire({
    title: 'Hapus Foto?',
    text: 'Foto akan dihapus setelah Anda menyimpan perubahan',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      // Mark for deletion
      deletedPhotoIds.push(photoId);
      
      // Remove from UI
      btn.parentElement.remove();
      
      // Check if no photos left
      const container = document.getElementById('existingPhotoPreview');
      if (container.children.length === 0) {
        container.innerHTML = '<p class="text-muted">Belum ada foto</p>';
      }

      Swal.fire({
        icon: 'success',
        title: 'Ditandai untuk Dihapus',
        text: 'Foto akan dihapus setelah Anda menyimpan perubahan',
        timer: 2000,
        showConfirmButton: false
      });
    }
  });
}

// ==================== NEW PHOTO UPLOAD ====================
const uploadArea = document.getElementById('uploadArea');
const fotoInput = document.getElementById('fotoInput');
const photoPreview = document.getElementById('photoPreview');

uploadArea.addEventListener('click', () => fotoInput.click());

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
  handleNewFiles(e.dataTransfer.files);
});

fotoInput.addEventListener('change', (e) => {
  handleNewFiles(e.target.files);
});

function handleNewFiles(files) {
  for (let file of files) {
    if (file.type.startsWith('image/') && file.size <= 5242880) {
      uploadedNewFiles.push(file);
      displayNewPhoto(file);
    } else {
      Swal.fire({
        icon: 'error',
        title: 'File Tidak Valid',
        text: 'File harus berupa gambar dan maksimal 5MB!'
      });
    }
  }
}

function displayNewPhoto(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    const div = document.createElement('div');
    div.className = 'preview-item';
    div.innerHTML = `
      <img src="${e.target.result}" alt="Preview">
      <span style="position:absolute; top:5px; left:5px; background:rgba(0,200,0,0.8); color:white; padding:2px 6px; font-size:10px; border-radius:3px;">BARU</span>
      <button type="button" class="remove-photo" onclick="removeNewPhoto(this, '${file.name}')">
        <i class="bi bi-x"></i>
      </button>
    `;
    photoPreview.appendChild(div);
    photoPreview.classList.add('active');
  };
  reader.readAsDataURL(file);
}

function removeNewPhoto(btn, fileName) {
  uploadedNewFiles = uploadedNewFiles.filter(f => f.name !== fileName);
  btn.parentElement.remove();
  if (uploadedNewFiles.length === 0) {
    photoPreview.classList.remove('active');
  }
}

// ==================== FORM SUBMIT (UPDATE) ====================
document.getElementById('progressEditForm').addEventListener('submit', function(e) {
  e.preventDefault();

  // Collect form data
  const formData = new FormData(this);
  
  // Add deleted photo IDs
  deletedPhotoIds.forEach(id => {
    formData.append('deleted_photos[]', id);
  });

  // Add new photos
  uploadedNewFiles.forEach((file, index) => {
    formData.append(`new_photos[${index}]`, file);
  });

  // Show loading
  const submitBtn = this.querySelector('.btn-submit');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
  submitBtn.disabled = true;

  Swal.fire({
    title: 'Menyimpan Perubahan...',
    html: 'Mohon tunggu',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Send UPDATE request
  fetch(`/app/progress-harian/${reportId}`, {
    method: 'PUT', // atau 'POST' dengan _method: 'PUT' untuk Laravel
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    }
  })
  .then(response => {
    // Check if response is JSON
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
        text: 'Perubahan berhasil disimpan',
        confirmButtonColor: '#1d6ba8'
      }).then(() => {
        window.location.href = 'ringkasan.html';
      });
    } else {
      throw new Error(data.message || 'Gagal menyimpan perubahan');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error.message || 'Terjadi kesalahan saat menyimpan'
    });
  });
});

// ==================== DELETE REPORT ====================
document.getElementById('deleteBtn').addEventListener('click', function() {
  Swal.fire({
    title: 'Hapus Laporan?',
    text: 'Laporan yang dihapus tidak dapat dikembalikan!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      performDelete();
    }
  });
});

async function performDelete() {
  Swal.fire({
    title: 'Menghapus...',
    html: 'Mohon tunggu',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });
  
  try {
    const response = await fetch(`/app/progress-harian/${reportId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json'
      }
    });
    
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      throw new Error('Server tidak mengembalikan JSON');
    }
    
    const data = await response.json();
    
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Terhapus!',
        text: 'Laporan berhasil dihapus',
        confirmButtonColor: '#1d6ba8'
      }).then(() => {
        window.location.href = 'ringkasan.html';
      });
    } else {
      throw new Error(data.message || 'Gagal menghapus laporan');
    }
    
  } catch (error) {
    console.error('Error deleting:', error);
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error.message || 'Terjadi kesalahan saat menghapus'
    });
  }
}

// ==================== INITIALIZE ON LOAD ====================
window.addEventListener('DOMContentLoaded', function() {
  loadReportData();
  console.log('📝 Edit Laporan - Ready! 🚀');
});