<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\Jadwal;
use App\Models\KeteranganKeluarSiswa;
use App\Models\Jadwal_siswa;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    /** ===============================
     * INDEX & CRUD DATA KELAS
     * =============================== */
    public function index()
    {
        // PERBAIKAN 1: Jabatan disesuaikan dengan inputan di GTK ('Guru Kelas' bukan 'Wali Kelas')
        $wali = Pegawai::whereIn('jabatan', ['Guru Kelas', 'Guru Mapel', 'Kepala Sekolah'])
                        ->orderBy('nama')
                        ->get();

        $tahunAktif = Tahun::where('status', 'Aktif')->first();
        
        // 'tahun' untuk modal sekarang mengambil SEMUA tahun ajaran
        $tahun = Tahun::orderBy('nama', 'desc')->get();
        
        // Query untuk tabel utama, hanya menampilkan kelas dari tahun ajaran yang aktif.
        $klsQuery = Kelas::query();
        if ($tahunAktif) {
            $klsQuery->where('tahun_id', $tahunAktif->id);
        }
        
        $kls = $klsQuery->with(['pegawai', 'tahun'])->withCount('siswas')->orderBy('kelas', 'asc')->get();
        
        return view('kelas.index', compact('kls', 'wali', 'tahun', 'tahunAktif'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'kelas' => [
                'required',
                Rule::unique('kelases')->where(function ($query) use ($request) {
                    return $query->where('nama', $request->nama)
                                 ->where('tahun_id', $request->tahun_id);
                }),
            ],
            'nama' => 'required',
            'pegawai_id' => 'required|exists:pegawais,id',
            'tahun_id' => 'required|exists:tahuns,id',
        ], [
            'kelas.unique' => 'Kombinasi Kelas, Fase, dan Tahun Ajaran ini sudah ada.'
        ]);

        Kelas::create($request->all());
        return redirect()->route('kelas.index')->with('notif', '✅ Data Kelas berhasil ditambah.');
    }

    public function edit(Kelas $kelas)
    {
        // PERBAIKAN 1: Jabatan disesuaikan
        $wali = Pegawai::whereIn('jabatan', ['Guru Kelas', 'Guru Mapel', 'Kepala Sekolah'])->orderBy('nama')->get();
        $tahun = Tahun::orderBy('nama', 'desc')->get();
        
        return view('kelas.edit', ['edit' => $kelas, 'wali' => $wali, 'tahun' => $tahun]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
             'kelas' => [
                'required',
                Rule::unique('kelases')->ignore($kelas->id)->where(function ($query) use ($request) {
                    return $query->where('nama', $request->nama)
                                 ->where('tahun_id', $request->tahun_id);
                }),
            ],
            'nama' => 'required',
            'pegawai_id' => 'required|exists:pegawais,id',
            'tahun_id' => 'required|exists:tahuns,id',
        ], [
            'kelas.unique' => 'Kombinasi Kelas, Fase, dan Tahun Ajaran ini sudah ada.'
        ]);

        $kelas->update($request->all());
        return redirect()->route('kelas.index')->with('notif', '✅ Data Kelas berhasil diperbarui.');
    }
    
    public function destroy(Kelas $kelas)
    {
        if ($kelas->siswas()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal! Kelas ini tidak dapat dihapus karena masih memiliki anggota siswa.');
        }

        $kelas->delete();
        return redirect()->route('kelas.index')->with('notif', '🗑️ Data Kelas berhasil dihapus.');
    }

    /** ===============================
     * KELOLA ANGGOTA KELAS
     * =============================== */
    public function manage(Kelas $kelas)
    {
        // PERBAIKAN 2: Hanya mengambil Siswa Aktif yang BENAR-BENAR belum punya kelas
        $calon_anggota = Siswa::whereNull('kelas_id')
                            ->where('status', 'Aktif')
                            ->orderBy('nama')
                            ->get();

        $anggota_kelas = $kelas->siswas()->orderBy('nama')->get();

        return view('kelas.manage', compact('kelas', 'calon_anggota', 'anggota_kelas'));
    }

    public function addMember(Request $request, Kelas $kelas)
    {
        $request->validate(['siswa_id' => 'required|exists:siswas,id']);
        
        $siswa = Siswa::find($request->siswa_id);
        $siswa->kelas_id = $kelas->id;
        $siswa->status = 'Aktif';
        $siswa->save();

        return redirect()->back()->with('notif', '✅ Siswa berhasil ditambahkan ke kelas ini.');
    }

    public function removeMember(Siswa $siswa)
    {
        $siswa->kelas_id = null;
        // Opsional: Biarkan statusnya tetap 'Aktif', hanya kelasnya saja yang kosong (belum masuk kelas)
        // $siswa->status = 'Tidak Aktif'; -> ini kita hapus agar siswa yg dikeluarkan tidak otomatis mati akunnya
        $siswa->save();

        return redirect()->back()->with('notif', '✅ Anggota berhasil dikeluarkan dari kelas.');
    }

    public function removeAllMembers(Kelas $kelas)
    {
        foreach ($kelas->siswas as $siswa) {
            $siswa->kelas_id = null;
            $siswa->save();
        }

        return redirect()->back()->with('notif', '🧹 Semua siswa telah dikeluarkan dari kelas.');
    }

    /**
     * Fungsi ini sekarang menangani "Lanjut Semester" dan "Naik Tahun Ajaran" secara otomatis.
     */
    public function gantiSemester()
    {
        return DB::transaction(function () {
            $tahunAktif = Tahun::where('status', 'Aktif')->first();
            if (!$tahunAktif) return back()->with('error', '❌ Tidak ada tahun pelajaran aktif.');

            $isGanjil = strtolower($tahunAktif->semester) === 'ganjil';
            $totalNaik = 0;

            // 1. Cari/Tentukan Tahun Baru
            if ($isGanjil) {
                // Ganjil ke Genap (Tahun yang sama)
                $tahunBaru = Tahun::where('nama', $tahunAktif->nama)->where('semester', 'Genap')->first();
            } else {
                // Genap ke Ganjil (Tahun berikutnya)
                $tahunAwal = (int) substr($tahunAktif->nama, 0, 4);
                $namaTahunBerikutnya = ($tahunAwal + 1) . '/' . ($tahunAwal + 2);
                $tahunBaru = Tahun::where('nama', $namaTahunBerikutnya)->where('semester', 'Ganjil')->first();
            }

            if (!$tahunBaru) return back()->with('error', '⚠️ Periode berikutnya belum dibuat di menu Tahun Pelajaran.');

            // 2. Validasi Khusus Akhir Tahun (Luluskan kelas 6)
            if (!$isGanjil) {
                $kelas6Ids = Kelas::where('kelas', 6)->where('tahun_id', $tahunAktif->id)->pluck('id');
                if (Siswa::whereIn('kelas_id', $kelas6Ids)->where('status', 'Aktif')->count() > 0) {
                    return back()->with('error', '❌ Harap luluskan semua siswa kelas 6 terlebih dahulu sebelum naik tahun ajaran.');
                }
            }

            // 3. PROSES DUPLIKASI KELAS (Menciptakan "Rumah Baru" untuk semester/tahun baru)
            $kelasLama = Kelas::where('tahun_id', $tahunAktif->id)->get();
            $kelasBaruMap = [];

            foreach ($kelasLama as $kl) {
                // Tetap gunakan tingkat kelas yang sama untuk wali kelas
                $tingkatBaru = $kl->kelas;

                $fase = match (true) {
                    in_array($tingkatBaru, [1, 2]) => 'A',
                    in_array($tingkatBaru, [3, 4]) => 'B',
                    in_array($tingkatBaru, [5, 6]) => 'C',
                    default => null,
                };

                $kelasBaru = Kelas::create([
                    'kelas' => $tingkatBaru,
                    'nama' => $fase,
                    'pegawai_id' => $kl->pegawai_id, // Wali kelas tetap di kelas yang sama
                    'tahun_id' => $tahunBaru->id,
                ]);

                $kelasBaruMap[$tingkatBaru] = $kelasBaru;
            }

            // 4. PINDAHKAN SISWA
            foreach ($kelasLama as $kl) {
                if ($isGanjil) {
                    $kelasTujuan = $kelasBaruMap[$kl->kelas] ?? null;
                } else {
                    $kelasTujuan = $kelasBaruMap[$kl->kelas + 1] ?? null;
                }

                if (!$kelasTujuan) continue;

                $naik = Siswa::where('kelas_id', $kl->id)
                             ->where('status', 'Aktif')
                             ->update(['kelas_id' => $kelasTujuan->id]);
                $totalNaik += $naik;
            }

            // 5. Update Status Tahun
            $tahunAktif->update(['status' => 'Tidak Aktif']);
            $tahunBaru->update(['status' => 'Aktif']);

            return back()->with('notif', "✅ Berhasil! Sistem kini di <b>{$tahunBaru->nama} ({$tahunBaru->semester})</b>.<br>Siswa berpindah: {$totalNaik}");
        });
    }

    public function luluskanSemua(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_kelulusan' => 'required|date|before_or_equal:today',
        ]);

        $kelas = \App\Models\Kelas::findOrFail($id);
        $siswas = $kelas->siswas()->where('status', 'Aktif')->get();

        if ($siswas->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada siswa aktif di kelas ini yang bisa diluluskan.');
        }

        DB::beginTransaction();
        try {
            $tahunAktif = \App\Models\Tahun::where('status', 'Aktif')->first();

            foreach ($siswas as $siswa) {
                $siswa->update([
                    'status' => 'Lulus',
                    'kelas_id' => null,
                    'tahun_lulus' => date('Y', strtotime($validated['tanggal_kelulusan'])),
                ]);

                KeteranganKeluarSiswa::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'alasan_keluar' => 'Lulus'
                    ],
                    [
                        'tanggal_keluar' => $validated['tanggal_kelulusan'],
                        'keterangan' => 'Siswa dinyatakan lulus dari kelas ' . $kelas->kelas,
                    ]
                );

                if ($tahunAktif) {
                    $jadwalSiswaIds = Jadwal_siswa::where('siswa_id', $siswa->id)
                        ->where('tahun_id', $tahunAktif->id)
                        ->pluck('id');

                    if ($jadwalSiswaIds->isNotEmpty()) {
                        $nilaiCount = Nilai::whereIn('jadwal_siswa_id', $jadwalSiswaIds)->count();

                        // Jika siswa sudah memiliki nilai, simpan histori nilai dan jadwal siswa.
                        // Hapus jadwal siswa hanya jika tidak ada nilai terkait.
                        if ($nilaiCount === 0) {
                            Jadwal_siswa::whereIn('id', $jadwalSiswaIds)->delete();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('notif', '🎓 ' . $siswas->count() . ' siswa di kelas <strong>' . $kelas->kelas . '</strong> berhasil diluluskan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}