# Struktur Sistem Monitoring CCTV

## 📋 Deskripsi Umum
Sistem ini adalah aplikasi web untuk monitoring CCTV berbasis lokasi. Sistem menggunakan hierarki:
- **Wilayah** (contoh: Kabupaten Bantul, Kota Yogyakarta)
- **Lokasi** (contoh: CCTV Balaikota Bantul, Simpang Bausasran)
- **CCTV** (kamera individu di setiap lokasi)

## 🗂️ Struktur Database

### Tabel `wilayah`
Menyimpan data wilayah administratif (kabupaten/kota).
- `id` - Primary key
- `nama_wilayah` - Nama wilayah (contoh: "Kabupaten Bantul")
- `timestamps`

### Tabel `lokasi` 
Menyimpan data lokasi pemasangan CCTV.
- `id` - Primary key
- `nama_lokasi` - Nama lokasi (contoh: "CCTV BALAIKOTA BANTUL")
- `alamat` - Alamat lengkap lokasi
- `wilayah_id` - Foreign key ke tabel `wilayah`
- `is_active` - Status aktif/nonaktif
- `timestamps`

### Tabel `cctvs`
Menyimpan data kamera CCTV individual.
- `id` - Primary key
- `lokasi_id` - Foreign key ke tabel `lokasi`
- `wilayah_id` - Foreign key ke tabel `wilayah`
- `nama_cctv` - Nama kamera CCTV
- `link_stream` - URL streaming CCTV
- `active` - Status aktif/nonaktif
- `timestamps`

## 📁 Struktur File & Folder

### Models
- `app/Models/Wilayah.php` - Model untuk wilayah
- `app/Models/Lokasi.php` - Model untuk lokasi CCTV
- `app/Models/Cctv.php` - Model untuk kamera CCTV

### Controllers
- `app/Http/Controllers/LokasiController.php` - Controller utama untuk lokasi
- `app/Http/Controllers/Api/ApiLokasiController.php` - API controller untuk lokasi

### Views
- `resources/views/lokasi/` - Direktori view untuk lokasi
  - `index.blade.php` - Halaman utama daftar CCTV
  - `menu-lokasi.blade.php` - Menu manajemen lokasi
- `resources/views/rekapan/` - Direktori view untuk rekapan
  - `cctv_lokasi.blade.php` - Rekapan CCTV per lokasi
  - `detaillokasi.blade.php` - Detail lokasi

### Routes
- `routes/web.php` - Routes untuk web interface
- `routes/api.php` - Routes untuk API endpoints

### Assets
- `public/js/lokasi.js` - JavaScript untuk halaman lokasi
- `public/js/dashboard_lokasi.js` - JavaScript untuk dashboard
- `public/css/lokasi.css` - Stylesheet untuk lokasi

### Database
- `database/migrations/2025_04_30_090314_create_lokasi_table.php` - Migration tabel lokasi
- `database/migrations/2025_08_04_044128_create_cctvs_table.php` - Migration tabel cctvs
- `database/seeders/LokasiSeeder.php` - Seeder untuk data lokasi
- `database/seeders/CctvSeeder.php` - Seeder untuk data CCTV
- `database/seeders/WilayahSeeder.php` - Seeder untuk data wilayah

## 🔄 Relasi Antar Model

```php
Wilayah (1) ---> (N) Lokasi
Wilayah (1) ---> (N) Cctv
Lokasi (1) ---> (N) Cctv
```

### Wilayah Model
```php
public function lokasi() {
    return $this->hasMany(Lokasi::class, 'wilayah_id');
}

public function cctvs() {
    return $this->hasMany(Cctv::class, 'wilayah_id');
}
```

### Lokasi Model
```php
public function wilayah() {
    return $this->belongsTo(Wilayah::class, 'wilayah_id');
}

public function cctvs() {
    return $this->hasMany(Cctv::class, 'lokasi_id');
}
```

### Cctv Model
```php
public function lokasi() {
    return $this->belongsTo(Lokasi::class, 'lokasi_id');
}

public function wilayah() {
    return $this->belongsTo(Wilayah::class, 'wilayah_id');
}
```

## 🛣️ Routes Utama

### Web Routes
```php
Route::get('/', [LokasiController::class, 'cctvlokasi'])->name('lokasi.index');
Route::get('/dashboard', [LokasiController::class, 'dashboard'])->name('dashboard');
Route::get('cctv-lokasi', [LokasiController::class, 'index'])->name('menu-lokasi');
```

### API Routes
```php
Route::apiResource('cctvlokasi', ApiLokasiController::class);
Route::post('/cctvlokasi/{id}/toggle', [LokasiController::class, 'toggle']);
Route::post('/cctvlokasi/bulk-toggle', [LokasiController::class, 'bulkToggle']);
```

## 🔧 Perintah Artisan

### Migration & Seeding
```bash
# Reset database dan seed ulang
php artisan migrate:fresh --seed

# Jalankan hanya migration
php artisan migrate

# Jalankan hanya seeder
php artisan db:seed
```

### Testing
```bash
# Test koneksi database
php artisan tinker
> App\Models\Lokasi::count()
> App\Models\Cctv::count()
```

## 📊 Contoh Data

### Wilayah
- Kabupaten Bantul
- Kota Yogyakarta
- Kabupaten Sleman
- Kabupaten Kulon Progo
- Kabupaten Gunung Kidul

### Lokasi (contoh)
- CCTV BALAIKOTA BANTUL
- SIMPANG BAUSASRAN (BETHESDA)
- ALUN-ALUN UTARA
- MALIOBORO
- TUGU JOGJA

## ⚠️ Catatan Penting

1. **Tidak ada lagi referensi "sekolah"** - Seluruh sistem sudah digeneralisir menjadi "lokasi"
2. **Field Names** - Gunakan `lokasi_id`, `nama_lokasi`, `nama_cctv` (bukan `sekolah_id`, `nama_sekolah`, `nama_titik`)
3. **Table Names** - Tabel menggunakan nama `lokasi`, `cctvs`, `wilayah`
4. **Controller Names** - LokasiController (bukan SekolahController)
5. **View Paths** - `resources/views/lokasi/` (bukan `resources/views/sekolah/`)
6. **Route Names** - Gunakan prefix `lokasi.` (contoh: `lokasi.index`, `lokasi.show`)

## 🚀 Menambah Fitur Baru

### Menambah Lokasi Baru
1. Buat data di tabel `lokasi` melalui form atau seeder
2. Pastikan `wilayah_id` valid
3. Set `is_active` sesuai kebutuhan

### Menambah CCTV Baru  
1. Buat data di tabel `cctvs`
2. Isi `lokasi_id` dan `wilayah_id` 
3. Isi `nama_cctv` dan `link_stream`
4. Set `active` = true untuk mengaktifkan

## 📝 Naming Convention

- **Models**: PascalCase singular (Lokasi, Cctv, Wilayah)
- **Tables**: snake_case plural (lokasi, cctvs, wilayah) - *Note: lokasi tetap singular*
- **Columns**: snake_case (nama_lokasi, wilayah_id, link_stream)
- **Variables**: camelCase ($lokasiCount, $cctvList)
- **Routes**: kebab-case (cctv-lokasi, menu-lokasi)
- **Views**: kebab-case (menu-lokasi.blade.php)

## 🔐 Akses & Authorization

Sistem menggunakan middleware Laravel:
- `auth` - Hanya user terautentikasi
- `role:admin` - Hanya untuk admin (contoh: dashboard)

## 📞 Support

Untuk pertanyaan atau issue, silakan baca dokumentasi Laravel di https://laravel.com/docs
