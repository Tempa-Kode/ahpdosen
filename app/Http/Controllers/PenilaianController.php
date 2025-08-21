<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Indikator; // Tambahkan ini
use App\Models\Kriteria;
use App\Models\SubIndikator;
use App\Models\SubSubIndikator;
use Illuminate\Http\Request;

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
            }
        ])->get();

        return view('penilaian.form', compact('dosen', 'kriterias'));
    }

    /**
     * Menyimpan atau memperbarui nilai untuk seorang dosen.
     */
    public function store(Request $request, Dosen $dosen)
    {
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

        return back()->with('success', 'Nilai untuk ' . $dosen->nama . ' berhasil diperbarui.');
    }
}
