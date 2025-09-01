@extends("app")

@section("title", "Perhitungan AHP Tridarma")

@section("konten")
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Perhitungan AHP Tridarma</h4>
                            <p class="card-description">
                                Analytic Hierarchy Process (AHP) untuk semua kriteria Tridarma Perguruan Tinggi
                            </p>

                            <!-- Loading spinner -->
                            <div id="loading" class="text-center my-4">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Memuat data perhitungan AHP...</p>
                            </div>

                            <!-- Error message -->
                            <div id="error-message" class="alert alert-danger" style="display: none;">
                                <h6>Terjadi kesalahan!</h6>
                                <span id="error-text"></span>
                            </div>

                            <!-- Success content -->
                            <div id="content" style="display: none;">
                                <!-- Informasi Konsistensi -->
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Uji Konsistensi Matriks</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Lambda Maks:</strong>
                                                <span id="lambda-maks" class="text-info"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>CI:</strong>
                                                <span id="ci" class="text-info"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>CR:</strong>
                                                <span id="cr" class="text-info"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Status:</strong>
                                                <span id="konsisten" class="badge"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bobot Prioritas Kriteria -->
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Bobot Prioritas Kriteria</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Kode</th>
                                                        <th>Nama Kriteria</th>
                                                        <th>Bobot Prioritas</th>
                                                        <th>Persentase</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="bobot-kriteria">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter dan Search -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="filterKategori">Filter Kategori:</label>
                                                <select id="filterKategori" class="form-control">
                                                    <option value="">Semua Kategori</option>
                                                    <option value="Sangat Baik">Sangat Baik</option>
                                                    <option value="Baik">Baik</option>
                                                    <option value="Cukup">Cukup</option>
                                                    <option value="Kurang">Kurang</option>
                                                    <option value="Sangat Kurang">Sangat Kurang</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="searchDosen">Cari Dosen:</label>
                                                <input type="text" id="searchDosen" class="form-control"
                                                    placeholder="Masukkan nama atau NIDN dosen...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hasil Ranking Dosen -->
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">Hasil Ranking Dosen</h5>
                                        <small>Total Dosen: <span id="total-dosen"></span></small>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="table-hasil">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Ranking</th>
                                                        <th>NIDN</th>
                                                        <th>Nama Dosen</th>
                                                        <th>Program Studi</th>
                                                        <th>Prioritas Global</th>
                                                        <th>Persentase</th>
                                                        <th>Kategori</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="hasil-ranking">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Dosen -->
    <div class="modal fade" id="modalDetailDosen" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Perhitungan Dosen</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="resetModalState()">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body-detail">
                    <!-- Loading state -->
                    <div id="modal-loading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 mb-0">Memuat detail perhitungan...</p>
                    </div>

                    <!-- Error state -->
                    <div id="modal-error" class="alert alert-danger" style="display: none;">
                        <h6>Terjadi kesalahan!</h6>
                        <span id="modal-error-text"></span>
                    </div>

                    <!-- Content will be loaded here -->
                    <div id="modal-content">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("css")
    <style>
        /* Button loading animation */
        .btn-loading {
            transition: all 0.3s ease;
        }

        .btn .spinner-border-sm {
            width: 0.875rem;
            height: 0.875rem;
        }

        /* Modal loading animation */
        #modal-loading {
            animation: fadeIn 0.3s ease-in-out;
        }

        #modal-content {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Pulse animation for loading spinner */
        .spinner-border {
            animation: spinner-border 0.75s linear infinite, pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
            }
        }

        /* Button hover effect enhancement */
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
            transition: all 0.2s ease;
        }

        .btn-primary:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>
@endsection

@section("js")
    <script>
        console.log('Script block loaded');

        let originalData = [];

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Starting AHP Tridarma initialization');
            console.log('Current URL:', window.location.href);
            console.log('Document ready state:', document.readyState);

            // Test if elements exist
            console.log('Loading element exists:', !!document.getElementById('loading'));
            console.log('Content element exists:', !!document.getElementById('content'));
            console.log('Error message element exists:', !!document.getElementById('error-message'));

            // Setup modal close event
            $('#modalDetailDosen').on('hidden.bs.modal', function() {
                resetModalState();
            });

            loadAhpData();
        });

        // Also add jQuery ready as fallback
        $(document).ready(function() {
            console.log('jQuery document ready - backup initialization');
            // Only try if DOM ready hasn't worked
            setTimeout(function() {
                if (!originalData.length) {
                    console.log('jQuery fallback - retrying load...');
                    loadAhpData();
                }
            }, 500);
        });

        function loadAhpData() {
            console.log('Starting to load AHP data...');

            fetch('/api/ahp-tridarma')
                .then(response => {
                    console.log('Response received:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.status === 'success') {
                        console.log('Success! Data length:', data.data.hasil_akhir.length);
                        originalData = data.data.hasil_akhir;
                        displayData(data);
                        setupFilters();
                    } else {
                        console.error('API returned error:', data.message);
                        showError('Gagal memuat data: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showError('Terjadi kesalahan saat memuat data: ' + error.message);
                })
                .finally(() => {
                    const loadingElement = document.getElementById('loading');
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                });
        }

        function displayData(data) {
            console.log('displayData called with:', data);

            // Show content
            const contentElement = document.getElementById('content');
            if (contentElement) {
                contentElement.style.display = 'block';
            }

            // Display konsistensi
            const konsistensi = data.data.konsistensi;
            console.log('Konsistensi data:', konsistensi);

            const lambdaMaks = document.getElementById('lambda-maks');
            const ci = document.getElementById('ci');
            const cr = document.getElementById('cr');
            const konsisten = document.getElementById('konsisten');

            if (lambdaMaks) lambdaMaks.textContent = konsistensi.total_lambda_maks;
            if (ci) ci.textContent = konsistensi.CI;
            if (cr) cr.textContent = konsistensi.CR;
            if (konsisten) {
                konsisten.textContent = konsistensi.konsisten;
                konsisten.className = konsistensi.konsisten === 'Ya' ? 'badge badge-success' : 'badge badge-danger';
            }

            // Display bobot kriteria
            const bobotTbody = document.getElementById('bobot-kriteria');
            if (bobotTbody) {
                const bobotKriteria = data.data.bobot_prioritas.bobot_prioritas;
                const kriteriaNama = data.data.metadata.kriteria;

                console.log('Bobot kriteria:', bobotKriteria);
                console.log('Kriteria nama:', kriteriaNama);

                bobotTbody.innerHTML = '';
                for (const [kode, bobot] of Object.entries(bobotKriteria)) {
                    const persentase = (bobot * 100).toFixed(2);
                    bobotTbody.innerHTML += `
                    <tr>
                        <td><strong>${kode}</strong></td>
                        <td>${kriteriaNama[kode]}</td>
                        <td>${bobot}</td>
                        <td>${persentase}%</td>
                    </tr>
                `;
                }
            }

            // Display hasil ranking
            const totalDosen = document.getElementById('total-dosen');
            if (totalDosen) {
                totalDosen.textContent = data.data.jumlah_dosen;
            }

            console.log('Displaying ranking for', data.data.hasil_akhir.length, 'dosen');
            displayRanking(data.data.hasil_akhir);
        }

        function displayRanking(hasil) {
            console.log('displayRanking called with', hasil.length, 'items');
            const tbody = document.getElementById('hasil-ranking');

            if (!tbody) {
                console.error('Table body not found');
                return;
            }

            tbody.innerHTML = '';

            hasil.forEach((item, index) => {
                console.log('Processing item', index, ':', item);
                const badgeClass = getBadgeClass(item.kategori_nilai.kategori);
                tbody.innerHTML += `
            <tr>
                <td><strong>#${item.ranking}</strong></td>
                <td>${item.dosen.nidn || 'N/A'}</td>
                <td>${item.dosen.nama || item.dosen.nama_dosen || 'N/A'}</td>
                <td>${item.dosen.prodi || 'N/A'}</td>
                <td>${item.prioritas_global}</td>
                <td>${item.persentase}%</td>
                <td><span class="badge ${badgeClass}">${item.kategori_nilai.kategori}</span></td>
                <td>
                    <button id="btn-detail-${item.dosen.id}" class="btn btn-sm btn-primary" onclick="showDetailDosen(${item.dosen.id})">
                        <span class="btn-text">Detail</span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Loading...
                        </span>
                    </button>
                </td>
            </tr>
        `;
            });

            console.log('Table updated with', tbody.children.length, 'rows');
        }

        function getBadgeClass(kategori) {
            switch (kategori) {
                case 'Sangat Baik':
                    return 'badge-success';
                case 'Baik':
                    return 'badge-info';
                case 'Cukup':
                    return 'badge-warning';
                case 'Kurang':
                    return 'badge-danger';
                case 'Sangat Kurang':
                    return 'badge-dark';
                default:
                    return 'badge-secondary';
            }
        }

        function setupFilters() {
            // Filter kategori
            const filterKategori = document.getElementById('filterKategori');
            if (filterKategori) {
                filterKategori.addEventListener('change', applyFilters);
            }

            // Search dosen
            const searchDosen = document.getElementById('searchDosen');
            if (searchDosen) {
                searchDosen.addEventListener('input', applyFilters);
            }
        }

        function applyFilters() {
            const filterKategori = document.getElementById('filterKategori');
            const searchDosen = document.getElementById('searchDosen');

            const kategoriFilter = filterKategori ? filterKategori.value : '';
            const searchTerm = searchDosen ? searchDosen.value.toLowerCase() : '';

            let filteredData = originalData;

            // Filter by kategori
            if (kategoriFilter) {
                filteredData = filteredData.filter(item =>
                    item.kategori_nilai.kategori === kategoriFilter
                );
            }

            // Filter by search term
            if (searchTerm) {
                filteredData = filteredData.filter(item => {
                    const nama = item.dosen.nama || item.dosen.nama_dosen || '';
                    const nidn = item.dosen.nidn || '';
                    const prodi = item.dosen.prodi || '';

                    return nama.toLowerCase().includes(searchTerm) ||
                        nidn.toLowerCase().includes(searchTerm) ||
                        prodi.toLowerCase().includes(searchTerm);
                });
            }

            displayRanking(filteredData);
        }

        function showDetailDosen(dosenId) {
            console.log('Loading detail for dosen ID:', dosenId);

            // Show button loading state
            const button = document.getElementById(`btn-detail-${dosenId}`);
            if (button) {
                button.disabled = true;
                button.querySelector('.btn-text').classList.add('d-none');
                button.querySelector('.btn-loading').classList.remove('d-none');
            }

            // Show modal loading state
            showModalLoading();
            $('#modalDetailDosen').modal('show');

            fetch(`/api/ahp-tridarma/dosen/${dosenId}`)
                .then(response => {
                    console.log('Detail response received:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Detail data received:', data);
                    if (data.status === 'success') {
                        displayDetailModal(data.data);
                    } else {
                        showModalError('Gagal memuat detail dosen: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Detail fetch error:', error);
                    showModalError('Terjadi kesalahan saat memuat detail dosen: ' + error.message);
                })
                .finally(() => {
                    // Reset button state
                    if (button) {
                        button.disabled = false;
                        button.querySelector('.btn-text').classList.remove('d-none');
                        button.querySelector('.btn-loading').classList.add('d-none');
                    }
                });
        }

        function resetModalState() {
            document.getElementById('modal-loading').style.display = 'none';
            document.getElementById('modal-error').style.display = 'none';
            document.getElementById('modal-content').style.display = 'none';
            document.getElementById('modal-content').innerHTML = '';
        }

        function showModalLoading() {
            document.getElementById('modal-loading').style.display = 'block';
            document.getElementById('modal-error').style.display = 'none';
            document.getElementById('modal-content').style.display = 'none';
        }

        function showModalError(message) {
            document.getElementById('modal-loading').style.display = 'none';
            document.getElementById('modal-error').style.display = 'block';
            document.getElementById('modal-content').style.display = 'none';
            document.getElementById('modal-error-text').textContent = message;
        }

        function showModalContent() {
            document.getElementById('modal-loading').style.display = 'none';
            document.getElementById('modal-error').style.display = 'none';
            document.getElementById('modal-content').style.display = 'block';
        }

        function displayDetailModal(data) {
            console.log('Displaying detail modal for:', data);

            const modalContent = document.getElementById('modal-content');

            let detailKriteriaHtml = '';
            for (const [kode, detail] of Object.entries(data.detail_kriteria)) {
                detailKriteriaHtml += `
            <tr>
                <td><strong>${kode}</strong></td>
                <td>${detail.nilai}</td>
                <td>${detail.bobot}</td>
                <td>${detail.kontribusi}</td>
            </tr>
        `;
            }

            modalContent.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Informasi Dosen</h6>
                <table class="table table-sm">
                    <tr><td>NIDN</td><td>${data.dosen.nidn}</td></tr>
                    <tr><td>Nama</td><td>${data.dosen.nama || data.dosen.nama_dosen}</td></tr>
                    <tr><td>Program Studi</td><td>${data.dosen.prodi}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Hasil Perhitungan</h6>
                <table class="table table-sm">
                    <tr><td>Ranking</td><td><strong>#${data.ranking}</strong></td></tr>
                    <tr><td>Prioritas Global</td><td>${data.prioritas_global}</td></tr>
                    <tr><td>Persentase</td><td>${data.persentase}%</td></tr>
                    <tr><td>Kategori</td><td><span class="badge ${getBadgeClass(data.kategori_nilai.kategori)}">${data.kategori_nilai.kategori}</span></td></tr>
                </table>
            </div>
        </div>

        <h6 class="mt-4">Detail Kontribusi Kriteria</h6>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Nilai</th>
                        <th>Bobot</th>
                        <th>Kontribusi</th>
                    </tr>
                </thead>
                <tbody>
                    ${detailKriteriaHtml}
                </tbody>
            </table>
        </div>
    `;

            // Show the content and hide loading
            showModalContent();
        }

        function showError(message) {
            const errorText = document.getElementById('error-text');
            const errorMessage = document.getElementById('error-message');

            if (errorText && errorMessage) {
                errorText.textContent = message;
                errorMessage.style.display = 'block';
            } else {
                console.error('Error elements not found, showing alert instead:', message);
                alert(message);
            }
        }
    </script>
@endsection
