@extends("app")

@section("judul", "Dashboard")

@section("konten")
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dashboard - Ranking Dosen Terbaik</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <!-- Bar Chart -->
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Ranking Dosen Terbaik (Bar Chart)</h6>
                                        <p class="text-muted mb-0 small">Skor berdasarkan Prioritas Global Choice</p>
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
                                                        <th>NIDN</th>
                                                        <th>Nama Dosen</th>
                                                        <th>Program Studi</th>
                                                        <th>Skor Choice</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($dosenTerbaik as $dosen)
                                                        <tr>
                                                            <td>
                                                                @if ($dosen["ranking"] == 1)
                                                                    <span class="badge bg-warning">🥇
                                                                        #{{ $dosen["ranking"] }}</span>
                                                                @elseif($dosen["ranking"] == 2)
                                                                    <span class="badge bg-secondary">🥈
                                                                        #{{ $dosen["ranking"] }}</span>
                                                                @elseif($dosen["ranking"] == 3)
                                                                    <span class="badge bg-warning text-dark">�
                                                                        #{{ $dosen["ranking"] }}</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-primary">#{{ $dosen["ranking"] }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <code class="small">{{ $dosen["nidn"] ?? "N/A" }}</code>
                                                            </td>
                                                            <td>
                                                                <strong>{{ $dosen["nama"] }}</strong>
                                                            </td>
                                                            <td>{{ $dosen["prodi"] }}</td>
                                                            <td>
                                                                <strong
                                                                    class="text-primary">{{ number_format($dosen["skor"], 5) }}</strong>
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
                labels: dosenData.map(dosen => {
                    // Ambil 2 kata dari depan atau nama lengkap jika pendek
                    const namaParts = dosen.nama.split(' ');
                    return namaParts.length > 2 ? namaParts.slice(0, 2).join(' ') : dosen.nama;
                }),
                datasets: [{
                    label: 'Skor Prioritas Global Choice',
                    data: dosenData.map(dosen => dosen.skor),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)',
                        'rgba(255, 123, 145, 0.8)',
                        'rgba(123, 255, 178, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)',
                        'rgba(255, 123, 145, 1)',
                        'rgba(123, 255, 178, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Ranking Dosen Berdasarkan Prioritas Global Choice (AHP Tridarma)'
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const dosen = dosenData[context.dataIndex];
                                return [
                                    'NIDN: ' + (dosen.nidn || 'N/A'),
                                    'Prodi: ' + dosen.prodi,
                                    'Persentase: ' + dosen.persentase.toFixed(2) + '%',
                                    'Kategori: ' + dosen.kategori
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(5);
                            }
                        },
                        title: {
                            display: true,
                            text: 'Skor Prioritas Global Choice'
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        },
                        title: {
                            display: true,
                            text: 'Nama Dosen'
                        }
                    }
                }
            }
        });

        // Pie Chart - Distribusi per Program Studi
        const prodiCount = {};
        dosenData.forEach(dosen => {
            if (dosen.prodi !== 'N/A') {
                prodiCount[dosen.prodi] = (prodiCount[dosen.prodi] || 0) + 1;
            }
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
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' dosen (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
