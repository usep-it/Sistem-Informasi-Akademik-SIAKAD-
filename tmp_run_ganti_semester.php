<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tahun;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

function printClasses($year) {
    echo "Classes for {$year->nama} {$year->semester}:\n";
    foreach (Kelas::where('tahun_id', $year->id)->orderBy('kelas')->orderBy('nama')->get() as $k) {
        echo "  {$k->kelas} {$k->nama} - wali: " . ($k->pegawai ? $k->pegawai->nama : 'null') . " - siswa: " . Siswa::where('kelas_id', $k->id)->count() . "\n";
    }
}

$tahunAktif = Tahun::where('status', 'Aktif')->first();
if (!$tahunAktif) {
    echo "Tidak ada tahun aktif\n";
    exit(1);
}
printClasses($tahunAktif);

$isGanjil = strtolower($tahunAktif->semester) === 'ganjil';
if ($isGanjil) {
    $tahunBaru = Tahun::where('nama', $tahunAktif->nama)->where('semester', 'Genap')->first();
} else {
    $tahunAwal = (int) substr($tahunAktif->nama, 0, 4);
    $namaTahunBerikutnya = ($tahunAwal + 1) . '/' . ($tahunAwal + 2);
    $tahunBaru = Tahun::where('nama', $namaTahunBerikutnya)->where('semester', 'Ganjil')->first();
}
if (!$tahunBaru) {
    echo "Target tahun belum dibuat\n";
    exit(1);
}

printClasses($tahunBaru);

echo "\nRunning gantiSemester from {$tahunAktif->nama} {$tahunAktif->semester} to {$tahunBaru->nama} {$tahunBaru->semester}\n";

DB::transaction(function() use ($tahunAktif, $tahunBaru, $isGanjil) {
    $kelasLama = Kelas::where('tahun_id', $tahunAktif->id)->get();
    $kelasBaruMap = [];
    foreach ($kelasLama as $kl) {
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
            'pegawai_id' => $kl->pegawai_id,
            'tahun_id' => $tahunBaru->id,
        ]);
        $kelasBaruMap[$tingkatBaru] = $kelasBaru;
    }

    foreach ($kelasLama as $kl) {
        if ($isGanjil) {
            $kelasTujuan = $kelasBaruMap[$kl->kelas] ?? null;
        } else {
            $kelasTujuan = $kelasBaruMap[$kl->kelas + 1] ?? null;
        }
        if (!$kelasTujuan) continue;
        Siswa::where('kelas_id', $kl->id)
            ->where('status', 'Aktif')
            ->update(['kelas_id' => $kelasTujuan->id]);
    }

    $tahunAktif->update(['status' => 'Tidak Aktif']);
    $tahunBaru->update(['status' => 'Aktif']);
});

echo "Done.\n\n";
$tahunAktif = Tahun::find($tahunAktif->id);
$tahunBaru = Tahun::find($tahunBaru->id);
printClasses($tahunAktif);
printClasses($tahunBaru);
