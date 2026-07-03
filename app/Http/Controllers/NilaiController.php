<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Jadwal_siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\Hari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    /**
     * ==========================
     * HALAMAN UTAMA NILAI
     * ==========================
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $daftarTahun = Tahun::orderByDesc('id')->get();

        $tahunAktif = $request->input('tahun_id')
            ? Tahun::find($request->input('tahun_id'))
            : Tahun::where('status', 'Aktif')->orderByDesc('id')->first();

        if (!$tahunAktif) {
            return view('nilai.index_kosong')->with('notif_error', 'Belum ada data Tahun Ajaran di sistem.');
        }

        /** ===== UNTUK GURU ===== */
        if ($user->role === 'Guru') {
            $kelasDiampu = Jadwal::with(['kelas'])
                ->where('pegawai_id', $user->pegawai_id)
                ->where('tahun_id', $tahunAktif->id)
                ->get()
                ->unique('kelas_id')
                ->pluck('kelas')
                ->filter();

            return view('nilai.index', [
                'kelasDiampu' => $kelasDiampu,
                'tahun' => $daftarTahun,
                'tahunAktif' => $tahunAktif,
            ]);
        }

        /** ===== UNTUK SISWA ===== */
        if ($user->role === 'Siswa') {
            $nilaisiswa = Nilai::select('nilais.*', 'mapels.nama as nama_mapel')
                ->leftJoin('mapels', 'mapels.id', '=', 'nilais.mapel_id')
                ->where('nilais.siswa_id', $user->siswa_id)
                ->where('nilais.tahun_id', $tahunAktif->id)
                ->get()
                ->groupBy('nama_mapel');

            return view('nilai.index', [
                'nilaisiswa' => $nilaisiswa,
                'tahun' => $daftarTahun,
                'tahunAktif' => $tahunAktif,
            ]);
        }

        return redirect()->route('home')->with('notif_error', 'Role tidak dikenali.');
    }


    /**
     * ==========================
     * HALAMAN NILAI SISWA LOGIN
     * ==========================
     */
    public function siswa(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'Siswa') {
            return redirect()->route('home')->with('notif_error', 'Akses hanya untuk siswa.');
        }

        $daftarTahun = Tahun::orderByDesc('id')->get();

        $tahunAktif = $request->filled('tahun_id')
            ? Tahun::find($request->input('tahun_id'))
            : Tahun::where('status', 'Aktif')->orderByDesc('id')->first();

        if (!$request->filled('tahun_id') && $user->siswa && $user->siswa->status !== 'Aktif') {
            $tahunTerakhir = Nilai::where('siswa_id', $user->siswa_id)
                ->orderByDesc('tahun_id')
                ->value('tahun_id');

            if ($tahunTerakhir) {
                $tahunAktif = Tahun::find($tahunTerakhir);
            }
        }

        if (!$tahunAktif) {
            return view('nilai.saya', [
                'nilaisiswa' => collect(),
                'daftarTahun' => $daftarTahun,
                'tahunAktif' => null,
                'kelasSiswaPadaTahunItu' => null,
                'rankSiswa' => '-',
                'totalSiswa' => 0
            ])->with('notif', 'Belum ada tahun ajaran aktif.');
        }

        $siswa = Siswa::with('kelas')->find($user->siswa_id);
        
        if (!$siswa) {
            return redirect()->route('home')->with('notif_error', 'Data identitas siswa tidak valid.');
        }

        $nilaisiswa = Nilai::select('nilais.*', 'mapels.nama as nama_mapel', 'mapels.id as mapel_id')
            ->leftJoin('mapels', 'mapels.id', '=', 'nilais.mapel_id')
            ->where('nilais.siswa_id', $siswa->id)
            ->where('nilais.tahun_id', $tahunAktif->id)
            ->get()
            ->groupBy('nama_mapel');

        $kelasSiswaPadaTahunItu = null;
        $rekamNilai = Nilai::where('siswa_id', $siswa->id)->where('tahun_id', $tahunAktif->id)->first();
        if ($rekamNilai && $rekamNilai->kelas_id) {
            $kelasSiswaPadaTahunItu = Kelas::find($rekamNilai->kelas_id);
        } else {
            $jadwalSiswa = Jadwal_siswa::where('siswa_id', $siswa->id)->where('tahun_id', $tahunAktif->id)->first();
            if ($jadwalSiswa && $jadwalSiswa->kelas_id) {
                $kelasSiswaPadaTahunItu = Kelas::find($jadwalSiswa->kelas_id);
            }
        }

        if (!$kelasSiswaPadaTahunItu) {
            $kelasSiswaPadaTahunItu = $siswa->kelas;
        }

        $rankSiswa = '-';
        $totalSiswa = 0;

        if ($kelasSiswaPadaTahunItu) {
            $siswaList = Siswa::where('kelas_id', $kelasSiswaPadaTahunItu->id)
                ->orWhereHas('nilai', function ($q) use ($kelasSiswaPadaTahunItu, $tahunAktif) {
                    $q->where('kelas_id', $kelasSiswaPadaTahunItu->id)
                      ->where('tahun_id', $tahunAktif->id);
                })
                ->with(['nilai' => function ($q) use ($tahunAktif) {
                    $q->where('tahun_id', $tahunAktif->id);
                }])
                ->get();

            $averageScores = [];

            foreach ($siswaList as $s) {
                $mapelGroup = $s->nilai->groupBy('mapel_id');
                $total = 0;
                $count = 0;

                foreach ($mapelGroup as $mapel) {
                    $harian = $mapel->where('jenis','HARIAN')->avg('nilai');
                    $pts = $mapel->where('jenis','PTS')->first()?->nilai;
                    $pas = $mapel->where('jenis','PAS')->first()?->nilai;

                    $nilaiAkhir = null;

                    if (!is_null($harian) && (!is_null($pts) || !is_null($pas))) {
                        $ujian = collect([$pts, $pas])->filter(fn($v) => !is_null($v))->avg();
                        $nilaiAkhir = ($harian * 0.6) + ($ujian * 0.4);
                    } elseif (!is_null($harian)) {
                        $nilaiAkhir = $harian;
                    } elseif (!is_null($pts) || !is_null($pas)) {
                        $nilaiAkhir = collect([$pts, $pas])->filter(fn($v) => !is_null($v))->avg();
                    }

                    if (!is_null($nilaiAkhir)) {
                        $total += $nilaiAkhir;
                        $count++;
                    }
                }

                $averageScores[$s->id] = $count > 0 ? $total / $count : 0;
            }

            $ranked = collect($averageScores)->filter(function($score) {
                return $score > 0;
            })->sortDesc();
            
            $ranks = $ranked->keys()->flip();

            $rankSiswa = isset($ranks[$siswa->id]) ? $ranks[$siswa->id] + 1 : '-';
            $totalSiswa = $siswaList->count();
        }

        return view('nilai.saya', compact('daftarTahun','tahunAktif','nilaisiswa','kelasSiswaPadaTahunItu','rankSiswa','totalSiswa'));
    }


    /**
     * ==========================
     * DAFTAR SISWA PER KELAS (GURU)
     * ==========================
     */
    public function detail($kelas_id)
    {
        $kelas = Kelas::with('tahun')->findOrFail($kelas_id);
        $tahunAktif = Tahun::where('status', 'Aktif')->first();
        
        $siswaList = Siswa::where('kelas_id', $kelas_id)
            ->where('status', 'Aktif')
            ->orderBy('nama')
            ->get();

        $mapelDiampu = Jadwal::where('pegawai_id', Auth::user()->pegawai_id)
            ->where('kelas_id', $kelas_id)
            ->where('tahun_id', $tahunAktif->id)
            ->with('mapel')
            ->get()
            ->unique('mapel_id')
            ->pluck('mapel');

        return view('nilai.all-siswa', compact('kelas', 'siswaList', 'mapelDiampu', 'tahunAktif'));
    }

    /**
     * ==========================
     * HALAMAN INPUT NILAI PER SISWA
     * ==========================
     */
    public function nilaiLangsung(Request $request, $siswa_id)
    {
        $siswa = Siswa::with(['kelas.tahun'])->findOrFail($siswa_id);
        $tahunAktif = Tahun::where('status', 'Aktif')->first();
        $guruId = Auth::user()->pegawai_id;

        $mapelDiampu = Jadwal::where('pegawai_id', $guruId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('tahun_id', $tahunAktif?->id)
            ->with('mapel')
            ->get()
            ->unique('mapel_id')
            ->pluck('mapel');

        $nilaiList = Nilai::where('siswa_id', $siswa->id)
            ->where('tahun_id', $tahunAktif?->id)
            ->whereIn('mapel_id', $mapelDiampu->pluck('id'))
            ->with('mapel')
            ->get();

        $data = (object) [
            'siswa' => $siswa,
            'kelas' => $siswa->kelas,
            'mapelDiampu' => $mapelDiampu,
            'nilaiList' => $nilaiList,
        ];

        return view('nilai.siswa', compact('data', 'tahunAktif'));
    }


    /**
     * ==========================
     * SIMPAN NILAI BARU
     * ==========================
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'mapel_id' => 'required|integer',
            'jenis'    => 'required|in:HARIAN,PTS,PAS',
            'nilai'    => 'required|numeric|min:0|max:100',
            'siswa_id' => 'required|integer',
            'kelas_id' => 'required|integer',
        ]);

        $tahunAktif = Tahun::where('status', 'Aktif')->first();
        if (!$tahunAktif) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $cek = Nilai::where('siswa_id', $request->siswa_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('tahun_id', $tahunAktif->id)
            ->where('jenis', $request->jenis)
            ->exists();

        if ($cek) {
            return back()->with('error', '❌ Kategori nilai ini sudah diinput untuk mata pelajaran tersebut!');
        }

        $jadwal = Jadwal::where('pegawai_id', auth()->user()->pegawai_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('tahun_id', $tahunAktif->id)
            ->first();

        $jadwal_id = $jadwal ? $jadwal->id : null;

        $jadwalSiswa = Jadwal_siswa::firstOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'mapel_id' => $request->mapel_id,
                'tahun_id' => $tahunAktif->id,
            ],
            [
                'jadwal_id' => $jadwal_id,
                'pegawai_id' => auth()->user()->pegawai_id,
                'kelas_id' => $request->kelas_id,
            ]
        );

        Nilai::create([
            'uuid' => Str::uuid(),
            'pegawai_id' => auth()->user()->pegawai_id,
            'jadwal_id' => $jadwal_id,
            'jadwal_siswa_id' => $jadwalSiswa->id,
            'siswa_id' => $request->siswa_id,
            'tahun_id' => $tahunAktif->id,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'jenis' => $request->jenis,
            'nilai' => $request->nilai,
        ]);

        return redirect()->back()->with('notif', '✅ Nilai berhasil ditambahkan.');
    }

    /**
     * ==========================
     * EDIT & UPDATE NILAI
     * ==========================
     */
    public function edit($uuid)
    {
        $edit = Nilai::where('uuid', $uuid)->firstOrFail();
        return view('nilai.edit', compact('edit'));
    }

    public function update(Request $request, $uuid)
    {
        $this->validate($request, [
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $nilai = Nilai::where('uuid', $uuid)->firstOrFail();
        $nilai->update(['nilai' => $request->nilai]);

        return redirect()->route('nilai.langsung', ['siswa_id' => $nilai->siswa_id])->with('notif', '✅ Nilai berhasil diperbarui.');
    }

    /**
     * ==========================
     * HAPUS NILAI
     * ==========================
     */
    public function destroy($uuid)
    {
        try {
            $nilai = Nilai::where('uuid', $uuid)->firstOrFail();
            $nilai->delete();

            return redirect()->back()->with('notif', '✅ Nilai berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal menghapus nilai: ' . $e->getMessage());
        }
    }

    /**
     * ==========================
     * PUSAT MIGRASI DATA
     * ==========================
     */
    public function migrationPage()
    {
        $tahun = Tahun::orderBy('nama', 'desc')->get();
        $kelas = Kelas::with('tahun')->orderBy('kelas')->get();
        return view('admin.migrasi', compact('tahun', 'kelas'));
    }

    /**
     * IMPORT JADWAL (Smarter Search & Deep Mapping)
     */
    public function importJadwalGanjil(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'tahun_id' => 'required|exists:tahuns,id'
        ]);
        
        $data = Excel::toArray([], $request->file('file'))[0];
        $count = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($data as $key => $row) {
                if ($key == 0 || empty($row[0])) continue; 

                $rawHari   = trim($row[0]);
                $rawKelas  = trim($row[1]);
                $rawMapel  = trim($row[2]);
                $rawGuru   = trim($row[3]);

                $hari = Hari::where('nama', 'like', '%' . $rawHari . '%')->first();
                $kelas = Kelas::where('kelas', $rawKelas)->where('tahun_id', $request->tahun_id)->first();
                
                // Cari Mapel Menggunakan Logika Grup
                $mapel = $this->findMapelByFlexibleName($rawMapel);
                
                $guru = Pegawai::where('nama', 'like', '%' . $rawGuru . '%')->first();
                if (!$guru) {
                    $cleanName = trim(explode(',', $rawGuru)[0]);
                    $guru = Pegawai::where('nama', 'like', '%' . $cleanName . '%')->first();
                }

                if ($hari && $kelas && $mapel && $guru) {
                    $jamMulai   = $this->formatTime($row[4]);
                    $jamSelesai = $this->formatTime($row[5]);

                    Jadwal::updateOrCreate([
                        'kelas_id'  => $kelas->id,
                        'mapel_id'  => $mapel->id,
                        'tahun_id'  => $request->tahun_id,
                        'hari_id'   => $hari->id,
                        'jam_mulai' => $jamMulai,
                    ], [
                        'pegawai_id' => $guru->id,
                        'jam_selesai'=> $jamSelesai,
                    ]);
                    $count++;
                } else {
                    $missing = [];
                    if (!$hari) $missing[] = "Hari ($rawHari)";
                    if (!$kelas) $missing[] = "Kelas ($rawKelas)";
                    if (!$mapel) $missing[] = "Mapel ($rawMapel)";
                    if (!$guru) $missing[] = "Guru ($rawGuru)";
                    $errors[] = "Baris " . ($key+1) . " gagal: " . implode(', ', $missing);
                }
            }
            DB::commit();

            $msg = "✅ Berhasil mengimpor $count jadwal pelajaran.";
            if (!empty($errors)) {
                $msg .= "<br><br><b>⚠️ Baris terlewati (Perlu periksa nama di Master Data):</b><br>" . implode('<br>', array_slice($errors, 0, 15));
            }

            return back()->with('notif', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal impor jadwal: " . $e->getMessage());
        }
    }

    /**
     * Helper Format Waktu
     */
    private function formatTime($value)
    {
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('H:i');
        }
        $cleaned = trim($value);
        if (empty($cleaned)) return '00:00';
        
        $cleaned = str_replace('.', ':', $cleaned);
        
        try {
            return date('H:i', strtotime($cleaned));
        } catch (\Exception $e) {
            return '00:00';
        }
    }

    /**
     * IMPORT LEGER (Mapping e-Rapor ke PAS SIAKAD)
     */
    public function importLegerGanjil(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'kelas_id' => 'required|exists:kelases,id'
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $tahunId = $kelas->tahun_id;
        
        // Ambil semester dari tahun terkait
        $tahunData = Tahun::find($tahunId);
        $semester = $tahunData ? $tahunData->semester : 'Ganjil';

        // Pemetaan Singkatan Leger ke Kata Kunci Pencarian Mapel
        $mapelLegerMapping = [
            'PAIDBP' => 'Agama',
            'PPDK'   => 'Pancasila',
            'BI'     => 'Indonesia',
            'MU'     => 'Matematika',
            'PJODK'  => 'PJOK',
            'SR'     => 'Seni',
            'MLBD'   => 'Sunda',
            'IPADSI' => 'IPAS',
            'BING'   => 'Inggris',
            'ING'    => 'Inggris',
        ];

        $data = Excel::toArray([], $request->file('file'))[0];
        $headers = $data[4] ?? null; 
        
        if (!$headers) return back()->with('error', 'Format file tidak sesuai template Leger.');

        $countNilai = 0;
        $biFoundCount = 0; 

        DB::beginTransaction();
        try {
            for ($i = 7; $i < count($data); $i++) {
                $row = $data[$i];
                if (empty($row[2])) continue; 

                $siswa = Siswa::where('nisn', $row[2])->first();
                if (!$siswa) continue;

                $biFoundCount = 0; 
                foreach ($headers as $index => $abbreviation) {
                    $abbreviation = strtoupper(trim($abbreviation));
                    $keyword = null;

                    // Logika Deteksi Mapel dari Singkatan Leger
                    if ($abbreviation === 'BI') {
                        $biFoundCount++;
                        $keyword = ($biFoundCount === 1) ? 'Indonesia' : 'Inggris';
                    } elseif (isset($mapelLegerMapping[$abbreviation])) {
                        $keyword = $mapelLegerMapping[$abbreviation];
                    }

                    if ($keyword) {
                        $mapelDb = $this->findMapelByFlexibleName($keyword);
                        $nilaiAngka = $row[$index];

                        if ($mapelDb && is_numeric($nilaiAngka)) {
                            // Cari guru pengampu dan ID Jadwal
                            $jadwalMapel = Jadwal::where('kelas_id', $kelas->id)
                                                ->where('mapel_id', $mapelDb->id)
                                                ->where('tahun_id', $tahunId)
                                                ->first();
                            
                            $idGuruPengampu = $jadwalMapel ? $jadwalMapel->pegawai_id : $kelas->pegawai_id;
                            
                            // =========================================================================
                            // FIX: Menangani error 'jadwal_id' mandatory
                            // Jika jadwal_id WAJIB di database tapi jadwal belum dibuat, kita ambil ID 
                            // jadwal manapun yang cocok dengan mapel tersebut di tahun yang sama.
                            // =========================================================================
                            $idJadwal = $jadwalMapel ? $jadwalMapel->id : null;
                            
                            if (!$idJadwal) {
                                $anyJadwal = Jadwal::where('mapel_id', $mapelDb->id)
                                                   ->where('tahun_id', $tahunId)
                                                   ->first();
                                $idJadwal = $anyJadwal ? $anyJadwal->id : null;
                            }

                            // Jika masih null dan database tetap menolak, maka baris ini akan dilewati 
                            // demi menjaga integritas database.
                            if (!$idJadwal) continue; 

                            $js = Jadwal_siswa::firstOrCreate([
                                'siswa_id' => $siswa->id,
                                'mapel_id' => $mapelDb->id,
                                'tahun_id' => $tahunId,
                            ], [
                                'kelas_id'   => $kelas->id,
                                'pegawai_id' => $idGuruPengampu,
                                'jadwal_id'  => $idJadwal 
                            ]);

                            Nilai::updateOrCreate([
                                'siswa_id' => $siswa->id,
                                'mapel_id' => $mapelDb->id,
                                'tahun_id' => $tahunId,
                                'jenis'    => 'PAS',
                            ], [
                                'uuid'       => (string) Str::uuid(),
                                'nilai'      => $nilaiAngka,
                                'kelas_id'   => $kelas->id,
                                'pegawai_id' => $idGuruPengampu,
                                'jadwal_id'  => $idJadwal,
                                'jadwal_siswa_id' => $js->id,
                                'semester'   => $semester
                            ]);
                            $countNilai++;
                        }
                    }
                }
            }
            DB::commit();
            return back()->with('notif', "✅ Berhasil menyinkronkan $countNilai data nilai Leger Kelas $kelas->kelas.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal impor nilai: " . $e->getMessage());
        }
    }

    /**
     * Fungsi Helper untuk mencari Mapel berdasarkan nama yang fleksibel (Grup Mapping)
     */
    private function findMapelByFlexibleName($rawName)
    {
        // 1. Coba cari langsung (Exact or Like)
        $mapel = Mapel::where('nama', 'like', '%' . $rawName . '%')->first();
        if ($mapel) return $mapel;

        // 2. Gunakan logic grup jika tidak ketemu
        $mapelGroups = [
            ['Pendidikan Agama dan Budi Pekerti', 'Pendidikan Agama Islam', 'PAI', 'PAIDBP', 'Agama'],
            ['Pendidikan Pancasila', 'PPKN', 'PKN', 'PPDK', 'Pancasila'],
            ['Bahasa Indonesia', 'B. Indonesia', 'BI'],
            ['Matematika', 'MTK', 'MU', 'Math'],
            ['IPAS', 'IPA', 'Ilmu Pengetahuan Alam dan Sosial', 'IPADSI'],
            ['Seni Rupa', 'Seni Budaya', 'SBDP', 'SR'],
            ['PJOK', 'Penjas', 'Penjaskes', 'Olahraga', 'PJODK'],
            ['Bahasa Sunda', 'B. Sunda', 'Mulok', 'MLBD', 'Sunda'],
            ['Bahasa Inggris', 'B. Inggris', 'BING', 'Inggris', 'English'],
        ];

        foreach ($mapelGroups as $group) {
            foreach ($group as $alias) {
                if (stripos($rawName, $alias) !== false) {
                    return Mapel::where(function($q) use ($group) {
                        foreach ($group as $name) {
                            $q->orWhere('nama', 'like', '%' . $name . '%');
                        }
                    })->first();
                }
            }
        }

        return null;
    }

    /**
     * IMPORT LEGER GENAP (Sama dengan Ganjil, hanya berbeda semester)
     */
    public function importLegerGenap(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'kelas_id' => 'required|exists:kelases,id'
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $tahunId = $kelas->tahun_id;
        
        // Ambil semester dari tahun terkait (pastikan semester = 'Genap')
        $tahunData = Tahun::find($tahunId);
        $semester = $tahunData ? $tahunData->semester : 'Genap';

        // Pemetaan Singkatan Leger ke Kata Kunci Pencarian Mapel
        $mapelLegerMapping = [
            'PAIDBP' => 'Agama',
            'PPDK'   => 'Pancasila',
            'BI'     => 'Indonesia',
            'MU'     => 'Matematika',
            'PJODK'  => 'PJOK',
            'SR'     => 'Seni',
            'MLBD'   => 'Sunda',
            'IPADSI' => 'IPAS',
            'BING'   => 'Inggris',
            'ING'    => 'Inggris',
        ];

        $data = Excel::toArray([], $request->file('file'))[0];
        $headers = $data[4] ?? null; 
        
        if (!$headers) return back()->with('error', 'Format file tidak sesuai template Leger.');

        $countNilai = 0;
        $biFoundCount = 0; 

        DB::beginTransaction();
        try {
            for ($i = 7; $i < count($data); $i++) {
                $row = $data[$i];
                if (empty($row[2])) continue; 

                $siswa = Siswa::where('nisn', $row[2])->first();
                if (!$siswa) continue;

                $biFoundCount = 0; 
                foreach ($headers as $index => $abbreviation) {
                    $abbreviation = strtoupper(trim($abbreviation));
                    $keyword = null;

                    // Logika Deteksi Mapel dari Singkatan Leger
                    if ($abbreviation === 'BI') {
                        $biFoundCount++;
                        $keyword = ($biFoundCount === 1) ? 'Indonesia' : 'Inggris';
                    } elseif (isset($mapelLegerMapping[$abbreviation])) {
                        $keyword = $mapelLegerMapping[$abbreviation];
                    }

                    if ($keyword) {
                        $mapelDb = $this->findMapelByFlexibleName($keyword);
                        $nilaiAngka = $row[$index];

                        if ($mapelDb && is_numeric($nilaiAngka)) {
                            // Cari guru pengampu dan ID Jadwal
                            $jadwalMapel = Jadwal::where('kelas_id', $kelas->id)
                                                ->where('mapel_id', $mapelDb->id)
                                                ->where('tahun_id', $tahunId)
                                                ->first();
                            
                            $idGuruPengampu = $jadwalMapel ? $jadwalMapel->pegawai_id : $kelas->pegawai_id;
                            
                            $idJadwal = $jadwalMapel ? $jadwalMapel->id : null;
                            
                            if (!$idJadwal) {
                                $anyJadwal = Jadwal::where('mapel_id', $mapelDb->id)
                                                   ->where('tahun_id', $tahunId)
                                                   ->first();
                                $idJadwal = $anyJadwal ? $anyJadwal->id : null;
                            }

                            if (!$idJadwal) continue; 

                            $js = Jadwal_siswa::firstOrCreate([
                                'siswa_id' => $siswa->id,
                                'mapel_id' => $mapelDb->id,
                                'tahun_id' => $tahunId,
                            ], [
                                'kelas_id'   => $kelas->id,
                                'pegawai_id' => $idGuruPengampu,
                                'jadwal_id'  => $idJadwal 
                            ]);

                            Nilai::updateOrCreate([
                                'siswa_id' => $siswa->id,
                                'mapel_id' => $mapelDb->id,
                                'tahun_id' => $tahunId,
                                'jenis'    => 'PAS',
                            ], [
                                'uuid'       => (string) Str::uuid(),
                                'nilai'      => $nilaiAngka,
                                'kelas_id'   => $kelas->id,
                                'pegawai_id' => $idGuruPengampu,
                                'jadwal_id'  => $idJadwal,
                                'jadwal_siswa_id' => $js->id,
                                'semester'   => $semester
                            ]);
                            $countNilai++;
                        }
                    }
                }
            }
            DB::commit();
            return back()->with('notif', "✅ Berhasil menyinkronkan $countNilai data nilai Leger Genap Kelas $kelas->kelas.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal impor nilai Genap: " . $e->getMessage());
        }
    }
}