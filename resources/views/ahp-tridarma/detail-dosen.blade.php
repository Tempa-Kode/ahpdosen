@extends("app")

@section("judul", "Detail Perhitungan AHP Dosen")

@section("konten")
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>
                            Detail Perhitungan AHP Tridarma Dosen
                        </h1>
                        <p class="card-text mt-2">
                            Analisis lengkap perhitungan AHP untuk dosen tertentu
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="loading-container" class="text-center py-5">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Memuat detail perhitungan dosen...</p>
        </div>

        <!-- Detail Content -->
        <div id="detail-content" style="display: none;">
            <!-- Informasi Dosen -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5><i class="fas fa-id-card me-2"></i>Informasi Dosen</h5>
                        </div>
                        <div class="card-body" id="info-dosen">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-trophy me-2"></i>Hasil Perhitungan</h5>
                        </div>
                        <div class="card-body" id="hasil-perhitungan">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Kriteria -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5><i class="fas fa-chart-bar me-2"></i>Detail Penilaian per Kriteria</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Kriteria</th>
                                            <th>Nama Kriteria</th>
                                            <th>Nilai Asli</th>
                                            <th>Nilai Normalisasi</th>
                                            <th>Bobot Kriteria</th>
                                            <th>Kontribusi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail-kriteria">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formula Perhitungan -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5><i class="fas fa-calculator me-2"></i>Formula Perhitungan Prioritas Global</h5>
                        </div>
                        <div class="card-body">
                            <div id="formula-detail" class="bg-light p-3 rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Perbandingan dengan Dosen Lain -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5><i class="fas fa-users me-2"></i>Posisi Ranking</h5>
                        </div>
                        <div class="card-body">
                            <div id="ranking-comparison">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <button class="btn btn-outline-primary btn-lg" onclick="window.history.back()">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Halaman Utama
                </button>
            </div>
        </div>
    </div>
@endsection

@section("js")
    <style>
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .formula-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Courier New', monospace;
            line-height: 1.6;
        }

        .ranking-badge {
            font-size: 1.2em;
            padding: 0.5rem 1rem;
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
    </style>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const dosenId = urlParams.get('dosen_id');

        if (!dosenId) {
            alert('ID Dosen tidak ditemukan');
            window.history.back();
        }

        async function loadDosenDetail() {
            try {
                const response = await fetch(`{{ url("/api/ahp-tridarma/dosen") }}/${dosenId}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.status === 'success') {
                    displayDosenDetail(data.data);
                    document.getElementById('loading-container').style.display = 'none';
                    document.getElementById('detail-content').style.display = 'block';
                } else {
                    throw new Error(data.message || 'Data tidak valid');
                }
            } catch (error) {
                console.error('Error loading dosen detail:', error);
                document.getElementById('loading-container').innerHTML = `
                    <div class="alert alert-danger">
                        <h6>Error!</h6>
                        <p>Gagal memuat detail dosen: ${error.message}</p>
                        <button class="btn btn-primary" onclick="location.reload()">Coba Lagi</button>
                    </div>
                `;
            }
        }

        function displayDosenDetail(data) {
            displayInfoDosen(data);
            displayHasilPerhitungan(data);
            displayDetailKriteria(data);
            displayFormula(data);
            displayRankingComparison(data);
        }

        function displayInfoDosen(data) {
            const html = `
                <div class="info-item">
                    <strong>NIDN:</strong>
                    <span>${data.dosen.nidn || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <strong>Nama Lengkap:</strong>
                    <span>${data.dosen.nama || data.dosen.nama_dosen || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <strong>Program Studi:</strong>
                    <span>${data.dosen.prodi || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <strong>Status:</strong>
                    <span class="badge bg-info">Aktif</span>
                </div>
            `;
            document.getElementById('info-dosen').innerHTML = html;
        }

        function displayHasilPerhitungan(data) {
            const rankClass = data.ranking <= 3 ? `rank-${data.ranking}` : 'bg-primary';
            const html = `
                <div class="info-item">
                    <strong>Ranking:</strong>
                    <span class="badge ranking-badge ${rankClass}">#${data.ranking || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <strong>Prioritas Global:</strong>
                    <span class="text-primary fw-bold">${data.prioritas_global || '0.000'}</span>
                </div>
                <div class="info-item">
                    <strong>Persentase:</strong>
                    <span class="text-success fw-bold">${data.persentase || '0.00'}%</span>
                </div>
                <div class="info-item">
                    <strong>Kategori:</strong>
                    <span class="badge bg-success">${data.kategori_nilai?.kategori || 'N/A'}</span>
                </div>
            `;
            document.getElementById('hasil-perhitungan').innerHTML = html;
        }

        function displayDetailKriteria(data) {
            const kriteriaNama = {
                'KTR1': 'Pendidikan dan Pengajaran',
                'KTR2': 'Penelitian',
                'KTR3': 'Pengabdian kepada Masyarakat'
            };

            let html = '';
            if (data.detail_kriteria) {
                for (const [kode, detail] of Object.entries(data.detail_kriteria)) {
                    html += `
                        <tr>
                            <td><strong>${kode}</strong></td>
                            <td>${kriteriaNama[kode] || kode}</td>
                            <td>${detail.nilai_asli || '0'}</td>
                            <td>${detail.nilai || '0.000'}</td>
                            <td>${detail.bobot || '0.000'}</td>
                            <td class="text-primary fw-bold">${detail.kontribusi || '0.000'}</td>
                        </tr>
                    `;
                }
            }

            if (!html) {
                html = '<tr><td colspan="6" class="text-center text-muted">Data kriteria tidak tersedia</td></tr>';
            }

            document.getElementById('detail-kriteria').innerHTML = html;
        }

        function displayFormula(data) {
            if (!data.detail_kriteria) {
                document.getElementById('formula-detail').innerHTML =
                    '<p class="text-muted">Formula tidak dapat ditampilkan karena data kriteria tidak tersedia.</p>';
                return;
            }

            const detailKriteria = data.detail_kriteria;
            let formulaTerms = [];
            let calculationTerms = [];

            for (const [kode, detail] of Object.entries(detailKriteria)) {
                formulaTerms.push(`(${kode}_norm × W_${kode})`);
                calculationTerms.push(`(${detail.nilai || '0.000'} × ${detail.bobot || '0.000'})`);
            }

            const html = `
                <div class="formula-box">
                    <strong>Formula Umum:</strong><br>
                    Prioritas Global = ${formulaTerms.join(' + ')}<br><br>

                    <strong>Perhitungan untuk ${data.dosen.nama || data.dosen.nama_dosen}:</strong><br>
                    Prioritas Global = ${calculationTerms.join(' + ')}<br>
                    Prioritas Global = ${data.prioritas_global || '0.000'}<br><br>

                    <strong>Keterangan:</strong><br>
                    • KTR1_norm = Nilai normalisasi Pendidikan dan Pengajaran<br>
                    • KTR2_norm = Nilai normalisasi Penelitian<br>
                    • KTR3_norm = Nilai normalisasi Pengabdian kepada Masyarakat<br>
                    • W_KTR = Bobot prioritas masing-masing kriteria
                </div>
            `;

            document.getElementById('formula-detail').innerHTML = html;
        }

        function displayRankingComparison(data) {
            // Untuk demo, kita buat perbandingan sederhana
            const currentRank = data.ranking || 0;
            const totalDosen = 100; // Asumsi total dosen

            const percentile = ((totalDosen - currentRank + 1) / totalDosen * 100).toFixed(1);

            const html = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 class="text-primary">#${currentRank}</h3>
                            <p class="text-muted">Ranking dari ${totalDosen} dosen</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 class="text-success">${percentile}%</h3>
                            <p class="text-muted">Persentil</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 class="text-info">${data.prioritas_global || '0.000'}</h3>
                            <p class="text-muted">Skor AHP</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>Interpretasi Ranking:</h6>
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: ${percentile}%"
                             aria-valuenow="${percentile}" aria-valuemin="0" aria-valuemax="100">
                            ${percentile}%
                        </div>
                    </div>
                    <small class="text-muted">
                        Dosen ini berada pada ${percentile}% teratas dari seluruh dosen yang dinilai.
                        ${currentRank <= 10 ? 'Termasuk dalam 10 besar!' :
                          currentRank <= 25 ? 'Termasuk dalam kuartil pertama.' :
                          'Masih ada ruang untuk peningkatan.'}
                    </small>
                </div>
            `;

            document.getElementById('ranking-comparison').innerHTML = html;
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadDosenDetail();
        });
    </script>
@endsection
