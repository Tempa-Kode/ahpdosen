<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AhpPenelitianWebController extends Controller
{
    /**
     * Tampilkan halaman utama AHP Penelitian
     */
    public function index()
    {
        return view('ahp-penelitian.index');
    }

    /**
     * Tampilkan halaman detail dosen
     */
    public function detailDosen($dosen_id)
    {
        return view('ahp-penelitian.detail-dosen', compact('dosen_id'));
    }

    /**
     * Tampilkan halaman perhitungan step by step
     */
    public function langkahPerhitungan()
    {
        return view('ahp-penelitian.langkah-perhitungan');
    }
}
