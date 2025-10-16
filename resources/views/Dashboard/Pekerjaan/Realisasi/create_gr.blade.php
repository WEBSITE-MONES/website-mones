@extends('Dashboard.base')

@section('title', 'Tambah GR')

@push('styles')
{{-- CSS Terpadu - Disamakan dengan gaya Edit GR, menggunakan warna biru yang lebih modern --}}
<style>
/* Style untuk area upload file */
.custom-file-upload {
    border: 2px dashed #0d6efd;
    border-radius: .5rem;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    background-color: #f8f9fa;
    transition: all .3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 150px;
}

.custom-file-upload:hover {
    background-color: #e9ecef;
    border-color: #0b5ed7;
}

.custom-file-upload .icon {
    font-size: 2.5rem;
    color: #0d6efd;
    margin-bottom: .5rem;
}

.custom-file-upload .file-text {
    display: block;
    font-weight: 600;
    color: #495057;
}

.custom-file-upload .file-name {
    display: block;
    font-size: .875rem;
    color: #0d6efd;
    font-weight: 500;
    margin-top: .5rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}
</style>
@endpush

@section('content')
<div class="page-inner py-4">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">Tambah Good Receipt (GR)</h2>
            <h5 class="fw-normal text-muted">Lengkapi detail penerimaan barang/jasa per bulan dan lampirkan dokumen.
            </h5>
        </div>
        <a href="{{ route('realisasi.index') }}" class="btn btn-light btn-sm d-none d-md-block">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    @if(!$po)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Error!</strong> PO belum tersedia untuk PR ini. Silakan buat PO terlebih dahulu.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('realisasi.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Realisasi
        </a>
    </div>
    @else
    <form action="{{ route('realisasi.storeGR', ['pr' => $pr->id, 'po' => $po->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">

                        <div class="alert alert-info border-0 mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Catatan:</strong> GR (Good Receipt) adalah pencatatan penerimaan barang/jasa yang
                            dilakukan <strong>per bulan</strong>.
                            Pembayaran akan diproses nanti berdasarkan termin PO.
                        </div>

                        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>Informasi Utama
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nomor_gr" class="form-label">
                                    Nomor GR <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" id="nomor_gr" name="nomor_gr"
                                        class="form-control @error('nomor_gr') is-invalid @enderror"
                                        value="{{ old('nomor_gr') }}" required placeholder="Contoh: GR/2025/01/001">
                                </div>
                                @error('nomor_gr')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tanggal_gr" class="form-label">
                                    Tanggal GR <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" id="tanggal_gr" name="tanggal_gr"
                                        class="form-control @error('tanggal_gr') is-invalid @enderror"
                                        value="{{ old('tanggal_gr', date('Y-m-d')) }}" required>
                                </div>
                                @error('tanggal_gr')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nilai_gr_input" class="form-label">
                                    Nilai GR <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="nilai_gr_input"
                                        class="form-control @error('nilai_gr') is-invalid @enderror"
                                        value="{{ old('nilai_gr', '') }}" required placeholder="0">
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan nilai penerimaan bulan ini
                                </small>
                                @error('nilai_gr')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">
                            <i class="fas fa-paperclip text-primary me-2"></i>Lampiran Dokumen (.pdf)
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">BA Pemeriksaan Pekerjaan</label>
                                <label for="ba_pemeriksaan" class="custom-file-upload">
                                    <i class="fas fa-cloud-upload-alt icon"></i>
                                    <span class="file-text">Pilih file BAP...</span>
                                    <span class="file-name text-success"></span>
                                </label>
                                <input type="file" id="ba_pemeriksaan" name="ba_pemeriksaan" class="d-none file-input"
                                    accept=".pdf">
                                @error('ba_pemeriksaan')
                                <div class="text-danger mt-2 small d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">BA Serah Terima Pekerjaan</label>
                                <label for="ba_serah_terima" class="custom-file-upload">
                                    <i class="fas fa-cloud-upload-alt icon"></i>
                                    <span class="file-text">Pilih file BAST...</span>
                                    <span class="file-name text-success"></span>
                                </label>
                                <input type="file" id="ba_serah_terima" name="ba_serah_terima" class="d-none file-input"
                                    accept=".pdf">
                                @error('ba_serah_terima')
                                <div class="text-danger mt-2 small d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">BA Pembayaran</label>
                                <label for="ba_pembayaran" class="custom-file-upload">
                                    <i class="fas fa-cloud-upload-alt icon"></i>
                                    <span class="file-text">Pilih file BAPembayaran...</span>
                                    <span class="file-name text-success"></span>
                                </label>
                                <input type="file" id="ba_pembayaran" name="ba_pembayaran" class="d-none file-input"
                                    accept=".pdf">
                                @error('ba_pembayaran')
                                <div class="text-danger mt-2 small d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Laporan / Dokumentasi</label>
                                <label for="laporan_dokumentasi" class="custom-file-upload">
                                    <i class="fas fa-cloud-upload-alt icon"></i>
                                    <span class="file-text">Pilih file Laporan...</span>
                                    <span class="file-name text-success"></span>
                                </label>
                                <input type="file" id="laporan_dokumentasi" name="laporan_dokumentasi"
                                    class="d-none file-input" accept=".pdf">
                                @error('laporan_dokumentasi')
                                <div class="text-danger mt-2 small d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 mb-4 sticky-lg-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3">
                            <i class="fas fa-dollar-sign text-primary me-2"></i>Total Nilai
                        </h5>
                        <div class="text-center bg-light p-3 rounded mb-4">
                            <h6 class="text-muted mb-1">NILAI PENERIMAAN (GR)</h6>
                            <h3 class="fw-bold text-success mb-0" id="display_nilai_gr">
                                Rp 0
                            </h3>
                        </div>

                        <input type="hidden" name="nilai_gr" id="nilai_gr_hidden" value="{{ old('nilai_gr', 0) }}">

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan GR
                            </button>
                            <a href="{{ route('realisasi.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>

                        <div class="alert alert-secondary mt-3 small">
                            <strong>Nilai PO Total:</strong><br>
                            Rp {{ number_format($po->nilai_po, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Elemen DOM ---
    const nilaiInput = document.getElementById('nilai_gr_input');
    const nilaiHidden = document.getElementById('nilai_gr_hidden');
    const displayNilai = document.getElementById('display_nilai_gr');

    if (!nilaiInput || !nilaiHidden || !displayNilai) {
        return;
    }

    // --- Fungsi Utilitas ---
    function formatToRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(number);
    }

    function formatToNumber(value) {
        return String(value).replace(/[^\d]/g, '');
    }

    function cleanNumber(value) {
        return Number(formatToNumber(value)) || 0;
    }

    // --- Fungsi Update Nilai GR ---
    function updateNilaiGR() {
        const rawValue = cleanNumber(nilaiInput.value);

        nilaiInput.value = new Intl.NumberFormat('id-ID').format(rawValue);


        nilaiHidden.value = rawValue;

        displayNilai.textContent = formatToRupiah(rawValue);
    }

    nilaiInput.addEventListener('input', function(e) {
        const cursorPosition = e.target.selectionStart;
        const oldLength = e.target.value.length;

        updateNilaiGR();

        const newLength = e.target.value.length;
        const newPosition = cursorPosition + (newLength - oldLength);
        e.target.setSelectionRange(newPosition, newPosition);
    });

    nilaiInput.addEventListener('blur', function() {
        updateNilaiGR();
    });

    document.querySelectorAll('.file-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : '';
            const parentLabel = this.previousElementSibling;
            if (parentLabel && parentLabel.classList.contains('custom-file-upload')) {
                const fileTextEl = parentLabel.querySelector('.file-text');
                const fileNameEl = parentLabel.querySelector('.file-name');

                if (fileName) {
                    fileNameEl.textContent = fileName;
                    fileTextEl.textContent = 'File siap diupload:';
                } else {
                    fileNameEl.textContent = '';
                    fileTextEl.textContent = 'Pilih file...';
                }
            }
        });
    });

    const oldNilai = "{{ old('nilai_gr', '0') }}";
    if (oldNilai && oldNilai !== '0') {
        nilaiInput.value = oldNilai;
        updateNilaiGR();
    } else {
        updateNilaiGR();
    }
});
</script>
@endpush