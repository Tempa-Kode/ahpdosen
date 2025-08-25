<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Kriteria;
use App\Models\Indikator;
use App\Models\SubIndikator;
use App\Models\SubSubIndikator;
use Illuminate\Http\Request;

class PerhitunganController extends Controller
{
    public function showPendidikanDanPembelajaran()
    {
        return view('perhitungan.pendidikan-pembelajaran');
    }

    public function pendidikanDanPembelajaran()
    {
        // Ambil kriteria dengan kode K001 (Pendidikan dan Pembelajaran)
        $kriteria = Kriteria::where('kd_kriteria', 'K001')->first();

        if (!$kriteria) {
            return response()->json(['error' => 'Kriteria K001 tidak ditemukan'], 404);
        }

        // Ambil semua penilaian yang terkait dengan kriteria K001
        // Termasuk penilaian dari indikator, sub-indikator, dan sub-sub-indikator
        $penilaian = Penilaian::where(function ($query) use ($kriteria) {
            // Penilaian dari indikator yang terkait dengan kriteria K001
            $query->where('penilaian_type', 'App\\Models\\Indikator')
                  ->whereIn('penilaian_id', function ($subQuery) use ($kriteria) {
                      $subQuery->select('id')
                               ->from('indikator')
                               ->where('kriteria_id', $kriteria->id);
                  });
        })
        ->orWhere(function ($query) use ($kriteria) {
            // Penilaian dari sub-indikator yang indikatornya terkait dengan kriteria K001
            $query->where('penilaian_type', 'App\\Models\\SubIndikator')
                  ->whereIn('penilaian_id', function ($subQuery) use ($kriteria) {
                      $subQuery->select('si.id')
                               ->from('sub_indikator as si')
                               ->join('indikator as i', 'si.indikator_id', '=', 'i.id')
                               ->where('i.kriteria_id', $kriteria->id);
                  });
        })
        ->orWhere(function ($query) use ($kriteria) {
            // Penilaian dari sub-sub-indikator yang sub-indikatornya terkait dengan kriteria K001
            $query->where('penilaian_type', 'App\\Models\\SubSubIndikator')
                  ->whereIn('penilaian_id', function ($subQuery) use ($kriteria) {
                      $subQuery->select('ssi.id')
                               ->from('sub_sub_indikator as ssi')
                               ->join('sub_indikator as si', 'ssi.sub_indikator_id', '=', 'si.id')
                               ->join('indikator as i', 'si.indikator_id', '=', 'i.id')
                               ->where('i.kriteria_id', $kriteria->id);
                  });
        })
        ->with(['dosen', 'penilaian'])
        ->get();

        // Hitung persentase untuk setiap dosen
        $hasilPerhitungan = $this->hitungPersentaseDosen($penilaian);

        // Hitung skala interval berdasarkan distribusi data
        $skalaIntervalDinamis = $this->hitungSkalaIntervalDinamis($hasilPerhitungan);

        // Update skala interval untuk setiap dosen berdasarkan data
        $hasilPerhitungan = $this->updateSkalaIntervalDosen($hasilPerhitungan, $skalaIntervalDinamis);

        // Hitung statistik skala interval
        $statistikSkala = $this->hitungStatistikSkala($hasilPerhitungan);

        return response()->json([
            'kriteria' => $kriteria,
            'skala_interval_referensi_tetap' => [
                ['range' => '81% - 100%', 'variabel' => 'Sangat tinggi', 'nilai_decimal' => 5.00000],
                ['range' => '61% - 80%', 'variabel' => 'Tinggi', 'nilai_decimal' => 4.00000],
                ['range' => '41% - 60%', 'variabel' => 'Sedang', 'nilai_decimal' => 3.00000],
                ['range' => '21% - 40%', 'variabel' => 'Rendah', 'nilai_decimal' => 2.00000],
                ['range' => '0% - 20%', 'variabel' => 'Sangat rendah', 'nilai_decimal' => 1.00000]
            ],
            'skala_interval_dinamis' => $skalaIntervalDinamis,
            'statistik_skala_interval' => $statistikSkala,
            'hasil_perhitungan' => $hasilPerhitungan
        ]);
    }

    private function hitungPersentaseDosen($penilaianData)
    {
        // Group penilaian berdasarkan dosen_id
        $penilaianPerDosen = $penilaianData->groupBy('dosen_id');

        $hasilPerhitungan = [];

        foreach ($penilaianPerDosen as $dosenId => $penilaianDosen) {
            $dosen = $penilaianDosen->first()->dosen;

            // Hitung skor berdasarkan SubSubIndikator (kategori nilai)
            $hasilKalkulasi = $this->hitungSkorSubSubIndikator($penilaianDosen);

            $skorActual = $hasilKalkulasi['skor_actual'];
            $skorMin = $hasilKalkulasi['skor_min'];
            $skorMax = $hasilKalkulasi['skor_max'];
            $detailKategori = $hasilKalkulasi['detail_kategori'];

            // Hitung persentase menggunakan formula:
            // Nilai dalam % = (skor actual – skor min absolut / skor maksimal - skor min) * 100%
            $persentase = 0;
            if ($skorMax > $skorMin) {
                $persentase = (($skorActual - $skorMin) / ($skorMax - $skorMin)) * 100;
                // Pastikan persentase tidak negatif dan tidak lebih dari 100%
                $persentase = max(0, min(100, $persentase));
            }

            // Skala interval akan diupdate nanti berdasarkan distribusi data
            $hasilPerhitungan[] = [
                'dosen_id' => $dosenId,
                'dosen_nama' => $dosen->nama_dosen ?? 'Unknown',
                'dosen_nidn' => $dosen->nidn ?? '-',
                'dosen_prodi' => $dosen->prodi ?? '-',
                'skor_actual' => $skorActual,
                'skor_min' => $skorMin,
                'skor_max' => $skorMax,
                'persentase' => round($persentase, 2),
                'total_responden' => $hasilKalkulasi['total_responden'],
                'ringkasan_kategori' => $detailKategori['ringkasan'],
                'detail_penilaian' => $detailKategori['detail']
            ];
        }

        // Urutkan berdasarkan persentase tertinggi
        usort($hasilPerhitungan, function ($a, $b) {
            return $b['persentase'] <=> $a['persentase'];
        });

        return $hasilPerhitungan;
    }

    private function hitungSkorSubSubIndikator($penilaianDosen)
    {
        $skorActual = 0;
        $skorMin = 0;
        $skorMax = 0;
        $totalResponden = 0;

        // Untuk ringkasan kategori
        $kategoriCount = [
            'sangat_baik' => 0,
            'baik' => 0,
            'cukup_baik' => 0,
            'kurang_baik' => 0
        ];

        $detailPenilaian = [];

        foreach ($penilaianDosen as $penilaian) {
            if ($penilaian->penilaian_type === 'App\\Models\\SubSubIndikator') {
                $subSubIndikator = SubSubIndikator::find($penilaian->penilaian_id);

                if ($subSubIndikator) {
                    $jumlahResponden = $penilaian->nilai; // Jumlah responden yang memilih kategori ini
                    $skorKredit = $subSubIndikator->skor_kredit;
                    $kategori = $subSubIndikator->nama_sub_sub_indikator;

                    // Hitung skor actual: jumlah responden × skor kredit
                    $skorItem = $jumlahResponden * $skorKredit;
                    $skorActual += $skorItem;

                    // Total responden
                    $totalResponden += $jumlahResponden;

                    // Hitung skor minimum (semua responden memilih "kurang baik" = 1)
                    $skorMin += $jumlahResponden * 1;

                    // Hitung skor maksimum (semua responden memilih "sangat baik" = 4)
                    $skorMax += $jumlahResponden * 4;

                    // Kategorikan berdasarkan nama
                    switch (strtolower(trim($kategori))) {
                        case 'sangat baik':
                            $kategoriCount['sangat_baik'] += $jumlahResponden;
                            break;
                        case 'baik':
                            $kategoriCount['baik'] += $jumlahResponden;
                            break;
                        case 'cukup baik':
                            $kategoriCount['cukup_baik'] += $jumlahResponden;
                            break;
                        case 'kurang baik':
                            $kategoriCount['kurang_baik'] += $jumlahResponden;
                            break;
                    }

                    $detailPenilaian[] = [
                        'id' => $penilaian->id,
                        'sub_indikator_id' => $subSubIndikator->sub_indikator_id,
                        'kategori' => $kategori,
                        'jumlah_responden' => $jumlahResponden,
                        'skor_kredit' => $skorKredit,
                        'skor_item' => $skorItem,
                        'sub_indikator_nama' => $this->getNamaSubIndikator($subSubIndikator->sub_indikator_id)
                    ];
                }
            } else {
                // Untuk tipe penilaian lain (Indikator, SubIndikator) gunakan metode lama
                $skorActual += $penilaian->nilai;
                $skorMaxItem = $this->getSkorMaksimum($penilaian);
                $skorMax += $skorMaxItem;

                $detailPenilaian[] = [
                    'id' => $penilaian->id,
                    'tipe' => $penilaian->penilaian_type,
                    'nilai' => $penilaian->nilai,
                    'skor_max_item' => $skorMaxItem,
                    'nama_item' => $this->getNamaItem($penilaian)
                ];
            }
        }

        return [
            'skor_actual' => $skorActual,
            'skor_min' => $skorMin,
            'skor_max' => $skorMax,
            'total_responden' => $totalResponden,
            'detail_kategori' => [
                'ringkasan' => $kategoriCount,
                'detail' => $detailPenilaian
            ]
        ];
    }

    private function getNamaSubIndikator($subIndikatorId)
    {
        $subIndikator = SubIndikator::find($subIndikatorId);
        return $subIndikator ? $subIndikator->nama_sub_indikator : 'Unknown SubIndikator';
    }

    private function getSkorMaksimum($penilaian)
    {
        switch ($penilaian->penilaian_type) {
            case 'App\\Models\\SubIndikator':
                $subIndikator = SubIndikator::find($penilaian->penilaian_id);
                return $subIndikator->skor_kredit ?? 50; // Default jika tidak ada skor_kredit

            case 'App\\Models\\SubSubIndikator':
                $subSubIndikator = SubSubIndikator::find($penilaian->penilaian_id);
                return $subSubIndikator->skor_kredit ?? 50; // Default jika tidak ada skor_kredit

            case 'App\\Models\\Indikator':
                // Untuk indikator, bisa menggunakan logika khusus atau default
                return 50; // Default skor maksimum untuk indikator

            default:
                return 50; // Default fallback
        }
    }

    private function getNamaItem($penilaian)
    {
        switch ($penilaian->penilaian_type) {
            case 'App\\Models\\SubIndikator':
                $subIndikator = SubIndikator::find($penilaian->penilaian_id);
                return $subIndikator->nama_sub_indikator ?? 'Unknown SubIndikator';

            case 'App\\Models\\SubSubIndikator':
                $subSubIndikator = SubSubIndikator::find($penilaian->penilaian_id);
                return $subSubIndikator->nama_sub_sub_indikator ?? 'Unknown SubSubIndikator';

            case 'App\\Models\\Indikator':
                $indikator = Indikator::find($penilaian->penilaian_id);
                return $indikator->nama_indikator ?? 'Unknown Indikator';

            default:
                return 'Unknown Item';
        }
    }

    private function hitungStatistikSkala($hasilPerhitungan)
    {
        $statistik = [
            'sangat_tinggi' => ['count' => 0, 'dosen' => []],
            'tinggi' => ['count' => 0, 'dosen' => []],
            'sedang' => ['count' => 0, 'dosen' => []],
            'rendah' => ['count' => 0, 'dosen' => []],
            'sangat_rendah' => ['count' => 0, 'dosen' => []]
        ];

        $totalDosen = count($hasilPerhitungan);
        $totalPersentase = 0;

        foreach ($hasilPerhitungan as $hasil) {
            $variabel = strtolower(str_replace(' ', '_', $hasil['skala_interval']['variabel']));
            $totalPersentase += $hasil['persentase'];

            if (isset($statistik[$variabel])) {
                $statistik[$variabel]['count']++;
                $statistik[$variabel]['dosen'][] = [
                    'nama' => $hasil['dosen_nama'],
                    'nidn' => $hasil['dosen_nidn'],
                    'persentase' => $hasil['persentase']
                ];
            }
        }

        // Hitung persentase distribusi dan rata-rata
        $distribusi = [];
        foreach ($statistik as $key => $data) {
            $persentaseDistribusi = $totalDosen > 0 ? round(($data['count'] / $totalDosen) * 100, 2) : 0;
            $distribusi[$key] = [
                'jumlah_dosen' => $data['count'],
                'persentase_distribusi' => $persentaseDistribusi,
                'dosen' => $data['dosen']
            ];
        }

        $rataRataPersentase = $totalDosen > 0 ? round($totalPersentase / $totalDosen, 2) : 0;

        return [
            'total_dosen' => $totalDosen,
            'rata_rata_persentase' => $rataRataPersentase,
            'distribusi_skala' => $distribusi,
            'ringkasan' => [
                'terbaik' => $totalDosen > 0 ? $hasilPerhitungan[0] : null,
                'terlemah' => $totalDosen > 0 ? $hasilPerhitungan[$totalDosen - 1] : null
            ]
        ];
    }

    private function tentukanSkalaInterval($persentase)
    {
        if ($persentase >= 81 && $persentase <= 100) {
            return [
                'range' => '81% - 100%',
                'variabel' => 'Sangat tinggi',
                'nilai_decimal' => 5.00000,
                'persentase' => $persentase
            ];
        } elseif ($persentase >= 61 && $persentase < 81) {
            return [
                'range' => '61% - 80%',
                'variabel' => 'Tinggi',
                'nilai_decimal' => 4.00000,
                'persentase' => $persentase
            ];
        } elseif ($persentase >= 41 && $persentase < 61) {
            return [
                'range' => '41% - 60%',
                'variabel' => 'Sedang',
                'nilai_decimal' => 3.00000,
                'persentase' => $persentase
            ];
        } elseif ($persentase >= 21 && $persentase < 41) {
            return [
                'range' => '21% - 40%',
                'variabel' => 'Rendah',
                'nilai_decimal' => 2.00000,
                'persentase' => $persentase
            ];
        } else { // 0% - 20%
            return [
                'range' => '0% - 20%',
                'variabel' => 'Sangat rendah',
                'nilai_decimal' => 1.00000,
                'persentase' => $persentase
            ];
        }
    }

    private function hitungSkalaIntervalDinamis($hasilPerhitungan)
    {
        if (empty($hasilPerhitungan)) {
            return $this->getSkalaIntervalDefault();
        }

        // Ambil semua persentase dan urutkan
        $persentaseData = array_column($hasilPerhitungan, 'persentase');
        sort($persentaseData);

        $totalData = count($persentaseData);

        // Hitung quintiles (pembagian 5 bagian sama besar)
        $q1Index = intval($totalData * 0.2); // 20% data terendah
        $q2Index = intval($totalData * 0.4); // 40% data terendah
        $q3Index = intval($totalData * 0.6); // 60% data terendah
        $q4Index = intval($totalData * 0.8); // 80% data terendah

        // Ambil nilai batas untuk setiap quintile
        $batas1 = $q1Index > 0 ? $persentaseData[$q1Index - 1] : $persentaseData[0];
        $batas2 = $q2Index > 0 ? $persentaseData[$q2Index - 1] : $persentaseData[0];
        $batas3 = $q3Index > 0 ? $persentaseData[$q3Index - 1] : $persentaseData[0];
        $batas4 = $q4Index > 0 ? $persentaseData[$q4Index - 1] : $persentaseData[0];

        $min = min($persentaseData);
        $max = max($persentaseData);

        return [
            [
                'range' => round($batas4, 2) . '% - ' . round($max, 2) . '%',
                'variabel' => 'Sangat tinggi',
                'nilai_decimal' => 5.00000,
                'batas_min' => $batas4,
                'batas_max' => $max,
                'jumlah_data' => $totalData - $q4Index
            ],
            [
                'range' => round($batas3, 2) . '% - ' . round($batas4, 2) . '%',
                'variabel' => 'Tinggi',
                'nilai_decimal' => 4.00000,
                'batas_min' => $batas3,
                'batas_max' => $batas4,
                'jumlah_data' => $q4Index - $q3Index
            ],
            [
                'range' => round($batas2, 2) . '% - ' . round($batas3, 2) . '%',
                'variabel' => 'Sedang',
                'nilai_decimal' => 3.00000,
                'batas_min' => $batas2,
                'batas_max' => $batas3,
                'jumlah_data' => $q3Index - $q2Index
            ],
            [
                'range' => round($batas1, 2) . '% - ' . round($batas2, 2) . '%',
                'variabel' => 'Rendah',
                'nilai_decimal' => 2.00000,
                'batas_min' => $batas1,
                'batas_max' => $batas2,
                'jumlah_data' => $q2Index - $q1Index
            ],
            [
                'range' => round($min, 2) . '% - ' . round($batas1, 2) . '%',
                'variabel' => 'Sangat rendah',
                'nilai_decimal' => 1.00000,
                'batas_min' => $min,
                'batas_max' => $batas1,
                'jumlah_data' => $q1Index
            ]
        ];
    }

    private function getSkalaIntervalDefault()
    {
        return [
            ['range' => '81% - 100%', 'variabel' => 'Sangat tinggi', 'nilai_decimal' => 5.00000, 'batas_min' => 81, 'batas_max' => 100],
            ['range' => '61% - 80%', 'variabel' => 'Tinggi', 'nilai_decimal' => 4.00000, 'batas_min' => 61, 'batas_max' => 80],
            ['range' => '41% - 60%', 'variabel' => 'Sedang', 'nilai_decimal' => 3.00000, 'batas_min' => 41, 'batas_max' => 60],
            ['range' => '21% - 40%', 'variabel' => 'Rendah', 'nilai_decimal' => 2.00000, 'batas_min' => 21, 'batas_max' => 40],
            ['range' => '0% - 20%', 'variabel' => 'Sangat rendah', 'nilai_decimal' => 1.00000, 'batas_min' => 0, 'batas_max' => 20]
        ];
    }

    private function updateSkalaIntervalDosen($hasilPerhitungan, $skalaIntervalDinamis)
    {
        foreach ($hasilPerhitungan as &$hasil) {
            $persentase = $hasil['persentase'];

            // Tentukan skala berdasarkan batas dinamis
            foreach ($skalaIntervalDinamis as $skala) {
                if ($persentase >= $skala['batas_min'] && $persentase <= $skala['batas_max']) {
                    $hasil['skala_interval'] = [
                        'range' => $skala['range'],
                        'variabel' => $skala['variabel'],
                        'nilai_decimal' => $skala['nilai_decimal'],
                        'persentase' => $persentase,
                        'metode' => 'dinamis'
                    ];
                    break;
                }
            }

            // Fallback jika tidak ada yang cocok
            if (!isset($hasil['skala_interval']['metode'])) {
                $hasil['skala_interval'] = $this->tentukanSkalaInterval($persentase);
                $hasil['skala_interval']['metode'] = 'tetap';
            }
        }

        return $hasilPerhitungan;
    }

    private function tentukanSkalaIntervalDinamis($persentase, $skalaIntervalDinamis)
    {
        foreach ($skalaIntervalDinamis as $skala) {
            if ($persentase >= $skala['batas_min'] && $persentase <= $skala['batas_max']) {
                return [
                    'range' => $skala['range'],
                    'variabel' => $skala['variabel'],
                    'nilai_decimal' => $skala['nilai_decimal'],
                    'persentase' => $persentase,
                    'metode' => 'dinamis'
                ];
            }
        }

        // Fallback ke metode tetap
        $result = $this->tentukanSkalaInterval($persentase);
        $result['metode'] = 'tetap';
        return $result;
    }
}
