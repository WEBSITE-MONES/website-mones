@extends('Dashboard.base')

@section('title', 'Realisasi Pekerjaan')

@section('content')
<div class="page-inner">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h4 class="page-title">Realisasi Anggaran Tahun {{ date('Y') }}</h4>

        <div class="d-flex">
            <select class="form-select me-2" style="width:120px">
                <option value="2025" selected>2025</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
            </select>

            <a href="{{ route('realisasi.createPR') }}" class="btn btn-primary btn-round">
                <i class="fa fa-plus"></i> Input PR
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-body">

                    <div class="table-responsive" style="max-height: 500px; overflow:auto;">
                        <table id="realisasiTable" class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ACTION</th>
                                    <th>Status Pekerjaan</th>
                                    <th>Nomor Prodef Sap</th>
                                    <th>Nama Investasi</th>
                                    <th>Waktu</th>
                                    <th>Nilai Pekerjaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $statusLabels = [
                                'PR' => ['label' => 'Perencanaan (PR)', 'class' => 'text-warning'],
                                'PO' => ['label' => 'Kontrak (PO)', 'class' => 'text-primary'],
                                'Progress'=> ['label' => 'Progress', 'class' => 'text-purple'],
                                'GR' => ['label' => 'Realisasi (GR)', 'class' => 'text-info'],
                                'Payment' => ['label' => 'Payment Request', 'class' => 'text-success'],
                                ];
                                @endphp

                                @foreach($prs as $pr)
                                @php
                                $pekerjaan = $pr->pekerjaan;
                                $currentStatus = $pr->status_pekerjaan ?? 'PR';
                                $po = $pr->po ?? null;
                                $progress = $po?->progresses()->latest()->first() ?? null;
                                $gr = $pr->gr ?? null;
                                $payment = $pr->payment ?? null;

                                $display = $statusLabels[$currentStatus] ?? ['label' => '-', 'class' => ''];

                                if ($currentStatus == 'PR') {
                                $waktu = $pr->tanggal_pr ? 'Tgl. PR: ' .
                                \Carbon\Carbon::parse($pr->tanggal_pr)->format('Y-m-d') : '-';
                                $nilai = $pr->nilai_pr ?? 0;
                                } elseif ($currentStatus == 'PO' && $po) {
                                $waktu = $po->tanggal_po ? 'Tgl. PO: ' .
                                \Carbon\Carbon::parse($po->tanggal_po)->format('Y-m-d') : '-';
                                $nilai = $po->nilai_po ?? 0;
                                } elseif ($currentStatus == 'GR' && $gr) {
                                $waktu = $gr->tanggal_gr ? 'Tgl. GR: ' .
                                \Carbon\Carbon::parse($gr->tanggal_gr)->format('Y-m-d') : '-';
                                $nilai = $po->nilai_po ?? 0;
                                } elseif ($currentStatus == 'Payment' && $payment) {
                                $waktu = $payment->tanggal_payment ? 'Tgl. Payment: ' .
                                \Carbon\Carbon::parse($payment->tanggal_payment)->format('Y-m-d') : '-';
                                $nilai = $po->nilai_po ?? 0;
                                } else {
                                $waktu = '-';
                                $nilai = 0;
                                }
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fa fa-eye text-info"></i> Rincian
                                                    </a>
                                                </li>

                                                {{-- Edit PR --}}
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('realisasi.editPR', $pr->id) }}">
                                                        <i class="fa fa-edit text-primary"></i> Edit PR
                                                    </a>
                                                </li>

                                                {{-- Tambah/Edit PO/GR/Payment --}}
                                                @if($currentStatus == 'PR')
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('realisasi.createPO', $pr->id) }}">
                                                        <i class="fa fa-plus text-warning"></i> Tambah PO
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('realisasi.createGR', $pr->id) }}">
                                                        <i class="fa fa-plus text-warning"></i> Tambah GR
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('realisasi.createPayment', $pr->id) }}">
                                                        <i class="fa fa-plus text-warning"></i> Tambah Payment
                                                    </a>
                                                </li>
                                                @else
                                                @if($po)
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('realisasi.editPO', $po->id) }}">
                                                        <i class="fa fa-edit text-primary"></i> Edit PO
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('realisasi.editProgress', $po->id) }}">
                                                        <i class="fa fa-tasks text-primary"></i> Edit Progress
                                                    </a>
                                                </li>
                                                @endif

                                                @if($gr)
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('realisasi.editGR', $pr->id) }}">
                                                        <i class="fa fa-edit text-primary"></i> Edit GR
                                                    </a>
                                                </li>
                                                @else
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('realisasi.createGR', $pr->id) }}">
                                                        <i class="fa fa-plus text-warning"></i> Tambah GR
                                                    </a>
                                                </li>
                                                @endif

                                                @if($payment)
                                                <li>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fa fa-edit text-primary"></i> Edit Payment
                                                    </a>
                                                </li>
                                                @else
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('realisasi.createPayment', $pr->id) }}">
                                                        <i class="fa fa-plus text-warning"></i> Tambah Payment
                                                    </a>
                                                </li>
                                                @endif
                                                @endif

                                                {{-- Hapus PR --}}
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
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="fw-bold {{ $display['class'] }}">
                                            {{ $display['label'] }}
                                        </span>
                                    </td>
                                    <td>{{ $pekerjaan->nomor_prodef_sap ?? '-' }}</td>
                                    <td>{{ $pekerjaan->nama_investasi ?? '-' }}</td>
                                    <td>{{ $waktu }}</td>
                                    <td>{{ number_format($nilai, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $prs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-purple {
    color: #6f42c1 !important;
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    $('#realisasiTable').DataTable({
        pageLength: 10,
        responsive: true,
        language: {
            paginate: {
                previous: "Previous",
                next: "Next"
            },
            search: "_INPUT_",
            searchPlaceholder: "Pencarian..",
            lengthMenu: "Tampilkan _MENU_ data"
        }
    });
});
</script>
@endpush
@endsection