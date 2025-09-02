@extends('Dashboard.base')

@section('title', $wilayah->nama)

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Kota {{ $wilayah->nama }}</h4>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Data Pekerjaan Di Kota {{ $wilayah->nama }}</h4>
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
                                    <th>Nama Pekerjaan</th>
                                    <th>Status Pekerjaan</th>
                                    <th>Kebutuhan Dana</th>
                                    <th>Tahun</th>
                                    <th>Tanggal Pekerjaan</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wilayah->pekerjaans as $pekerjaan)
                                <tr>
                                    <td>{{ $pekerjaan->nama_pekerjaan }}</td>
                                    <td>{{ $pekerjaan->status }}</td>
                                    <td>Rp {{ number_format($pekerjaan->kebutuhan_dana, 0, ',', '.') }}</td>
                                    <td>{{ $pekerjaan->tahun }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pekerjaan->tanggal)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-start gap-1">
                                            {{-- Tombol Detail --}}
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $pekerjaan->id }}">
                                                <i class="fa fa-eye"></i>
                                            </button>

                                            @if(auth()->user()->role === 'superadmin')
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('pekerjaan.edit', $pekerjaan->id) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('pekerjaan.destroy', $pekerjaan->id) }}"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pekerjaan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>

                                            @endif
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
@endsection