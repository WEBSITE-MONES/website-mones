@extends('Dashboard.base')

@section('title', 'Edit Profile')

@section('content')
<div class="page-inner">
    <div class="card">
        <div class="card-header text-center">
            <h3 class="card-title">Edit Profile</h3>
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

        <form action="{{ route('account.update') }}" method="POST" class="form-horizontal">
            @csrf
            @method('PUT')

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

                <div class="row">
                    {{-- Kiri --}}
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}"
                                placeholder="Masukan nama lengkap.." required>
                        </div>

                        <div class="form-group">
                            <label>Tgl. Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $profile->tanggal_lahir) }}">
                        </div>

                        <div class="form-group">
                            <label>Jenis Kelamin <span class="text-danger">*</span></label>
                            <div class="form-check pt-0 pb-0 ml-2">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="L"
                                        {{ old('jenis_kelamin', $profile->jenis_kelamin) == 'L' ? 'checked' : '' }}>
                                    Laki-laki
                                </label>
                                <label class="form-check-label ml-3">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="P"
                                        {{ old('jenis_kelamin', $profile->jenis_kelamin) == 'P' ? 'checked' : '' }}>
                                    Perempuan
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Kanan --}}
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Jabatan</label>
                            <input type="text" class="form-control" name="jabatan"
                                value="{{ old('jabatan', optional($profile)->jabatan) }}"
                                placeholder="Masukan jabatan..">
                        </div>

                        <div class="form-group">
                            <label>Agama</label>
                            <select name="agama" class="form-control">
                                <option value="">-- Pilih Agama --</option>
                                @foreach(['Islam','Kristen Katolik','Kristen
                                Protestan','Hindu','Budha','Konghucu','Lainnya'] as $agama)
                                <option value="{{ $agama }}"
                                    {{ old('agama', $profile->agama) == $agama ? 'selected' : '' }}>
                                    {{ $agama }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>No. Telepon</label>
                            <input type="text" class="form-control" name="nomor_telepon"
                                value="{{ old('nomor_telepon', $profile->nomor_telepon) }}" placeholder="Ex : 0852xxx"
                                maxlength="13">
                        </div>
                    </div>

                    {{-- Full --}}
                    <div class="col-12 col-lg-12">
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"
                                placeholder="Masukan alamat lengkap..">{{ old('alamat', $profile->alamat) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-right bg-light">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-check"></i> Simpan
                </button>
                <button type="reset" class="btn btn-sm btn-danger">
                    <i class="fa fa-undo"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>
@endsection