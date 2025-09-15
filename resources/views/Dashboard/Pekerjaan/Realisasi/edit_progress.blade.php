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

    <div class="row">
        {{-- Form Edit Progress --}}
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Edit Progress</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('realisasi.updateProgress', $po->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Nama Pekerjaan</th>
                                    <td>{{ $po->pr->pekerjaan->nama_investasi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nilai Pekerjaan</th>
                                    <td>Rp {{ number_format($po->nilai_po, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Progress</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input type="range" name="progress" min="0" max="100"
                                                value="{{ old('progress', $po->progress->progress ?? 0) }}"
                                                class="form-range me-3" id="progressRange">
                                            <span id="progressValue">
                                                {{ old('progress', $po->progress->progress ?? 0) }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Bulan</th>
                                    <td>
                                        <select name="bulan" class="form-select" required>
                                            @foreach ([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                                            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                                            9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num =>
                                            $month)
                                            <option value="{{ $num }}"
                                                {{ old('bulan', $po->progress->bulan ?? '') == $num ? 'selected' : '' }}>
                                                {{ $month }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nomor BA Mulai Kerja</th>
                                    <td>
                                        <input type="text" name="nomor_ba_mulai_kerja"
                                            value="{{ old('nomor_ba_mulai_kerja', $po->progress->nomor_ba_mulai_kerja ?? '') }}"
                                            class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal BA Mulai Kerja</th>
                                    <td>
                                        <input type="date" name="tanggal_ba_mulai_kerja"
                                            value="{{ old('tanggal_ba_mulai_kerja', $po->progress->tanggal_ba_mulai_kerja ?? '') }}"
                                            class="form-control">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-end mt-3">
                            <a href="{{ route('realisasi.index') }}" class="btn btn-danger">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Riwayat Progress --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Riwayat Progress</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Progress (%)</th>
                                <th>BA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($progresses as $p)
                            <tr>
                                <td>{{ $p->bulan }}</td>
                                <td>{{ $p->progress }}%</td>
                                <td>
                                    @if($p->file_ba)
                                    <a href="{{ asset('storage/'.$p->file_ba) }}" target="_blank">Lihat</a>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada progress</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const progressRange = document.getElementById('progressRange');
const progressValue = document.getElementById('progressValue');

progressRange.addEventListener('input', function() {
    progressValue.innerText = this.value + '%';
});
</script>
@endpush

@endsection