<?php

namespace App\Http\Controllers;

use App\Models\Informasis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InformasiController extends Controller
{
    public function index()
    {
        $info = Informasis::latest()->get();
        return view('informasi.index', compact('info'));
    }

    /**
     * Menampilkan halaman tulis berita baru
     */
    public function create()
    {
        return view('informasi.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
            'foto'  => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload foto ke folder informasi_foto di dalam public
        $nama_file = time() . "_" . $request->file('foto')->getClientOriginalName();
        $request->file('foto')->move(public_path('informasi_foto'), $nama_file);

        Informasis::create([
            'judul' => $validatedData['judul'],
            'isi'   => $validatedData['isi'],
            'foto'  => $nama_file,
        ]);

        return redirect()->route('informasi.index')->with('notif', '✅ Informasi berhasil ditambahkan.');
    }

    public function edit(Informasis $informasi)
    {
        return view('informasi.edit', ['edit' => $informasi]);
    }

    public function update(Request $request, Informasis $informasi)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
            'foto'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $informasi->judul = $validatedData['judul'];
        $informasi->isi   = $validatedData['isi'];

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($informasi->foto && File::exists(public_path('informasi_foto/' . $informasi->foto))) {
                File::delete(public_path('informasi_foto/' . $informasi->foto));
            }

            // Upload foto baru
            $nama_file = time() . "_" . $request->file('foto')->getClientOriginalName();
            $request->file('foto')->move(public_path('informasi_foto'), $nama_file);
            $informasi->foto = $nama_file;
        }

        $informasi->save();
        return redirect()->route('informasi.index')->with('notif', '✅ Informasi berhasil diperbarui.');
    }

    public function destroy(Informasis $informasi)
    {
        if ($informasi->foto && File::exists(public_path('informasi_foto/' . $informasi->foto))) {
            File::delete(public_path('informasi_foto/' . $informasi->foto));
        }
        
        $informasi->delete();
        return redirect()->route('informasi.index')->with('notif', '🗑️ Informasi berhasil dihapus.');
    }
}