<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Indikator;
use App\Models\SubIndikator;
use App\Models\Penilaian;
use App\Models\Dosen;

class PerhitunganPenelitianController extends Controller
{
    /**
     * Hitung nilai penelitian untuk kriteria K002
     */
    public function hitungPenelitian($dosen_id)
    {
        // Ambil kriteria K002 (Penelitian)
        $kriteria = Kriteria::where('kd_kriteria', 'K002')->first();

        if (!$kriteria) {
            return response()->json(['error' => 'Kriteria K002 tidak ditemukan'], 404);
        }

        // Ambil semua indikator untuk kriteria K002
        $indikators = Indikator::where('kriteria_id', $kriteria->id)->get();

        $hasil_perhitungan = [
            'dosen_id' => $dosen_id,
            'kriteria' => [
                'kode' => $kriteria->kd_kriteria,
                'nama' => $kriteria->nama_kriteria,
                'bobot' => $kriteria->bobot
            ],
            'detail_indikator' => [],
        ];

        $total_nilai_penelitian = 0;

        foreach ($indikators as $indikator) {
            $nilai_indikator = $this->hitungNilaiIndikator($dosen_id, $indikator);

            $hasil_perhitungan['detail_indikator'][] = $nilai_indikator;
        }

        // Total nilai kriteria penelitian
        $hasil_perhitungan['total_nilai_kriteria'] = $total_nilai_penelitian * $kriteria->bobot;

        return response()->json($hasil_perhitungan);
    }

    /**
     * Hitung nilai untuk satu indikator
     */
    private function hitungNilaiIndikator($dosen_id, $indikator)
    {
        // Cek apakah indikator memiliki nilai langsung
        $penilaianIndikator = Penilaian::where('dosen_id', $dosen_id)
            ->where('penilaian_type', 'App\Models\Indikator')
            ->where('penilaian_id', $indikator->id)
            ->first();

                // Jika indikator memiliki nilai langsung, gunakan nilai tersebut
        if ($penilaianIndikator && $penilaianIndikator->nilai > 0) {
            $nilai_indikator = $penilaianIndikator->nilai;

            // Hitung bobot berdasarkan skala interval untuk indikator KPT
            $skalaInterval = $this->hitungSkalaIntervalIndikator($indikator->kd_indikator, $nilai_indikator);
            $bobot = $skalaInterval ? $skalaInterval['nilai_skala'] : $indikator->bobot_indikator;

            $result = [
                'indikator_id' => $indikator->id,
                'kode' => $indikator->kd_indikator,
                'nama' => $indikator->nama_indikator,
                'bobot' => $bobot,
                'total_nilai_indikator' => $nilai_indikator,
                'detail_sub_indikator' => [],
            ];

            return $result;
        }

        // Jika tidak ada nilai langsung, gunakan perhitungan berdasarkan sub indikator
        $subIndikators = SubIndikator::where('indikator_id', $indikator->id)->get();

        $detail_sub_indikator = [];
        $total_nilai_indikator = 0;

        foreach ($subIndikators as $subIndikator) {
            // Ambil penilaian untuk sub indikator ini
            $penilaian = Penilaian::where('dosen_id', $dosen_id)
                ->where('penilaian_type', 'App\Models\SubIndikator')
                ->where('penilaian_id', $subIndikator->id)
                ->first();

            $nilai = $penilaian ? $penilaian->nilai : 0;
            $total_sub = $nilai * $subIndikator->skor_kredit;
            $total_nilai_indikator += $total_sub;

            $detail_sub_indikator[] = [
                'sub_indikator_id' => $subIndikator->id,
                'nama' => $subIndikator->nama_sub_indikator,
                'skor_kredit' => $subIndikator->skor_kredit,
                'nilai' => $nilai,
                'total' => $total_sub
            ];
        }

        // Hitung bobot berdasarkan skala interval untuk indikator KPT
        $skalaInterval = $this->hitungSkalaIntervalIndikator($indikator->kd_indikator, $total_nilai_indikator);
        $bobot = $skalaInterval ? $skalaInterval['nilai_skala'] : $indikator->bobot_indikator;

        return [
            'indikator_id' => $indikator->id,
            'kode' => $indikator->kd_indikator,
            'nama' => $indikator->nama_indikator,
            'bobot' => $bobot,
            'total_nilai_indikator' => $total_nilai_indikator,
            'detail_sub_indikator' => $detail_sub_indikator,
        ];
    }

    /**
     * Hitung skala interval berdasarkan total nilai indikator KPT01
     */
    private function hitungSkalaIntervalKPT01($totalNilai)
    {
        if ($totalNilai >= 300) {
            return [
                'nilai_skala' => 5,
                'kategori' => 'Sangat tinggi',
                'range' => '>=300'
            ];
        } elseif ($totalNilai >= 200) {
            return [
                'nilai_skala' => 4,
                'kategori' => 'Tinggi',
                'range' => '200-299'
            ];
        } elseif ($totalNilai >= 150) {
            return [
                'nilai_skala' => 3,
                'kategori' => 'Sedang',
                'range' => '150-199'
            ];
        } elseif ($totalNilai >= 100) {
            return [
                'nilai_skala' => 2,
                'kategori' => 'Rendah',
                'range' => '100-149'
            ];
        } else {
            return [
                'nilai_skala' => 1,
                'kategori' => 'Sangat rendah',
                'range' => '<=99'
            ];
        }
    }

    /**
     * Hitung skala interval berdasarkan total nilai indikator KPT02
     */
    private function hitungSkalaIntervalKPT02($totalNilai)
    {
        if ($totalNilai >= 400) {
            return [
                'nilai_skala' => 5,
                'kategori' => 'Sangat tinggi',
                'range' => '>=400'
            ];
        } elseif ($totalNilai >= 300) {
            return [
                'nilai_skala' => 4,
                'kategori' => 'Tinggi',
                'range' => '300-399'
            ];
        } elseif ($totalNilai >= 200) {
            return [
                'nilai_skala' => 3,
                'kategori' => 'Sedang',
                'range' => '200-299'
            ];
        } elseif ($totalNilai >= 100) {
            return [
                'nilai_skala' => 2,
                'kategori' => 'Rendah',
                'range' => '100-199'
            ];
        } else {
            return [
                'nilai_skala' => 1,
                'kategori' => 'Sangat Rendah',
                'range' => '<=99'
            ];
        }
    }

    /**
     * Hitung skala interval berdasarkan total nilai indikator KPT03
     */
    private function hitungSkalaIntervalKPT03($totalNilai)
    {
        if ($totalNilai >= 241) {
            return [
                'nilai_skala' => 5,
                'kategori' => 'Sangat tinggi',
                'range' => '241-300'
            ];
        } elseif ($totalNilai >= 178) {
            return [
                'nilai_skala' => 4,
                'kategori' => 'Tinggi',
                'range' => '178-240'
            ];
        } elseif ($totalNilai >= 121) {
            return [
                'nilai_skala' => 3,
                'kategori' => 'Sedang',
                'range' => '121-178'
            ];
        } elseif ($totalNilai >= 61) {
            return [
                'nilai_skala' => 2,
                'kategori' => 'Rendah',
                'range' => '61-120'
            ];
        } else {
            return [
                'nilai_skala' => 1,
                'kategori' => 'Sangat Rendah',
                'range' => '<=60'
            ];
        }
    }

    /**
     * Hitung skala interval berdasarkan total nilai indikator KPT04
     */
    private function hitungSkalaIntervalKPT04($totalNilai)
    {
        if ($totalNilai >= 57) {
            return [
                'nilai_skala' => 5,
                'kategori' => 'Sangat tinggi',
                'range' => '57-69'
            ];
        } elseif ($totalNilai >= 45) {
            return [
                'nilai_skala' => 4,
                'kategori' => 'Tinggi',
                'range' => '45-56'
            ];
        } elseif ($totalNilai >= 29) {
            return [
                'nilai_skala' => 3,
                'kategori' => 'Sedang',
                'range' => '29-42'
            ];
        } elseif ($totalNilai >= 15) {
            return [
                'nilai_skala' => 2,
                'kategori' => 'Rendah',
                'range' => '15-28'
            ];
        } else {
            return [
                'nilai_skala' => 1,
                'kategori' => 'Sangat Rendah',
                'range' => '<=14'
            ];
        }
    }

    /**
     * Hitung skala interval berdasarkan total nilai indikator KPT05
     */
    private function hitungSkalaIntervalKPT05($totalNilai)
    {
        if ($totalNilai >= 12) {
            return [
                'nilai_skala' => 5,
                'kategori' => 'Sangat tinggi',
                'range' => '>=12'
            ];
        } elseif ($totalNilai >= 9) {
            return [
                'nilai_skala' => 4,
                'kategori' => 'Tinggi',
                'range' => '9-11'
            ];
        } elseif ($totalNilai >= 6) {
            return [
                'nilai_skala' => 3,
                'kategori' => 'Sedang',
                'range' => '6-8'
            ];
        } elseif ($totalNilai >= 3) {
            return [
                'nilai_skala' => 2,
                'kategori' => 'Rendah',
                'range' => '3-5'
            ];
        } else {
            return [
                'nilai_skala' => 1,
                'kategori' => 'Sangat Rendah',
                'range' => '<=2'
            ];
        }
    }

    /**
     * Hitung skala interval untuk semua indikator KPT
     */
    private function hitungSkalaIntervalIndikator($kodeIndikator, $totalNilai)
    {
        switch ($kodeIndikator) {
            case 'KPT01':
                return $this->hitungSkalaIntervalKPT01($totalNilai);
            case 'KPT02':
                return $this->hitungSkalaIntervalKPT02($totalNilai);
            case 'KPT03':
                return $this->hitungSkalaIntervalKPT03($totalNilai);
            case 'KPT04':
                return $this->hitungSkalaIntervalKPT04($totalNilai);
            case 'KPT05':
                return $this->hitungSkalaIntervalKPT05($totalNilai);
            default:
                return null;
        }
    }

    /**
     * Hitung skala interval KPT01 untuk semua dosen
     */
    public function laporanSkalaIntervalKPT01()
    {
        $dosens = Dosen::all();
        $hasil_skala = [];

        // Ambil indikator KPT01
        $indikatorKPT01 = Indikator::where('kd_indikator', 'KPT01')->first();

        if (!$indikatorKPT01) {
            return response()->json(['error' => 'Indikator KPT01 tidak ditemukan'], 404);
        }

        foreach ($dosens as $dosen) {
            // Hitung nilai indikator KPT01 untuk dosen ini
            $nilaiIndikator = $this->hitungNilaiIndikator($dosen->id, $indikatorKPT01);

            // Hitung skala interval berdasarkan total nilai indikator KPT01
            $skalaInterval = $this->hitungSkalaIntervalKPT01($nilaiIndikator['total_nilai_indikator']);

            $hasil_skala[] = [
                'dosen' => [
                    'id' => $dosen->id,
                    'nidn' => $dosen->nidn,
                    'nama' => $dosen->nama_dosen,
                    'prodi' => $dosen->prodi
                ],
                'indikator_kpt01' => [
                    'total_nilai_indikator' => $nilaiIndikator['total_nilai_indikator'],
                    'skala_interval' => $skalaInterval,
                    'detail_perhitungan' => $nilaiIndikator
                ]
            ];
        }

        // Urutkan berdasarkan total nilai indikator (tertinggi ke terendah)
        usort($hasil_skala, function($a, $b) {
            return $b['indikator_kpt01']['total_nilai_indikator'] <=> $a['indikator_kpt01']['total_nilai_indikator'];
        });

        return response()->json([
            'indikator' => [
                'kode' => 'KPT01',
                'nama' => $indikatorKPT01->nama_indikator,
                'bobot' => $indikatorKPT01->bobot_indikator
            ],
            'skala_interval_range' => [
                ['range' => '>=300', 'kategori' => 'Sangat tinggi', 'nilai' => 5],
                ['range' => '200-299', 'kategori' => 'Tinggi', 'nilai' => 4],
                ['range' => '150-199', 'kategori' => 'Sedang', 'nilai' => 3],
                ['range' => '100-149', 'kategori' => 'Rendah', 'nilai' => 2],
                ['range' => '<=99', 'kategori' => 'Sangat rendah', 'nilai' => 1]
            ],
            'total_dosen' => count($hasil_skala),
            'hasil_perhitungan' => $hasil_skala
        ]);
    }

    /**
     * Hitung nilai penelitian untuk semua dosen
     */
    public function hitungSemuaDosen()
    {
        $dosens = Dosen::all();
        $hasil_semua = [];

        foreach ($dosens as $dosen) {
            $hasil = $this->hitungPenelitian($dosen->id);
            $hasil_semua[] = [
                'dosen' => [
                    'id' => $dosen->id,
                    'nidn' => $dosen->nidn,
                    'nama' => $dosen->nama_dosen,
                    'prodi' => $dosen->prodi
                ],
                'perhitungan' => $hasil->getData()
            ];
        }

        return response()->json($hasil_semua);
    }
}

