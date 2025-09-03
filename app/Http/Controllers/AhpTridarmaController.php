<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Dosen;

class AhpTridarmaController extends Controller
{
    // Bobot dasar untuk masing-masing kriteria Tridarma
    private $bobotDasar = [
        'K001' => 0.35, // Pendidikan dan Pembelajaran
        'K002' => 0.45, // Penelitian
        'K003' => 0.1, // PKM
        'K004' => 0.1 // Penunjang
    ];

    // Random Index untuk menghitung CR
    private $randomIndex = [
        1 => 0.00,
        2 => 0.00,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49
    ];

    /**
     * Perhitungan AHP lengkap untuk semua kriteria Tridarma
     */
    public function perhitunganAhpTridarma()
    {
        // 1. Ambil data nilai dari semua kriteria
        $dataKriteria = $this->ambilDataSemuaKriteria();

        // 2. Siapkan matriks perbandingan berpasangan
        $kriteriaKode = ['K001', 'K002', 'K003', 'K004'];
        $matriks = $this->buatMatriksPerbandingan($kriteriaKode);

        // 3. Hitung bobot prioritas
        $bobotPrioritas = $this->hitungBobotPrioritas($matriks, $kriteriaKode);

        // 4. Hitung konsistensi
        $konsistensi = $this->hitungKonsistensi($matriks, $bobotPrioritas, count($kriteriaKode));

        // 5. Hitung prioritas global untuk setiap dosen
        $prioritasGlobal = $this->hitungPrioritasGlobal($dataKriteria, $bobotPrioritas['bobot_prioritas']);

        // 6. Urutkan dan tambahkan ranking
        usort($prioritasGlobal, function($a, $b) {
            return $b['prioritas_global'] <=> $a['prioritas_global'];
        });

        foreach ($prioritasGlobal as $index => &$data) {
            $data['ranking'] = $index + 1;
        }

        // 7. Hitung persentase menggunakan min-max scaling
        $prioritasGlobal = $this->hitungPersentaseMinMax($prioritasGlobal);

        // 8. Tambahkan kategori nilai decimal
        $prioritasGlobal = $this->tambahkanKategoriNilaiDecimal($prioritasGlobal);

        return response()->json([
            'status' => 'success',
            'message' => 'Perhitungan AHP Tridarma berhasil',
            'data' => [
                'data_kriteria' => $dataKriteria,
                'matriks_perbandingan' => $matriks,
                'bobot_prioritas' => $bobotPrioritas,
                'konsistensi' => $konsistensi,
                'hasil_akhir' => $prioritasGlobal,
                'jumlah_dosen' => count($prioritasGlobal),
                'metadata' => [
                    'metode' => 'AHP (Analytical Hierarchy Process)',
                    'kriteria' => [
                        'K001' => 'Pendidikan dan Pembelajaran',
                        'K002' => 'Penelitian',
                        'K003' => 'PKM (Pengabdian Kepada Masyarakat)',
                        'K004' => 'Penunjang'
                    ],
                    'bobot_dasar' => $this->bobotDasar
                ]
            ]
        ]);
    }

    /**
     * Ambil data dari semua kriteria
     */
    private function ambilDataSemuaKriteria()
    {
        // Ambil semua dosen dari database sebagai referensi
        $semuaDosen = \App\Models\Dosen::all()->keyBy('id');

        // K001 - Pendidikan dan Pembelajaran
        $controllerK001 = new \App\Http\Controllers\PerhitunganController();
        $hasilK001 = $controllerK001->pendidikanDanPembelajaran();
        $dataK001 = json_decode($hasilK001->getContent(), true);

        // K002 - Penelitian
        $controllerK002 = new \App\Http\Controllers\AhpPenelitianController();
        $hasilK002 = $controllerK002->perhitunganAhpPenelitian();
        $dataK002 = json_decode($hasilK002->getContent(), true);

        // K003 - PKM
        $controllerK003 = new \App\Http\Controllers\PerhitunganPKMController();
        $hasilK003 = $controllerK003->penilaianK003(new Request());
        $dataK003 = json_decode($hasilK003->getContent(), true);

        // K004 - Penunjang/Tridarma
        $controllerK004 = new \App\Http\Controllers\PerhitunganTridarmaController();
        $hasilK004 = $controllerK004->penilaianK004(new Request());
        $dataK004 = json_decode($hasilK004->getContent(), true);

        // Inisialisasi data gabungan dengan semua dosen
        $gabunganDosen = [];
        foreach ($semuaDosen as $dosen) {
            $gabunganDosen[$dosen->id] = [
                'dosen' => [
                    'id' => $dosen->id,
                    'nidn' => $dosen->nidn,
                    'nama' => $dosen->nama_dosen,
                    'nama_dosen' => $dosen->nama_dosen,
                    'prodi' => $dosen->prodi
                ],
                'K001' => 1.0,
                'K002' => 1.0,
                'K003' => 1.0,
                'K004' => 1.0
            ];
        }

        // Proses data K001 - Pendidikan dan Pembelajaran
        if (isset($dataK001['hasil_perhitungan'])) {
            foreach ($dataK001['hasil_perhitungan'] as $item) {
                $dosenId = $item['dosen_id'] ?? null;
                if ($dosenId && isset($gabunganDosen[$dosenId])) {
                    $gabunganDosen[$dosenId]['K001'] = $item['skala_interval']['nilai_decimal'] ?? 1.0;
                }
            }
        }

        // Proses data K002 - Penelitian
        if (isset($dataK002['prioritas_global']) && is_array($dataK002['prioritas_global'])) {
            foreach ($dataK002['prioritas_global'] as $item) {
                $dosenId = $item['dosen']['id'] ?? null;
                if ($dosenId && isset($gabunganDosen[$dosenId])) {
                    $gabunganDosen[$dosenId]['K002'] = $item['nilai_decimal'] ?? 1.0;
                }
            }
        }

        // Proses data K003 - PKM
        if (isset($dataK003['data'])) {
            foreach ($dataK003['data'] as $item) {
                $dosenId = $item['dosen_id'] ?? null;
                if ($dosenId && isset($gabunganDosen[$dosenId])) {
                    $gabunganDosen[$dosenId]['K003'] = $item['skala_interval'] ?? 1.0;
                }
            }
        }

        // Proses data K004 - Penunjang/Tridarma
        if (isset($dataK004['data'])) {
            foreach ($dataK004['data'] as $item) {
                $dosenId = $item['dosen_id'] ?? null;
                if ($dosenId && isset($gabunganDosen[$dosenId])) {
                    $gabunganDosen[$dosenId]['K004'] = $item['skala_interval'] ?? 1.0;
                }
            }
        }

        return array_values($gabunganDosen);
    }

    /**
     * Membuat matriks perbandingan berpasangan
     */
    private function buatMatriksPerbandingan($kriteriaKode)
    {
        $n = count($kriteriaKode);
        $matriks = [];
        $jumlahKolom = array_fill(0, $n, 0);

        // Buat matriks perbandingan
        for ($i = 0; $i < $n; $i++) {
            $matriks[$kriteriaKode[$i]] = [];
            for ($j = 0; $j < $n; $j++) {
                if ($i == $j) {
                    $nilai = 1.0;
                } elseif ($i < $j) {
                    // Gunakan bobot dasar untuk perbandingan
                    $bobotI = $this->bobotDasar[$kriteriaKode[$i]];
                    $bobotJ = $this->bobotDasar[$kriteriaKode[$j]];
                    $nilai = $bobotI / $bobotJ;
                } else {
                    // Kebalikan dari nilai di atas diagonal
                    $bobotI = $this->bobotDasar[$kriteriaKode[$i]];
                    $bobotJ = $this->bobotDasar[$kriteriaKode[$j]];
                    $nilai = $bobotI / $bobotJ;
                }

                $matriks[$kriteriaKode[$i]][$kriteriaKode[$j]] = round($nilai, 5);
                $jumlahKolom[$j] += $nilai;
            }
        }

        return [
            'matriks' => $matriks,
            'jumlah_kolom' => $jumlahKolom,
            'kriteria' => $kriteriaKode
        ];
    }

    /**
     * Menghitung bobot prioritas
     */
    private function hitungBobotPrioritas($matriksData, $kriteriaKode)
    {
        $matriks = $matriksData['matriks'];
        $jumlahKolom = $matriksData['jumlah_kolom'];
        $n = count($kriteriaKode);

        $matriksNormalisasi = [];
        $jumlahBaris = [];

        // Normalisasi matriks
        foreach ($kriteriaKode as $i) {
            $matriksNormalisasi[$i] = [];
            $jumlahBaris[$i] = 0;

            foreach ($kriteriaKode as $j => $kode) {
                $nilaiNormalisasi = $matriks[$i][$kode] / $jumlahKolom[$j];
                $matriksNormalisasi[$i][$kode] = round($nilaiNormalisasi, 5);
                $jumlahBaris[$i] += $nilaiNormalisasi;
            }
        }

        // Hitung bobot prioritas (rata-rata baris)
        $bobotPrioritas = [];
        foreach ($kriteriaKode as $kode) {
            $bobotPrioritas[$kode] = round($jumlahBaris[$kode] / $n, 5);
        }

        return [
            'matriks_normalisasi' => $matriksNormalisasi,
            'jumlah_baris' => $jumlahBaris,
            'bobot_prioritas' => $bobotPrioritas
        ];
    }

    /**
     * Menghitung konsistensi (CI dan CR)
     */
    private function hitungKonsistensi($matriksData, $bobotPrioritas, $n)
    {
        $matriks = $matriksData['matriks'];
        $bobot = $bobotPrioritas['bobot_prioritas'];
        $jumlahKolom = $matriksData['jumlah_kolom']; // Perbaiki key dari 'total_kolom' ke 'jumlah_kolom'
        $kriteria = $matriksData['kriteria'];

        // Hitung λ maks menggunakan total kolom matriks perbandingan x bobot prioritas
        $lambdaMaks = [];
        $totalLambdaMaks = 0;

        foreach ($kriteria as $i => $kode) {
            // Gunakan jumlah kolom dari matriks perbandingan sesuai urutan kriteria
            $lambdaMaks[$kode] = $jumlahKolom[$i] * $bobot[$kode];
            $totalLambdaMaks += $lambdaMaks[$kode];
        }

        // Hitung CI dan CR
        $CI = ($totalLambdaMaks - $n) / ($n - 1);
        $RI = $this->randomIndex[$n] ?? 1.12;
        $CR = $CI / $RI;

        return [
            'lambda_maks' => $lambdaMaks,
            'total_lambda_maks' => round($totalLambdaMaks, 5),
            'CI' => round($CI, 5),
            'RI' => $RI,
            'CR' => round($CR, 5),
            'konsisten' => $CR < 0.1 ? 'Ya' : 'Tidak'
        ];
    }

    /**
     * Hitung prioritas global untuk setiap dosen
     */
    private function hitungPrioritasGlobal($dataKriteria, $bobotKriteria)
    {
        $prioritasGlobal = [];

        foreach ($dataKriteria as $data) {
            $prioritas = 0;
            $detailKriteria = [];

            foreach (['K001', 'K002', 'K003', 'K004'] as $kode) {
                $nilaiKriteria = $data[$kode];
                $bobotKriteriaVal = $bobotKriteria[$kode];
                $kontribusi = $nilaiKriteria * $bobotKriteriaVal;

                $prioritas += $kontribusi;
                $detailKriteria[$kode] = [
                    'nilai' => $nilaiKriteria,
                    'bobot' => $bobotKriteriaVal,
                    'kontribusi' => round($kontribusi, 5)
                ];
            }

            $prioritasGlobal[] = [
                'dosen' => $data['dosen'],
                'detail_kriteria' => $detailKriteria,
                'prioritas_global' => round($prioritas, 5)
            ];
        }

        return $prioritasGlobal;
    }

    /**
     * Hitung persentase menggunakan min-max scaling
     */
    private function hitungPersentaseMinMax($prioritasGlobal)
    {
        if (empty($prioritasGlobal)) return $prioritasGlobal;

        $nilaiPrioritas = array_column($prioritasGlobal, 'prioritas_global');
        $min = min($nilaiPrioritas);
        $max = max($nilaiPrioritas);

        foreach ($prioritasGlobal as &$data) {
            if ($max > $min) {
                $persentase = (($data['prioritas_global'] - $min) / ($max - $min)) * 100;
            } else {
                $persentase = 100;
            }
            $data['persentase'] = round($persentase, 2);
        }

        return $prioritasGlobal;
    }

    /**
     * Tambahkan kategori nilai decimal berdasarkan persentase
     */
    private function tambahkanKategoriNilaiDecimal($prioritasGlobal)
    {
        foreach ($prioritasGlobal as &$data) {
            $persentase = $data['persentase'];

            if ($persentase >= 81) {
                $kategori = [
                    'kategori' => 'Sangat Baik',
                    'nilai_decimal' => 5.0,
                    'keterangan' => 'Performa sangat memuaskan'
                ];
            } elseif ($persentase >= 61) {
                $kategori = [
                    'kategori' => 'Baik',
                    'nilai_decimal' => 4.0,
                    'keterangan' => 'Performa memuaskan'
                ];
            } elseif ($persentase >= 41) {
                $kategori = [
                    'kategori' => 'Cukup',
                    'nilai_decimal' => 3.0,
                    'keterangan' => 'Performa memadai'
                ];
            } elseif ($persentase >= 21) {
                $kategori = [
                    'kategori' => 'Kurang',
                    'nilai_decimal' => 2.0,
                    'keterangan' => 'Performa perlu ditingkatkan'
                ];
            } else {
                $kategori = [
                    'kategori' => 'Sangat Kurang',
                    'nilai_decimal' => 1.0,
                    'keterangan' => 'Performa sangat perlu ditingkatkan'
                ];
            }

            $data['kategori_nilai'] = $kategori;
        }

        return $prioritasGlobal;
    }

    /**
     * API untuk mendapatkan detail perhitungan per dosen
     */
    public function detailDosenAhpTridarma($dosen_id)
    {
        // Ambil data lengkap
        $hasilLengkap = $this->perhitunganAhpTridarma();
        $dataLengkap = json_decode($hasilLengkap->getContent(), true);

        // Cari data dosen spesifik
        $dataDosen = null;
        foreach ($dataLengkap['data']['hasil_akhir'] as $item) {
            if ($item['dosen']['id'] == $dosen_id) {
                $dataDosen = $item;
                break;
            }
        }

        if (!$dataDosen) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data dosen tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'dosen' => $dataDosen['dosen'],
                'detail_kriteria' => $dataDosen['detail_kriteria'],
                'prioritas_global' => $dataDosen['prioritas_global'],
                'persentase' => $dataDosen['persentase'],
                'ranking' => $dataDosen['ranking'],
                'kategori_nilai' => $dataDosen['kategori_nilai'],
                'bobot_kriteria' => $dataLengkap['data']['bobot_prioritas']['bobot_prioritas'],
                'konsistensi' => $dataLengkap['data']['konsistensi'],
                'matriks_bobot_prioritas' => $this->hitungMatriksBobotPrioritasDosen($dataDosen, $dataLengkap['data']['bobot_prioritas']['bobot_prioritas'])
            ]
        ]);
    }

    /**
     * Hitung Matriks Bobot Prioritas untuk dosen tertentu
     */
    private function hitungMatriksBobotPrioritasDosen($dataDosen, $bobotKriteria)
    {
        $kriteria = ['K001', 'K002', 'K003', 'K004'];
        $kriteriaNama = [
            'K001' => 'Pendidikan dan Pembelajaran',
            'K002' => 'Penelitian',
            'K003' => 'PKM (Pengabdian Kepada Masyarakat)',
            'K004' => 'Penunjang'
        ];

        // Nilai maksimum untuk normalisasi (bisa disesuaikan berdasarkan skala)
        $nilaiMaksimum = [
            'K001' => 5.0,
            'K002' => 5.0,
            'K003' => 5.0,
            'K004' => 5.0
        ];

        $detailKriteria = $dataDosen['detail_kriteria'] ?? [];

        // 1. Langkah Nilai Mentah
        $nilaiMentah = [];
        foreach ($kriteria as $kode) {
            $detail = $detailKriteria[$kode] ?? ['nilai' => 1.0];
            $nilaiAsli = $detail['nilai'] ?? 1.0;

            $nilaiMentah[$kode] = [
                'kode' => $kode,
                'nama' => $kriteriaNama[$kode],
                'nilai_mentah' => $nilaiAsli,
                'kategori' => $this->getKategoriNilai($nilaiAsli),
                'badge_class' => $this->getBadgeClass($nilaiAsli)
            ];
        }

        // 2. Langkah Normalisasi
        $normalisasi = [];
        foreach ($kriteria as $kode) {
            $nilaiAsli = $nilaiMentah[$kode]['nilai_mentah'];
            $nilaiMax = $nilaiMaksimum[$kode];
            $nilaiNormalisasi = $nilaiMax > 0 ? ($nilaiAsli / $nilaiMax) : 0;

            $normalisasi[$kode] = [
                'kode' => $kode,
                'nilai_mentah' => $nilaiAsli,
                'nilai_max' => $nilaiMax,
                'nilai_normalisasi' => round($nilaiNormalisasi, 5),
                'formula' => "{$nilaiAsli} ÷ {$nilaiMax} = " . round($nilaiNormalisasi, 5)
            ];
        }

        // 3. Langkah Prioritas Global
        $prioritasGlobal = [];
        $totalPrioritas = 0;

        foreach ($kriteria as $kode) {
            $nilaiNormalisasi = $normalisasi[$kode]['nilai_normalisasi'];
            $bobot = $bobotKriteria[$kode] ?? 0;
            $kontribusi = $nilaiNormalisasi * $bobot;
            $totalPrioritas += $kontribusi;

            $prioritasGlobal[$kode] = [
                'kode' => $kode,
                'nama' => $kriteriaNama[$kode],
                'nilai_normalisasi' => round($nilaiNormalisasi, 5),
                'bobot_kriteria' => round($bobot, 5),
                'kontribusi' => round($kontribusi, 5),
                'formula' => round($nilaiNormalisasi, 5) . " × " . round($bobot, 5) . " = " . round($kontribusi, 5)
            ];
        }

        // 4. Matriks Perbandingan Antar Kriteria untuk Dosen ini
        $matriksPerbandingan = $this->buatMatriksPerbandinganDosen($nilaiMentah, $kriteria);

        // 5. Hitung Bobot Prioritas dari Matriks Perbandingan
        $bobotPrioritasMatriks = $this->hitungBobotPrioritasMatriks($matriksPerbandingan);

        return [
            'nilai_mentah' => $nilaiMentah,
            'normalisasi' => $normalisasi,
            'prioritas_global' => $prioritasGlobal,
            'total_prioritas_global' => round($totalPrioritas, 5),
            'matriks_perbandingan' => $matriksPerbandingan,
            'bobot_prioritas_matriks' => $bobotPrioritasMatriks,
            'ringkasan' => [
                'dosen_nama' => $dataDosen['dosen']['nama'] ?? $dataDosen['dosen']['nama_dosen'],
                'total_kriteria' => count($kriteria),
                'rata_rata_nilai' => round(array_sum(array_column($nilaiMentah, 'nilai_mentah')) / count($kriteria), 3),
                'kriteria_terbaik' => $this->getKriteriaTerbaik($nilaiMentah),
                'kriteria_terlemah' => $this->getKriteriaTermudah($nilaiMentah),
                'bobot_prioritas_valid' => $bobotPrioritasMatriks['is_valid']
            ]
        ];
    }

    /**
     * Membuat matriks perbandingan untuk dosen
     */
    private function buatMatriksPerbandinganDosen($nilaiMentah, $kriteria)
    {
        $matriks = [];
        $totalKolom = array_fill(0, count($kriteria), 0);

        foreach ($kriteria as $i => $k1) {
            $matriks[$k1] = [];
            foreach ($kriteria as $j => $k2) {
                if ($k1 === $k2) {
                    $nilai = 1.0;
                } else {
                    $nilai1 = $nilaiMentah[$k1]['nilai_mentah'] ?: 1.0;
                    $nilai2 = $nilaiMentah[$k2]['nilai_mentah'] ?: 1.0;
                    $nilai = $nilai2 > 0 ? ($nilai1 / $nilai2) : 1.0;
                }
                $matriks[$k1][$k2] = round($nilai, 3);
                $totalKolom[$j] += $nilai;
            }
        }

        return [
            'matriks' => $matriks,
            'total_kolom' => array_map(function($total) { return round($total, 3); }, $totalKolom),
            'kriteria' => $kriteria
        ];
    }

    /**
     * Menghitung bobot prioritas dari matriks perbandingan dengan normalisasi
     */
    private function hitungBobotPrioritasMatriks($matriksData)
    {
        $matriks = $matriksData['matriks'];
        $totalKolom = $matriksData['total_kolom'];
        $kriteria = $matriksData['kriteria'];

        // Langkah 1: Normalisasi matriks (bagi setiap elemen dengan total kolomnya)
        $matriksNormalisasi = [];
        $jumlahBaris = [];

        foreach ($kriteria as $i => $k1) {
            $matriksNormalisasi[$k1] = [];
            $jumlahBaris[$k1] = 0;

            foreach ($kriteria as $j => $k2) {
                // Normalisasi: a_ij / total_kolom_j
                $nilaiNormalisasi = $totalKolom[$j] > 0 ? ($matriks[$k1][$k2] / $totalKolom[$j]) : 0;
                $matriksNormalisasi[$k1][$k2] = round($nilaiNormalisasi, 5);
                $jumlahBaris[$k1] += $nilaiNormalisasi;
            }
        }

        // Langkah 2: Hitung bobot prioritas (rata-rata setiap baris)
        $bobotPrioritas = [];
        $n = count($kriteria);

        foreach ($kriteria as $k) {
            $bobotPrioritas[$k] = round($jumlahBaris[$k] / $n, 5);
        }

        // Langkah 3: Verifikasi total bobot = 1
        $totalBobot = array_sum($bobotPrioritas);

        return [
            'matriks_normalisasi' => $matriksNormalisasi,
            'jumlah_baris' => $jumlahBaris,
            'bobot_prioritas' => $bobotPrioritas,
            'total_bobot' => round($totalBobot, 5),
            'is_valid' => abs($totalBobot - 1.0) < 0.001, // Toleransi untuk floating point
            'langkah_perhitungan' => $this->getDetailLangkahPerhitungan($matriks, $totalKolom, $matriksNormalisasi, $kriteria)
        ];
    }

    /**
     * Mendapatkan detail langkah perhitungan untuk setiap elemen
     */
    private function getDetailLangkahPerhitungan($matriks, $totalKolom, $matriksNormalisasi, $kriteria)
    {
        $langkahDetail = [];

        foreach ($kriteria as $i => $k1) {
            $langkahDetail[$k1] = [];
            foreach ($kriteria as $j => $k2) {
                $nilaiAsli = $matriks[$k1][$k2];
                $totalKol = $totalKolom[$j];
                $hasilNormalisasi = $matriksNormalisasi[$k1][$k2];

                $langkahDetail[$k1][$k2] = [
                    'nilai_asli' => $nilaiAsli,
                    'total_kolom' => $totalKol,
                    'hasil_normalisasi' => $hasilNormalisasi,
                    'formula' => "{$nilaiAsli} ÷ {$totalKol} = {$hasilNormalisasi}",
                    'perhitungan' => "{$nilaiAsli}/{$totalKol}"
                ];
            }
        }

        return $langkahDetail;
    }

    /**
     * Helper functions
     */
    private function getKategoriNilai($nilai)
    {
        if ($nilai >= 4.0) return 'Sangat Baik';
        if ($nilai >= 3.0) return 'Baik';
        if ($nilai >= 2.0) return 'Cukup';
        return 'Kurang';
    }

    private function getBadgeClass($nilai)
    {
        if ($nilai >= 4.0) return 'bg-success';
        if ($nilai >= 3.0) return 'bg-primary';
        if ($nilai >= 2.0) return 'bg-warning';
        return 'bg-danger';
    }

    private function getKriteriaTerbaik($nilaiMentah)
    {
        $terbaik = array_reduce($nilaiMentah, function($carry, $item) {
            return (!$carry || $item['nilai_mentah'] > $carry['nilai_mentah']) ? $item : $carry;
        });
        return $terbaik['nama'] ?? 'N/A';
    }

    private function getKriteriaTermudah($nilaiMentah)
    {
        $terlemah = array_reduce($nilaiMentah, function($carry, $item) {
            return (!$carry || $item['nilai_mentah'] < $carry['nilai_mentah']) ? $item : $carry;
        });
        return $terlemah['nama'] ?? 'N/A';
    }
}
