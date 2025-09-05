@extends("app")

@section("judul", "Langkah Perhitungan AHP Tridarma")

@section("konten")
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Langkah-langkah Perhitungan AHP Tridarma
                        </h1>
                        <p class="card-text mt-2">
                            Detail proses perhitungan Analytical Hierarchy Process untuk Tridarma Perguruan Tinggi
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Steps -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <nav class="nav nav-pills nav-justified" id="step-nav">
                            <a class="nav-link active" data-step="1" href="#step1">
                                <i class="fas fa-weight me-1"></i>
                                1. Bobot Dasar
                            </a>
                            <a class="nav-link" data-step="2" href="#step2">
                                <i class="fas fa-table me-1"></i>
                                2. Matriks Perbandingan
                            </a>
                            <a class="nav-link" data-step="3" href="#step3">
                                <i class="fas fa-balance-scale me-1"></i>
                                3. Bobot Prioritas
                            </a>
                            <a class="nav-link" data-step="4" href="#step4">
                                <i class="fas fa-check-circle me-1"></i>
                                4. Uji Konsistensi
                            </a>
                            <a class="nav-link" data-step="5" href="#step5">
                                <i class="fas fa-trophy me-1"></i>
                                5. Skor AHP
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="loading-container" class="text-center py-5">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Memuat data perhitungan AHP...</p>
        </div>

        <!-- Step Content -->
        <div id="step-content" style="display: none;">
            <!-- Step 1: Bobot Dasar -->
            <div class="step-section" id="step1">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-weight me-2"></i>Step 1: Bobot Dasar Kriteria</h4>
                    </div>
                    <div class="card-body">
                        <div id="bobot-dasar-content"></div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Matriks Perbandingan -->
            <div class="step-section" id="step2" style="display: none;">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4><i class="fas fa-table me-2"></i>Step 2: Matriks Perbandingan Berpasangan</h4>
                    </div>
                    <div class="card-body">
                        <div id="matriks-perbandingan-content"></div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Bobot Prioritas -->
            <div class="step-section" id="step3" style="display: none;">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h4><i class="fas fa-balance-scale me-2"></i>Step 3: Perhitungan Bobot Prioritas</h4>
                    </div>
                    <div class="card-body">
                        <div id="bobot-prioritas-content"></div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Uji Konsistensi -->
            <div class="step-section" id="step4" style="display: none;">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4><i class="fas fa-check-circle me-2"></i>Step 4: Uji Konsistensi</h4>
                    </div>
                    <div class="card-body">
                        <div id="uji-konsistensi-content"></div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Normalisasi Data -->
            <div class="step-section" id="step6" style="display: none;">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h4><i class="fas fa-chart-bar me-2"></i>Step 5: Normalisasi Data Dosen</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Penjelasan</h6>
                            <p class="mb-0">Data nilai dosen untuk setiap kriteria dinormalisasi agar dapat dibandingkan
                                secara fair dalam perhitungan prioritas global.</p>
                        </div>
                        <div id="normalisasi-data-content"></div>
                    </div>
                </div>
            </div>

            <!-- Step 6: Prioritas Global -->
            <div class="step-section" id="step5" style="display: none;">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4><i class="fas fa-trophy me-2"></i>Step 5: Skor AHP</h4>
                    </div>
                    <div class="card-body">
                        <div id="prioritas-global-content"></div>
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
        .step-section {
            margin-bottom: 2rem;
        }

        .nav-pills .nav-link {
            color: #6c757d;
            border-radius: 0.5rem;
        }

        .nav-pills .nav-link.active {
            background-color: #007bff;
        }

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
        }

        .formula-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Courier New', monospace;
        }

        .result-highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 0.25rem;
            padding: 0.5rem;
            margin: 0.25rem 0;
        }
    </style>

    <script>
        let ahpData = {};

        async function loadAhpData() {
            try {
                const response = await fetch('/api/ahp-tridarma');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.status === 'success') {
                    ahpData = data.data;
                    displayStepContent();
                    document.getElementById('loading-container').style.display = 'none';
                    document.getElementById('step-content').style.display = 'block';
                } else {
                    throw new Error(data.message || 'Data tidak valid');
                }
            } catch (error) {
                console.error('Error loading AHP data:', error);
                document.getElementById('loading-container').innerHTML = `
                    <div class="alert alert-danger">
                        <h6>Error!</h6>
                        <p>Gagal memuat data AHP: ${error.message}</p>
                        <button class="btn btn-primary" onclick="location.reload()">Coba Lagi</button>
                    </div>
                `;
            }
        }

        function displayStepContent() {
            displayBobotDasar();
            displayMatriksPerbandingan();
            displayBobotPrioritas();
            displayUjiKonsistensi();
            displayNormalisasiData();
            displayPrioritasGlobal();
        }

        function displayBobotDasar() {
            const bobotKriteria = ahpData.bobot_prioritas?.bobot_prioritas || {};
            const kriteriaNama = ahpData.metadata?.kriteria || {};

            let html = `
                <h6>Bobot Dasar Kriteria Tridarma</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Kriteria</th>
                                <th>Bobot</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            for (const [kode, bobot] of Object.entries(bobotKriteria)) {
                const nama = kriteriaNama[kode] || kode;
                const persentase = (parseFloat(bobot) * 100).toFixed(2);
                html += `
                    <tr>
                        <td><strong>${kode}</strong></td>
                        <td>${nama}</td>
                        <td>${bobot}</td>
                        <td>${persentase}%</td>
                    </tr>
                `;
            }

            // html += `
        //             </tbody>
        //         </table>
        //     </div>
        //     <div class="formula-box">
        //         <strong>Kriteria Tridarma:</strong><br>
        //         • KTR1 (Pendidikan): Mengajar, membimbing, mengembangkan kurikulum<br>
        //         • KTR2 (Penelitian): Publikasi, riset, karya ilmiah<br>
        //         • KTR3 (Pengabdian): Pengabdian masyarakat, konsultasi, pelatihan
        //     </div>
        // `;

            document.getElementById('bobot-dasar-content').innerHTML = html;
        }

        function displayMatriksPerbandingan() {
            // Simulasi matriks perbandingan berdasarkan bobot
            const bobotKriteria = ahpData.bobot_prioritas?.bobot_prioritas || {};
            const kriteria = Object.keys(bobotKriteria);

            let html = `
                <h6>Matriks Perbandingan Berpasangan</h6>
                <div class="table-responsive">
                    <table class="table table-bordered matriks-table">
                        <thead class="table-success">
                            <tr>
                                <th>Kriteria</th>
            `;

            kriteria.forEach(k => {
                html += `<th>${k}</th>`;
            });
            html += '</tr></thead><tbody>';

            // Array untuk menyimpan total kolom
            const totalKolom = new Array(kriteria.length).fill(0);

            kriteria.forEach((k1, indexBaris) => {
                html += `<tr><td class="kriteria-header">${k1}</td>`;
                kriteria.forEach((k2, indexKolom) => {
                    let nilai;
                    if (k1 === k2) {
                        nilai = 1.00;
                        html += '<td>1.00</td>';
                    } else {
                        const bobot1 = parseFloat(bobotKriteria[k1] || 0);
                        const bobot2 = parseFloat(bobotKriteria[k2] || 0);
                        nilai = bobot2 !== 0 ? (bobot1 / bobot2) : 1.00;
                        html += `<td>${nilai}</td>`;
                    }
                    // Tambahkan ke total kolom
                    totalKolom[indexKolom] += nilai;
                });
                html += '</tr>';
            });

            // Tambahkan baris total
            html += '<tr class="table-warning"><td class="kriteria-header"><strong>TOTAL</strong></td>';
            totalKolom.forEach(total => {
                html += `<td><strong>${total}</strong></td>`;
            });
            html += '</tr>';

            document.getElementById('matriks-perbandingan-content').innerHTML = html;
        }

        function displayBobotPrioritas() {
            const bobotKriteria = ahpData.bobot_prioritas?.bobot_prioritas || {};
            const kriteria = Object.keys(bobotKriteria);

            // let html;
            // let html = `
            //     <h6>Perhitungan Bobot Prioritas</h6>

            //     <!-- Langkah 1: Matriks Perbandingan Original -->
            //     <div class="mb-4">
            //         <h6 class="text-primary">Langkah 1: Matriks Perbandingan Berpasangan</h6>
            //         <div class="table-responsive">
            //             <table class="table table-bordered matriks-table">
            //                 <thead class="table-primary">
            //                     <tr>
            //                         <th>Kriteria</th>
            // `;

            // kriteria.forEach(k => {
            //     html += `<th>${k}</th>`;
            // });
            // html += '</tr></thead><tbody>';

            // Buat matriks perbandingan dan hitung total kolom
            const matriksPerbandingan = {};
            const totalKolom = new Array(kriteria.length).fill(0);

            kriteria.forEach((k1, indexBaris) => {
                matriksPerbandingan[k1] = {};
                // html += `<tr><td class="kriteria-header">${k1}</td>`;
                kriteria.forEach((k2, indexKolom) => {
                    let nilai;
                    if (k1 === k2) {
                        nilai = 1.00;
                    } else {
                        const bobot1 = parseFloat(bobotKriteria[k1] || 0);
                        const bobot2 = parseFloat(bobotKriteria[k2] || 0);
                        nilai = bobot2 !== 0 ? (bobot1 / bobot2) : 1.00;
                    }
                    matriksPerbandingan[k1][k2] = nilai;
                    totalKolom[indexKolom] += nilai;
                    // html += `<td>${nilai.toFixed(3)}</td>`;
                });
                // html += '</tr>';
            });

            // Baris total
            // html += '<tr class="table-warning"><td class="kriteria-header"><strong>TOTAL</strong></td>';
            // totalKolom.forEach(total => {
            //     html += `<td><strong>${total.toFixed(3)}</strong></td>`;
            // });
            // html += '</tr></tbody></table></div></div>';

            // Langkah 2: Matriks Normalisasi
            let html = `
                <div class="mb-4">
                    <h6 class="text-success">Proses Bobot Prioritas</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered matriks-table">
                            <thead class="table-success">
                                <tr>
                                    <th>Kriteria</th>
            `;

            kriteria.forEach(k => {
                html += `<th>${k}</th>`;
            });
            html += '<th class="table-warning">Rata-rata Baris</th></tr></thead><tbody>';

            // Hitung matriks normalisasi dan rata-rata baris
            const matriksNormalisasi = {};
            const rataRataBaris = {};

            kriteria.forEach((k1, indexBaris) => {
                matriksNormalisasi[k1] = {};
                let jumlahBaris = 0;
                html += `<tr><td class="kriteria-header">${k1}</td>`;

                kriteria.forEach((k2, indexKolom) => {
                    const nilaiNormalisasi = matriksPerbandingan[k1][k2] / totalKolom[indexKolom];
                    matriksNormalisasi[k1][k2] = nilaiNormalisasi;
                    jumlahBaris += nilaiNormalisasi;
                    html += `<td>${nilaiNormalisasi.toFixed(5)}</td>`;
                });

                const rataRata = jumlahBaris / kriteria.length;
                rataRataBaris[k1] = rataRata;
                html += `<td class="table-warning"><strong>${rataRata.toFixed(5)}</strong></td>`;
                html += '</tr>';
            });

            html += '</tbody></table></div></div>';

            // Langkah 3: Hasil Bobot Prioritas
            html += `
                <div class="mb-4">
                    <h6 class="text-warning">Hasil Bobot Prioritas</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-warning">
                                <tr>
                                    <th>Kriteria</th>
                                    <th>Bobot Prioritas</th>
                                    <th>Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            let totalBobot = 0;
            for (const [kode, bobot] of Object.entries(bobotKriteria)) {
                const persentase = (parseFloat(bobot) * 100).toFixed(2);
                totalBobot += parseFloat(bobot);
                html += `
                    <tr>
                        <td><strong>${kode}</strong></td>
                        <td>${bobot}</td>
                        <td>${persentase}%</td>
                    </tr>
                `;
            }

            // Baris total
            html += `
                <tr class="table-info">
                    <td><strong>TOTAL</strong></td>
                    <td><strong>${totalBobot.toFixed(5)}</strong></td>
                    <td><strong>100.00%</strong></td>
                </tr>
            `;

            html += `
                        </tbody>
                    </table>
                </div>
            </div>
            `;

            document.getElementById('bobot-prioritas-content').innerHTML = html;
        }

        function displayUjiKonsistensi() {
            const konsistensi = ahpData.konsistensi || {};
            console.log('Konsistensi Data:', konsistensi);
            let html = `
                <h6>Hasil Uji Konsistensi</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="result-highlight">
                            <strong>Lambda Max (λmax):</strong> ${konsistensi.total_lambda_maks.toFixed(1) || 'N/A'}
                        </div>
                        <div class="result-highlight">
                            <strong>Consistency Index (CI):</strong> ${konsistensi.CI}
                        </div>
                        <div class="result-highlight">
                            <strong>Consistency Ratio (CR):</strong> ${konsistensi.CR}
                        </div>
                        <div class="result-highlight">
                            <strong>Status:</strong>
                            <span class="badge ${konsistensi.konsisten === 'Ya' ? 'bg-success' : 'bg-danger'}">
                                ${konsistensi.konsisten === 'Ya' ? 'Konsisten' : 'Tidak Konsisten'}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="formula-box">
                            <strong>Formula Uji Konsistensi:</strong><br>
                            CI = (λmax - n) / (n - 1)<br>
                            CR = CI / RI<br><br>
                            <strong>Dimana:</strong><br>
                            n = jumlah kriteria (${Object.keys(ahpData.bobot_prioritas?.bobot_prioritas || {}).length})<br>
                            RI = Random Index<br>
                            CR ≤ 0.1 = Konsisten
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('uji-konsistensi-content').innerHTML = html;
        }

        function displayNormalisasiData() {
            const hasil = ahpData.hasil_akhir || [];
            const sample = hasil.slice(0, 5); // Ambil 5 dosen pertama sebagai contoh

            let html = `
                <h6>Contoh Normalisasi Data (5 Dosen Teratas)</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>Nama Dosen</th>
                                <th>KTR1 (Norm)</th>
                                <th>KTR2 (Norm)</th>
                                <th>KTR3 (Norm)</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            sample.forEach(item => {
                const detailKriteria = item.detail_kriteria || {};
                html += `
                    <tr>
                        <td><strong>${item.dosen.nama || item.dosen.nama_dosen}</strong></td>
                        <td>${detailKriteria.KTR1?.nilai || '0.000'}</td>
                        <td>${detailKriteria.KTR2?.nilai || '0.000'}</td>
                        <td>${detailKriteria.KTR3?.nilai || '0.000'}</td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
                <div class="formula-box">
                    <strong>Proses Normalisasi:</strong><br>
                    1. Kumpulkan semua nilai mentah untuk setiap kriteria<br>
                    2. Hitung nilai maksimum untuk setiap kriteria<br>
                    3. Normalisasi: Nilai_Norm = Nilai_Mentah / Nilai_Max<br>
                    4. Hasil normalisasi berkisar 0-1
                </div>
            `;

            document.getElementById('normalisasi-data-content').innerHTML = html;
        }

        function displayPrioritasGlobal() {
            const hasil = ahpData.hasil_akhir || [];
            const bobotKriteria = ahpData.bobot_prioritas?.bobot_prioritas || {};
            const sample = hasil.slice(0, 5);

            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Ranking</th>
                                <th>Nama Dosen</th>
                                <th>Skor AHP</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            sample.forEach(item => {
                html += `
                    <tr>
                        <td><span class="badge bg-primary">#${item.ranking}</span></td>
                        <td><strong>${item.dosen.nama || item.dosen.nama_dosen}</strong></td>
                        <td>${item.prioritas_global}</td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';

            document.getElementById('prioritas-global-content').innerHTML = html;
        }

        // Navigation
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('#step-nav .nav-link');
            const stepSections = document.querySelectorAll('.step-section');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Remove active class from all links and sections
                    navLinks.forEach(l => l.classList.remove('active'));
                    stepSections.forEach(s => s.style.display = 'none');

                    // Add active class to clicked link
                    this.classList.add('active');

                    // Show corresponding section
                    const stepId = this.getAttribute('data-step');
                    document.getElementById(`step${stepId}`).style.display = 'block';
                });
            });

            // Load data
            loadAhpData();
        });
    </script>
@endsection
