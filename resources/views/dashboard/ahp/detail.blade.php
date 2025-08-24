@extends("app")

@section("judul", "Detail Perhitungan AHP - " . $dosen->nama_dosen)

@section("konten")
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Detail Perhitungan AHP</h6>
                        <a href="{{ route("ahp.dashboard") }}" class="btn btn-secondary btn-sm">
                            <i data-feather="arrow-left"></i> Kembali
                        </a>
                    </div>

                    <!-- Dosen Info -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                        style="width: 80px; height: 80px; font-size: 24px; font-weight: bold;">
                                        {{ substr($dosen->nama_dosen, 0, 2) }}
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <h4 class="mb-1">{{ $dosen->nama_dosen }}</h4>
                                    <p class="text-muted mb-1">NIDN: {{ $dosen->nidn }}</p>
                                    <p class="text-muted">Program Studi: {{ $dosen->prodi }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calculation Details -->
                    @foreach ($detailCalculation as $kriteriaDetail)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    {{ $kriteriaDetail["kriteria"]->nama_kriteria }}
                                    <span class="badge bg-primary float-end">
                                        {{ number_format($kriteriaDetail["weighted_score"], 3) }}
                                    </span>
                                </h5>
                                <small class="text-muted">
                                    Bobot: {{ $kriteriaDetail["kriteria"]->bobot }} |
                                    Skor: {{ number_format($kriteriaDetail["score"], 3) }} |
                                    Weighted: {{ number_format($kriteriaDetail["weighted_score"], 3) }}
                                </small>
                            </div>
                            <div class="card-body">
                                @foreach ($kriteriaDetail["indikators"] as $indikatorDetail)
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="text-primary">
                                                {{ $indikatorDetail["indikator"]->nama_indikator }}
                                                @if ($indikatorDetail["indikator"]->bobot_indikator)
                                                    <small class="badge bg-light text-dark">Bobot:
                                                        {{ $indikatorDetail["indikator"]->bobot_indikator }}</small>
                                                @endif
                                            </h6>
                                            <span class="badge bg-success">
                                                {{ number_format($indikatorDetail["score"], 3) }}
                                            </span>
                                        </div>

                                        @if (count($indikatorDetail["sub_indikators"]) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Sub Indikator</th>
                                                            <th class="text-center">Skor</th>
                                                            <th>Detail Sub-Sub Indikator</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($indikatorDetail["sub_indikators"] as $subIndikatorDetail)
                                                            <tr>
                                                                <td>{{ $subIndikatorDetail["sub_indikator"]->nama_sub_indikator }}
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-info">
                                                                        {{ number_format($subIndikatorDetail["score"], 3) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if (count($subIndikatorDetail["sub_sub_indikators"]) > 0)
                                                                        <div class="row">
                                                                            @foreach ($subIndikatorDetail["sub_sub_indikators"] as $subSubDetail)
                                                                                <div class="col-md-6 mb-2">
                                                                                    <div class="border rounded p-2">
                                                                                        <div
                                                                                            class="d-flex justify-content-between align-items-center">
                                                                                            <div>
                                                                                                <small class="fw-semibold">
                                                                                                    {{ $subSubDetail["sub_sub_indikator"]->nama_sub_sub_indikator }}
                                                                                                </small>
                                                                                                <br>
                                                                                                <small class="text-muted">
                                                                                                    Raw:
                                                                                                    {{ $subSubDetail["raw_score"] }}
                                                                                                    |
                                                                                                    Norm:
                                                                                                    {{ number_format($subSubDetail["normalized_score"], 3) }}
                                                                                                </small>
                                                                                            </div>
                                                                                            <span
                                                                                                class="badge
                                                                    @if ($subSubDetail["normalized_score"] >= 0.8) bg-success
                                                                    @elseif($subSubDetail["normalized_score"] >= 0.6) bg-warning
                                                                    @elseif($subSubDetail["normalized_score"] >= 0.4) bg-info
                                                                    @else bg-secondary @endif">
                                                                                                {{ number_format($subSubDetail["normalized_score"], 3) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <small class="text-muted">Tidak ada data</small>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Total Score Summary -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Ringkasan Perhitungan AHP</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kriteria</th>
                                            <th class="text-center">Bobot Kriteria</th>
                                            <th class="text-center">Skor Kriteria</th>
                                            <th class="text-center">Skor Berbobot</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalWeightedScore = 0; @endphp
                                        @foreach ($detailCalculation as $kriteriaDetail)
                                            @php $totalWeightedScore += $kriteriaDetail['weighted_score']; @endphp
                                            <tr>
                                                <td><strong>{{ $kriteriaDetail["kriteria"]->nama_kriteria }}</strong></td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-primary">{{ $kriteriaDetail["kriteria"]->bobot }}</span>
                                                </td>
                                                <td class="text-center">{{ number_format($kriteriaDetail["score"], 3) }}
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ number_format($kriteriaDetail["weighted_score"], 3) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-success">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total Skor AHP:</strong></td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-success fs-6">{{ number_format($totalWeightedScore, 3) }}</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
