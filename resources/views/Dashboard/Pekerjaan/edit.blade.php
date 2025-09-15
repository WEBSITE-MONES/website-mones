@extends('Dashboard.base')

@section('title', 'Edit Investasi Kerja')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit Investasi Kerja</h4>
    </div>

    {{-- Error Validation --}}
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
            <h3 class="card-title">Form Edit Investasi Kerja</h3>
        </div>

        <form action="{{ route('pekerjaan.update', $pekerjaan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">

                {{-- Baris 1: Unit Cabang, COA, Program Investasi --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>Unit Cabang <span class="text-danger">*</span></label>
                        <select name="wilayah_id" class="form-select" required>
                            <option value="">-- Pilih Unit Cabang --</option>
                            @foreach ($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id }}"
                                {{ $pekerjaan->wilayah_id == $wilayah->id ? 'selected' : '' }}>
                                {{ $wilayah->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>COA <span class="text-danger">*</span></label>
                        <select name="coa" id="coa" class="form-select" required>
                            @foreach(['201','202','203','204','211','212','213','221','222'] as $c)
                            <option value="{{ $c }}" {{ $pekerjaan->coa == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Program Investasi <span class="text-danger">*</span></label>
                        <input type="text" name="program_investasi" class="form-control"
                            value="{{ old('program_investasi', $pekerjaan->program_investasi) }}" required>
                    </div>
                </div>

                {{-- Baris 2: Tipe Investasi, Nomor Prodef SAP --}}
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Tipe Investasi <span class="text-danger">*</span></label>
                        <select name="tipe_investasi" id="tipe_investasi" class="form-select" required>
                            @php
                            $tipeOptions = [
                            'A'=>'Investasi Murni','B'=>'Investasi Multi Year','B1'=>'Multi Year 2020',
                            'B2'=>'Multi Year 2021','B3'=>'Multi Year 2022','B4'=>'Multi Year 2023 & 2024',
                            'C'=>'Carry Forward/Over','KAP'=>'Kapitalisasi Bunga','PMPI'=>'Penyertaan Modal'
                            ];
                            @endphp
                            <option value="">-- Pilih Tipe --</option>
                            @foreach($tipeOptions as $key => $label)
                            <option value="{{ $key }}"
                                {{ optional($pekerjaan->masterInvestasis->first())->tipe == $key ? 'selected' : '' }}>
                                {{ $key }}: {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        {{-- Hidden field untuk kode tipe --}}
                        <input type="hidden" name="tipe" id="tipe"
                            value="{{ old('tipe', optional($pekerjaan->masterInvestasis->first())->tipe) }}">
                    </div>

                    <div class="col-md-6">
                        <label>Nomor Prodef SAP</label>
                        <input type="text" name="nomor_prodef_sap" class="form-control"
                            value="{{ old('nomor_prodef_sap', $pekerjaan->nomor_prodef_sap) }}">
                    </div>
                </div>

                {{-- Baris 3: Nama Investasi --}}
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label>Nama Investasi <span class="text-danger">*</span></label>
                        <textarea name="nama_investasi" class="form-control" rows="2"
                            required>{{ old('nama_investasi', $pekerjaan->nama_investasi) }}</textarea>
                    </div>
                </div>

                {{-- Baris 4: Tahun Usulan --}}
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Tahun Usulan <span class="text-danger">*</span></label>
                        <select name="tahun_usulan" class="form-select" required>
                            @for ($year=date('Y'); $year<=date('Y')+5; $year++) <option value="{{ $year }}"
                                {{ $pekerjaan->tahun_usulan == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                        </select>
                    </div>
                </div>

                {{-- Baris 5: Kebutuhan Dana & RKAP --}}
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Kebutuhan Dana <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="kebutuhan_dana" class="form-control dana" step="1000"
                                value="{{ old('kebutuhan_dana', $pekerjaan->kebutuhan_dana) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label>RKAP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="rkap" class="form-control rkap" step="1000"
                                value="{{ old('rkap', $pekerjaan->rkap) }}" required>
                        </div>
                    </div>
                </div>

                {{-- Baris 6: Jenis, Kategori, Manfaat, Sifat, Urgensi --}}
                <div class="row mt-2">
                    <div class="col-md-2">
                        <label>Jenis Investasi <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-select" required>
                            @foreach(['Investasi Murni','Investasi Multi Year','Carry Forward','Kapitalisasi
                            Bunga','Penyertaan Modal'] as $jenis)
                            <option value="{{ $jenis }}"
                                {{ optional($pekerjaan->masterInvestasis->first())->jenis == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Kategori Investasi <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            @foreach([
                            '1'=>'Penggantian untuk mempertahankan bisnis',
                            '2'=>'Penggantian untuk efisiensi',
                            '3'=>'Pengembangan brownfield',
                            '4'=>'Pengembangan greenfield',
                            '5'=>'Proyek Penugasan',
                            '6'=>'Investasi lain-lain'
                            ] as $key => $label)
                            <option value="{{ $key }}"
                                {{ optional($pekerjaan->masterInvestasis->first())->kategori == $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Manfaat Investasi <span class="text-danger">*</span></label>
                        <select name="manfaat" class="form-select" required>
                            @foreach([
                            '1'=>'Menghasilkan Pendapatan / Menurunkan Biaya',
                            '2'=>'Memenuhi Kebutuhan Minimal',
                            '3'=>'Meningkatkan Keselamatan / Keamanan',
                            '4'=>'Tujuan administratif / layanan / estetika'
                            ] as $key => $label)
                            <option value="{{ $key }}"
                                {{ optional($pekerjaan->masterInvestasis->first())->manfaat == $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Sifat Investasi <span class="text-danger">*</span></label>
                        <select name="sifat" class="form-select" required>
                            @foreach(['Wajib','Opsional'] as $sifat)
                            <option value="{{ $sifat }}"
                                {{ optional($pekerjaan->masterInvestasis->first())->sifat == $sifat ? 'selected' : '' }}>
                                {{ $sifat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Urgensi <span class="text-danger">*</span></label>
                        <select name="urgensi" class="form-select" required>
                            @foreach(['Tinggi','Sedang','Rendah'] as $urgensi)
                            <option value="{{ $urgensi }}"
                                {{ optional($pekerjaan->masterInvestasis->first())->urgensi == $urgensi ? 'selected' : '' }}>
                                {{ $urgensi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>COA SUB <span class="text-danger">*</span></label>
                        <input type="text" name="coa_sub" id="coa_sub" class="form-control"
                            value="{{ old('coa_sub', optional($pekerjaan->masterInvestasis->first())->coa_sub) }}"
                            readonly required>
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
                <button type="submit" class="btn btn-primary">Update</button>
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

    // Total Dana otomatis
    function hitungTotal() {
        let kebutuhan = parseFloat($('input[name="kebutuhan_dana"]').val()) || 0;
        let rkap = parseFloat($('input[name="rkap"]').val()) || 0;
        let total = kebutuhan + rkap;
        $('#total-dana').text('Rp ' + total.toLocaleString());
        $('#total-dana-hidden').val(total);
    }
    $('input[name="kebutuhan_dana"], input[name="rkap"]').on('input', hitungTotal);
    hitungTotal();
});
</script>
@endpush
@endsection