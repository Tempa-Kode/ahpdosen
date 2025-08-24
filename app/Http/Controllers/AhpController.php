<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kriteria;
use App\Models\Indikator;
use App\Models\SubIndikator;
use App\Models\SubSubIndikator;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AhpController extends Controller
{
    public function dashboard()
    {
        $dosens = Dosen::all();
        $kriterias = Kriteria::all();

        // Hitung ranking untuk semua dosen
        $rankings = $this->calculateAhpRanking();

        return view('dashboard.ahp.index', compact('dosens', 'kriterias', 'rankings'));
    }

    public function debug()
    {
        // Method untuk debugging masalah perhitungan
        $dosen = Dosen::first();
        $kriteria = Kriteria::first();

        if (!$dosen || !$kriteria) {
            return response()->json(['error' => 'No data found']);
        }

        $penilaianCount = Penilaian::where('dosen_id', $dosen->id)->count();
        $indikatorCount = $kriteria->indikator()->count();

        // Cek penilaian spesifik
        $penilaianSample = Penilaian::where('dosen_id', $dosen->id)->take(5)->get();

        // Cek indikator untuk kriteria ini
        $indikatorSample = $kriteria->indikator()->with('subIndikator.subSubIndikator')->take(2)->get();

        $debugInfo = [
            'dosen' => $dosen->nama_dosen,
            'penilaian_count' => $penilaianCount,
            'kriteria' => $kriteria->nama_kriteria,
            'indikator_count' => $indikatorCount,
            'sample_penilaian' => $penilaianSample,
            'sample_indikator' => $indikatorSample,
            'kriteria_score' => $this->calculateKriteriaScore($dosen->id, $kriteria),
            'all_kriterias' => Kriteria::all(),
            'penilaian_summary' => [
                'total_penilaian' => Penilaian::count(),
                'unique_dosen' => Penilaian::distinct('dosen_id')->count(),
                'penilaian_types' => Penilaian::distinct('penilaian_type')->pluck('penilaian_type')
            ]
        ];

        return response()->json($debugInfo, JSON_PRETTY_PRINT);
    }

    public function detail($dosenId)
    {
        $dosen = Dosen::findOrFail($dosenId);
        $detailCalculation = $this->calculateDosenDetail($dosenId);

        return view('dashboard.ahp.detail', compact('dosen', 'detailCalculation'));
    }

    public function calculateAhpRanking()
    {
        $dosens = Dosen::all();
        $kriterias = Kriteria::all();
        $rankings = [];

        foreach ($dosens as $dosen) {
            $totalScore = 0;
            $kriteriaScores = [];

            foreach ($kriterias as $kriteria) {
                $kriteriaScore = $this->calculateKriteriaScore($dosen->id, $kriteria);
                $kriteriaScores[$kriteria->nama_kriteria] = $kriteriaScore;
                $totalScore += $kriteriaScore * $kriteria->bobot;
            }

            $rankings[] = [
                'dosen' => $dosen,
                'total_score' => $totalScore,
                'kriteria_scores' => $kriteriaScores,
                'rank' => 0 // akan diisi setelah sorting
            ];
        }

        // Sort berdasarkan total score
        usort($rankings, function($a, $b) {
            return $b['total_score'] <=> $a['total_score'];
        });

        // Assign ranking
        foreach ($rankings as $index => &$ranking) {
            $ranking['rank'] = $index + 1;
        }

        return $rankings;
    }

    private function calculateKriteriaScore($dosenId, $kriteria)
    {
        $indikators = $kriteria->indikator;
        $totalScore = 0;
        $totalBobot = 0;
        $countIndikator = 0;

        foreach ($indikators as $indikator) {
            if ($indikator->bobot_indikator && $indikator->bobot_indikator > 0) {
                $indikatorScore = $this->calculateIndikatorScore($dosenId, $indikator);
                $totalScore += $indikatorScore * $indikator->bobot_indikator;
                $totalBobot += $indikator->bobot_indikator;
            } else {
                // Jika tidak ada bobot indikator, hitung dari sub indikator atau penilaian langsung
                $indikatorScore = $this->calculateIndikatorScore($dosenId, $indikator);
                $totalScore += $indikatorScore;
                $countIndikator++;
            }
        }

        // Return weighted average jika ada bobot, atau simple average jika tidak ada bobot
        if ($totalBobot > 0) {
            return $totalScore; // sudah weighted
        } else {
            return $countIndikator > 0 ? $totalScore / $countIndikator : 0;
        }
    }

    private function calculateIndikatorScore($dosenId, $indikator)
    {
        // Cek apakah ada penilaian langsung untuk indikator
        $penilaian = Penilaian::where('dosen_id', $dosenId)
            ->where('penilaian_type', 'App\Models\Indikator')
            ->where('penilaian_id', $indikator->id)
            ->first();

        if ($penilaian) {
            return $this->normalizeScore($penilaian->nilai);
        }

        // Jika tidak ada, hitung dari sub indikator
        $subIndikators = $indikator->subIndikator;
        $totalScore = 0;
        $count = 0;

        foreach ($subIndikators as $subIndikator) {
            $subScore = $this->calculateSubIndikatorScore($dosenId, $subIndikator);
            $totalScore += $subScore;
            $count++;
        }

        return $count > 0 ? $totalScore / $count : 0;
    }

    private function calculateSubIndikatorScore($dosenId, $subIndikator)
    {
        // Cek apakah ada penilaian langsung untuk sub indikator
        $penilaian = Penilaian::where('dosen_id', $dosenId)
            ->where('penilaian_type', 'App\Models\SubIndikator')
            ->where('penilaian_id', $subIndikator->id)
            ->first();

        if ($penilaian) {
            return $this->normalizeScore($penilaian->nilai);
        }

        // Jika tidak ada, hitung dari sub sub indikator
        $subSubIndikators = $subIndikator->subSubIndikator;
        $totalScore = 0;
        $count = 0;

        foreach ($subSubIndikators as $subSubIndikator) {
            $subSubScore = $this->calculateSubSubIndikatorScore($dosenId, $subSubIndikator);
            $totalScore += $subSubScore;
            $count++;
        }

        return $count > 0 ? $totalScore / $count : 0;
    }

    private function calculateSubSubIndikatorScore($dosenId, $subSubIndikator)
    {
        $penilaian = Penilaian::where('dosen_id', $dosenId)
            ->where('penilaian_type', 'App\Models\SubSubIndikator')
            ->where('penilaian_id', $subSubIndikator->id)
            ->first();

        return $penilaian ? $this->normalizeScore($penilaian->nilai) : 0;
    }

    private function normalizeScore($score)
    {
        // Normalisasi skor ke skala 0-1
        // Untuk AHP, kita gunakan skala yang lebih realistis
        $maxScore = 100; // sesuaikan dengan skala maksimal di data
        return min($score / $maxScore, 1);
    }

    public function calculateDosenDetail($dosenId)
    {
        $dosen = Dosen::findOrFail($dosenId);
        $kriterias = Kriteria::all();
        $detail = [];

        foreach ($kriterias as $kriteria) {
            $kriteriaDetail = [
                'kriteria' => $kriteria,
                'score' => $this->calculateKriteriaScore($dosenId, $kriteria),
                'weighted_score' => $this->calculateKriteriaScore($dosenId, $kriteria) * $kriteria->bobot,
                'indikators' => []
            ];

            foreach ($kriteria->indikator as $indikator) {
                $indikatorDetail = [
                    'indikator' => $indikator,
                    'score' => $this->calculateIndikatorScore($dosenId, $indikator),
                    'weighted_score' => $this->calculateIndikatorScore($dosenId, $indikator) * ($indikator->bobot_indikator ?? 1),
                    'sub_indikators' => []
                ];

                foreach ($indikator->subIndikator as $subIndikator) {
                    $subIndikatorDetail = [
                        'sub_indikator' => $subIndikator,
                        'score' => $this->calculateSubIndikatorScore($dosenId, $subIndikator),
                        'sub_sub_indikators' => []
                    ];

                    foreach ($subIndikator->subSubIndikator as $subSubIndikator) {
                        $penilaian = Penilaian::where('dosen_id', $dosenId)
                            ->where('penilaian_type', 'App\Models\SubSubIndikator')
                            ->where('penilaian_id', $subSubIndikator->id)
                            ->first();

                        $subIndikatorDetail['sub_sub_indikators'][] = [
                            'sub_sub_indikator' => $subSubIndikator,
                            'raw_score' => $penilaian ? $penilaian->nilai : 0,
                            'normalized_score' => $this->calculateSubSubIndikatorScore($dosenId, $subSubIndikator)
                        ];
                    }

                    $indikatorDetail['sub_indikators'][] = $subIndikatorDetail;
                }

                $kriteriaDetail['indikators'][] = $indikatorDetail;
            }

            $detail[] = $kriteriaDetail;
        }

        return $detail;
    }

    public function comparison()
    {
        $rankings = $this->calculateAhpRanking();

        // Ambil top 5 untuk perbandingan
        $topDosens = array_slice($rankings, 0, 5);
        $kriterias = Kriteria::all();

        return view('dashboard.ahp.comparison', compact('topDosens', 'kriterias'));
    }
}
