<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Indikator;
use App\Models\Dosen;

class AhpPenelitianController extends Controller
{
    // Bobot dasar untuk masing-masing indikator KPT
    private $bobotDasar = [
        'KPT01' => 3.00000,
        'KPT02' => 2.50000,
        'KPT03' => 2.00000,
        'KPT04' => 1.50000,
        'KPT05' => 1.00000
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
     * Perhitungan AHP lengkap untuk kriteria penelitian
     */
    public function perhitunganAhpPenelitian()
    {
        // 1. Ambil data nilai indikator untuk semua dosen
        $perhitunganController = new \App\Http\Controllers\PerhitunganPenelitianController();
        $hasilSemuaDosen = $perhitunganController->hitungSemuaDosen();
        $dataDosen = json_decode($hasilSemuaDosen->getContent(), true);

        // 2. Siapkan matriks perbandingan berpasangan
        $indikatorKode = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'];
        $matriks = $this->buatMatriksPerbandingan($indikatorKode);

        // 3. Hitung bobot prioritas
        $bobotPrioritas = $this->hitungBobotPrioritas($matriks, $indikatorKode);

        // 4. Hitung konsistensi
        $konsistensi = $this->hitungKonsistensi($matriks, $bobotPrioritas, count($indikatorKode));

        // 5. Normalisasi data dosen
        $dataNormalisasi = $this->normalisasiDataDosen($dataDosen);

        // 6. Hitung skor akhir AHP
        $skorAhp = $this->hitungSkorAhp($dataNormalisasi, $bobotPrioritas);

        return response()->json([
            'langkah_perhitungan' => [
                '1_bobot_dasar_indikator' => $this->bobotDasar,
                '2_matriks_perbandingan_berpasangan' => $matriks,
                '3_bobot_prioritas' => $bobotPrioritas,
                '4_uji_konsistensi' => $konsistensi,
                '5_data_normalisasi_dosen' => $dataNormalisasi,
                '6_skor_akhir_ahp' => $skorAhp
            ],
            'hasil_ranking' => $this->urutkanHasil($skorAhp),
            'kesimpulan' => [
                'konsistensi_diterima' => $konsistensi['CR'] <= 0.1,
                'total_dosen_dievaluasi' => count($skorAhp),
                'metode' => 'Analytical Hierarchy Process (AHP)'
            ]
        ]);
    }

    /**
     * Membuat matriks perbandingan berpasangan
     */
    private function buatMatriksPerbandingan($indikatorKode)
    {
        $n = count($indikatorKode);
        $matriks = [];
        $jumlahKolom = array_fill(0, $n, 0);

        // Buat matriks perbandingan
        for ($i = 0; $i < $n; $i++) {
            $matriks[$indikatorKode[$i]] = [];
            for ($j = 0; $j < $n; $j++) {
                if ($i == $j) {
                    $nilai = 1.00000;
                } else {
                    // Perbandingan bobot indikator i dengan indikator j
                    $nilai = $this->bobotDasar[$indikatorKode[$i]] / $this->bobotDasar[$indikatorKode[$j]];
                }
                $matriks[$indikatorKode[$i]][$indikatorKode[$j]] = round($nilai, 5);
                $jumlahKolom[$j] += $nilai;
            }
        }

        return [
            'matriks' => $matriks,
            'jumlah_kolom' => array_combine($indikatorKode, array_map(function($val) { return round($val, 5); }, $jumlahKolom))
        ];
    }

    /**
     * Menghitung bobot prioritas
     */
    private function hitungBobotPrioritas($matriksData, $indikatorKode)
    {
        $matriks = $matriksData['matriks'];
        $jumlahKolom = $matriksData['jumlah_kolom'];
        $n = count($indikatorKode);

        $matriksNormalisasi = [];
        $jumlahBaris = [];

        // Normalisasi matriks (bagi setiap elemen dengan jumlah kolomnya)
        foreach ($indikatorKode as $i) {
            $matriksNormalisasi[$i] = [];
            $jumlahBaris[$i] = 0;

            foreach ($indikatorKode as $j) {
                $nilaiNormalisasi = $matriks[$i][$j] / $jumlahKolom[$j];
                $matriksNormalisasi[$i][$j] = round($nilaiNormalisasi, 5);
                $jumlahBaris[$i] += $nilaiNormalisasi;
            }
            $jumlahBaris[$i] = round($jumlahBaris[$i], 5);
        }

        // Hitung rata-rata setiap baris (bobot prioritas)
        $bobotPrioritas = [];
        foreach ($indikatorKode as $kode) {
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
        $jumlahKolom = $matriksData['jumlah_kolom'];
        $bobot = $bobotPrioritas['bobot_prioritas'];

        // Hitung λ maks
        $lambdaMaks = [];
        $totalLambdaMaks = 0;

        $indikatorKode = array_keys($bobot);
        foreach ($indikatorKode as $i => $kode) {
            $lambdaMaks[$kode] = $jumlahKolom[$kode] * $bobot[$kode];
            $totalLambdaMaks += $lambdaMaks[$kode];
        }

        // Hitung CI
        $CI = ($totalLambdaMaks - $n) / ($n - 1);

        // Hitung CR
        $RI = $this->randomIndex[$n] ?? 1.12;
        $CR = $CI / $RI;

        return [
            'lambda_maks_detail' => $lambdaMaks,
            'total_lambda_maks' => round($totalLambdaMaks, 5),
            'CI' => round($CI, 7),
            'RI' => $RI,
            'CR' => round($CR, 10),
            'status_konsistensi' => $CR <= 0.1 ? 'Konsisten' : 'Tidak Konsisten'
        ];
    }

    /**
     * Normalisasi data dosen berdasarkan skala interval
     */
    private function normalisasiDataDosen($dataDosen)
    {
        $dataNormalisasi = [];

        foreach ($dataDosen as $dosen) {
            $dosenInfo = $dosen['dosen'];
            $perhitungan = $dosen['perhitungan'];

            $nilaiNormalisasi = [];

            foreach ($perhitungan['detail_indikator'] as $indikator) {
                $kode = $indikator['kode'];
                if (in_array($kode, ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'])) {
                    // Gunakan bobot yang sudah dihitung (skala 1-5)
                    $nilaiNormalisasi[$kode] = [
                        'total_nilai_indikator' => $indikator['total_nilai_indikator'],
                        'skala_normalisasi' => $indikator['bobot'] // Ini sudah dalam skala 1-5
                    ];
                }
            }

            $dataNormalisasi[] = [
                'dosen' => $dosenInfo,
                'nilai_normalisasi' => $nilaiNormalisasi
            ];
        }

        return $dataNormalisasi;
    }

    /**
     * Menghitung skor akhir AHP
     */
    private function hitungSkorAhp($dataNormalisasi, $bobotPrioritas)
    {
        $skorAhp = [];
        $bobot = $bobotPrioritas['bobot_prioritas'];

        foreach ($dataNormalisasi as $data) {
            $dosenInfo = $data['dosen'];
            $nilaiNormalisasi = $data['nilai_normalisasi'];

            $skorTotal = 0;
            $detailSkor = [];

            foreach (['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'] as $kode) {
                if (isset($nilaiNormalisasi[$kode])) {
                    $nilaiSkala = $nilaiNormalisasi[$kode]['skala_normalisasi'];
                    $bobotIndikator = $bobot[$kode];
                    $skorIndikator = $nilaiSkala * $bobotIndikator;

                    $skorTotal += $skorIndikator;

                    $detailSkor[$kode] = [
                        'total_nilai_indikator' => $nilaiNormalisasi[$kode]['total_nilai_indikator'],
                        'skala_normalisasi' => $nilaiSkala,
                        'bobot_prioritas' => $bobotIndikator,
                        'skor' => round($skorIndikator, 5)
                    ];
                }
            }

            $skorAhp[] = [
                'dosen' => $dosenInfo,
                'detail_skor' => $detailSkor,
                'skor_total_ahp' => round($skorTotal, 5)
            ];
        }

        return $skorAhp;
    }

    /**
     * Mengurutkan hasil berdasarkan skor AHP tertinggi
     */
    private function urutkanHasil($skorAhp)
    {
        usort($skorAhp, function($a, $b) {
            return $b['skor_total_ahp'] <=> $a['skor_total_ahp'];
        });

        // Tambahkan ranking
        foreach ($skorAhp as $index => &$data) {
            $data['ranking'] = $index + 1;
        }

        return $skorAhp;
    }

    /**
     * API untuk mendapatkan detail perhitungan per dosen
     */
    public function detailDosenAhp($dosen_id)
    {
        $perhitunganController = new \App\Http\Controllers\PerhitunganPenelitianController();
        $hasilDosen = $perhitunganController->hitungPenelitian($dosen_id);
        $dataDosen = json_decode($hasilDosen->getContent(), true);

        // Hitung bobot prioritas
        $indikatorKode = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'];
        $matriks = $this->buatMatriksPerbandingan($indikatorKode);
        $bobotPrioritas = $this->hitungBobotPrioritas($matriks, $indikatorKode);

        // Normalisasi untuk dosen ini
        $nilaiNormalisasi = [];
        $skorTotal = 0;
        $detailSkor = [];

        foreach ($dataDosen['detail_indikator'] as $indikator) {
            $kode = $indikator['kode'];
            if (in_array($kode, $indikatorKode)) {
                $nilaiSkala = $indikator['bobot']; // Sudah dalam skala 1-5
                $bobotIndikator = $bobotPrioritas['bobot_prioritas'][$kode];
                $skorIndikator = $nilaiSkala * $bobotIndikator;

                $skorTotal += $skorIndikator;

                $detailSkor[$kode] = [
                    'nama_indikator' => $indikator['nama'],
                    'total_nilai_indikator' => $indikator['total_nilai_indikator'],
                    'skala_normalisasi' => $nilaiSkala,
                    'bobot_prioritas' => $bobotIndikator,
                    'skor' => round($skorIndikator, 5)
                ];
            }
        }

        return response()->json([
            'dosen' => [
                'id' => $dosen_id,
                'info' => 'Detail perhitungan AHP untuk dosen ID: ' . $dosen_id
            ],
            'detail_perhitungan' => $detailSkor,
            'skor_total_ahp' => round($skorTotal, 5),
            'bobot_prioritas_digunakan' => $bobotPrioritas['bobot_prioritas']
        ]);
    }
}
