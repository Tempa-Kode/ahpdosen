@extends("app")

@section("judul", "Langkah Perhitungan AHP")

@section("konten")
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Langkah-langkah Perhitungan AHP
                        </h1>
                        <p class="card-text mt-2">
                            Detail proses perhitungan Analytical Hierarchy Process untuk penelitian
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
                                <i class="fas fa-chart-bar me-1"></i>
                                5. Normalisasi Data
                            </a>
                            <a class="nav-link" data-step="6" href="#step6">
                                <i class="fas fa-trophy me-1"></i>
                                6. Skor Akhir
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step Content -->
        <div class="row">
            <div class="col-12">
                <!-- Step 1: Bobot Dasar -->
                <div class="card step-content" id="step1">
                    <div class="card-header">
                        <h5><i class="fas fa-weight me-2"></i>Langkah 1: Bobot Dasar Indikator</h5>
                    </div>
                    <div class="card-body">
                        <p>Menentukan bobot dasar untuk setiap indikator penelitian:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Kode Indikator</th>
                                        <th>Nama Indikator</th>
                                        <th>Bobot Dasar</th>
                                    </tr>
                                </thead>
                                <tbody id="bobot-dasar-table">
                                    <!-- Data akan diisi via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Matriks Perbandingan -->
                <div class="card step-content d-none" id="step2">
                    <div class="card-header">
                        <h5><i class="fas fa-table me-2"></i>Langkah 2: Matriks Perbandingan Berpasangan</h5>
                    </div>
                    <div class="card-body">
                        <p>Membuat matriks perbandingan berpasangan dengan rumus: KPTi/KPTj</p>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th>KPT01</th>
                                        <th>KPT02</th>
                                        <th>KPT03</th>
                                        <th>KPT04</th>
                                        <th>KPT05</th>
                                    </tr>
                                </thead>
                                <tbody id="matriks-perbandingan-table">
                                    <!-- Data akan diisi via JavaScript -->
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr id="jumlah-kolom-row">
                                        <!-- Data akan diisi via JavaScript -->
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Bobot Prioritas -->
                <div class="card step-content d-none" id="step3">
                    <div class="card-header">
                        <h5><i class="fas fa-balance-scale me-2"></i>Langkah 3: Menghitung Bobot Prioritas</h5>
                    </div>
                    <div class="card-body">
                        <p>Normalisasi matriks dengan membagi setiap elemen dengan total kolomnya:</p>

                        <h6>Matriks Normalisasi:</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th>KPT01</th>
                                        <th>KPT02</th>
                                        <th>KPT03</th>
                                        <th>KPT04</th>
                                        <th>KPT05</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody id="matriks-normalisasi-table">
                                    <!-- Data akan diisi via JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <h6>Bobot Prioritas (Rata-rata baris ÷ 5):</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-success">
                                    <tr>
                                        <th>Indikator</th>
                                        <th>Jumlah Baris</th>
                                        <th>Bobot Prioritas</th>
                                    </tr>
                                </thead>
                                <tbody id="bobot-prioritas-table">
                                    <!-- Data akan diisi via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Uji Konsistensi -->
                <div class="card step-content d-none" id="step4">
                    <div class="card-header">
                        <h5><i class="fas fa-check-circle me-2"></i>Langkah 4: Uji Konsistensi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Perhitungan λ maks:</h6>
                                <p class="text-muted small">Formula: Total Perbandingan × Bobot Prioritas</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-info">
                                            <tr>
                                                <th>Indikator</th>
                                                <th>Total Perbandingan × Bobot</th>
                                                <th>λ maks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lambda-maks-table">
                                            <!-- Data akan diisi via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Hasil Uji Konsistensi:</h6>
                                <div class="alert alert-info" id="konsistensi-result">
                                    <!-- Data akan diisi via JavaScript -->
                                </div>

                                <div class="mt-3">
                                    <h6>Interpretasi:</h6>
                                    <ul>
                                        <li><strong>CI (Consistency Index):</strong> Mengukur inkonsistensi</li>
                                        <li><strong>CR (Consistency Ratio):</strong> Rasio konsistensi</li>
                                        <li><strong>Status:</strong> Konsisten jika CR ≤ 0.1</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Normalisasi Data -->
                <div class="card step-content d-none" id="step5">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar me-2"></i>Langkah 5: Normalisasi Data Dosen</h5>
                    </div>
                    <div class="card-body">
                        <p>Konversi nilai asli dosen ke skala 1-5 berdasarkan range yang telah ditentukan:</p>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h6>Range Skala Interval:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white">KPT01</div>
                                            <div class="card-body small">
                                                <div>≥300: Skala 5</div>
                                                <div>200-299: Skala 4</div>
                                                <div>150-199: Skala 3</div>
                                                <div>100-149: Skala 2</div>
                                                <div>≤99: Skala 1</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-success">
                                            <div class="card-header bg-success text-white">KPT02</div>
                                            <div class="card-body small">
                                                <div>≥400: Skala 5</div>
                                                <div>300-399: Skala 4</div>
                                                <div>200-299: Skala 3</div>
                                                <div>100-199: Skala 2</div>
                                                <div>≤99: Skala 1</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning text-dark">KPT03</div>
                                            <div class="card-body small">
                                                <div>241-300: Skala 5</div>
                                                <div>178-240: Skala 4</div>
                                                <div>121-178: Skala 3</div>
                                                <div>61-120: Skala 2</div>
                                                <div>≤60: Skala 1</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dosen</th>
                                        <th>KPT01</th>
                                        <th>KPT02</th>
                                        <th>KPT03</th>
                                        <th>KPT04</th>
                                        <th>KPT05</th>
                                    </tr>
                                </thead>
                                <tbody id="normalisasi-data-table">
                                    <!-- Data akan diisi via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Skor Akhir -->
                <div class="card step-content d-none" id="step6">
                    <div class="card-header">
                        <h5><i class="fas fa-trophy me-2"></i>Langkah 6: Menghitung Skor Akhir AHP</h5>
                    </div>
                    <div class="card-body">
                        <p>Rumus: <strong>Skor AHP = Σ(Skala Normalisasi × Bobot Prioritas)</strong></p>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Ranking</th>
                                        <th>Nama Dosen</th>
                                        <th>Program Studi</th>
                                        <th>Perhitungan Detail</th>
                                        <th>Skor AHP</th>
                                    </tr>
                                </thead>
                                <tbody id="skor-akhir-table">
                                    <!-- Data akan diisi via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <button class="btn btn-secondary me-2" id="prev-btn" onclick="previousStep()" disabled>
                            <i class="fas fa-chevron-left me-1"></i>
                            Sebelumnya
                        </button>
                        <button class="btn btn-primary me-2" id="next-btn" onclick="nextStep()">
                            Selanjutnya
                            <i class="fas fa-chevron-right ms-1"></i>
                        </button>
                        <a href="/dashboard/ahp-penelitian" class="btn btn-success">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali ke Ranking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("js")
    <style>
        .step-content {
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link {
            border-radius: 0.375rem;
            margin: 0 2px;
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
        }

        .table th {
            font-size: 0.9em;
        }

        .table td {
            font-size: 0.85em;
        }

        .small-table {
            font-size: 0.8em;
        }
    </style>

    @push("scripts")
        <script>
            let currentStep = 1;
            let maxStep = 6;
            let ahpData = {};

            document.addEventListener('DOMContentLoaded', function() {
                loadAhpData();

                // Setup nav click handlers
                document.querySelectorAll('[data-step]').forEach(nav => {
                    nav.addEventListener('click', function(e) {
                        e.preventDefault();
                        const step = parseInt(this.getAttribute('data-step'));
                        goToStep(step);
                    });
                });
            });

            async function loadAhpData() {
                try {
                    const response = await fetch('/api/ahp-penelitian/');
                    const data = await response.json();
                    ahpData = data;

                    populateAllSteps(data);
                } catch (error) {
                    console.error('Error loading AHP data:', error);
                    alert('Gagal memuat data AHP.');
                }
            }

            function populateAllSteps(data) {
                populateStep1(data.langkah_perhitungan['1_bobot_dasar_indikator']);
                populateStep2(data.langkah_perhitungan['2_matriks_perbandingan_berpasangan']);
                populateStep3(data.langkah_perhitungan['3_bobot_prioritas']);
                populateStep4(data.langkah_perhitungan['4_uji_konsistensi']);
                populateStep5(data.langkah_perhitungan['5_data_normalisasi_dosen']);
                populateStep6(data.hasil_ranking);
            }

            function populateStep1(bobotDasar) {
                const indikatorNames = {
                    'KPT01': 'Publikasi Terakreditasi Nasional & Internasional',
                    'KPT02': 'Presentasi dalam seminar nasional dan internasional',
                    'KPT03': 'Buku dari hasil penelitian',
                    'KPT04': 'HaKI',
                    'KPT05': 'Karya Ilmiah atau seni yang dipamerkan'
                };

                const tbody = document.getElementById('bobot-dasar-table');
                tbody.innerHTML = Object.entries(bobotDasar).map(([kode, bobot]) => `
        <tr>
            <td><strong>${kode}</strong></td>
            <td>${indikatorNames[kode]}</td>
            <td class="text-center"><span class="badge bg-primary">${bobot}</span></td>
        </tr>
    `).join('');
            }

            function populateStep2(matriksData) {
                const matriks = matriksData.matriks;
                const jumlahKolom = matriksData.jumlah_kolom;

                const tbody = document.getElementById('matriks-perbandingan-table');
                const indikatorKode = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'];

                tbody.innerHTML = indikatorKode.map(i => `
        <tr>
            <td><strong>${i}</strong></td>
            ${indikatorKode.map(j => `<td>${matriks[i][j]}</td>`).join('')}
        </tr>
    `).join('');

                document.getElementById('jumlah-kolom-row').innerHTML = `
        <td><strong>Jumlah</strong></td>
        ${indikatorKode.map(kode => `<td><strong>${jumlahKolom[kode]}</strong></td>`).join('')}
    `;
            }

            function populateStep3(bobotPrioritas) {
                const matriksNormalisasi = bobotPrioritas.matriks_normalisasi;
                const jumlahBaris = bobotPrioritas.jumlah_baris;
                const bobot = bobotPrioritas.bobot_prioritas;

                const indikatorKode = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'];

                // Matriks Normalisasi
                const tbody1 = document.getElementById('matriks-normalisasi-table');
                tbody1.innerHTML = indikatorKode.map(i => `
        <tr>
            <td><strong>${i}</strong></td>
            ${indikatorKode.map(j => `<td>${matriksNormalisasi[i][j]}</td>`).join('')}
            <td><strong>${jumlahBaris[i]}</strong></td>
        </tr>
    `).join('');

                // Bobot Prioritas
                const tbody2 = document.getElementById('bobot-prioritas-table');
                tbody2.innerHTML = indikatorKode.map(kode => `
        <tr>
            <td><strong>${kode}</strong></td>
            <td>${jumlahBaris[kode]}</td>
            <td><span class="badge bg-success">${bobot[kode]}</span></td>
        </tr>
    `).join('');
            }

            function populateStep4(konsistensi) {
                // Lambda maks table dengan detail yang lebih jelas
                const tbody = document.getElementById('lambda-maks-table');
                const totalPerbandingan = konsistensi.total_perbandingan_per_kolom;
                const bobotPrioritas = konsistensi.bobot_prioritas;

                tbody.innerHTML = Object.entries(konsistensi.lambda_maks_detail).map(([kode, lambda]) => `
        <tr>
            <td><strong>${kode}</strong></td>
            <td>${totalPerbandingan[kode]} × ${bobotPrioritas[kode]}</td>
            <td><span class="badge bg-info">${lambda}</span></td>
        </tr>
    `).join('') + `
        <tr class="table-warning">
            <td colspan="2"><strong>Total λ maks</strong></td>
            <td><strong>${konsistensi.total_lambda_maks}</strong></td>
        </tr>
    `;

                // Konsistensi result dengan penjelasan lengkap
                const alertClass = konsistensi.status_konsistensi === 'Konsisten' ? 'alert-success' : 'alert-danger';
                document.getElementById('konsistensi-result').className = `alert ${alertClass}`;

                // Tampilkan CI dan CR langsung tanpa format tambahan
                const ciDisplay = konsistensi.CI || konsistensi.CI_raw || 0;
                const crDisplay = konsistensi.CR || konsistensi.CR_raw || 0;

                document.getElementById('konsistensi-result').innerHTML = `
        <h6>Hasil Perhitungan:</h6>
        <div class="row">
            <div class="col-md-12">
                <p><strong>Total λ maks:</strong> ${konsistensi.total_lambda_maks}</p>
                <p><strong>Jumlah Kriteria (n):</strong> 5</p>
                <hr>
                <p><strong>CI (Consistency Index):</strong> <strong>${ciDisplay}</strong></p>
                <small class="text-muted">Rumus: (λ maks - n) / (n - 1) = (${konsistensi.total_lambda_maks} - 5) / (5 - 1)</small>

                <p class="mt-2"><strong>RI (Random Index):</strong> ${konsistensi.RI}</p>
                <small class="text-muted">Nilai tetap untuk matriks 5×5</small>

                <p class="mt-2"><strong>CR (Consistency Ratio):</strong> <strong class="text-primary">${crDisplay}</strong></p>
                <small class="text-muted">Rumus: CI / RI = ${ciDisplay} / ${konsistensi.RI}</small>

                <hr>
                <h6 class="mb-0">Status: <span class="badge ${konsistensi.status_konsistensi === 'Konsisten' ? 'bg-success' : 'bg-danger'}">${konsistensi.status_konsistensi}</span></h6>
                <small class="text-muted d-block mt-1">Konsisten jika CR ≤ 0.1, saat ini CR = ${crDisplay}</small>
            </div>
        </div>
    `;
            }

            function populateStep5(dataNormalisasi) {
                const tbody = document.getElementById('normalisasi-data-table');
                tbody.innerHTML = dataNormalisasi.slice(0, 10).map((data, index) => `
        <tr>
            <td>${index + 1}</td>
            <td><strong>${data.dosen.nama}</strong></td>
            <td>
                <small>${data.nilai_normalisasi.KPT01?.total_nilai_indikator || 0}</small><br>
                <span class="badge bg-primary">${data.nilai_normalisasi.KPT01?.skala_normalisasi || 0}</span>
            </td>
            <td>
                <small>${data.nilai_normalisasi.KPT02?.total_nilai_indikator || 0}</small><br>
                <span class="badge bg-success">${data.nilai_normalisasi.KPT02?.skala_normalisasi || 0}</span>
            </td>
            <td>
                <small>${data.nilai_normalisasi.KPT03?.total_nilai_indikator || 0}</small><br>
                <span class="badge bg-warning">${data.nilai_normalisasi.KPT03?.skala_normalisasi || 0}</span>
            </td>
            <td>
                <small>${data.nilai_normalisasi.KPT04?.total_nilai_indikator || 0}</small><br>
                <span class="badge bg-info">${data.nilai_normalisasi.KPT04?.skala_normalisasi || 0}</span>
            </td>
            <td>
                <small>${data.nilai_normalisasi.KPT05?.total_nilai_indikator || 0}</small><br>
                <span class="badge bg-secondary">${data.nilai_normalisasi.KPT05?.skala_normalisasi || 0}</span>
            </td>
        </tr>
    `).join('');
            }

            function populateStep6(ranking) {
                const tbody = document.getElementById('skor-akhir-table');
                const bobotPrioritas = ahpData.langkah_perhitungan['3_bobot_prioritas'].bobot_prioritas;

                tbody.innerHTML = ranking.slice(0, 10).map(item => {
                    const detailCalculation = Object.entries(item.detail_skor)
                        .map(([kode, detail]) =>
                            `${detail.skala_normalisasi} × ${bobotPrioritas[kode]} = ${detail.skor}`)
                        .join('<br>');

                    return `
            <tr>
                <td>
                    <span class="badge ${item.ranking <= 3 ? 'bg-warning' : 'bg-secondary'} fs-6">
                        ${item.ranking}
                    </span>
                </td>
                <td><strong>${item.dosen.nama}</strong></td>
                <td><span class="badge bg-info">${item.dosen.prodi}</span></td>
                <td><small>${detailCalculation}</small></td>
                <td>
                    <span class="badge bg-success fs-6">${item.skor_total_ahp}</span>
                </td>
            </tr>
        `;
                }).join('');
            }

            function goToStep(step) {
                if (step < 1 || step > maxStep) return;

                // Hide current step
                document.getElementById(`step${currentStep}`).classList.add('d-none');

                // Show new step
                document.getElementById(`step${step}`).classList.remove('d-none');

                // Update nav
                document.querySelector(`[data-step="${currentStep}"]`).classList.remove('active');
                document.querySelector(`[data-step="${step}"]`).classList.add('active');

                currentStep = step;
                updateButtons();
            }

            function nextStep() {
                if (currentStep < maxStep) {
                    goToStep(currentStep + 1);
                }
            }

            function previousStep() {
                if (currentStep > 1) {
                    goToStep(currentStep - 1);
                }
            }

            function updateButtons() {
                document.getElementById('prev-btn').disabled = currentStep === 1;
                document.getElementById('next-btn').disabled = currentStep === maxStep;

                if (currentStep === maxStep) {
                    document.getElementById('next-btn').innerHTML = '<i class="fas fa-check me-1"></i>Selesai';
                } else {
                    document.getElementById('next-btn').innerHTML = 'Selanjutnya <i class="fas fa-chevron-right ms-1"></i>';
                }
            }
        </script>
    @endsection
