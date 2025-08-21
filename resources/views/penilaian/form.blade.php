@extends('app') @section('konten')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
        </ol>
    </nav>

    <div class="page-content">
        <h6 class="card-title">Formulir Penilaian Dosen: {{ $dosen->nama_dosen }}</h6>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('penilaian.store', $dosen->id) }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Kriteria / Indikator</th>
                        <th style="width: 15%;">Nilai</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($kriterias as $kriteria)
                        <tr class="table-light">
                            <td colspan="2"><strong>{{ $kriteria->nama_kriteria }}</strong></td>
                        </tr>

                        @foreach ($kriteria->indikator as $indikator)
                            @if ($indikator->subIndikator->isNotEmpty())
                                {{-- JIKA INDIKATOR PUNYA ANAK, tampilkan sebagai header --}}
                                <tr>
                                    <td class="ps-4" colspan="2"><strong>{{ $indikator->nama_indikator }}</strong></td>
                                </tr>
                                @foreach ($indikator->subIndikator as $subIndikator)
                                    @if ($subIndikator->subSubIndikator->isNotEmpty())
                                        {{-- Jika sub-indikator punya anak, tampilkan sebagai header --}}
                                        <tr>
                                            <td class="ps-5" colspan="2">{{ $subIndikator->nama_sub_indikator }}</td>
                                        </tr>
                                        @foreach ($subIndikator->subSubIndikator as $subSub)
                                            @php
                                                $nilai = $subSub->penilaians->first()->nilai ?? '';
                                            @endphp
                                            <tr>
                                                <td class="ps-6">{{ $subSub->nama_sub_sub_indikator }}</td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control" name="nilai[sub_sub_indikator][{{ $subSub->id }}]" value="{{ $nilai }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        {{-- Jika sub-indikator TIDAK punya anak, tampilkan input nilai untuknya --}}
                                        @php
                                            $nilai = $subIndikator->penilaians->first()->nilai ?? '';
                                        @endphp
                                        <tr>
                                            <td class="ps-5">{{ $subIndikator->nama_sub_indikator }}</td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control" name="nilai[sub_indikator][{{ $subIndikator->id }}]" value="{{ $nilai }}">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                {{-- JIKA INDIKATOR TIDAK PUNYA ANAK, tampilkan input nilai untuknya --}}
                                @php
                                    $nilai = $indikator->penilaians->first()->nilai ?? '';
                                @endphp
                                <tr>
                                    <td class="ps-4"><strong>{{ $indikator->nama_indikator }}</strong></td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control" name="nilai[indikator][{{ $indikator->id }}]" value="{{ $nilai }}">
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan Nilai</button>
            <a href="{{ route('dosen.index') }}" class="btn btn-secondary mt-3">Kembali</a>
        </form>
    </div>
@endsection
