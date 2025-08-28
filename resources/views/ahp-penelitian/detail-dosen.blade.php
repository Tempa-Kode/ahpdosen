@extends("app")

@section("judul", "Detail Dosen AHP")

@section("konten")
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-user-graduate me-2"></i>
                            Detail Perhitungan AHP Dosen
                        </h1>
                        <p class="card-text mt-2">
                            Analisis mendalam perhitungan AHP untuk dosen ID: {{ $dosen_id }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dosen Info Card -->
        <div class="row mb-4" id="dosen-info-section">
            <!-- Will be populated via JavaScript -->
        </div>

        <!-- Detail Perhitungan -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Detail Perhitungan per Indikator
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Indikator</th>
                                        <th>Nama Lengkap</th>
                                        <th>Nilai Asli</th>
                                        <th>Skala (1-5)</th>
                                        <th>Bobot Prioritas</th>
                                        <th>Skor</th>
                                        <th>Kontribusi (%)</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-perhitungan-table">
                                    <!-- Will be populated via JavaScript -->
                                </tbody>
                                <tfoot class="table-success">
                                    <tr id="total-row">
                                        <!-- Will be populated via JavaScript -->
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Kontribusi per Indikator
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="kontribusiChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-radar me-2"></i>
                            Profil Kinerja
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="radarChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <a href="/dashboard/ahp-penelitian" class="btn btn-primary me-2">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali ke Ranking
                        </a>
                        <button class="btn btn-success me-2" onclick="downloadReport()">
                            <i class="fas fa-download me-1"></i>
                            Download Laporan
                        </button>
                        <button class="btn btn-info" onclick="shareResult()">
                            <i class="fas fa-share me-1"></i>
                            Bagikan Hasil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay"
        class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center"
        style="z-index: 9999; display: none !important;">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Memuat detail perhitungan...</p>
        </div>
    </div>
@endsection

@section("js")
    <style>
        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }

        .indikator-card {
            border-left: 4px solid #007bff;
        }

        .score-display {
            font-size: 2rem;
            font-weight: bold;
        }

        .contribution-bar {
            height: 20px;
            background: linear-gradient(to right, #28a745, #20c997);
            border-radius: 10px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @push("scripts")
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const dosenId = {{ $dosen_id }};
            let detailData = {};

            document.addEventListener('DOMContentLoaded', function() {
                loadDetailDosen();
            });

            async function loadDetailDosen() {
                showLoading(true);

                try {
                    const response = await fetch(`/api/ahp-penelitian/dosen/${dosenId}`);
                    const data = await response.json();

                    detailData = data;

                    populateDosenInfo(data);
                    populateDetailTable(data);
                    createCharts(data);

                } catch (error) {
                    console.error('Error loading detail dosen:', error);
                    alert('Gagal memuat detail dosen.');
                } finally {
                    showLoading(false);
                }
            }

            function populateDosenInfo(data) {
                // You can add dosen info here if available in the API response
                const infoSection = document.getElementById('dosen-info-section');
                infoSection.innerHTML = `
        <div class="col-md-8">
            <div class="card indikator-card">
                <div class="card-body">
                    <h5 class="card-title">Informasi Dosen</h5>
                    <p class="card-text">
                        <strong>ID Dosen:</strong> ${data.dosen.id}<br>
                        <small class="text-muted">Detail lengkap akan ditampilkan setelah integrasi dengan data dosen</small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Skor Total AHP</h6>
                    <div class="score-display">${data.skor_total_ahp}</div>
                </div>
            </div>
        </div>
    `;
            }

            function populateDetailTable(data) {
                const tbody = document.getElementById('detail-perhitungan-table');
                const totalSkor = data.skor_total_ahp;

                tbody.innerHTML = Object.entries(data.detail_perhitungan).map(([kode, detail]) => {
                    const kontribusi = ((detail.skor / totalSkor) * 100).toFixed(1);

                    return `
            <tr>
                <td><strong>${kode}</strong></td>
                <td>${detail.nama_indikator}</td>
                <td>${detail.total_nilai_indikator}</td>
                <td>
                    <span class="badge bg-info fs-6">${detail.skala_normalisasi}</span>
                </td>
                <td>${detail.bobot_prioritas}</td>
                <td>
                    <span class="badge bg-success">${detail.skor}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="contribution-bar me-2" style="width: ${kontribusi}%; max-width: 100px;"></div>
                        <small>${kontribusi}%</small>
                    </div>
                </td>
            </tr>
        `;
                }).join('');

                // Total row
                document.getElementById('total-row').innerHTML = `
        <td colspan="5"><strong>TOTAL SKOR AHP</strong></td>
        <td><strong>${totalSkor}</strong></td>
        <td><strong>100%</strong></td>
    `;
            }

            function createCharts(data) {
                const labels = Object.keys(data.detail_perhitungan);
                const scores = Object.values(data.detail_perhitungan).map(d => d.skor);
                const skalaValues = Object.values(data.detail_perhitungan).map(d => d.skala_normalisasi);

                // Pie Chart - Kontribusi
                const ctx1 = document.getElementById('kontribusiChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: scores,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 205, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(153, 102, 255, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.raw / total) * 100).toFixed(1);
                                        return `${context.label}: ${context.raw} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Radar Chart - Profil Kinerja
                const ctx2 = document.getElementById('radarChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Skala Kinerja (1-5)',
                            data: skalaValues,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 5,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            function downloadReport() {
                alert('Fitur download laporan akan segera tersedia');
            }

            function shareResult() {
                if (navigator.share) {
                    navigator.share({
                        title: 'Hasil AHP Dosen',
                        text: `Skor AHP: ${detailData.skor_total_ahp}`,
                        url: window.location.href
                    });
                } else {
                    // Fallback - copy to clipboard
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        alert('Link berhasil disalin ke clipboard');
                    });
                }
            }

            function showLoading(show) {
                const overlay = document.getElementById('loading-overlay');
                overlay.style.display = show ? 'flex' : 'none';
            }
        </script>
    @endsection
