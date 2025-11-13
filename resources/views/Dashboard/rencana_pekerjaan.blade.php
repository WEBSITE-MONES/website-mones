@extends('Dashboard.base')

@section('title', 'Rencana Pekerjaan')

@push('styles')
<style>
.card-pekerjaan {
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    position: relative;
}

.card-pekerjaan:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

/* Overlay effect saat hover */
.card-pekerjaan::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(71, 178, 228, 0.05), rgba(29, 107, 168, 0.05));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
    pointer-events: none;
}

.card-pekerjaan:hover::before {
    opacity: 1;
}

/* Pastikan konten card tetap di atas overlay */
.card-pekerjaan>* {
    position: relative;
    z-index: 2;
}

/* Dropdown menu tetap bisa diklik dan tidak trigger card click */
.card-pekerjaan .dropdown {
    position: relative;
    z-index: 10;
}

/* Tambah indicator bahwa card bisa diklik */
.card-pekerjaan:hover .card-title {
    color: #47b2e4 !important;
}

/* Badge untuk visual feedback */
.clickable-indicator {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(71, 178, 228, 0.9);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 3;
}

.card-pekerjaan:hover .clickable-indicator {
    opacity: 1;
}

.info-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
}

/* Smooth fade-in animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-pekerjaan {
    animation: fadeInUp 0.5s ease forwards;
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
                        <a href="{{ route('pekerjaan.index') }}">Rencana Pekerjaan</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Info Badge menggantikan tombol tambah --}}
        <div class="info-badge">
            <i class="fas fa-info-circle me-2"></i>
            <span>Data dari Database Pekerjaan</span>
        </div>
    </div>

    <hr>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Info Box --}}
    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-lightbulb fa-2x me-3"></i>
        <div>
            <h6 class="mb-1 fw-bold">Informasi</h6>
            <small>
                <i class="fas fa-hand-pointer me-1"></i>
                <strong>Klik pada card</strong> untuk melihat detail pekerjaan.
                Untuk menambah atau mengedit data, silakan ke menu
                <strong><a href="{{ route('realisasi.index', ['tab' => 'database']) }}"
                        class="text-decoration-none">Database Pekerjaan</a></strong>
            </small>
        </div>
    </div>

    <div class="row">
        @forelse($pekerjaans as $pekerjaan)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden card-pekerjaan"
                data-url="{{ route('pekerjaan.detail', $pekerjaan->id) }}"
                onclick="handleCardClick(event, '{{ route('pekerjaan.detail', $pekerjaan->id) }}')">

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
                                aria-expanded="false" onclick="event.stopPropagation()">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('pekerjaan.detail', $pekerjaan->id) }}"
                                        onclick="event.stopPropagation()">
                                        <i class="fa fa-eye text-info me-2"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('realisasi.index', ['tab' => 'database']) }}"
                                        onclick="event.stopPropagation()">
                                        <i class="fa fa-list text-warning me-2"></i> Lihat di Database
                                    </a>
                                </li>
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
                                {{ number_format($pekerjaan->total_dana ?? 0,0,',','.') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center p-5 card shadow-sm">
                <i class="fas fa-folder-open fa-5x text-muted mb-4"></i>
                <h5 class="fw-bold">Belum Ada Rencana Kerja</h5>
                <p class="text-muted">Belum ada data pekerjaan yang tersedia. Silakan tambahkan data di Database
                    Pekerjaan.</p>
                @if(auth()->user()->role === 'superadmin')
                <a href="{{ route('realisasi.index', ['tab' => 'database']) }}" class="btn btn-primary mt-3">
                    <i class="fas fa-database me-2"></i> Ke Database Pekerjaan
                </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fungsi untuk handle card click
function handleCardClick(event, url) {
    // Cek apakah yang diklik adalah dropdown atau link di dalam card
    const target = event.target;
    const isDropdown = target.closest('.dropdown');
    const isLink = target.closest('a');

    // Jika yang diklik bukan dropdown atau link, redirect ke detail
    if (!isDropdown && !isLink) {
        window.location.href = url;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Animasi untuk cards dengan stagger effect
    const cards = document.querySelectorAll('.card-pekerjaan');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Optional: Tambah visual feedback saat card diklik
    cards.forEach(card => {
        card.addEventListener('mousedown', function() {
            this.style.transform = 'scale(0.98)';
        });

        card.addEventListener('mouseup', function() {
            this.style.transform = '';
        });
    });

    // Keyboard navigation (Enter key)
    cards.forEach(card => {
        card.setAttribute('tabindex', '0');
        card.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const url = this.getAttribute('data-url');
                window.location.href = url;
            }
        });
    });
});
</script>
@endpush