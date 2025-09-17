@extends('Dashboard.base')

@section('title', 'Tambah Investasi Kerja')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Tambah Investasi Kerja</h4>
    </div>

    {{-- Error validation --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-header text-center">
            <h3 class="card-title">Form Tambah Investasi Kerja</h3>
        </div>

        <form action="{{ route('pekerjaan.store') }}" method="POST">
            @csrf
            <div class="card-body">

                {{-- Unit Cabang, COA, Program Investasi --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>Unit Cabang <span class="text-danger">*</span></label>
                        <select name="wilayah_id" class="form-select" required>
                            <option value="">-- Pilih Unit Cabang --</option>
                            @foreach ($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id }}">{{ $wilayah->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>COA <span class="text-danger">*</span></label>
                        <select name="coa" id="coa" class="form-select" required>
                            <option value="">-- Pilih COA --</option>
                            @foreach(['201','202','203','204','211','212','213','221','222'] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Program Investasi <span class="text-danger">*</span></label>
                        <input type="text" name="program_investasi" class="form-control" required>
                    </div>
                </div>

                {{-- Tipe Investasi, Nomor Prodef SAP --}}
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Tipe Investasi <span class="text-danger">*</span></label>
                        <select name="tipe" id="tipe" class="form-select" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="A">A: Investasi Murni</option>
                            <option value="B">B: Investasi Multi Year</option>
                            <option value="B1">B1: Multi Year 2020</option>
                            <option value="B2">B2: Multi Year 2021</option>
                            <option value="B3">B3: Multi Year 2022</option>
                            <option value="B4">B4: Multi Year 2023 & 2024</option>
                            <option value="C">C: Carry Forward/Over</option>
                            <option value="KAP">KAP: Kapitalisasi Bunga</option>
                            <option value="PMPI">PMPI: Penyertaan Modal</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Nomor Prodef SAP</label>
                        <input type="text" name="nomor_prodef_sap" class="form-control">
                    </div>
                </div>

                {{-- Nama Investasi --}}
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label>Nama Investasi <span class="text-danger">*</span></label>
                        <textarea name="nama_investasi" class="form-control" rows="2" required></textarea>
                    </div>
                </div>

                {{-- Tahun Usulan --}}
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Tahun Usulan <span class="text-danger">*</span></label>
                        <select name="tahun_usulan" class="form-select" required>
                            <option value="">-- Pilih Tahun --</option>
                            @php
                            $currentYear = date('Y');
                            $startYear = 2000; // tahun awal
                            $endYear = $currentYear + 15; // tahun akhir
                            for ($i = $startYear; $i <= $endYear; $i++) { echo "<option value='$i'>$i</option>" ; }
                                @endphp </select>
                    </div>
                </div>

                {{-- Kebutuhan Dana & RKAP --}}
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Kebutuhan Dana <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="kebutuhan_dana" class="form-control dana" step="1000" value="0"
                                required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label>RKAP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="rkap" class="form-control rkap" step="1000" value="0" required>
                        </div>
                    </div>
                </div>

                {{-- COA SUB, Tipe Investasi, Kategori & Manfaat --}}
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label>Tipe<span class="text-danger">*</span></label>
                        <input type="text" name="tipe_investasi" id="tipe_investasi" class="form-control" readonly
                            required>
                    </div>

                    <div class="col-md-4">
                        <label>COA SUB <span class="text-danger">*</span></label>
                        <input type="text" name="coa_sub" id="coa_sub" class="form-control" readonly required>
                    </div>

                    <div class="col-md-4">
                        <label>Kategori Investasi <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="1">Penggantian untuk mempertahankan bisnis</option>
                            <option value="2">Penggantian untuk efisiensi</option>
                            <option value="3">Pengembangan bisnis brownfield</option>
                            <option value="4">Pengembangan bisnis greenfield</option>
                            <option value="5">Proyek Penugasan</option>
                            <option value="6">Investasi lain-lain</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <label>Manfaat Investasi <span class="text-danger">*</span></label>
                        <select name="manfaat" class="form-select" required>
                            <option value="">-- Pilih Manfaat --</option>
                            <option value="1">Menghasilkan Pendapatan / Menurunkan Biaya</option>
                            <option value="2">Memenuhi Kebutuhan Minimal</option>
                            <option value="3">Meningkatkan Keselamatan Kerja / Lingkungan</option>
                            <option value="4">Tujuan administratif / kualitas pelayanan</option>
                        </select>
                    </div>
                </div>

                {{-- Jenis, Sifat, Urgensi --}}
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label>Jenis Investasi <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Investasi Murni">Investasi Murni</option>
                            <option value="Investasi Multi Year">Investasi Multi Year</option>
                            <option value="Investasi Carry Forward">Carry Forward</option>
                            <option value="Kapitalisasi Bunga">Kapitalisasi Bunga</option>
                            <option value="Penyertaan Modal">Penyertaan Modal</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Sifat Investasi <span class="text-danger">*</span></label>
                        <select name="sifat" class="form-select" required>
                            <option value="">-- Pilih Sifat --</option>
                            <option value="Wajib">Wajib</option>
                            <option value="Opsional">Opsional</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Urgensi <span class="text-danger">*</span></label>
                        <select name="urgensi" class="form-select" required>
                            <option value="">-- Pilih Urgensi --</option>
                            <option value="Tinggi">Tinggi</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Rendah">Rendah</option>
                        </select>
                    </div>
                </div>

                {{-- Total Dana --}}
                <div class="row mt-2">
                    <div class="col-md-12 text-end">
                        <strong>Total Dana: </strong>
                        <span id="total-dana">Rp 0</span>
                        <input type="hidden" name="total_dana" id="total-dana-hidden" value="0">
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="card-footer text-end">
                <a href="{{ route('pekerjaan.index') }}" class="btn btn-danger">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // COA SUB otomatis
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
    $('#coa').change(function() {
        $('#coa_sub').val(coaSubMap[$(this).val()] || '');
    });

    // Tipe Investasi otomatis
    const tipeInvestasiMap = {
        'A': 'Investasi Murni',
        'B': 'Investasi Multi Year',
        'B1': 'Investasi Multi Year',
        'B2': 'Investasi Multi Year',
        'B3': 'Investasi Multi Year',
        'B4': 'Investasi Multi Year',
        'C': 'Investasi Carry Forward/Over',
        'KAP': 'Kapitalisasi Bunga',
        'PMPI': 'Penyertaan Modal'
    };
    $('#tipe').change(function() {
        $('#tipe_investasi').val(tipeInvestasiMap[$(this).val()] || '');
    });

    // Total Dana otomatis
    function hitungTotal() {
        let kebutuhan = parseFloat($('.dana').val()) || 0;
        let rkap = parseFloat($('.rkap').val()) || 0;
        let total = kebutuhan + rkap;
        $('#total-dana').text('Rp ' + total.toLocaleString('id-ID'));
        $('#total-dana-hidden').val(total);
    }
    $('.dana, .rkap').on('input', hitungTotal);
    hitungTotal();
});
</script>
@endpush
@endsection