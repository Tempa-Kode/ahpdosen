<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    /**
     * Menampilkan daftar dosen.
     */
    public function index()
    {
        $dosen = Dosen::all();
        return view('dosen.index', compact('dosen'));
    }

    /**
     * Menampilkan form tambah dosen.
     */
    public function tambah()
    {
        return view('dosen.tambah');
    }

    /**
     * Menyimpan data dosen baru.
     */
    public function simpan(Request $request)
    {
        $validasi = $request->validate([
            'nidn' => 'required|string|max:15|unique:dosen,nidn',
            'nama_dosen' => 'required|string|max:50',
            'prodi' => 'required|string|max:20',
        ], [
            'nidn.required' => 'NIDN harus diisi',
            'nidn.unique' => 'NIDN sudah terdaftar',
            'nama_dosen.required' => 'Nama Dosen harus diisi',
            'prodi.required' => 'Program Studi harus diisi',
        ]);

        DB::beginTransaction();
        try {
            Dosen::create($validasi);
            DB::commit();
            return redirect()->route('dosen.index')->with('success', 'Data Dosen berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    /**
     * Menampilkan form edit dosen.
     */
    public function edit($id)
    {
        $dosen = Dosen::findOrFail($id);
        return view('dosen.edit', compact('dosen'));
    }

    /**
     * Memperbarui data dosen.
     */
    public function update(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);

        $validasi = $request->validate([
            'nidn' => 'required|string|max:15|unique:dosen,nidn,' . $dosen->id,
            'nama_dosen' => 'required|string|max:50',
            'prodi' => 'required|string|max:20',
        ], [
            'nidn.required' => 'NIDN harus diisi',
            'nidn.unique' => 'NIDN sudah terdaftar',
            'nama_dosen.required' => 'Nama Dosen harus diisi',
            'prodi.required' => 'Program Studi harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $dosen->update($validasi);
            DB::commit();
            return redirect()->route('dosen.index')->with('success', 'Data Dosen berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus data dosen.
     */
    public function hapus($id)
    {
        $dosen = Dosen::findOrFail($id);

        DB::beginTransaction();
        try {
            $dosen->delete();
            DB::commit();
            return redirect()->route('dosen.index')->with('success', 'Data Dosen berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
