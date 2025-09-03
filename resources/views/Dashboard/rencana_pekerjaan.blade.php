@extends('Dashboard.base')

@section('title', 'Daftar Rencana Kerja')

@section('content')
<div class="page-inner">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h4 class="page-title">Daftar Semua Rencana Kerja {{ request('tahun') ?? date('Y') }}</h4>

        <form id="filterForm" method="GET" action="{{ route('pekerjaan.index') }}" class="d-flex align-items-center">
            {{-- Search --}}
            <div class="form-outline me-3 flex-grow-1 position-relative">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5"
                    placeholder="Search ...">
                <i class="fas fa-search"
                    style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
            </div>

            {{-- Filter Tahun --}}
            <div class="input-group me-2" style="width: 150px;">
                <span class="input-group-text"><i class="fas fa-filter"></i></span>
                <select name="tahun" class="form-control">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($tahunList as $thn)
                    <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>
                        {{ $thn }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Rencana Kerja</h4>
                    @if(auth()->user()->role === 'superadmin')
                    <a href="{{ route('pekerjaan.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Input Rencana Kerja
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="add-row" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Wilayah</th>
                                    <th>Nama Pekerjaan</th>
                                    <th>Status</th>
                                    <th>Kebutuhan Dana</th>
                                    <th>Tahun</th>
                                    <th>Tanggal</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pekerjaans as $pekerjaan)
                                <tr>
                                    <td>{{ $pekerjaan->wilayah->nama }}</td>
                                    <td>{{ $pekerjaan->nama_pekerjaan }}</td>
                                    <td style="vertical-align: middle; white-space: nowrap;">{{ $pekerjaan->status }}
                                    </td>
                                    <td style="vertical-align: middle; white-space: nowrap;">Rp
                                        {{ number_format($pekerjaan->kebutuhan_dana, 0, ',', '.') }}</td>
                                    <td>{{ $pekerjaan->tahun }}</td>
                                    <td style="vertical-align: middle; white-space: nowrap;">
                                        {{ \Carbon\Carbon::parse($pekerjaan->tanggal)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="aksiDropdown{{ $pekerjaan->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu"
                                                aria-labelledby="aksiDropdown{{ $pekerjaan->id }}">
                                                <li>
                                                    <a href="{{ route('pekerjaan.show', $pekerjaan->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-eye me-2"></i> Detail
                                                    </a>
                                                </li>

                                                @if(auth()->user()->role === 'superadmin')
                                                <li>
                                                    <a href="{{ route('pekerjaan.edit', $pekerjaan->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-edit me-2"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('pekerjaan.destroy', $pekerjaan->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Hapus pekerjaan ini?');">
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
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada rencana kerja</td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $pekerjaans->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const searchInput = form.querySelector('input[name="search"]');
    const tahunSelect = form.querySelector('select[name="tahun"]');

    // Submit otomatis saat memilih tahun
    tahunSelect.addEventListener('change', function() {
        form.submit();
    });

    // Submit otomatis saat mengetik pencarian (enter)
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            form.submit();
        }
    });
});
</script>

@endsection