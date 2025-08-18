@extends('app')

@section('judul', 'Tambah Data Indikator')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.index') }}">Kriteria</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.detail', request('kriteria_id')) }}">Detail Kriteria</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Indikator</li>
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
                    <h4>Input Data Indikator {{ $kriteria->nama_kriteria }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('indikator.simpan') }}" class="forms-sample" method="post">
                        @csrf
                        @method('POST')
                        <input type="number" name="kriteria_id" id="kriteria_id" value="{{ $kriteria->id }}" hidden>
                        <div class="mb-3">
                            <label for="kd_indikator" class="form-label">Kode Indikator</label>
                            <input type="text" class="form-control" id="kd_indikator" name="kd_indikator" autocomplete="off" value="{{ old('kd_indikator') }}">
                        </div>
                        <div class="mb-3">
                            <label for="nama_indikator" class="form-label">Nama Indikator</label>
                            <input type="text" class="form-control" id="nama_indikator" name="nama_indikator" autocomplete="off" value="{{ old('nama_indikator') }}">
                        </div>
                        <div class="mb-3">
                            <label for="bobot_indikator" class="form-label">Bobot Indikator</label>
                            <input type="text" class="form-control" id="bobot_indikator" name="bobot_indikator" autocomplete="off" value="{{ old('bobot_indikator') }}">
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
