@extends('app')

@section('judul', 'Data Kriteria')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.index') }}">Kriteria</a></li>
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
                    <h4>{{ $kriteria->kd_kriteria }} - {{ $kriteria->nama_kriteria }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <label for="kd_kriteria" class="col-sm-2 col-form-label">Kode Kriteria</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="kd_kriteria" value="{{ $kriteria->kd_kriteria }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="kd_kriteria" class="col-sm-2 col-form-label">Kriteria</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="kd_kriteria" value="{{ $kriteria->nama_kriteria }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="bobot" class="col-sm-2 col-form-label">Bobot</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="bobot" value="{{ $kriteria->bobot }}" readonly>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="">Indikator</h4>
                            <a href="{{ route('indikator.tambah', ['kriteria_id' => $kriteria->id]) }}" class="btn btn-sm btn-primary">Tambah Indikator</a>
                        </div>
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Indikator</th>
                                <th>Nama Indikator</th>
                                <th>Bobot Indikator</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($kriteria->indikator as $indikator)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $indikator->kd_indikator ?? '-' }}</td>
                                    <td>{{ $indikator->nama_indikator }}</td>
                                    <td>{{ $indikator->bobot_indikator ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('indikator.edit', $indikator->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <a href="{{ route('indikator.detail', $indikator->id) }}" class="btn btn-sm btn-warning">Detail</a>
                                        <form action="{{ route('indikator.hapus', $indikator->id) }}" class="d-inline" method="post">
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
