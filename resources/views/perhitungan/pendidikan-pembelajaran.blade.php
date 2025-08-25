@extends("app")

@section("judul", "Hasil Perhitungan Pendidikan dan Pembelajaran")

@section("konten")
    <style>
        .badge-outline-primary {
            background-color: transparent;
            border: 1px solid #0d6efd;
            color: #0d6efd;
        }

        .badge-outline-info {
            background-color: transparent;
            border: 1px solid #0dcaf0;
            color: #0dcaf0;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .ranking-badge {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .percentage-badge {
            font-size: 1rem;
            font-weight: bold;
        }

        .modal-body .card {
            border: 1px solid #e9ecef;
            margin-bottom: 1rem;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Hasil Perhitungan AHP - Pendidikan dan Pembelajaran</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0" onclick="refreshData()">
                <i class="btn-icon-prepend" data-feather="refresh-cw"></i>
                Refresh Data
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Statistik Cards -->
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card card-hover">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Dosen</h6>
                                <div class="dropdown mb-2">
                                    <i class="icon-lg text-primary" data-feather="users"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2 text-primary" id="totalDosen">-</h3>
                                    <div class="d-flex align-items-baseline">
                                        <p class="text-success mb-0">
                                            <span>Dosen</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card card-hover">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Rata-rata</h6>
                                <div class="dropdown mb-2">
                                    <i class="icon-lg text-info" data-feather="bar-chart-2"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2 text-info" id="rataRata">-</h3>
                                    <div class="d-flex align-items-baseline">
                                        <p class="text-success mb-0">
                                            <span>Persentase</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card card-hover">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Tertinggi</h6>
                                <div class="dropdown mb-2">
                                    <i class="icon-lg text-success" data-feather="trending-up"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2 text-success" id="tertinggi">-</h3>
                                    <div class="d-flex align-items-baseline">
                                        <p class="text-success mb-0">
                                            <span>Persentase</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card card-hover">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Terendah</h6>
                                <div class="dropdown mb-2">
                                    <i class="icon-lg text-warning" data-feather="trending-down"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2 text-warning" id="terendah">-</h3>
                                    <div class="d-flex align-items-baseline">
                                        <p class="text-danger mb-0">
                                            <span>Persentase</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Skala Interval Reference -->
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card" id="skalaReferensiCard">
                <div class="card-body">
                    <h6 class="card-title">
                        <i data-feather="layers" class="icon-sm me-2"></i>
                        Referensi Skala Interval
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Range</th>
                                    <th>Kategori</th>
                                    <th>Nilai Decimal</th>
                                    <th>Jumlah Dosen</th>
                                </tr>
                            </thead>
                            <tbody id="skalaReferensi">
                                <!-- Data akan diisi dengan JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tabel Hasil Perhitungan -->
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Hasil Perhitungan AHP - Pendidikan dan Pembelajaran</h6>
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ranking</th>
                                    <th>Nama Dosen</th>
                                    <th>NIDN</th>
                                    <th>Program Studi</th>
                                    <th>Persentase</th>
                                    <th>Nilai Decimal</th>
                                    <th>Kategori</th>
                                    <th>Total Responden</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data akan diisi dengan JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalContent">
                        <!-- Content akan diisi dengan JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("js")
    <script>
        let dataHasil = [];

        function loadData() {
            // Tampilkan loading
            showLoading();

            fetch('{{ route("perhitungan.pendidikan-dan-pembelajaran") }}')
                .then(response => response.json())
                .then(data => {
                    console.log('Data loaded:', data);
                    dataHasil = data;
                    renderStatistik(data.statistik_skala_interval);

                    // Prioritaskan skala interval dinamis jika tersedia
                    const skalaInterval = data.skala_interval_dinamis || data.skala_interval_referensi;
                    const jenisSkala = data.skala_interval_dinamis ? 'Dinamis (Berdasarkan Data)' :
                    'Statis (Referensi)';
                    renderSkalaReferensi(skalaInterval, data.statistik_skala_interval, jenisSkala);

                    renderTable(data.hasil_perhitungan);
                    hideLoading();
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoading();
                    alert('Terjadi kesalahan saat memuat data');
                });
        }

        function showLoading() {
            document.getElementById('tableBody').innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="loading-spinner"></div>
                    <span class="ms-2">Memuat data perhitungan...</span>
                </td>
            </tr>
        `;

            // Reset statistik
            document.getElementById('totalDosen').innerHTML = '<div class="loading-spinner"></div>';
            document.getElementById('rataRata').innerHTML = '<div class="loading-spinner"></div>';
            document.getElementById('tertinggi').innerHTML = '<div class="loading-spinner"></div>';
            document.getElementById('terendah').innerHTML = '<div class="loading-spinner"></div>';
        }

        function hideLoading() {
            // Loading sudah dihilangkan saat data dirender
        }

        function renderStatistik(statistik) {
            document.getElementById('totalDosen').textContent = statistik.total_dosen;
            document.getElementById('rataRata').textContent = statistik.rata_rata_persentase + '%';

            if (statistik.ringkasan.terbaik) {
                document.getElementById('tertinggi').textContent = statistik.ringkasan.terbaik.persentase + '%';
            }

            if (statistik.ringkasan.terlemah) {
                document.getElementById('terendah').textContent = statistik.ringkasan.terlemah.persentase + '%';
            }
        }

        function renderSkalaReferensi(referensi, statistik, jenisSkala = 'Statis (Referensi)') {
            const tbody = document.getElementById('skalaReferensi');
            let html = '';

            // Update label jenis skala
            const cardTitle = document.querySelector('#skalaReferensiCard .card-title');
            if (cardTitle) {
                cardTitle.innerHTML = `
                    <i data-feather="layers" class="icon-sm me-2"></i>
                    Referensi Skala Interval
                    <small class="text-muted ms-2">(${jenisSkala})</small>
                `;
                feather.replace();
            }

            referensi.forEach(item => {
                const key = item.variabel.toLowerCase().replace(' ', '_');
                const jumlahDosen = statistik.distribusi_skala[key]?.jumlah_dosen || 0;
                const badgeClass = getBadgeClass(item.variabel);

                html += `
                <tr>
                    <td>${item.range}</td>
                    <td>${item.variabel}</td>
                    <td>${item.nilai_decimal.toFixed(5)}</td>
                    <td>${jumlahDosen} dosen</td>
                </tr>
            `;
            });

            tbody.innerHTML = html;
        }

        function renderTable(hasil) {
            const tbody = document.getElementById('tableBody');
            let html = '';

            hasil.forEach((item, index) => {
                const badgeClass = getBadgeClass(item.skala_interval.variabel);

                html += `
                <tr>
                    <td><span class="badge badge-outline-primary ranking-badge">#${index + 1}</span></td>
                    <td>
                        <div class="fw-bold">${item.dosen_nama}</div>
                        <small class="text-muted">${item.dosen_prodi}</small>
                    </td>
                    <td><code>${item.dosen_nidn}</code></td>
                    <td>${item.dosen_prodi}</td>
                    <td><span class="badge badge-outline-info percentage-badge">${item.persentase}%</span></td>
                    <td><strong class="text-primary">${item.skala_interval.nilai_decimal.toFixed(5)}</strong></td>
                    <td>${item.skala_interval.variabel}</td>
                    <td>
                        ${item.total_responden}
                        <small class="text-muted d-block">responden</small>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="showDetail(${item.dosen_id})" title="Lihat Detail">
                            <i data-feather="eye"></i>
                        </button>
                    </td>
                </tr>
            `;
            });

            tbody.innerHTML = html;

            // Re-initialize feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }

        function getBadgeClass(variabel) {
            switch (variabel.toLowerCase()) {
                case 'sangat tinggi':
                    return 'badge-success';
                case 'tinggi':
                    return 'badge-primary';
                case 'sedang':
                    return 'badge-warning';
                case 'rendah':
                    return 'badge-danger';
                case 'sangat rendah':
                    return 'badge-dark';
                default:
                    return 'badge-secondary';
            }
        }

        function showDetail(dosenId) {
            const dosen = dataHasil.hasil_perhitungan.find(item => item.dosen_id == dosenId);
            if (!dosen) return;

            let html = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informasi Dosen</h6>
                    <table class="table table-borderless">
                        <tr><td>Nama</td><td>: ${dosen.dosen_nama}</td></tr>
                        <tr><td>NIDN</td><td>: ${dosen.dosen_nidn}</td></tr>
                        <tr><td>Program Studi</td><td>: ${dosen.dosen_prodi}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Hasil Perhitungan</h6>
                    <table class="table table-borderless">
                        <tr><td>Skor Aktual</td><td>: ${dosen.skor_actual}</td></tr>
                        <tr><td>Skor Minimum</td><td>: ${dosen.skor_min}</td></tr>
                        <tr><td>Skor Maksimum</td><td>: ${dosen.skor_max}</td></tr>
                        <tr><td>Persentase</td><td>: ${dosen.persentase}%</td></tr>
                        <tr><td>Kategori</td><td>: ${dosen.skala_interval.variabel}</td></tr>
                    </table>
                </div>
            </div>

            <h6>Ringkasan Kategori Penilaian</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="text-success">${dosen.ringkasan_kategori.sangat_baik}</h4>
                            <p class="mb-0">Sangat Baik</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="text-primary">${dosen.ringkasan_kategori.baik}</h4>
                            <p class="mb-0">Baik</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="text-warning">${dosen.ringkasan_kategori.cukup_baik}</h4>
                            <p class="mb-0">Cukup Baik</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="text-danger">${dosen.ringkasan_kategori.kurang_baik}</h4>
                            <p class="mb-0">Kurang Baik</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

            document.getElementById('modalContent').innerHTML = html;
            document.getElementById('detailModalLabel').textContent = `Detail Penilaian - ${dosen.dosen_nama}`;

            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }

        function refreshData() {
            loadData();
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });
    </script>
@endsection
