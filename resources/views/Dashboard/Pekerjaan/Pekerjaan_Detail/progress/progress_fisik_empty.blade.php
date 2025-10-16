@extends('Dashboard.base')

@section('title', 'Progress Fisik - Belum Ada Data')

@section('content')
<div class="page-inner">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">Progress Fisik Pekerjaan</h2>
        <a href="{{ route('pekerjaan.index') }}" class="btn btn-light">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pekerjaan
        </a>
    </div>

    <div class="card border-info">
        <div class="card-body text-center py-5">
            <i class="fas fa-chart-line fa-5x text-info mb-4 opacity-50"></i>
            <h4 class="text-info mb-3">Belum Ada Data Progress</h4>
            <p class="text-muted mb-4">
                PR dan PO sudah dibuat untuk <strong>{{ $pekerjaan->nama_investasi }}</strong>,
                tetapi progress fisik pekerjaan belum diinput.
            </p>
            <div class="alert alert-info d-inline-block">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Langkah:</strong> Menu Realisasi → Edit Progress → Input data progress mingguan
            </div>
            <div class="mt-4">
                <a href="{{ route('realisasi.editProgress', $po->id) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-edit me-2"></i>Input Progress Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection