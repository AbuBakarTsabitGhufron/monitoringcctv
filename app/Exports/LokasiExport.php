<?php

namespace App\Exports;

use App\Models\Cctv; // Menggunakan model Cctv
use App\Models\Lokasi;
use App\Models\Wilayah;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class LokasiExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Query untuk mengambil data CCTV, nama lokasi, dan nama wilayah
        $data = DB::table('cctvs')
            ->join('lokasi', 'cctvs.lokasi_id', '=', 'lokasi.id')
            ->join('wilayah', 'cctvs.wilayah_id', '=', 'wilayah.id')
            ->select(
                'wilayah.nama_wilayah as namaWilayah',
                'lokasi.nama_lokasi as namaLokasi',
                'cctvs.nama_titik as namaTitik',
                'cctvs.link_stream as link'
            )
            ->orderBy('wilayah.nama_wilayah')
            ->orderBy('lokasi.nama_lokasi')
            ->orderBy('cctvs.nama_titik')
            ->get();

        // Mengubah collection menjadi format yang diinginkan dengan nomor urut
        $exportData = [];
        $no = 1;
        foreach ($data as $row) {
            $exportData[] = [
                'No'            => $no++,
                'Nama Wilayah'  => $row->namaWilayah,
                'Nama Lokasi'   => $row->namaLokasi,
                'Nama Titik'    => $row->namaTitik,
                'Link'          => $row->link,
            ];
        }

        return new Collection($exportData);
    }

    public function headings(): array
    {
        return ['No', 'Nama Wilayah', 'Nama Lokasi', 'Nama Titik', 'Link'];
    }
}
