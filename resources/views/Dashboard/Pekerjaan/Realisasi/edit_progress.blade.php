@extends('Dashboard.base')

@section('title', 'Edit Progress')

@section('content')

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit Progress</h4>
    </div>

    {{-- Pesan sukses --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Utama yang Menyatukan Semua Input --}}
    <form action="{{ route('realisasi.updateProgress', $po->id) }}" method="POST" enctype="multipart/form-data"
        id="progressForm">
        @csrf
        @method('PUT')

        {{-- Tabs --}}
        <ul class="nav nav-tabs" id="progressTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#formProgress" type="button">
                    Form Edit Progress
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rekapProgress" type="button">
                    Input Progress
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3">

            {{-- TAB 1 --}}
            <div class="tab-pane fade show active" id="formProgress" role="tabpanel">
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
                        <div class="text-end">
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: Input Progress --}}
            <div class="tab-pane fade" id="rekapProgress" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header" style="background-color:#f8f9fa;">
                        <h6 class="mb-1 fw-bold">
                            {{ optional(optional($po->pr)->pekerjaan)->nama_investasi ?? 'Nama Pekerjaan Investasi (POS ID: '. $po->id .')' }}
                        </h6>
                        <small class="text-muted">Nilai Pekerjaan:
                            Rp{{ number_format($po->nilai_po,0,',','.') }}</small>
                    </div>

                    <div class="card-body">
                        {{-- Tombol Import Progress Rencana --}}
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#importProgressModal">
                            <i class="fas fa-file-import"></i> Import Progress (Excel)
                        </button>

                        <div class="modal fade" id="importProgressModal" tabindex="-1"
                            aria-labelledby="importProgressModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('realisasi.importExcel', $po->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Import Progress dari Excel</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="mb-2">
                                                Silakan gunakan template Excel berikut untuk memastikan format sesuai:
                                            </p>
                                            <a href="{{ route('realisasi.downloadTemplate') }}"
                                                class="btn btn-outline-secondary btn-sm mb-3">
                                                <i class="fas fa-download"></i> Download Template
                                            </a>
                                            <div class="mb-3">
                                                <label for="file" class="form-label">Upload File Excel</label>
                                                <input type="file" name="file" id="file" class="form-control"
                                                    accept=".xlsx,.xls,.csv" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-upload"></i> Import
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Progress Kumulatif --}}
                        <h5 class="mt-2 mb-3 fw-bold">Progress Kumulatif Pekerjaan (Per Akhir Minggu Ini)</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded">
                                    <small class="text-muted">Rencana Kumulatif:</small>
                                    <div class="h5 fw-bold text-primary">{{ number_format($rencanaPct,2) }}%</div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ max(0, min(100, $rencanaPct)) }}%"
                                            aria-valuenow="{{ $rencanaPct }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded">
                                    <small class="text-muted">Realisasi Kumulatif:</small>
                                    <div class="h5 fw-bold text-success">{{ number_format($realisasiPct,2) }}% (Deviasi:
                                        {{ number_format($deviasiPct,2) }}%)</div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ max(0, min(100, $realisasiPct)) }}%"
                                            aria-valuenow="{{ $realisasiPct }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kurva S --}}
                        <h5 class="mt-4 mb-3 fw-bold">Grafik Kurva S Rencana vs Realisasi</h5>
                        <div class="border p-3 mb-4 bg-light text-center">
                            <div
                                style="max-width: 100%; height: 250px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #fff;">
                                <span class="text-muted">Area Chart Kurva S</span>
                            </div>
                        </div>

                        {{-- Detail Progress Mingguan (WBS) --}}
                        <h5 class="mt-4 mb-3 fw-bold">Detail Progress Mingguan (WBS)</h5>

                        <div class="table-responsive">
                            <table id="rekapTable" class="table table-bordered table-striped align-middle"
                                style="min-width:1500px;">
                                <thead class="text-center" style="background-color:#005baa; color:white;">
                                    @php
                                    $totalDynamicColspan = $masterMinggu->count() * 5;
                                    @endphp
                                    <tr>
                                        <th rowspan="5" style="width:50px;">No</th>
                                        <th rowspan="5" style="width:300px;">Jenis Pekerjaan</th>
                                        <th rowspan="5" style="width:70px;">Vol</th>
                                        <th rowspan="5" style="width:70px;">Sat</th>
                                        <th rowspan="5" style="width:70px;">Bobot (%)</th>
                                        <th colspan="{{ $totalDynamicColspan }}">JADWAL PELAKSANAAN PEKERJAAN</th>
                                    </tr>
                                    <tr>
                                        @foreach ($monthMap as $monthName => $data)
                                        <th colspan="{{ $data['colspan'] }}">{{ $monthName }}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($masterMinggu as $minggu)
                                        <th colspan="5">{{ $minggu->kode_minggu }}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($dateRanges as $range)
                                        <th colspan="5" class="text-xs" style="font-size:0.75rem;">{{ $range }}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($masterMinggu as $minggu)
                                        <th>Rencana</th>
                                        <th>Rencana Kumulatif</th>
                                        <th>Realisasi</th>
                                        <th>Realisasi Kumulatif</th>
                                        <th>Deviasi</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Menggunakan blade loop untuk rendering tabel --}}
                                    @foreach ($items as $item)
                                    @include('Dashboard.Pekerjaan.Realisasi.partials.progress_table_row', ['item' =>
                                    $item, 'level' => 0])
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark font-weight-bold text-center">
                                        <td colspan="5">TOTAL KESELURUHAN</td>
                                        @php
                                        $rencanaCum = 0;
                                        $realisasiCum = 0;
                                        @endphp
                                        @foreach ($masterMinggu as $minggu)
                                        @php
                                        $totalRencana = $po->progresses->sum(function($p) use ($minggu) {
                                        $detail = $p->details->firstWhere('minggu_id', $minggu->id);
                                        return (float) optional($detail)->bobot_rencana ?: 0;
                                        });
                                        $totalRealisasi = $po->progresses->sum(function($p) use ($minggu) {
                                        $detail = $p->details->firstWhere('minggu_id', $minggu->id);
                                        return (float) optional($detail)->bobot_realisasi ?: 0;
                                        });
                                        $rencanaCum += $totalRencana;
                                        $realisasiCum += $totalRealisasi;
                                        @endphp
                                        <td class="text-right">{{ number_format($totalRencana,2) }}%</td>
                                        <td class="text-right">{{ number_format($rencanaCum,2) }}%</td>
                                        <td class="text-right">{{ number_format($totalRealisasi,2) }}%</td>
                                        <td class="text-right">{{ number_format($realisasiCum,2) }}%</td>
                                        <td
                                            class="text-right {{ ($realisasiCum - $rencanaCum) < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($realisasiCum - $rencanaCum,2) }}%
                                        </td>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Actions --}}
        <div class="mt-3 text-end">
            <a href="{{ url()->previous() }}" class="btn btn-warning">Batal</a>
            <button type="submit" class="btn btn-success">Update Progress</button>
        </div>
    </form>

</div>

@endsection

@push('scripts')
{{-- Tambahkan script ini untuk memastikan modal backdrop dihapus --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const importModal = document.getElementById('importProgressModal');
    if (importModal) {
        importModal.addEventListener('hidden.bs.modal', function() {
            // Hapus backdrop dan class modal-open secara manual
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }
});
</script>

@endpush