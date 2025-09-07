<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

// use PDF;

class ReportController extends Controller
{
    public function index()
    {
        $ahpTridarmaController = new \App\Http\Controllers\AhpTridarmaController();
        $hasilAhpTridarma = $ahpTridarmaController->perhitunganAhpTridarma();
        $dataAhp = json_decode($hasilAhpTridarma->getContent(), true);

        $dosenTerbaik = [];

        // Ambil data dari hasil_akhir yang sudah diurutkan berdasarkan ranking
        if (isset($dataAhp['data']['hasil_akhir']) && is_array($dataAhp['data']['hasil_akhir'])) {
            foreach ($dataAhp['data']['hasil_akhir'] as $item) {
                $dosenTerbaik[] = [
                    'id' => $item['dosen']['id'],
                    'nama' => $item['dosen']['nama_dosen'] ?? $item['dosen']['nama'],
                    'nidn' => $item['dosen']['nidn'] ?? '',
                    'prodi' => $item['dosen']['prodi'] ?? 'N/A',
                    'skor' => $item['prioritas_global'],
                    'persentase' => $item['persentase'] ?? 0,
                    'ranking' => $item['ranking'] ?? 0,
                    'k001' => $item['detail_kriteria']['K001']['nilai'] ?? 0,
                    'k002' => $item['detail_kriteria']['K002']['nilai'] ?? 0,
                    'k003' => $item['detail_kriteria']['K003']['nilai'] ?? 0,
                    'k004' => $item['detail_kriteria']['K004']['nilai'] ?? 0,
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

        PDF::loadView('report',
            ['data' => $dosenTerbaik]
        )->stream('laporan_dosen_terbaik.pdf');
    }
}
