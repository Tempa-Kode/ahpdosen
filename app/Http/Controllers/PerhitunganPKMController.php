<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Indikator;
use App\Models\Penilaian;
use App\Models\SubIndikator;
use Illuminate\Http\Request;
use App\Models\SubSubIndikator;
use Illuminate\Http\JsonResponse;

class PerhitunganPKMController extends Controller
{
    /**
     * Ambil semua penilaian yang terkait dengan kriteria (kd_kriteria) K003
     * - mengumpulkan id indikator/sub_indikator/sub_sub_indikator untuk kriteria tersebut
     * - mencari penilaian polymorphic yang menunjuk ke id-id itu
     *
     * Request optional query param: ?kd=K003
     */
    protected function getPenilaianData(string $kd = 'K003'): array
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

        $dataWithSkala = $penilaian->map(function ($item) {
            $arr = $item->toArray();
            $nilai = null;
            if (isset($item->nilai) && is_numeric($item->nilai)) {
                $nilai = (float) $item->nilai;
            }

            $label = null;
            if ($nilai !== null) {
                if ($nilai >= 5) {
                    $label = 5;
                } elseif ($nilai == 4) {
                    $label = 4;
                } elseif ($nilai == 3) {
                    $label = 3;
                } elseif ($nilai == 2) {
                    $label = 2;
                } else {
                    $label = 1;
                }
            }

            $arr['skala_interval'] = $label;
            return $arr;
        })->values();

        return [
            'kriteria' => $kriteria->kd_kriteria,
            'kriteria_nama' => $kriteria->nama_kriteria,
            'count' => $dataWithSkala->count(),
            'data' => $dataWithSkala->toArray(),
        ];
    }

    /**
     * JSON API that returns penilaian for kriteria (default K003)
     */
    public function penilaianK003(Request $request): JsonResponse
    {
        $kd = $request->query('kd', 'K003');
        $res = $this->getPenilaianData($kd);
        if (isset($res['error'])) {
            return response()->json(['message' => $res['error'], 'data' => []], 404);
        }

        return response()->json($res);
    }

    /**
     * Web page that renders penilaian (uses Blade view)
     */
    public function penilaianK003Page(Request $request)
    {
        $kd = $request->query('kd', 'K003');
        $res = $this->getPenilaianData($kd);
        if (isset($res['error'])) {
            abort(404, $res['error']);
        }

        return view('perhitungan.penilaian_k003', [
            'kriteria' => $res['kriteria'],
            'kriteria_nama' => $res['kriteria_nama'],
            'count' => $res['count'],
            'data' => $res['data'],
        ]);
    }
}
