@extends("app")

@section("judul", "Dashboard AHP - Ranking Dosen")

@section("konten")
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Dashboard AHP - Ranking Dosen</h6>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="mb-1">{{ count($rankings) }}</h4>
                                    <p class="text-muted mb-0">Total Dosen</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="mb-1">{{ number_format($rankings[0]["total_score"] ?? 0, 3) }}</h4>
                                    <p class="text-muted mb-0">Skor Tertinggi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="mb-1">{{ number_format(collect($rankings)->avg("total_score"), 3) }}</h4>
                                    <p class="text-muted mb-0">Rata-rata Skor</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="mb-1">{{ count($kriterias) }}</h4>
                                    <p class="text-muted mb-0">Kriteria</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Ranking Dosen Berdasarkan AHP</h6>
                        <a href="{{ route("ahp.comparison") }}" class="btn btn-primary btn-sm">
                            <i data-feather="bar-chart"></i> Perbandingan
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Dosen</th>
                                    <th>NIDN</th>
                                    <th>Program Studi</th>
                                    @foreach ($kriterias as $kriteria)
                                        <th class="text-center">{{ $kriteria->nama_kriteria }}</th>
                                    @endforeach
                                    <th class="text-center">Total Score</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rankings as $ranking)
                                    <tr>
                                        <td>
                                            <span
                                                class="badge
                                        @if ($ranking["rank"] == 1) bg-success
                                        @elseif($ranking["rank"] == 2) bg-warning
                                        @elseif($ranking["rank"] == 3) bg-info
                                        @else bg-secondary @endif">
                                                #{{ $ranking["rank"] }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $ranking["dosen"]->nama_dosen }}</strong>
                                        </td>
                                        <td>{{ $ranking["dosen"]->nidn }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $ranking["dosen"]->prodi }}</span>
                                        </td>
                                        @foreach ($kriterias as $kriteria)
                                            <td class="text-center">
                                                <div>
                                                    <strong>{{ number_format($ranking["kriteria_scores"][$kriteria->nama_kriteria] ?? 0, 3) }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        ({{ number_format(($ranking["kriteria_scores"][$kriteria->nama_kriteria] ?? 0) * $kriteria->bobot, 3) }})
                                                    </small>
                                                </div>
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            <span
                                                class="badge
                                        @if ($ranking["total_score"] >= 0.8) bg-success
                                        @elseif($ranking["total_score"] >= 0.6) bg-warning
                                        @elseif($ranking["total_score"] >= 0.4) bg-info
                                        @else bg-secondary @endif">
                                                {{ number_format($ranking["total_score"], 3) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route("ahp.detail", $ranking["dosen"]->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i data-feather="eye"></i> Detail
                                            </a>
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
