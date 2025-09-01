<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Dosen;

class AhpTridarmaController extends Controller
{
    // Bobot dasar untuk masing-masing kriteria Tridarma
    private $bobotDasar = [
        'K001' => 3.00000, // Pendidikan dan Pembelajaran
        'K002' => 2.50000, // Penelitian
        'K003' => 2.00000, // PKM
        'K004' => 1.50000  // Penunjang
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
        $controllerK002 = new \App\Http\Controllers\PerhitunganPenelitianController();
        $hasilK002 = $controllerK002->hitungSemuaDosen();
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
                $dosenId = $item['dosen']['id'] ?? null;
                if ($dosenId && isset($gabunganDosen[$dosenId])) {
                    $gabunganDosen[$dosenId]['K001'] = $item['skala_interval']['nilai_decimal'] ?? 1.0;
                }
            }
        }

        // Proses data K002 - Penelitian
        if (isset($dataK002) && is_array($dataK002)) {
            foreach ($dataK002 as $item) {
                $dosenId = $item['dosen']['id'] ?? null;
                if ($dosenId && isset($gabunganDosen[$dosenId])) {
                    // Hitung rata-rata nilai bobot dari semua indikator penelitian
                    $totalBobot = 0;
                    $jumlahIndikator = 0;

                    if (isset($item['perhitungan']) && isset($item['perhitungan']['detail_indikator'])) {
                        foreach ($item['perhitungan']['detail_indikator'] as $indikator) {
                            $totalBobot += $indikator['bobot'] ?? 1.0;
                            $jumlahIndikator++;
                        }
                    }

                    $rataRataBobot = $jumlahIndikator > 0 ? $totalBobot / $jumlahIndikator : 1.0;
                    $gabunganDosen[$dosenId]['K002'] = $rataRataBobot;
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

        // Hitung λ maks
        $lambdaMaks = [];
        $totalLambdaMaks = 0;

        $kriteriaKode = array_keys($bobot);
        foreach ($kriteriaKode as $kode) {
            $jumlahKolom = 0;
            foreach ($kriteriaKode as $kode2) {
                $jumlahKolom += $matriks[$kode2][$kode];
            }
            $lambdaMaks[$kode] = $jumlahKolom * $bobot[$kode];
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
                'konsistensi' => $dataLengkap['data']['konsistensi']
            ]
        ]);
    }
}
