@extends('Dashboard.base')

@section('title', 'Progress Fisik Pekerjaan')

@section('content')
<div class="page-inner">
    {{-- Header Halaman Ditingkatkan --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title fw-bold">
            <i class="fas fa-tasks me-2 text-primary"></i> Progress Fisik Pekerjaan
        </h4>
        @if(auth()->user()->role === 'superadmin')
        <a href="{{ route('pekerjaan.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus me-1"></i> Input Rencana Kerja
        </a>
        @endif
    </div>

    ---

    {{-- Alert Sukses (Ditingkatkan) --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        @forelse($pekerjaans as $pekerjaan)
        <div class="col-lg-4 col-md-6 mb-4">
            {{-- Kartu Pekerjaan --}}
            <div class="card h-100 shadow-lg border-primary border-3 border-start rounded-3">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title fw-bold text-primary mb-1">
                                {{ $pekerjaan->nama_investasi }}
                            </h5>
                            <span class="badge bg-secondary text-white">{{ $pekerjaan->nomor_prodef_sap }}</span>
                        </div>
                        {{-- Tombol Aksi Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm text-secondary" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('pekerjaan.detail', $pekerjaan->id) }}">
                                        <i class="fa fa-eye text-info me-2"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fa fa-list text-warning me-2"></i> Rincian
                                    </a>
                                </li>
                                @if(auth()->user()->role === 'superadmin')
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('pekerjaan.edit', $pekerjaan->id) }}">
                                        <i class="fa fa-edit text-primary me-2"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('pekerjaan.destroy', $pekerjaan->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pekerjaan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fa fa-times me-2"></i> Hapus
                                        </button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    {{-- Detail Pekerjaan dalam List Group --}}
                    <ul class="list-group list-group-flush flex-grow-1">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted"><i class="fas fa-building me-2"></i>Entitas/Terminal</span>
                            <span class="fw-semibold">{{ $pekerjaan->wilayah?->nama ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted"><i class="fas fa-barcode me-2"></i>COA</span>
                            <span class="fw-semibold">{{ $pekerjaan->coa }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted"><i class="fas fa-tag me-2"></i>Tipe Investasi</span>
                            <span class="fw-semibold text-end">{{ $pekerjaan->tipe_investasi }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Tahun Usulan</span>
                            <span class="fw-semibold">{{ $pekerjaan->tahun_usulan ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted"><i class="fas fa-chart-line me-2"></i>Kebutuhan Dana</span>
                            <span class="fw-bold text-success">Rp
                                {{ number_format($pekerjaan->kebutuhan_dana ?? 0,0,',','.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted"><i class="fas fa-money-bill-wave me-2"></i>RKAP</span>
                            <span class="fw-bold text-success">Rp
                                {{ number_format($pekerjaan->rkap ?? 0,0,',','.') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="fas fa-info-circle me-2"></i> Data pekerjaan belum tersedia.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection