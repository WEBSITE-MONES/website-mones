@extends('Dashboard.base')

@section('title', 'Edit Investasi Kerja')

@section('content')
<div class="page-inner">
    {{-- Header Halaman --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title fw-bold">
            <i class="fas fa-edit me-2 text-primary"></i> Edit Rencana Investasi Kerja
        </h4>
        <a href="{{ route('pekerjaan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pekerjaan
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Card Utama Form --}}
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white p-3 rounded-top-3">
                    <h4 class="card-title mb-0 text-center fw-bolder">
                        FORM EDIT RENCANA INVESTASI
                    </h4>
                </div>

                <form action="{{ route('pekerjaan.update', $pekerjaan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body p-4">

                        {{-- Alert Validasi Error --}}
                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <h5 class="alert-heading fs-6 fw-bold">
                                <i class="fas fa-exclamation-triangle me-2"></i> Kesalahan Input!
                            </h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        {{-- Info Bidang Wajib Isi --}}
                        <div class="alert alert-info border-info shadow-sm p-2 mb-4">
                            <small class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                Bidang yang ditanda bintang merah (<span class="text-danger">*</span>) wajib diisi.
                            </small>
                        </div>

                        {{-- Section: Informasi Dasar --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-folder me-1"></i> Data Umum
                            </legend>
                            <div class="row g-3">
                                {{-- Unit Cabang --}}
                                <div class="col-md-4">
                                    <label for="wilayah_id" class="form-label fw-semibold">
                                        <i class="fas fa-building me-1 text-muted"></i> Unit Cabang <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="wilayah_id" id="wilayah_id" class="form-select form-select-sm"
                                        required>
                                        <option value="">-- Pilih Unit Cabang --</option>
                                        @foreach ($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id }}"
                                            {{ old('wilayah_id', $pekerjaan->wilayah_id) == $wilayah->id ? 'selected' : '' }}>
                                            {{ $wilayah->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- COA --}}
                                <div class="col-md-4">
                                    <label for="coa" class="form-label fw-semibold">
                                        <i class="fas fa-barcode me-1 text-muted"></i> COA <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="coa" id="coa" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih COA --</option>
                                        @foreach(['201','202','203','204','211','212','213','221','222'] as $c)
                                        <option value="{{ $c }}"
                                            {{ old('coa', $pekerjaan->coa) == $c ? 'selected' : '' }}>
                                            {{ $c }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- COA SUB --}}
                                <div class="col-md-4">
                                    <label for="coa_sub" class="form-label fw-semibold">
                                        <i class="fas fa-stream me-1 text-muted"></i> COA SUB <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="coa_sub" id="coa_sub" class="form-control form-control-sm"
                                        value="{{ old('coa_sub', optional($pekerjaan->masterInvestasis->first())->coa_sub) }}"
                                        readonly required>
                                </div>
                                {{-- Program Investasi --}}
                                <div class="col-md-6">
                                    <label for="program_investasi" class="form-label fw-semibold">
                                        <i class="fas fa-project-diagram me-1 text-muted"></i> Program Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="program_investasi" id="program_investasi"
                                        class="form-control form-control-sm"
                                        value="{{ old('program_investasi', $pekerjaan->program_investasi) }}"
                                        placeholder="Contoh: Pengadaan Kapal" required>
                                </div>
                                {{-- Nomor Prodef SAP --}}
                                <div class="col-md-6">
                                    <label for="nomor_prodef_sap" class="form-label fw-semibold">
                                        <i class="fas fa-hashtag me-1 text-muted"></i> Nomor Prodef SAP
                                    </label>
                                    <input type="text" name="nomor_prodef_sap" id="nomor_prodef_sap"
                                        class="form-control form-control-sm"
                                        value="{{ old('nomor_prodef_sap', $pekerjaan->nomor_prodef_sap) }}"
                                        placeholder="Opsional">
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section: Detail Investasi --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-info-circle me-1"></i> Rincian Proyek
                            </legend>
                            <div class="row g-3">
                                {{-- Nama Investasi --}}
                                <div class="col-md-12">
                                    <label for="nama_investasi" class="form-label fw-semibold">
                                        <i class="fas fa-file-signature me-1 text-muted"></i> Nama Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea name="nama_investasi" id="nama_investasi"
                                        class="form-control form-control-sm" rows="2"
                                        placeholder="Deskripsi lengkap investasi"
                                        required>{{ old('nama_investasi', $pekerjaan->nama_investasi) }}</textarea>
                                </div>
                                {{-- Tipe --}}
                                <div class="col-md-4">
                                    <label for="tipe" class="form-label fw-semibold">
                                        <i class="fas fa-chart-pie me-1 text-muted"></i> Tipe <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="tipe" id="tipe" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        @php
                                        $tipeOptions = [
                                        'A'=>'Investasi Murni','B'=>'Investasi Multi Year','B1'=>'Multi Year 2020',
                                        'B2'=>'Multi Year 2021','B3'=>'Multi Year 2022','B4'=>'Multi Year 2023 & 2024',
                                        'C'=>'Carry Forward/Over','KAP'=>'Kapitalisasi Bunga','PMPI'=>'Penyertaan Modal'
                                        ];
                                        @endphp
                                        @foreach($tipeOptions as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('tipe', optional($pekerjaan->masterInvestasis->first())->tipe) == $key ? 'selected' : '' }}>
                                            {{ $key }}: {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Jenis --}}
                                <div class="col-md-4">
                                    <label for="jenis" class="form-label fw-semibold">
                                        <i class="fas fa-shapes me-1 text-muted"></i> Jenis Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="jenis" id="jenis" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        @foreach(['Investasi Murni','Investasi Multi Year','Carry Forward','Kapitalisasi
                                        Bunga','Penyertaan Modal'] as $jenis)
                                        <option value="{{ $jenis }}"
                                            {{ old('jenis', optional($pekerjaan->masterInvestasis->first())->jenis) == $jenis ? 'selected' : '' }}>
                                            {{ $jenis }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Tipe Investasi (Readonly) --}}
                                <div class="col-md-4">
                                    <label for="tipe_investasi" class="form-label fw-semibold">
                                        <i class="fas fa-tag me-1 text-muted"></i> Tipe Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="tipe_investasi" id="tipe_investasi"
                                        class="form-control form-control-sm"
                                        value="{{ old('tipe_investasi', $pekerjaan->tipe_investasi) }}" readonly
                                        required>
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section: Klasifikasi & Urgensi --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-filter me-1"></i> Klasifikasi Proyek
                            </legend>
                            <div class="row g-3">
                                {{-- Kategori --}}
                                <div class="col-md-4">
                                    <label for="kategori" class="form-label fw-semibold">
                                        <i class="fas fa-cogs me-1 text-muted"></i> Kategori Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="kategori" id="kategori" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach([
                                        '1'=>'Penggantian untuk mempertahankan bisnis', '2'=>'Penggantian untuk
                                        efisiensi',
                                        '3'=>'Pengembangan brownfield', '4'=>'Pengembangan greenfield',
                                        '5'=>'Proyek Penugasan', '6'=>'Investasi lain-lain'
                                        ] as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('kategori', optional($pekerjaan->masterInvestasis->first())->kategori) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Manfaat --}}
                                <div class="col-md-4">
                                    <label for="manfaat" class="form-label fw-semibold">
                                        <i class="fas fa-handshake me-1 text-muted"></i> Manfaat Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="manfaat" id="manfaat" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Manfaat --</option>
                                        @foreach([
                                        '1'=>'Menghasilkan Pendapatan / Menurunkan Biaya', '2'=>'Memenuhi Kebutuhan
                                        Minimal',
                                        '3'=>'Meningkatkan Keselamatan / Keamanan', '4'=>'Tujuan administratif / layanan
                                        / estetika'
                                        ] as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('manfaat', optional($pekerjaan->masterInvestasis->first())->manfaat) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Sifat --}}
                                <div class="col-md-4">
                                    <label for="sifat" class="form-label fw-semibold">
                                        <i class="fas fa-exclamation-triangle me-1 text-muted"></i> Sifat Investasi
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="sifat" id="sifat" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Sifat --</option>
                                        @foreach(['Wajib','Opsional'] as $sifat)
                                        <option value="{{ $sifat }}"
                                            {{ old('sifat', optional($pekerjaan->masterInvestasis->first())->sifat) == $sifat ? 'selected' : '' }}>
                                            {{ $sifat }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Urgensi --}}
                                <div class="col-md-4">
                                    <label for="urgensi" class="form-label fw-semibold">
                                        <i class="fas fa-chart-bar me-1 text-muted"></i> Urgensi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="urgensi" id="urgensi" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Urgensi --</option>
                                        @foreach(['Tinggi','Sedang','Rendah'] as $urgensi)
                                        <option value="{{ $urgensi }}"
                                            {{ old('urgensi', optional($pekerjaan->masterInvestasis->first())->urgensi) == $urgensi ? 'selected' : '' }}>
                                            {{ $urgensi }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Tahun Usulan --}}
                                <div class="col-md-4">
                                    <label for="tahun_usulan" class="form-label fw-semibold">
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i> Tahun Usulan <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="tahun_usulan" id="tahun_usulan" class="form-select form-select-sm"
                                        required>
                                        @for ($year=date('Y'); $year<=date('Y')+5; $year++) <option value="{{ $year }}"
                                            {{ old('tahun_usulan', $pekerjaan->tahun_usulan) == $year ? 'selected' : '' }}>
                                            {{ $year }}</option>
                                            @endfor
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section: Anggaran --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-dollar-sign me-1"></i> Rincian Anggaran
                            </legend>
                            <div class="row g-3">
                                {{-- Kebutuhan Dana --}}
                                <div class="col-md-6">
                                    <label for="kebutuhan_dana" class="form-label fw-semibold">
                                        <i class="fas fa-money-check-alt me-1 text-muted"></i> Kebutuhan Dana <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="kebutuhan_dana" id="kebutuhan_dana"
                                            class="form-control dana" step="1000"
                                            value="{{ old('kebutuhan_dana', $pekerjaan->kebutuhan_dana) }}" required>
                                    </div>
                                </div>
                                {{-- RKAP --}}
                                <div class="col-md-6">
                                    <label for="rkap" class="form-label fw-semibold">
                                        <i class="fas fa-piggy-bank me-1 text-muted"></i> RKAP <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="rkap" id="rkap" class="form-control rkap" step="1000"
                                            value="{{ old('rkap', $pekerjaan->rkap) }}" required>
                                    </div>
                                </div>
                                {{-- Total Dana --}}
                                <div class="col-md-12">
                                    <div class="alert alert-primary text-end fw-bold shadow-sm p-2 mb-0">
                                        Total Anggaran: <span id="total-dana">Rp 0</span>
                                        <input type="hidden" name="total_dana" id="total-dana-hidden" value="0">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    {{-- Card Footer Aksi --}}
                    <div class="card-footer d-flex justify-content-end p-3 bg-light border-top">
                        <a href="{{ route('pekerjaan.index') }}" class="btn btn-outline-danger me-2 px-4 shadow-sm">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- SCRIPT DISERAGAMKAN DENGAN FORM TAMBAH --}}
<script>
$(document).ready(function() {
    // Mapping COA → COA SUB
    const coaSubMap = {
        '201': 'Bangunan Fasilitas',
        '202': 'Kapal',
        '203': 'Alat-Alat Fasilitas',
        '204': 'Instalasi Fasilitas',
        '211': 'Tanah dan Hak Atas Tanah',
        '212': 'Jalan, Bangunan, Sarana dan Prasarana',
        '213': 'Peralatan dan Perlengkapan',
        '221': 'Kendaraan',
        '222': 'Emplasemen'
    };

    // Mapping Tipe → Tipe Investasi
    const tipeInvestasiMap = {
        'A': 'Investasi Murni',
        'B': 'Investasi Multi Year',
        'B1': 'Investasi Multi Year',
        'B2': 'Investasi Multi Year',
        'B3': 'Investasi Multi Year',
        'B4': 'Investasi Multi Year',
        'C': 'Carry Forward/Over',
        'KAP': 'Kapitalisasi Bunga',
        'PMPI': 'Penyertaan Modal'
    };

    // Event: COA
    $('#coa').change(function() {
        $('#coa_sub').val(coaSubMap[$(this).val()] || '');
    });

    // Event: Tipe
    $('#tipe').change(function() {
        $('#tipe_investasi').val(tipeInvestasiMap[$(this).val()] || '');
    });

    // Fungsi Hitung Total
    function hitungTotal() {
        let kebutuhan = parseFloat($('.dana').val()) || 0;
        let rkap = parseFloat($('.rkap').val()) || 0;
        let total = kebutuhan + rkap;
        $('#total-dana').text('Rp ' + total.toLocaleString('id-ID'));
        $('#total-dana-hidden').val(total);
    }

    // Event: Input Dana & RKAP
    $('.dana, .rkap').on('input', hitungTotal);

    // Trigger awal saat halaman dimuat untuk mengisi data
    hitungTotal();
    $('#coa').trigger('change');
    $('#tipe').trigger('change');
});
</script>
@endpush
@endsection