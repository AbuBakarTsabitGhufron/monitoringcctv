# Perubahan Struktur Database - CCTV Monitoring System

## Ringkasan Perubahan

Struktur database telah diubah dari sistem berbasis "sekolah" menjadi sistem CCTV monitoring yang lebih umum dengan konsep "lokasi".

## Detail Perubahan

### 1. Tabel Database

#### Sebelum:
- `sekolah` (nama_sekolah, wilayah_id)
- `cctvs` (sekolah_id, nama_titik, link_stream, ...)

#### Sesudah:
- `lokasi` (nama_lokasi, alamat, wilayah_id, is_active)
- `cctvs` (lokasi_id, nama_cctv, link_stream, ...)

### 2. Model

#### File yang Diubah:
- `/app/Models/Sekolah.php` → Sekarang class `Lokasi`
- `/app/Models/Cctv.php` → Relasi berubah dari `sekolah_id` ke `lokasi_id`

#### Relasi:
```php
// Model Lokasi
public function cctvs() {
    return $this->hasMany(Cctv::class, 'lokasi_id');
}

// Model Cctv
public function lokasi() {
    return $this->belongsTo(Lokasi::class, 'lokasi_id');
}
```

### 3. Migration

#### File yang Diubah:
- `2025_04_30_090314_create_sekolah_table.php`
  - Tabel: `sekolah` → `lokasi`
  - Kolom: `nama_sekolah` → `nama_lokasi`
  - Tambahan: kolom `alamat`, `is_active`

- `2025_08_04_044128_create_cctvs_table.php`
  - Foreign key: `sekolah_id` → `lokasi_id`
  - Kolom: `nama_titik` → `nama_cctv`

### 4. Seeders

#### WilayahSeeder (Baru)
Menambahkan data wilayah DIY:
- KABUPATEN BANTUL
- KOTA YOGYAKARTA
- KABUPATEN SLEMAN
- KABUPATEN KULON PROGO
- KABUPATEN GUNUNG KIDUL

#### SekolahSeeder (Diubah)
Sekarang menyimpan data lokasi CCTV seperti:
- CCTV BALAIKOTA BANTUL
- SIMPANG BAUSASRAN (BETHESDA)
- ALUN-ALUN UTARA
- MALIOBORO
- dll.

#### CctvSeeder (Baru)
Menambahkan sample data CCTV untuk setiap lokasi.

### 5. Controller

#### File Baru:
- `/app/Http/Controllers/LokasiController.php`
  - Menggantikan fungsi SekolahController
  - Method tetap sama, hanya menggunakan model `Lokasi`

### 6. Export

#### File Baru:
- `/app/Exports/LokasiExport.php`
  - Export data dengan kolom: No, Nama Wilayah, Nama Lokasi, Alamat, Nama CCTV, Link

## Contoh Struktur Data

```
CCTV Monitoring
├── KABUPATEN BANTUL
│   ├── CCTV BALAIKOTA BANTUL
│   │   ├── CCTV Pintu Utama
│   │   └── CCTV Parkiran
│   └── SIMPANG BAUSASRAN (BETHESDA)
│       ├── CCTV Simpang Utara
│       └── CCTV Simpang Selatan
└── KOTA YOGYAKARTA
    ├── ALUN-ALUN UTARA
    │   ├── CCTV Area Utara
    │   └── CCTV Area Selatan
    └── MALIOBORO
        ├── CCTV Titik 0
        └── CCTV Malioboro Mall
```

## Migrasi Database

Untuk menerapkan perubahan ini:

```bash
# Hapus database lama dan buat ulang dengan struktur baru
php artisan migrate:fresh --seed
```

**PERINGATAN:** Command di atas akan menghapus semua data yang ada!

## Catatan Penting

1. File model `Sekolah.php` masih ada tapi sekarang berisi class `Lokasi`
2. Untuk backward compatibility, bisa dibuat alias jika diperlukan
3. View dan route masih menggunakan nama lama (`sekolah.*`), bisa diupdate secara bertahap
4. Controller baru `LokasiController` sudah dibuat tapi belum terhubung ke route

## TODO Selanjutnya

- [ ] Update routes di `web.php` dan `api.php`
- [ ] Update view files untuk menggunakan terminologi baru
- [ ] Update JavaScript/frontend untuk field names baru
- [ ] Update validasi form
- [ ] Testing semua fitur CRUD
