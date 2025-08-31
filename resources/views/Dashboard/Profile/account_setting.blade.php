@extends('Dashboard.base')

@section('title', 'Account Setting')

@section('content')
<div class="page-inner">
    <div class="card">
        <div class="card-header text-center">
            <h3 class="card-title">Account Setting</h3>
        </div>

        {{-- tampilkan error validasi --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- tampilkan pesan sukses --}}
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('account.setting.update') }}" method="POST" class="form-horizontal">
            @csrf

            <div class="card-body">
                <div class="col-md-12 mt-3">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle fa-x5"></i>
                        <small>
                            <cite title="Source Title">
                                Inputan yang ditanda bintang merah (<span class="text-danger">*</span>) harus
                                diisi!
                            </cite>
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control" value="{{ $user->username }}" disabled>
                            </div>
                            <div class="form-group">
                                <label>Password Lama <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right bg-light">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fa fa-check"></i> Simpan Perubahan
                    </button>
                    <button type="reset" class="btn btn-sm btn-danger">
                        <i class="fa fa-undo"></i> Reset
                    </button>
                </div>
        </form>
    </div>
</div>
@endsection