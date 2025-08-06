<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataDosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dosen::create([
            'nidn' => '0114046501',
            'nama_dosen' => 'Prof. Dr. Zakarias Situmorang, M.T',
            'prodi' => 'Sains Data',
        ]);
        Dosen::create([
            'nidn' => '0124126801',
            'nama_dosen' => 'Drs. Lamhot Sitorus, M.Kom',
            'prodi' => 'Teknik Informatika',
        ]);
        Dosen::create([
            'nidn' => '0116117302',
            'nama_dosen' => 'Emerson P. Malau, S.Si., M.Kom',
            'prodi' => 'Sistem Informasi',
        ]);
    }
}
