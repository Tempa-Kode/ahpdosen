<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    public function index()
    {
        $dosen = Dosen::all();
        return view('dosen.index', compact('dosen'));
    }

    public function tambah()
    {
        return view('dosen.tambah');
    }

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
}
