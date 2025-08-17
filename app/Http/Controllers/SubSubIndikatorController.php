<?php

namespace App\Http\Controllers;

use App\Models\SubIndikator;
use App\Models\SubSubIndikator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubSubIndikatorController extends Controller
{
    public function tambah()
    {
        $subIndikatorId = request()->query('sub_indikator_id');
        $subIndikator = SubIndikator::where('id', $subIndikatorId)->with('indikator.kriteria')->first();
        return view('subsub_indikator.tambah', compact('subIndikator'));
    }

    public function simpan(Request $request)
    {
        $validasi = $request->validate([
            'sub_indikator_id' => 'required|exists:sub_indikator,id',
            'nama_sub_sub_indikator' => 'required|string|max:50',
            'skor_kredit' => 'required|numeric|min:0|max:100',
        ], [
            'sub_indikator_id.required' => 'Sub Indikator harus dipilih.',
            'sub_indikator_id.exists' => 'Sub Indikator tidak ditemukan.',
            'nama_sub_sub_indikator.required' => 'Nama Sub Sub Indikator harus diisi.',
            'nama_sub_sub_indikator.max' => 'Nama Sub Sub Indikator maksimal 50 karakter.',
            'skor_kredit.required' => 'Skor Kredit harus diisi.',
            'skor_kredit.numeric' => 'Skor Kredit harus berupa angka.',
            'skor_kredit.min' => 'Skor Kredit minimal 0.',
            'skor_kredit.max' => 'Skor Kredit maksimal 100.',
        ]);

        DB::beginTransaction();
        try {
            SubSubIndikator::create($validasi);
            DB::commit();
            return redirect()->route('subindikator.detail', $validasi['sub_indikator_id'])
                ->with('success', 'Sub Sub Indikator berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.']);
        }
    }

    public function edit($id)
    {
        $subSubIndikator = SubSubIndikator::findOrFail($id);
        $subIndikator = SubIndikator::where('id', $subSubIndikator->sub_indikator_id)
            ->with('indikator.kriteria')->first();
        return view('subsub_indikator.edit', compact('subSubIndikator', 'subIndikator'));
    }

    public function update(Request $request, $id)
    {
        $validasi = $request->validate([
            'nama_sub_sub_indikator' => 'required|string|max:50',
            'skor_kredit' => 'required|numeric|min:0|max:100',
        ], [
            'nama_sub_sub_indikator.required' => 'Nama Sub Sub Indikator harus diisi.',
            'nama_sub_sub_indikator.max' => 'Nama Sub Sub Indikator maksimal 50 karakter.',
            'skor_kredit.required' => 'Skor Kredit harus diisi.',
            'skor_kredit.numeric' => 'Skor Kredit harus berupa angka.',
            'skor_kredit.min' => 'Skor Kredit minimal 0.',
            'skor_kredit.max' => 'Skor Kredit maksimal 100.',
        ]);

        DB::beginTransaction();
        try {
            $subSubIndikator = SubSubIndikator::findOrFail($id);
            $subSubIndikator->update($validasi);
            DB::commit();
            return redirect()->route('subindikator.detail', $subSubIndikator->sub_indikator_id)
                ->with('success', 'Sub Sub Indikator berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data.']);
        }
    }

    public function hapus($id)
    {
        DB::beginTransaction();
        try {
            $subSubIndikator = SubSubIndikator::findOrFail($id);
            $subIndikatorId = $subSubIndikator->sub_indikator_id;
            $subSubIndikator->delete();
            DB::commit();
            return redirect()->route('subindikator.detail', $subIndikatorId)
                ->with('success', 'Sub Sub Indikator berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data.']);
        }
    }
}
