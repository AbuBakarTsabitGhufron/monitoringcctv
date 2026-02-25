<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    // Tambahkan properti $fillable
    protected $fillable = [
        'nama_lokasi',
        'alamat',
        'wilayah_id',
        'is_active',
    ];

    // Relasi ke tabel wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    // Relasi ke tabel cctvs (jika dibutuhkan untuk relasi hasMany)
    public function cctvs()
    {
        return $this->hasMany(Cctv::class, 'lokasi_id');
    }
}