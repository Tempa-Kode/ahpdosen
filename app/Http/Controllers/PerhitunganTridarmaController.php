<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Indikator;
use App\Models\Penilaian;
use App\Models\SubIndikator;
use Illuminate\Http\Request;
use App\Models\SubSubIndikator;
use Illuminate\Http\JsonResponse;

class PerhitunganTridarmaController extends Controller
{
    protected function getPenilaianData(string $kd = 'K004'): array
    {
        $kriteria = Kriteria::where('kd_kriteria', $kd)->first();
        if (! $kriteria) {
            return ['error' => "Kriteria {$kd} tidak ditemukan"];
        }

        // ambil id indikator di bawah kriteria ini
        $indikatorIds = Indikator::where('kriteria_id', $kriteria->id)->pluck('id')->toArray();

        // ambil id sub_indikator dan sub_sub_indikator terkait
        $subIndikatorIds = SubIndikator::whereIn('indikator_id', $indikatorIds)->pluck('id')->toArray();
        $subSubIds = SubSubIndikator::whereIn('sub_indikator_id', $subIndikatorIds)->pluck('id')->toArray();

        // build query penilaian polymorphic
        $query = Penilaian::query();
        $query->where(function ($q) use ($indikatorIds, $subIndikatorIds, $subSubIds) {
            if (! empty($indikatorIds)) {
                $q->orWhere(function ($q2) use ($indikatorIds) {
                    $q2->where('penilaian_type', 'App\\Models\\Indikator')->whereIn('penilaian_id', $indikatorIds);
                });
            }
            if (! empty($subIndikatorIds)) {
                $q->orWhere(function ($q2) use ($subIndikatorIds) {
                    $q2->where('penilaian_type', 'App\\Models\\SubIndikator')->whereIn('penilaian_id', $subIndikatorIds);
                });
            }
            if (! empty($subSubIds)) {
                $q->orWhere(function ($q2) use ($subSubIds) {
                    $q2->where('penilaian_type', 'App\\Models\\SubSubIndikator')->whereIn('penilaian_id', $subSubIds);
                });
            }
        });

        // optional: eager load dosen jika relation ada pada model Penilaian
        $penilaian = $query->with('dosen')->get();

        // group by dosen (gunakan id jika tersedia, fallback ke nama)
        $grouped = $penilaian->groupBy(function ($item) {
            $dosen = $item->dosen ?? null;
            if ($dosen && isset($dosen->id)) {
                return 'dosen_id_'.$dosen->id;
            }
            if ($dosen && (isset($dosen->nama_dosen) || isset($dosen->nama))) {
                return 'dosen_nama_'.($dosen->nama_dosen ?? $dosen->nama);
            }
            // fallback to penilaian row identifier
            return 'unknown_'.$item->id;
        });

        // aggregate: jumlahkan nilai untuk setiap dosen dan keep dosen info dari first item
        $aggregated = $grouped->map(function ($group) {
            $first = $group->first();
            $dosen = $first->dosen ?? null;

            $sumNilai = $group->reduce(function ($carry, $i) {
                $v = (isset($i->nilai) && is_numeric($i->nilai)) ? (float) $i->nilai : 0.0;
                return $carry + $v;
            }, 0.0);

            // tentukan label skala berdasarkan nilai total
            $label = null;
            if ($sumNilai !== null) {
                if ($sumNilai >= 7) {
                    $label = 5;
                } elseif ($sumNilai >= 5 && $sumNilai <= 6) {
                    $label = 4;
                } elseif ($sumNilai >= 3 && $sumNilai <= 4) {
                    $label = 3;
                } elseif ($sumNilai >= 1 && $sumNilai < 3) {
                    $label = 2;
                } elseif ($sumNilai < 1) {
                    $label = 1;
                } else {
                    $label = null;
                }
            }

            return [
                'id' => $first->id ?? null,
                'dosen_id' => $dosen->id ?? null,
                'dosen' => $dosen ? (is_array($dosen) ? $dosen : $dosen->toArray()) : null,
                'nilai' => $sumNilai,
                'skala_interval' => $label,
            ];
        });

        return [
            'kriteria' => $kriteria->kd_kriteria,
            'kriteria_nama' => $kriteria->nama_kriteria,
            'count' => $aggregated->count(),
            'data' => $aggregated->values()->toArray(),
        ];
    }

    /**
     * JSON API that returns penilaian for kriteria (default K004)
     */
    public function penilaianK004(Request $request): JsonResponse
    {
        $kd = $request->query('kd', 'K004');
        $res = $this->getPenilaianData($kd);
        if (isset($res['error'])) {
            return response()->json(['message' => $res['error'], 'data' => []], 404);
        }

        return response()->json($res);
    }

    /**
     * Web page that renders penilaian (uses Blade view)
     */
    public function penilaianK004Page(Request $request)
    {
        $kd = $request->query('kd', 'K004');
        $res = $this->getPenilaianData($kd);
        if (isset($res['error'])) {
            abort(404, $res['error']);
        }

        return view('perhitungan.penilaian_k004', [
            'kriteria' => $res['kriteria'],
            'kriteria_nama' => $res['kriteria_nama'],
            'count' => $res['count'],
            'data' => $res['data'],
        ]);
    }
}
