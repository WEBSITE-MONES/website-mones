@extends('Dashboard.base')

@section('title', 'Edit Progress')

@section('content')

<div class="page-inner">
    <div class="page-header mb-4">
        <h4 class="page-title fw-bold text-primary">✏️ Edit Progress</h4>
    </div>

    {{-- Pesan sukses --}}
    @if(session('success'))
    <div class="alert alert-success shadow-sm rounded">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Pesan error --}}
    @if ($errors->any())
    <div class="alert alert-danger shadow-sm rounded">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>⚠️ {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Utama --}}
    <form action="{{ route('realisasi.updateProgress', $po->id) }}" method="POST" enctype="multipart/form-data"
        id="progressForm" class="mb-5">
        @csrf
        @method('PUT')

        {{-- Tabs --}}
        <ul class="nav nav-tabs border-0 shadow-sm rounded overflow-hidden" id="progressTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#formProgress"
                    type="button">
                    Form Edit Progress
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#rekapProgress" type="button">
                    Input Progress
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3">

            {{-- TAB 1 --}}
            <div class="tab-pane fade show active" id="formProgress" role="tabpanel">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body">
                        <table class="table table-bordered align-middle">
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
                                        <small class="d-block mt-2 text-muted">
                                            📂 File saat ini:
                                            <a href="{{ asset('storage/'.$po->progresses->first()->file_ba) }}"
                                                target="_blank" class="fw-semibold">Lihat</a>
                                        </small>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-end mt-3">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">⬅️ Batal</a>
                            <button type="submit" class="btn btn-success">💾 Update Progress</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- TAB 2 --}}
    <div class="tab-pane fade" id="rekapProgress" role="tabpanel">
        <div class="card shadow-sm border-0 rounded">
            <div class="card-header bg-light">
                <h6 class="mb-1 fw-bold text-primary">
                    {{ optional(optional($po->pr)->pekerjaan)->nama_investasi ?? 'Nama Pekerjaan Investasi (POS ID: '. $po->id .')' }}
                </h6>
                <small class="text-muted">Nilai Pekerjaan:
                    Rp{{ number_format($po->nilai_po,0,',','.') }}</small>
            </div>
            <div class="card-body">

                {{-- Tombol Import --}}
                <button type="button" class="btn btn-primary mb-3 shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#importProgressModal">
                    <i class="fas fa-file-import"></i> Import Progress (Excel)
                </button>

                {{-- Modal Import --}}
                <div class="modal fade" id="importProgressModal" tabindex="-1"
                    aria-labelledby="importProgressModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('realisasi.importExcel', $po->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">📥 Import Progress dari Excel</h5>
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

                {{-- Progress kumulatif --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded shadow-sm bg-white">
                            <small class="text-muted">Rencana Kumulatif:</small>
                            <div class="h5 fw-bold text-primary">{{ number_format($rencanaPct,2) }}%</div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ max(0, min(100, $rencanaPct)) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded shadow-sm bg-white">
                            <small class="text-muted">Realisasi Kumulatif:</small>
                            <div class="h5 fw-bold text-success">
                                {{ number_format($realisasiPct,2) }}% (Deviasi: {{ number_format($deviasiPct,2) }}%)
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ max(0, min(100, $realisasiPct)) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Grafik Kurva S --}}
                <h5 class="mt-4 mb-3 fw-bold">Grafik Kurva S Rencana vs Realisasi</h5>
                <div class="border p-3 mb-4 bg-light text-center rounded shadow-sm">
                    <div
                        style="max-width: 100%; height: 250px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #fff;">
                        <span class="text-muted">Area Chart Kurva S</span>
                    </div>
                </div>

                {{-- Tabel Progress Mingguan --}}
                <h5 class="mt-4 mb-3 fw-bold">Detail Progress Mingguan (WBS)</h5>

                <div class="table-responsive shadow-sm rounded">
                    <table id="rekapTable" class="table table-bordered table-striped align-middle"
                        style="min-width:1500px;">
                        <thead class="text-center text-white sticky-top" style="background-color:#005baa;">
                            @php
                            $totalDynamicColspan = ($masterMinggu && $masterMinggu->count() > 0)
                            ? $masterMinggu->count() * 5
                            : 1;
                            @endphp
                            <tr>
                                <th rowspan="5" style="width:50px;">No</th>
                                <th rowspan="5" style="width:300px;">Jenis Pekerjaan</th>
                                <th rowspan="5" style="width:300px;">Sub Pekerjaan</th>
                                <th rowspan="5" style="width:300px;">Sub-Sub Pekerjaan</th>
                                <th rowspan="5" style="width:70px;">Volume</th>
                                <th rowspan="5" style="width:70px;">Satuan</th>
                                <th rowspan="5" style="width:70px;">Harga Satuan</th>
                                <th rowspan="5" style="width:70px;">Jumlah Harga</th>
                                <th rowspan="5" style="width:70px;">Bobot Total</th>
                                <th rowspan="5" style="width:70px;">Bobot (%)</th>
                                <th colspan="{{ $totalDynamicColspan }}">JADWAL PELAKSANAAN PEKERJAAN</th>
                            </tr>
                            <tr>
                                @if (!empty($monthMap) && count($monthMap) > 0)
                                @foreach ($monthMap as $monthName => $data)
                                <th colspan="{{ $data['colspan'] ?? 1 }}">{{ $monthName }}</th>
                                @endforeach
                                @else
                                <th>-</th>
                                @endif
                            </tr>
                            <tr>
                                @if ($masterMinggu && $masterMinggu->count() > 0)
                                @foreach ($masterMinggu as $minggu)
                                <th colspan="5">{{ $minggu->kode_minggu }}</th>
                                @endforeach
                                @else
                                <th>-</th>
                                @endif
                            </tr>
                            <tr>
                                @if (!empty($dateRanges) && count($dateRanges) > 0)
                                @foreach ($dateRanges as $range)
                                <th colspan="5" class="text-xs" style="font-size:0.75rem;">{{ $range }}</th>
                                @endforeach
                                @else
                                <th>-</th>
                                @endif
                            </tr>
                            <tr>
                                @if ($masterMinggu && $masterMinggu->count() > 0)
                                @foreach ($masterMinggu as $minggu)
                                <th>Rencana</th>
                                <th>Rencana Kumulatif</th>
                                <th>Realisasi</th>
                                <th>Realisasi Kumulatif</th>
                                <th>Deviasi</th>
                                @endforeach
                                @else
                                <th>-</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($items as $item)
                            @include('Dashboard.Pekerjaan.Realisasi.partials.progress_table_row', [
                            'item' => $item,
                            'level' => 0
                            ])
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold bg-light">
                                {{-- No, Jenis, Sub, Sub-Sub --}}
                                <td colspan="4" class="text-center">Total</td>

                                {{-- Volume total --}}
                                <td class="text-end">
                                </td>

                                {{-- Satuan: biasanya kosong karena beda2 --}}
                                <td></td>

                                {{-- Harga Satuan: nggak bisa ditotal --}}
                                <td></td>

                                {{-- Jumlah Harga total --}}
                                <td class="text-end">
                                    {{ number_format($items->sum('jumlah_harga'), 0, ',', '.') }}
                                </td>

                                {{-- Bobot Total (sum) --}}
                                <td class="text-end">
                                    {{ number_format($items->sum('bobot') ?: $items->sum('bobot_total'), 2) }}%
                                </td>

                                {{-- Bobot (%) --}}
                                <td class="text-end">
                                    {{ number_format($items->sum('bobot') ?: $items->sum('bobot_total'), 2) }}%
                                </td>

                                {{-- Kolom dinamis minggu --}}
                                @php
                                $totalRencanaAll = [];
                                $totalRealisasiAll = [];
                                @endphp

                                @foreach ($masterMinggu as $minggu)
                                @php
                                $totalRencana = $po->progresses->sum(function($p) use ($minggu) {
                                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                                return (float) ($detail?->bobot_rencana ?? 0);
                                });

                                $totalRealisasi = $po->progresses->sum(function($p) use ($minggu) {
                                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                                return (float) ($detail?->bobot_realisasi ?? 0);
                                });

                                $totalRencanaAll[] = $totalRencana;
                                $totalRealisasiAll[] = $totalRealisasi;

                                $rencanaCum = array_sum($totalRencanaAll);
                                $realisasiCum = array_sum($totalRealisasiAll);
                                $deviasi = $realisasiCum - $rencanaCum;
                                @endphp

                                <td class="text-end">{{ number_format($totalRencana, 2) }}%</td>
                                <td class="text-end">{{ number_format($rencanaCum, 2) }}%</td>
                                <td class="text-end">{{ number_format($totalRealisasi, 2) }}%</td>
                                <td class="text-end">{{ number_format($realisasiCum, 2) }}%</td>
                                <td class="text-end {{ $deviasi < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($deviasi, 2) }}%
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const importModal = document.getElementById('importProgressModal');
    if (importModal) {
        importModal.addEventListener('hidden.bs.modal', function() {
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