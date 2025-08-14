@extends('app')

@section('judul', 'Data Kriteria')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.detail', $indikator->kriteria->id) }}">Kriteria</a></li>
            <li class="breadcrumb-item"><a href="">Indikator</a></li>
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
                    <h4>{{ $indikator->nama_indikator }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <label for="kd_kriteria" class="col-sm-2 col-form-label">Kriteria</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="kd_kriteria" value="{{ $indikator->kriteria->kd_kriteria }} - {{ $indikator->kriteria->nama_kriteria }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="kd_kriteria" class="col-sm-2 col-form-label">Indikator</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="kd_kriteria" value="{{ $indikator->nama_indikator }}" readonly>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="">Sub Indikator</h4>
                            <a href="{{ route('subindikator.tambah', ['indikator_id' => $indikator->id]) }}" class="btn btn-sm btn-primary">Tambah Sub Indikator</a>
                        </div>
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Sub Indikator</th>
                                <th>Skor Kredit</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($indikator->subIndikator as $subIndikator)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $subIndikator->nama_sub_indikator }}</td>
                                        <td>{{ $subIndikator->skor_kredit ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('subindikator.edit', $subIndikator->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                            <a href="{{ route('subindikator.detail', $subIndikator->id) }}" class="btn btn-sm btn-warning">Detail</a>
                                            <form action="{{ route('subindikator.hapus', $subIndikator->id) }}" class="d-inline" method="post">
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
