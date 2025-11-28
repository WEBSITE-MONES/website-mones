@extends('Dashboard.base')

@section('title', 'Edit Progress')

@section('content')


{{-- ===== CSS STYLING ===== --}}
@push('styles')
<style>
.toggle-icon {
    transition: transform 0.2s ease;
}

tr[class*="child-of-"] {
    transition: opacity 0.2s ease;
}

.parent-row:hover {
    background-color: #f8f9fa !important;
    cursor: pointer;
}

.level-0 {
    font-weight: bold;
}

.level-1 {
    background-color: #f8f9fa;
}

.level-2 {
    background-color: #ffffff;
}
</style>
@endpush

<div class="page-inner">
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-4">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Formulir Edit Progress Pekerjaan</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Realisasi Berjalan</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Edit Progress</a>
                </li>
            </ul>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-lg rounded-3 border-0" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2 fs-4"></i>
            <strong>Success!</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-lg rounded-3 border-0" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-2 fs-4"></i>
            <strong>Error!</strong> {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-lg rounded-3 border-0" role="alert">
        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Validation Errors</h5>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
            <li>⚠️ {{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Form Container --}}
    <form action="{{ route('realisasi.updateProgress', $po->id) }}" method="POST" enctype="multipart/form-data"
        id="progressForm" class="mb-5">
        @csrf
        @method('PUT')

        {{-- Tabs for Navigation --}}
        <ul class="nav nav-pills nav-fill mb-3 bg-white p-2 rounded-3 shadow-sm" id="progressTab" role="tablist">
            <li class="nav-item">
                <button
                    class="nav-link fw-bold text-gray {{ session('activeTab', 'formProgress') == 'formProgress' ? 'active' : '' }}"
                    id="form-progress-tab" data-bs-toggle="tab" data-bs-target="#formProgress" type="button" role="tab">
                    <i class="fas fa-file-alt me-1"></i> Form Edit BA & PCM
                </button>
            </li>
            <li class="nav-item">
                <button
                    class="nav-link fw-bold text-success {{ session('activeTab') == 'rekapProgress' ? 'active' : '' }}"
                    id="rekap-progress-tab" data-bs-toggle="tab" data-bs-target="#rekapProgress" type="button"
                    role="tab">
                    <i class="fas fa-chart-line me-1"></i> Rekap Progress (Read Only)
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3">
            {{-- =================================================================== --}}
            {{-- TAB 1: Form Edit BA & PCM (EDITABLE)                                --}}
            {{-- =================================================================== --}}
            <div class="tab-pane fade {{ session('activeTab', 'formProgress') == 'formProgress' ? 'show active' : '' }}"
                id="formProgress" role="tabpanel">

                <!-- Detail Proyek -->
                <div class="card shadow-lg border-0 rounded-3 mb-4">
                    <div class="card-header bg-primary text-white p-3 rounded-top-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i> Detail Proyek</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-borderless align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <th class="bg-light text-dark fw-semibold" style="width: 30%;">Nama Pekerjaan
                                        </th>
                                        <td>{{ optional(optional($po->pr)->pekerjaan)->nama_investasi ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-dark fw-semibold">Nilai Pekerjaan (PO)</th>
                                        <td>
                                            <span class="fw-bold text-success fs-6">
                                                Rp {{ number_format($po->nilai_po, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- BA Mulai Kerja -->
                <div class="card shadow-lg border-0 rounded-3 mb-4">
                    <div class="card-header bg-primary text-white p-3 rounded-top-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-file-contract me-2"></i> Berita Acara (BA) Mulai Kerja
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nomor_ba_mulai_kerja" class="form-label fw-semibold">Nomor BA Mulai
                                    Kerja</label>
                                <input type="text" name="nomor_ba_mulai_kerja" id="nomor_ba_mulai_kerja"
                                    class="form-control form-control-sm" placeholder="Cth: BA/001/2023"
                                    value="{{ old('nomor_ba_mulai_kerja', $po->progresses->first()?->nomor_ba_mulai_kerja) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_ba_mulai_kerja" class="form-label fw-semibold">Tanggal BA Mulai
                                    Kerja</label>
                                <input type="date" name="tanggal_ba_mulai_kerja" id="tanggal_ba_mulai_kerja"
                                    class="form-control form-control-sm"
                                    value="{{ old('tanggal_ba_mulai_kerja', $po->progresses->first()?->tanggal_ba_mulai_kerja) }}">
                            </div>
                            <div class="col-12">
                                <label for="file_ba" class="form-label fw-semibold">Upload Dokumen BA (PDF/DOC)</label>
                                <input type="file" name="file_ba" id="file_ba" class="form-control form-control-sm">
                                @if($po->progresses->first()?->file_ba)
                                <small class="d-block mt-2 text-muted fst-italic">
                                    <i class="fas fa-file-pdf me-1 text-danger"></i> File saat ini:
                                    <a href="{{ asset('storage/'.$po->progresses->first()->file_ba) }}" target="_blank"
                                        class="fw-semibold text-decoration-underline">
                                        Lihat Dokumen
                                    </a>
                                    <span class="ms-2 badge bg-success">Sudah Diunggah</span>
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PCM -->
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-primary text-white p-3 rounded-top-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-handshake me-2"></i> Project Coordination Meeting
                            (PCM)</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nomor_pcm_mulai_kerja" class="form-label fw-semibold">Nomor PCM</label>
                                <input type="text" name="nomor_pcm_mulai_kerja" id="nomor_pcm_mulai_kerja"
                                    class="form-control form-control-sm" placeholder="Cth: PCM/001/2023"
                                    value="{{ old('nomor_pcm_mulai_kerja', $po->progresses->first()?->nomor_pcm_mulai_kerja) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_pcm_mulai_kerja" class="form-label fw-semibold">Tanggal PCM</label>
                                <input type="date" name="tanggal_pcm_mulai_kerja" id="tanggal_pcm_mulai_kerja"
                                    class="form-control form-control-sm"
                                    value="{{ old('tanggal_pcm_mulai_kerja', $po->progresses->first()?->tanggal_pcm_mulai_kerja) }}">
                            </div>
                            <div class="col-12">
                                <label for="file_pcm" class="form-label fw-semibold">Upload Dokumen PCM
                                    (PDF/DOC)</label>
                                <input type="file" name="file_pcm" id="file_pcm" class="form-control form-control-sm">
                                @if($po->progresses->first()?->file_pcm)
                                <small class="d-block mt-2 text-muted fst-italic">
                                    <i class="fas fa-file-pdf me-1 text-danger"></i> File saat ini:
                                    <a href="{{ asset('storage/'.$po->progresses->first()->file_pcm) }}" target="_blank"
                                        class="fw-semibold text-decoration-underline">
                                        Lihat Dokumen
                                    </a>
                                    <span class="ms-2 badge bg-success">Sudah Diunggah</span>
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================================== --}}
            {{-- TAB 2: Rekap Progress (READ ONLY)                                   --}}
            {{-- =================================================================== --}}
            {{-- TAB 2: Rekap Progress (READ ONLY) --}}
            <div class="tab-pane fade {{ session('activeTab') == 'rekapProgress' ? 'show active' : '' }}"
                id="rekapProgress" role="tabpanel">

                <!-- Summary Cards -->
                <div class="row mb-4 g-3">
                    <div class="col-md-6">
                        <div
                            class="card card-body border-start border-4 border-primary shadow-sm bg-light-subtle h-100">
                            <small class="text-muted fw-semibold">Rencana Kumulatif:</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="h3 fw-bolder text-primary mb-0">{{ number_format($rencanaPct, 2) }}%</div>
                                <i class="fas fa-calendar-check text-primary fs-3 opacity-50"></i>
                            </div>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ max(0, min(100, $rencanaPct)) }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            class="card card-body border-start border-4 border-success shadow-sm bg-light-subtle h-100">
                            <small class="text-muted fw-semibold">Realisasi Kumulatif:</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="h3 fw-bolder text-success mb-0">{{ number_format($realisasiPct, 2) }}%</div>
                                <i class="fas fa-tasks text-success fs-3 opacity-50"></i>
                            </div>
                            <div
                                class="small mt-1 text-end fw-semibold text-{{ $deviasiPct >= 0 ? 'info' : 'danger' }}">
                                Deviasi: {{ number_format($deviasiPct, 2) }}%
                            </div>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ max(0, min(100, $realisasiPct)) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Kurva S -->
                <div class="card shadow-lg border-0 rounded-3 mb-4">
                    <div class="card-header bg-light p-3">
                        <h5 class="mb-0 fw-bold text-success">
                            <i class="fas fa-chart-area me-2"></i> Grafik Kurva S Rencana vs Realisasi
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="curveSChart" height="80"></canvas>
                    </div>
                </div>

                <!-- ✅ HANYA 1 TABEL INI SAJA -->
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-table me-2"></i> Detail Progress Mingguan (WBS)
                        </h5>

                        <div class="btn-group" role="group">
                            <!-- Tombol Expand/Collapse -->
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="expandAll()"
                                title="Expand Semua">
                                <i class="fas fa-plus-square me-1"></i> Expand All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="collapseAll()"
                                title="Collapse Semua">
                                <i class="fas fa-minus-square me-1"></i> Collapse All
                            </button>

                            <!-- Tombol Import -->
                            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#importProgressModal">
                                <i class="fas fa-file-import me-1"></i> Import Rencana (Excel)
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @include('Dashboard.Pekerjaan.Realisasi.partials.progress_table')
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-4">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Catatan:</strong>
                    Progress realisasi dihitung otomatis dari
                    <a href="{{ route('landingpage.index.pelaporan') }}" class="fw-bold text-decoration-underline"
                        target="_blank">
                        Form Pelaporan Progress Harian
                    </a>.
                    Data pada tabel ini tidak bisa diubah secara manual.
                </div>
            </div>
        </div>

        {{-- Submit Button (Hanya untuk Tab 1) --}}
        <div class="d-flex justify-content-end pt-4 mt-3 border-top">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2 px-4 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-success px-5 shadow-sm">
                <i class="fas fa-save me-1"></i> Simpan BA & PCM
            </button>
        </div>

    </form>
</div>

{{-- Modal Import Rencana Progress --}}
<div class="modal fade" id="importProgressModal" tabindex="-1" aria-labelledby="importProgressModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="importForm" action="{{ route('realisasi.importExcel', $po->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title" id="importProgressModalLabel">
                        <i class="fas fa-file-excel me-2"></i> Import Rencana Progress
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-3 text-muted">
                        File Excel berisi <strong>rencana time schedule</strong> per minggu (bukan realisasi).
                    </p>
                    <a href="{{ route('realisasi.downloadTemplate') }}"
                        class="btn btn-outline-success btn-sm mb-4 fw-semibold w-100">
                        <i class="fas fa-download me-1"></i> Download Template Excel
                    </a>
                    <div class="mb-3">
                        <label for="file" class="form-label fw-bold">Pilih File Excel</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" id="btnImport" class="btn btn-success shadow-sm">
                        <i class="fas fa-upload me-1"></i> Proses Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- ===== CHART.JS SCRIPT ===== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Ready - Starting Chart Initialization');

    const ctx = document.getElementById('curveSChart');
    console.log('Canvas Element:', ctx);

    if (ctx) {
        const chartData = @json($chartData ?? []);
        console.log('Chart Data:', chartData);

        if (!chartData || chartData.length === 0) {
            console.error('❌ No chart data available!');
            return;
        }

        try {
            if (window.curveSChartInstance) {
                window.curveSChartInstance.destroy();
            }

            window.curveSChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(d => d.week_label || d.week || 'Unknown'),
                    datasets: [{
                            label: 'Rencana (%)',
                            data: chartData.map(d => parseFloat(d.rencana) || 0),
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: 'rgb(54, 162, 235)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Realisasi (%)',
                            data: chartData.map(d => parseFloat(d.realisasi) || 0),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: 'rgb(75, 192, 192)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Progress Kumulatif Rencana vs Realisasi',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed.y.toFixed(2) + '%';
                                    return label;
                                },
                                afterBody: function(tooltipItems) {
                                    const index = tooltipItems[0].dataIndex;
                                    const deviasi = chartData[index].deviasi;
                                    return '\nDeviasi: ' + (deviasi >= 0 ? '+' : '') + deviasi
                                        .toFixed(2) + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                },
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            console.log('✅ Chart created successfully!');

        } catch (error) {
            console.error('❌ Error creating chart:', error);
        }
    }
});
</script>

<script>
function toggleChildren(rowId) {
    const icon = document.querySelector(`tr[data-id="${rowId}"] .toggle-icon`);
    const childRows = document.querySelectorAll(`.child-of-${rowId}`);

    if (icon.classList.contains('fa-chevron-down')) {
        // Collapse
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');

        childRows.forEach(row => {
            row.style.display = 'none';
            const childIcon = row.querySelector('.toggle-icon');
            if (childIcon && childIcon.classList.contains('fa-chevron-down')) {
                childIcon.classList.remove('fa-chevron-down');
                childIcon.classList.add('fa-chevron-right');
            }
        });
    } else {
        // Expand
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');

        childRows.forEach(row => {
            const rowLevel = parseInt(row.dataset.level);
            const parentLevel = parseInt(document.querySelector(`tr[data-id="${rowId}"]`).dataset.level);

            if (rowLevel === parentLevel + 1) {
                row.style.display = '';
            }
        });
    }
}

function expandAll() {
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    });
    document.querySelectorAll('tr[class*="child-of-"]').forEach(row => {
        row.style.display = '';
    });
}

function collapseAll() {
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    });
    document.querySelectorAll('tr[class*="child-of-"]').forEach(row => {
        row.style.display = 'none';
    });
}
</script>

{{-- ===== IMPORT FORM VALIDATION ===== --}}
<script>
document.getElementById('importForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const file = document.getElementById('file').files[0];

    if (!file) {
        alert('Pilih file terlebih dahulu');
        return;
    }

    const btnImport = document.getElementById('btnImport');
    btnImport.disabled = true;
    btnImport.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

    this.submit();
});
</script>
@endpush