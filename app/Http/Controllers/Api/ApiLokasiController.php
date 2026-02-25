<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\GlobalResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cctv;
use App\Models\Lokasi;
use Illuminate\Support\Facades\Validator;

class ApiLokasiController extends Controller
{
    public function index()
    {
        try {
            $cctvs = Cctv::with(['lokasi', 'wilayah'])
                ->orderBy('wilayah_id')
                ->orderBy('lokasi_id')
                ->orderBy('nama_cctv')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'wilayah_id' => $item->wilayah_id, // Perbaikan: Menambahkan wilayah_id
                        'nama_wilayah' => $item->wilayah->nama_wilayah ?? '-',
                        'nama_lokasi' => $item->lokasi->nama_lokasi ?? '-',
                        'nama_cctv' => $item->nama_cctv,
                        'link' => $item->link_stream,
                        'is_active' => $item->active ? true : false,
                    ];
                });

            if ($cctvs->isEmpty()) {
                return new GlobalResource(false, 'Belum ada data CCTV lokasi yang tersedia.', null);
            }

            return new GlobalResource(true, 'Data CCTV lokasi berhasil dimuat.', $cctvs);
        } catch (\Exception $e) {
            return new GlobalResource(false, 'Terjadi kesalahan saat mengambil data.', null);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'wilayah_id' => 'required|exists:wilayah,id',
                'nama_lokasi' => 'required|string|max:255',
                'nama_cctv' => 'required|string|max:255',
                'link_stream' => 'required|url|unique:cctvs,link_stream',
            ]);

            if ($validator->fails()) {
                return new GlobalResource(false, 'Data yang Anda masukkan tidak valid.', $validator->errors());
            }

            $lokasi = Lokasi::firstOrCreate(
                ['nama_lokasi' => $request->nama_lokasi],
                ['wilayah_id' => $request->wilayah_id]
            );

            // PERBAIKAN: Mengatur status 'active' menjadi TRUE secara eksplisit
            $cctv = Cctv::create([
                'wilayah_id' => $request->wilayah_id,
                'lokasi_id' => $lokasi->id,
                'nama_cctv' => $request->nama_cctv,
                'link_stream' => $request->link_stream,
                'active' => true, // Selalu true saat membuat data baru
            ]);

            return new GlobalResource(true, 'Data CCTV lokasi berhasil ditambahkan.', $cctv);
        } catch (\Exception $e) {
            return new GlobalResource(false, 'Terjadi kesalahan saat menyimpan data. ' . $e->getMessage(), null);
        }
    }

    public function show(string $id)
    {
        try {
            $data = Cctv::with(['lokasi', 'wilayah'])->find($id);

            if (!$data) {
                return new GlobalResource(false, 'Data CCTV lokasi tidak ditemukan.', null);
            }

            // Catatan untuk Front-End:
            // Data 'wilayah' di sini berisi informasi wilayah yang terkait dengan CCTV ini.
            // Gunakan `data->wilayah_id` untuk mencocokkan dan memilih opsi yang benar di dropdown Wilayah pada form edit.
            return new GlobalResource(true, 'Detail data CCTV lokasi berhasil dimuat.', $data);
        } catch (\Exception $e) {
            return new GlobalResource(false, 'Terjadi kesalahan saat memuat data CCTV lokasi.', null);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = Cctv::find($id);

            if (!$data) {
                return new GlobalResource(false, 'Data CCTV tidak ditemukan.', null);
            }

            $validator = Validator::make($request->all(), [
                'wilayah_id' => 'required|exists:wilayah,id',
                'nama_lokasi' => 'required|string|max:255',
                'nama_cctv' => 'required|string|max:255',
                'link_stream' => 'required|url|unique:cctvs,link_stream,' . $id,
            ]);

            if ($validator->fails()) {
                return new GlobalResource(false, 'Data yang Anda masukkan tidak valid.', $validator->errors());
            }

            $lokasi = Lokasi::firstOrCreate(['nama_lokasi' => $request->nama_lokasi]);
            
            // PERBAIKAN: Memastikan status 'active' tidak berubah saat update jika tidak dikirimkan.
            $data->update([
                'wilayah_id' => $request->wilayah_id,
                'lokasi_id' => $lokasi->id,
                'nama_cctv' => $request->nama_cctv,
                'link_stream' => $request->link_stream,
                'active' => $request->has('active') ? $request->active : $data->active,
            ]);

            return new GlobalResource(true, 'Data CCTV lokasi berhasil diperbarui.', $data);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return new GlobalResource(false, 'Link CCTV sudah digunakan. Silakan gunakan link yang berbeda.', null);
            }
            return new GlobalResource(false, 'Terjadi kesalahan saat memperbarui data CCTV lokasi.', null);
        }
    }

    public function destroy(string $id)
    {
        try {
            $data = Cctv::find($id);

            if (!$data) {
                return new GlobalResource(false, 'Data CCTV lokasi tidak ditemukan.', null);
            }

            $schoolId = $data->lokasi_id;

            // Hapus CCTV
            $data->delete();

            // Cek apakah lokasi ini masih punya CCTV
            $remainingCCTV = Cctv::where('lokasi_id', $schoolId)->count();
            if ($remainingCCTV === 0) {
                Lokasi::where('id', $schoolId)->delete();
            }

            return new GlobalResource(true, 'Data CCTV lokasi berhasil dihapus.', null);
        } catch (\Exception $e) {
            return new GlobalResource(false, 'Terjadi kesalahan saat menghapus data CCTV lokasi.', null);
        }
    }


    // Fungsi baru untuk mengaktifkan/menonaktifkan banyak data sekaligus
    public function bulkToggle(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:cctvs,id',
                'active' => 'required|boolean', // <-- Perubahan: Gunakan 'active'
            ]);

            if ($validator->fails()) {
                return new GlobalResource(false, 'Data yang Anda masukkan tidak valid.', $validator->errors());
            }

            Cctv::whereIn('id', $request->ids)->update(['active' => $request->active]); // <-- Perubahan: Gunakan 'active'

            $message = $request->active ? 'Berhasil mengaktifkan semua data.' : 'Berhasil menonaktifkan semua data.';

            return new GlobalResource(true, $message, null);
        } catch (\Exception $e) {
            return new GlobalResource(false, 'Terjadi kesalahan saat mengubah status data.', null);
        }
    }
}
