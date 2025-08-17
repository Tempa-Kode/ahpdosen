<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\SubIndikator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubIndikatorController extends Controller
{
    public function tambah(Request $request)
    {
        $indikatorId = $request->query('indikator_id');
        $indikator = Indikator::where('id', $indikatorId)->with('kriteria')->first();
        return view('sub_indikator.tambah', compact('indikator'));
    }

    public function simpan(Request $request)
    {
        $validasi = $request->validate([
            'indikator_id' => 'required|exists:indikator,id',
            'nama_sub_indikator' => 'required|string|max:255',
            'skor_kredit' => 'nullable|numeric|min:0',
        ], [
            'indikator_id.required' => 'Indikator harus dipilih.',
            'nama_sub_indikator.required' => 'Nama sub indikator harus diisi.',
        ]);

        DB::beginTransaction();
        try {
            SubIndikator::create($validasi);
            DB::commit();
            return redirect()->route('indikator.detail', $validasi['indikator_id'])
                ->with('success', 'Sub Indikator berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan sub indikator: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $subIndikator = SubIndikator::where('id', $id)->with('indikator.kriteria')->first();
        return view('sub_indikator.edit', compact('subIndikator'));
    }

    public function update(Request $request, $id)
    {
        $validasi = $request->validate([
            'nama_sub_indikator' => 'required|string|max:255',
            'skor_kredit' => 'nullable|numeric|min:0',
        ], [
            'nama_sub_indikator.required' => 'Nama sub indikator harus diisi.',
            'skor_kredit.numeric' => 'Skor kredit harus berupa angka.',
        ]);

        DB::beginTransaction();
        try {
            $subIndikator = SubIndikator::where('id', $id)->first();
            $subIndikator->update($validasi);
            DB::commit();
            return redirect()->route('indikator.detail', $subIndikator->indikator_id)
                ->with('success', 'Sub Indikator berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui sub indikator: ' . $e->getMessage()]);
        }
    }

    public function hapus($id)
    {
        DB::beginTransaction();
        try {
            $subIndikator = SubIndikator::findOrFail($id);
            $indikatorId = $subIndikator->indikator_id;
            $subIndikator->delete();
            DB::commit();
            return redirect()->route('indikator.detail', $indikatorId)
                ->with('success', 'Sub Indikator berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus sub indikator: ' . $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        $subIndikator = SubIndikator::where('id', $id)->with('indikator.kriteria', 'subSubIndikator')->first();
        if (!$subIndikator) {
            return redirect()->back()->withErrors(['error' => 'Sub Indikator tidak ditemukan.']);
        }
        return view('sub_indikator.detail', compact('subIndikator'));
    }
}
