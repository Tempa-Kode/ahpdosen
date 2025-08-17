@extends('app')
{{--@dd($subIndikator)--}}
@section('judul', 'Data Kriteria')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.detail', $subIndikator->indikator->kriteria->id) }}">Kriteria</a></li>
            <li class="breadcrumb-item"><a href="{{ route('indikator.detail', $subIndikator->indikator->id) }}">Indikator</a></li>
            <li class="breadcrumb-item"><a href="">Sub Indikator</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav>
    @session('success')
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
    @endsession
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header d-flex justify-content-start align-items-center">
                    <h4>{{ $subIndikator->nama_sub_indikator }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <label for="kd_kriteria" class="col-sm-2 col-form-label">Kriteria</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="kd_kriteria" value="{{ $subIndikator->indikator->kriteria->kd_kriteria }} - {{ $subIndikator->indikator->kriteria->nama_kriteria }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="nama_indikator" class="col-sm-2 col-form-label">Indikator</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nama_indikator" value="{{ $subIndikator->indikator->nama_indikator }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="nama_sub_indikator" class="col-sm-2 col-form-label">Sub Indikator</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nama_sub_indikator" value="{{ $subIndikator->nama_sub_indikator }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="skor_kredit" class="col-sm-2 col-form-label">Skor Kredit</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="skor_kredit" value="{{ $subIndikator->skor_kredit ?? '-' }}" readonly>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="">Sub-Sub Indikator</h4>
                            <a href="{{ route('subsubindikator.tambah', ['sub_indikator_id' => $subIndikator->id]) }}" class="btn btn-sm btn-primary">Tambah Sub-Sub Indikator</a>
                        </div>
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Sub Sub Indikator</th>
                                <th>Skor Kredit</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($subIndikator->subSubIndikator as $index => $subSubIndikator)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $subSubIndikator->nama_sub_sub_indikator }}</td>
                                    <td>{{ $subSubIndikator->skor_kredit }}</td>
                                    <td>
                                        <a href="{{ route('subsubindikator.edit', $subSubIndikator->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('subsubindikator.hapus', $subSubIndikator->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus sub-sub indikator ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
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
@endsection
