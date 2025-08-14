@extends('app')

@section('judul', 'Edit Data Indikator')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.index') }}">Kriteria</a></li>
            <li class="breadcrumb-item"><a href="">Detail Kriteria</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Indikator</li>
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
                    <h4>Edit Data Indikator {{ $indikator->nama_indikator }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('indikator.update', $indikator->id) }}" class="forms-sample" method="post">
                        @csrf
                        @method('POST')
                        <input type="number" name="kriteria_id" id="kriteria_id" value="{{ $indikator->id }}" hidden>
                        <div class="mb-3">
                            <label for="nama_indikator" class="form-label">Nama Indikator</label>
                            <input type="text" class="form-control" id="nama_indikator" name="nama_indikator" autocomplete="off" value="{{ old('nama_indikator', $indikator->nama_indikator) }}">
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Edit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
