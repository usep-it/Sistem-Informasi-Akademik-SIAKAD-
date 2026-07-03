<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jadwal;
use App\Models\Tahun;
use App\Models\Informasis;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $tahunAktif = Tahun::where('status', 'Aktif')->first();

        if ($user->role === 'Dev') {
            // Statistik Master Data
            $totalUsers       = User::count();
            $pegawai          = Pegawai::count();
            $siswa            = Siswa::where('status', 'Aktif')->count();
            $totalKelasAktif  = Kelas::whereHas('tahun', fn($q) => $q->where('status', 'Aktif'))->count();
            $totalMapel       = Mapel::count();
            $siswaLaki        = Siswa::where('status', 'Aktif')->where('jk', 'L')->count();
            $siswaPerempuan   = Siswa::where('status', 'Aktif')->where('jk', 'P')->count();
            $jadwal           = Jadwal::whereHas('tahun', fn($q) => $q->where('status', 'Aktif'))->count();

            // Data untuk Chart Distribusi Siswa per Rombel
            $kelasAktif = Kelas::withCount(['siswas' => fn($q) => $q->where('status', 'Aktif')])
                ->whereHas('tahun', fn($q) => $q->where('status', 'Aktif'))
                ->orderBy('kelas')
                ->get();

            $distribusiSiswaLabels = $kelasAktif->pluck('kelas')->map(fn($k) => "Kelas $k");
            $distribusiSiswaData   = $kelasAktif->pluck('siswas_count');

            // Data untuk Grafik Tren Kunjungan (7 Hari Terakhir)
            $visitData = User::select(
                    DB::raw('DATE(last_seen_at) as date'),
                    DB::raw('count(*) as total')
                )
                ->whereNotNull('last_seen_at')
                ->where('last_seen_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            $labels = $visitData->pluck('date')->map(fn($date) => Carbon::parse($date)->translatedFormat('d M'));
            $counts = $visitData->pluck('total');

            return view('home', compact(
                'totalUsers', 'pegawai', 'siswa', 'totalKelasAktif', 'totalMapel', 
                'siswaLaki', 'siswaPerempuan', 'jadwal', 'distribusiSiswaLabels', 
                'distribusiSiswaData', 'tahunAktif', 'labels', 'counts'
            ));
        }

        // 2. Jika Role: Guru
        if ($user->role === 'Guru') {
            $pegawai = $user->pegawai;
            
            // Tentukan tipe pengguna guru berdasarkan jabatan
            $isGuru = in_array(strtolower($pegawai->jabatan ?? ''), ['guru', 'guru kelas', 'guru mapel', 'guru mata pelajaran', 'pendidik']);
            $userType = strtolower($pegawai->jabatan ?? 'guru'); // Untuk identifikasi di view

            $kelasWali = Kelas::where('pegawai_id', $pegawai->id)
                ->whereHas('tahun', fn($q) => $q->where('status', 'Aktif'))
                ->withCount(['siswas' => fn($q) => $q->where('status', 'Aktif')])
                ->first();

            $totalSiswaWali = $kelasWali?->siswas_count ?? 0;
            $inputSiswaWali = 0;
            $progressWali = 0;
            if ($kelasWali && $totalSiswaWali > 0 && $tahunAktif) {
                $inputSiswaWali = Siswa::where('kelas_id', $kelasWali->id)
                    ->where('status', 'Aktif')
                    ->whereHas('nilai', fn($query) => $query
                        ->where('tahun_id', $tahunAktif->id)
                    )
                    ->count();
                $progressWali = (int) round(($inputSiswaWali / $totalSiswaWali) * 100);
            }

            $kelasDiampuIds = Jadwal::where('pegawai_id', $pegawai->id)
                ->where('tahun_id', $tahunAktif?->id)
                ->pluck('kelas_id')
                ->unique()
                ->filter();

            $jadwalMengajarCount = Jadwal::where('pegawai_id', $pegawai->id)
                ->where('tahun_id', $tahunAktif?->id)
                ->count();

            $totalSiswaDiampu = 0;
            $inputSiswaDiampu = 0;
            $progressGuruMapel = 0;
            if ($kelasDiampuIds->isNotEmpty()) {
                $totalSiswaDiampu = Siswa::whereIn('kelas_id', $kelasDiampuIds)
                    ->where('status', 'Aktif')
                    ->count();

                if ($totalSiswaDiampu > 0) {
                    $inputSiswaDiampu = Siswa::whereIn('kelas_id', $kelasDiampuIds)
                        ->where('status', 'Aktif')
                        ->whereHas('nilai', fn($query) => $query
                            ->where('tahun_id', $tahunAktif->id)
                            ->where('pegawai_id', $pegawai->id)
                        )
                        ->count();

                    $progressGuruMapel = (int) round(($inputSiswaDiampu / $totalSiswaDiampu) * 100);
                }
            }

            return view('home', compact(
                'pegawai', 'tahunAktif', 'kelasWali', 'totalSiswaWali', 'inputSiswaWali',
                'progressWali', 'totalSiswaDiampu', 'inputSiswaDiampu', 'progressGuruMapel', 'jadwalMengajarCount',
                'isGuru', 'userType'
            ));
        }

        // 3. Jika Role: Siswa
        if ($user->role === 'Siswa') {
            $siswa = $user->siswa;
            return view('home', compact('siswa', 'tahunAktif'));
        }

        return view('home', compact('tahunAktif'));
    }

    /**
     * Halaman khusus alumni / siswa lulus.
     */
    public function alumniDashboard()
    {
        $siswa = Auth::user()->siswa;
        $tahunAktif = Tahun::where('status', 'Aktif')->first();

        return view('alumni.dashboard', compact('siswa', 'tahunAktif'));
    }

    /**
     * Endpoint API untuk Statistik Real-time (AJAX)
     */
    public function getOnlineStats()
    {
        // Anggap online jika ada aktivitas dalam 5 menit terakhir (Asia/Jakarta timezone)
        $onlineCount = User::where('last_seen_at', '>=', now('Asia/Jakarta')->subMinutes(5))->count();
        
        return response()->json([
            'online_now' => $onlineCount,
            'time' => now('Asia/Jakarta')->translatedFormat('H:i:s')
        ]);
    }

    /**
     * Fitur Publik: Cek Kelulusan
     */
    public function cekKelulusan()
    {
        return view('frontend.cek_kelulusan');
    }

    public function prosesCekKelulusan(Request $request)
{
    $request->validate([
        'nisn' => 'required|numeric',
        'ttl'  => 'required|date'
    ], [
        'nisn.required' => 'NISN wajib diisi.',
        'nisn.numeric'  => 'NISN harus berupa angka.',
        'ttl.required'  => 'Tanggal lahir wajib dipilih.',
        'ttl.date'      => 'Format tanggal lahir tidak valid.'
    ]);

    $siswa = Siswa::where('nisn', $request->nisn)
                  ->whereDate('ttl', $request->ttl)
                  ->first();

    if (!$siswa) {
        return redirect()
            ->route('cek-kelulusan')
            ->with('error', 'Data siswa tidak ditemukan.');
    }

    return redirect()
        ->route('cek-kelulusan')
        ->with('hasil_kelulusan', $siswa);
}

    /**
     * Fitur Publik: Berita & Informasi
     */
    public function berita(Request $request)
    {
        $query = Informasis::latest();
        if ($request->has('cari')) {
            $query->where('judul', 'like', '%' . $request->cari . '%');
        }
        $berita = $query->paginate(6);
        return view('frontend.berita.index', compact('berita'));
    }

    public function detailBerita($slug)
    {
        $berita = Informasis::all()->first(function ($item) use ($slug) {
            return Str::slug(trim($item->judul)) === $slug;
        });

        if (!$berita) {
            abort(404, 'Berita tidak ditemukan.');
        }

        $beritaLain = Informasis::where('id', '!=', $berita->id)->latest()->take(5)->get();
        
        return view('frontend.berita.show', compact('berita', 'beritaLain'));
    }

    /**
     * Utilitas: Optimasi Sistem & Bersihkan Cache
     */
    public function clearCache()
    {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return "<h1>✅ Sistem Berhasil Dioptimasi!</h1><p>Cache telah dibersihkan.</p><a href='/home'>Kembali ke Dashboard</a>";
    }
    
}