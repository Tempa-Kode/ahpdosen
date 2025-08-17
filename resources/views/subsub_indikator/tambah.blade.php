@extends('app')
{{--@dd($subIndikator)--}}
@section('judul', 'Tambah Data Sub Indikator')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.detail', $subIndikator->indikator->kriteria->id) }}">Kriteria</a></li>
            <li class="breadcrumb-item"><a href="{{ route('indikator.detail', $subIndikator->indikator->id) }}">Indikator</a></li>
            <li class="breadcrumb-item"><a href="{{ route('subindikator.detail', $subIndikator->id) }}">Sub Indikator</a></li>
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
                    <h4>Input Data Sub Indikator {{ $subIndikator->nama_sub_indikator }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('subsubindikator.simpan') }}" class="forms-sample" method="post">
                        @csrf
                        @method('POST')
                        <input type="number" name="sub_indikator_id" id="sub_indikator_id" value="{{ $subIndikator->id }}" hidden>
                        <div class="mb-3">
                            <label for="nama_sub_sub_indikator" class="form-label">Nama Sub Sub Indikator <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_sub_sub_indikator" name="nama_sub_sub_indikator" autocomplete="off" value="{{ old('nama_sub_sub_indikator') }}" required>
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
