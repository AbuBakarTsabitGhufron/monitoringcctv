<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cctv extends Model
{
    use HasFactory;

    protected $table = 'cctvs';

    // Tambahkan properti $fillable di sini
    protected $fillable = [
        'wilayah_id',
        'lokasi_id',
        'nama_cctv',
        'link_stream',
        'active',
    ];

    // Relasi ke tabel lokasi
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    // Relasi ke tabel wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }
}