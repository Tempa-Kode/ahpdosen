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

            <!-- Matriks Bobot Prioritas Dosen -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5><i class="fas fa-table me-2"></i>Matriks Perbandingan</h5>
                        </div>
                        <div class="card-body">
                            <!-- Matriks Perbandingan Nilai Dosen -->
                            <div class="mb-4">
                                <h6 class="text-primary">Matriks Perbandingan Antar Kriteria (Berdasarkan Nilai Dosen)</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered matriks-table">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Kriteria</th>
                                                <th>K001</th>
                                                <th>K002</th>
                                                <th>K003</th>
                                                <th>K004</th>
                                            </tr>
                                        </thead>
                                        <tbody id="matriks-perbandingan-dosen">
                                        </tbody>
                                        <tfoot class="table-warning">
                                            <tr id="total-kolom-matriks">
                                                <th>TOTAL</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Perhitungan Bobot Prioritas dari Matriks -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-success text-white">
                            <h5><i class="fas fa-calculator me-2"></i>Perhitungan Bobot Prioritas
                            </h5>
                        </div>
                        <div class="card-body">

                            <!-- Langkah 1: Matriks Normalisasi -->
                            <div class="mb-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered matriks-table">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Kriteria</th>
                                                <th>K001</th>
                                                <th>K002</th>
                                                <th>K003</th>
                                                <th>K004</th>
                                                <th class="table-warning">Jumlah Baris</th>
                                                <th class="table-info">Bobot Prioritas</th>
                                            </tr>
                                        </thead>
                                        <tbody id="matriks-normalisasi">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Uji Konsistensi CI & CR --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-warning text-dark">
                            <h5><i class="fas fa-check-circle me-2"></i>Uji Konsistensi (CI & CR)</h5>
                        </div>
                        <div class="card-body">
                            <!-- Status Konsistensi -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert" id="status-konsistensi-alert">
                                        <h6><i class="fas fa-info-circle me-2"></i>Status Konsistensi Matriks</h6>
                                        <div id="status-konsistensi"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Langkah 1: Perhitungan Lambda Max -->
                            <div class="mb-4">
                                <h6 class="text-primary step-title">Langkah 1: Perhitungan Lambda Maksimum (λmax)</h6>
                                <p class="text-muted">Menghitung eigenvalue maksimum dari matriks perbandingan dengan rumus:
                                    λmax = Σ(Total_kolom_kriteria_i × Bobot_prioritas_i)</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Kriteria</th>
                                                <th>Total Kolom × Bobot Prioritas</th>
                                                <th>Hasil</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lambda-max-calculation">
                                        </tbody>
                                        <tfoot class="table-warning">
                                            <tr>
                                                <th>Lambda Max (λmax)</th>
                                                <th></th>
                                                <th id="lambda-max-result">0.000</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Langkah 2: Perhitungan CI (Consistency Index) -->
                            <div class="mb-4">
                                <h6 class="text-success step-title">Langkah 2: Perhitungan Consistency Index (CI)</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-success">
                                            <div class="card-body">
                                                <h6 class="card-title">Formula CI</h6>
                                                <div class="formula-box">
                                                    <strong>CI = (λmax - n) / (n - 1)</strong><br><br>
                                                    Dimana:<br>
                                                    • λmax = Lambda maksimum<br>
                                                    • n = Jumlah kriteria (4)
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-info">
                                            <div class="card-body">
                                                <h6 class="card-title">Perhitungan</h6>
                                                <div id="ci-calculation" class="result-highlight">
                                                    <!-- Akan diisi dengan JavaScript -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Langkah 3: Perhitungan CR (Consistency Ratio) -->
                            <div class="mb-4">
                                <h6 class="text-warning step-title">Langkah 3: Perhitungan Consistency Ratio (CR)</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <h6 class="card-title">Random Index (RI)</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>n</th>
                                                                <th>RI</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>1</td>
                                                                <td>0.00</td>
                                                            </tr>
                                                            <tr>
                                                                <td>2</td>
                                                                <td>0.00</td>
                                                            </tr>
                                                            <tr>
                                                                <td>3</td>
                                                                <td>0.58</td>
                                                            </tr>
                                                            <tr class="table-warning">
                                                                <td><strong>4</strong></td>
                                                                <td><strong>0.90</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>5</td>
                                                                <td>1.12</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-info">
                                            <div class="card-body">
                                                <h6 class="card-title">Formula CR</h6>
                                                <div class="formula-box">
                                                    <strong>CR = CI / RI</strong><br><br>
                                                    Dimana:<br>
                                                    • CI = Consistency Index<br>
                                                    • RI = Random Index untuk n=4
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-success">
                                            <div class="card-body">
                                                <h6 class="card-title">Hasil Perhitungan</h6>
                                                <div id="cr-calculation" class="result-highlight">
                                                    <!-- Akan diisi dengan JavaScript -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hasil Akhir Uji Konsistensi -->
                            <div class="mb-4">
                                <h6 class="text-danger step-title">Kesimpulan Uji Konsistensi</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Parameter</th>
                                                        <th>Nilai</th>
                                                        <th>Standar</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="konsistensi-summary">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card" id="kesimpulan-card">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Kesimpulan</h5>
                                                <div id="kesimpulan-konsistensi">
                                                    <!-- Akan diisi dengan JavaScript -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Penjelasan Lengkap -->
                            <div class="formula-box">
                                <strong>Penjelasan Uji Konsistensi AHP:</strong><br><br>
                                <strong>1. Lambda Maksimum (λmax):</strong><br>
                                Eigenvalue terbesar dari matriks perbandingan berpasangan<br>
                                Dihitung dengan: Σ(Total_kolom_kriteria_i × Bobot_prioritas_i)<br><br>

                                <strong>2. Consistency Index (CI):</strong><br>
                                Mengukur penyimpangan dari konsistensi sempurna<br>
                                CI = 0 berarti konsistensi sempurna<br><br>

                                <strong>3. Consistency Ratio (CR):</strong><br>
                                Rasio CI terhadap Random Index<br>
                                <strong>CR ≤ 0.1 (10%)</strong> dianggap konsisten dan dapat diterima<br>
                                <strong>CR > 0.1</strong> menunjukkan inkonsistensi dan perlu revisi<br><br>

                                <strong>4. Random Index (RI):</strong><br>
                                Nilai rata-rata CI dari matriks random dengan ukuran yang sama
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

        /* Styling untuk tabel perhitungan */
        .table-bordered th,
        .table-bordered td {
            vertical-align: middle;
            text-align: center;
        }

        .table-bordered th:first-child,
        .table-bordered td:first-child {
            text-align: left;
        }

        code {
            background-color: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.85em;
        }

        .step-title {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        /* Styling untuk matriks */
        .matriks-table th,
        .matriks-table td {
            text-align: center;
            vertical-align: middle;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .matriks-table .kriteria-header {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: left !important;
        }

        /* Gradient backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
        }

        /* Progress bars styling */
        .progress {
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
        }

        /* Card hover effects */
        .card {
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Badge animations */
        .badge {
            transition: all 0.3s ease;
        }

        .badge:hover {
            transform: scale(1.05);
        }

        /* Additional styling for new sections */
        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #1e7e34) !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: #e7f3ff;
            color: #0056b3;
        }

        .accordion-button:focus {
            z-index: 3;
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .step-title {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .step-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: #007bff;
        }

        .calculation-formula {
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            padding: 0.5rem;
            border-radius: 0.25rem;
            border-left: 3px solid #007bff;
        }

        .step-item {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 1rem;
        }

        .step-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .result-highlight {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin: 0.5rem 0;
        }

        .formula-box {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 0.375rem;
            padding: 1rem;
            margin: 0.5rem 0;
            font-size: 0.9rem;
            line-height: 1.5;
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
                console.log(data);
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
            displayMatriksBobotPrioritas(data);
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
            `;
            document.getElementById('hasil-perhitungan').innerHTML = html;
        }

        function displayMatriksBobotPrioritas(data) {
            const matriksBobotPrioritas = data.matriks_bobot_prioritas;

            if (!matriksBobotPrioritas) {
                console.warn('Data matriks bobot prioritas tidak tersedia');
                return;
            }

            // 2. Display Matriks Perbandingan
            displayMatriksPerbandinganDosen(matriksBobotPrioritas.matriks_perbandingan);

            // 5. Display Perhitungan Bobot Prioritas Matriks
            displayPerhitunganBobotPrioritasMatriks(matriksBobotPrioritas.bobot_prioritas_matriks, data.bobot_kriteria);

            // 6. Display Uji Konsistensi - menggunakan data konsistensi dari API
            displayUjiKonsistensiFromAPI(data.konsistensi);
        }

        function displayMatriksPerbandinganDosen(matriksData) {
            if (!matriksData) return;

            const {
                matriks,
                total_kolom,
                kriteria
            } = matriksData;
            let html = '';

            // Buat baris matriks
            kriteria.forEach(k1 => {
                html += `<tr><td class="kriteria-header"><strong>${k1}</strong></td>`;
                kriteria.forEach(k2 => {
                    const nilai = matriks[k1][k2];
                    const cellClass = nilai > 1 ? 'text-success fw-bold' : nilai < 1 ? 'text-danger' : '';
                    html += `<td class="${cellClass}">${nilai}</td>`;
                });
                html += '</tr>';
            });

            // Baris total
            let totalHtml = '<th>TOTAL</th>';
            total_kolom.forEach(total => {
                totalHtml += `<th>${total}</th>`;
            });

            document.getElementById('matriks-perbandingan-dosen').innerHTML = html;
            document.getElementById('total-kolom-matriks').innerHTML = totalHtml;
        }

        function displayPerhitunganBobotPrioritasMatriks(bobotPrioritasData, bobotKriteriaUmum) {
            if (!bobotPrioritasData) {
                console.warn('Data bobot prioritas matriks tidak tersedia');
                return;
            }

            const kriteriaNama = {
                'K001': 'Pendidikan dan Pembelajaran',
                'K002': 'Penelitian',
                'K003': 'PKM (Pengabdian Kepada Masyarakat)',
                'K004': 'Penunjang'
            };

            // 2. Display Matriks Normalisasi
            displayMatriksNormalisasi(bobotPrioritasData);
        }

        function displayMatriksNormalisasi(bobotPrioritasData) {
            const matriksNormalisasi = bobotPrioritasData.matriks_normalisasi;
            const bobotPrioritas = bobotPrioritasData.bobot_prioritas;
            const jumlahBaris = bobotPrioritasData.jumlah_baris;
            const kriteria = ['K001', 'K002', 'K003', 'K004'];

            let html = '';
            kriteria.forEach(k1 => {
                html += `<tr><td class="kriteria-header"><strong>${k1}</strong></td>`;
                kriteria.forEach(k2 => {
                    const nilai = matriksNormalisasi[k1][k2];
                    html += `<td>${nilai}</td>`;
                });
                html += `<td class="table-warning"><strong>${jumlahBaris[k1].toFixed(5)}</strong></td>`;
                html += `<td class="table-info"><strong>${bobotPrioritas[k1].toFixed(5)}</strong></td>`;
                html += '</tr>';
            });

            document.getElementById('matriks-normalisasi').innerHTML = html;
        }

        function displayUjiKonsistensi(matriksPerbandingan, bobotPrioritas) {
            const ujiKonsistensiContainer = document.getElementById('uji-konsistensi-hasil');
            if (!ujiKonsistensiContainer) {
                console.error('Container uji konsistensi tidak ditemukan');
                return;
            }

            // Perhitungan Lambda Max
            let lambdaMax = 0;
            const kriteria = ['K001', 'K002', 'K003', 'K004'];

            for (let i = 0; i < kriteria.length; i++) {
                let sumRow = 0;
                for (let j = 0; j < kriteria.length; j++) {
                    sumRow += matriksPerbandingan.matriks[kriteria[i]][kriteria[j]] * bobotPrioritas.bobot_prioritas[
                        kriteria[j]];
                }
                lambdaMax += sumRow;
            }

            // Perhitungan CI (Consistency Index)
            const n = kriteria.length;
            const ci = (lambdaMax - n) / (n - 1);

            // Nilai RI (Random Index) untuk n=4
            const ri = 0.90;

            // Perhitungan CR (Consistency Ratio)
            const cr = ci / ri;

            // Status konsistensi
            const isKonsisten = cr <= 0.1;

            let html = `
                <div class="alert ${isKonsisten ? 'alert-success' : 'alert-warning'} mb-4">
                    <h6 class="mb-2">
                        <i class="fas fa-${isKonsisten ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        Status Konsistensi: ${isKonsisten ? 'KONSISTEN' : 'TIDAK KONSISTEN'}
                    </h6>
                    <small>CR = ${cr.toFixed(4)} ${isKonsisten ? '≤' : '>'} 0.1</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Langkah Perhitungan</h6>
                            </div>
                            <div class="card-body">
                                <div class="step-item mb-3">
                                    <strong>1. Lambda Max (λmax)</strong>
                                    <div class="mt-2">
                                        <div class="calculation-formula mb-2">
                                            λmax = Σ(Wi × Σj(aij × Wj))
                                        </div>`;

            // Detail perhitungan lambda max
            for (let i = 0; i < kriteria.length; i++) {
                let sumRow = 0;
                let detailCalculation = '';
                for (let j = 0; j < kriteria.length; j++) {
                    const value = matriksPerbandingan.matriks[kriteria[i]][kriteria[j]] * bobotPrioritas.bobot_prioritas[
                        kriteria[j]];
                    sumRow += value;
                    detailCalculation +=
                        `(${matriksPerbandingan.matriks[kriteria[i]][kriteria[j]].toFixed(3)} × ${bobotPrioritas.bobot_prioritas[kriteria[j]].toFixed(3)})`;
                    if (j < kriteria.length - 1) detailCalculation += ' + ';
                }

                html += `
                        <div class="small text-muted mb-1">
                            ${kriteria[i]}: ${detailCalculation} = ${sumRow.toFixed(4)}
                        </div>`;
            }

            html += `
                                        <div class="fw-bold">λmax = ${lambdaMax}</div>
                                    </div>
                                </div>

                                <div class="step-item mb-3">
                                    <strong>2. Consistency Index (CI)</strong>
                                    <div class="mt-2">
                                        <div class="calculation-formula mb-2">
                                            CI = (λmax - n) / (n - 1)
                                        </div>
                                        <div class="small text-muted mb-1">
                                            CI = (${lambdaMax.toFixed(4)} - ${n}) / (${n} - 1)
                                        </div>
                                        <div class="small text-muted mb-1">
                                            CI = ${(lambdaMax - n).toFixed(4)} / ${(n - 1)}
                                        </div>
                                        <div class="fw-bold">CI = ${ci.toFixed(4)}</div>
                                    </div>
                                </div>

                                <div class="step-item">
                                    <strong>3. Consistency Ratio (CR)</strong>
                                    <div class="mt-2">
                                        <div class="calculation-formula mb-2">
                                            CR = CI / RI
                                        </div>
                                        <div class="small text-muted mb-1">
                                            RI untuk n=${n} = ${ri}
                                        </div>
                                        <div class="small text-muted mb-1">
                                            CR = ${ci.toFixed(4)} / ${ri}
                                        </div>
                                        <div class="fw-bold">CR = ${cr.toFixed(4)}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Ringkasan Hasil</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Lambda Max (λmax)</strong></td>
                                        <td class="text-end">${lambdaMax}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Consistency Index (CI)</strong></td>
                                        <td class="text-end">${ci.toFixed(4)}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Random Index (RI)</strong></td>
                                        <td class="text-end">${ri}</td>
                                    </tr>
                                    <tr class="table-${isKonsisten ? 'success' : 'warning'}">
                                        <td><strong>Consistency Ratio (CR)</strong></td>
                                        <td class="text-end"><strong>${cr.toFixed(4)}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Batas Konsistensi</strong></td>
                                        <td class="text-end">≤ 0.1</td>
                                    </tr>
                                    <tr class="table-${isKonsisten ? 'success' : 'warning'}">
                                        <td><strong>Status</strong></td>
                                        <td class="text-end">
                                            <span class="badge bg-${isKonsisten ? 'success' : 'warning'}">
                                                ${isKonsisten ? 'KONSISTEN' : 'TIDAK KONSISTEN'}
                                            </span>
                                        </td>
                                    </tr>
                                </table>

                                ${!isKonsisten ? `
                                    <div class="alert alert-warning mt-3 mb-0">
                                        <small>
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Matriks perbandingan tidak konsisten.
                                            Disarankan untuk merevisi nilai perbandingan.
                                        </small>
                                    </div>` : `
                                    <div class="alert alert-success mt-3 mb-0">
                                        <small>
                                            <i class="fas fa-check-circle me-1"></i>
                                            Matriks perbandingan konsisten dan dapat digunakan.
                                        </small>
                                    </div>`}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            ujiKonsistensiContainer.innerHTML = html;
        }

        function displayUjiKonsistensiFromAPI(konsistensiData) {
            console.log('Data konsistensi yang diterima:', konsistensiData);

            if (!konsistensiData) {
                console.warn('Data konsistensi tidak tersedia');
                return;
            }

            const {
                total_lambda_maks,
                CI,
                RI,
                CR,
                konsisten,
                lambda_maks
            } = konsistensiData;

            console.log('Lambda maks data:', lambda_maks);
            console.log('Total lambda maks:', total_lambda_maks);
            console.log('CI:', CI, 'RI:', RI, 'CR:', CR);
            console.log('Konsisten:', konsisten);

            // Status konsistensi
            const isKonsisten = konsisten === 'Ya';

            // 1. Update Status Konsistensi Alert
            const statusAlert = document.getElementById('status-konsistensi-alert');
            if (statusAlert) {
                statusAlert.className = `alert ${isKonsisten ? 'alert-success' : 'alert-warning'}`;
            }

            const statusKonsistensi = document.getElementById('status-konsistensi');
            if (statusKonsistensi) {
                statusKonsistensi.innerHTML = `
                    <strong>CR = ${CR} ${isKonsisten ? '≤' : '>'} 0.1</strong><br>
                    Status: <span class="badge bg-${isKonsisten ? 'success' : 'warning'}">
                        ${isKonsisten ? 'KONSISTEN' : 'TIDAK KONSISTEN'}
                    </span>
                `;
            }

            // 2. Update Lambda Max Calculation dengan detail
            const lambdaMaxCalc = document.getElementById('lambda-max-calculation');
            if (lambdaMaxCalc && lambda_maks) {
                let lambdaHtml = '';
                let totalLambda = 0;

                Object.keys(lambda_maks).forEach((kriteria, index) => {
                    const nilai = lambda_maks[kriteria];
                    totalLambda += parseFloat(nilai);

                    lambdaHtml += `
                        <tr>
                            <td><strong>${kriteria}</strong></td>
                            <td>Total kolom ${kriteria} × Bobot prioritas ${kriteria}</td>
                            <td>${nilai}</td>
                        </tr>
                    `;
                });
                lambdaMaxCalc.innerHTML = lambdaHtml;

                // Pastikan total ditampilkan dengan benar
                const lambdaMaxResult = document.getElementById('lambda-max-result');
                if (lambdaMaxResult) {
                    lambdaMaxResult.textContent = total_lambda_maks || totalLambda;
                }
            }

            // 3. Update CI Calculation
            const ciCalculation = document.getElementById('ci-calculation');
            if (ciCalculation) {
                ciCalculation.innerHTML = `
                    <strong>Perhitungan CI:</strong><br>
                    CI = (${total_lambda_maks} - 4) / (4 - 1)<br>
                    CI = ${(total_lambda_maks - 4)} / 3<br>
                    <strong class="text-success">CI = ${CI}</strong>
                `;
            }

            // 4. Update CR Calculation
            const crCalculation = document.getElementById('cr-calculation');
            if (crCalculation) {
                crCalculation.innerHTML = `
                    <strong>Perhitungan CR:</strong><br>
                    CR = ${CI} / ${RI}<br>
                    <strong class="text-${isKonsisten ? 'success' : 'warning'}">CR = ${CR}</strong><br><br>
                    <span class="badge bg-${isKonsisten ? 'success' : 'warning'}">
                        ${isKonsisten ? 'KONSISTEN' : 'TIDAK KONSISTEN'}
                    </span>
                `;
            }

            // 5. Update Konsistensi Summary Table
            const konsistensiSummary = document.getElementById('konsistensi-summary');
            if (konsistensiSummary) {
                konsistensiSummary.innerHTML = `
                    <tr>
                        <td><strong>Lambda Max (λmax)</strong></td>
                        <td>${total_lambda_maks}</td>
                        <td>≥ n (4)</td>
                        <td><span class="badge bg-info">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Consistency Index (CI)</strong></td>
                        <td>${CI}</td>
                        <td>≥ 0</td>
                        <td><span class="badge bg-info">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Random Index (RI)</strong></td>
                        <td>${RI}</td>
                        <td>0.90 (n=4)</td>
                        <td><span class="badge bg-info">Standar</span></td>
                    </tr>
                    <tr class="table-${isKonsisten ? 'success' : 'warning'}">
                        <td><strong>Consistency Ratio (CR)</strong></td>
                        <td><strong>${CR}</strong></td>
                        <td><strong>≤ 0.1</strong></td>
                        <td>
                            <span class="badge bg-${isKonsisten ? 'success' : 'warning'}">
                                ${isKonsisten ? 'KONSISTEN' : 'TIDAK KONSISTEN'}
                            </span>
                        </td>
                    </tr>
                `;
            }

            // 6. Update Kesimpulan Konsistensi
            const kesimpulanKonsistensi = document.getElementById('kesimpulan-konsistensi');
            const kesimpulanCard = document.getElementById('kesimpulan-card');

            if (kesimpulanKonsistensi) {
                kesimpulanKonsistensi.innerHTML = `
                    <div class="mb-3">
                        <i class="fas fa-${isKonsisten ? 'check-circle' : 'exclamation-triangle'} fa-3x text-${isKonsisten ? 'success' : 'warning'} mb-3"></i>
                    </div>
                    <h6 class="text-${isKonsisten ? 'success' : 'warning'}">${isKonsisten ? 'KONSISTEN' : 'TIDAK KONSISTEN'}</h6>
                    <p class="small mb-3">CR = ${CR}</p>
                    ${isKonsisten ?
                        '<p class="small text-muted">Matriks perbandingan dapat diterima dan digunakan untuk pengambilan keputusan.</p>' :
                        '<p class="small text-muted">Matriks perbandingan perlu direvisi karena tidak konsisten.</p>'
                    }
                `;
            }

            if (kesimpulanCard) {
                kesimpulanCard.className = `card border-${isKonsisten ? 'success' : 'warning'}`;
            }
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadDosenDetail();
        });
    </script>
@endsection
