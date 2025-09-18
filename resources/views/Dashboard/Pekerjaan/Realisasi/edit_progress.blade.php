@extends('Dashboard.base')

@section('title', 'Form Edit Progress')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit Progress</h4>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('realisasi.updateProgress', $po->id) }}" method="POST" enctype="multipart/form-data"
        id="progressForm">
        @csrf
        @method('PUT')

        {{-- Header PO / BA --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%">Nama Pekerjaan</th>
                            <td>{{ optional(optional($po->pr)->pekerjaan)->nama_investasi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nilai Pekerjaan</th>
                            <td>Rp {{ number_format($po->nilai_po,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Nomor BA Mulai Kerja</th>
                            <td>
                                <input type="text" name="nomor_ba_mulai_kerja" class="form-control"
                                    value="{{ old('nomor_ba_mulai_kerja', $po->progresses->first()?->nomor_ba_mulai_kerja) }}">
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal BA Mulai Kerja</th>
                            <td>
                                <input type="date" name="tanggal_ba_mulai_kerja" class="form-control"
                                    value="{{ old('tanggal_ba_mulai_kerja', $po->progresses->first()?->tanggal_ba_mulai_kerja) }}">
                            </td>
                        </tr>
                        <tr>
                            <th>Upload BA Mulai Kerja</th>
                            <td>
                                <input type="file" name="file_ba" class="form-control">
                                @if($po->progresses->first()?->file_ba)
                                <small>File saat ini:
                                    <a href="{{ asset('storage/'.$po->progresses->first()->file_ba) }}"
                                        target="_blank">Lihat</a>
                                </small>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tombol tambah progress --}}
        <button type="button" id="addProgress" class="btn btn-success btn-sm mb-3">+ Tambah Progress</button>

        {{-- Container Progress --}}
        <div id="progressGroups">
            @foreach($po->progresses as $progress)
            <div class="card mb-2 progress-group" data-progress-id="{{ $progress->id }}">
                <div class="card-body p-2">
                    {{-- Progress Header --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pekerjaan</label>
                            <input type="text" class="form-control form-control-sm"
                                name="progress[{{ $progress->id }}][jenis_pekerjaan]"
                                value="{{ old('progress.'.$progress->id.'.jenis_pekerjaan', $progress->jenis_pekerjaan) }}">
                        </div>
                        <div class="text-end ms-2">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-progress"
                                data-progress-id="{{ $progress->id }}">Hapus Progress</button>
                        </div>
                    </div>

                    {{-- Subs --}}
                    @foreach($progress->subs as $sub)
                    <div class="card mb-2 p-2 border border-secondary sub-group" data-sub-id="{{ $sub->id }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1 pe-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Sub Pekerjaan</label>
                                        <input type="text" class="form-control form-control-sm"
                                            name="subs[{{ $sub->id }}][sub_pekerjaan]"
                                            value="{{ $sub->sub_pekerjaan }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Volume</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm"
                                            name="subs[{{ $sub->id }}][volume]" value="{{ $sub->volume }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Sat.</label>
                                        <input type="text" class="form-control form-control-sm"
                                            name="subs[{{ $sub->id }}][satuan]" value="{{ $sub->satuan }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Bobot (%)</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm"
                                            name="subs[{{ $sub->id }}][bobot]" value="{{ $sub->bobot }}">
                                    </div>
                                </div>
                            </div>
                            <div class="text-end ms-2">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-sub"
                                    data-sub-id="{{ $sub->id }}">Hapus Sub</button>
                            </div>
                        </div>

                        {{-- Detail Table --}}
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Minggu</th>
                                        <th>Tanggal Awal</th>
                                        <th>Tanggal Akhir</th>
                                        <th>Bulan</th>
                                        <th>Rencana (%)</th>
                                        <th>Realisasi (%)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="details-{{ $sub->id }}">
                                    @foreach($sub->details as $idx => $detail)
                                    @php
                                    $bulanAwal = $detail->tanggal_awal_minggu ?
                                    \Carbon\Carbon::parse($detail->tanggal_awal_minggu)->translatedFormat('F') : '-';
                                    $bulanAkhir = $detail->tanggal_akhir_minggu ?
                                    \Carbon\Carbon::parse($detail->tanggal_akhir_minggu)->translatedFormat('F') : '-';
                                    @endphp
                                    <tr data-detail-id="{{ $detail->id }}">
                                        <td>{{ $idx + 1 }}</td>
                                        <td><input type="number" class="form-control form-control-sm"
                                                name="details[{{ $detail->id }}][minggu]" value="{{ $detail->minggu }}">
                                        </td>
                                        <td><input type="date" class="form-control form-control-sm"
                                                name="details[{{ $detail->id }}][tanggal_awal_minggu]"
                                                value="{{ $detail->tanggal_awal_minggu }}"></td>
                                        <td><input type="date" class="form-control form-control-sm"
                                                name="details[{{ $detail->id }}][tanggal_akhir_minggu]"
                                                value="{{ $detail->tanggal_akhir_minggu }}"></td>
                                        <td class="bulan-cell">
                                            {{ $bulanAwal == $bulanAkhir ? $bulanAwal : "$bulanAwal - $bulanAkhir" }}
                                        </td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm"
                                                name="details[{{ $detail->id }}][rencana]"
                                                value="{{ $detail->rencana }}"></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm"
                                                name="details[{{ $detail->id }}][realisasi]"
                                                value="{{ $detail->realisasi }}"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-detail"
                                                data-detail-id="{{ $detail->id }}">Hapus</button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-1 text-end">
                            <button type="button" class="btn btn-success btn-sm add-minggu"
                                data-sub-id="{{ $sub->id }}">+ Minggu</button>
                        </div>
                    </div>
                    @endforeach

                    {{-- Tombol tambah sub baru --}}
                    <div class="mt-2 text-end">
                        <button type="button" class="btn btn-success btn-sm add-sub"
                            data-progress-id="{{ $progress->id }}">+ Tambah Sub Pekerjaan</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Delete marker --}}
        <div id="deletes"></div>

        {{-- Actions --}}
        <div class="mt-3 text-end">
            <a href="{{ url()->previous() }}" class="btn btn-warning">Batal</a>
            <button type="submit" class="btn btn-success">Update Progress</button>
        </div>
    </form>
</div>

@push('scripts')
<script src="{{ asset('assets/js/progress.js') }}"></script>
@endpush

@endsection