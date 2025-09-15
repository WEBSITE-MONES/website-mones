@extends('Dashboard.base')

@section('title', 'Progress Fisik Pekerjaan')

@section('content')
<div class="page-inner">
    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-3">
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Daftar Rencana Kerja</h4>
                    @if(auth()->user()->role === 'superadmin')
                    <a href="{{ route('pekerjaan.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Input Rencana Kerja
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="rencanaTable" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Unit Cabang</th>
                                    <th>COA</th>
                                    <th>Program Investasi</th>
                                    <th>Tipe Investasi</th>
                                    <th>Tahun Usulan</th>
                                    <th>Nomor Prodef SAP</th>
                                    <th>Nama Investasi</th>
                                    <th>Kebutuhan Dana</th>
                                    <th>RKAP</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pekerjaans as $pekerjaan)
                                <tr>
                                    <td>{{ $pekerjaan->wilayah?->nama ?? '-' }}</td>
                                    <td>{{ $pekerjaan->coa }}</td>
                                    <td>{{ $pekerjaan->program_investasi }}</td>
                                    <td>{{ $pekerjaan->tipe_investasi }}</td>
                                    <td>{{ $pekerjaan->tahun_usulan ?? '-' }}</td>
                                    <td>{{ $pekerjaan->nomor_prodef_sap }}</td>
                                    <td>{{ $pekerjaan->nama_investasi }}</td>
                                    <td>Rp {{ number_format($pekerjaan->kebutuhan_dana ?? 0,0,',','.') }}</td>
                                    <td>Rp {{ number_format($pekerjaan->rkap ?? 0,0,',','.') }}</td>
                                    <td>
                                        <div class="dropdown dropend">
                                            <!-- Tombol dropdown -->
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="aksiDropdown{{ $pekerjaan->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>

                                            <ul class="dropdown-menu"
                                                aria-labelledby="aksiDropdown{{ $pekerjaan->id }}">
                                                <!-- Tombol Detail -->
                                                <li>
                                                    <a href="{{ route('pekerjaan.detail', $pekerjaan->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-eye text-info"></i> Detail
                                                    </a>
                                                </li>

                                                <!-- Tombol Rincian -->
                                                <li>
                                                    <a href="#" class="dropdown-item">
                                                        <i class="fa fa-list text-warning"></i> Rincian
                                                    </a>
                                                </li>

                                                @if(auth()->user()->role === 'superadmin')
                                                <!-- Tombol Edit -->
                                                <li>
                                                    <a href="{{ route('pekerjaan.edit', $pekerjaan->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-edit text-primary"></i> Edit
                                                    </a>
                                                </li>

                                                <!-- Tombol Hapus -->
                                                <li>
                                                    <form action="{{ route('pekerjaan.destroy', $pekerjaan->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pekerjaan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-times"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
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

@push('scripts')
<script>
$('#rencanaTable').DataTable({
    pageLength: -1,
    scrollX: true,
    language: {
        paginate: {
            previous: "Previous",
            next: "Next"
        },
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        search: "_INPUT_",
        searchPlaceholder: "Search...",
        lengthMenu: "Tampilkan _MENU_ data"
    }
});
</script>
@endpush
@endsection