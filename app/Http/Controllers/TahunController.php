<?php

namespace App\Http\Controllers;

use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunController extends Controller
{
    /**
     * Menampilkan daftar semua tahun pelajaran.
     */
    public function index()
    {
        // Urutkan agar 'Aktif' muncul di atas, lalu berdasarkan nama tahun terbaru
        $tahun = Tahun::orderByRaw("CASE WHEN status = 'Aktif' THEN 0 ELSE 1 END")
                      ->orderBy('nama', 'desc')
                      ->get();

        return view('tahun.index', compact('tahun'));
    }

    /**
     * Menyimpan tahun pelajaran baru (tidak langsung diaktifkan).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:tahuns,nama,NULL,id,semester,' . $request->semester,
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        // Simpan tanpa mengubah status tahun lain, dan status default = Tidak Aktif
        Tahun::create([
            'nama' => $request->nama,
            'semester' => $request->semester,
            'status' => 'Tidak Aktif', // tidak langsung aktif
        ]);

        return redirect()->route('tahun.index')->with('notif', '🆕 Tahun Pelajaran baru berhasil ditambahkan (belum diaktifkan).');
    }

    /**
     * Menampilkan form edit (opsional jika pakai modal).
     */
    public function edit(Tahun $tahun)
    {
        return view('tahun.edit', compact('tahun'));
    }

    /**
     * Memperbarui data tahun pelajaran.
     */
    public function update(Request $request, Tahun $tahun)
    {
        $request->validate([
            'nama' => 'required|string|unique:tahuns,nama,' . $tahun->id . ',id,semester,' . $request->semester,
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        $tahun->update($request->only(['nama', 'semester']));

        return redirect()->route('tahun.index')->with('notif', '✏️ Data Tahun Pelajaran berhasil diperbarui.');
    }

    /**
     * Menghapus tahun pelajaran (tidak boleh menghapus yang aktif).
     */
    public function destroy(Tahun $tahun)
    {
        if ($tahun->status == 'Aktif') {
            return redirect()->route('tahun.index')->with('error', '❌ Tahun Pelajaran yang sedang aktif tidak bisa dihapus.');
        }

        $tahun->delete();
        return redirect()->route('tahun.index')->with('notif', '🗑️ Data Tahun Pelajaran berhasil dihapus.');
    }

    /**
     * Mengaktifkan satu tahun pelajaran dan menonaktifkan yang lain.
     */
    public function toggleStatus(Tahun $tahun)
    {
        DB::transaction(function () use ($tahun) {
            Tahun::query()->update(['status' => 'Tidak Aktif']);
            $tahun->update(['status' => 'Aktif']);
        });

        return redirect()->route('tahun.index')->with('notif', "✅ Tahun Pelajaran <strong>{$tahun->nama} Semester {$tahun->semester}</strong> berhasil diaktifkan.");
    }
}
