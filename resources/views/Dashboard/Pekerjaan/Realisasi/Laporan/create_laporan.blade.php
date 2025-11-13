@extends('Dashboard.base')

@section('title', 'Buat Laporan Baru')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
.page-inner {
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.page-inner.fade-in {
    opacity: 1;
}

.card-header .card-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: 0;
    width: 45px;
    justify-content: center;
}

.form-select {
    border-left: 0;
}

.form-select:focus {
    box-shadow: none;
    border-color: #ced4da;
}

.btn-primary {
    background: linear-gradient(45deg, #1e88e5, #42a5f5);
    border: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(30, 136, 229, 0.4);
}

.card-info {
    background-color: #e3f2fd;
    border-left: 5px solid #1e88e5;
}
</style>
@endpush

@section('content')
<div class="page-inner fade-in">
    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title fw-bold">
            <i class=" me-2 text-primary"></i>Laporan Baru
        </h4>
        <a href="{{ route('pekerjaan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan!</h4>
        <p>Mohon periksa kembali input Anda. Terdapat beberapa hal yang perlu diperbaiki:</p>
        <hr>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- FORM UTAMA --}}
    <form action="{{ route('laporan.store') }}" method="POST" id="formLaporan">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-file-invoice me-2 text-primary"></i>
                            Detail Laporan
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        {{-- Jenis Laporan --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Jenis Laporan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-list-alt"></i></span>
                                <select name="jenis_laporan" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>-- Pilih Jenis Laporan --</option>
                                    <option value="rekap_rincian"
                                        {{ old('jenis_laporan') == 'rekap_rincian' ? 'selected' : '' }}>
                                        Laporan Rekap Rincian
                                    </option>
                                    <option value="rekap_activa"
                                        {{ old('jenis_laporan') == 'rekap_activa' ? 'selected' : '' }}>
                                        Laporan Rekap Activa
                                    </option>
                                </select>
                            </div>
                            <small class="text-muted mt-1 d-block">Pilih jenis laporan yang akan Anda buat.</small>
                        </div>

                        {{-- Periode Laporan --}}
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <select name="tahun" class="form-select form-select-lg" required>
                                        <option value="" disabled selected>-- Pilih Tahun --</option>
                                        @for($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}"
                                            {{ old('tahun', $tahun ?? date('Y')) == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Bulan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                    <select name="bulan" class="form-select form-select-lg" required>
                                        <option value="" disabled selected>-- Pilih Bulan --</option>
                                        @php
                                        $bulanList = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                        @endphp
                                        @foreach($bulanList as $key => $nama)
                                        <option value="{{ $key }}"
                                            {{ old('bulan', $bulan ?? date('n')) == $key ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">Laporan akan dibuat untuk periode sampai dengan bulan dan
                            tahun yang dipilih.</small>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: INFORMASI & AKSI --}}
            <div class="col-lg-5">
                <div class="card border-0 rounded-4 mb-4 card-info">
                    <div class="card-body d-flex align-items-start p-4">
                        <i class="fas fa-info-circle fa-2x me-3 text-primary opacity-75"></i>
                        <div>
                            <h6 class="fw-bold mb-2">Informasi Penting</h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Laporan
                                    di-generate otomatis dari database.</li>
                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Status awal
                                    laporan adalah <strong>Draft</strong>.</li>
                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Anda perlu submit
                                    untuk proses approval.</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Approval oleh Manager &
                                    Assisten Manager.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Proses Sistem --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-cogs text-primary me-2"></i> Rangkuman Proses Sistem
                        </h6>
                        <ol class="list-group list-group-flush list-group-numbered small">
                            <li class="list-group-item">Mengambil data dari:
                                <code>pekerjaan, prs, pos, progress, payments</code>
                            </li>
                            <li class="list-group-item">Filter data berdasarkan periode yang dipilih.</li>
                            <li class="list-group-item">Agregasi data berdasarkan COA (Chart of Account).</li>
                            <li class="list-group-item">Membuat record approval untuk atasan terkait.</li>
                            <li class="list-group-item">Laporan siap di-export ke PDF atau Excel.</li>
                        </ol>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill" id="btnSubmit">
                            <i class="fas fa-rocket me-2"></i> Buat & Generate Laporan
                        </button>
                        <a href="{{ route('laporan.index') }}" class="btn btn-light btn-lg rounded-pill">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Efek fadeIn saat halaman dimuat
    $('.page-inner').addClass('fade-in');

    $('#formLaporan').on('submit', function(e) {
        e.preventDefault();

        const form = this;
        const jenis = $('select[name="jenis_laporan"] option:selected').text().trim();
        const tahun = $('select[name="tahun"]').val();
        const bulan = $('select[name="bulan"] option:selected').text().trim();

        if (!jenis || !tahun || !bulan || $('select[name="jenis_laporan"]').val() === '') {
            Swal.fire({
                icon: 'error',
                title: 'Input Tidak Lengkap',
                text: 'Mohon pastikan Jenis Laporan, Tahun, dan Bulan sudah dipilih.',
                confirmButtonColor: '#d33',
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Pembuatan Laporan',
            html: `Anda akan membuat laporan berikut:
                   <ul class="list-group list-group-flush text-start mt-3">
                     <li class="list-group-item"><b>Jenis:</b> ${jenis}</li>
                     <li class="list-group-item"><b>Periode:</b> ${bulan} ${tahun}</li>
                   </ul>
                   <br>Apakah Anda yakin untuk melanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e88e5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check"></i> Ya, Lanjutkan!',
            cancelButtonText: '<i class="fas fa-times"></i> Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const btnSubmit = $('#btnSubmit');
                btnSubmit.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...'
                );

                setTimeout(() => {
                    form.submit();
                }, 500);
            }
        });
    });
});
</script>
@endpush