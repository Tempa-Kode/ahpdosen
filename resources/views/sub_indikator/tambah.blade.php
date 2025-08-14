@extends('app')
{{--@dd($indikator)--}}
@section('judul', 'Tambah Data Sub Indikator')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.detail', $indikator->kriteria->id) }}">Kriteria</a></li>
            <li class="breadcrumb-item"><a href="{{ route('indikator.detail', $indikator->id) }}">Indikator</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Sub Indikator</li>
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
                    <h4>Input Data Sub Indikator {{ $indikator->nama_sub_indikator }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('subindikator.simpan') }}" class="forms-sample" method="post">
                        @csrf
                        @method('POST')
                        <input type="number" name="indikator_id" id="indikator_id" value="{{ $indikator->id }}" hidden>
                        <div class="mb-3">
                            <label for="nama_sub_indikator" class="form-label">Nama Sub Indikator <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_sub_indikator" name="nama_sub_indikator" autocomplete="off" value="{{ old('nama_sub_indikator') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="skor_kredit" class="form-label">Skor Kredit</label>
                            <input type="number" class="form-control" id="skor_kredit" name="skor_kredit" autocomplete="off" value="{{ old('skor_kredit') }}">
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
