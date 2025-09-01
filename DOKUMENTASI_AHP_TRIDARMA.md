# AHP Tridarma - Dokumentasi

## Deskripsi
Sistem perhitungan AHP (Analytical Hierarchy Process) untuk mengevaluasi kinerja dosen berdasarkan semua kriteria Tridarma Perguruan Tinggi.

## Fitur Utama

### 1. Perhitungan AHP Lengkap
- **Matriks Perbandingan Berpasangan**: Menggunakan bobot dasar untuk setiap kriteria
- **Bobot Prioritas**: Menghitung bobot prioritas menggunakan metode normalisasi
- **Uji Konsistensi**: Menghitung CI, CR untuk memastikan konsistensi matriks
- **Prioritas Global**: Menghitung prioritas global untuk setiap dosen

### 2. Kriteria yang Dievaluasi
- **K001 - Pendidikan dan Pembelajaran**: Bobot dasar 3.00000
- **K002 - Penelitian**: Bobot dasar 2.50000  
- **K003 - PKM (Pengabdian Kepada Masyarakat)**: Bobot dasar 2.00000
- **K004 - Penunjang**: Bobot dasar 1.50000

### 3. API Endpoints

#### `/api/ahp-tridarma`
**Method**: GET  
**Deskripsi**: Mengembalikan perhitungan AHP lengkap untuk semua dosen

**Response Structure**:
```json
{
    "status": "success",
    "message": "Perhitungan AHP Tridarma berhasil",
    "data": {
        "matriks_perbandingan": {...},
        "bobot_prioritas": {...},
        "konsistensi": {...},
        "hasil_akhir": [...],
        "jumlah_dosen": 10,
        "metadata": {...}
    }
}
```

#### `/api/ahp-tridarma/dosen/{dosen_id}`
**Method**: GET  
**Deskripsi**: Detail perhitungan AHP untuk dosen tertentu

**Response Structure**:
```json
{
    "status": "success",
    "data": {
        "dosen": {...},
        "detail_kriteria": {...},
        "prioritas_global": 2.8456,
        "persentase": 85.2,
        "ranking": 3,
        "kategori_nilai": {...}
    }
}
```

### 4. Web Interface

#### `/dashboard/ahp-tridarma`
**Deskripsi**: Halaman web untuk melihat hasil perhitungan AHP dengan fitur:
- Tabel hasil ranking dosen
- Filter berdasarkan kategori
- Pencarian dosen (nama/NIDN)
- Detail perhitungan per dosen
- Informasi konsistensi matriks
- Bobot prioritas kriteria

### 5. Algoritma Perhitungan

#### Step 1: Pengumpulan Data
```php
// Mengambil data dari controller masing-masing kriteria
$dataK001 = PerhitunganController::pendidikanDanPembelajaran();
$dataK002 = PerhitunganPenelitianController::hitungSemuaDosen();
$dataK003 = PerhitunganPKMController::penilaianK003();
$dataK004 = PerhitunganTridarmaController::penilaianK004();
```

#### Step 2: Matriks Perbandingan
```php
// Matriks 4x4 berdasarkan bobot dasar
$matriks = [
    'K001' => ['K001' => 1.0, 'K002' => 1.2, 'K003' => 1.5, 'K004' => 2.0],
    'K002' => ['K001' => 0.833, 'K002' => 1.0, 'K003' => 1.25, 'K004' => 1.667],
    'K003' => ['K001' => 0.667, 'K002' => 0.8, 'K003' => 1.0, 'K004' => 1.333],
    'K004' => ['K001' => 0.5, 'K002' => 0.6, 'K003' => 0.75, 'K004' => 1.0]
];
```

#### Step 3: Normalisasi dan Bobot Prioritas
```php
// Normalisasi setiap elemen
for ($i = 0; $i < $n; $i++) {
    for ($j = 0; $j < $n; $j++) {
        $normalized[$i][$j] = $matrix[$i][$j] / $columnSum[$j];
    }
}

// Hitung rata-rata baris sebagai bobot prioritas
$priority[$i] = array_sum($normalized[$i]) / $n;
```

#### Step 4: Uji Konsistensi
```php
// Lambda maks
$lambdaMax = sum($columnSum * $priority);

// Consistency Index
$CI = ($lambdaMax - $n) / ($n - 1);

// Consistency Ratio
$CR = $CI / $RI[$n];
```

#### Step 5: Prioritas Global
```php
$prioritasGlobal = 
    ($nilaiK001 * $bobotK001) + 
    ($nilaiK002 * $bobotK002) + 
    ($nilaiK003 * $bobotK003) + 
    ($nilaiK004 * $bobotK004);
```

### 6. Kategori Nilai Akhir

| Persentase | Kategori | Nilai Decimal | Keterangan |
|------------|----------|---------------|------------|
| 81-100% | Sangat Baik | 5.0 | Performa sangat memuaskan |
| 61-80% | Baik | 4.0 | Performa memuaskan |
| 41-60% | Cukup | 3.0 | Performa memadai |
| 21-40% | Kurang | 2.0 | Performa perlu ditingkatkan |
| 0-20% | Sangat Kurang | 1.0 | Performa sangat perlu ditingkatkan |

### 7. Contoh Penggunaan

#### Via API
```javascript
// Ambil data AHP lengkap
fetch('/api/ahp-tridarma')
    .then(response => response.json())
    .then(data => {
        console.log('Total dosen:', data.data.jumlah_dosen);
        console.log('Ranking teratas:', data.data.hasil_akhir[0]);
    });

// Detail dosen tertentu
fetch('/api/ahp-tridarma/dosen/1')
    .then(response => response.json())
    .then(data => {
        console.log('Ranking dosen:', data.data.ranking);
        console.log('Prioritas global:', data.data.prioritas_global);
    });
```

#### Via Web Interface
1. Akses `/dashboard/ahp-tridarma`
2. Lihat hasil ranking dan konsistensi
3. Gunakan filter dan pencarian
4. Klik "Detail" untuk melihat perhitungan lengkap

### 8. Kelebihan Sistem

1. **Konsistensi Matematika**: Menggunakan uji konsistensi CR < 0.1
2. **Transparansi**: Semua langkah perhitungan dapat dilihat
3. **Fleksibilitas**: Bobot kriteria dapat disesuaikan
4. **User-Friendly**: Interface web yang mudah digunakan
5. **API Integration**: Dapat diintegrasikan dengan sistem lain

### 9. Validasi Data

- Input data menggunakan nilai skala interval dari setiap kriteria
- Nilai default 1.0 jika data tidak tersedia
- Validasi konsistensi matriks perbandingan
- Pengecekan completeness data setiap dosen

### 10. Performance

- Optimized query untuk mengambil data kriteria
- Caching hasil perhitungan jika diperlukan
- Minimal database calls dengan eager loading
- Efficient array operations untuk matriks calculation

## Kesimpulan

Sistem AHP Tridarma ini memberikan evaluasi objektif dan komprehensif terhadap kinerja dosen berdasarkan empat pilar utama Tridarma Perguruan Tinggi. Dengan menggunakan metode AHP yang terbukti secara matematika, sistem ini dapat membantu pengambilan keputusan yang lebih akurat dalam penilaian kinerja dosen.
