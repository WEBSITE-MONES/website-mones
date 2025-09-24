@extends('Dashboard.base')

@section('title', 'Form Input PR')

@section('content')
<div class="page-inner">
    {{-- Header Halaman --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <h4 class="page-title fw-bold">
            <i class="fas fa-file-invoice me-2 text-primary"></i> Form Input PR
        </h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    ---

    {{-- Validasi Error --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <h5 class="alert-heading fs-6 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Kesalahan Input!</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Kartu Utama Form --}}
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white p-3 rounded-top-3">
            <h4 class="card-title mb-0 text-center fw-bolder">
                INPUT PERENCANAAN (PR)
            </h4>
        </div>

        <form action="{{ route('realisasi.storePR') }}" method="POST">
            @csrf
            <div class="card-body p-4">

                {{-- Group: Detail Pekerjaan --}}
                <fieldset class="border p-3 mb-4 rounded-3">
                    <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                        <i class="fas fa-tasks me-1"></i> Detail Pekerjaan
                    </legend>
                    <div class="row g-3">
                        {{-- Jenis Pekerjaan --}}
                        <div class="col-md-6">
                            <label for="jenis_pekerjaan" class="form-label fw-semibold">Jenis Pekerjaan <span
                                    class="text-danger">*</span></label>
                            <select name="jenis_pekerjaan" id="jenis_pekerjaan"
                                class="form-select form-select-sm @error('jenis_pekerjaan') is-invalid @enderror"
                                required>
                                <option value="" disabled selected>-- Pilih Jenis Pekerjaan --</option>
                                <option value="Konsultan Perencana"
                                    {{ old('jenis_pekerjaan') == 'Konsultan Perencana' ? 'selected' : '' }}>Konsultan
                                    Perencana</option>
                                <option value="Pelaksanaan Fisik"
                                    {{ old('jenis_pekerjaan') == 'Pelaksanaan Fisik' ? 'selected' : '' }}>Pelaksanaan
                                    Fisik</option>
                                <option value="Konsultan Pengawas"
                                    {{ old('jenis_pekerjaan') == 'Konsultan Pengawas' ? 'selected' : '' }}>Konsultan
                                    Pengawas</option>
                            </select>
                            @error('jenis_pekerjaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Pekerjaan --}}
                        <div class="col-md-6">
                            <label for="pekerjaan_id" class="form-label fw-semibold">Nama Pekerjaan <span
                                    class="text-danger">*</span></label>
                            <select name="pekerjaan_id" id="pekerjaan_id"
                                class="form-select form-select-sm @error('pekerjaan_id') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Pilih Pekerjaan --</option>
                                @foreach($pekerjaans as $p)
                                <option value="{{ $p->id }}" {{ old('pekerjaan_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_investasi }}</option>
                                @endforeach
                            </select>
                            @error('pekerjaan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Group: Data PR & Anggaran --}}
                <fieldset class="border p-3 rounded-3">
                    <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                        <i class="fas fa-barcode me-1"></i> Data PR & Anggaran
                    </legend>
                    <div class="row g-3">
                        {{-- Nomor PR --}}
                        <div class="col-md-6">
                            <label for="nomor_pr" class="form-label fw-semibold">Nomor PR <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nomor_pr" id="nomor_pr"
                                class="form-control form-control-sm @error('nomor_pr') is-invalid @enderror"
                                placeholder="Cth: PR-2023/10/001" value="{{ old('nomor_pr') }}" required>
                            @error('nomor_pr')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal PR --}}
                        <div class="col-md-6">
                            <label for="tanggal_pr" class="form-label fw-semibold">Tanggal PR <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pr" id="tanggal_pr"
                                class="form-control form-control-sm @error('tanggal_pr') is-invalid @enderror"
                                value="{{ old('tanggal_pr', date('Y-m-d')) }}" required>
                            @error('tanggal_pr')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nilai PR --}}
                        <div class="col-md-6">
                            <label for="nilai_pr_display" class="form-label fw-semibold">Nilai PR <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-sm @error('nilai_pr') has-validation @enderror">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="text" id="nilai_pr_display" class="form-control text-end"
                                    placeholder="Misal: 10.000.000" value="{{ old('nilai_pr_display') }}" required>
                                <input type="hidden" name="nilai_pr" id="nilai_pr" value="{{ old('nilai_pr') }}">
                                @error('nilai_pr')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted fst-italic">Masukkan nilai tanpa titik/koma.</small>
                        </div>

                        {{-- Tahun Anggaran --}}
                        <div class="col-md-6">
                            <label for="tahun_anggaran" class="form-label fw-semibold">Tahun Anggaran <span
                                    class="text-danger">*</span></label>
                            <select name="tahun_anggaran" id="tahun_anggaran"
                                class="form-select form-select-sm @error('tahun_anggaran') is-invalid @enderror"
                                required>
                                <option value="" disabled selected>-- Pilih Tahun --</option>
                                @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 20;
                                $endYear = $currentYear + 15;
                                for ($i = $startYear; $i <= $endYear; $i++) { $selected=($i==old('tahun_anggaran',
                                    $currentYear)) ? 'selected' : '' ; echo "<option value='$i' $selected>$i</option>" ;
                                    } @endphp </select>
                                    @error('tahun_anggaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                        </div>

                        {{-- Total PR Display --}}
                        <div class="col-12 mt-4">
                            <div class="alert alert-light text-end shadow-sm border border-success">
                                <strong class="text-dark">TOTAL NILAI PR: </strong>
                                <span id="total-pr" class="fw-bolder text-success fs-5">Rp 0</span>
                                <input type="hidden" name="total_pr" id="total-pr-hidden"
                                    value="{{ old('total_pr', 0) }}">
                            </div>
                        </div>
                    </div>
                </fieldset>

            </div>

            {{-- Footer Aksi --}}
            <div class="card-footer bg-light border-top p-3 d-flex justify-content-end">
                <a href="{{ url()->previous() }}" class="btn btn-outline-danger me-2 px-4 shadow-sm">
                    <i class="fas fa-times me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-save me-1"></i> Simpan PR
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const displayInput = $('#nilai_pr_display');
    const hiddenInput = $('#nilai_pr');

    // Fungsi format angka ke Rupiah
    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'decimal',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(angka);
    };

    // Fungsi menghitung total PR
    function hitungTotal() {
        let nilai = parseInt(hiddenInput.val()) || 0;
        $('#total-pr').text('Rp ' + formatRupiah(nilai));
        $('#total-pr-hidden').val(nilai);
    }

    // Event saat input Nilai PR berubah
    displayInput.on('input', function() {
        let value = this.value.replace(/\D/g, ''); // Hapus semua non-digit

        // Update input tersembunyi dengan nilai murni
        hiddenInput.val(value);

        // Update tampilan input dengan format Rupiah
        this.value = formatRupiah(value);

        hitungTotal();
    });

    // Panggil hitungTotal saat halaman dimuat (untuk old value jika ada)
    // Pastikan nilai awal di displayInput juga diformat jika old('nilai_pr') ada
    if (hiddenInput.val()) {
        displayInput.val(formatRupiah(hiddenInput.val()));
    }
    hitungTotal();
});
</script>
@endpush
@endsection