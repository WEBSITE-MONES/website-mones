@extends('Dashboard.base')

@section('title', 'Daftar Sub Pekerjaan')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Daftar Sub Pekerjaan - {{ $pekerjaan->nama_investasi ?? $pekerjaan->nama_pekerjaan }}
        </h4>
        <div class="ms-auto">
            <a href="{{ route('pekerjaan.detail', $pekerjaan->id) }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Sub Pekerjaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pekerjaan->subPekerjaan as $index => $sub)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $sub->nama_sub }}</td>
                        <td>
                            <a href="{{ route('pekerjaan.sub.progress', [$pekerjaan->id, $sub->id]) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-chart-line"></i> Lihat Progress
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada sub pekerjaan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection