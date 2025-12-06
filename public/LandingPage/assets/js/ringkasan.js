let allReports = [];
let filteredReports = [];
let currentPage = 1;
const itemsPerPage = 10;

window.addEventListener("DOMContentLoaded", function () {
  loadReports();
  setupEventListeners();
  
  setInterval(() => {
    console.log('🔄 Auto-refreshing data...');
    loadReports();
  }, 50000);
});

function setupEventListeners() {
  document.getElementById("btnFilter").addEventListener("click", applyFilters);

  ["filterPekerjaan", "filterTanggalMulai", "filterTanggalAkhir"].forEach((id) => {
    document.getElementById(id).addEventListener("keypress", (e) => {
      if (e.key === "Enter") applyFilters();
    });
  });
}

// ==================== LOAD REPORTS FROM API (REAL DATA) ====================
async function loadReports() {
  showLoading(true);

  try {
    const response = await fetch("/landingpage/api/progress-harian", {
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    });

    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      throw new Error("Server tidak mengembalikan JSON. Pastikan route API sudah benar.");
    }

    if (!response.ok) {
      throw new Error(`HTTP Error: ${response.status} - ${response.statusText}`);
    }

    const data = await response.json();

    if (data.success) {
      allReports = data.data;
      filteredReports = [...allReports];

      updateSummaryCards();
      renderTable();
      renderPagination();
    } else {
      throw new Error(data.message || "Gagal memuat data");
    }
  } catch (error) {
    console.error("Error loading reports:", error);
    showErrorMessage(error.message || "Gagal memuat data laporan");
  } finally {
    showLoading(false);
  }
}

function updateSummaryCards() {
  const total = allReports.length;

  const approved = allReports.filter((r) => r.status_approval === "approved").length;
  const pending = allReports.filter((r) => r.status_approval === "pending").length;
  const rejected = allReports.filter((r) => r.status_approval === "rejected").length;

  document.getElementById("totalLaporan").textContent = total;
  document.getElementById("totalDisetujui").textContent = approved;
  document.getElementById("totalPending").textContent = pending;
  document.getElementById("totalRevisi").textContent = rejected;
}

function applyFilters() {
  const pekerjaan = document.getElementById("filterPekerjaan").value;
  const tanggalMulai = document.getElementById("filterTanggalMulai").value;
  const tanggalAkhir = document.getElementById("filterTanggalAkhir").value;

  filteredReports = allReports.filter((report) => {
    if (pekerjaan && report.pekerjaan !== pekerjaan) {
      return false;
    }

    if (tanggalMulai && report.tanggal < tanggalMulai) {
      return false;
    }

    if (tanggalAkhir && report.tanggal > tanggalAkhir) {
      return false;
    }

    return true;
  });

  currentPage = 1;
  renderTable();
  renderPagination();

  Swal.fire({
    icon: "success",
    title: "Filter Diterapkan",
    text: `Menampilkan ${filteredReports.length} dari ${allReports.length} laporan`,
    timer: 2000,
    showConfirmButton: false,
  });
}

// ==================== RENDER TABLE ====================
function renderTable() {
  const tbody = document.querySelector("#tableReports tbody");

  if (filteredReports.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center py-4">
          <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
          <p class="text-muted mt-2">Tidak ada data laporan</p>
        </td>
      </tr>
    `;
    return;
  }

  const start = (currentPage - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const paginatedData = filteredReports.slice(start, end);

  tbody.innerHTML = paginatedData
    .map(
      (report) => `
    <tr>
      <td>
        <div class="fw-semibold">${formatDate(report.tanggal)}</div>
        <small class="text-muted">${report.tanggal}</small>
      </td>
      <td>
        <div class="fw-semibold" style="color: #2c5aa0;">${report.kode_pekerjaan}</div>
        <small class="text-muted">${report.nama_pekerjaan}</small>
      </td>
      <td>
        <div class="fw-semibold">${report.pelapor}</div>
      </td>
      <td>
        <div class="fw-semibold" style="font-size: 0.9rem;">
          <i class="bi bi-geo-alt-fill text-primary"></i>
          ${report.lokasi_nama}
        </div>
        <small class="text-muted" style="font-size: 0.75rem;">
          ${report.latitude.toFixed(4)}, ${report.longitude.toFixed(4)}
        </small>
      </td>
      <td>
        ${getStatusBadge(report.status_approval)}
      </td>
      <td class="text-center">
        <div class="dropdown">
          <button class="btn action-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end action-dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="viewDetail(${report.id}); return false;">
              <i class="bi bi-eye-fill"></i> Lihat Detail
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" onclick="exportPdf(${report.id}); return false;">
              <i class="bi bi-file-pdf-fill"></i> Export PDF
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="/landingpage/pelaporanform-edit?id=${report.id}">
              <i class="bi bi-pencil-fill"></i> Edit
            </a></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="deleteReport(${report.id}); return false;">
              <i class="bi bi-trash-fill"></i> Hapus
            </a></li>
          </ul>
        </div>
      </td>
    </tr>
  `
    )
    .join("");
}


// ==================== EXPORT PDF ====================
function exportPdf(id) {
  console.log('Exporting PDF for report ID:', id);
  
  Swal.fire({
    title: 'Export PDF',
    text: 'Mengunduh laporan dalam format PDF...',
    icon: 'info',
    showConfirmButton: false,
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Create temporary link for download
  const downloadUrl = `/landingpage/laporan/${id}/export-pdf`;
  const link = document.createElement('a');
  link.href = downloadUrl;
  link.download = `Laporan_${id}.pdf`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  
  setTimeout(() => {
    Swal.close();
    Swal.fire({
      title: 'Berhasil!',
      text: 'PDF berhasil diunduh',
      icon: 'success',
      timer: 2000,
      showConfirmButton: false
    });
  }, 1500);
}

// ==================== UPDATE VIEW DETAIL MODAL ====================
function viewDetail(reportId) {
  const report = allReports.find((r) => r.id === reportId);

  if (!report) {
    Swal.fire("Error", "Laporan tidak ditemukan", "error");
    return;
  }

  const weatherInfo = report.cuaca_deskripsi
    ? `${report.cuaca_deskripsi}, ${report.cuaca_suhu}°C, Kelembaban: ${report.cuaca_kelembaban}%`
    : "Data tidak tersedia";

  let photosHtml = "";
  if (report.fotos && report.fotos.length > 0) {
    photosHtml = '<div class="row g-2 mt-2">';
    report.fotos.forEach((foto) => {
      photosHtml += `
        <div class="col-4">
          <img src="${foto.url}" class="img-fluid rounded" alt="Foto" style="cursor: pointer;" onclick="window.open('${foto.url}', '_blank')">
        </div>
      `;
    });
    photosHtml += "</div>";
  } else {
    photosHtml = '<p class="text-muted">Tidak ada foto</p>';
  }

  Swal.fire({
    title: "<strong>Detail Laporan Progress Harian</strong>",
    html: `
      <div class="text-start">
        <div class="mb-3">
          <strong>Tanggal:</strong> ${formatDate(report.tanggal)}<br>
          <strong>Pelapor:</strong> ${report.pelapor}<br>
          <strong>Proyek:</strong> ${report.nama_proyek || '-'}<br>
          <strong>Item Pekerjaan:</strong> ${report.kode_pekerjaan} - ${report.nama_pekerjaan}<br>
          <strong>Status:</strong> ${getStatusBadge(report.status_approval)}
        </div>
        <hr>
        <div class="mb-3">
          <strong>Lokasi:</strong><br>
          <i class="bi bi-geo-alt-fill text-primary"></i> ${report.lokasi_nama}<br>
          <small class="text-muted">
            Koordinat: ${report.latitude.toFixed(6)}, ${report.longitude.toFixed(6)}
          </small>
        </div>
        <hr>
        <div class="mb-3">
          <strong>Deskripsi Pekerjaan:</strong><br>
          ${report.jenis_pekerjaan || report.deskripsi}
        </div>
        <div class="mb-3">
          <strong>Volume:</strong> ${report.volume || "-"} ${report.satuan || ""}
        </div>
        <hr>
        <div class="mb-3">
          <strong>Cuaca:</strong> ${weatherInfo}<br>
          <strong>Jam Kerja:</strong> ${report.jam_kerja || "-"} jam<br>
          <strong>Kondisi Lapangan:</strong> ${report.kondisi_lapangan || "-"}
        </div>
        ${report.kendala ? `<div class="mb-3"><strong>Kendala:</strong><br>${report.kendala}</div>` : ""}
        ${report.solusi ? `<div class="mb-3"><strong>Solusi:</strong><br>${report.solusi}</div>` : ""}
        <hr>
        <div class="mb-3">
          <strong>Rencana Besok:</strong><br>
          ${report.rencana_besok}
        </div>
        <hr>
        <div>
          <strong>Dokumentasi Foto:</strong>
          ${photosHtml}
        </div>
      </div>
    `,
    width: "800px",
    showCancelButton: true,
    confirmButtonText: '<i class="bi bi-printer"></i> Print',
    cancelButtonText: 'Tutup',
    confirmButtonColor: "#2c5aa0",
    cancelButtonColor: "#6c757d",
  }).then((result) => {
    if (result.isConfirmed) {
      printLaporan(reportId);
    }
  });
}

// ==================== GET STATUS BADGE (FIXED) ====================
function getStatusBadge(status) {
  const statusConfig = {
    'approved': {
      class: 'badge bg-success',
      icon: 'bi-check-circle-fill',
      text: 'Disetujui'
    },
    'pending': {
      class: 'badge bg-warning text-dark',
      icon: 'bi-clock-fill',
      text: 'Menunggu Approval'
    },
    'rejected': {
      class: 'badge bg-danger',
      icon: 'bi-x-circle-fill',
      text: 'Ditolak'
    }
  };

  const config = statusConfig[status] || statusConfig['pending'];

  return `
    <span class="${config.class}">
      <i class="bi ${config.icon}"></i> ${config.text}
    </span>
  `;
}

// ==================== RENDER PAGINATION ====================
function renderPagination() {
  const totalPages = Math.ceil(filteredReports.length / itemsPerPage);
  const pagination = document.getElementById("pagination");

  if (totalPages <= 1) {
    pagination.innerHTML = "";
    return;
  }

  let html = "";

  html += `
    <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
      <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">Sebelumnya</a>
    </li>
  `;

  for (let i = 1; i <= totalPages; i++) {
    if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
      html += `
        <li class="page-item ${i === currentPage ? "active" : ""}">
          <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
        </li>
      `;
    } else if (i === currentPage - 2 || i === currentPage + 2) {
      html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }
  }

  html += `
    <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
      <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Selanjutnya</a>
    </li>
  `;

  pagination.innerHTML = html;
}

// ==================== CHANGE PAGE ====================
function changePage(page) {
  const totalPages = Math.ceil(filteredReports.length / itemsPerPage);
  if (page < 1 || page > totalPages) return;

  currentPage = page;
  renderTable();
  renderPagination();
  window.scrollTo({ top: 0, behavior: "smooth" });
}

// ==================== DELETE REPORT ====================
function deleteReport(reportId) {
  Swal.fire({
    title: "Hapus Laporan?",
    text: "Laporan yang dihapus tidak dapat dikembalikan!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      performDelete(reportId);
    }
  });
}

async function performDelete(reportId) {
  Swal.fire({
    title: "Menghapus...",
    html: "Mohon tunggu",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  try {
    const response = await fetch(`/landingpage/api/progress-harian/${reportId}`, {
      method: "DELETE",
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    });

    const data = await response.json();

    if (data.success) {
      Swal.fire({
        icon: "success",
        title: "Terhapus!",
        text: "Laporan berhasil dihapus",
        timer: 2000,
        showConfirmButton: false,
      });

      loadReports();
    } else {
      throw new Error(data.message || "Gagal menghapus laporan");
    }
  } catch (error) {
    console.error("Error deleting report:", error);
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: error.message || "Terjadi kesalahan saat menghapus laporan",
    });
  }
}

// ==================== HELPER FUNCTIONS ====================
function formatDate(dateString) {
  const date = new Date(dateString);
  const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
  return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

function getPekerjaanName(value) {
  const names = {
    revitalisasi_pelabuhan: "Pekerjaan Revitalisasi Pelabuhan",
    pembangunan_dermaga: "Pembangunan Dermaga Baru",
    renovasi_gudang: "Renovasi Gudang Logistik",
    instalasi_crane: "Instalasi Crane Container",
    perbaikan_jalan: "Perbaikan Jalan Akses",
  };
  return names[value] || value;
}

function showLoading(show) {
  const tbody = document.querySelector("#tableReports tbody");
  if (show) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Memuat data...</p>
        </td>
      </tr>
    `;
  }
}

function showErrorMessage(message) {
  const tbody = document.querySelector("#tableReports tbody");
  tbody.innerHTML = `
    <tr>
      <td colspan="6" class="text-center py-4">
        <i class="bi bi-exclamation-triangle" style="font-size: 48px; color: #dc3545;"></i>
        <p class="text-danger mt-2">${message}</p>
        <button class="btn btn-primary btn-sm" onclick="loadReports()">
          <i class="bi bi-arrow-clockwise"></i> Coba Lagi
        </button>
      </td>
    </tr>
  `;
}