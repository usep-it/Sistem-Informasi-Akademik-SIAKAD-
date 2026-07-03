<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jadwal_siswa;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        // Tangkap parameter kategori, defaultnya adalah 'guru'
        $kategori = $request->query('kategori', 'guru');

        // Filter data berdasarkan kategori (Kepala Sekolah sekarang masuk ke Tendik)
        if ($kategori === 'tendik') {
            $pegawai = Pegawai::whereNotIn('jabatan', ['Guru Kelas', 'Guru Mapel'])->latest()->get();
            $title = 'Data Tenaga Kependidikan & Kepala Sekolah';
        } else {
            $pegawai = Pegawai::whereIn('jabatan', ['Guru Kelas', 'Guru Mapel'])->latest()->get();
            $title = 'Data Pendidik (Guru)';
        }

        return view('pegawai.index', compact('pegawai', 'kategori', 'title'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama'               => 'required|string|max:100',
            'jabatan'            => 'required|string|max:50',
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'jk'                 => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir'       => 'required|string',
            'ttl'                => 'required|date',
            'status_kepegawaian' => 'required|string',
            'alamat'             => 'required|string',
            'email'              => 'nullable|email|max:100',
            'nip'                => 'nullable|string|max:30',
            'nuptk'              => 'nullable|string|max:20',
            'nik'                => 'nullable|numeric|digits:16',
        ]);

        // Validasi unik manual: abaikan jika nilainya kosong atau "-"
        if (!empty($request->nip) && $request->nip != '-') {
            if (Pegawai::where('nip', $request->nip)->exists()) {
                return back()->withErrors(['nip' => 'NIP sudah digunakan.'])->withInput();
            }
        }

        if (!empty($request->nuptk) && $request->nuptk != '-') {
            if (Pegawai::where('nuptk', $request->nuptk)->exists()) {
                return back()->withErrors(['nuptk' => 'NUPTK sudah digunakan.'])->withInput();
            }
        }

        if (!empty($request->nik)) {
            if (Pegawai::where('nik', $request->nik)->exists()) {
                return back()->withErrors(['nik' => 'NIK sudah digunakan.'])->withInput();
            }
        }

        if (!empty($request->email)) {
            if (Pegawai::where('email', $request->email)->exists()) {
                return back()->withErrors(['email' => 'Email sudah digunakan.'])->withInput();
            }
        }

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('foto_pegawai'), $nama_file);
            $validatedData['foto'] = $nama_file;
        }

        $validatedData['uuid'] = (string) Str::uuid();
        Pegawai::create($validatedData);
        
        // Pendidik hanya Guru, sisanya (termasuk Kepsek) dilempar ke tab Tendik
        $kategori = in_array($validatedData['jabatan'], ['Guru Kelas', 'Guru Mapel']) ? 'guru' : 'tendik';
        return redirect()->route('pegawai.index', ['kategori' => $kategori])->with('notif', '✅ Data GTK berhasil ditambahkan!');
    }

    // PERBAIKAN: Fungsi Edit dikembalikan seperti semula agar tidak Error Undefined Variable
    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', ['edit' => $pegawai]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validatedData = $request->validate([
            'nama'               => 'required|string|max:100',
            'jabatan'            => 'required|string|max:50',
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'jk'                 => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir'       => 'required|string',
            'ttl'                => 'required|date',
            'status_kepegawaian' => 'required|string',
            'alamat'             => 'required|string',
            'nip'                => 'nullable|string|max:30',
            'nuptk'              => 'nullable|string|max:20',
            'nik'                => 'nullable|numeric|digits:16',
            'email'              => 'nullable|email|max:100',
        ]);

        // Validasi unik manual saat update (abaikan nilai "-")
        if (!empty($request->nip) && $request->nip != '-') {
            if (Pegawai::where('nip', $request->nip)->where('id', '!=', $pegawai->id)->exists()) {
                return back()->withErrors(['nip' => 'NIP sudah digunakan.'])->withInput();
            }
        }

        if (!empty($request->nuptk) && $request->nuptk != '-') {
            if (Pegawai::where('nuptk', $request->nuptk)->where('id', '!=', $pegawai->id)->exists()) {
                return back()->withErrors(['nuptk' => 'NUPTK sudah digunakan.'])->withInput();
            }
        }

        if (!empty($request->nik)) {
            if (Pegawai::where('nik', $request->nik)->where('id', '!=', $pegawai->id)->exists()) {
                return back()->withErrors(['nik' => 'NIK sudah digunakan.'])->withInput();
            }
        }

        if (!empty($request->email)) {
            if (Pegawai::where('email', $request->email)->where('id', '!=', $pegawai->id)->exists()) {
                return back()->withErrors(['email' => 'Email sudah digunakan.'])->withInput();
            }
        }

        // Ganti foto jika ada file baru
        if ($request->hasFile('foto')) {
            if ($pegawai->foto && File::exists(public_path('foto_pegawai/' . $pegawai->foto))) {
                File::delete(public_path('foto_pegawai/' . $pegawai->foto));
            }

            $file = $request->file('foto');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('foto_pegawai'), $nama_file);
            $validatedData['foto'] = $nama_file;
        }

        $pegawai->update($validatedData);

        // Pendidik hanya Guru, sisanya (termasuk Kepsek) dilempar ke tab Tendik
        $kategori = in_array($validatedData['jabatan'], ['Guru Kelas', 'Guru Mapel']) ? 'guru' : 'tendik';
        return redirect()->route('pegawai.index', ['kategori' => $kategori])->with('notif', '✅ Data GTK berhasil diperbarui!');
    }

    public function destroy(Pegawai $pegawai)
    {
        // Cek relasi sebelum hapus
        $masihWali = Kelas::where('pegawai_id', $pegawai->id)->exists();
        $masihTerpakaiDiJadwal = Jadwal_siswa::where('pegawai_id', $pegawai->id)->exists();

        // Tentukan dia guru atau tendik untuk arah redirect
        $kategori = in_array($pegawai->jabatan, ['Guru Kelas', 'Guru Mapel']) ? 'guru' : 'tendik';

        if ($masihWali || $masihTerpakaiDiJadwal) {
            return redirect()->route('pegawai.index', ['kategori' => $kategori])
                ->with('error', '⚠️ Pegawai ini tidak bisa dihapus karena masih terhubung dengan kelas atau jadwal siswa.');
        }

        // Hapus foto jika ada
        if ($pegawai->foto && File::exists(public_path('foto_pegawai/' . $pegawai->foto))) {
            File::delete(public_path('foto_pegawai/' . $pegawai->foto));
        }

        $pegawai->delete();
        return redirect()->route('pegawai.index', ['kategori' => $kategori])->with('notif', '🗑️ Data GTK berhasil dihapus!');
    }

    public function rekapGuru()
    {
        $user = Auth::user();
        $jabatan = $user->pegawai?->jabatan ?? null;

        // Akses Dev & Kepsek
        if (!($user->role == 'Dev' || ($user->role == 'Guru' && $jabatan == 'Kepala Sekolah'))) {
            abort(403, 'Akses Ditolak');
        }

        // 1. Data untuk Tabel (per nama)
        $pegawai = Pegawai::orderBy('nama', 'asc')->get();
        $total = Pegawai::count();

        // 2. Data untuk Chart (Agregat)
        // Mengambil data untuk chart Status Kepegawaian
        $rekapStatus = Pegawai::select('status_kepegawaian', DB::raw('COUNT(*) as jumlah'))
                                ->groupBy('status_kepegawaian')
                                ->pluck('jumlah', 'status_kepegawaian'); // 'pluck' agar mudah dipakai di JS

        // Mengambil data untuk chart Jabatan
        $rekapJabatan = Pegawai::select('jabatan', DB::raw('COUNT(*) as jumlah'))
                                ->groupBy('jabatan')
                                ->pluck('jumlah', 'jabatan');

        // Mengirim semua data ke view
        return view('kepala_sekolah.rekap_guru', compact(
            'pegawai',      // Data tabel
            'total',        // Data tabel
            'rekapStatus',  // Data chart 1
            'rekapJabatan'  // Data chart 2
        ));
    }

    /**
     * Tampilkan halaman rekap siswa untuk Kepala Sekolah
     */
   public function rekapSiswa()
   {
    $user = Auth::user();
    $jabatan = $user->pegawai?->jabatan ?? null;

    if (!($user->role == 'Dev' || ($user->role == 'Guru' && $jabatan == 'Kepala Sekolah'))) {
        abort(403, 'Akses Ditolak');
    }

    $tahunAktif = Tahun::where('status', 'Aktif')->first();

    // ============================
    // 1. Total seluruh siswa di database (untuk CHART)
    // ============================
    $totalLaki_db       = Siswa::where('status', 'Aktif')->where('jk', 'L')->count();
    $totalPerempuan_db  = Siswa::where('status', 'Aktif')->where('jk', 'P')->count();
    $totalSiswa_db      = $totalLaki_db + $totalPerempuan_db;

    $chartTotalGender = [
        'Laki-laki' => $totalLaki_db,
        'Perempuan' => $totalPerempuan_db
    ];

    // ============================
    // 2. Rekap data rombel (khusus siswa yang SUDAH MASUK KELAS)
    // ============================
    $kelasAktif = Kelas::with('pegawai')
        ->whereHas('tahun', fn($q) => $q->where('status', 'Aktif'))
        ->orderBy('kelas')
        ->get();

    $rekapData = [];
    $totalLaki = 0;
    $totalPerempuan = 0;
    $totalSiswa = 0;

    $chartPerRombel = [
        'labels' => [],
        'dataLaki' => [],
        'dataPerempuan' => [],
    ];

    foreach ($kelasAktif as $k) {

        $laki = Siswa::where('kelas_id', $k->id)
                    ->where('status', 'Aktif')
                    ->where('jk', 'L')
                    ->count();

        $perempuan = Siswa::where('kelas_id', $k->id)
                        ->where('status', 'Aktif')
                        ->where('jk', 'P')
                        ->count();

        $totalKelas = $laki + $perempuan;

        // Total utk tabel rombel
        $totalLaki      += $laki;
        $totalPerempuan += $perempuan;
        $totalSiswa     += $totalKelas;

        // Data tabel
        $rekapData[] = [
            'kelas'       => $k->kelas,
            'wali_kelas'  => $k->pegawai->nama ?? '-',
            'laki_laki'   => $laki,
            'perempuan'   => $perempuan,
            'total_kelas' => $totalKelas
        ];

        // Data chart bar
        $chartPerRombel['labels'][]        = "Kelas $k->kelas";
        $chartPerRombel['dataLaki'][]      = $laki;
        $chartPerRombel['dataPerempuan'][] = $perempuan;
    }

    return view('kepala_sekolah.rekap_siswa', compact(
        'tahunAktif',
        'rekapData',

        // Chart per kelas
        'chartPerRombel',

        // Chart seluruh database
        'chartTotalGender',
        'totalLaki_db',
        'totalPerempuan_db',
        'totalSiswa_db',

        // Total tabel rombel
        'totalLaki',
        'totalPerempuan',
        'totalSiswa'
    ));
   }

    /**
     * Tampilkan halaman tugas untuk Tenaga Administrasi
     */
    public function tugasAdmin()
    {
        // Logika Keamanan
        $user = Auth::user();
        $jabatan = $user->pegawai?->jabatan ?? null;
        if (!($user->role == 'Dev' || ($user->role == 'Guru' && $jabatan == 'Tenaga Administrasi'))) {
            abort(403, 'Akses Ditolak');
        }

        return view('tenaga_administrasi.tugas');
    }
}