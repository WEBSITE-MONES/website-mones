@extends('Dashboard.base')

@section('title', 'Edit GR')

@section('content')
<div class="page-inner">
    <div class="card card-round">
        <div class="card-header">
            <h4 class="card-title">Edit GR</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('realisasi.updateGR', [$pr->id, $gr->id]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tanggal_gr" class="form-label">Tanggal GR</label>
                        <input type="date" name="tanggal_gr" class="form-control" required
                            value="{{ $gr->tanggal_gr }}">
                    </div>
                    <div class="col-md-4">
                        <label for="nomor_gr" class="form-label">Nomor GR</label>
                        <input type="text" name="nomor_gr" class="form-control" required value="{{ $gr->nomor_gr }}">
                    </div>
                    <div class="col-md-4">
                        <label for="nilai_gr" class="form-label">Nilai Pekerjaan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="nilai_gr" id="nilai_gr" class="form-control"
                                value="{{ $gr->nilai_gr }}" readonly>
                        </div>
                    </div>
                </div>

                {{-- Attachment --}}
                <h5 class="mt-4">Attachment</h5>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>BA Pemeriksaan Pekerjaan Selesai</th>
                            <th>BA Serah Terima Pekerjaan</th>
                            <th>BA Pembayaran</th>
                            <th>Laporan / Dokumentasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="file" name="ba_pemeriksaan" class="form-control"></td>
                            <td><input type="file" name="ba_serah_terima" class="form-control"></td>
                            <td><input type="file" name="ba_pembayaran" class="form-control"></td>
                            <td><input type="file" name="laporan_dokumentasi" class="form-control"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="card-footer text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-danger">Batal</a>
                    <button type="submit" class="btn btn-primary">Update GR</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection