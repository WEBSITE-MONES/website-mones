@extends('Dashboard.base')

@section('title', 'Edit Progress')

@section('content')

<div class="page-inner">
    <div class="page-header mb-4">
        <h4 class="page-title fw-bolder text-primary d-flex align-items-center">
            <i class="fas fa-edit me-2"></i> Edit Progress Pekerjaan
        </h4>
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
                    <i class="fas fa-chart-line me-1"></i> Input & Rekap Progress
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3">
            {{-- =================================================================== --}}
            {{-- TAB 1: Form Edit BA & PCM                                           --}}
            {{-- =================================================================== --}}
            <div class="tab-pane fade {{ session('activeTab', 'formProgress') == 'formProgress' ? 'show active' : '' }}"
                id="formProgress" role="tabpanel">
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
                                <label for="file_ba" class="form-label fw-semibold">Upload Dokumen BA Mulai Kerja
                                    (PDF/DOC)</label>
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
                                @else
                                <small class="d-block mt-2 text-info">Belum ada file BA yang diunggah.</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-primary text-white p-3 rounded-top-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-handshake me-2"></i> Project Coordination Meeting
                            (PCM)</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nomor_pcm_mulai_kerja" class="form-label fw-semibold">Nomor PCM Mulai
                                    Kerja</label>
                                <input type="text" name="nomor_pcm_mulai_kerja" id="nomor_pcm_mulai_kerja"
                                    class="form-control form-control-sm" placeholder="Cth: PCM/001/2023"
                                    value="{{ old('nomor_pcm_mulai_kerja', $po->progresses->first()?->nomor_pcm_mulai_kerja) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_pcm_mulai_kerja" class="form-label fw-semibold">Tanggal PCM Mulai
                                    Kerja</label>
                                <input type="date" name="tanggal_pcm_mulai_kerja" id="tanggal_pcm_mulai_kerja"
                                    class="form-control form-control-sm"
                                    value="{{ old('tanggal_pcm_mulai_kerja', $po->progresses->first()?->tanggal_pcm_mulai_kerja) }}">
                            </div>
                            <div class="col-12">
                                <label for="file_pcm" class="form-label fw-semibold">Upload Dokumen PCM Mulai Kerja
                                    (PDF/DOC)</label>
                                <input type="file" name="file_pcm" id="file_pcm" class="form-control form-control-sm">
                                @if(isset($po->progresses->first()?->file_pcm) && $po->progresses->first()->file_pcm)
                                <small class="d-block mt-2 text-muted fst-italic">
                                    <i class="fas fa-file-pdf me-1 text-danger"></i> File saat ini:
                                    <a href="{{ asset('storage/'.$po->progresses->first()->file_pcm) }}" target="_blank"
                                        class="fw-semibold text-decoration-underline">
                                        Lihat Dokumen
                                    </a>
                                    <span class="ms-2 badge bg-success">Sudah Diunggah</span>
                                </small>
                                @else
                                <small class="d-block mt-2 text-info">Belum ada file PCM yang diunggah.</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================================== --}}
            {{-- TAB 2: Input & Rekap Progress                                       --}}
            {{-- =================================================================== --}}
            <div class="tab-pane fade {{ session('activeTab') == 'rekapProgress' ? 'show active' : '' }}"
                id="rekapProgress" role="tabpanel">

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
                                    style="width: {{ max(0, min(100, $rencanaPct)) }}%"
                                    aria-valuenow="{{ number_format($rencanaPct, 2) }}" aria-valuemin="0"
                                    aria-valuemax="100"></div>
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
                                    style="width: {{ max(0, min(100, $realisasiPct)) }}%"
                                    aria-valuenow="{{ number_format($realisasiPct, 2) }}" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-lg border-0 rounded-3 mb-4">
                    <div class="card-header bg-light p-3">
                        <h5 class="mb-0 fw-bold text-success"><i class="fas fa-chart-area me-2"></i> Grafik Kurva S
                            Rencana vs Realisasi</h5>
                    </div>
                    <div class="card-body p-4">
                        @include('Dashboard.Pekerjaan.Realisasi.partials.progress_chart')
                    </div>
                </div>

                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-table me-2"></i> Detail Progress Mingguan
                            (WBS)</h5>
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#importProgressModal">
                            <i class="fas fa-file-import me-1"></i> Import (Excel)
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @include('Dashboard.Pekerjaan.Realisasi.partials.progress_table')
                    </div>
                </div>
            </div>
        </div>

        {{-- Aksi Form Utama --}}
        <div class="d-flex justify-content-end pt-4 mt-3 border-top">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2 px-4 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-success px-5 shadow-sm">
                <i class="fas fa-save me-1"></i> Simpan Semua Perubahan
            </button>
        </div>

    </form>
</div>

{{-- Modal Import (Enhanced) --}}
<div class="modal fade" id="importProgressModal" tabindex="-1" aria-labelledby="importProgressModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('realisasi.importExcel', $po->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title" id="importProgressModalLabel">
                        <i class="fas fa-file-excel me-2"></i> 📥 Import Progress dari Excel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-3 text-muted">
                        Pastikan file Excel Anda menggunakan <strong>format template yang benar</strong> untuk
                        menghindari kegagalan impor data.
                    </p>
                    <a href="{{ route('realisasi.downloadTemplate') }}"
                        class="btn btn-outline-success btn-sm mb-4 fw-semibold w-100">
                        <i class="fas fa-download me-1"></i> Download Template Excel
                    </a>
                    <div class="mb-3">
                        <label for="file" class="form-label fw-bold">Pilih File Excel Progress</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success shadow-sm">
                        <i class="fas fa-upload me-1"></i> Proses Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cari semua elemen toggle pada tree
    const toggles = document.querySelectorAll('.tree-toggle');

    toggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            // Mencegah link berpindah halaman
            e.preventDefault();

            const parentRow = this.closest('tr');
            const parentId = parentRow.dataset.id;

            // Cari semua baris yang merupakan anak langsung dari baris yang di-klik
            const childRows = document.querySelectorAll(`tr[data-parent-id="${parentId}"]`);

            // Fungsi rekursif untuk menyembunyikan semua turunan
            const hideAllDescendants = (parentIdToHide) => {
                const descendants = document.querySelectorAll(
                    `tr[data-parent-id="${parentIdToHide}"]`);
                descendants.forEach(descendant => {
                    descendant.classList.add('d-none'); // Sembunyikan turunan

                    // Reset ikonnya jika ia juga punya anak
                    const descendantToggleIcon = descendant.querySelector(
                        '.tree-toggle i');
                    if (descendantToggleIcon) {
                        descendantToggleIcon.classList.remove('bi-chevron-up');
                        descendantToggleIcon.classList.add('bi-chevron-down');
                    }

                    // Lanjutkan ke level berikutnya
                    hideAllDescendants(descendant.dataset.id);
                });
            };

            // Ubah visibilitas (tampil/sembunyi) baris anak
            childRows.forEach(child => {
                child.classList.toggle('d-none');

                // Jika baris anak ditutup, sembunyikan juga semua turunannya
                if (child.classList.contains('d-none')) {
                    hideAllDescendants(child.dataset.id);
                }
            });

            // Ganti ikon dari 'bawah' ke 'atas' atau sebaliknya
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-chevron-down');
                icon.classList.toggle('bi-chevron-up');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Fix for modal backdrop issue
    const importModal = document.getElementById('importProgressModal');
    if (importModal) {
        importModal.addEventListener('hidden.bs.modal', function() {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }
    // Add logic for chart.js here
});
</script>
@endpush