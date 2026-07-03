<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Tahun;
use App\Models\KeteranganKeluarSiswa;
use App\Models\Jadwal;
use App\Models\Jadwal_siswa;
use App\Models\Nilai;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use App\Exports\SiswaTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    /**
     * ======================================================================
     * BAGIAN PENGELOLAAN DATA SISWA (CRUD - ADMIN/DEV)
     * ======================================================================
     */

    public function index(Request $request)
    {
        $tahunAktif = Tahun::where('status', 'Aktif')->first();
        $daftarTahun = Tahun::orderBy('nama', 'desc')->get();

        $selectedTahunId = $request->input('tahun_id', $tahunAktif?->id);
        $selectedTahun = $selectedTahunId ? Tahun::find($selectedTahunId) : null;

        if (!$tahunAktif && !$selectedTahunId) {
            return view('siswa.index', [
                'siswa' => collect(),
                'kelas' => collect(),
                'tahunAktif' => null,
                'daftarTahun' => $daftarTahun,
                'selectedTahun' => null
            ])->with('error', '⚠️ Belum ada tahun ajaran aktif atau tahun ajaran belum dipilih.');
        }

        $kelasQuery = Kelas::query();
        if ($selectedTahunId) {
             $kelasQuery->where('tahun_id', $selectedTahunId);
        }
        $kelas = $kelasQuery->orderBy('kelas')->get();

        $query = Siswa::with(['kelas' => function ($q) {
            $q->select('id', 'kelas', 'nama', 'tahun_id')->with('tahun:id,nama,semester'); 
        }])->where('status', 'Aktif');

        if ($selectedTahunId) {
            $query->where(function($q) use ($selectedTahunId) {
                $q->whereHas('kelas', function ($subQ) use ($selectedTahunId) {
                    $subQ->where('tahun_id', $selectedTahunId);
                })->orWhereNull('kelas_id'); 
            });
        }

        if ($request->filled('kelas_id')) {
            if ($request->kelas_id == 'tanpa_kelas') {
                $query->whereNull('kelas_id'); 
            } else {
                 $query->where('kelas_id', $request->kelas_id);
            }
        }

        $siswa = $query->orderBy('nama')->get();
        return view('siswa.index', compact('siswa', 'kelas', 'tahunAktif', 'daftarTahun', 'selectedTahun')); 
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama'     => 'required|string|max:255',
            'jk'       => 'required|in:Laki-Laki,Perempuan',
            'tempat'   => 'required|string',
            'ttl'      => 'required|date',
            'alamat'   => 'required|string',
            'nis'      => 'required|numeric|unique:siswas,nis',
            'nisn'     => 'required|numeric|digits:10|unique:siswas,nisn',
            'hp'       => 'nullable|numeric|digits_between:10,13',
            'kelas_id' => 'nullable|exists:kelases,id',
        ]);

        $validatedData['uuid'] = (string) Str::uuid();
        $validatedData['status'] = 'Aktif';
        Siswa::create($validatedData);

        return redirect()->route('siswa.index')->with('notif', '✅ Data Siswa berhasil ditambah.');
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::with('tahun:id,nama,semester')->orderBy('kelas')->get();
        return view('siswa.edit', ['edit' => $siswa, 'kelas' => $kelas]);
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validatedData = $request->validate([
            'nama'     => 'required|string|max:255',
            'jk'       => 'required|in:Laki-Laki,Perempuan',
            'tempat'   => 'required|string',
            'ttl'      => 'required|date',
            'alamat'   => 'required|string',
            'nis'      => 'required|numeric|unique:siswas,nis,' . $siswa->id,
            'nisn'     => 'required|numeric|digits:10|unique:siswas,nisn,' . $siswa->id,
            'hp'       => 'nullable|numeric|digits_between:10,13',
            'kelas_id' => 'nullable|exists:kelases,id', 
            'status'   => 'nullable|string|in:Aktif,Lulus,Mutasi,Berhenti'
        ]);

        if (!$request->filled('status')) {
            $validatedData['status'] = $siswa->status;
        }

        $siswa->update($validatedData);
        return redirect()->route('siswa.index')->with('notif', '✅ Data Siswa berhasil diubah.');
    }

    /**
     * =============================================================
     * 👨‍🏫 FITUR WALI KELAS (SISWA SAYA)
     * =============================================================
     */

    /**
     * Rute awal 'siswa-saya' - Langsung Redirect ke Tahun Aktif
     */
    public function tahun()
    {
        $tahunAktif = Tahun::where('status', 'Aktif')->first();
        
        if (!$tahunAktif) {
            return redirect()->route('home')->with('error', '⚠️ Sistem belum memiliki periode aktif. Hubungi Admin.');
        }

        // Langsung arahkan ke list kelas di tahun aktif tanpa pilih manual
        return redirect()->route('siswa_saya.list', $tahunAktif->id);
    }

    /**
     * Daftar kelas yang diampu Wali Kelas pada tahun tertentu
     */
    public function siswa_saya($tahunId)
    {
        $pegawaiId = Auth::user()->pegawai_id;
        if (!$pegawaiId) {
            return redirect()->route('home')->with('error', 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $tahunPilihan = Tahun::findOrFail($tahunId);
        $tahunAktif = Tahun::where('status', 'Aktif')->first();

        $daftar_kelas = Kelas::where('pegawai_id', $pegawaiId)
            ->where('tahun_id', $tahunId)
            ->withCount(['siswas' => function($q) {
                $q->where('status', 'Aktif');
            }])
            ->get();

        return view('siswa_saya.index', compact('daftar_kelas', 'tahunPilihan', 'tahunAktif'));
    }

    /**
     * Detail siswa dalam satu kelas wali
     */
    public function siswa_saya_detail(Kelas $kelas)
    {
        if (Auth::user()->role !== 'Dev' && Auth::user()->pegawai_id !== $kelas->pegawai_id) {
            abort(403, 'Anda bukan wali kelas dari rombel ini.');
        }

        $siswa = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'Aktif')
            ->orderBy('nama')
            ->get();

        return view('siswa_saya.detail', compact('kelas', 'siswa'));
    }

    public function siswa_saya_nilai(Siswa $siswa)
    {
        if ($siswa->kelas?->pegawai_id != Auth::user()->pegawai_id && Auth::user()->role !== 'Dev') {
            abort(403, 'Anda tidak memiliki akses ke data nilai siswa ini.');
        }

        $tahunTerkait = $siswa->kelas?->tahun ?? Tahun::where('status', 'Aktif')->first();
        if (!$tahunTerkait) return back()->with('error', 'Periode akademik tidak ditemukan.');

        // Ambil nilai hanya untuk tahun/semester yang dipilih
        $nilai_per_mapel = $siswa->nilai()
            ->where('tahun_id', $tahunTerkait->id)
            ->with('mapel')
            ->get()
            ->groupBy('mapel.nama');

        $mapels = Mapel::orderBy('nama')->get();
        $kelas = $siswa->kelas;

        return view('siswa_saya.nilai-detail', compact('siswa', 'nilai_per_mapel', 'kelas', 'mapels', 'tahunTerkait'));
    }

    public function rekap(Kelas $kelas)
    {
        $tahunAktif = $kelas->tahun;
        
        $daftar_siswa = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'Aktif')
            ->with(['nilai' => function($q) use ($tahunAktif) {
                $q->where('tahun_id', $tahunAktif->id);
            }])
            ->orderBy('nama')
            ->get();

        $mapelIds = Jadwal::where('kelas_id', $kelas->id)
            ->where('tahun_id', $tahunAktif->id)
            ->pluck('mapel_id')
            ->unique();
            
        $mapels = Mapel::whereIn('id', $mapelIds)->orderBy('nama')->get();

        return view('siswa_saya.rekap', compact('kelas', 'daftar_siswa', 'mapels', 'tahunAktif'));
    }

    /**
     * ======================================================================
     * FITUR IMPORT & EXPORT
     * ======================================================================
     */

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        try {
            Excel::import(new SiswaImport, $request->file('file'));
            return redirect()->route('siswa.index')->with('notif', '✅ Data siswa berhasil diimpor.');
        } catch (\Exception $e) {
            Log::error("Error import siswa: ". $e->getMessage()); 
            return redirect()->route('siswa.index')->with('error', '❌ Terjadi kesalahan saat impor data.'); 
        }
    }

    public function exportExcel()
    {
        return Excel::download(new SiswaExport(true), 'daftar-siswa-aktif.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new SiswaTemplateExport, 'template_siswa.xlsx');
    }

    /**
     * ======================================================================
     * REGISTRASI KELUAR & AKUN
     * ======================================================================
     */

    public function keluar(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Lulus,Mutasi,Berhenti',
            'tanggal_keluar' => 'required|date|before_or_equal:today',
            'keterangan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $siswa = Siswa::with('user')->findOrFail($id);
            
            $siswa->status = $validated['status'];
            $siswa->kelas_id = null;
            if ($validated['status'] == 'Lulus') {
                $siswa->tahun_lulus = date('Y', strtotime($validated['tanggal_keluar']));
            }
            $siswa->save();

            $ketKeluar = KeteranganKeluarSiswa::firstOrNew(['siswa_id' => $siswa->id]);
            $ketKeluar->alasan_keluar = $validated['status'];
            $ketKeluar->tanggal_keluar = $validated['tanggal_keluar'];
            $ketKeluar->keterangan = $validated['keterangan'] ?? '-'; 
            $ketKeluar->save();

            if ($siswa->user) {
                $siswa->user->update(['status' => 'Tidak Aktif']);
            }
            
            DB::commit();
            return redirect()->route('siswa.index')->with('notif', '✅ Registrasi keluar ' . $siswa->nama . ' berhasil.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function updateSkl(Request $request, $id)
    {
        $request->validate(['link_skl' => 'required|url']);
        try {
            $siswa = Siswa::findOrFail($id);
            $siswa->link_skl = $request->link_skl;
            $siswa->save();
            return redirect()->back()->with('notif', '✅ Link SKL berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal: ' . $e->getMessage());
        }
    }

    public function generateAkun()
    {
        DB::beginTransaction();
        try {
            $siswaTanpaAkun = Siswa::where('status', 'Aktif')->doesntHave('user')->get();
            if ($siswaTanpaAkun->isEmpty()) {
                DB::commit();
                return redirect()->back()->with('info', '✅ Semua siswa sudah memiliki akun.');
            }

            foreach ($siswaTanpaAkun as $siswa) {
                $username = $siswa->nis ?: strtolower(str_replace(' ', '', $siswa->nama)) . rand(10, 99);
                $passwordPlain = $siswa->ttl ? date('dmY', strtotime($siswa->ttl)) : '123456';
                
                User::create([
                    'name' => $siswa->nama,
                    'username' => $username,
                    'password' => Hash::make($passwordPlain),
                    'plain_password' => $passwordPlain,
                    'role' => 'Siswa',
                    'siswa_id' => $siswa->id,
                    'status' => 'Aktif'
                ]);
            }
            DB::commit();
            return redirect()->back()->with('notif', '✅ Akun berhasil dibuat secara masal.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal generate akun.');
        }
    }

    public function destroy(Siswa $siswa)
    {
        try {
            if ($siswa->nilai()->exists()) {
                 return back()->with('error', '❌ Gagal! Siswa sudah memiliki data nilai.');
            }

            DB::beginTransaction();
            $siswa->keteranganKeluar()->delete();
            if($siswa->user) $siswa->user()->delete();
            $siswa->delete(); 

            DB::commit();
            return back()->with('notif', '🗑️ Data Siswa berhasil dihapus.');
        } catch (\Exception $e) {
             DB::rollBack();
             return back()->with('error', 'Gagal hapus.');
        }
    }

    public function destroyAll()
    {
        DB::beginTransaction();
        try {
            $siswaNonAktif = Siswa::where('status', '!=', 'Aktif')->doesntHave('nilai')->delete();
            DB::commit();
            return back()->with('notif', "🗑️ Data siswa non-aktif tanpa nilai berhasil dibersihkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membersihkan data.');
        }
    }

    public function getSiswaByKelas(Request $request, Kelas $kelas)
    {
        $tahunId = $request->query('tahun_id');
        if ($tahunId) {
            $tahunAjaran = Tahun::find($tahunId);
            // Ambil data hanya untuk tahun/semester yang dipilih
            $currentSiswaIds = $kelas->siswas()->pluck('id');
            $nilaiSiswaIds = Nilai::where('kelas_id', $kelas->id)->where('tahun_id', $tahunAjaran->id)->pluck('siswa_id');
            $jadwalSiswaIds = Jadwal_siswa::where('kelas_id', $kelas->id)->where('tahun_id', $tahunAjaran->id)->pluck('siswa_id');

            $allIds = $currentSiswaIds->merge($nilaiSiswaIds)->merge($jadwalSiswaIds)->unique();
            $siswas = Siswa::whereIn('id', $allIds)->orderBy('nama')->get(['id', 'nisn', 'nama']);
        } else {
            $siswas = $kelas->siswas()->where('status', 'Aktif')->orderBy('nama')->get(['id', 'nisn', 'nama']);
        }
        return response()->json($siswas);
    }
}