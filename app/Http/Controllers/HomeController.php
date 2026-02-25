<?php

namespace App\Http\Controllers;

use App\Models\cctv;
use App\Http\Controllers\cctvController;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $cctv = cctv::all();
        return view('lokasi.index'', compact('cctv'));
    }
}
