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

        <!-- Tahap Choice: Prioritas Global -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-crown me-2"></i>
                            Tahap Choice : Prioritas Global
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Tahap Choice - Prioritas Global</h6>
                            <p class="mb-0">Pada tahap <strong>choice</strong> ini akan dilakukan perbandingan dari setiap
                                kriteria yang ada dengan mengalikan nilai bobot prioritas dari persepsi, dengan bobot
                                prioritas setiap kriteria dengan cara:</p>
                        </div>

                        <!-- Tabel Prioritas Global -->
                        <div class="row">
                            <div class="col-12">
                                <h6>Hasil Prioritas Global</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered prioritas-global-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th rowspan="2" class="text-center align-middle">KRITERIA</th>
                                                <th colspan="5" class="text-center">ALTERNATIF (DOSEN)</th>
                                                <th rowspan="2" class="text-center align-middle">Prioritas Global</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">K1</th>
                                                <th class="text-center">K2</th>
                                                <th class="text-center">K3</th>
                                                <th class="text-center">K4</th>
                                                <th class="text-center">K5</th>
                                            </tr>
                                        </thead>
                                        <tbody id="prioritas-global-tbody">
                                            <!-- Data akan diisi via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Formula Perhitungan -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Formula Perhitungan Prioritas Global</h6>
                                <div class="alert alert-light border">
                                    <p class="mb-2"><strong>Untuk Nilai Persepsi Berdasarkan Nilai bobot Prioritas yang
                                            dihasilkan dari total matriks kriteria dibagi elemen:</strong></p>
                                    <div class="row" id="formula-perhitungan">
                                        <!-- Formula akan diisi via JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ranking Final -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Ranking Final Berdasarkan Prioritas Global</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="table-success">
                                            <tr>
                                                <th>Ranking</th>
                                                <th>Nama Dosen</th>
                                                <th>Program Studi</th>
                                                <th>Prioritas Global</th>
                                                <th>Persentase (%)</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ranking-final-tbody">
                                            <!-- Data akan diisi via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
                updatePrioritasGlobal(data);
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

        function updatePrioritasGlobal(data) {
            try {
                const bobotPrioritas = data.langkah_perhitungan['3_bobot_prioritas'].bobot_prioritas;
                const ranking = data.hasil_ranking;

                if (!bobotPrioritas || !ranking) {
                    console.warn('Data bobot prioritas atau ranking tidak tersedia');
                    return;
                }

                // Update tabel prioritas global
                const tbody = document.getElementById('prioritas-global-tbody');

                // Buat baris untuk bobot prioritas
                const bobotRow = `
                        <tr class="table-warning">
                            <td><strong>Bobot Prioritas</strong></td>
                            <td class="text-center">${bobotPrioritas.KPT01 || '0.000'}</td>
                            <td class="text-center">${bobotPrioritas.KPT02 || '0.000'}</td>
                            <td class="text-center">${bobotPrioritas.KPT03 || '0.000'}</td>
                            <td class="text-center">${bobotPrioritas.KPT04 || '0.000'}</td>
                            <td class="text-center">${bobotPrioritas.KPT05 || '0.000'}</td>
                            <td class="text-center"><strong>-</strong></td>
                        </tr>
                    `;

                // Buat baris untuk setiap dosen (hanya top 5)
                const dosenRows = ranking.slice(0, 5).map((item, index) => {
                    const prioritasGlobal = item.skor_total_ahp;

                    return `
                            <tr>
                                <td><strong>${item.dosen?.nama?.split(' ').slice(0, 2).join(' ') || 'N/A'}</strong></td>
                                <td class="text-center">${item.detail_skor?.KPT01?.skala_normalisasi || 0}</td>
                                <td class="text-center">${item.detail_skor?.KPT02?.skala_normalisasi || 0}</td>
                                <td class="text-center">${item.detail_skor?.KPT03?.skala_normalisasi || 0}</td>
                                <td class="text-center">${item.detail_skor?.KPT04?.skala_normalisasi || 0}</td>
                                <td class="text-center">${item.detail_skor?.KPT05?.skala_normalisasi || 0}</td>
                                <td class="text-center priority-value">${prioritasGlobal}</td>
                            </tr>
                        `;
                }).join('');

                tbody.innerHTML = bobotRow + dosenRows;

                // Update formula perhitungan
                updateFormulaPerhitungan(ranking.slice(0, 2), bobotPrioritas);

                // Update ranking final
                updateRankingFinal(ranking);

            } catch (error) {
                console.error('Error updating prioritas global:', error);
                document.getElementById('prioritas-global-tbody').innerHTML =
                    '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
            }
        }

        function updateFormulaPerhitungan(topDosen, bobotPrioritas) {
            const formulaDiv = document.getElementById('formula-perhitungan');

            const formulaHtml = topDosen.map((dosen, index) => {
                const nama = dosen.dosen.nama.split(' ').slice(0, 2).join(' ');
                const calculations = [
                    `(${dosen.detail_skor.KPT01?.skala_normalisasi || 0} × ${bobotPrioritas.KPT01})`,
                    `(${dosen.detail_skor.KPT02?.skala_normalisasi || 0} × ${bobotPrioritas.KPT02})`,
                    `(${dosen.detail_skor.KPT03?.skala_normalisasi || 0} × ${bobotPrioritas.KPT03})`,
                    `(${dosen.detail_skor.KPT04?.skala_normalisasi || 0} × ${bobotPrioritas.KPT04})`,
                    `(${dosen.detail_skor.KPT05?.skala_normalisasi || 0} × ${bobotPrioritas.KPT05})`
                ];

                return `
                        <div class="col-md-6">
                            <div class="card formula-card">
                                <div class="card-body">
                                    <h6><strong>${nama} =</strong></h6>
                                    <p class="mb-1" style="font-size: 0.9em;">${calculations.join(' + ')}</p>
                                    <p class="mb-0 priority-value">= ${dosen.skor_total_ahp}</p>
                                </div>
                            </div>
                        </div>
                    `;
            }).join('');

            formulaDiv.innerHTML = formulaHtml;
        }

        function updateRankingFinal(ranking) {
            const tbody = document.getElementById('ranking-final-tbody');

            const maxScore = Math.max(...ranking.map(item => item.skor_total_ahp));

            const rows = ranking.slice(0, 10).map((item, index) => {
                const persentase = ((item.skor_total_ahp / maxScore) * 100).toFixed(2);
                const statusBadge = index === 0 ?
                    '<span class="badge bg-warning"><i class="fas fa-crown"></i> Terbaik</span>' :
                    index < 3 ? '<span class="badge bg-success">Top 3</span>' :
                    '<span class="badge bg-secondary">Standar</span>';

                const rankBadge = index < 3 ? `rank-${index + 1}` : 'bg-light';

                return `
                        <tr ${index === 0 ? 'class="table-warning"' : ''}>
                            <td>
                                <span class="badge rank-badge ${rankBadge}">
                                    ${item.ranking}
                                </span>
                            </td>
                            <td><strong>${item.dosen.nama}</strong></td>
                            <td><span class="badge bg-info">${item.dosen.prodi}</span></td>
                            <td><code>${item.skor_total_ahp}</code></td>
                            <td><strong>${persentase}%</strong></td>
                            <td>${statusBadge}</td>
                        </tr>
                    `;
            }).join('');

            tbody.innerHTML = rows;
        }

        async function lihatDetailDosen(dosenId) {
            showLoading(true);

            try {
                // Cari data dosen dari ahpData yang sudah ada
                const dosenData = ahpData.hasil_ranking.find(item => item.dosen.id == dosenId);

                if (!dosenData) {
                    throw new Error('Data dosen tidak ditemukan');
                }

                const modalContent = `
            <div class="row">
                <div class="col-12 mb-3">
                    <h6>Informasi Dosen</h6>
                    <p><strong>NIDN:</strong> ${dosenData.dosen.nidn}</p>
                    <p><strong>Nama:</strong> ${dosenData.dosen.nama}</p>
                    <p><strong>Program Studi:</strong> ${dosenData.dosen.prodi}</p>
                    <p><strong>Ranking:</strong> <span class="badge bg-warning">${dosenData.ranking}</span></p>
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
                                <table class="table table-sm table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Indikator</th>
                                            <th>Nama Indikator</th>
                                            <th>Total Nilai Indikator</th>
                                            <th>Skala Normalisasi</th>
                                            <th>Bobot Prioritas</th>
                                            <th>Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${Object.entries(dosenData.detail_skor).map(([kode, detail]) => {
                                            const namaIndikator = {
                                                'KPT01': 'Publikasi Terakreditasi Nasional & Internasional',
                                                'KPT02': 'Presentasi dalam seminar nasional dan internasional',
                                                'KPT03': 'Buku dari hasil penelitian',
                                                'KPT04': 'HaKI',
                                                'KPT05': 'Karya Ilmiah atau seni yang dipamerkan'
                                            };

                                            return `
                                                <tr>
                                                    <td><strong>${kode}</strong></td>
                                                    <td><small>${namaIndikator[kode] || 'Unknown'}</small></td>
                                                    <td><span class="badge bg-secondary">${detail.total_nilai_indikator || 0}</span></td>
                                                    <td><span class="badge bg-info">${detail.skala_normalisasi}</span></td>
                                                    <td><span class="badge bg-warning text-dark">${detail.bobot_prioritas}</span></td>
                                                    <td><span class="badge bg-success">${detail.skor}</span></td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Matriks Perbandingan Individual -->
                        <div class="tab-pane fade" id="matriks-pane" role="tabpanel">
                            ${generateMatriksPerbandinganHtml(dosenData)}
                        </div>
                    </div>
                </div>
            </div>
        `;

                document.getElementById('modal-detail-content').innerHTML = modalContent;
                new bootstrap.Modal(document.getElementById('detailDosenModal')).show();

            } catch (error) {
                console.error('Error loading detail dosen:', error);
                alert('Gagal memuat detail dosen: ' + error.message);
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

        function generateMatriksPerbandinganHtml(dosenData) {
            const indikatorKode = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'];
            const namaIndikator = {
                'KPT01': 'Publikasi Terakreditasi Nasional & Internasional',
                'KPT02': 'Presentasi dalam seminar nasional dan internasional',
                'KPT03': 'Buku dari hasil penelitian',
                'KPT04': 'HaKI',
                'KPT05': 'Karya Ilmiah atau seni yang dipamerkan'
            };

            return `
                    <div class="row">
                        <!-- Matriks Perbandingan Berpasangan (Simulasi) -->
                        <div class="col-12 mb-4">
                            <h6><i class="fas fa-table"></i> Matriks Perbandingan Berpasangan</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th></th>
                                            ${indikatorKode.map(kode => `<th>${kode}</th>`).join('')}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${indikatorKode.map(kode1 => `
                                                <tr>
                                                    <th class="table-dark">${kode1}</th>
                                                    ${indikatorKode.map(kode2 => {
                                                        if (kode1 === kode2) {
                                                            return '<td><strong>1</strong></td>';
                                                        } else {
                                                            const nilai1 = dosenData.detail_skor[kode1]?.skala_normalisasi || 1;
                                                            const nilai2 = dosenData.detail_skor[kode2]?.skala_normalisasi || 1;
                                                            const perbandingan = (nilai1 / nilai2).toFixed(3);
                                                            return `<td>${perbandingan}</td>`;
                                                        }
                                                    }).join('')}
                                                </tr>
                                            `).join('')}
                                    </tbody>
                                    <tfoot class="table-warning">
                                        <tr>
                                            <th><strong>TOTAL</strong></th>
                                            ${indikatorKode.map(kodeCol => {
                                                // Hitung total untuk setiap kolom
                                                let totalKolom = 0;
                                                indikatorKode.forEach(kodeBaris => {
                                                    if (kodeBaris === kodeCol) {
                                                        totalKolom += 1; // Diagonal = 1
                                                    } else {
                                                        const nilai1 = dosenData.detail_skor[kodeBaris]?.skala_normalisasi || 1;
                                                        const nilai2 = dosenData.detail_skor[kodeCol]?.skala_normalisasi || 1;
                                                        totalKolom += parseFloat((nilai1 / nilai2).toFixed(3));
                                                    }
                                                });
                                                return `<th class="bg-warning"><strong>${totalKolom.toFixed(3)}</strong></th>`;
                                            }).join('')}
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Matriks Normalisasi dan Bobot Prioritas -->
                        <div class="col-12 mb-4">
                            <h6><i class="fas fa-calculator"></i>Bobot Prioritas</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm text-center">
                                    <thead class="table-success">
                                        <tr>
                                            <th></th>
                                            ${indikatorKode.map(kode => `<th>${kode}</th>`).join('')}
                                            <th>Jumlah</th>
                                            <th>Bobot Prioritas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${indikatorKode.map(kode1 => {
                                            // Hitung total untuk setiap kolom (sama seperti sebelumnya)
                                            const totalsPerKolom = {};
                                            indikatorKode.forEach(kodeCol => {
                                                let totalKolom = 0;
                                                indikatorKode.forEach(kodeBaris => {
                                                    if (kodeBaris === kodeCol) {
                                                        totalKolom += 1;
                                                    } else {
                                                        const nilai1 = dosenData.detail_skor[kodeBaris]?.skala_normalisasi || 1;
                                                        const nilai2 = dosenData.detail_skor[kodeCol]?.skala_normalisasi || 1;
                                                        totalKolom += parseFloat((nilai1 / nilai2).toFixed(3));
                                                    }
                                                });
                                                totalsPerKolom[kodeCol] = totalKolom;
                                            });

                                            // Hitung nilai normalisasi untuk baris ini
                                            const nilaiNormalisasi = [];
                                            indikatorKode.forEach(kode2 => {
                                                let nilaiAsli;
                                                if (kode1 === kode2) {
                                                    nilaiAsli = 1;
                                                } else {
                                                    const nilai1 = dosenData.detail_skor[kode1]?.skala_normalisasi || 1;
                                                    const nilai2 = dosenData.detail_skor[kode2]?.skala_normalisasi || 1;
                                                    nilaiAsli = parseFloat((nilai1 / nilai2).toFixed(3));
                                                }
                                                const normalisasi = (nilaiAsli / totalsPerKolom[kode2]).toFixed(4);
                                                nilaiNormalisasi.push(parseFloat(normalisasi));
                                            });

                                            // Hitung rata-rata (bobot prioritas)
                                            const rataRata = (nilaiNormalisasi.reduce((sum, val) => sum + val, 0) / nilaiNormalisasi.length).toFixed(4);

                                            // Hitung jumlah per baris
                                            const jumlahBaris = nilaiNormalisasi.reduce((sum, val) => sum + val, 0).toFixed(4);

                                            return `
                                                <tr>
                                                    <th class="table-success">${kode1}</th>
                                                    ${nilaiNormalisasi.map(nilai => `<td>${nilai.toFixed(4)}</td>`).join('')}
                                                    <td class="bg-info text-white"><strong>${jumlahBaris}</strong></td>
                                                    <th class="bg-warning"><strong>${rataRata}</strong></th>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
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

                // Update prioritas global dengan data yang difilter
                const filteredAhpData = {
                    ...ahpData,
                    hasil_ranking: filteredData
                };
                updatePrioritasGlobal(filteredAhpData);
            });
        </script>
@endsection
