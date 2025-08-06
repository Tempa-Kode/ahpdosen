@extends('app')

@section('judul', 'Edit Data Dosen')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Data Dosen</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('dosen.update', $dosen->id) }}" class="forms-sample" method="post">
                        @csrf
                        @method('POST')
                        <div class="mb-3">
                            <label for="nidn" class="form-label">NIDN</label>
                            <input type="number" class="form-control" id="nidn" name="nidn" autocomplete="off" value="{{ old('nidn', $dosen->nidn) }}">
                        </div>
                        <div class="mb-3">
                            <label for="nama_dosen" class="form-label">Nama Dosen</label>
                            <input type="text" class="form-control" id="nama_dosen" name="nama_dosen" autocomplete="off" value="{{ old('nama_dosen' , $dosen->nama_dosen) }}">
                        </div>
                        <div class="mb-3">
                            <label for="prodi" class="form-label">Program Studi</label>
                            <select class="form-select" id="prodi" name="prodi">
                                <option hidden value="">Pilih Prodi</option>
                                <option value="Teknik Informatika" {{ old('prodi', $dosen->prodi) == "Teknik Informatika" ? 'selected' : '' }}>Teknik Informatika</option>
                                <option value="Sistem Informasi" {{ old('prodi' , $dosen->prodi) == "Sistem Informasi" ? 'selected' : '' }}>Sistem Informasi</option>
                                <option value="Sains Data" {{ old('prodi' , $dosen->prodi) == "Sains Data" ? 'selected' : '' }}>Sains Data</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Edit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
