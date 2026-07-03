<?php

namespace App\Http\Controllers;

use App\Models\Hari;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\Jadwal_siswa;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    /**
     * Halaman utama untuk menampilkan jadwal berdasarkan role pengguna.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $viewData = [];

        $tahunAktif = Tahun::where('status', 'Aktif')->first();
        $daftarTahun = Tahun::orderBy('nama', 'desc')->get();
        
        // Ambil tahun yang dipilih melalui filter, jika kosong gunakan tahun aktif
        $selectedTahun = $request->filled('tahun_id') 
            ? Tahun::find($request->input('tahun_id')) 
            : $tahunAktif;

        $viewData['tahunAktif'] = $tahunAktif;
        $viewData['daftarTahun'] = $daftarTahun;
        $viewData['selectedTahun'] = $selectedTahun;

        // ====================== LOGIKA UNTUK ADMIN/DEV ======================
        if ($user->role === 'Dev') {
            $viewData['jadwal'] = Jadwal::with(['mapel', 'pegawai', 'hari', 'tahun', 'kelas'])
                                ->when($selectedTahun, fn($query) => $query->where('tahun_id', $selectedTahun->id))
                                ->orderBy('hari_id')
                                ->orderBy('jam_mulai')
                                ->get();
            
            // Variabel untuk modal tambah & edit jadwal
            $viewData['kelas'] = Kelas::orderBy('kelas')->get();
            $viewData['mapel'] = Mapel::orderBy('nama')->get();
            $viewData['guru']  = Pegawai::orderBy('nama')->get();
            $viewData['hari']  = Hari::all();
        }

        // ====================== LOGIKA UNTUK GURU ======================
        if ($user->role === 'Guru') {
            if (!$user->pegawai_id) {
                session()->flash('error', 'Akun Anda belum terhubung dengan data GTK.');
                $viewData['jaguru'] = collect();
            } else {
                $viewData['jaguru'] = Hari::with(['jadwal' => function ($q) use ($user, $selectedTahun) {
                    $q->where('pegawai_id', $user->pegawai_id)
                      ->when($selectedTahun, fn($query) => $query->where('tahun_id', $selectedTahun->id))
                      ->with(['kelas', 'mapel'])
                      ->orderBy('jam_mulai');
                }])->get();
            }
        }

        // ====================== LOGIKA UNTUK SISWA ======================
        if ($user->role === 'Siswa') {
            $siswa = $user->siswa;
            $viewData['jadwalSiswaGrouped'] = collect(); // Berikan default array kosong
            $viewData['kelasSiswaPadaTahunItu'] = null;

            if ($siswa && $selectedTahun) {
                // Cari histori kelas siswa pada tahun ajaran yang dipilih
                $kelasId = null;
                $rekamNilai = Nilai::where('siswa_id', $siswa->id)->where('tahun_id', $selectedTahun->id)->first();
                
                if ($rekamNilai && $rekamNilai->kelas_id) {
                    $kelasId = $rekamNilai->kelas_id;
                } else {
                    $jadwalSiswaTbl = Jadwal_siswa::where('siswa_id', $siswa->id)->where('tahun_id', $selectedTahun->id)->first();
                    if ($jadwalSiswaTbl && $jadwalSiswaTbl->kelas_id) {
                        $kelasId = $jadwalSiswaTbl->kelas_id;
                    } elseif ($siswa->kelas && $siswa->kelas->tahun_id == $selectedTahun->id) {
                        $kelasId = $siswa->kelas_id; // Fallback ke kelas saat ini jika tahun cocok
                    }
                }

                if ($kelasId) {
                    $kelasSiswa = Kelas::find($kelasId);
                    $viewData['kelasSiswaPadaTahunItu'] = $kelasSiswa;

                    $jadwalSiswa = Jadwal::where('kelas_id', $kelasId)
                        ->where('tahun_id', $selectedTahun->id) 
                        ->with(['hari', 'mapel', 'pegawai', 'tahun'])
                        ->orderBy('hari_id')
                        ->orderBy('jam_mulai')
                        ->get();
                        
                    $viewData['jadwalSiswaGrouped'] = $jadwalSiswa->groupBy(fn($item) => $item->hari?->nama ?? 'Tanpa Hari');
                } else {
                    session()->flash('error', 'Anda tidak memiliki riwayat kelas pada tahun ajaran yang dipilih.');
                }
            } elseif (!$selectedTahun) {
                session()->flash('error', 'Sistem belum memiliki tahun ajaran aktif.');
            }
        }

        return view('jadwal.index', $viewData);
    }


    /**
     * Menampilkan jadwal pelajaran untuk kelas yang dipilih.
     */
    public function kelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $tahunAktif = Tahun::where('status', 'Aktif')->first();

        $jadwalQuery = Jadwal::where('kelas_id', $id)
            ->with(['mapel', 'pegawai', 'hari', 'tahun'])
            ->orderBy('hari_id')
            ->orderBy('jam_mulai');

        if ($tahunAktif) {
            $jadwalQuery->where('tahun_id', $tahunAktif->id);
        }
        
        $jadwal = $jadwalQuery->get();

        $mapel = Mapel::orderBy('nama')->get();
        $guru = Pegawai::orderBy('nama')->get();
        $hari = Hari::all();
        $daftarTahun = Tahun::orderBy('nama', 'desc')->get(); 

        return view('jadwal.kelas', compact('kelas', 'jadwal', 'mapel', 'guru', 'hari', 'daftarTahun', 'tahunAktif'));
    }

    /**
     * Menyimpan jadwal baru dengan tahun ajaran aktif secara otomatis.
     */
    public function store(Request $request)
    {
        $tahunAktif = Tahun::where('status', 'Aktif')->first();
        if (!$tahunAktif) {
            return back()->with('error', '❌ Tidak ada Tahun Pelajaran yang aktif. Silakan aktifkan satu tahun terlebih dahulu.');
        }

        $validated = $request->validate([
            'kelas_id'   => 'required|exists:kelases,id',
            'mapel_id'   => 'required|exists:mapels,id',
            'pegawai_id' => 'required|exists:pegawais,id',
            'hari_id'    => 'required|exists:haris,id',
            'jam_mulai'  => 'required|date_format:H:i',
            'jam_selesai'=> 'required|date_format:H:i|after:jam_mulai',
        ], [
            'required' => ':attribute wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $validated['tahun_id'] = $tahunAktif->id;

        Jadwal::create($validated);

        return redirect()->route('jadwal.kelas', $validated['kelas_id'])
            ->with('notif', "✅ Jadwal berhasil ditambahkan untuk Semester {$tahunAktif->semester} ({$tahunAktif->nama}).");
    }

    /**
     * Menampilkan form edit jadwal.
     */
    public function edit(Jadwal $jadwal)
    {
        return view('jadwal.edit', [
            'edit'        => $jadwal,
            'kelas'       => Kelas::orderBy('kelas')->get(),
            'mapel'       => Mapel::orderBy('nama')->get(),
            'guru'        => Pegawai::orderBy('nama')->get(),
            'daftarTahun' => Tahun::orderBy('nama', 'desc')->get(),
            'hari'        => Hari::all(),
        ]);
    }

    /**
     * Memperbarui jadwal pelajaran. Anda tetap bisa memilih tahun ajaran saat mengedit.
     */
    public function update(Request $request, Jadwal $jadwal)
    {
         $validated = $request->validate([
            'kelas_id'   => 'required|exists:kelases,id',
            'tahun_id'   => 'required|exists:tahuns,id',
            'mapel_id'   => 'required|exists:mapels,id',
            'pegawai_id' => 'required|exists:pegawais,id',
            'hari_id'    => 'required|exists:haris,id',
            'jam_mulai'  => 'required|date_format:H:i',
            'jam_selesai'=> 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal->update($validated);
        
        return redirect()->route('jadwal.kelas', $validated['kelas_id'])
            ->with('notif', "✅ Jadwal berhasil diperbarui.");
    }

    /**
     * Menghapus jadwal pelajaran.
     */
    public function destroy(Jadwal $jadwal)
    {
        $kelas_id = $jadwal->kelas_id;
        $jadwal->delete();
        return redirect()->route('jadwal.kelas', $kelas_id)->with('notif', '🗑️ Jadwal berhasil dihapus.');
    }
}