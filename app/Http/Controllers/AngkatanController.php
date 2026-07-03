<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AngkatanController extends Controller
{
    public function index()
    {
        $angkatans = Angkatan::withCount('siswas')->get();
        return view('backend.angkatan.index', compact('angkatans'));
    }

    public function show($id)
    {
        $angkatan = Angkatan::findOrFail($id);
        $siswas = Siswa::where('angkatan_id', $id)->get();
        return view('backend.angkatan.show', compact('angkatan', 'siswas'));
    }
}
