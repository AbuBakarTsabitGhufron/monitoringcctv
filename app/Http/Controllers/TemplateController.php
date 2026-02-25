<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class TemplateController extends Controller
{
    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'namaWilayah');
        $sheet->setCellValue('B1', 'namaLokasi');  
        $sheet->setCellValue('C1', 'namaTitik');
        $sheet->setCellValue('D1', 'link');

        // Contoh isi (opsional)
        $sheet->setCellValue('A2', 'KABUPATEN BANTUL');
        $sheet->setCellValue('B2', 'SMKN 1 Bantul');
        $sheet->setCellValue('C2', 'CCTV Aula');
        $sheet->setCellValue('D2', 'http://example.com');

        // Buat file Excel ke memory
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_lokasi.xlsx';

        // Simpan ke memory
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
