@extends("app")

@section("judul", "Perbandingan Dosen Terbaik - AHP")

@section("konten")
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Perbandingan Dosen Terbaik</h6>
                        <a href="{{ route("ahp.dashboard") }}" class="btn btn-secondary btn-sm">
                            <i data-feather="arrow-left"></i> Kembali
                        </a>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Ranking Top 5 Dosen</h6>
                                    <small class="text-muted">Berdasarkan perhitungan AHP</small>
                                </div>
                                <div class="card-body">
                                    <canvas id="rankingChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Perbandingan Kriteria</h6>
                                    <small class="text-muted">Skor per kriteria untuk top 5</small>
                                </div>
                                <div class="card-body">
                                    <canvas id="criteriaChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Comparison Table -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">
                                <i data-feather="bar-chart" class="me-2"></i>
                                Tabel Perbandingan Detail
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="comparisonTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Rank</th>
                                            <th>Dosen</th>
                                            <th>Program Studi</th>
                                            @foreach ($kriterias as $kriteria)
                                                <th class="text-center">
                                                    {{ $kriteria->nama_kriteria }}
                                                    <br>
                                                    <small class="text-muted">({{ $kriteria->bobot }})</small>
                                                </th>
                                            @endforeach
                                            <th class="text-center">Total Score</th>
                                            <th class="text-center">Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topDosens as $index => $ranking)
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
                                                    <div>
                                                        <strong>{{ $ranking["dosen"]->nama_dosen }}</strong>
                                                        <br>
                                                        <small class="text-muted">NIDN:
                                                            {{ $ranking["dosen"]->nidn }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $ranking["dosen"]->prodi }}</span>
                                                </td>
                                                @foreach ($kriterias as $kriteria)
                                                    <td class="text-center">
                                                        <div>
                                                            <strong>{{ number_format($ranking["kriteria_scores"][$kriteria->nama_kriteria] ?? 0, 3) }}</strong>
                                                            <div class="progress mt-1" style="height: 6px;">
                                                                <div class="progress-bar bg-success"
                                                                    style="width: {{ ($ranking["kriteria_scores"][$kriteria->nama_kriteria] ?? 0) * 100 }}%">
                                                                </div>
                                                            </div>
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
                                                    @if (isset($topDosens[0]["total_score"]) && $topDosens[0]["total_score"] > 0)
                                                        <strong>{{ number_format(($ranking["total_score"] / $topDosens[0]["total_score"]) * 100, 1) }}%</strong>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Analysis Summary -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Analisis Hasil</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if (count($topDosens) > 0)
                                            <div class="col-md-6">
                                                <div class="alert alert-success">
                                                    <h5 class="alert-heading">
                                                        <i data-feather="award" class="me-2"></i>
                                                        Dosen Terbaik
                                                    </h5>
                                                    <p class="mb-0">
                                                        <strong>{{ $topDosens[0]["dosen"]->nama_dosen }}</strong>
                                                        dari {{ $topDosens[0]["dosen"]->prodi }}
                                                        <br>Dengan skor AHP:
                                                        <strong>{{ number_format($topDosens[0]["total_score"], 3) }}</strong>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-6">
                                            <div class="alert alert-info">
                                                <h5 class="alert-heading">
                                                    <i data-feather="pie-chart" class="me-2"></i>
                                                    Kriteria Dominan
                                                </h5>
                                                @php
                                                    $maxKriteria = $kriterias->sortByDesc("bobot")->first();
                                                @endphp
                                                <p class="mb-0">
                                                    <strong>{{ $maxKriteria->nama_kriteria }}</strong>
                                                    <br>Bobot: <strong>{{ $maxKriteria->bobot }}</strong>
                                                    ({{ number_format($maxKriteria->bobot * 100, 0) }}%)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ranking Chart
            const rankingCtx = document.getElementById('rankingChart').getContext('2d');
            const rankingChart = new Chart(rankingCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($topDosens as $ranking)
                            '{{ substr($ranking["dosen"]->nama_dosen, 0, 20) }}{{ strlen($ranking["dosen"]->nama_dosen) > 20 ? "..." : "" }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Skor AHP',
                        data: [
                            @foreach ($topDosens as $ranking)
                                {{ $ranking["total_score"] }},
                            @endforeach
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 1
                        }
                    }
                }
            });

            // Criteria Comparison Chart
            const criteriaCtx = document.getElementById('criteriaChart').getContext('2d');
            const criteriaLabels = [
                @foreach ($kriterias as $kriteria)
                    '{{ $kriteria->nama_kriteria }}',
                @endforeach
            ];

            const datasets = [];
            const colors = [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ];

            @foreach ($topDosens as $index => $ranking)
                datasets.push({
                    label: '{{ substr($ranking["dosen"]->nama_dosen, 0, 15) }}{{ strlen($ranking["dosen"]->nama_dosen) > 15 ? "..." : "" }}',
                    data: [
                        @foreach ($kriterias as $kriteria)
                            {{ $ranking["kriteria_scores"][$kriteria->nama_kriteria] ?? 0 }},
                        @endforeach
                    ],
                    backgroundColor: colors[{{ $index }}],
                    borderColor: colors[{{ $index }}].replace('0.8', '1'),
                    borderWidth: 1
                });
            @endforeach

            const criteriaChart = new Chart(criteriaCtx, {
                type: 'radar',
                data: {
                    labels: criteriaLabels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 1
                        }
                    }
                }
            });
        });
    </script>
@endsection
