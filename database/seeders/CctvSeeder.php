<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CctvSeeder extends Seeder
{
    public function run()
    {
        // Sample CCTV data for different locations
        $cctvData = [
            // Lokasi 1: CCTV BALAIKOTA BANTUL
            ['lokasi_id' => 1, 'wilayah_id' => 1, 'nama_cctv' => 'CCTV Pintu Utama', 'link_stream' => 'rtsp://example.com/stream1'],
            ['lokasi_id' => 1, 'wilayah_id' => 1, 'nama_cctv' => 'CCTV Parkiran', 'link_stream' => 'rtsp://example.com/stream2'],
            
            // Lokasi 2: SIMPANG BAUSASRAN
            ['lokasi_id' => 2, 'wilayah_id' => 1, 'nama_cctv' => 'CCTV Simpang Utara', 'link_stream' => 'rtsp://example.com/stream3'],
            ['lokasi_id' => 2, 'wilayah_id' => 1, 'nama_cctv' => 'CCTV Simpang Selatan', 'link_stream' => 'rtsp://example.com/stream4'],
            
            // Lokasi 3: SIMPANG MANDING
            ['lokasi_id' => 3, 'wilayah_id' => 1, 'nama_cctv' => 'CCTV Perempatan', 'link_stream' => 'rtsp://example.com/stream5'],
            
            // Lokasi 4: PEREMPATAN DONGKELAN
            ['lokasi_id' => 4, 'wilayah_id' => 1, 'nama_cctv' => 'CCTV Arah Timur', 'link_stream' => 'rtsp://example.com/stream6'],
            ['lokasi_id' => 4, 'wilayah_id' => 1, 'nama_cctv' => 'CCTV Arah Barat', 'link_stream' => 'rtsp://example.com/stream7'],
            
            // Lokasi 7: ALUN-ALUN UTARA
            ['lokasi_id' => 7, 'wilayah_id' => 2, 'nama_cctv' => 'CCTV Area Utara', 'link_stream' => 'rtsp://example.com/stream8'],
            ['lokasi_id' => 7, 'wilayah_id' => 2, 'nama_cctv' => 'CCTV Area Selatan', 'link_stream' => 'rtsp://example.com/stream9'],
            
            // Lokasi 8: MALIOBORO
            ['lokasi_id' => 8, 'wilayah_id' => 2, 'nama_cctv' => 'CCTV Titik 0', 'link_stream' => 'rtsp://example.com/stream10'],
            ['lokasi_id' => 8, 'wilayah_id' => 2, 'nama_cctv' => 'CCTV Malioboro Mall', 'link_stream' => 'rtsp://example.com/stream11'],
        ];

        foreach ($cctvData as $cctv) {
            DB::table('cctvs')->insert([
                'lokasi_id' => $cctv['lokasi_id'],
                'wilayah_id' => $cctv['wilayah_id'],
                'nama_cctv' => $cctv['nama_cctv'],
                'link_stream' => $cctv['link_stream'],
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
