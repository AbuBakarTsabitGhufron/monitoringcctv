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
            ['nama_wilayah' => 'KABUPATEN BANTUL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'KOTA YOGYAKARTA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'KABUPATEN SLEMAN', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'KABUPATEN KULON PROGO', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['nama_wilayah' => 'KABUPATEN GUNUNG KIDUL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('wilayah')->insert($wilayah);
    }
}
