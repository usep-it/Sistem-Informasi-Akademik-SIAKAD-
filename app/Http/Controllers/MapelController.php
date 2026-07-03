<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MapelController extends Controller
{
    public function index()
    {
        $mapel = Mapel::orderBy('nama', 'asc')->get();
        return view('mapel.index', compact('mapel'));
    }

    public function store(Request $request)
    {
        $messages = [
            'required' => ':attribute wajib diisi !!',
            'max'      => ':attribute maksimal :max karakter.',
        ];

        $this->validate($request, [
            'nama'      => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:15', // Singkatan maksimal 15 karakter
        ], $messages);

        Mapel::create([
            'uuid'      => (string) Str::uuid(),
            'nama'      => $request->nama,
            'singkatan' => $request->singkatan,
        ]);

        return redirect('mapel')->with('notif', '✅ Data Mapel berhasil ditambah.');
    }

    public function edit($id)
    {
        $edit = Mapel::where('uuid', $id)->firstOrFail();
        return view('mapel.edit', compact('edit'));
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'required' => ':attribute wajib diisi !!',
            'max'      => ':attribute maksimal :max karakter.',
        ];

        $this->validate($request, [
            'nama'      => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:15',
        ], $messages);

        $mapel = Mapel::where('uuid', $id)->firstOrFail();
        $mapel->nama = $request->nama;
        $mapel->singkatan = $request->singkatan;
        $mapel->save();

        return redirect('/mapel')->with('notif', '✅ Data Mapel berhasil diperbarui.');
    }

    public function delete($id)
    {
        $mapel = Mapel::where('uuid', $id)->firstOrFail();
        $mapel->delete();
        return redirect('mapel')->with('notif', '🗑️ Data Mapel berhasil dihapus.');
    }
}