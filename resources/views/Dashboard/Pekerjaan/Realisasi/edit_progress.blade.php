@extends('Dashboard.base')

@section('title', 'Form Edit Progress')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit Progress</h4>
    </div>

    {{-- Error --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
    $existingMonths = $progresses->pluck('bulan')->map(fn($m) => (int)$m)->unique()->sort()->values()->toArray();
    $initialMonths = count($existingMonths) ? $existingMonths : [1];
    $monthNames = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
    7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];
    $initialCount = count($initialMonths);
    @endphp

    <form action="{{ route('realisasi.updateProgress', $po->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Data pekerjaan --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%">Nama Pekerjaan</th>
                            <td>{{ $po->pr->pekerjaan->nama_investasi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nilai Pekerjaan</th>
                            <td>Rp {{ number_format($po->nilai_po,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Nomor BA Mulai Kerja</th>
                            <td><input type="text" name="nomor_ba_mulai_kerja" class="form-control"
                                    value="{{ old('nomor_ba_mulai_kerja', $po->progresses->last()->nomor_ba_mulai_kerja ?? '') }}">
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal BA Mulai Kerja</th>
                            <td><input type="date" name="tanggal_ba_mulai_kerja" class="form-control"
                                    value="{{ old('tanggal_ba_mulai_kerja', $po->progresses->last()->tanggal_ba_mulai_kerja ?? '') }}">
                            </td>
                        </tr>
                        <tr>
                            <th>Upload BA Mulai Kerja</th>
                            <td>
                                <input type="file" name="file_ba" class="form-control">
                                @if(!empty($po->progresses->last()->file_ba))
                                <small>File saat ini: <a href="{{ asset('storage/'.$po->progresses->last()->file_ba) }}"
                                        target="_blank">Lihat</a></small>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel progress input --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle" id="progressTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px">No</th>
                                <th style="width:160px">Bulan</th>
                                <th style="width:100px">Pekan</th>
                                <th>Rencana</th>
                                <th>Realisasi</th>
                                <th>Deviasi</th>
                            </tr>
                        </thead>
                        <tbody id="progressTableBody">
                            @php $no = 1; @endphp
                            @foreach ($initialMonths as $num)
                            @for ($week = 1; $week <= 4; $week++) @php $p=$progresses->where('bulan',
                                $num)->where('pekan', $week)->first();
                                @endphp
                                <tr data-bulan="{{ $num }}" data-pekan="{{ $week }}">
                                    @if ($week == 1)
                                    <td rowspan="4" class="no-cell">{{ $no++ }}</td>
                                    <td rowspan="4" class="month-cell">
                                        {{ $monthNames[$num] }}
                                        <input type="hidden" name="bulan[]" value="{{ $num }}">
                                    </td>
                                    @endif
                                    <td>
                                        {{ $week }}
                                        <input type="hidden" name="pekan[]" value="{{ $week }}">
                                    </td>
                                    <td>
                                        <input type="number" name="rencana[]" class="form-control rencana" min="0"
                                            value="{{ old('rencana_'.$num.'_'.$week, $p->rencana ?? 0) }}">
                                    </td>
                                    <td>
                                        <input type="number" name="realisasi[]" class="form-control realisasi" min="0"
                                            value="{{ old('realisasi_'.$num.'_'.$week, $p->realisasi ?? 0) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control deviasi" readonly
                                            value="{{ number_format(($p->realisasi ?? 0) - ($p->rencana ?? 0), 2) }}">
                                    </td>
                                </tr>
                                @endfor
                                @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th><span id="totalRencana">0</span></th>
                                <th><span id="totalRealisasi">0</span></th>
                                <th><span id="totalDeviasi">0</span></th>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-end">Progress: <span id="progressPersen">0%</span></th>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <button type="button" id="addMonthBtn" class="btn btn-success btn-sm">
                                        + Tambah Bulan
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!--  Riwayat Progress-->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Progress</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Bulan</th>
                                <th>Persentase</th>
                                <th>BA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($progresses->groupBy('bulan') as $bulan => $rows)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</td>
                                <td>{{ $rows->avg('progress') }}%</td>
                                <td>
                                    @foreach($rows as $row)
                                    @if($row->nomor_ba_mulai_kerja)
                                    <div>{{ $row->nomor_ba_mulai_kerja }} ({{ $row->tanggal_ba_mulai_kerja }})</div>
                                    @endif
                                    @endforeach
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-muted">Belum ada riwayat progress.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3 text-end">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Progress</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
const monthNames = @json($monthNames);
let existingMonths = @json($initialMonths);
let nextNo = parseInt("{{ $initialCount + 1 }}");


function getNextMonth() {
    for (let m = 1; m <= 12; m++) {
        if (!existingMonths.includes(m)) return m;
    }
    return null;
}

function appendMonthRows(monthNum) {
    const tbody = document.getElementById('progressTableBody');
    const monthLabel = monthNames[monthNum];

    for (let week = 1; week <= 4; week++) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-bulan', monthNum);
        tr.setAttribute('data-pekan', week);

        if (week === 1) {
            tr.innerHTML = `
                <td rowspan="4" class="no-cell">${nextNo}</td>
                <td rowspan="4" class="month-cell">${monthLabel}
                    <input type="hidden" name="bulan[]" value="${monthNum}">
                </td>
                <td>${week} <input type="hidden" name="pekan[]" value="${week}"></td>
                <td><input type="number" name="rencana[]" class="form-control rencana" min="0" value="0"></td>
                <td><input type="number" name="realisasi[]" class="form-control realisasi" min="0" value="0"></td>
                <td><input type="text" class="form-control deviasi" readonly value="0"></td>
            `;
        } else {
            tr.innerHTML = `
                <td>${week} <input type="hidden" name="pekan[]" value="${week}"></td>
                <td><input type="number" name="rencana[]" class="form-control rencana" min="0" value="0"></td>
                <td><input type="number" name="realisasi[]" class="form-control realisasi" min="0" value="0"></td>
                <td><input type="text" class="form-control deviasi" readonly value="0"></td>
            `;
        }

        tbody.appendChild(tr);
    }

    existingMonths.push(monthNum);
    nextNo++;
    recalcAll();
}

document.getElementById('addMonthBtn')?.addEventListener('click', function() {
    const next = getNextMonth();
    if (!next) {
        alert('Semua bulan sudah ditambahkan.');
        return;
    }
    appendMonthRows(next);
});

function recalcAll() {
    let totalR = 0,
        totalRl = 0,
        totalD = 0;

    document.querySelectorAll('#progressTableBody tr').forEach(row => {
        const rInp = row.querySelector('.rencana');
        const rlInp = row.querySelector('.realisasi');
        const devInp = row.querySelector('.deviasi');
        if (!rInp || !rlInp || !devInp) return;

        const r = parseFloat(rInp.value || 0);
        const rl = parseFloat(rlInp.value || 0);
        const d = rl - r;

        devInp.value = d.toFixed(0);

        totalR += r;
        totalRl += rl;
        totalD += d;
    });

    document.getElementById('totalRencana').innerText = totalR.toFixed(0);
    document.getElementById('totalRealisasi').innerText = totalRl.toFixed(0);
    document.getElementById('totalDeviasi').innerText = totalD.toFixed(0);

    const progress = totalR > 0 ? ((totalRl / totalR) * 100).toFixed(2) : 0;
    document.getElementById('progressPersen').innerText = progress + '%';
}

document.addEventListener('input', function(e) {
    if (e.target.matches('.rencana') || e.target.matches('.realisasi')) {
        recalcAll();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    recalcAll();
});
</script>

@endpush

@endsection