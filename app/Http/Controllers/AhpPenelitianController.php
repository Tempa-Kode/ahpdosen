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

        // 7. Hitung prioritas global untuk setiap dosen
        $bobotKriteria = $bobotPrioritas['bobot_prioritas'];
        $prioritasGlobal = [];
        foreach ($skorAhp as $dosenData) {
            $totalGlobal = 0;
            foreach ($bobotKriteria as $kode => $bobotK) {
                // Bobot prioritas dosen pada kriteria: gunakan bobot_prioritas (hasil matriks normalisasi dosen)
                $bobotPrioritasDosen = isset($dosenData['detail_skor'][$kode]['bobot_prioritas'])
                    ? $dosenData['detail_skor'][$kode]['bobot_prioritas']
                    : 0;
                $totalGlobal += $bobotPrioritasDosen * $bobotK;
            }
            $prioritasGlobal[] = [
                'dosen' => $dosenData['dosen'],
                'prioritas_global' => $totalGlobal, // hasil prioritas global
                'detail_skor' => $dosenData['detail_skor'],
                'skor_total_ahp' => $dosenData['skor_total_ahp'],
                'ranking' => $dosenData['ranking'] ?? null
            ];
        }

        return response()->json([
            'langkah_perhitungan' => [
                '1_bobot_dasar_indikator' => $this->bobotDasar,
                '2_matriks_perbandingan_berpasangan' => $matriks,
                '3_bobot_prioritas' => $bobotPrioritas,
                '4_uji_konsistensi' => $konsistensi,
                '5_data_normalisasi_dosen' => $dataNormalisasi,
                '6_skor_akhir_ahp' => $skorAhp,
                '7_prioritas_global' => $prioritasGlobal
            ],
            'hasil_ranking' => $this->urutkanHasil($skorAhp),
            'prioritas_global' => $prioritasGlobal,
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

        // Hitung λ maks dengan rumus yang benar:
        // Total Perbandingan x Bobot Prioritas untuk setiap kriteria
        $lambdaMaks = [];
        $totalLambdaMaks = 0;

        $indikatorKode = array_keys($bobot);
        foreach ($indikatorKode as $kode) {
            // Lambda maks = Total perbandingan kolom × Bobot prioritas
            $lambdaMaks[$kode] = $jumlahKolom[$kode] * $bobot[$kode];
            $totalLambdaMaks += $lambdaMaks[$kode];
        }

        // Lambda maks rata-rata
        $lambdaMaksRataRata = $totalLambdaMaks;

        // Hitung CI (Consistency Index)
        $CI = ($lambdaMaksRataRata - $n) / ($n - 1);

        // Hitung CR (Consistency Ratio)
        $RI = $this->randomIndex[$n] ?? 1.12;
        $CR = $CI / $RI;

        // Format CR untuk tampilan yang lebih cantik
        $CR_formatted = $this->formatAngkaKecil($CR);
        $CI_formatted = $this->formatAngkaKecil($CI);

        return [
            'total_perbandingan_per_kolom' => $jumlahKolom,
            'bobot_prioritas' => $bobot,
            'lambda_maks_detail' => array_map(function($val) { return round($val, 5); }, $lambdaMaks),
            'total_lambda_maks' => round($totalLambdaMaks, 5),
            'lambda_maks_rata_rata' => round($lambdaMaksRataRata, 5),
            'CI' => $CI_formatted,
            'CI_raw' => round($CI, 10),
            'RI' => $RI,
            'CR' => $CR_formatted,
            'CR_raw' => round($CR, 10),
            'status_konsistensi' => abs($CR) <= 0.1 ? 'Konsisten' : 'Tidak Konsisten',
            'penjelasan' => [
                'rumus_lambda_maks' => 'Total Perbandingan × Bobot Prioritas',
                'rumus_ci' => '(λ maks - n) / (n - 1)',
                'rumus_cr' => 'CI / RI',
                'batas_konsistensi' => 'CR ≤ 0.1',
                'interpretasi_angka' => 'Angka yang sangat kecil (mendekati 0) menunjukkan konsistensi yang sangat baik'
            ]
        ];
    }    /**
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
     * Menghitung bobot prioritas individual per dosen (matriks perbandingan, normalisasi, bobot prioritas)
     */
    private function hitungBobotPrioritasIndividual($nilaiNormalisasi, $indikatorKode = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'])
    {
        $n = count($indikatorKode);
        // 1. Matriks perbandingan berpasangan
        $pairwiseMatrix = [];
        foreach ($indikatorKode as $kode1) {
            $row = [];
            foreach ($indikatorKode as $kode2) {
                $nilai1 = isset($nilaiNormalisasi[$kode1]) ? $nilaiNormalisasi[$kode1]['skala_normalisasi'] : 1;
                $nilai2 = isset($nilaiNormalisasi[$kode2]) ? $nilaiNormalisasi[$kode2]['skala_normalisasi'] : 1;
                $row[] = $nilai2 != 0 ? round($nilai1 / $nilai2, 5) : 1;
            }
            $pairwiseMatrix[$kode1] = $row;
        }
        // 2. Jumlah kolom
        $columnTotals = [];
        for ($j = 0; $j < $n; $j++) {
            $sum = 0;
            foreach ($indikatorKode as $kode1) {
                $sum += $pairwiseMatrix[$kode1][$j];
            }
            $columnTotals[$j] = round($sum, 5);
        }
        // 3. Matriks normalisasi
        $normalizedMatrix = [];
        foreach ($indikatorKode as $i => $kode1) {
            $row = [];
            for ($j = 0; $j < $n; $j++) {
                $val = $columnTotals[$j] != 0 ? $pairwiseMatrix[$kode1][$j] / $columnTotals[$j] : 0;
                $row[] = round($val, 5);
            }
            $normalizedMatrix[$kode1] = $row;
        }
        // 4. Jumlah baris dan bobot prioritas
        $jumlahBaris = [];
        $bobotPrioritasIndividu = [];
        foreach ($indikatorKode as $i => $kode1) {
            $rowSum = array_sum($normalizedMatrix[$kode1]);
            $jumlahBaris[$kode1] = round($rowSum, 5);
            $bobotPrioritasIndividu[$kode1] = round($rowSum / $n, 5);
        }
        return [
            'pairwise_matrix' => $pairwiseMatrix,
            'column_totals' => $columnTotals,
            'normalized_matrix' => $normalizedMatrix,
            'jumlah_baris' => $jumlahBaris,
            'bobot_prioritas_individu' => $bobotPrioritasIndividu
        ];
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

            // Hitung bobot prioritas individual per dosen
            $indikatorKode = ['KPT01', 'KPT02', 'KPT03', 'KPT04', 'KPT05'];
            $individu = $this->hitungBobotPrioritasIndividual($nilaiNormalisasi, $indikatorKode);

            foreach ($indikatorKode as $kode) {
                if (isset($nilaiNormalisasi[$kode])) {
                    $nilaiSkala = $nilaiNormalisasi[$kode]['skala_normalisasi'];
                    $bobotIndikator = $bobot[$kode];
                    $skorIndikator = $nilaiSkala * $bobotIndikator;
                    $skorTotal += $skorIndikator;
                    $detailSkor[$kode] = [
                        'total_nilai_indikator' => $nilaiNormalisasi[$kode]['total_nilai_indikator'],
                        'skala_normalisasi' => $nilaiSkala,
                        'bobot_prioritas' => $individu['bobot_prioritas_individu'][$kode],
                        'skor' => round($skorIndikator, 5),
                        'matriks_perbandingan' => $individu['pairwise_matrix'][$kode],
                        'matriks_normalisasi' => $individu['normalized_matrix'][$kode],
                        'jumlah_baris' => $individu['jumlah_baris'][$kode]
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

    /**
     * Format angka yang sangat kecil untuk tampilan yang lebih cantik
     */
    private function formatAngkaKecil($angka)
    {
        // Tampilkan angka dalam format desimal langsung dengan 10 digit
        return number_format($angka, 10, '.', '');
    }
}
