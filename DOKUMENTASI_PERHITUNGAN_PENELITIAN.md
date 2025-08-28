# Dokumentasi Perhitungan Penelitian (K002)

## Overview
Controller `PerhitunganPenelitianController` berisi implementasi lengkap untuk menghitung nilai penelitian dosen berdasarkan kriteria K002 dengan menggunakan metode AHP (Analytical Hierarchy Process).

## Rumus Perhitungan

### 1. Perhitungan Sub Indikator
```
Nilai Sub Indikator = Nilai × Skor Kredit
```

### 2. Perhitungan Indikator
```
Total Nilai Indikator = Σ(Nilai Sub Indikator)
Nilai Tertimbang Indikator = Total Nilai Indikator × Bobot Indikator
```

### 3. Perhitungan Kriteria
```
Total Nilai Kriteria = Σ(Nilai Tertimbang Indikator) × Bobot Kriteria (0.45)
```

## Struktur Indikator K002

| Kode | Nama Indikator | Bobot |
|------|----------------|-------|
| KPT01 | Publikasi Terakreditasi Nasional & Internasional | 0.30 |
| KPT02 | Presentasi seminar nasional dan internasional | 0.25 |
| KPT03 | Buku dari hasil penelitian | 0.20 |
| KPT04 | HaKI | 0.15 |
| KPT05 | Karya Ilmiah atau seni yang dipamerkan | 0.10 |

## Endpoints API

### 1. Perhitungan untuk Satu Dosen
```
GET /dashboard/perhitungan/penelitian/{dosen_id}
```
Mengembalikan perhitungan lengkap untuk satu dosen.

### 2. Perhitungan untuk Semua Dosen
```
GET /dashboard/perhitungan/penelitian
```
Mengembalikan perhitungan untuk semua dosen.

### 3. Ringkasan Perhitungan
```
GET /dashboard/perhitungan/penelitian/ringkasan/{dosen_id}
```
Mengembalikan ringkasan perhitungan untuk satu dosen.

### 4. Penjelasan Perhitungan
```
GET /dashboard/perhitungan/penelitian/penjelasan/{dosen_id}
```
Mengembalikan penjelasan langkah-langkah perhitungan.

### 5. Ranking Dosen
```
GET /dashboard/perhitungan/penelitian/ranking
```
Mengembalikan ranking dosen berdasarkan nilai penelitian.

### 6. Statistik Penelitian
```
GET /dashboard/perhitungan/penelitian/statistik
```
Mengembalikan statistik nilai penelitian (rata-rata, median, dll).

## Contoh Response

### Ringkasan Perhitungan
```json
{
    "dosen_id": 1,
    "kriteria_kode": "K002",
    "kriteria_nama": "Penelitian",
    "total_nilai_akhir": 289.485,
    "detail_per_indikator": [
        {
            "kode": "KPT01",
            "nama": "Publikasi Terakreditasi Nasional & Internasional",
            "total_nilai": 1061,
            "nilai_tertimbang": 318.3
        }
    ]
}
```

### Ranking Dosen
```json
{
    "title": "Ranking Dosen Berdasarkan Nilai Penelitian (K002)",
    "total_dosen": 20,
    "ranking": [
        {
            "ranking": 1,
            "dosen": {
                "id": 1,
                "nama": "Prof. Dr. ZAKARIAS SITUMORANG, M.T, MCE",
                "nidn": "0114046501",
                "prodi": "Sains Data"
            },
            "nilai_penelitian": 289.485
        }
    ]
}
```

## Cara Penggunaan

### Via Controller
```php
$controller = new PerhitunganPenelitianController();

// Hitung untuk satu dosen
$result = $controller->hitungPenelitian(1);

// Dapatkan ringkasan
$ringkasan = $controller->getRingkasanPenelitian(1);

// Lihat ranking
$ranking = $controller->rankingPenelitian();
```

### Via Route
```php
// Akses via browser atau API call
http://localhost/dashboard/perhitungan/penelitian/1
http://localhost/dashboard/perhitungan/penelitian/ringkasan/1
http://localhost/dashboard/perhitungan/penelitian/ranking
```

## Fitur Utama

1. **Perhitungan Akurat**: Menggunakan rumus AHP yang tepat
2. **Detail Lengkap**: Menampilkan breakdown per sub indikator
3. **Multiple Format**: JSON, ringkasan, penjelasan lengkap
4. **Ranking System**: Otomatis mengurutkan dosen
5. **Statistik**: Rata-rata, median, tertinggi, terendah
6. **Flexible**: Dapat digunakan untuk satu atau semua dosen

## Testing

Controller telah ditest dengan berbagai skenario:
- Dosen dengan nilai tinggi (Prof. Zakarias: 289.485)
- Dosen dengan nilai sedang (Sorang Pakpahan: 29.745)
- Dosen dengan nilai rendah (Emerson Malau: 11.88)
- Dosen tanpa penilaian (nilai 0)

## Kesimpulan

Sistem perhitungan penelitian K002 telah berhasil diimplementasikan dengan:
- ✅ Perhitungan nilai × skor kredit untuk setiap sub indikator
- ✅ Penerapan bobot indikator yang tepat
- ✅ Penerapan bobot kriteria (0.45)
- ✅ API lengkap untuk berbagai kebutuhan
- ✅ System ranking dan statistik
- ✅ Response format yang fleksibel
