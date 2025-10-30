// ==================== RINGKASAN LAPORAN JS ====================

// Global variables
let allReports = [];
let filteredReports = [];
let currentPage = 1;
const itemsPerPage = 10;

// ==================== INITIALIZE ====================
window.addEventListener("DOMContentLoaded", function () {
  loadReports();
  setupEventListeners();
});

// ==================== SETUP EVENT LISTENERS ====================
function setupEventListeners() {
  // Filter button
  document.getElementById("btnFilter").addEventListener("click", applyFilters);

  // Enter key on filter inputs
  ["filterPekerjaan", "filterTanggalMulai", "filterTanggalAkhir"].forEach(
    (id) => {
      document.getElementById(id).addEventListener("keypress", (e) => {
        if (e.key === "Enter") applyFilters();
      });
    }
  );
}

// ==================== LOAD REPORTS FROM API ====================
async function loadReports() {
  showLoading(true);

  try {
    const response = await fetch("/app/progress-harian", {
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    });

    // Check if response is JSON
    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      throw new Error(
        "Server tidak mengembalikan JSON. Pastikan route API sudah benar."
      );
    }

    if (!response.ok) {
      throw new Error(
        `HTTP Error: ${response.status} - ${response.statusText}`
      );
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

    // Jika route belum ada, gunakan dummy data
    if (
      error.message.includes("Failed to fetch") ||
      error.message.includes("tidak mengembalikan JSON")
    ) {
      console.warn(
        "⚠️ API belum tersedia, menggunakan dummy data untuk testing"
      );
      loadDummyData();
    } else {
      showErrorMessage(error.message || "Gagal memuat data laporan");
    }
  } finally {
    showLoading(false);
  }
}

// ==================== LOAD DUMMY DATA (untuk testing) ====================
function loadDummyData() {
  allReports = [
    {
      id: 1,
      tanggal: "2025-10-28",
      pelapor: "Budi Santoso",
      pekerjaan: "pembangunan_dermaga",
      jenis_pekerjaan: "Pengecoran Pile Cap P-10",
      volume: 12.5,
      satuan: "m³",
      deskripsi: "Pekerjaan pengecoran Pile Cap P-10 selesai 100%",
      latitude: -5.1198,
      longitude: 119.4305,
      cuaca_suhu: 29,
      cuaca_deskripsi: "Berawan",
      cuaca_kelembaban: 80,
      jam_kerja: 7.5,
      kondisi_lapangan: "normal",
      kendala: "Material terlambat 2 jam",
      solusi: "Koordinasi ulang dengan supplier",
      rencana_besok: "Melanjutkan pembesian P-11",
      jumlah_pekerja: 15,
      alat_berat: "Concrete Pump, Mixer",
      material: "Semen 50 sak, Pasir 5 m³",
      status: "approved",
      fotos: [
        { id: 1, url: "https://placehold.co/400x300/1d6ba8/fff?text=Foto+1" },
        { id: 2, url: "https://placehold.co/400x300/2b7ab5/fff?text=Foto+2" },
      ],
    },
    {
      id: 2,
      tanggal: "2025-10-28",
      pelapor: "Citra Lestari",
      pekerjaan: "revitalisasi_pelabuhan",
      jenis_pekerjaan: "Pemasangan Fender",
      volume: 8,
      satuan: "unit",
      deskripsi: "Pemasangan fender type A selesai",
      latitude: -5.1477,
      longitude: 119.4327,
      cuaca_suhu: 31,
      cuaca_deskripsi: "Cerah",
      cuaca_kelembaban: 75,
      jam_kerja: 8,
      kondisi_lapangan: "normal",
      kendala: null,
      solusi: null,
      rencana_besok: "Lanjut pemasangan bollard",
      jumlah_pekerja: 10,
      alat_berat: "Crane 20 ton",
      material: "Fender type A: 8 unit",
      status: "submitted",
      fotos: [],
    },
    {
      id: 3,
      tanggal: "2025-10-27",
      pelapor: "Andi Wijaya",
      pekerjaan: "renovasi_gudang",
      jenis_pekerjaan: "Pengecatan Dinding",
      volume: 250,
      satuan: "m²",
      deskripsi: "Pengecatan dinding gudang bagian utara",
      latitude: -5.152,
      longitude: 119.428,
      cuaca_suhu: 28,
      cuaca_deskripsi: "Berawan",
      cuaca_kelembaban: 82,
      jam_kerja: 6,
      kondisi_lapangan: "becek",
      kendala: "Hujan ringan menghambat pekerjaan",
      solusi: "Menunggu cuaca membaik",
      rencana_besok: "Melanjutkan pengecatan",
      jumlah_pekerja: 8,
      alat_berat: null,
      material: "Cat 20 liter",
      status: "revision",
      fotos: [],
    },
    {
      id: 4,
      tanggal: "2025-10-26",
      pelapor: "Budi Santoso",
      pekerjaan: "pembangunan_dermaga",
      jenis_pekerjaan: "Pembesian Pile Cap",
      volume: 150,
      satuan: "kg",
      deskripsi: "Pembesian pile cap P-9 selesai",
      latitude: -5.1198,
      longitude: 119.4305,
      cuaca_suhu: 30,
      cuaca_deskripsi: "Cerah",
      cuaca_kelembaban: 70,
      jam_kerja: 8,
      kondisi_lapangan: "normal",
      kendala: null,
      solusi: null,
      rencana_besok: "Persiapan bekisting",
      jumlah_pekerja: 12,
      alat_berat: null,
      material: "Besi D16: 100 batang",
      status: "approved",
      fotos: [],
    },
  ];

  filteredReports = [...allReports];

  updateSummaryCards();
  renderTable();
  renderPagination();

  // Show info that using dummy data
  Swal.fire({
    icon: "info",
    title: "Mode Testing",
    text: "Menggunakan data dummy karena API belum tersedia",
    timer: 3000,
    showConfirmButton: false,
  });
}

// ==================== UPDATE SUMMARY CARDS ====================
function updateSummaryCards() {
  const total = allReports.length;
  const approved = allReports.filter((r) => r.status === "approved").length;
  const pending = allReports.filter((r) => r.status === "submitted").length;
  const revision = allReports.filter((r) => r.status === "revision").length;

  document.getElementById("totalLaporan").textContent = total;
  document.getElementById("totalDisetujui").textContent = approved;
  document.getElementById("totalPending").textContent = pending;
  document.getElementById("totalRevisi").textContent = revision;
}

// ==================== APPLY FILTERS ====================
function applyFilters() {
  const pekerjaan = document.getElementById("filterPekerjaan").value;
  const tanggalMulai = document.getElementById("filterTanggalMulai").value;
  const tanggalAkhir = document.getElementById("filterTanggalAkhir").value;

  filteredReports = allReports.filter((report) => {
    // Filter by pekerjaan
    if (pekerjaan && report.pekerjaan !== pekerjaan) {
      return false;
    }

    // Filter by tanggal
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

  // Show filter result message
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

  // Pagination
  const start = (currentPage - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const paginatedData = filteredReports.slice(start, end);

  tbody.innerHTML = paginatedData
    .map(
      (report) => `
    <tr>
      <td>${formatDate(report.tanggal)}</td>
      <td>${getPekerjaanName(report.pekerjaan)}</td>
      <td>${report.pelapor}</td>
      <td>
        <small class="text-muted">
          <i class="bi bi-geo-alt-fill"></i>
          ${report.latitude.toFixed(4)}, ${report.longitude.toFixed(4)}
        </small>
      </td>
      <td>
        <span class="status-badge ${getStatusClass(report.status)}">
          ${getStatusText(report.status)}
        </span>
      </td>
      <td class="text-center">
        <div class="dropdown">
          <button class="btn action-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end action-dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="viewDetail(${
              report.id
            })">
              <i class="bi bi-eye-fill"></i> Lihat Detail
            </a></li>
            <li><a class="dropdown-item" href="pelaporan-form_edit.html?id=${
              report.id
            }">
              <i class="bi bi-pencil-fill"></i> Edit
            </a></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="deleteReport(${
              report.id
            })">
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

// ==================== RENDER PAGINATION ====================
function renderPagination() {
  const totalPages = Math.ceil(filteredReports.length / itemsPerPage);
  const pagination = document.getElementById("pagination");

  if (totalPages <= 1) {
    pagination.innerHTML = "";
    return;
  }

  let html = "";

  // Previous button
  html += `
    <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
      <a class="page-link" href="#" onclick="changePage(${
        currentPage - 1
      }); return false;">Sebelumnya</a>
    </li>
  `;

  // Page numbers
  for (let i = 1; i <= totalPages; i++) {
    if (
      i === 1 ||
      i === totalPages ||
      (i >= currentPage - 1 && i <= currentPage + 1)
    ) {
      html += `
        <li class="page-item ${i === currentPage ? "active" : ""}">
          <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
        </li>
      `;
    } else if (i === currentPage - 2 || i === currentPage + 2) {
      html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }
  }

  // Next button
  html += `
    <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
      <a class="page-link" href="#" onclick="changePage(${
        currentPage + 1
      }); return false;">Selanjutnya</a>
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

  // Scroll to top
  window.scrollTo({ top: 0, behavior: "smooth" });
}

// ==================== VIEW DETAIL (MODAL) ====================
function viewDetail(reportId) {
  const report = allReports.find((r) => r.id === reportId);

  if (!report) {
    Swal.fire("Error", "Laporan tidak ditemukan", "error");
    return;
  }

  // Build weather info
  const weatherInfo = report.cuaca_deskripsi
    ? `${report.cuaca_deskripsi}, ${report.cuaca_suhu}°C, Kelembaban: ${report.cuaca_kelembaban}%`
    : "Data tidak tersedia";

  // Build photos HTML
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
    title: "<strong>Detail Laporan</strong>",
    html: `
      <div class="text-start">
        <div class="mb-3">
          <strong>Tanggal:</strong> ${formatDate(report.tanggal)}<br>
          <strong>Pelapor:</strong> ${report.pelapor}<br>
          <strong>Pekerjaan:</strong> ${getPekerjaanName(report.pekerjaan)}
        </div>
        
        <hr>
        
        <div class="mb-3">
          <strong>Lokasi GPS:</strong><br>
          <small class="text-muted">
            <i class="bi bi-geo-alt-fill"></i> 
            Lat: ${report.latitude.toFixed(6)}, Lon: ${report.longitude.toFixed(
      6
    )}
          </small>
        </div>
        
        <hr>
        
        <div class="mb-3">
          <strong>Jenis Pekerjaan:</strong><br>
          ${report.jenis_pekerjaan}
        </div>
        
        <div class="mb-3">
          <strong>Volume:</strong> ${report.volume || "-"} ${
      report.satuan || ""
    }
        </div>
        
        <div class="mb-3">
          <strong>Deskripsi:</strong><br>
          ${report.deskripsi}
        </div>
        
        <hr>
        
        <div class="mb-3">
          <strong>Cuaca:</strong> ${weatherInfo}<br>
          <strong>Jam Kerja:</strong> ${report.jam_kerja || "-"} jam<br>
          <strong>Kondisi Lapangan:</strong> ${report.kondisi_lapangan || "-"}
        </div>
        
        ${
          report.kendala
            ? `
          <div class="mb-3">
            <strong>Kendala:</strong><br>
            ${report.kendala}
          </div>
        `
            : ""
        }
        
        ${
          report.solusi
            ? `
          <div class="mb-3">
            <strong>Solusi:</strong><br>
            ${report.solusi}
          </div>
        `
            : ""
        }
        
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
    confirmButtonText: "Tutup",
    confirmButtonColor: "#1d6ba8",
  });
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
    const response = await fetch(`/app/progress-harian/${reportId}`, {
      method: "DELETE",
      headers: {
        "X-CSRF-TOKEN":
          document.querySelector('meta[name="csrf-token"]')?.content || "",
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

      // Reload data
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
  const months = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "Mei",
    "Jun",
    "Jul",
    "Agu",
    "Sep",
    "Okt",
    "Nov",
    "Des",
  ];
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

function getStatusClass(status) {
  const classes = {
    approved: "status-approved",
    submitted: "status-submitted",
    revision: "status-revision",
    draft: "status-draft",
  };
  return classes[status] || "status-draft";
}

function getStatusText(status) {
  const texts = {
    approved: "Disetujui",
    submitted: "Terkirim",
    revision: "Perlu Revisi",
    draft: "Draft",
  };
  return texts[status] || "Unknown";
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

console.log("📊 Ringkasan Laporan - Ready! 🚀");
