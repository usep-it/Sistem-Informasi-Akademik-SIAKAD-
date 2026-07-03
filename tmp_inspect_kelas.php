<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tahun;
use App\Models\Kelas;
use App\Models\Siswa;

$tahun = Tahun::where('status','Aktif')->first();
echo 'Tahun aktif: ' . ($tahun ? $tahun->nama . ' ' . $tahun->semester : 'none') . "\n";
if ($tahun) {
    foreach (Kelas::where('tahun_id', $tahun->id)->orderBy('kelas')->orderBy('nama')->get() as $k) {
        echo $k->kelas . ' ' . $k->nama . ' - Wali: ' . ($k->pegawai ? $k->pegawai->nama : 'null') . ' - count ' . Siswa::where('kelas_id',$k->id)->count() . "\n";
    }

    $prev = Tahun::where('nama', '<>', $tahun->nama)->orderByDesc('nama')->first();
    if ($prev) {
        echo "\nPrevious year: {$prev->nama} {$prev->semester}\n";
        foreach (Kelas::where('tahun_id', $prev->id)->orderBy('kelas')->orderBy('nama')->get() as $k) {
            echo $k->kelas . ' ' . $k->nama . ' - count aktif ' . Siswa::where('kelas_id',$k->id)->where('status','Aktif')->count() . "\n";
        }
    }
}
