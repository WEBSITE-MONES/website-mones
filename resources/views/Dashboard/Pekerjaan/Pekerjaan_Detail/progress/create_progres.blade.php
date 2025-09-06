@extends('Dashboard.base')

@section('title', 'Input Progress Bulanan')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Input Progress Bulanan</h4>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card card-round">
        <div class="card-header text-center">
            <h3 class="card-title">Form Input Progress Bulanan</h3>
        </div>

        <form action="{{ route('pekerjaan.progress.store', $pekerjaan->id) }}" method="POST">
            @csrf
            <div class="card-body">

                <div class="row">
                    {{-- Bulan --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Bulan <span class="text-danger">*</span></label>
                            <input type="month" name="bulan" class="form-control" required>
                        </div>
                    </div>

                    {{-- Rencana --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Rencana (%) <span class="text-danger">*</span></label>
                            <input type="number" name="rencana" class="form-control" step="0.01" min="0" max="100"
                                required>
                        </div>
                    </div>

                    {{-- Realisasi --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Realisasi (%) <span class="text-danger">*</span></label>
                            <input type="number" name="realisasi" class="form-control" step="0.01" min="0" max="100"
                                required>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer text-right">
                <a href="{{ route('pekerjaan.progres.fisik', $pekerjaan->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-undo mr-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-check mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection