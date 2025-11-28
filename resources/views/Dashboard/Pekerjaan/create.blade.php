@extends('Dashboard.base')

@section('title', 'Tambah Investasi Kerja')

@section('content')
<div class="page-inner">
    {{-- Header Halaman --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title fw-bold">
            <i class="fas fa-plus-circle me-2 text-primary"></i> Tambah Rencana Investasi Kerja
        </h4>
        <a href="{{ route('pekerjaan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <form action="{{ route('pekerjaan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Card Utama Form --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white p-3">
                        <h4 class="card-title mb-0 text-center fw-bolder">
                            <i class="fas fa-edit me-2"></i> FORM INPUT RENCANA INVESTASI
                        </h4>
                    </div>

                    <div class="card-body p-4">

                        {{-- Alert Validasi Error --}}
                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                            <h5 class="alert-heading fs-6 fw-bold">
                                <i class="fas fa-exclamation-triangle me-2"></i> Ada Kesalahan Input!
                            </h5>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        {{-- Info Bidang Wajib Isi --}}
                        <div class="alert alert-info border-info-subtle bg-info-subtle shadow-sm p-2 mb-4 rounded-3">
                            <small class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                Bidang dengan tanda bintang merah (<span class="text-danger">*</span>) wajib diisi.
                            </small>
                        </div>

                        {{-- Section: Data Umum --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-3 fs-6 fw-semibold text-primary">
                                <i class="fas fa-folder-open me-2"></i>Data Umum
                            </legend>
                            <div class="row g-3">
                                {{-- Unit Cabang --}}
                                <div class="col-md-4">
                                    <label for="wilayah_id" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-building me-2 text-muted"></i>Unit Cabang <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="wilayah_id" id="wilayah_id" class="form-select form-select-sm"
                                        required>
                                        <option value="">-- Pilih Unit Cabang --</option>
                                        @foreach ($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id }}"
                                            {{ old('wilayah_id') == $wilayah->id ? 'selected' : '' }}>
                                            {{ $wilayah->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Nomor Prodef SAP --}}
                                <div class="col-md-4">
                                    <label for="nomor_prodef_sap" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-hashtag me-2 text-muted"></i>Nomor Prodef SAP
                                    </label>
                                    <input type="text" name="nomor_prodef_sap" id="nomor_prodef_sap"
                                        class="form-control form-control-sm" value="{{ old('nomor_prodef_sap') }}"
                                        placeholder="Opsional">
                                </div>
                                {{-- Upload gambar proyek --}}
                                <div class="col-md-4">
                                    <label for="gambar" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-image me-2 text-muted"></i>Gambar Proyek
                                    </label>
                                    <input type="file" name="gambar" id="gambar" class="form-control form-control-sm"
                                        accept="image/*">
                                </div>
                                {{-- Nama Investasi --}}
                                <div class="col-md-12">
                                    <label for="nama_investasi" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-file-signature me-2 text-muted"></i>Nama Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea name="nama_investasi" id="nama_investasi"
                                        class="form-control form-control-sm" rows="2"
                                        placeholder="Masukkan deskripsi lengkap mengenai investasi..."
                                        required>{{ old('nama_investasi') }}</textarea>
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section: Klasifikasi Investasi --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-3 fs-6 fw-semibold text-primary">
                                <i class="fas fa-filter me-2"></i>Klasifikasi Investasi
                            </legend>
                            <div class="row g-3">
                                {{-- COA --}}
                                <div class="col-md-4">
                                    <label for="coa" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-barcode me-2 text-muted"></i>COA <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="coa" id="coa" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih COA --</option>
                                        @foreach(['201','202','203','204','211','212','213','221','222'] as $c)
                                        <option value="{{ $c }}" {{ old('coa') == $c ? 'selected' : '' }}>{{ $c }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- COA SUB --}}
                                <div class="col-md-4">
                                    <label for="coa_sub" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-stream me-2 text-muted"></i>COA SUB <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="coa_sub" id="coa_sub"
                                        class="form-control form-control-sm bg-light" value="{{ old('coa_sub') }}"
                                        readonly required>
                                </div>
                                {{-- Jenis --}}
                                <div class="col-md-4">
                                    <label for="jenis" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-shapes me-2 text-muted"></i>Jenis Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="jenis" id="jenis" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="STR: Strategi"
                                            {{ old('jenis') == 'STR: Strategi' ? 'selected' : '' }}>STR: Strategi
                                        </option>
                                        <option value="NSTF:Non Strategis (BAU)"
                                            {{ old('jenis') == 'NSTF:Non Strategis (BAU)' ? 'selected' : '' }}>NSTF: Non
                                            Strategis (BAU)</option>
                                    </select>
                                </div>
                                {{-- Tipe --}}
                                <div class="col-md-4">
                                    <label for="tipe" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-chart-pie me-2 text-muted"></i>Tipe <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="tipe" id="tipe" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="A" {{ old('tipe') == 'A' ? 'selected' : '' }}>A: Investasi Murni
                                        </option>
                                        <option value="B" {{ old('tipe') == 'B' ? 'selected' : '' }}>B: Investasi Multi
                                            Year</option>
                                        <option value="B1" {{ old('tipe') == 'B1' ? 'selected' : '' }}>B1: Multi Year
                                            2020</option>
                                        <option value="B2" {{ old('tipe') == 'B2' ? 'selected' : '' }}>B2: Multi Year
                                            2021</option>
                                        <option value="B3" {{ old('tipe') == 'B3' ? 'selected' : '' }}>B3: Multi Year
                                            2022</option>
                                        <option value="B4" {{ old('tipe') == 'B4' ? 'selected' : '' }}>B4: Multi Year
                                            2023 & 2024</option>
                                        <option value="C" {{ old('tipe') == 'C' ? 'selected' : '' }}>C: Carry
                                            Forward/Over</option>
                                        <option value="KAP" {{ old('tipe') == 'KAP' ? 'selected' : '' }}>KAP:
                                            Kapitalisasi Bunga</option>
                                        <option value="PMPI" {{ old('tipe') == 'PMPI' ? 'selected' : '' }}>PMPI:
                                            Penyertaan Modal</option>
                                    </select>
                                </div>
                                {{-- Tipe Investasi --}}
                                <div class="col-md-4">
                                    <label for="tipe_investasi" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-tag me-2 text-muted"></i>Tipe Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="tipe_investasi" id="tipe_investasi"
                                        class="form-control form-control-sm bg-light"
                                        value="{{ old('tipe_investasi') }}" readonly required>
                                </div>
                                {{-- Kategori --}}
                                <div class="col-md-4">
                                    <label for="kategori" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-cogs me-2 text-muted"></i>Kategori Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="kategori" id="kategori" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Penggantian untuk mempertahankan bisnis"
                                            {{ old('kategori') == 'Penggantian untuk mempertahankan bisnis' ? 'selected' : '' }}>
                                            Penggantian untuk mempertahankan bisnis</option>
                                        <option value="Penggantian untuk efisiensi"
                                            {{ old('kategori') == 'Penggantian untuk efisiensi' ? 'selected' : '' }}>
                                            Penggantian untuk efisiensi</option>
                                        <option value="Pengembangan bisnis brownfield"
                                            {{ old('kategori') == 'Pengembangan bisnis brownfield' ? 'selected' : '' }}>
                                            Pengembangan bisnis brownfield</option>
                                        <option value="Pengembangan bisnis greenfield"
                                            {{ old('kategori') == 'Pengembangan bisnis greenfield' ? 'selected' : '' }}>
                                            Pengembangan bisnis greenfield</option>
                                        <option value="Proyek Penugasan"
                                            {{ old('kategori') == 'Proyek Penugasan' ? 'selected' : '' }}>Proyek
                                            Penugasan</option>
                                        <option value="Investasi lain-lain"
                                            {{ old('kategori') == 'Investasi lain-lain' ? 'selected' : '' }}>Investasi
                                            lain-lain</option>
                                    </select>
                                </div>
                                {{-- Manfaat --}}
                                <div class="col-md-4">
                                    <label for="manfaat" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-handshake me-2 text-muted"></i>Manfaat Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="manfaat" id="manfaat" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Manfaat --</option>
                                        <option value="Menghasilkan Pendapatan / Menurunkan Biaya"
                                            {{ old('manfaat') == 'Menghasilkan Pendapatan / Menurunkan Biaya' ? 'selected' : '' }}>
                                            Menghasilkan Pendapatan / Menurunkan Biaya</option>
                                        <option value="Memenuhi Kebutuhan Minimal"
                                            {{ old('manfaat') == 'Memenuhi Kebutuhan Minimal' ? 'selected' : '' }}>
                                            Memenuhi Kebutuhan Minimal</option>
                                        <option value="Meningkatkan Keselamatan Kerja / Lingkungan"
                                            {{ old('manfaat') == 'Meningkatkan Keselamatan Kerja / Lingkungan' ? 'selected' : '' }}>
                                            Meningkatkan Keselamatan Kerja / Lingkungan</option>
                                        <option value="Tujuan administratif / kualitas pelayanan"
                                            {{ old('manfaat') == 'Tujuan administratif / kualitas pelayanan' ? 'selected' : '' }}>
                                            Tujuan administratif / kualitas pelayanan</option>
                                    </select>
                                </div>
                                {{-- Sifat --}}
                                <div class="col-md-4">
                                    <label for="sifat" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-check-circle me-2 text-muted"></i>Sifat Investasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="sifat" id="sifat" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Sifat --</option>
                                        <option value="Pengembangan/Expanse"
                                            {{ old('sifat') == 'Pengembangan/Expanse' ? 'selected' : '' }}>PE:
                                            Pengembangan/Expanse</option>
                                        <option value="RU: Rutin/Maintenance"
                                            {{ old('sifat') == 'RU: Rutin/Maintenance' ? 'selected' : '' }}>RU:
                                            Rutin/Maintenance</option>
                                    </select>
                                </div>
                                {{-- Urgensi --}}
                                <div class="col-md-4">
                                    <label for="urgensi" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-exclamation-triangle me-2 text-muted"></i>Urgensi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="urgensi" id="urgensi" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Urgensi --</option>
                                        <option value="Tinggi" {{ old('urgensi') == 'Tinggi' ? 'selected' : '' }}>MU:
                                            Must Have</option>
                                        <option value="Sedang" {{ old('urgensi') == 'Sedang' ? 'selected' : '' }}>Ni:
                                            Nice to Have</option>
                                    </select>
                                </div>
                                {{-- Tahun Usulan --}}
                                <div class="col-md-4">
                                    <label for="tahun_usulan" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-calendar-alt me-2 text-muted"></i>Tahun Usulan <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="tahun_usulan" id="tahun_usulan" class="form-select form-select-sm"
                                        required>
                                        <option value="">-- Pilih Tahun --</option>
                                        @php
                                        $currentYear = date('Y');
                                        $startYear = 2019;
                                        $endYear = $currentYear + 5;
                                        for ($i = $startYear; $i <= $endYear; $i++) { $selected=old('tahun_usulan')==$i
                                            ? 'selected' : '' ; echo "<option value='$i' $selected>$i</option>" ; }
                                            @endphp </select>
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section: Rincian Anggaran (IMPROVED) --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-3 fs-6 fw-semibold text-primary">
                                <i class="fas fa-dollar-sign me-2"></i>Rincian Anggaran
                            </legend>
                            <div class="row g-4">

                                {{-- Kebutuhan Dana Total --}}
                                <div class="col-md-5">
                                    <label for="kebutuhan_dana_display" class="form-label fw-semibold mb-1">
                                        <i class="fas fa-money-check-alt me-2 text-muted"></i>
                                        Total Kebutuhan Dana <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text fw-bold">Rp</span>
                                        {{-- Input tampil (format rupiah) --}}
                                        <input type="text" id="kebutuhan_dana_display"
                                            class="form-control format-rupiah"
                                            value="{{ old('kebutuhan_dana') ? number_format(old('kebutuhan_dana'), 0, ',', '.') : '0' }}"
                                            required>
                                        {{-- Hidden input (nilai mentah untuk DB) --}}
                                        <input type="hidden" name="kebutuhan_dana" id="kebutuhan_dana"
                                            value="{{ old('kebutuhan_dana', 0) }}">
                                    </div>
                                    <small class="form-text text-muted">
                                        Estimasi total biaya yang dibutuhkan untuk keseluruhan investasi.
                                    </small>
                                </div>

                                {{-- Alokasi RKAP per Tahun --}}
                                <div class="col-md-7">
                                    <div class="bg-light p-3 rounded-3 border">
                                        <h6 class="fw-semibold mb-2 text-dark">
                                            <i class="fas fa-calendar-check me-2 text-muted"></i>
                                            Alokasi Dana per Tahun (RKAP)
                                        </h6>
                                        <div id="rkap-wrapper" class="row gx-2 gy-2">
                                            {{-- Dynamic RKAP inputs will be generated here by JS --}}
                                        </div>
                                    </div>
                                </div>

                                {{-- Total Dana Summary --}}
                                <div class="col-md-12 mt-3">
                                    <div
                                        class="d-flex justify-content-between align-items-center bg-primary-subtle text-primary-emphasis p-3 rounded-3 fw-bold fs-5 shadow-sm">
                                        <span>Total Nilai Anggaran</span>
                                        <span id="total-dana">Rp 0</span>
                                        <input type="hidden" name="total_dana" id="total-dana-hidden" value="0">
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                    </div>

                    {{-- Card Footer Aksi --}}
                    <div class="card-footer d-flex justify-content-end p-3 bg-body-tertiary border-top">
                        <a href="{{ route('pekerjaan.index') }}"
                            class="btn btn-outline-danger me-2 px-4 shadow-sm rounded-pill">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                            <i class="fas fa-save me-1"></i> Simpan Rencana
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/pekerjaan.js') }}"></script>
@endpush