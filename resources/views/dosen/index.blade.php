@extends('app')

@section('judul', 'Data Dosen')

@section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dosen</li>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Data Dosen</h4>
                    <a href="{{ route('dosen.tambah') }}" class="btn btn-primary">Tambah Data Dosen</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>NIDN</th>
                                <th>Nama Dosen</th>
                                <th>Prodi</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($dosen as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nidn }}</td>
                                        <td>{{ $item->nama_dosen }}</td>
                                        <td>{{ $item->prodi }}</td>
                                        <td>
                                            <a href="{{ route('dosen.edit', $item->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                            <form action="{{ route('dosen.hapus', $item->id) }}" class="d-inline" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                            <a href="{{ route('penilaian.form', $item->id) }}" class="btn btn-sm btn-info text-white">Input Nilai</a>
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
