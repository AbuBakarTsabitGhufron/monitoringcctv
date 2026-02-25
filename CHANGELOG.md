# CHANGELOG - Sistem Monitoring CCTV

## [2.0.0] - 2026-02-25

### 🔄 MAJOR REFACTORING - Generalisasi Sistem

#### ✨ Perubahan Besar
- **BREAKING CHANGE**: Sistem diubah total dari "CCTV Sekolah" menjadi "Monitoring CCTV Umum"
- Menghapus semua referensi ke "sekolah" dan menggantinya dengan "lokasi"
- Struktur database lebih generic dan mudah dipahami

#### 📊 Database Changes

**Tabel yang Diubah:**
- `sekolah` → `lokasi`
  - `nama_sekolah` → `nama_lokasi`
  - Field baru: `alamat` (alamat lengkap lokasi)
  - Field baru: `is_active` (status aktif/nonaktif)

**Tabel `cctvs`:**
- `sekolah_id` → `lokasi_id`
- `nama_titik` → `nama_cctv`
- Relasi sekarang: `lokasi_id` references `lokasi(id)`

#### 📁 File Structure Changes

**Models:**
- ✅ `Sekolah.php` → `Lokasi.php`
- ❌ Deleted: `SekolahAlias.php` (tidak diperlukan lagi)

**Controllers:**
- ✅ `SekolahController.php` → `LokasiController.php`
- ✅ `ApiSekolahController.php` → `ApiLokasiController.php`

**Views:**
- ✅ `resources/views/sekolah/` → `resources/views/lokasi/`
- ✅ `sekolah.blade.php` → `index.blade.php`
- ✅ `menu-sekolah.blade.php` → `menu-lokasi.blade.php`
- ✅ `cctv_sekolah.blade.php` → `cctv_lokasi.blade.php`
- ✅ `detailsekolah.blade.php` → `detaillokasi.blade.php`
- ✅ `layouts/user_type/sekolah.blade.php` → `lokasi.blade.php`

**Assets:**
- ✅ `public/js/sekolah.js` → `lokasi.js`
- ✅ `public/js/dashboard_sekolah.js` → `dashboard_lokasi.js`
- ✅ `public/css/sekolah.css` → `lokasi.css`

**Database:**
- ✅ `SekolahSeeder.php` → `LokasiSeeder.php`
- ✅ `SekolahExport.php` → `LokasiExport.php`
- ✅ `create_sekolah_table.php` → `create_lokasi_table.php`
- ✅ `add_is_active_to_sekolah_table.php` → `add_is_active_to_lokasi_table.php`

#### 🛣️ Routes Changes

**Web Routes:**
```php
// Before:
Route::get('/', [SekolahController::class, 'cctvsekolah'])->name('sekolah.sekolah');
Route::get('cctv-sekolah', [SekolahController::class, 'index'])->name('menu-sekolah');

// After:
Route::get('/', [LokasiController::class, 'cctvlokasi'])->name('lokasi.index');
Route::get('cctv-lokasi', [LokasiController::class, 'index'])->name('menu-lokasi');
```

**API Routes:**
```php
// Before:
Route::apiResource('cctvsekolah', ApiSekolahController::class);

// After:
Route::apiResource('cctvlokasi', ApiLokasiController::class);
```

#### 📝 Naming Convention Updates

| Kategori | Before | After |
|----------|--------|-------|
| Table | `sekolah` | `lokasi` |
| Model | `Sekolah` | `Lokasi` |
| Controller | `SekolahController` | `LokasiController` |
| Foreign Key | `sekolah_id` | `lokasi_id` |
| Column | `nama_sekolah` | `nama_lokasi` |
| Column | `nama_titik` | `nama_cctv` |
| Route Name | `sekolah.sekolah` | `lokasi.index` |
| View Path | `sekolah/sekolah` | `lokasi/index` |
| JS File | `sekolah.js` | `lokasi.js` |
| CSS File | `sekolah.css` | `lokasi.css` |

#### 🗄️ Sample Data

**Lokasi yang ditambahkan:**
1. CCTV BALAIKOTA BANTUL - Jl. Robert Wolter Monginsidi
2. SIMPANG BAUSASRAN (BETHESDA) - Jl. Bausasran
3. ALUN-ALUN UTARA - Jl. Rotowijayan
4. MALIOBORO - Jl. Malioboro
5. TUGU JOGJA - Jl. Jenderal Sudirman
6. SIMPANG JOMBOR - Jl. Magelang Km 5
7. Dan lainnya (total 16 lokasi)

**Wilayah:**
1. Kabupaten Bantul
2. Kota Yogyakarta  
3. Kabupaten Sleman
4. Kabupaten Kulon Progo
5. Kabupaten Gunung Kidul

#### ✅ Migration Status

```bash
# Tested and verified:
php artisan migrate:fresh --seed

# Result:
✅ 16 Lokasi created
✅ 11 CCTV created
✅ 5 Wilayah created
✅ All relationships working correctly
```

#### 📚 Documentation

New documentation files:
- ✅ `STRUKTUR_SISTEM.md` - Comprehensive system structure guide
- ✅ `DATABASE_CHANGES.md` - Database migration details
- ✅ `CHANGELOG.md` - This file

#### 🔧 Technical Improvements

1. **Code Quality:**
   - Removed all hardcoded "sekolah" references
   - Consistent naming convention throughout the system
   - Better separation of concerns

2. **Database:**
   - Added `alamat` field to lokasi table
   - Added `is_active` status flags
   - Improved foreign key relationships

3. **User Experience:**
   - More intuitive naming (lokasi vs sekolah)
   - Clearer data hierarchy: Wilayah → Lokasi → CCTV
   - Better organization of views and routes

#### ⚠️ Breaking Changes

1. **API Endpoints:**
   - `/api/cctvsekolah` → `/api/cctvlokasi`
   - Response JSON keys changed (nama_sekolah → nama_lokasi, etc.)

2. **Database:**
   - Table `sekolah` renamed to `lokasi`
   - Columns renamed (breaking change for direct SQL queries)

3. **Routes:**
   - Named routes changed (sekolah.* → lokasi.*)
   - URL paths changed (/cctv-sekolah → /cctv-lokasi)

#### 🚀 Migration Guide

For existing deployments:

```bash
# 1. Backup database
mysqldump -u root -p monitoring_cctv > backup_before_migration.sql

# 2. Pull latest code
git pull origin main

# 3. Run migrations (WARNING: Will drop all data)
php artisan migrate:fresh --seed

# 4. Verify data
php artisan tinker
> App\Models\Lokasi::count()
> App\Models\Cctv::count()
```

#### 📞 Support & Questions

For questions about this migration:
- Read: `STRUKTUR_SISTEM.md`
- Review: `DATABASE_CHANGES.md`
- Check: Laravel documentation at https://laravel.com/docs

---

## [1.0.0] - Before 2026-02-25

### Initial Release
- Basic CCTV monitoring system with school-based structure
- Sekolah (school) as primary entity
- Basic CRUD operations
- Dashboard and reporting features
