<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ReportController extends Controller
{
    public function index()
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
                    'k001' => $item['matriks_bobot_prioritas']['nilai_mentah']['K001']['nilai_mentah'] ?? 0,
                    'k002' => $item['matriks_bobot_prioritas']['nilai_mentah']['K002']['nilai_mentah'] ?? 0,
                    'k003' => $item['matriks_bobot_prioritas']['nilai_mentah']['K003']['nilai_mentah'] ?? 0,
                    'k004' => $item['matriks_bobot_prioritas']['nilai_mentah']['K004']['nilai_mentah'] ?? 0,
                    'ranking' => $item['ranking'] ?? 0,
                    'kategori' => $item['kategori_nilai']['kategori'] ?? 'N/A',
                    'prioritas_global_choice' => $item['prioritas_global_choice'] ?? 0,
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

        PDF::loadView('report',
            ['data' => $dosenTerbaik]
        )->stream('laporan_dosen_terbaik.pdf');
    }
}
