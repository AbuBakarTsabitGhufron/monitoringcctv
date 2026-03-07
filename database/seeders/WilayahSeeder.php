<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WilayahSeeder extends Seeder
{
    public function run()
    {
        $wilayah = [
            ['nama_wilayah' => 'KABUPATEN BANTUL',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'KOTA YOGYAKARTA',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'KABUPATEN SLEMAN',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'KABUPATEN KULON PROGO',  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'KABUPATEN GUNUNG KIDUL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'ATCS KOTA',              'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'ATCS DIY',               'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'MALIOBORO',              'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'PANORAMA',               'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        foreach ($wilayah as $row) {
            DB::table('wilayah')->updateOrInsert(
                ['nama_wilayah' => $row['nama_wilayah']],
                $row
            );
        }
    }
}
