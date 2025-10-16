@extends('Dashboard.base')

@section('title', 'Progress Fisik - Belum Ada PR')

@section('content')
<div class="page-inner">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">Progress Fisik Pekerjaan</h2>
        <a href="{{ route('pekerjaan.index') }}" class="btn btn-light">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pekerjaan
        </a>
    </div>

    <div class="card border-warning">
        <div class="card-body text-center py-5">
            <i class="fas fa-file-invoice fa-5x text-warning mb-4 opacity-50"></i>
            <h4 class="text-warning mb-3">PR Belum Dibuat</h4>
            <p class="text-muted mb-4">
                Untuk dapat melihat progress fisik pekerjaan <strong>{{ $pekerjaan->nama_investasi }}</strong>,
                Anda harus membuat <strong>Purchase Request (PR)</strong> terlebih dahulu.
            </p>
            <div class="alert alert-info d-inline-block">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Langkah:</strong> Menu Realisasi → Input PR
            </div>
            <div class="mt-4">
                <a href="{{ route('realisasi.createPR') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Buat PR Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection