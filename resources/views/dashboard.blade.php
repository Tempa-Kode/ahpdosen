@extends("app")

@section("judul", "Dashboard")

@section("konten")
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dashboard - Dosen Terbaik</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Bar Chart -->
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Ranking Dosen Terbaik (Bar Chart)</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="barChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Pie Chart -->
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Distribusi Dosen per Program Studi</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="pieChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Data Dosen -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Tabel Ranking Dosen Terbaik</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Ranking</th>
                                                        <th>Nama Dosen</th>
                                                        <th>Program Studi</th>
                                                        <th>Skor AHP</th>
                                                        <th>Badge</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($dosenTerbaik as $index => $dosen)
                                                        <tr>
                                                            <td>
                                                                @if ($index == 0)
                                                                    <span class="badge bg-warning">🥇
                                                                        #{{ $index + 1 }}</span>
                                                                @elseif($index == 1)
                                                                    <span class="badge bg-secondary">🥈
                                                                        #{{ $index + 1 }}</span>
                                                                @elseif($index == 2)
                                                                    <span class="badge bg-warning text-dark">🥉
                                                                        #{{ $index + 1 }}</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-primary">#{{ $index + 1 }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $dosen["nama"] }}</td>
                                                            <td>{{ $dosen["prodi"] }}</td>
                                                            <td>
                                                                <strong>{{ $dosen["skor"] }}</strong>
                                                                <div class="progress mt-1" style="height: 6px;">
                                                                    <div class="progress-bar" role="progressbar"
                                                                        style="width: {{ ($dosen["skor"] / 100) * 100 }}%">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @if ($dosen["skor"] >= 90)
                                                                    <span class="badge bg-success">Excellent</span>
                                                                @elseif($dosen["skor"] >= 85)
                                                                    <span class="badge bg-info">Very Good</span>
                                                                @elseif($dosen["skor"] >= 80)
                                                                    <span class="badge bg-warning">Good</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Fair</span>
                                                                @endif
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data untuk chart
        const dosenData = @json($dosenTerbaik);

        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: dosenData.map(dosen => dosen.nama.split(' ')[1] + ' ' + dosen.nama.split(' ')[
                2]), // Ambil nama belakang
                datasets: [{
                    label: 'Skor AHP',
                    data: dosenData.map(dosen => dosen.skor),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Ranking Dosen Berdasarkan Skor AHP'
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });

        // Pie Chart - Distribusi per Program Studi
        const prodiCount = {};
        dosenData.forEach(dosen => {
            prodiCount[dosen.prodi] = (prodiCount[dosen.prodi] || 0) + 1;
        });

        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(prodiCount),
                datasets: [{
                    data: Object.values(prodiCount),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribusi Dosen Terbaik per Program Studi'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection
