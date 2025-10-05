<?php

namespace App\Http\Controllers;

use App\Models\SubSubIndikator;
use Illuminate\Http\Request;

class SubSubSubIndikatorController extends Controller
{
    public function tambah()
    {
        $subSubIndikatorId = request()->query('sub_sub_indikator_id');
        $subSubIndikator = SubSubIndikator::where('id', $subSubIndikatorId)
            ->with('subIndikator.indikator.kriteria')->first();
        return view('subsubsub_indikator.tambah', compact('subSubIndikator'));
    }

    public function simpan(Request $request)
    {
        $validasi = $request->validate([
            'sub_sub_indikator_id' => 'required|exists:sub_sub_indikator,id',
            'nama_sub_sub_sub_indikator' => 'required|string|max:50',
            'skor_kredit' => 'required|numeric|min:0|max:100',
        ], [
            'sub_sub_indikator_id.required' => 'Sub Sub Indikator harus dipilih.',
            'sub_sub_indikator_id.exists' => 'Sub Sub Indikator tidak ditemukan.',
            'nama_sub_sub_sub_indikator.required' => 'Nama Sub Sub Sub Indikator harus diisi.',
            'nama_sub_sub_sub_indikator.max' => 'Nama Sub Sub Sub Indikator maksimal 50 karakter.',
            'skor_kredit.required' => 'Skor Kredit harus diisi.',
            'skor_kredit.numeric' => 'Skor Kredit harus berupa angka.',
            'skor_kredit.min' => 'Skor Kredit minimal 0.',
            'skor_kredit.max' => 'Skor Kredit maksimal 100.',
        ]);

        try {
            \App\Models\SubSubSubIndikator::create($validasi);
            return redirect()->route('subsubindikator.detail', $validasi['sub_sub_indikator_id'])
                ->with('success', 'Sub Sub Sub Indikator berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $subSubSubIndikator = \App\Models\SubSubSubIndikator::findOrFail($id);
        $subSubIndikator = SubSubIndikator::where('id', $subSubSubIndikator->sub_sub_indikator_id)
            ->with('subIndikator.indikator.kriteria')->first();
        return view('subsubsub_indikator.edit', compact('subSubSubIndikator', 'subSubIndikator'));
    }

    public function update(Request $request, $id)
    {
        $validasi = $request->validate([
            'nama_sub_sub_sub_indikator' => 'required|string|max:50',
            'skor_kredit' => 'required|numeric|min:0|max:100',
        ], [
            'nama_sub_sub_sub_indikator.required' => 'Nama Sub Sub Sub Indikator harus diisi.',
            'nama_sub_sub_sub_indikator.max' => 'Nama Sub Sub Sub Indikator maksimal 50 karakter.',
            'skor_kredit.required' => 'Skor Kredit harus diisi.',
            'skor_kredit.numeric' => 'Skor Kredit harus berupa angka.',
            'skor_kredit.min' => 'Skor Kredit minimal 0.',
            'skor_kredit.max' => 'Skor Kredit maksimal 100.',
        ]);

        try {
            $subSubSubIndikator = \App\Models\SubSubSubIndikator::findOrFail($id);
            $subSubSubIndikator->update($validasi);
            return redirect()->route('subsubindikator.detail', $subSubSubIndikator->sub_sub_indikator_id)
                ->with('success', 'Sub Sub Sub Indikator berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function hapus($id)
    {
        try {
            $subSubSubIndikator = \App\Models\SubSubSubIndikator::findOrFail($id);
            $subSubIndikatorId = $subSubSubIndikator->sub_sub_indikator_id;
            $subSubSubIndikator->delete();
            return redirect()->route('subsubindikator.detail', $subSubIndikatorId)
                ->with('success', 'Sub Sub Sub Indikator berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()]);
        }
    }
}
