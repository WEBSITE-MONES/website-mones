@extends('Dashboard.base')

@section('title', 'Rencana Pekerjaan')

@push('styles')
<style>
.card-pekerjaan {
    transition: all 0.3s ease-in-out;
}

.card-pekerjaan:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
}
</style>
@endpush

@section('content')
<div class="page-inner">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="page-header">
                <h3 class="fw-bold mb-3">Rencana Pekerjaan</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('realisasi.index') }}">Rencana Pekerjaan</a>
                    </li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->role === 'superadmin')
        <a href="{{ route('pekerjaan.create') }}" class="btn btn-light btn-lg shadow-sm">
            <i class="fas fa-plus me-2"></i> Tambah Rencana Kerja
        </a>
        @endif
    </div>

    <hr>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        @forelse($pekerjaans as $pekerjaan)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden card-pekerjaan">
                <img src="{{ $pekerjaan->gambar ? asset('storage/' . $pekerjaan->gambar) : asset('assets/img/proyek_pelindo.jpg') }}"
                    class="card-img-top" alt="Foto Pekerjaan {{ $pekerjaan->nama_investasi }}"
                    style="height: 200px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title fw-bold text-primary mb-1">
                                {{ Str::limit($pekerjaan->nama_investasi, 100) }}
                            </h5>
                            <span class="badge badge-modern bg-primary">{{ $pekerjaan->nomor_prodef_sap }}</span>
                        </div>
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
                                    <a class="dropdown-item"
                                        href="{{ route('realisasi.index', ['tab' => 'database']) }}">
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
                                        class="form-hapus d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" class="dropdown-item text-danger btn-hapus">
                                            <i class="fa fa-times me-2"></i> Hapus
                                        </a>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush flex-grow-1">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted"><i class="fas fa-building me-2"></i>Entitas</span>
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
                            <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Tahun</span>
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
            <div class="text-center p-5 card shadow-sm">
                <img src="https://via.placeholder.com/150" alt="Tidak ada data" class="mx-auto mb-4"
                    style="width:150px;">
                <h5 class="fw-bold">Belum Ada Rencana Kerja</h5>
                <p class="text-muted">Silakan input rencana kerja baru untuk menampilkannya di sini.</p>
                @if(auth()->user()->role === 'superadmin')
                <a href="{{ route('pekerjaan.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-1"></i> Input Rencana Kerja Sekarang
                </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-hapus');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            const form = this.closest('form');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pekerjaan ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush