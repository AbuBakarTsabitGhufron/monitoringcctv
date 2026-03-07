<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $cctv = Cctv::all();
        return view('lokasi.index', compact('cctv'));
    }
}
