<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndikatorController extends Controller
{
    public function tambah(Request $request)
    {
        $kriteriaId = $request->query('kriteria_id');
        $kriteria = Kriteria::where('id', $kriteriaId)->first();
        return view('indikator.tambah', compact('kriteriaId', 'kriteria'));
    }

    public function simpan(Request $request)
    {
        $validasi = $request->validate([
            'kriteria_id' => 'nullable|exists:kriteria,id',
            'nama_indikator' => 'required|string|max:255',
            'kd_indikator' => 'required|string|max:50',
            'bobot_indikator' => 'nullable|numeric|min:0|max:100',
        ], [
            'kriteria_id.required' => 'Kriteria harus dipilih.',
            'nama_indikator.required' => 'Nama indikator harus diisi.',
            'kd_indikator.required' => 'Kode indikator harus diisi.',
            'bobot_indikator.required' => 'Bobot indikator harus diisi.',
        ]);

        DB::beginTransaction();
        try {
            Indikator::create($validasi);
            DB::commit();
            return redirect()->route('kriteria.detail', ['id' => $validasi['kriteria_id']])->with('success', 'Indikator berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan indikator: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $indikator = Indikator::where('id', $id)->first();
        return(view('indikator.edit', compact('indikator')));
    }

    public function update(Request $request, $id)
    {
        $validasi = $request->validate([
            'kd_indikator' => 'nullable|string|max:50',
            'nama_indikator' => 'required|string|max:255',
            'bobot_indikator' => 'nullable|numeric|min:0|max:100',
        ], [
            'kd_indikator.required' => 'Kode indikator harus diisi.',
            'nama_indikator.required' => 'Nama indikator harus diisi.',
            'bobot_indikator.required' => 'Bobot indikator harus diisi.',
        ]);

        DB::beginTransaction();
        try {
            $indikator = Indikator::where('id', $id)->first();
            $indikator->update($validasi);
            DB::commit();
            return redirect()->route('kriteria.detail', ['id' => $indikator->kriteria_id])->with('success', 'Indikator berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui indikator: ' . $e->getMessage()]);
        }
    }

    public function hapus($id)
    {
        DB::beginTransaction();
        try {
            $indikator = Indikator::where('id', $id)->first();
            $indikator->delete();
            DB::commit();
            return redirect()->route('kriteria.detail', ['id' => $indikator->kriteria_id])->with('success', 'Indikator berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus indikator: ' . $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        $indikator = Indikator::where('id', $id)->with('kriteria')->first();
        return view('indikator.detail', compact('indikator'));
    }
}
