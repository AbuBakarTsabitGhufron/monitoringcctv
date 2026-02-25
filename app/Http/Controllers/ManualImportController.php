<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\Lokasi;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ManualImportController extends Controller
{
    public function form()
    {
        return view('lokasi.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            unset($rows[0]); // header

            $importedCount = 0;

            foreach ($rows as $row) {
                // Pastikan baris data memiliki setidaknya 4 kolom
                if (count($row) < 4) continue;
                
                // Skip empty rows
                if (empty($row[0]) && empty($row[1]) && empty($row[2]) && empty($row[3])) continue;

                $namaWilayah = trim($row[0]);
                $namaLokasi = trim($row[1]);
                $namaTitik   = trim($row[2]);
                $linkStream  = trim($row[3]);

                // Skip if essential fields are empty
                if (empty($namaWilayah) || empty($namaLokasi) || empty($namaTitik)) continue;

                // Cari atau buat wilayah
                $wilayah = Wilayah::firstOrCreate(['nama_wilayah' => $namaWilayah]);

                // Cari atau buat lokasi, dan hubungkan dengan wilayah
                $lokasi = Lokasi::firstOrCreate(
                    ['nama_lokasi' => $namaLokasi],
                    ['wilayah_id' => $wilayah->id]
                );

                // Update wilayah_id jika lokasi sudah ada tapi punya wilayah berbeda
                if ($lokasi->wilayah_id != $wilayah->id) {
                    $lokasi->wilayah_id = $wilayah->id;
                    $lokasi->save();
                }

                // Buat entri baru di tabel cctvs dengan ID lokasi dan wilayah
                Cctv::create([
                    'lokasi_id'   => $lokasi->id,
                    'wilayah_id'  => $wilayah->id,
                    'nama_cctv'   => $namaTitik,
                    'link_stream' => $linkStream,
                    'active'      => 1,
                ]);

                $importedCount++;
            }

            return back()->with('swal', [
                'status' => 'success',
                'message' => "Import berhasil! {$importedCount} data CCTV telah ditambahkan."
            ]);
        } catch (\Exception $e) {
            return back()->with('swal', [
                'status' => 'error',
                'message' => 'Import gagal: ' . $e->getMessage()
            ]);
        }
    }
}
