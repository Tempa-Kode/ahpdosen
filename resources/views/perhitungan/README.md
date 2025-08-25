# Halaman Hasil Perhitungan AHP - Pendidikan dan Pembelajaran

## Deskripsi
Halaman ini menampilkan hasil perhitungan Analytical Hierarchy Process (AHP) untuk kriteria "Pendidikan dan Pembelajaran" dalam evaluasi kinerja dosen.

## URL dan Route
- **Halaman View**: `/dashboard/perhitungan/pendidikan-dan-pembelajaran`
- **API Endpoint**: `/dashboard/perhitungan/api/pendidian-dan-pembelajaran`
- **Route Name**: `perhitungan.show.pendidikan-dan-pembelajaran`

## Fitur Utama

### 1. Dashboard Statistik
- **Total Dosen**: Jumlah dosen yang dievaluasi
- **Rata-rata Persentase**: Nilai rata-rata seluruh dosen
- **Nilai Tertinggi**: Persentase terbaik
- **Nilai Terendah**: Persentase terlemah

### 2. Tabel Referensi Skala Interval
Menampilkan referensi skala penilaian:
- **81% - 100%**: Sangat tinggi (5.00000)
- **61% - 80%**: Tinggi (4.00000)
- **41% - 60%**: Sedang (3.00000)
- **21% - 40%**: Rendah (2.00000)
- **0% - 20%**: Sangat rendah (1.00000)

### 3. Tabel Hasil Perhitungan
Kolom yang ditampilkan:
- **Ranking**: Posisi berdasarkan persentase
- **Nama Dosen**: Nama lengkap dosen
- **NIDN**: Nomor Induk Dosen Nasional
- **Program Studi**: Prodi dosen
- **Persentase**: Hasil perhitungan dalam persen
- **Nilai Decimal**: Nilai skala interval (1-5)
- **Kategori**: Klasifikasi berdasarkan range
- **Total Responden**: Jumlah responden yang menilai
- **Aksi**: Tombol untuk melihat detail

### 4. Modal Detail Penilaian
Menampilkan informasi lengkap:
- Informasi personal dosen
- Hasil perhitungan detail (skor aktual, min, max)
- Ringkasan kategori penilaian (sangat baik, baik, cukup baik, kurang baik)

## Formula Perhitungan
```
Skor Actual = Σ(Jumlah Responden × Skor Kredit Kategori)
Persentase = ((Skor Actual - Skor Min) / (Skor Max - Skor Min)) × 100%
```

## Teknologi yang Digunakan
- **Backend**: Laravel PHP
- **Frontend**: Bootstrap 5, JavaScript
- **Icons**: Feather Icons
- **Template**: NobleUI Admin Template

## File Terkait
- **Controller**: `app/Http/Controllers/PerhitunganController.php`
- **View**: `resources/views/perhitungan/pendidikan-pembelajaran.blade.php`
- **Layout**: `resources/views/app.blade.php`
- **Routes**: `routes/web.php`

## Cara Akses
1. Login ke sistem
2. Klik menu "Perhitungan" di navigation bar
3. Pilih "Pendidikan Dan Pembelajaran"
4. Data akan dimuat secara otomatis via AJAX

## Fitur Responsif
- Tabel responsive untuk mobile
- Card statistics dengan layout grid
- Modal detail dengan scrollable content
- Loading indicators untuk better UX
