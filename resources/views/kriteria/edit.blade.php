@extends('app')

@section('judul', 'Edit Data Kriteria')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.index') }}">Kriteria</a></li>
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
                    <h4>Edit Data Kriteria</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('kriteria.update', $kriteria->id) }}" class="forms-sample" method="post">
                        @csrf
                        @method('POST')
                        <div class="mb-3">
                            <label for="kd_kriteria" class="form-label">Kode Kriteria</label>
                            <input type="text" class="form-control" id="kd_kriteria" name="kd_kriteria" autocomplete="off" value="{{ old('kd_kriteria', $kriteria->kd_kriteria) }}">
                        </div>
                        <div class="mb-3">
                            <label for="nama_kriteria" class="form-label">Nama Kriteria</label>
                            <input type="text" class="form-control" id="nama_kriteria" name="nama_kriteria" autocomplete="off" value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}">
                        </div>
                        <div class="mb-3">
                            <label for="bobot" class="form-label">Bobot</label>
                            <input type="text" class="form-control" id="bobot" name="bobot" autocomplete="off" value="{{ old('bobot', $kriteria->bobot) }}">
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Edit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
