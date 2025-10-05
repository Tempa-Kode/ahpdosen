<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Indikator; // Tambahkan ini
use App\Models\Kriteria;
use App\Models\SubIndikator;
use App\Models\SubSubIndikator;
use App\Models\SubSubSubIndikator;
use Illuminate\Http\Request;
use Revolution\Google\Sheets\Facades\Sheets;

class PenilaianController extends Controller
{
    /**
     * Menampilkan form untuk input/edit nilai seorang dosen.
     */
    public function form(Dosen $dosen)
    {
        $kriterias = Kriteria::with([
            // Eager load relasi secara bersarang
            'indikator' => function ($query) use ($dosen) {
                // Ambil penilaian untuk indikator itu sendiri
                $query->with(['penilaians' => fn($q) => $q->where('dosen_id', $dosen->id)]);
            },
            'indikator.subIndikator' => function ($query) use ($dosen) {
                // Ambil penilaian untuk sub-indikator
                $query->with(['penilaians' => fn($q) => $q->where('dosen_id', $dosen->id)]);
            },
            'indikator.subIndikator.subSubIndikator' => function ($query) use ($dosen) {
                // Ambil penilaian untuk sub-sub-indikator
                $query->with(['penilaians' => fn($q) => $q->where('dosen_id', $dosen->id)]);
            },
            'indikator.subIndikator.subSubIndikator.subSubSubIndikator' => function ($query) use ($dosen) {
                // Ambil penilaian untuk sub-sub-sub-indikator
                $query->with(['penilaians' => fn($q) => $q->where('dosen_id', $dosen->id)]);
            }
        ])->get();

        return view('penilaian.form', compact('dosen', 'kriterias'));
    }

    /**
     * Menyimpan atau memperbarui nilai untuk seorang dosen.
     */
    public function store(Request $request, Dosen $dosen)
    {
        // dd($request->all());
        $request->validate([
            'nilai' => 'sometimes|array',
            'nilai.*' => 'nullable|array',
            'nilai.*.*' => 'nullable|numeric|min:0',
        ]);

        // 1. Proses nilai untuk Indikator
        if ($request->has('nilai.indikator')) {
            foreach ($request->nilai['indikator'] as $id => $nilai) {
                if (!is_null($nilai)) {
                    Indikator::find($id)->penilaians()->updateOrCreate(
                        ['dosen_id' => $dosen->id],
                        ['nilai' => $nilai]
                    );
                }
            }
        }

        // 2. Proses nilai untuk SubIndikator
        if ($request->has('nilai.sub_indikator')) {
            foreach ($request->nilai['sub_indikator'] as $id => $nilai) {
                if (!is_null($nilai)) {
                    SubIndikator::find($id)->penilaians()->updateOrCreate(
                        ['dosen_id' => $dosen->id],
                        ['nilai' => $nilai]
                    );
                }
            }
        }

        // 3. Proses nilai untuk SubSubIndikator
        if ($request->has('nilai.sub_sub_indikator')) {
            foreach ($request->nilai['sub_sub_indikator'] as $id => $nilai) {
                if (!is_null($nilai)) {
                    SubSubIndikator::find($id)->penilaians()->updateOrCreate(
                        ['dosen_id' => $dosen->id],
                        ['nilai' => $nilai]
                    );
                }
            }
        }

        // 4. Proses nilai untuk SubSubSubIndikator
        if ($request->has('nilai.sub_sub_sub_indikator')) {
            foreach ($request->nilai['sub_sub_sub_indikator'] as $id => $nilai) {
                if (!is_null($nilai)) {
                    SubSubSubIndikator::find($id)->penilaians()->updateOrCreate(
                        ['dosen_id' => $dosen->id],
                        ['nilai' => $nilai]
                    );
                }
            }
        }

        return back()->with('success', 'Nilai untuk ' . $dosen->nama . ' berhasil diperbarui.');
    }

    public function getDataSpreadSheet($nidn)
    {
        try {
            $values = Sheets::spreadsheet('1CmS-dIZWZkPq7FHSIICsmQj-z5OJhv1jch-cSXiuXzc')
                ->sheet($nidn)
                ->all();

            // Proses data spreadsheet
            $processedData = $this->processSpreadsheetData($values);

            return response()->json([
                'status' => 'success',
                'data' => $processedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function processSpreadsheetData($values)
    {
        if (empty($values) || count($values) < 2) {
            return [
                'questions' => [],
                'summary' => [],
                'responses' => []
            ];
        }

        // Ambil baris pertama (header/questions)
        $rawQuestions = $values[0];

        // Bersihkan nomor dari setiap pertanyaan
        $questions = array_map(function($question) {
            // Hapus nomor di awal (contoh: "1. ", "10. ", dll)
            return preg_replace('/^\d+\.\s*/', '', trim($question));
        }, $rawQuestions);

        // Ambil data jawaban (skip header)
        $responses = array_slice($values, 1);

        // Inisialisasi summary untuk setiap pertanyaan
        $summary = [];
        foreach ($questions as $index => $question) {
            $summary[$index] = [
                'question' => $question,
                'sangat_baik' => 0,
                'baik' => 0,
                'cukup_baik' => 0,
                'kurang_baik' => 0,
                'total_responses' => 0
            ];
        }

        // Hitung setiap jawaban
        foreach ($responses as $response) {
            foreach ($response as $questionIndex => $answer) {
                if (isset($summary[$questionIndex])) {
                    $normalizedAnswer = strtolower(trim($answer));

                    switch ($normalizedAnswer) {
                        case 'sangat baik':
                            $summary[$questionIndex]['sangat_baik']++;
                            break;
                        case 'baik':
                            $summary[$questionIndex]['baik']++;
                            break;
                        case 'cukup baik':
                            $summary[$questionIndex]['cukup_baik']++;
                            break;
                        case 'kurang baik':
                            $summary[$questionIndex]['kurang_baik']++;
                            break;
                    }
                    $summary[$questionIndex]['total_responses']++;
                }
            }
        }

        // Hitung persentase dan skor untuk setiap pertanyaan
        foreach ($summary as $index => &$item) {
            $total = $item['total_responses'];
            if ($total > 0) {
                $item['percentages'] = [
                    'sangat_baik' => round(($item['sangat_baik'] / $total) * 100, 1),
                    'baik' => round(($item['baik'] / $total) * 100, 1),
                    'cukup_baik' => round(($item['cukup_baik'] / $total) * 100, 1),
                    'kurang_baik' => round(($item['kurang_baik'] / $total) * 100, 1),
                ];

                // Hitung skor weighted (Sangat Baik=4, Baik=3, Cukup Baik=2, Kurang Baik=1)
                $weightedScore = ($item['sangat_baik'] * 4) +
                               ($item['baik'] * 3) +
                               ($item['cukup_baik'] * 2) +
                               ($item['kurang_baik'] * 1);
                $item['average_score'] = round($weightedScore / $total, 2);
                $item['grade'] = $this->getGrade($item['average_score']);
            } else {
                $item['percentages'] = [
                    'sangat_baik' => 0, 'baik' => 0, 'cukup_baik' => 0, 'kurang_baik' => 0
                ];
                $item['average_score'] = 0;
                $item['grade'] = 'N/A';
            }
        }

        // Hitung overall summary
        $overallSummary = [
            'sangat_baik' => array_sum(array_column($summary, 'sangat_baik')),
            'baik' => array_sum(array_column($summary, 'baik')),
            'cukup_baik' => array_sum(array_column($summary, 'cukup_baik')),
            'kurang_baik' => array_sum(array_column($summary, 'kurang_baik')),
            'total_responses' => count($responses),
            'total_questions' => count($questions)
        ];

        $totalAnswers = $overallSummary['sangat_baik'] + $overallSummary['baik'] +
                       $overallSummary['cukup_baik'] + $overallSummary['kurang_baik'];

        if ($totalAnswers > 0) {
            $overallWeightedScore = ($overallSummary['sangat_baik'] * 4) +
                                  ($overallSummary['baik'] * 3) +
                                  ($overallSummary['cukup_baik'] * 2) +
                                  ($overallSummary['kurang_baik'] * 1);
            $overallSummary['average_score'] = round($overallWeightedScore / $totalAnswers, 2);
            $overallSummary['grade'] = $this->getGrade($overallSummary['average_score']);
        } else {
            $overallSummary['average_score'] = 0;
            $overallSummary['grade'] = 'N/A';
        }

        return [
            'questions' => $questions,
            'summary' => array_values($summary), // Reset array keys
            'overall_summary' => $overallSummary,
            'responses' => $responses,
            'total_responses' => count($responses)
        ];
    }

    private function getGrade($score)
    {
        if ($score >= 3.5) return 'A (Sangat Baik)';
        if ($score >= 3.0) return 'B (Baik)';
        if ($score >= 2.5) return 'C (Cukup)';
        return 'D (Kurang)';
    }

    public function mapDataToForm(Request $request)
    {
        try {
            $spreadsheetData = $request->input('spreadsheet_data');
            $dosenId = $request->input('dosen_id');

            // Mapping data spreadsheet ke form fields
            $mappedData = $this->mapQuestionsToFormFields($spreadsheetData);

            return response()->json([
                'status' => 'success',
                'mapped_data' => $mappedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function mapQuestionsToFormFields($spreadsheetData)
    {
        // Definisi mapping pertanyaan ke field form
        $questionMapping = [
            'ketersediaan perangkat pembelajaran' => 'field_rps',
            'materi kuliah dikemas' => 'field_motivasi',
            'materi kuliah diberikan sesuai' => 'field_sesuai_rps',
            'penilaian hasil belajar' => 'field_penilaian_objektif',
            'mengawali dan mengakhiri' => 'field_tepat_waktu',
            'jumlah perkuliahan 14 kali' => 'field_jumlah_perkuliahan',
            'struktur materi' => 'field_struktur_runut',
            'menguasai materi' => 'field_penguasaan_materi',
            'memberikan contoh' => 'field_contoh_aplikatif',
            'menyampaikan materi' => 'field_metode_efektif',
            'memanfaatkan media' => 'field_media_teknologi',
            'up to date' => 'field_materi_update',
            'kesesuaian materi' => 'field_kesesuaian_ujian',
            'umpan balik' => 'field_feedback',
            'arif dalam mengambil' => 'field_arif_keputusan',
            'memberikan keteladanan' => 'field_keteladanan',
            'bersikap sesuai norma' => 'field_norma',
            'adil dalam memperlakukan' => 'field_adil',
            'berkomunikasi lisan' => 'field_komunikasi',
            'sikap terbuka' => 'field_terbuka_kritik',
            'interaksi dengan mahasiswa' => 'field_interaksi',
            'toleran terhadap keberagaman' => 'field_toleransi'
        ];

        $mappedData = [];

        foreach ($spreadsheetData['summary'] as $index => $questionData) {
            $question = strtolower($questionData['question']);

            // Cari field yang cocok
            $fieldName = null;
            foreach ($questionMapping as $keyword => $field) {
                if (strpos($question, $keyword) !== false) {
                    $fieldName = $field;
                    break;
                }
            }

            if ($fieldName) {
                $mappedData[$fieldName] = [
                    'question' => $questionData['question'],
                    'average_score' => $questionData['average_score'],
                    'grade' => $questionData['grade'],
                    'counts' => [
                        'sangat_baik' => $questionData['sangat_baik'],
                        'baik' => $questionData['baik'],
                        'cukup_baik' => $questionData['cukup_baik'],
                        'kurang_baik' => $questionData['kurang_baik']
                    ],
                    'percentages' => $questionData['percentages']
                ];
            }
        }

        return $mappedData;
    }
}
