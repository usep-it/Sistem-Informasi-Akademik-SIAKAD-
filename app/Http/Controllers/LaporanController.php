<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\Nilai;
use App\Models\Jadwal;
use App\Models\Mapel;
use App\Models\Jadwal_siswa;

use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman utama pusat laporan akademik.
     */
    public function index()
    {
        $tahunAktif = Tahun::where('status', 'Aktif')->first();
        $kelas = Kelas::with(['tahun', 'pegawai'])->orderBy('kelas', 'asc')->get();
        $siswa = Siswa::orderBy('nama')->get();
        $tahun = Tahun::orderBy('nama', 'desc')->get();

        return view('laporan.index', compact('kelas', 'siswa', 'tahun', 'tahunAktif'));
    }

    /**
     * HALAMAN VERIFIKASI PUBLIK (Scan QR Code)
     */
    public function verifikasi(Request $request, $uuid)
    {
        $siswa = Siswa::where('uuid', $uuid)->with(['kelas.pegawai'])->firstOrFail();
        
        $tahunId = $request->query('t');
        $tahunTerpilih = Tahun::find($tahunId);

        if (!$tahunTerpilih) {
            $tahunTerpilih = Tahun::where('status', 'Aktif')->first();
        }
        
        $nilai = Nilai::where('siswa_id', $siswa->id)
                      ->where('tahun_id', $tahunTerpilih->id)
                      ->with('mapel')
                      ->get()
                      ->groupBy('mapel.nama');

        return view('laporan.verifikasi_tte', [
            'siswa' => $siswa,
            'nilai' => $nilai,
            'tahunAktif' => $tahunTerpilih
        ]);
    }

    /**
     * FUNGSI UTAMA LAPORAN (Hander untuk HTML & PDF)
     * FIX: Menambahkan method laporan agar tidak error BadMethodCallException
     */
    public function laporan(Siswa $siswa, Tahun $tahun)
    {
        // Jika ada parameter mode=pdf, arahkan ke fungsi PDF
        if (request()->query('mode') === 'pdf') {
            return $this->pdf($siswa, $tahun);
        }

        // Jika tidak, tampilkan pratinjau HTML
        $data = $this->getLaporanData($siswa, $tahun);
        
        return view('laporan.nilai', array_merge($data, [
            'siswa' => $siswa,
            'tahun' => $tahun
        ]));
    }

    /**
     * FUNGSI GENERATE PDF
     */
    public function pdfSiswa(Tahun $tahun)
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            abort(403, 'Akses ditolak.');
        }

        return $this->generatePdf($siswa, $tahun);
    }

    public function pdf(Siswa $siswa, Tahun $tahun)
    {
        return $this->generatePdf($siswa, $tahun);
    }

    /**
     * Helper untuk membuat respons PDF standar.
     */
    protected function generatePdf(Siswa $siswa, Tahun $tahun)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $data = $this->getLaporanData($siswa, $tahun);

        $pdf = Pdf::loadView('laporan.nilai', array_merge($data, [
            'siswa' => $siswa,
            'tahun' => $tahun
        ]));
        $pdf->setOptions([
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
        ]);
        $pdf->setPaper('a4', 'portrait');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Transkrip-'.$siswa->nisn.'.pdf"');
    }

    /**
     * HELPER: Mengambil data kolektif untuk laporan agar tidak redundan
     */
    private function getLaporanData($siswa, $tahun)
    {
        $urlVerifikasi = route('laporan.verifikasi', ['uuid' => $siswa->uuid, 't' => $tahun->id]);

        $qrcode = base64_encode(
            QrCode::format('svg')->size(120)->margin(0)->generate($urlVerifikasi)
        );

        $siswa->load(['nilai' => function ($query) use ($tahun) {
            $query->where('tahun_id', $tahun->id)->with(['mapel', 'kelas.pegawai']);
        }]);

        $nilaiPertama = $siswa->nilai->first();
        $kelasHistoris = ($nilaiPertama && $nilaiPertama->kelas) 
            ? $nilaiPertama->kelas 
            : (Jadwal_siswa::where('tahun_id', $tahun->id)->where('siswa_id', $siswa->id)->first()?->kelas ?? $siswa->kelas);

        $laporan = $siswa->nilai->groupBy('mapel.nama');
        $mapelIds = Jadwal::where('kelas_id', $kelasHistoris->id ?? null)->where('tahun_id', $tahun->id)->pluck('mapel_id')->unique();
        $mapels = Mapel::whereIn('id', $mapelIds)->orderBy('nama')->get();

        return [
            'laporan' => $laporan,
            'kelasHistoris' => $kelasHistoris,
            'mapels' => $mapels,
            'kkm' => 60,
            'qrcode' => $qrcode,
            'urlVerifikasi' => $urlVerifikasi
        ];
    }

    /**
     * CETAK REKAP NILAI PER KELAS
     */
    public function cetakPerKelas($kelasId, Request $request)
    {
        $tahunId = $request->query('tahun');
        if (!$tahunId) return redirect()->back()->with('error', 'Pilih Tahun Pelajaran!');

        $tahunAjaran = Tahun::findOrFail($tahunId);
        $kelasTarget = Kelas::with('pegawai')->findOrFail($kelasId);

        $mapelIds = Jadwal::where('kelas_id', $kelasId)->where('tahun_id', $tahunAjaran->id)->pluck('mapel_id')->unique();
        $mapels = Mapel::whereIn('id', $mapelIds)->orderBy('nama')->get();

        $siswaIdsRecord = Nilai::where('kelas_id', $kelasId)->where('tahun_id', $tahunAjaran->id)->pluck('siswa_id');
        $siswaIdsJadwal = Jadwal_siswa::where('kelas_id', $kelasId)->where('tahun_id', $tahunAjaran->id)->pluck('siswa_id');
        $siswaIdsAktif = Siswa::where('kelas_id', $kelasId)->pluck('id');

        $allSiswaIds = $siswaIdsRecord->merge($siswaIdsJadwal)->merge($siswaIdsAktif)->unique();

        $siswas = Siswa::whereIn('id', $allSiswaIds)
            ->with(['nilai' => function ($query) use ($tahunAjaran) {
                $query->where('tahun_id', $tahunAjaran->id);
            }])
            ->orderBy('nama')->get();

        $kelasTarget->siswas = $siswas;
        $kkm = 60;

        if (request()->query('mode') === 'pdf') {
            $pdf = Pdf::loadView('laporan.nilai-kelas', [
                'kelas' => $kelasTarget,
                'tahunAjaran' => $tahunAjaran,
                'mapels' => $mapels,
                'kkm' => $kkm
            ]);
            $pdf->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);
            $pdf->setPaper('a4', 'landscape');
            return response($pdf->output(), 200)->header('Content-Type', 'application/pdf');
        }

        return view('laporan.nilai-kelas', [
            'kelas' => $kelasTarget,
            'tahunAjaran' => $tahunAjaran,
            'mapels' => $mapels,
            'kkm' => $kkm
        ]);
    }
}