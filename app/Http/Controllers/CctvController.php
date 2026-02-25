<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cctv;

class cctvController extends Controller
{
    public function __construct()
    {
        // Share data to all views so lokasi.blade.php can use them directly.
        view()->share('activeCctvCount', $this->getActiveCctvCount());
        view()->share('sidebarCctvVisible', session('sidebar_cctv_visible', true));
    }

    public function index()
    {
        $cctv = cctv::all();
        return view('cctv.index', compact('cctv'));
    }

    public function edit($id)
    {
        $cctv = cctv::find($id);
        return view('cctv.edit', compact('cctv'));
    }

    public function update(Request $request, $id)
    {
        $cctv = cctv::find($id);
        $cctv->namaWilayah = $request->namaWilayah;
        $cctv->namaTitik = $request->namaTitik;
        $cctv->link = $request->link;
        $cctv->save();
        return redirect()->route('cctv.index');
    }

    public function create()
    {
        return view('cctv.create');
    }

    public function store(Request $request)
    {
        $cctv = new cctv;
        $cctv->namaWilayah = $request->namaWilayah;
        $cctv->namaTitik = $request->namaTitik;
        $cctv->link = $request->link;
        $cctv->save();
        return redirect()->route('cctv.index');
    }

    public function delete($id)
    {
        $cctv = cctv::find($id);
        $cctv->delete();
        return redirect()->route('cctv.index');
    }

    // Return current sidebar visibility and current active CCTV count.
    public function getSidebarState(Request $request)
    {
        return response()->json([
            'visible' => session('sidebar_cctv_visible', true),
            'activeCount' => $this->getActiveCctvCount(),
        ]);
    }

    // Toggle sidebar visibility and return the new state (sync your fa-eye/fa-eye-slash with this).
    public function toggleSidebar(Request $request)
    {
        $visible = !session('sidebar_cctv_visible', true);
        session(['sidebar_cctv_visible' => $visible]);

        return response()->json([
            'visible' => $visible,
            'activeCount' => $this->getActiveCctvCount(),
        ]);
    }

    // Helper: define "active" as having a non-empty link.
    private function getActiveCctvCount(): int
    {
        return cctv::whereNotNull('link')->where('link', '!=', '')->count();
    }
}
