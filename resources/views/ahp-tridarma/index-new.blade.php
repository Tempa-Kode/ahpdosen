@extends("app")

@section("judul", "AHP Tridarma - Ranking Dosen")

@section("konten")
    <div class="container-fluid">

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Filter & Kontrol</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-select" id="filter-prodi">
                                    <option value="">Semua Program Studi</option>
                                    <option value="Teknik Informatika">Teknik Informatika</option>
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                    <option value="Sains Data">Sains Data</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-info w-100" onclick="lihatLangkahPerhitungan()">
                                    <i class="fas fa-calculator me-1"></i>
                                    Lihat Langkah Perhitungan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Status Konsistensi</h5>
                        <div id="status-konsistensi">
                            <span class="badge bg-secondary">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            Ranking Dosen Berdasarkan AHP Tridarma
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="ranking-table">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="text-center">Rank</th>
                                        <th>NIDN</th>
                                        <th>Nama Dosen</th>
                                        <th>Prodi</th>
                                        <th class="text-center" title="Pendidikan dan Pembelajaran">K001</th>
                                        <th class="text-center" title="Penelitian">K002</th>
                                        <th class="text-center" title="PKM">K003</th>
                                        <th class="text-center" title="PKM">K004</th>
                                        <th class="text-center" title="PKM">Skor</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="ranking-tbody">
                                    <!-- Konten diisi oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Top 10 Dosen
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topDosenChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Distribusi per Program Studi
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="prodiChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-overlay"
        class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-none align-items-center justify-content-center"
        style="z-index: 9999;">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4>Memuat data AHP Tridarma...</h4>
        </div>
    </div>
@endsection

@section("js")
    <style>
        .rank-badge {
            font-size: 1.1em;
            padding: 8px 12px;
        }

        .rank-1 {
            background-color: #FFD700 !important;
            color: #000;
        }

        .rank-2 {
            background-color: #C0C0C0 !important;
            color: #000;
        }

        .rank-3 {
            background-color: #CD7F32 !important;
            color: #fff;
        }

        .skor-badge {
            font-size: 1em;
            padding: 6px 12px;
            min-width: 80px;
        }

        .indikator-score {
            font-size: 0.9em;
            padding: 4px 8px;
        }

        .table td {
            vertical-align: middle;
        }

        .card-stats {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
        }

        .stats-icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        .prioritas-global-table {
            font-size: 0.9em;
        }

        .prioritas-global-table th {
            background-color: #343a40 !important;
            color: white;
            text-align: center;
            vertical-align: middle;
        }

        .prioritas-global-table .table-warning {
            background-color: #fff3cd !important;
        }

        .formula-card {
            border-left: 4px solid #28a745;
        }

        .priority-value {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #0066cc;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let ahpData = {};
        let filteredData = [];

        // Fungsi untuk mengambil data dan menginisialisasi halaman
        async function initializePage() {
            showLoading(true);
            try {
                const response = await fetch('{{ url("/api/ahp-tridarma") }}');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log('API Response:', data);

                if (data.status === 'success') {
                    ahpData = data.data;
                    filteredData = Array.isArray(data.data.hasil_akhir) ? data.data.hasil_akhir : [];

                    // Perbarui semua komponen UI
                    updateUI(data.data);
                } else {
                    throw new Error(data.message || 'Data tidak valid');
                }

            } catch (error) {
                console.error('AHP fetch error:', error);
                alert('Gagal memuat data AHP. Silakan refresh halaman.');
                displayErrorInTables();
            } finally {
                // Beri sedikit jeda agar loading terlihat mulus
                setTimeout(() => showLoading(false), 200);
            }
        }

        // Fungsi terpusat untuk memperbarui semua bagian UI
        function updateUI(data) {
            // Konsistensi
            if (data.konsistensi) {
                updateKonsistensi(data.konsistensi);
            } else {
                document.getElementById('status-konsistensi').innerHTML =
                    '<span class="badge bg-secondary">Tidak tersedia</span>';
            }

            // Tabel Ranking
            if (Array.isArray(data.hasil_akhir) && data.hasil_akhir.length > 0) {
                updateRankingTable(data.hasil_akhir);
                createCharts(data.hasil_akhir); // Buat chart jika data ranking ada
            } else {
                document.getElementById('ranking-tbody').innerHTML =
                    '<tr><td colspan="10" class="text-center text-danger">Data ranking tidak tersedia</td></tr>';
            }
        }

        // Fungsi untuk menampilkan error di tabel jika fetch gagal
        function displayErrorInTables() {
            document.getElementById('ranking-tbody').innerHTML =
                '<tr><td colspan="10" class="text-center text-danger">Gagal memuat data AHP</td></tr>';
        }

        function updateKonsistensi(konsistensiData) {
            const badgeClass = konsistensiData.konsisten === 'Ya' ? 'bg-success' : 'bg-danger';
            const crDisplay = konsistensiData.CR || 0;
            document.getElementById('status-konsistensi').innerHTML =
                `<span class="badge ${badgeClass}">${konsistensiData.konsisten === 'Ya' ? 'Konsisten' : 'Tidak Konsisten'}</span><small class="text-muted d-block">CR: <strong>${crDisplay}</strong></small>`;
        }

        function updateRankingTable(rankingData) {
            const tbody = document.getElementById('ranking-tbody');
            rankingData.sort((a, b) => a.ranking - b.ranking);
            tbody.innerHTML = rankingData.map(item => {
                const rankClass = item.ranking <= 3 ? `rank-${item.ranking}` : 'bg-info';

                // Pastikan detail_kriteria ada dan gunakan K001, K002, K003, K004
                const detailKriteria = item.detail_kriteria || {};

                return `
                    <tr>
                        <td><span class="badge rank-badge ${rankClass}">${item.ranking}</span></td>
                        <td>${item.dosen.nidn || 'N/A'}</td>
                        <td><strong>${item.dosen.nama || item.dosen.nama_dosen || 'N/A'}</strong></td>
                        <td><span class="badge bg-info">${item.dosen.prodi || 'N/A'}</span></td>
                        <td><span class="badge bg-secondary indikator-score">${detailKriteria.K001?.nilai || '0.000'}</span></td>
                        <td><span class="badge bg-secondary indikator-score">${detailKriteria.K002?.nilai || '0.000'}</span></td>
                        <td><span class="badge bg-secondary indikator-score">${detailKriteria.K003?.nilai || '0.000'}</span></td>
                        <td><span class="badge bg-secondary indikator-score">${detailKriteria.K004?.nilai || '0.000'}</span></td>
                        <td><span class="badge bg-success indikator-score">${item.prioritas_global || '0.000'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="lihatDetailDosen(${item.dosen.id})">
                                <i class="fas fa-eye me-1"></i>Detail
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function createCharts(data) {
            // Chart Top 10 Dosen
            const top10 = data.slice(0, 10);
            const ctx1 = document.getElementById('topDosenChart').getContext('2d');

            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: top10.map(item => item.dosen.nama || item.dosen.nama_dosen),
                    datasets: [{
                        label: 'Skor AHP',
                        data: top10.map(item => parseFloat(item.prioritas_global)),
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Chart Distribusi Prodi
            const prodiCount = {};
            data.forEach(item => {
                const prodi = item.dosen.prodi || 'Tidak Diketahui';
                prodiCount[prodi] = (prodiCount[prodi] || 0) + 1;
            });

            const ctx2 = document.getElementById('prodiChart').getContext('2d');
            new Chart(ctx2, {
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
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function showLoading(show) {
            const overlay = document.getElementById('loading-overlay');
            if (show) {
                overlay.classList.remove('d-none');
                overlay.classList.add('d-flex');
            } else {
                overlay.classList.add('d-none');
                overlay.classList.remove('d-flex');
            }
        }

        function lihatDetailDosen(dosenId) {
            window.open(`/dashboard/ahp-tridarma/detail-dosen?dosen_id=${dosenId}`, '_blank');
        }

        function lihatLangkahPerhitungan() {
            window.open('/dashboard/ahp-tridarma/langkah-perhitungan', '_blank');
        }

        // Filter prodi
        document.getElementById('filter-prodi').addEventListener('change', function() {
            const selectedProdi = this.value;
            if (selectedProdi) {
                filteredData = ahpData.hasil_akhir.filter(item =>
                    (item.dosen.prodi || '').toLowerCase().includes(selectedProdi.toLowerCase())
                );
            } else {
                filteredData = ahpData.hasil_akhir;
            }
            updateRankingTable(filteredData);
        });

        // Inisialisasi halaman saat DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM ready - initializing AHP Tridarma page');
            initializePage();
        });
    </script>
@endsection
