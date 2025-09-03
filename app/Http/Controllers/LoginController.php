<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function prosesLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->with('error', 'username atau passsword salah');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function dashboard()
    {
        // Ambil data dari AHP Tridarma yang sudah menggunakan prioritas global choice
        $ahpTridarmaController = new \App\Http\Controllers\AhpTridarmaController();
        $hasilAhpTridarma = $ahpTridarmaController->perhitunganAhpTridarma();
        $dataAhp = json_decode($hasilAhpTridarma->getContent(), true);

        $dosenTerbaik = [];

        // Ambil data dari prioritas_global_choice yang sudah diurutkan berdasarkan ranking
        if (isset($dataAhp['data']['prioritas_global_choice']) && is_array($dataAhp['data']['prioritas_global_choice'])) {
            foreach ($dataAhp['data']['prioritas_global_choice'] as $item) {
                $dosenTerbaik[] = [
                    'id' => $item['dosen']['id'],
                    'nama' => $item['dosen']['nama_dosen'] ?? $item['dosen']['nama'],
                    'nidn' => $item['dosen']['nidn'] ?? '',
                    'prodi' => $item['dosen']['prodi'] ?? 'N/A',
                    'skor' => $item['prioritas_global_choice'],
                    'persentase' => $item['persentase'] ?? 0,
                    'ranking' => $item['ranking'] ?? 0,
                    'kategori' => $item['kategori_nilai']['kategori'] ?? 'N/A',
                    'nilai_decimal' => $item['kategori_nilai']['nilai_decimal'] ?? 0,
                    'keterangan' => $item['kategori_nilai']['keterangan'] ?? '',
                    'detail_formula' => $item['formula_total'] ?? '',
                    'metodologi' => 'AHP Tridarma - Prioritas Global Choice'
                ];
            }
        }

        // Jika tidak ada data AHP, gunakan data fallback
        if (empty($dosenTerbaik)) {
            $dosenTerbaik = [
                [
                    'id' => 0,
                    'nama' => 'Data belum tersedia',
                    'nidn' => '',
                    'prodi' => 'Silakan cek data AHP Tridarma',
                    'skor' => 0,
                    'persentase' => 0,
                    'ranking' => 1,
                    'kategori' => 'N/A',
                    'nilai_decimal' => 0,
                    'keterangan' => 'Data belum tersedia',
                    'detail_formula' => '',
                    'metodologi' => 'Fallback Data'
                ]
            ];
        }

        return view('dashboard', compact('dosenTerbaik'));
    }
}
