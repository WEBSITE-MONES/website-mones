@extends('Dashboard.base')

@section('title', 'Edit PO')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit PO</h4>
    </div>

    <div class="card card-round shadow-sm">
        <div class="card-header text-white text-center rounded-top">
            <h3 class="card-title mb-0">FORM EDIT PO</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('realisasi.updatePO', $po->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal PO/Kontrak</label>
                        <input type="date" name="tanggal_po" class="form-control"
                            value="{{ old('tanggal_po', $po->tanggal_po) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nomor PO</label>
                        <input type="text" name="nomor_po" class="form-control"
                            value="{{ old('nomor_po', $po->nomor_po) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">No. Kontrak/SPPP/SPK</label>
                        <input type="text" name="nomor_kontrak" class="form-control"
                            value="{{ old('nomor_kontrak', $po->nomor_kontrak) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nilai PO</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="nilai_po" class="form-control"
                                value="{{ old('nilai_po', $po->nilai_po) }}" required>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estimated (Periode)</label>
                        <div class="input-group">
                            <input type="date" name="estimated_start" class="form-control"
                                value="{{ old('estimated_start', $po->estimated_start) }}">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="estimated_end" class="form-control"
                                value="{{ old('estimated_end', $po->estimated_end) }}">
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Waktu Pelaksanaan</label>
                        <div class="input-group">
                            <input type="number" name="waktu_pelaksanaan" class="form-control" min="1"
                                value="{{ old('waktu_pelaksanaan', $po->waktu_pelaksanaan) }}">
                            <span class="input-group-text">Hari</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pelaksana</label>
                        <input type="text" name="pelaksana" class="form-control"
                            placeholder="Contoh: PT. Intan Sejahtera" value="{{ old('pelaksana', $po->pelaksana) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Mekanisme Pembayaran</label>
                        <select name="mekanisme_pembayaran" class="form-select">
                            <option value="Uang muka"
                                {{ old('mekanisme_pembayaran', $po->mekanisme_pembayaran) == 'Uang muka' ? 'selected' : '' }}>
                                Uang muka</option>
                            <option value="Termin"
                                {{ old('mekanisme_pembayaran', $po->mekanisme_pembayaran) == 'Termin' ? 'selected' : '' }}>
                                Termin</option>
                        </select>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('realisasi.index') }}" class="btn btn-danger me-2">
                        <i class="fa fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Update PO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection