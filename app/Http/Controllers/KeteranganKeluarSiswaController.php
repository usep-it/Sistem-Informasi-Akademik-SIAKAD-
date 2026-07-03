<?php

namespace App\Http\Controllers;

use App\Models\KeteranganKeluarSiswa;
use Illuminate\Http\Request;

class KeteranganKeluarSiswaController extends Controller
{
    public function index()
    {
        $siswa_keluar = KeteranganKeluarSiswa::with('siswa')->get();
        return view('pd-keluar.index', compact('siswa_keluar'));
    }
}
