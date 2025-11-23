@extends('Dashboard.base')

@section('title', 'Realisasi Pekerjaan')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/realisasi.css') }}">
<!-- <link rel="stylesheet" href="{{ asset('assets/css/realisasi_scroll_fix.css') }}"> -->
@endpush

@section('content')
<div class="page-inner">
    {{-- HEADER HALAMAN --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Database Pekerjaan</h3>
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
                    <a href="{{ route('realisasi.index') }}">Database Pekerjaan</a>
                </li>
            </ul>
        </div>
    </div>

    {{-- KONTEN UTAMA DENGAN TAB --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <ul class="nav nav-pills nav-pills-custom" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('tab') != 'database' ? 'active' : '' }}"
                                    id="realisasi-tab" data-bs-toggle="tab" data-bs-target="#realisasi-content"
                                    type="button" role="tab" aria-controls="realisasi-content"
                                    aria-selected="{{ request('tab') != 'database' ? 'true' : 'false' }}">
                                    <i class="fas fa-tasks me-2"></i>
                                    <span>Realisasi Berjalan</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('tab') == 'database' ? 'active' : '' }}"
                                    id="database-tab" data-bs-toggle="tab" data-bs-target="#database-content"
                                    type="button" role="tab" aria-controls="database-content"
                                    aria-selected="{{ request('tab') == 'database' ? 'true' : 'false' }}">
                                    <i class="fas fa-database me-2"></i>
                                    <span>Database Pekerjaan</span>
                                </button>
                            </li>
                        </ul>

                        {{-- Tombol dinamis berdasarkan tab aktif --}}
                        <div>
                            {{-- Tombol Input PR - tampil di tab Realisasi Berjalan --}}
                            <a href="{{ route('realisasi.createPR') }}"
                                class="btn btn-primary btn-round shadow-sm btn-tab-action" data-tab="realisasi"
                                style="display: {{ request('tab') != 'database' ? 'inline-flex' : 'none' }};">
                                <i class="fa fa-plus me-2"></i> Input PR
                            </a>

                            {{-- Tombol Tambah Pekerjaan - tampil di tab Database --}}
                            @if(auth()->user()->role === 'superadmin')
                            <a href="{{ route('pekerjaan.create') }}"
                                class="btn btn-success btn-round shadow-sm btn-tab-action" data-tab="database"
                                style="display: {{ request('tab') == 'database' ? 'inline-flex' : 'none' }};">
                                <i class="fa fa-plus me-2"></i> Tambah Pekerjaan
                            </a>
                            @endif
                        </div>
                    </div>
                </div>


                {{-- TAB CONTENT --}}
                <div class="card-body p-4">
                    <div class="tab-content" id="myTabContent">

                        {{-- TAB 1: Realisasi Berjalan --}}
                        <div class="tab-pane fade {{ request('tab') != 'database' ? 'show active' : '' }}"
                            id="realisasi-content" role="tabpanel" aria-labelledby="realisasi-tab">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold text-dark mb-0">Daftar Realisasi Pekerjaan Berjalan</h5>
                            </div>

                            <div class="table-responsive">
                                <table id="tabelRealisasi" class="table table-hover align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 80px;">Aksi</th>
                                            <th style="width: 180px;">Status</th>
                                            <th>Nomor Prodef SAP</th>
                                            <th>Nama Investasi</th>
                                            <th style="width: 160px;">Waktu</th>
                                            <th class="text-end" style="width: 180px;">Nilai Pekerjaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        // Urutkan data berdasarkan status
                                        $sortedPrs = $prs->sortBy(function($pr) {
                                        $status = $pr->status_pekerjaan ?? 'PR';
                                        $statusOrder = [
                                        'PR' => 1,
                                        'PO' => 2,
                                        'Progress' => 3,
                                        'GR' => 4,
                                        'Payment' => 5
                                        ];
                                        return $statusOrder[$status] ?? 6;
                                        });
                                        @endphp

                                        @foreach($sortedPrs as $pr)
                                        @php
                                        $pekerjaan = $pr->pekerjaan;
                                        $currentStatus = $pr->status_pekerjaan ?? 'PR';
                                        $po = $pr->po ?? null;
                                        $progress = $po?->progresses()->latest()->first() ?? null;
                                        $gr = $pr->gr ?? null;
                                        $payment = $pr->payment ?? null;

                                        $statusInfo = [
                                        'PR' => ['label' => 'Perencanaan (PR)', 'badge' => 'badge-warning'],
                                        'PO' => ['label' => 'Kontrak (PO)', 'badge' => 'badge-primary'],
                                        'Progress' => ['label' => 'Progress', 'badge' => 'badge-purple'],
                                        'GR' => ['label' => 'Realisasi (GR)', 'badge' => 'badge-info'],
                                        'Payment' => ['label' => 'Payment Request', 'badge' => 'badge-success'],
                                        ];

                                        $display = $statusInfo[$currentStatus] ?? ['label' => '-', 'badge' =>
                                        'badge-secondary'];

                                        if ($currentStatus == 'PR') {
                                        $waktu = $pr->tanggal_pr ? 'Tgl. PR: ' .
                                        \Carbon\Carbon::parse($pr->tanggal_pr)->format('d M Y') : '-';
                                        $nilai = $pr->nilai_pr ?? 0;
                                        } elseif ($currentStatus == 'PO' && $po) {
                                        $waktu = $po->tanggal_po ? 'Tgl. PO: ' .
                                        \Carbon\Carbon::parse($po->tanggal_po)->format('d M Y') : '-';
                                        $nilai = $po->nilai_po ?? 0;
                                        } elseif ($currentStatus == 'GR' && $gr) {
                                        $waktu = $gr->tanggal_gr ? 'Tgl. GR: ' .
                                        \Carbon\Carbon::parse($gr->tanggal_gr)->format('d M Y') : '-';
                                        $nilai = $po->nilai_po ?? 0;
                                        } elseif ($currentStatus == 'Payment' && $payment) {
                                        $waktu = $payment->tanggal_payment ? 'Tgl. Payment: ' .
                                        \Carbon\Carbon::parse($payment->tanggal_payment)->format('d M Y') : '-';
                                        $nilai = $po->nilai_po ?? 0;
                                        } else {
                                        $waktu = '-';
                                        $nilai = 0;
                                        }
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light rounded-circle p-0"
                                                        type="button" data-bs-toggle="dropdown"
                                                        style="width: 32px; height: 32px;">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                        <li>
                                                            <h6 class="dropdown-header text-uppercase small">Menu Aksi
                                                            </h6>
                                                        </li>
                                                        <li><a class="dropdown-item" href="#">
                                                                <i class="fa fa-eye text-info me-2"></i> Rincian</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editPR', $pr->id) }}">
                                                                <i class="fa fa-edit text-primary me-2"></i> Edit PR</a>
                                                        </li>

                                                        {{-- URUTAN: 1. PO --}}
                                                        @if($currentStatus == 'PR')
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.createPO', $pr->id) }}">
                                                                <i class="fa fa-plus text-warning me-2"></i> Tambah
                                                                PO</a>
                                                        </li>
                                                        @else
                                                        @if($po)
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editPO', $po->id) }}">
                                                                <i class="fa fa-edit text-primary me-2"></i> Edit PO</a>
                                                        </li>

                                                        {{-- URUTAN: 2. Edit Progress --}}
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editProgress', $po->id) }}">
                                                                <i class="fa fa-tasks text-purple me-2"></i> Edit
                                                                Progress</a>
                                                        </li>
                                                        @endif

                                                        {{-- URUTAN: 3. GR --}}
                                                        @if($gr)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editGR', $pr->id) }}">
                                                                <i class="fa fa-edit text-info me-2"></i> Edit GR</a>
                                                        </li>
                                                        @else
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.createGR', $pr->id) }}">
                                                                <i class="fa fa-plus text-info me-2"></i> Tambah GR</a>
                                                        </li>
                                                        @endif

                                                        {{-- URUTAN: 4. Payment --}}
                                                        @if($payment)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editPayment', [$pr->id, $payment->id]) }}">
                                                                <i class="fa fa-edit text-success me-2"></i> Edit
                                                                Payment</a>
                                                        </li>
                                                        @else
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.createPayment', $pr->id) }}">
                                                                <i class="fa fa-plus text-success me-2"></i> Tambah
                                                                Payment</a>
                                                        </li>
                                                        @endif
                                                        @endif

                                                        @if(auth()->user()->role === 'superadmin')
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('realisasi.destroy', $pr->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    class="dropdown-item text-danger btn-hapus">
                                                                    <i class="fa fa-trash me-2"></i> Hapus
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-custom {{ $display['badge'] }}">
                                                    {{ $display['label'] }}
                                                </span>
                                            </td>
                                            <td class="fw-medium">{{ $pekerjaan->nomor_prodef_sap ?? '-' }}</td>
                                            <td>
                                                {{ $pr->subPekerjaan->first()->nama_sub ?? '-' }}
                                            </td>
                                            <td><small class="text-muted">{{ $waktu }}</small></td>
                                            <td class="text-end fw-semibold text-primary">
                                                Rp {{ number_format($nilai, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 2: Database Pekerjaan --}}
                        <div class="tab-pane fade {{ request('tab') == 'database' ? 'show active' : '' }}"
                            id="database-content" role="tabpanel" aria-labelledby="database-tab">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold text-dark mb-0">Database Master Pekerjaan</h5>
                            </div>

                            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                <table id="tabelDatabase" class="table table-hover align-middle">
                                    <thead style="position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            @if(auth()->user()->role === 'superadmin')
                                            <th style="width: 80px;">Aksi</th>
                                            @endif
                                            <th>Nama Investasi</th>
                                            <th>Entitas</th>
                                            <th>Nomor Prodef Sap</th>
                                            <th>Tahun Usulan</th>
                                            <th>COA</th>
                                            <th>Tipe Investasi</th>
                                            <th>Kategori Investasi</th>
                                            <th>Manfaat Investasi</th>
                                            <th>Jenis Investasi</th>
                                            <th>Kebutuhan Dana</th>
                                            <th>RKAP</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            @if(auth()->user()->role === 'superadmin')
                                            <th>Aksi</th>
                                            @endif
                                            <th>Nama Investasi</th>
                                            <th>Entitas</th>
                                            <th>Nomor Prodef Sap</th>
                                            <th>Tahun Usulan</th>
                                            <th>COA</th>
                                            <th>Tipe Investasi</th>
                                            <th>Kategori Investasi</th>
                                            <th>Manfaat Investasi</th>
                                            <th>Jenis Investasi</th>
                                            <th>Kebutuhan Dana</th>
                                            <th>RKAP</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach($pekerjaans as $pk)
                                        <tr>
                                            @if(auth()->user()->role === 'superadmin')
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light rounded-circle p-0"
                                                        type="button" data-bs-toggle="dropdown"
                                                        style="width: 32px; height: 32px;">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                        <li>
                                                            <h6 class="dropdown-header text-uppercase small">Menu Aksi
                                                            </h6>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('pekerjaan.edit', $pk->id) }}">
                                                                <i class="fa fa-edit text-primary me-2"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('pekerjaan.destroy', $pk->id) }}"
                                                                method="POST" class="form-hapus-pekerjaan">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    class="dropdown-item text-danger btn-hapus-pekerjaan">
                                                                    <i class="fa fa-trash me-2"></i> Hapus
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            @endif
                                            <td class="fw-medium">{{ $pk->nama_investasi }}</td>
                                            <td class="fw-medium">{{ $pk->wilayah->nama }}</td>
                                            <td class="fw-medium">{{ $pk->nomor_prodef_sap }}</td>
                                            <td><span class="badge bg-light text-dark">{{ $pk->tahun_usulan }}</span>
                                            </td>
                                            <td><small class="text-muted">{{ $pk->coa }}</small></td>
                                            <td>{{ $pk->tipe_investasi }}</td>
                                            <td>{{ $pk->masterInvestasi->kategori ?? '-' }}</td>
                                            <td>{{ $pk->masterInvestasi->manfaat ?? '-' }}</td>
                                            <td>{{ $pk->masterInvestasi->jenis ?? '-' }}</td>
                                            <td class="text-end fw-semibold text-primary">
                                                Rp{{ number_format($pk->kebutuhan_dana, 0, ',', '.')}}
                                            </td>
                                            <td class="text-end fw-semibold text-success">
                                                Rp{{ number_format($pk->total_dana ?? 0, 0, ',', '.')}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/realisasi.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-hapus');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const form = this.closest('form');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data realisasi berjalan ini akan dihapus secara permanen!",
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

    const deletePekerjaanButtons = document.querySelectorAll('.btn-hapus-pekerjaan');
    deletePekerjaanButtons.forEach(button => {
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

    // Toggle button visibility based on active tab
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(event) {
            const targetTab = event.target.getAttribute('data-bs-target');
            const actionButtons = document.querySelectorAll('.btn-tab-action');

            actionButtons.forEach(btn => {
                if (targetTab === '#realisasi-content' && btn.getAttribute(
                        'data-tab') === 'realisasi') {
                    btn.style.display = 'inline-flex';
                } else if (targetTab === '#database-content' && btn.getAttribute(
                        'data-tab') === 'database') {
                    btn.style.display = 'inline-flex';
                } else {
                    btn.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush
@endsection