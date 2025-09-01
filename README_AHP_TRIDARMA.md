# AHP Tridarma - Quick Start Guide

## Ringkasan Fitur

✅ **Perhitungan AHP Lengkap** - Menghitung prioritas global semua dosen berdasarkan 4 kriteria Tridarma  
✅ **Uji Konsistensi** - Validasi konsistensi matriks perbandingan (CR < 0.1)  
✅ **Ranking Otomatis** - Ranking dosen berdasarkan prioritas global  
✅ **API Integration** - Endpoint JSON untuk integrasi sistem lain  
✅ **Web Interface** - Dashboard interaktif dengan filter dan search  
✅ **Detail Perhitungan** - Transparansi langkah-langkah perhitungan AHP  

## Endpoint API

### 1. Perhitungan AHP Lengkap
```
GET /api/ahp-tridarma
```
**Output**: Ranking semua dosen dengan perhitungan AHP lengkap

### 2. Detail Dosen
```
GET /api/ahp-tridarma/dosen/{id}
```
**Output**: Detail perhitungan AHP untuk dosen tertentu

## Web Interface

### Akses Dashboard
```
URL: /dashboard/ahp-tridarma
Fitur: 
- Tabel ranking interaktif
- Filter berdasarkan kategori nilai
- Pencarian dosen (nama/NIDN)
- Modal detail perhitungan
- Informasi konsistensi matriks
```

## Kriteria Evaluasi

| Kode | Kriteria | Bobot Dasar | Persentase |
|------|----------|-------------|------------|
| K001 | Pendidikan & Pembelajaran | 3.00000 | ~33.33% |
| K002 | Penelitian | 2.50000 | ~27.78% |
| K003 | PKM | 2.00000 | ~22.22% |
| K004 | Penunjang | 1.50000 | ~16.67% |

## Kategori Hasil

| Persentase | Kategori | Badge Color |
|------------|----------|-------------|
| 81-100% | Sangat Baik | 🟢 Hijau |
| 61-80% | Baik | 🔵 Biru |
| 41-60% | Cukup | 🟡 Kuning |
| 21-40% | Kurang | 🔴 Merah |
| 0-20% | Sangat Kurang | ⚫ Hitam |

## Contoh Response API

```json
{
    "status": "success",
    "data": {
        "konsistensi": {
            "CR": 0,
            "konsisten": "Ya"
        },
        "hasil_akhir": [
            {
                "ranking": 1,
                "dosen": {
                    "nama": "Dr. PASKA MARTO HASUGIAN",
                    "nidn": "0115068201"
                },
                "prioritas_global": 2.55556,
                "persentase": 100,
                "kategori_nilai": {
                    "kategori": "Sangat Baik",
                    "nilai_decimal": 5.0
                }
            }
        ],
        "jumlah_dosen": 21
    }
}
```

## Validasi Sistem

✅ **Test Result**: 
- Total dosen: 21
- Konsistensi: Ya (CR = 0)
- Top ranking: Dr. PASKA MARTO HASUGIAN (100%)

## Route List

```bash
# API Routes
GET api/ahp-tridarma                    # Perhitungan lengkap
GET api/ahp-tridarma/dosen/{id}         # Detail dosen

# Web Routes  
GET dashboard/ahp-tridarma              # Dashboard web
```

---

**Status**: ✅ Ready to use  
**Last Updated**: {{ date('Y-m-d H:i:s') }}  
**Author**: AI Assistant
