@extends('Dashboard.base')

@section('title', 'Realisasi Pekerjaan')

@section('content')
<div class="page-inner">
    {{-- HEADER HALAMAN --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
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
                    <a href="{{ route('realisasi.index') }}">Realisasi Pekerjaan</a>
                </li>
            </ul>
        </div>
        <!-- <div class="d-flex align-items-center mt-2 mt-md-0">
            <label for="yearFilter" class="me-2 form-label mb-0 fw-bold">Tahun:</label>
            <select id="yearFilter" class="form-select form-select-sm me-3" style="width:100px;">
                <option value="2025" selected>2025</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
            </select>

            <a href="{{ route('realisasi.createPR') }}" class="btn btn-primary btn-round">
                <i class="fa fa-plus me-2"></i> Input PR
            </a>
        </div> -->
    </div>

    {{-- KONTEN UTAMA DENGAN TAB --}}
    <div class="row mt-3">
        <div class="col-md-12">
            {{-- Menggunakan shadow-sm untuk memberikan efek "mengambang" yang modern --}}
            <div class="card card-round shadow-sm">
                <div class="card-header bg-transparent pt-3 border-0">
                    {{-- STRUKTUR NAVIGASI TAB --}}
                    <ul class="nav nav-pills" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="realisasi-tab" data-bs-toggle="tab"
                                data-bs-target="#realisasi-content" type="button" role="tab"
                                aria-controls="realisasi-content" aria-selected="true">
                                Realisasi Berjalan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="database-tab" data-bs-toggle="tab"
                                data-bs-target="#database-content" type="button" role="tab"
                                aria-controls="database-content" aria-selected="false">
                                Database Pekerjaan
                            </button>
                        </li>

                        <button class="btn btn-primary btn-round ms-auto"
                            onclick="window.location='{{ route('realisasi.createPR') }}'">
                            <i class="fa fa-plus me-2"></i> Input PR
                        </button>

                    </ul>

                </div>

                {{-- Menambah padding p-4 untuk memberi ruang napas pada konten --}}
                <div class="card-body p-4">
                    {{-- KONTEN DARI MASING-MASING TAB --}}
                    <div class="tab-content" id="myTabContent">


                        {{-- KONTEN TAB 1: Realisasi Berjalan --}}
                        <div class="tab-pane fade show active" id="realisasi-content" role="tabpanel"
                            aria-labelledby="realisasi-tab">
                            <h4 class="card-title mb-4">Daftar Realisasi Pekerjaan Berjalan</h4>
                            <div class="table-responsive">
                                {{-- Mengganti table-bordered dengan table-striped dan menambahkan align-middle --}}
                                <table id="tabelRealisasi" class="table table-striped table-hover align-middle"
                                    style="width:100%">
                                    <thead class="text-muted">
                                        {{-- Menggunakan text-uppercase dan small untuk header yang lebih rapi --}}
                                        <tr class="text-uppercase small">
                                            <th class="text-center">Aksi</th>
                                            <th>Status Pekerjaan</th>
                                            <th>Nomor Prodef SAP</th>
                                            <th>Nama Investasi</th>
                                            <th>Waktu</th>
                                            <th class="text-end">Nilai Pekerjaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($prs as $pr)
                                        @php
                                        $pekerjaan = $pr->pekerjaan;
                                        $currentStatus = $pr->status_pekerjaan ?? 'PR';
                                        $po = $pr->po ?? null;
                                        $progress = $po?->progresses()->latest()->first() ?? null;
                                        $gr = $pr->gr ?? null;
                                        $payment = $pr->payment ?? null;

                                        $statusInfo = [
                                        'PR' => ['label' => 'Perencanaan (PR)', 'badge' => 'bg-warning text-dark'],
                                        'PO' => ['label' => 'Kontrak (PO)', 'badge' => 'bg-primary'],
                                        'Progress' => ['label' => 'Progress', 'badge' => 'bg-purple'],
                                        'GR' => ['label' => 'Realisasi (GR)', 'badge' => 'bg-info text-dark'],
                                        'Payment' => ['label' => 'Payment Request', 'badge' => 'bg-success'],
                                        ];

                                        $display = $statusInfo[$currentStatus] ?? ['label' => '-', 'badge' =>
                                        'bg-secondary'];

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
                                                    {{-- Mengganti button agar lebih minimalis --}}
                                                    <button class="btn btn-sm btn-icon btn-link-secondary" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#"><i
                                                                    class="fa fa-eye text-info me-2"></i> Rincian</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editPR', $pr->id) }}"><i
                                                                    class="fa fa-edit text-primary me-2"></i> Edit
                                                                PR</a></li>
                                                        @if($currentStatus == 'PR')
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.createPO', $pr->id) }}"><i
                                                                    class="fa fa-plus text-warning me-2"></i> Tambah
                                                                PO</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.createGR', $pr->id) }}"><i
                                                                    class="fa fa-plus text-info me-2"></i> Tambah GR</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.createPayment', $pr->id) }}"><i
                                                                    class="fa fa-plus text-success me-2"></i> Tambah
                                                                Payment</a></li>
                                                        @else
                                                        @if($po)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editPO', $po->id) }}"><i
                                                                    class="fa fa-edit text-primary me-2"></i> Edit
                                                                PO</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editProgress', $po->id) }}"><i
                                                                    class="fa fa-tasks text-purple me-2"></i> Edit
                                                                Progress</a></li>
                                                        @endif
                                                        @if($gr)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editGR', $pr->id) }}"><i
                                                                    class="fa fa-edit text-info me-2"></i> Edit GR</a>
                                                        </li>
                                                        @else
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.createGR', $pr->id) }}"><i
                                                                    class="fa fa-plus text-info me-2"></i> Tambah GR</a>
                                                        </li>
                                                        @endif
                                                        @if($payment)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.editPayment', [$pr->id, $payment->id]) }}"><i
                                                                    class="fa fa-edit text-success me-2"></i> Edit
                                                                Payment</a></li>
                                                        @else
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('realisasi.createPayment', $pr->id) }}"><i
                                                                    class="fa fa-plus text-success me-2"></i> Tambah
                                                                Payment</a></li>
                                                        @endif
                                                        @endif

                                                        @if(auth()->user()->role === 'superadmin')
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('realisasi.destroy', $pr->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Yakin hapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item text-danger"><i
                                                                        class="fa fa-trash me-2"></i> Hapus</button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                            {{-- Menggunakan badge untuk status --}}
                                            <td><span
                                                    class="badge {{ $display['badge'] }}">{{ $display['label'] }}</span>
                                            </td>
                                            <td>{{ $pekerjaan->nomor_prodef_sap ?? '-' }}</td>
                                            <td>{{ $pekerjaan->nama_investasi ?? '-' }}</td>
                                            <td>{{ $waktu }}</td>
                                            {{-- Menambahkan class text-end untuk perataan kanan pada angka --}}
                                            <td class="text-end">Rp {{ number_format($nilai, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $prs->links('pagination::bootstrap-5') }}
                            </div>
                        </div>

                        {{-- KONTEN TAB 2: Database Pekerjaan --}}
                        <div class="tab-pane fade" id="database-content" role="tabpanel" aria-labelledby="database-tab">
                            <h4 class="card-title mb-4">Database Semua Pekerjaan</h4>
                            <div class="table-responsive">
                                <table id="tabelDatabase" class="table table-striped table-hover align-middle"
                                    style="width:100%">
                                    <thead class="text-muted">
                                        <tr class="text-uppercase small">
                                            <th class="text-center">Aksi</th>
                                            <th>Nomor Prodef SAP</th>
                                            <th>Nama Investasi</th>
                                            <th>Tahun Usulan</th>
                                            <th>COA</th>
                                            <th>Program Investasi</th>
                                            <th>Tipe Investasi</th>
                                            <th class="text-end">Kebutuhan Dana</th>
                                            <th>Kategori Investasi</th>
                                            <th>Manfaat Investasi</th>
                                            <th>Jenis Investasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pekerjaans as $pk)
                                        <tr>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-icon btn-link-secondary" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#"><i
                                                                    class="fa fa-eye text-info me-2"></i> Rincian</a>
                                                        </li>
                                                        <li><a class="dropdown-item" href="#"><i
                                                                    class="fa fa-edit text-primary me-2"></i> Edit</a>
                                                        </li>
                                                        @if(auth()->user()->role === 'superadmin')
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('pekerjaan.destroy', $pk->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Yakin hapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item text-danger"><i
                                                                        class="fa fa-trash me-2"></i> Hapus</button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>{{ $pk->nomor_prodef_sap }}</td>
                                            <td>{{ $pk->nama_investasi }}</td>
                                            <td>{{ $pk->tahun_usulan }}</td>
                                            <td>{{ $pk->coa }}</td>
                                            <td>{{ $pk->program_investasi }}</td>
                                            <td>{{ $pk->tipe_investasi }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($pk->kebutuhan_dana, 0, ',', '.') }}</td>
                                            <td>{{ $pk->masterInvestasi->kategori ?? '-' }}</td>
                                            <td>{{ $pk->masterInvestasi->manfaat ?? '-' }}</td>
                                            <td>{{ $pk->masterInvestasi->jenis ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $pekerjaans->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Style tambahan untuk badge ungu dan button icon --}}
<style>
.bg-purple {
    background-color: #6f42c1 !important;
    color: white;
}

.text-purple {
    color: #6f42c1 !important;
}

.btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.btn-link-secondary {
    color: #6c757d;
    text-decoration: none;
}

.btn-link-secondary:hover,
.btn-link-secondary:focus {
    background-color: #e9ecef;
    color: #495057;
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Pengaturan ini tetap sama, karena sudah bagus
    const dataTableOptions = {
        pageLength: 10,
        responsive: false,
        // scrollX: true,
        language: {
            paginate: {
                previous: "Previous",
                next: "Next"
            },
            search: "_INPUT_",
            searchPlaceholder: "Pencarian...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari _MAX_ total data)"
        }
    };

    $('#tabelRealisasi').DataTable(dataTableOptions);
    $('#tabelDatabase').DataTable(dataTableOptions);
});
</script>
@endpush

@endsection