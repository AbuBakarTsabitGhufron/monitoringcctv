<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cctv;
use App\Models\Lokasi;
use App\Models\Wilayah;
use App\Models\Panorama;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LokasiExport;
use Illuminate\Support\Str;

class LokasiController extends Controller
{
    public function dashboard()
    {
        // Hitung total jumlah CCTV dari tabel cctvs yang terhubung dengan lokasi
        // Ini akan memberikan total semua CCTV lokasi, bukan hanya jumlah lokasi.
        $lokasiCount = Cctv::whereNotNull('lokasi_id')->count();

        // Hitung total jumlah panorama
        $panoramaCount = Panorama::count();

        // Hitung total jumlah user
        $userCount = User::count();

        // Statistik jumlah lokasi per wilayah
        $jumlahLokasiPerWilayah = Lokasi::select('wilayah.nama_wilayah as namaWilayah', DB::raw('COUNT(lokasi.id) as total_lokasi'))
            ->join('wilayah', 'lokasi.wilayah_id', '=', 'wilayah.id')
            ->groupBy('wilayah.nama_wilayah')
            ->get();

        // Statistik jumlah CCTV per wilayah
        $jumlahCCTVPerWilayah = Cctv::select('wilayah.nama_wilayah as namaWilayah', DB::raw('COUNT(cctvs.link_stream) as total_cctv'))
            ->join('wilayah', 'cctvs.wilayah_id', '=', 'wilayah.id')
            ->whereNotNull('cctvs.link_stream')
            ->groupBy('wilayah.nama_wilayah')
            ->get();

        // Statistik jumlah CCTV per lokasi
        $jumlahCCTVPerLokasi = Cctv::select('lokasi.nama_lokasi as namaLokasi', DB::raw('COUNT(cctvs.link_stream) as total_cctv'))
            ->join('lokasi', 'cctvs.lokasi_id', '=', 'lokasi.id')
            ->whereNotNull('cctvs.link_stream')
            ->groupBy('lokasi.nama_lokasi')
            ->get();

        return view('admin.dashboard', compact(
            'lokasiCount', 'panoramaCount', 'userCount',
            'jumlahLokasiPerWilayah', 'jumlahCCTVPerWilayah', 'jumlahCCTVPerLokasi'
        ));
    }

    public function cctvlokasi()
    {
        // Ambil CCTV aktif saja + eager load relasi, dan hanya kolom yang dibutuhkan
        $cctvs = Cctv::with(['lokasi', 'wilayah'])
            ->where('active', true)
            ->select('id', 'lokasi_id', 'wilayah_id', 'nama_cctv', 'link_stream', 'active')
            ->orderBy('wilayah_id')->orderBy('lokasi_id')->orderBy('nama_cctv')->get();

        $groupedCctvs = $cctvs
            ->sortBy(fn($c) => [$c->wilayah->nama_wilayah, $c->lokasi->nama_lokasi, $c->nama_cctv])
            ->groupBy(fn($c) => $c->wilayah->nama_wilayah)
            ->sortKeys() // urutkan wilayah
            ->map(function ($wg) {
                return $wg->groupBy(fn($c) => $c->lokasi->nama_lokasi)
                        ->sortKeys() // urutkan lokasi
                        ->map(fn($sg) => $sg->sortBy('nama_cctv')); // urutkan titik CCTV
            });

        // Statistik ringkas dan efisien
        $jumlahCCTV = Cctv::count();
        $jumlahLokasi = Lokasi::count();
        $jumlahWilayah = Wilayah::count();
        $jumlahCCTVaktif = Cctv::where('active', true)->count();

        // Mapping label wilayah (dipakai di blade)
        $namaWilayahLengkap = [
            'KABUPATEN GK'  => 'KABUPATEN GUNUNG KIDUL',
            'KABUPATEN KP'  => 'KABUPATEN KULONPROGO',
            'KABUPATEN BTL' => 'KABUPATEN BANTUL',
            'KABUPATEN SLM' => 'KABUPATEN SLEMAN',
            'KOTA YK'       => 'KOTA YOGYAKARTA',
        ];

        // Build lightweight index for lazy render on the client
        $cctvIndex = $cctvs->map(function ($c) {
            return [
                'wilayah'     => $c->wilayah->nama_wilayah,
                'lokasi'     => $c->lokasi->nama_lokasi,
                'lokasiSlug' => Str::slug($c->lokasi->nama_lokasi),
                'titik'       => $c->nama_cctv,
                'link'        => $c->link_stream,
                'active'      => (bool) $c->active,
                'cardId'      => Str::slug($c->lokasi->nama_lokasi . '-' . $c->nama_cctv),
            ];
        })->groupBy('lokasiSlug'); // lokasiSlug => [items...]

        return view('lokasi.index', compact(
            'groupedCctvs',
            'jumlahCCTV',
            'jumlahLokasi',
            'jumlahWilayah',
            'jumlahCCTVaktif',
            'namaWilayahLengkap',
            'cctvIndex' // ADD
        ));
    }

    public function bulkToggle(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'active' => 'required|boolean',
        ]);

        Cctv::whereIn('id', $request->ids)
            ->update(['active' => $request->active]);

        return response()->json([
            'success' => true,
            'message' => 'Status semua CCTV berhasil diperbarui.',
        ]);
    }

    public function index()
    {
        $cctvs = Cctv::with(['lokasi', 'wilayah'])->paginate(10);
        return view('lokasi.menu-lokasi', compact('cctvs'));
    }

    public function create()
    {
        $lokasis = Lokasi::all();
        $wilayahs = Wilayah::all();
        return view('lokasi.create', compact('lokasis', 'wilayahs'));
    }

    public function store(Request $request)
    {
        $cctv = new Cctv;
        $cctv->lokasi_id = $request->lokasi_id;
        $cctv->wilayah_id = $request->wilayah_id;
        $cctv->nama_cctv = $request->namaTitik;
        $cctv->link_stream = $request->link;
        $cctv->active = true;
        $cctv->save();
        return redirect()->route('lokasi.index');
    }

    public function edit($id)
    {
        $cctv = Cctv::with(['lokasi', 'wilayah'])->findOrFail($id);
        $lokasis = Lokasi::all();
        $wilayahs = Wilayah::all();
        return view('lokasi.edit', compact('cctv', 'lokasis', 'wilayahs'));
    }

    public function update(Request $request, $id)
    {
        $cctv = Cctv::findOrFail($id);
        $cctv->lokasi_id = $request->lokasi_id;
        $cctv->wilayah_id = $request->wilayah_id;
        $cctv->nama_cctv = $request->namaTitik;
        $cctv->link_stream = $request->link;
        $cctv->save();
        return redirect()->route('lokasi.index');
    }

    public function delete($id)
    {
        $cctv = Cctv::findOrFail($id);
        $cctv->delete();
        return redirect()->route('lokasi.index');
    }

    public function checkDuplicate(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        $exists = Cctv::where($field === 'namaTitik' ? 'nama_cctv' : 'link_stream', $value)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function getWilayah()
    {
        $wilayahs = Wilayah::select('nama_wilayah')->distinct()->get();
        return response()->json($wilayahs);
    }

    public function export()
    {
        return Excel::download(new LokasiExport, 'data-cctv-lokasi.xlsx');
    }

    public function showRekapanCCTV()
    {
        $data = DB::table('cctvs')
            ->join('lokasi', 'cctvs.lokasi_id', '=', 'lokasi.id')
            ->join('wilayah', 'cctvs.wilayah_id', '=', 'wilayah.id')
            ->select(
                'lokasi.id as lokasiId',
                'lokasi.nama_lokasi as namaLokasi',
                'wilayah.nama_wilayah as namaWilayah',
                DB::raw('COUNT(cctvs.id) as total_cctv')
            )
            ->groupBy('lokasi.id', 'lokasi.nama_lokasi', 'wilayah.nama_wilayah')
            ->orderBy('lokasi.nama_lokasi', 'asc')
            ->get();

        // Ambil detail titik CCTV per lokasi
        $detailCCTV = DB::table('cctvs')
            ->join('lokasi', 'cctvs.lokasi_id', '=', 'lokasi.id')
            ->select(
                'lokasi.id as lokasiId',
                'cctvs.nama_cctv as namaTitik',
                'cctvs.link_stream as linkStream'
            )
            ->orderBy('cctvs.nama_cctv', 'asc')
            ->get()
            ->groupBy('lokasiId');

        return view('rekapan.cctv_lokasi', [
            'jumlahCCTVPerLokasi' => $data,
            'detailCCTV' => $detailCCTV
        ]);
    }

    public function daftarLokasi()
    {
        $lokasi = Lokasi::with('wilayah')->get();
        return view('rekapan.detaillokasi', compact('lokasi'));
    }


    public function toggle($id)
    {
        $cctv = Cctv::findOrFail($id);
        $cctv->active = !$cctv->active;
        $cctv->save();

        return response()->json([
            'success' => true,
            'message' => 'Status CCTV berhasil diubah.',
            'data' => $cctv
        ]);
    }
}
