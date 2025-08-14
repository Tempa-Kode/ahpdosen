<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::all();
        return view('kriteria.index', compact('kriteria'));
    }

    public function tambah()
    {
        return view('kriteria.tambah');
    }

    public function simpan(Request $request)
    {
        $validasi = $request->validate([
            'kd_kriteria' => 'required|string|max:255',
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required',
        ], [
            'kd_kriteria.required' => 'Kode kriteria harus diisi.',
            'nama_kriteria.required' => 'Nama kriteria harus diisi.',
            'bobot.required' => 'Bobot harus diisi.',
        ]);

        DB::beginTransaction();
        try {
            Kriteria::create($validasi);
            DB::commit();
            return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan kriteria: ' . $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        $kriteria = Kriteria::where('id', $id)->with('indikator')->firstOrFail();
        return view('kriteria.detail', compact('kriteria'));
    }

    public function edit($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('kriteria.edit', compact('kriteria'));
    }

    public function update(Request $request, $id)
    {
        $validasi = $request->validate([
            'kd_kriteria' => 'required|string|max:255',
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required',
        ], [
            'kd_kriteria.required' => 'Kode kriteria harus diisi.',
            'nama_kriteria.required' => 'Nama kriteria harus diisi.',
            'bobot.required' => 'Bobot harus diisi.',
        ]);

        DB::beginTransaction();
        try {
            $kriteria = Kriteria::findOrFail($id);
            $kriteria->update($validasi);
            DB::commit();
            return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui kriteria: ' . $e->getMessage()]);
        }
    }

    public function hapus($id)
    {
        DB::beginTransaction();
        try {
            $kriteria = Kriteria::findOrFail($id);
            $kriteria->delete();
            DB::commit();
            return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus kriteria: ' . $e->getMessage()]);
        }
    }
}
