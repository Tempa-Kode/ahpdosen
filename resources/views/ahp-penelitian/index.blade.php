@extends("app")

@section("judul", "AHP Penelitian - Ranking Dosen")

@section("konten")
    <div class="container-fluid">

        {{-- <div class="row mb-4" id="statistik-cards">
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Memuat Statistik...</p>
            </div>
        </div> --}}

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
                            Ranking Dosen Berdasarkan AHP
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="ranking-table">
                                <thead class="table-dark">
                                    {{-- INI BAGIAN YANG DIPERBAIKI --}}
                                    <tr>
                                        <th class="text-center">Rank</th>
                                        <th>NIDN</th>
                                        <th>Nama Dosen</th>
                                        <th>Prodi</th>
                                        <th class="text-center" title="Publikasi Terakreditasi">K1</th>
                                        <th class="text-center" title="Presentasi Seminar">K2</th>
                                        <th class="text-center" title="Buku Penelitian">K3</th>
                                        <th class="text-center" title="HaKI">K4</th>
                                        <th class="text-center" title="Karya Ilmiah/Seni">K5</th>
                                        <th class="text-center">Bobot Prioritas</th>
                                        <th class="text-center">Skor AHP</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="ranking-tbody">
                                    {{-- Konten diisi oleh JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Tahap Choice: Prioritas Global & Perankingan Final
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Penjelasan Tahap Choice</h6>
                            <p class="mb-0">
                                Pada tahap ini, nilai bobot prioritas kriteria dikalikan dengan skor ternormalisasi setiap dosen untuk mendapatkan skor akhir (Prioritas Global). Berdasarkan skor inilah perankingan final ditentukan.
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h6>Hasil Prioritas Global (Top 5 Dosen)</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered prioritas-global-table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="text-center align-middle">ALTERNATIF (DOSEN)</th>
                                                <th colspan="5" class="text-center">SKOR TERNORMALISASI PER KRITERIA</th>
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
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Contoh Formula Perhitungan Prioritas Global</h6>
                                <div class="alert alert-light border">
                                    <div class="row" id="formula-perhitungan">
                                        </div>
                                </div>
                            </div>
                        </div>

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
                                                <th>Nilai Desimal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ranking-final-tbody">
                                            </tbody>
                                    </table>
                                </div>
                            </div>
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

    <div class="modal fade" id="detailDosenModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>
                        Detail Perhitungan AHP Dosen
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-detail-content">
                    </div>
            </div>
        </div>
    </div>

    <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-none align-items-center justify-content-center" style="z-index: 9999;">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4>Memuat data AHP...</h4>
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
                const response = await fetch('/api/ahp-penelitian');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log('API Response:', data);

                ahpData = data;
                filteredData = Array.isArray(data.hasil_ranking) ? data.hasil_ranking : [];

                // Perbarui semua komponen UI
                updateUI(data);

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
            // Statistik
            // if (data.statistik && data.langkah_perhitungan?.['4_uji_konsistensi']) {
            //     updateStatistiks(data.statistik, data.langkah_perhitungan['4_uji_konsistensi']);
            // } else {
            //     document.getElementById('statistik-cards').innerHTML = '<div class="col-12 text-center text-danger">Statistik tidak tersedia</div>';
            // }

            // Konsistensi
            if (data.langkah_perhitungan?.['4_uji_konsistensi']) {
                updateKonsistensi(data.langkah_perhitungan['4_uji_konsistensi']);
            } else {
                document.getElementById('status-konsistensi').innerHTML = '<span class="badge bg-secondary">Tidak tersedia</span>';
            }

            // Tabel Ranking
            if (Array.isArray(data.hasil_ranking) && data.hasil_ranking.length > 0) {
                updateRankingTable(data.hasil_ranking);
                createCharts(data.hasil_ranking); // Buat chart jika data ranking ada
            } else {
                document.getElementById('ranking-tbody').innerHTML = '<tr><td colspan="12" class="text-center text-danger">Data ranking tidak tersedia</td></tr>';
            }

            // Tabel Prioritas Global
            updatePrioritasGlobal(data);
        }

        // Fungsi untuk menampilkan error di tabel jika fetch gagal
        function displayErrorInTables() {
            document.getElementById('ranking-tbody').innerHTML = '<tr><td colspan="12" class="text-center text-danger">Gagal memuat data AHP</td></tr>';
            document.getElementById('prioritas-global-tbody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Gagal memuat data AHP</td></tr>';
        }

        function updateStatistiks(statistik, konsistensiData) {
            const statsHtml = `
                <div class="col-md-3"><div class="card card-stats text-center"><div class="card-body"><div class="stats-icon"><i class="fas fa-users"></i></div><h3 class="mt-2">${statistik.total_dosen}</h3><p class="mb-0">Total Dosen</p></div></div></div>
                <div class="col-md-3"><div class="card card-stats text-center"><div class="card-body"><div class="stats-icon"><i class="fas fa-calculator"></i></div><h3 class="mt-2">${statistik.avg_score}</h3><p class="mb-0">Rata-rata Skor</p></div></div></div>
                <div class="col-md-3"><div class="card card-stats text-center"><div class="card-body"><div class="stats-icon"><i class="fas fa-trophy"></i></div><h3 class="mt-2">${statistik.max_score}</h3><p class="mb-0">Skor Tertinggi</p></div></div></div>
                <div class="col-md-3"><div class="card card-stats text-center"><div class="card-body"><div class="stats-icon"><i class="fas fa-check-circle"></i></div><h3 class="mt-2">${konsistensiData.status_konsistensi}</h3><p class="mb-0">Status</p></div></div></div>
            `;
            document.getElementById('statistik-cards').innerHTML = statsHtml;
        }

        function updateKonsistensi(konsistensiData) {
            const badgeClass = konsistensiData.status_konsistensi === 'Konsisten' ? 'bg-success' : 'bg-danger';
            const crDisplay = konsistensiData.CR || konsistensiData.CR_raw || 0;
            document.getElementById('status-konsistensi').innerHTML = `<span class="badge ${badgeClass}">${konsistensiData.status_konsistensi}</span><small class="text-muted d-block">CR: <strong>${crDisplay}</strong></small>`;
        }

        function updateRankingTable(rankingData) {
            const tbody = document.getElementById('ranking-tbody');
            tbody.innerHTML = rankingData.map(item => {
                const rankClass = item.ranking <= 3 ? `rank-${item.ranking}` : 'bg-light';
                return `
                    <tr>
                        <td><span class="badge rank-badge ${rankClass}">${item.ranking}</span></td>
                        <td>${item.dosen.nidn}</td>
                        <td><strong>${item.dosen.nama}</strong></td>
                        <td><span class="badge bg-info">${item.dosen.prodi}</span></td>
                        <td><span class="badge bg-secondary indikator-score">${item.detail_skor.KPT01?.skala_normalisasi || 0}</span></td>
                        <td><span class="badge bg-secondary indikator-score">${item.detail_skor.KPT02?.skala_normalisasi || 0}</span></td>
                        <td><span class="badge bg-secondary indikator-score">${item.detail_skor.KPT03?.skala_normalisasi || 0}</span></td>
                        <td><span class="badge bg-secondary indikator-score">${item.detail_skor.KPT04?.skala_normalisasi || 0}</span></td>
                        <td><span class="badge bg-secondary indikator-score">${item.detail_skor.KPT05?.skala_normalisasi || 0}</span></td>
                        <td class="text-center" style="font-size: 0.8em; font-family: monospace;">
                            <div style="line-height: 1.3;">
                                <small>KPT01: ${item.detail_skor.KPT01?.bobot_prioritas || '0.000'}</small><br>
                                <small>KPT02: ${item.detail_skor.KPT02?.bobot_prioritas || '0.000'}</small><br>
                                <small>KPT03: ${item.detail_skor.KPT03?.bobot_prioritas || '0.000'}</small><br>
                                <small>KPT04: ${item.detail_skor.KPT04?.bobot_prioritas || '0.000'}</small><br>
                                <small>KPT05: ${item.detail_skor.KPT05?.bobot_prioritas || '0.000'}</small>
                            </div>
                        </td>
                        <td><span class="badge bg-success skor-badge">${item.skor_total_ahp}</span></td>
                        <td><button class="btn btn-sm btn-outline-primary" onclick="lihatDetailDosen(${item.dosen.id})"><i class="fas fa-eye"></i> Detail</button></td>
                    </tr>
                `;
            }).join('');
        }

        function updatePrioritasGlobal(data) {
            const tbody = document.getElementById('prioritas-global-tbody');
            const bobotPrioritas = data.langkah_perhitungan?.['3_bobot_prioritas']?.bobot_prioritas;
            const ranking = Array.isArray(data.prioritas_global) ? data.prioritas_global : [];

            if (!bobotPrioritas || ranking.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Data prioritas tidak lengkap.</td></tr>';
                return;
            }

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

            const dosenRows = ranking.slice(0, 5).map(item => `
                <tr>
                    <td><strong>${item.dosen?.nama || 'N/A'}</strong></td>
                    <td class="text-center">${item.detail_skor?.KPT01?.bobot_prioritas || 0}</td>
                    <td class="text-center">${item.detail_skor?.KPT02?.bobot_prioritas || 0}</td>
                    <td class="text-center">${item.detail_skor?.KPT03?.bobot_prioritas || 0}</td>
                    <td class="text-center">${item.detail_skor?.KPT04?.bobot_prioritas || 0}</td>
                    <td class="text-center">${item.detail_skor?.KPT05?.bobot_prioritas || 0}</td>
                    <td class="text-center priority-value">${item.prioritas_global }</td>
                </tr>
            `).join('');

            tbody.innerHTML = bobotRow + dosenRows;

            updateFormulaPerhitungan(ranking.slice(0, 2), bobotPrioritas);
            updateRankingFinal(ranking);
        }

        function updateFormulaPerhitungan(topDosen, bobotPrioritas) {
            const formulaDiv = document.getElementById('formula-perhitungan');
            formulaDiv.innerHTML = topDosen.map(dosen => {
                const nama = dosen.dosen.nama.split(' ').slice(0, 2).join(' ');
                const calculations = [
                    `(${dosen.detail_skor.KPT01?.bobot_prioritas || 0} × ${bobotPrioritas.KPT01})`,
                    `(${dosen.detail_skor.KPT02?.bobot_prioritas || 0} × ${bobotPrioritas.KPT02})`,
                    `(${dosen.detail_skor.KPT03?.bobot_prioritas || 0} × ${bobotPrioritas.KPT03})`,
                    `(${dosen.detail_skor.KPT04?.bobot_prioritas || 0} × ${bobotPrioritas.KPT04})`,
                    `(${dosen.detail_skor.KPT05?.bobot_prioritas || 0} × ${bobotPrioritas.KPT05})`
                ];
                return `
                    <div class="col-md-6">
                        <div class="card formula-card"><div class="card-body">
                            <h6><strong>${nama} =</strong></h6>
                            <p class="mb-1" style="font-size: 0.9em;">${calculations.join(' + ')}</p>
                            <p class="mb-0 priority-value">= ${dosen.prioritas_global}</p>
                        </div></div>
                    </div>
                `;
            }).join('');
        }

        function updateRankingFinal(ranking) {
            const tbody = document.getElementById('ranking-final-tbody');
            if (ranking.length === 0) return;

            const maxScore = Math.max(...ranking.map(item => item.skor_total_ahp));
            tbody.innerHTML = ranking.slice(0, 10).map((item, index) => {
                const persentase = maxScore > 0 ? ((item.skor_total_ahp / maxScore) * 100).toFixed(2) : 0;
                const rankBadge = index < 3 ? `rank-${index + 1}` : 'bg-light';
                return `
                    <tr ${index === 0 ? 'class="table-warning"' : ''}>
                        <td><span class="badge rank-badge ${rankBadge}">${item.ranking}</span></td>
                        <td><strong>${item.dosen.nama}</strong></td>
                        <td><span class="badge bg-info">${item.dosen.prodi}</span></td>
                        <td><code>${item.prioritas_global}</code></td>
                        <td><strong>${item.persentase}%</strong></td>
                        <td><strong>${item.nilai_decimal}</strong></td>
                    </tr>
                `;
            }).join('');
        }

        function lihatDetailDosen(dosenId) {
            const dosenData = ahpData.hasil_ranking.find(item => item.dosen.id == dosenId);
            if (!dosenData) {
                alert('Data dosen tidak ditemukan');
                return;
            }

            const modalContent = `
                <div class="row">
                    <div class="col-12 mb-3">
                        <h6>Informasi Dosen</h6>
                        <p class="mb-1"><strong>NIDN:</strong> ${dosenData.dosen.nidn}</p>
                        <p class="mb-1"><strong>Nama:</strong> ${dosenData.dosen.nama}</p>
                        <p class="mb-1"><strong>Program Studi:</strong> ${dosenData.dosen.prodi}</p>
                        <p class="mb-0"><strong>Ranking:</strong> <span class="badge bg-warning">${dosenData.ranking}</span></p>
                    </div>
                    <div class="col-12">
                        <ul class="nav nav-tabs" id="dosenDetailTabs" role="tablist">
                            <li class="nav-item" role="presentation"><button class="nav-link active" id="perhitungan-tab" data-bs-toggle="tab" data-bs-target="#perhitungan-pane" type="button" role="tab"><i class="fas fa-calculator"></i> Detail Perhitungan</button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link" id="matriks-tab" data-bs-toggle="tab" data-bs-target="#matriks-pane" type="button" role="tab"><i class="fas fa-table"></i> Matriks Perbandingan Individual</button></li>
                        </ul>
                        <div class="tab-content mt-3" id="dosenDetailTabsContent">
                            <div class="tab-pane fade show active" id="perhitungan-pane" role="tabpanel">
                                <h6>Detail Perhitungan per Indikator</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Indikator</th><th>Nama Indikator</th><th>Total Nilai</th><th>Skala Normalisasi</th><th>Bobot Prioritas</th><th>Skor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${Object.entries(dosenData.detail_skor).map(([kode, detail]) => `
                                                <tr>
                                                    <td><strong>${kode}</strong></td>
                                                    <td><small>${getNamaIndikator(kode)}</small></td>
                                                    <td><span class="badge bg-secondary">${detail.total_nilai_indikator || 0}</span></td>
                                                    <td><span class="badge bg-info">${detail.skala_normalisasi}</span></td>
                                                    <td><span class="badge bg-warning text-dark">${detail.bobot_prioritas}</span></td>
                                                    <td><span class="badge bg-success">${detail.skor}</span></td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="matriks-pane" role="tabpanel">
                                ${generateMatriksPerbandinganHtml(dosenData)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('modal-detail-content').innerHTML = modalContent;
            new bootstrap.Modal(document.getElementById('detailDosenModal')).show();
        }

        // Fungsi pembantu untuk mendapatkan nama indikator
        function getNamaIndikator(kode) {
            const nama = {
                'KPT01': 'Publikasi Terakreditasi Nasional & Internasional',
                'KPT02': 'Presentasi dalam seminar nasional dan internasional',
                'KPT03': 'Buku dari hasil penelitian',
                'KPT04': 'HaKI',
                'KPT05': 'Karya Ilmiah atau seni yang dipamerkan'
            };
            return nama[kode] || 'Indikator Tidak Dikenal';
        }

        /**
         * Fungsi ini menghasilkan HTML untuk tab matriks di modal detail.
         * Perhitungan di sini adalah simulasi berdasarkan skor ternormalisasi individu dosen,
         * BUKAN matriks perbandingan kriteria utama AHP.
         */
        function generateMatriksPerbandinganHtml(dosenData) {
            const indikatorKode = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'];
            const n = indikatorKode.length;
            const RI = 1.12; // Random Index untuk n=5

            // --- 1. Lakukan semua kalkulasi sekali ---
            const pairwiseMatrix = indikatorKode.map(kode1 =>
                indikatorKode.map(kode2 => {
                    const nilai1 = dosenData.detail_skor[kode1]?.skala_normalisasi || 1;
                    const nilai2 = dosenData.detail_skor[kode2]?.skala_normalisasi || 1;
                    return nilai2 !== 0 ? nilai1 / nilai2 : 1;
                })
            );

            const columnTotals = indikatorKode.map((_, colIndex) =>
                pairwiseMatrix.reduce((sum, row) => sum + row[colIndex], 0)
            );

            const   normalizedMatrix = pairwiseMatrix.map(row =>
                row.map((val, colIndex) => columnTotals[colIndex] !== 0 ? val / columnTotals[colIndex] : 0)
            );

            const priorityWeights = normalizedMatrix.map(row =>
                row.reduce((sum, val) => sum + val, 0) / n
            );

            const weightedSumVector = pairwiseMatrix.map(row =>
                row.reduce((sum, val, index) => sum + val * priorityWeights[index], 0)
            );

            const lambdaVector = weightedSumVector.map((val, index) =>
                priorityWeights[index] !== 0 ? val / priorityWeights[index] : 0
            );

            const lambdaMax = lambdaVector.reduce((sum, val) => sum + val, 0) / n;
            const CI = n > 1 ? (lambdaMax - n) / (n - 1) : 0;
            const CR = RI !== 0 ? CI / RI : 0;
            const statusKonsistensi = CR <= 0.1 ? 'KONSISTEN' : 'TIDAK KONSISTEN';
            const badgeClass = CR <= 0.1 ? 'bg-success' : 'bg-danger';

            // --- 2. Bangun HTML menggunakan hasil kalkulasi ---
            const matriksBerpasanganHtml = `
                <div class="col-12 mb-4">
                    <h6><i class="fas fa-table"></i> Matriks Perbandingan Berpasangan (Simulasi Skor)</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-dark">
                                <tr><th></th>${indikatorKode.map(k => `<th>${k}</th>`).join('')}</tr>
                            </thead>
                            <tbody>
                                ${pairwiseMatrix.map((row, i) => `
                                    <tr>
                                        <th class="table-dark">${indikatorKode[i]}</th>
                                        ${row.map(val => `<td>${val.toFixed(3)}</td>`).join('')}
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot class="table-warning">
                                <tr>
                                    <th><strong>TOTAL</strong></th>
                                    ${columnTotals.map(total => `<th><strong>${total.toFixed(3)}</strong></th>`).join('')}
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            `;

            const bobotPrioritasHtml = `
                <div class="col-12 mb-4">
                    <h6><i class="fas fa-calculator"></i> Matriks Normalisasi & Bobot Prioritas</h6>
                     <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-success">
                                <tr>
                                    <th></th>${indikatorKode.map(k => `<th>${k}</th>`).join('')}
                                    <th>Jumlah</th><th>Bobot Prioritas</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${normalizedMatrix.map((row, i) => {
                                    const rowSum = row.reduce((s, v) => s + v, 0);
                                    return `
                                        <tr>
                                            <th class="table-success">${indikatorKode[i]}</th>
                                            ${row.map(val => `<td>${val.toFixed(4)}</td>`).join('')}
                                            <td class="bg-info text-white"><strong>${rowSum.toFixed(4)}</strong></td>
                                            <th class="bg-warning"><strong>${priorityWeights[i].toFixed(4)}</strong></th>
                                        </tr>`;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            const ujiKonsistensiHtml = `
                <div class="col-12 mb-4">
                    <h6><i class="fas fa-check-circle"></i> Uji Konsistensi (CI & CR)</h6>
                    <div class="card bg-light p-3">
                        <p class="mb-1"><strong>λ Max:</strong> ${lambdaMax.toFixed(4)}</p>
                        <p class="mb-1"><strong>CI:</strong> (λ Max - n) / (n - 1) = ${CI.toFixed(4)}</p>
                        <p class="mb-1"><strong>CR:</strong> CI / RI = ${CR.toFixed(4)}</p>
                        <hr>
                        <div class="text-center card ${badgeClass} text-white p-2">
                           <h5 class="mb-1">Consistency Ratio (CR) = ${CR.toFixed(4)}</h5>
                           <p class="mb-0"><span class="badge bg-light text-dark">${statusKonsistensi}</span></p>
                        </div>
                    </div>
                </div>
            `;

            return `<div class="row">${matriksBerpasanganHtml}${bobotPrioritasHtml}${ujiKonsistensiHtml}</div>`;
        }


        let topDosenChart, prodiChart; // Variabel untuk menyimpan instance chart
        function createCharts(rankingData) {
            // Hancurkan chart lama jika ada untuk mencegah duplikasi
            if (topDosenChart) topDosenChart.destroy();
            if (prodiChart) prodiChart.destroy();

            // Top 10 Dosen Chart
            const top10 = rankingData.slice(0, 10);
            const ctx1 = document.getElementById('topDosenChart').getContext('2d');
            topDosenChart = new Chart(ctx1, {
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
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });

            // Distribusi Program Studi Chart
            const prodiStats = {};
            rankingData.forEach(item => {
                prodiStats[item.dosen.prodi] = (prodiStats[item.dosen.prodi] || 0) + 1;
            });
            const ctx2 = document.getElementById('prodiChart').getContext('2d');
            prodiChart = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: Object.keys(prodiStats),
                    datasets: [{
                        data: Object.values(prodiStats),
                        backgroundColor: ['rgba(255, 99, 132, 0.8)','rgba(54, 162, 235, 0.8)','rgba(255, 205, 86, 0.8)','rgba(75, 192, 192, 0.8)']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        function showLoading(show) {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                if (show) {
                    overlay.classList.add('d-flex');
                    overlay.classList.remove('d-none');
                } else {
                    overlay.classList.add('d-none');
                    overlay.classList.remove('d-flex');
                }
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', initializePage);

        document.getElementById('filter-prodi').addEventListener('change', function() {
            const selectedProdi = this.value;
            filteredData = (selectedProdi === '')
                ? ahpData.hasil_ranking
                : ahpData.hasil_ranking.filter(item => item.dosen.prodi === selectedProdi);

            // Buat objek data baru yang sudah difilter untuk dikirim ke fungsi update
            const filteredAhpData = { ...ahpData, hasil_ranking: filteredData };
            updateUI(filteredAhpData);
        });

        // Fungsi utilitas global (jika diperlukan)
        function lihatLangkahPerhitungan() {
            window.open('/dashboard/ahp-penelitian/langkah-perhitungan', '_blank');
        }

        function exportToExcel() {
            alert('Fitur export Excel akan segera tersedia');
        }

        function exportToPDF() {
            alert('Fitur export PDF akan segera tersedia');
        }

    </script>
@endsection
