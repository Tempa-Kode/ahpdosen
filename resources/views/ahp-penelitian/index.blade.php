@extends("app")

@section("judul", "AHP Penelitian - Ranking Dosen")

@section("konten")
    <div class="container-fluid">

        <!-- Statistics Cards -->
        <div class="row mb-4" id="statistik-cards">
            <!-- Cards akan diisi via JavaScript -->
        </div>

        <!-- Controls & Filters -->
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
                                <button class="btn btn-info" onclick="lihatLangkahPerhitungan()">
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

        <!-- Ranking Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            Ranking Dosen Berdasarkan AHP
                        </h5>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                                <i class="fas fa-file-excel me-1"></i>
                                Export Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf me-1"></i>
                                Export PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="ranking-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Rank</th>
                                        <th>NIDN</th>
                                        <th>Nama Dosen</th>
                                        <th>Program Studi</th>
                                        <th>KPT01<br><small>Skala</small></th>
                                        <th>KPT02<br><small>Skala</small></th>
                                        <th>KPT03<br><small>Skala</small></th>
                                        <th>KPT04<br><small>Skala</small></th>
                                        <th>KPT05<br><small>Skala</small></th>
                                        <th>Bobot Prioritas</th>
                                        <th>Skor AHP</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="ranking-tbody">
                                    <!-- Data akan diisi via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
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

    <!-- Modal Detail Dosen -->
    <div class="modal fade" id="detailDosenModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>
                        Detail Perhitungan AHP Dosen
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-detail-content">
                    <!-- Content akan diisi via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay"
        class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center"
        style="z-index: 9999; display: none;">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Memuat data AHP...</p>
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
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @push("scripts")
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let ahpData = {};
            let filteredData = [];

            // Load data saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded, starting AHP data load...');
                loadAhpData();
            });

            async function loadAhpData() {
                console.log('Loading AHP data...');
                showLoading(true);

                try {
                    const response = await fetch('/api/ahp-penelitian');

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();
                    console.log('AHP data loaded successfully:', data);

                    ahpData = data;
                    filteredData = data.hasil_ranking;

                    updateStatistiks(data);
                    updateKonsistensi(data.langkah_perhitungan['4_uji_konsistensi']);
                    updateRankingTable(data.hasil_ranking);
                    createCharts(data.hasil_ranking);

                    // Pastikan loading hilang setelah semua proses selesai dengan delay kecil
                    setTimeout(() => {
                        console.log('All data processing complete, hiding loading...');
                        showLoading(false);
                    }, 100);

                } catch (error) {
                    console.error('Error loading AHP data:', error);
                    alert('Gagal memuat data AHP. Silakan refresh halaman.');
                    showLoading(false);
                }
            }

            function updateStatistiks(data) {
                const total = data.hasil_ranking.length;
                const avgScore = data.hasil_ranking.reduce((sum, item) => sum + item.skor_total_ahp, 0) / total;
                const maxScore = Math.max(...data.hasil_ranking.map(item => item.skor_total_ahp));
                const konsisten = data.langkah_perhitungan['4_uji_konsistensi'].status_konsistensi;

                const statsHtml = `
        <div class="col-md-3">
            <div class="card card-stats text-center">
                <div class="card-body">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="mt-2">${total}</h3>
                    <p class="mb-0">Total Dosen</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats text-center">
                <div class="card-body">
                    <div class="stats-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="mt-2">${avgScore.toFixed(3)}</h3>
                    <p class="mb-0">Rata-rata Skor</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats text-center">
                <div class="card-body">
                    <div class="stats-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="mt-2">${maxScore.toFixed(3)}</h3>
                    <p class="mb-0">Skor Tertinggi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats text-center">
                <div class="card-body">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="mt-2">${konsisten}</h3>
                    <p class="mb-0">Status</p>
                </div>
            </div>
        </div>
    `;

                document.getElementById('statistik-cards').innerHTML = statsHtml;
            }

            function updateKonsistensi(konsistensiData) {
                const badgeClass = konsistensiData.status_konsistensi === 'Konsisten' ? 'bg-success' : 'bg-danger';

                // Tampilkan CR langsung tanpa format tambahan
                let crDisplay = konsistensiData.CR || konsistensiData.CR_raw || 0;

                const html = `
        <span class="badge ${badgeClass}">
            ${konsistensiData.status_konsistensi}
        </span>
        <small class="text-muted d-block">CR: <strong>${crDisplay}</strong></small>
    `;
                document.getElementById('status-konsistensi').innerHTML = html;
            }

            function updateRankingTable(rankingData) {
                const tbody = document.getElementById('ranking-tbody');

                tbody.innerHTML = rankingData.map(item => {
                    const rankClass = item.ranking <= 3 ? `rank-${item.ranking}` : 'bg-light';

                    // Format bobot prioritas untuk semua indikator
                    const bobotHtml = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'].map(kode => {
                        const detail = item.detail_skor[kode];
                        return detail ? detail.bobot_prioritas : '0.000';
                    }).join('<br>');

                    return `
            <tr>
                <td>
                    <span class="badge rank-badge ${rankClass}">
                        ${item.ranking}
                    </span>
                </td>
                <td>${item.dosen.nidn}</td>
                <td>
                    <strong>${item.dosen.nama}</strong>
                </td>
                <td>
                    <span class="badge bg-info">${item.dosen.prodi}</span>
                </td>
                <td>
                    <span class="badge bg-secondary indikator-score">
                        ${item.detail_skor.KPT01?.skala_normalisasi || 0}
                    </span>
                </td>
                <td>
                    <span class="badge bg-secondary indikator-score">
                        ${item.detail_skor.KPT02?.skala_normalisasi || 0}
                    </span>
                </td>
                <td>
                    <span class="badge bg-secondary indikator-score">
                        ${item.detail_skor.KPT03?.skala_normalisasi || 0}
                    </span>
                </td>
                <td>
                    <span class="badge bg-secondary indikator-score">
                        ${item.detail_skor.KPT04?.skala_normalisasi || 0}
                    </span>
                </td>
                <td>
                    <span class="badge bg-secondary indikator-score">
                        ${item.detail_skor.KPT05?.skala_normalisasi || 0}
                    </span>
                </td>
                <td class="text-center" style="font-size: 0.8em; font-family: monospace;">
                    <div style="line-height: 1.3;">
                        <small>KPT01: ${item.detail_skor.KPT01?.bobot_prioritas || '0.000'}</small><br>
                        <small>KPT02: ${item.detail_skor.KPT02?.bobot_prioritas || '0.000'}</small><br>
                        <small>KPT03: ${item.detail_skor.KPT03?.bobot_prioritas || '0.000'}</small><br>
                        <small>KPT04: ${item.detail_skor.KPT04?.bobot_prioritas || '0.000'}</small><br>
                        <small>KPT05: ${item.detail_skor.KPT05?.bobot_prioritas || '0.000'}</small>
                    </div>
                </td>
                <td>
                    <span class="badge bg-success skor-badge">
                        ${item.skor_total_ahp}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="lihatDetailDosen(${item.dosen.id})">
                        <i class="fas fa-eye"></i>
                        Detail
                    </button>
                </td>
            </tr>
        `;
                }).join('');
            }

            async function lihatDetailDosen(dosenId) {
                showLoading(true);

                try {
                    const response = await fetch(`/api/ahp-penelitian/dosen/${dosenId}`);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();

                    const modalContent = `
            <div class="row">
                <div class="col-12 mb-3">
                    <h6>Informasi Dosen</h6>
                    <p><strong>ID:</strong> ${data.dosen.id}</p>
                </div>

                <!-- Tab Navigation -->
                <div class="col-12">
                    <ul class="nav nav-tabs" id="dosenDetailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="perhitungan-tab" data-bs-toggle="tab" data-bs-target="#perhitungan-pane" type="button" role="tab">
                                <i class="fas fa-calculator"></i> Detail Perhitungan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="matriks-tab" data-bs-toggle="tab" data-bs-target="#matriks-pane" type="button" role="tab">
                                <i class="fas fa-table"></i> Matriks Perbandingan Individual
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="dosenDetailTabsContent">
                        <!-- Tab 1: Detail Perhitungan -->
                        <div class="tab-pane fade show active" id="perhitungan-pane" role="tabpanel">
                            <h6>Detail Perhitungan per Indikator</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Indikator</th>
                                            <th>Nilai Asli</th>
                                            <th>Skala (1-5)</th>
                                            <th>Bobot Prioritas</th>
                                            <th>Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${Object.entries(data.detail_perhitungan).map(([kode, detail]) => `
                                                    <tr>
                                                        <td><strong>${kode}</strong><br><small>${detail.nama_indikator}</small></td>
                                                        <td>${detail.total_nilai_indikator}</td>
                                                        <td><span class="badge bg-info">${detail.skala_normalisasi}</span></td>
                                                        <td>${detail.bobot_prioritas}</td>
                                                        <td><span class="badge bg-success">${detail.skor}</span></td>
                                                    </tr>
                                                `).join('')}
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-dark">
                                            <td colspan="4"><strong>Total Skor AHP</strong></td>
                                            <td><strong>${data.skor_total_ahp}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Matriks Perbandingan Individual -->
                        <div class="tab-pane fade" id="matriks-pane" role="tabpanel">
                            ${generateMatriksPerbandinganHtml(data.matriks_perbandingan_individual)}
                        </div>
                    </div>
                </div>
            </div>
        `;

                    document.getElementById('modal-detail-content').innerHTML = modalContent;
                    new bootstrap.Modal(document.getElementById('detailDosenModal')).show();

                } catch (error) {
                    console.error('Error loading detail dosen:', error);
                    alert('Gagal memuat detail dosen.');
                } finally {
                    showLoading(false);
                }
            }

            function createCharts(rankingData) {
                console.log('Creating charts...');

                try {
                    // Top 10 Dosen Chart
                    const top10 = rankingData.slice(0, 10);
                    const ctx1 = document.getElementById('topDosenChart').getContext('2d');

                    new Chart(ctx1, {
                        type: 'bar',
                        data: {
                            labels: top10.map(item => item.dosen.nama.split(' ').slice(0, 2).join(' ')),
                            datasets: [{
                                label: 'Skor AHP',
                                data: top10.map(item => item.skor_total_ahp),
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

                    // Distribusi Program Studi Chart
                    const prodiStats = {};
                    rankingData.forEach(item => {
                        prodiStats[item.dosen.prodi] = (prodiStats[item.dosen.prodi] || 0) + 1;
                    });

                    const ctx2 = document.getElementById('prodiChart').getContext('2d');

                    new Chart(ctx2, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(prodiStats),
                            datasets: [{
                                data: Object.values(prodiStats),
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.8)',
                                    'rgba(54, 162, 235, 0.8)',
                                    'rgba(255, 205, 86, 0.8)',
                                    'rgba(75, 192, 192, 0.8)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });

                    console.log('Charts created successfully');

                } catch (error) {
                    console.error('Error creating charts:', error);
                }
            }

            function lihatLangkahPerhitungan() {
                window.open('/dashboard/ahp-penelitian/langkah-perhitungan', '_blank');
            }

            function exportToExcel() {
                // Implementasi export Excel
                alert('Fitur export Excel akan segera tersedia');
            }

            function exportToPDF() {
                // Implementasi export PDF
                alert('Fitur export PDF akan segera tersedia');
            }

            function generateMatriksPerbandinganHtml(matriksData) {
                const indikatorKode = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'];

                return `
                    <div class="row">
                        <!-- Nilai Asli Indikator -->
                        <div class="col-12 mb-4">
                            <h6><i class="fas fa-list"></i> Nilai Asli Indikator Dosen</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-info">
                                        <tr>
                                            <th>Indikator</th>
                                            ${indikatorKode.map(kode => `<th>${kode}</th>`).join('')}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Nilai</strong></td>
                                            ${indikatorKode.map(kode => `<td><strong>${matriksData.nilai_indikator_asli[kode]}</strong></td>`).join('')}
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Matriks Perbandingan -->
                        <div class="col-12 mb-4">
                            <h6><i class="fas fa-table"></i> Matriks Perbandingan Berpasangan</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Indikator</th>
                                            ${indikatorKode.map(kode => `<th>${kode}</th>`).join('')}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${indikatorKode.map(kode1 => `
                                                    <tr>
                                                        <td><strong>${kode1}</strong></td>
                                                        ${indikatorKode.map(kode2 => `<td>${matriksData.matriks_perbandingan[kode1][kode2]}</td>`).join('')}
                                                    </tr>
                                                `).join('')}
                                        <tr class="table-warning">
                                            <td><strong>Jumlah</strong></td>
                                            ${indikatorKode.map(kode => `<td><strong>${matriksData.jumlah_kolom[kode]}</strong></td>`).join('')}
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Matriks Normalisasi -->
                        <div class="col-12 mb-4">
                            <h6><i class="fas fa-balance-scale"></i> Matriks Normalisasi & Bobot Prioritas</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Kriteria</th>
                                            ${indikatorKode.map(kode => `<th>${kode}</th>`).join('')}
                                            <th>Jumlah</th>
                                            <th>Bobot Prioritas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${indikatorKode.map(kode1 => `
                                                    <tr>
                                                        <td><strong>${kode1}</strong></td>
                                                        ${indikatorKode.map(kode2 => `<td>${matriksData.matriks_normalisasi[kode1][kode2]}</td>`).join('')}
                                                        <td><strong>${matriksData.jumlah_baris[kode1]}</strong></td>
                                                        <td><span class="badge bg-success">${matriksData.bobot_prioritas_individual[kode1]}</span></td>
                                                    </tr>
                                                `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Lambda Maks dan Konsistensi -->
                        <div class="col-md-6">
                            <h6><i class="fas fa-calculator"></i> Perhitungan λ Maks</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-info">
                                        <tr>
                                            <th>Indikator</th>
                                            <th>Perhitungan</th>
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${indikatorKode.map(kode => `
                                                    <tr>
                                                        <td><strong>${kode}</strong></td>
                                                        <td>${matriksData.jumlah_kolom[kode]} × ${matriksData.bobot_prioritas_individual[kode]}</td>
                                                        <td><span class="badge bg-info">${matriksData.lambda_maks.detail[kode]}</span></td>
                                                    </tr>
                                                `).join('')}
                                        <tr class="table-warning">
                                            <td colspan="2"><strong>Total λ Maks</strong></td>
                                            <td><strong>${matriksData.lambda_maks.total}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Uji Konsistensi -->
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle"></i> Uji Konsistensi</h6>
                            <div class="alert ${matriksData.konsistensi.status === 'Konsisten' ? 'alert-success' : 'alert-danger'}">
                                <p><strong>λ Maks:</strong> ${matriksData.lambda_maks.total}</p>
                                <p><strong>CI:</strong> ${matriksData.konsistensi.CI}</p>
                                <small class="text-muted">CI = (λ maks - n) / (n - 1) = (${matriksData.lambda_maks.total} - 5) / 4</small>

                                <p class="mt-2"><strong>RI:</strong> ${matriksData.konsistensi.RI}</p>
                                <p><strong>CR:</strong> ${matriksData.konsistensi.CR}</p>
                                <small class="text-muted">CR = CI / RI = ${matriksData.konsistensi.CI} / ${matriksData.konsistensi.RI}</small>

                                <hr>
                                <h6><span class="badge ${matriksData.konsistensi.status === 'Konsisten' ? 'bg-success' : 'bg-danger'}">${matriksData.konsistensi.status}</span></h6>
                                <small>Konsisten jika CR ≤ 0.1</small>
                            </div>
                        </div>
                    </div>
                `;
            }

            function showLoading(show) {
                console.log('showLoading called with:', show);
                const overlay = document.getElementById('loading-overlay');
                if (overlay) {
                    if (show) {
                        overlay.style.display = 'flex';
                        overlay.classList.add('d-flex');
                        overlay.classList.remove('d-none');
                        console.log('Loading overlay shown');
                    } else {
                        overlay.style.display = 'none';
                        overlay.classList.add('d-none');
                        overlay.classList.remove('d-flex');
                        console.log('Loading overlay hidden');
                    }
                } else {
                    console.error('Loading overlay element not found!');
                }
            }

            // Filter by Program Studi
            document.getElementById('filter-prodi').addEventListener('change', function() {
                const selectedProdi = this.value;

                if (selectedProdi === '') {
                    filteredData = ahpData.hasil_ranking;
                } else {
                    filteredData = ahpData.hasil_ranking.filter(item => item.dosen.prodi === selectedProdi);
                }

                updateRankingTable(filteredData);
            });

            async function showDetailDosen(dosenId) {
                console.log('Loading detail for dosen ID:', dosenId);

                try {
                    const response = await fetch(`/api/ahp-penelitian/dosen/${dosenId}`);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();
                    console.log('Detail dosen data:', data);

                    // Update konten tab Detail Perhitungan
                    document.getElementById('detailPerhitunganTable').innerHTML = generateDetailTable(data);

                    // Update konten tab Matriks Perbandingan Individual
                    document.getElementById('matriksPerbandinganIndividual').innerHTML =
                        generateMatriksPerbandinganHtml(data.matriks_perbandingan_individual);

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                    modal.show();

                } catch (error) {
                    console.error('Error loading detail dosen:', error);
                    alert('Gagal memuat detail dosen. Silakan coba lagi.');
                }
            }

            function generateDetailTable(data) {
                // Function untuk generate tabel detail perhitungan yang sudah ada
                const detailHtml = `
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Indikator</th>
                                    <th>Nilai Asli</th>
                                    <th>Skala Interval</th>
                                    <th>Normalisasi</th>
                                    <th>Bobot × Normalisasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${Object.keys(data.detail_skor).map(indikator => `
                                            <tr>
                                                <td><strong>${indikator}</strong></td>
                                                <td>${data.detail_skor[indikator].nilai_asli}</td>
                                                <td>${data.detail_skor[indikator].skala_interval}</td>
                                                <td>${data.detail_skor[indikator].skala_normalisasi}</td>
                                                <td><span class="badge bg-success">${(data.detail_skor[indikator].skala_normalisasi * data.bobot_global[indikator]).toFixed(6)}</span></td>
                                            </tr>
                                        `).join('')}
                            </tbody>
                            <tfoot>
                                <tr class="table-warning">
                                    <td colspan="4"><strong>Total Skor AHP</strong></td>
                                    <td><strong><span class="badge bg-primary">${data.skor_total_ahp}</span></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;

                return detailHtml;
            }
        </script>
    @endsection
