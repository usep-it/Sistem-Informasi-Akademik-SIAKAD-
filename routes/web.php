<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\AngkatanController;
use App\Http\Controllers\KeteranganKeluarSiswaController;
use App\Http\Controllers\GraduationSettingController;
use Illuminate\Support\Facades\Artisan;

use App\Models\Informasis;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    $info = Informasis::latest()->take(5)->get();
    $kepalaSekolah = Pegawai::where('jabatan', 'Kepala Sekolah')->first();
    $guru = Pegawai::whereIn('jabatan', ['Guru Kelas', 'Guru Mapel'])->get();
    $admin = Pegawai::whereNotIn('jabatan', ['Kepala Sekolah', 'Guru Kelas', 'Guru Mapel'])->get();

    return view('welcome', compact('info', 'kepalaSekolah', 'guru', 'admin'));
});

Auth::routes();

Route::prefix('profil')->middleware('auth')->name('profil.')->group(function () {
    Route::get('/', [UserController::class, 'editProfil'])->name('edit');
    Route::post('/update', [UserController::class, 'updateProfil'])->name('update');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/admin/api/online-stats', [HomeController::class, 'getOnlineStats'])->name('api.online_stats');
});

Route::middleware(['auth', 'HakRole:Dev,Guru,Kepala Sekolah'])->group(function () {

    Route::get('/user/active-sessions', [App\Http\Controllers\UserController::class, 'activeSessions'])->name('user.online');

    // Data Master
    Route::resource('pegawai', PegawaiController::class)->except(['create', 'show']);
    Route::resource('siswa', SiswaController::class)->except(['create', 'show']);
    Route::resource('mapel', MapelController::class)->except(['create', 'show']);
    Route::resource('tahun', TahunController::class)->except(['create', 'show']);
    Route::post('/tahun/{tahun}/toggle-status', [TahunController::class, 'toggleStatus'])->name('tahun.toggleStatus');
    Route::resource('informasi', InformasiController::class)->except(['create', 'show']);
    
    // Pengaturan Pengumuman Kelulusan
    Route::resource('graduation-settings', GraduationSettingController::class)->except(['show']);
    Route::patch('/graduation-settings/{graduationSetting}/toggle-status', [GraduationSettingController::class, 'toggleStatus'])->name('graduation-settings.toggleStatus');

    // Import & Export Siswa (Pusat Data PD)
    Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('siswa/template/download', [SiswaController::class, 'downloadTemplate'])->name('siswa.template.download');
    Route::get('/siswa/export', [SiswaController::class, 'exportExcel'])->name('siswa.export');
    Route::post('/siswa/generate-akun', [SiswaController::class, 'generateAkun'])->name('siswa.generateAkun');
    Route::delete('siswa/destroy-all', [SiswaController::class, 'destroyAll'])->name('siswa.destroyAll');

    // Registrasi Keluar & Riwayat
    Route::post('/siswa/{id}/keluar', [SiswaController::class, 'keluar'])->name('siswa.keluar');
    Route::get('/pd-keluar', [KeteranganKeluarSiswaController::class, 'index'])->name('pd-keluar.index');
    Route::post('/siswa/{id}/update-skl', [SiswaController::class, 'updateSkl'])->name('siswa.updateSkl');

    // User Management (Hanya Dev biasanya, tapi mengikuti grup Anda)
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/guru', [UserController::class, 'guru'])->name('guru');
        Route::get('/siswa', [UserController::class, 'siswa'])->name('siswa');
        Route::post('/store/guru', [UserController::class, 'store_guru'])->name('store.guru');
        Route::post('/store/siswa', [UserController::class, 'store_siswa'])->name('store.siswa');
        Route::get('/edit/{user}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/reset-password/{user}', [UserController::class, 'reset_password'])->name('reset_password');
        Route::get('/export/guru', [UserController::class, 'export_guru'])->name('export_guru');
        Route::get('/export/siswa', [UserController::class, 'exportSiswa'])->name('export.siswa');
        Route::post('/{id}/toggle-role', [UserController::class, 'toggleRole'])->name('toggle_role');
        Route::post('/{id}/jadikan-admin', [UserController::class, 'jadikanAdmin'])->name('jadikanAdmin');
        Route::post('/{id}/turunkan-role', [UserController::class, 'turunkanRole'])->name('turunkanRole');
    });

Route::get('/migrasi', [NilaiController::class, 'migrationPage'])->name('admin.migrasi');

Route::post('/import-jadwal-ganjil', [NilaiController::class, 'importJadwalGanjil'])->name('jadwal.importGanjil');

Route::post('/import-leger-ganjil', [NilaiController::class, 'importLegerGanjil'])->name('nilai.importLeger');

Route::post('/import-leger-genap', [NilaiController::class, 'importLegerGenap'])->name('nilai.importLegerGenap');

});
Route::resource('informasi', InformasiController::class);

Route::middleware(['auth', 'HakRole:Dev,Guru,Kepala Sekolah'])->group(function () {

    Route::prefix('laporans')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/nilai/kelas/{kelas}', [LaporanController::class, 'cetakPerKelas'])->name('nilai-kelas');
        Route::get('/nilai/siswa/{siswa}/{tahun}', [LaporanController::class, 'laporan'])->name('nilai.siswa');
    });

});

Route::middleware(['auth', 'HakRole:Siswa', 'check.siswa.status'])->group(function () {

    Route::get('/nilai/saya', [NilaiController::class, 'siswa'])->name('nilai.saya');

    Route::get('/nilai/detail/{jadwal}', [NilaiController::class, 'nilai'])->name('nilai.detail');

    Route::get('/alumni', [HomeController::class, 'alumniDashboard'])->name('alumni.dashboard');

});

Route::middleware(['auth', 'HakRole:Dev,Guru,Kepala Sekolah,Siswa', 'check.siswa.status'])->group(function () {
    Route::get('/laporan/pdf/saya/{tahun}', [LaporanController::class, 'pdfSiswa'])->name('laporan.pdf');
});

Route::middleware(['auth', 'HakRole:Dev,Guru,Kepala Sekolah,Siswa', 'check.siswa.status'])->group(function () {

    Route::get('/rekap-guru', [PegawaiController::class, 'rekapGuru'])->name('rekap.guru');
    Route::get('/rekap-siswa', [PegawaiController::class, 'rekapSiswa'])->name('rekap.siswa');

    Route::get('/tugas-admin', function () {
        $user = Auth::user();
        $jabatan = $user->pegawai?->jabatan ?? null;

        if ($user->role == 'Dev' || ($user->role == 'Guru' && $jabatan == 'Tenaga Administrasi')) {
            return view('tenaga_administrasi.tugas');
        }
        abort(403, 'Akses Ditolak');
    })->name('tugas.admin');


    Route::resource('jadwal', JadwalController::class)->except(['create', 'show']);
    Route::get('/jadwal/kelas/{id}', [JadwalController::class, 'kelas'])->name('jadwal.kelas');
    Route::post('/kelas/ganti-semester', [KelasController::class, 'gantiSemester'])->name('kelas.gantiSemester');

    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', [KelasController::class, 'index'])->name('index');
        Route::post('/', [KelasController::class, 'store'])->name('store');
        Route::get('/{kelas}/edit', [KelasController::class, 'edit'])->name('edit');
        Route::put('/{kelas}', [KelasController::class, 'update'])->name('update');
        Route::delete('/{kelas}', [KelasController::class, 'destroy'])->name('destroy');

        Route::get('/arsip/angkatan', [KelasController::class, 'arsipAngkatan'])->name('arsip');

        Route::post('/{kelas}/naikkan-ke-kelas', [KelasController::class, 'naikkanKeKelas'])->name('naikkanKeKelas');
        Route::post('/{kelas}/luluskan-semua', [KelasController::class, 'luluskanSemua'])->name('luluskanSemua');

        Route::get('/{kelas}/manage', [KelasController::class, 'manage'])->name('manage');
        Route::post('/{kelas}/add-member', [KelasController::class, 'addMember'])->name('addMember');
        Route::delete('/remove-member/{siswa}', [KelasController::class, 'removeMember'])->name('removeMember');

        Route::post('/{kelas}/lanjut-semester', [KelasController::class, 'lanjutSemester'])->name('lanjutSemester');

        Route::post('/{kelas}/remove-all-members', [KelasController::class, 'removeAllMembers'])->name('removeAllMembers');
    });

    Route::get('/kelas-saya', [KelasController::class, 'kelasSaya'])->name('kelas.saya');


    Route::prefix('nilai')->name('nilai.')->group(function () {
        Route::get('/', [NilaiController::class, 'index'])->name('index');
        Route::get('/all/siswa/{jadwal}', [NilaiController::class, 'detail'])->name('detail');
        Route::get('/siswa/{jadwal_siswa}', [NilaiController::class, 'nilai'])->name('nilai');
        Route::post('/store', [NilaiController::class, 'store'])->name('store');
        Route::get('/{nilai:uuid}/edit', [NilaiController::class, 'edit'])->name('edit');
        Route::put('/{nilai:uuid}', [NilaiController::class, 'update'])->name('update');
        Route::delete('/{nilai}', [NilaiController::class, 'destroy'])->name('destroy');
        Route::get('/langsung/{siswa_id}', [NilaiController::class, 'nilaiLangsung'])->name('langsung');
    });


    Route::prefix('siswa-saya')->name('siswa_saya.')->group(function () {
        Route::get('/', [SiswaController::class, 'tahun'])->name('tahun');
        Route::get('/tahun/{tahun}', [SiswaController::class, 'siswa_saya'])->name('list');
        Route::get('/kelas/{kelas}', [SiswaController::class, 'siswa_saya_detail'])->name('detail');

        Route::get('/nilai/{siswa}', [SiswaController::class, 'siswa_saya_nilai'])->name('nilai');
        Route::get('/rekap/{kelas}', [SiswaController::class, 'rekap'])->name('rekap');
    });

    Route::get('/api/siswa-by-kelas/{kelas}', [SiswaController::class, 'getSiswaByKelas'])->name('api.siswaByKelas');

    Route::resource('angkatan', AngkatanController::class)->only(['index','show']);

});


Route::get('/cek-kelulusan', [App\Http\Controllers\HomeController::class, 'cekKelulusan'])->name('cek-kelulusan');
Route::post('/cek-kelulusan', [App\Http\Controllers\HomeController::class, 'prosesCekKelulusan'])->name('cek-kelulusan.proses');

Route::post('/siswa/{id}/update-skl', [App\Http\Controllers\SiswaController::class, 'updateSkl'])->name('siswa.updateSkl');

Route::get('/berita', [HomeController::class, 'berita'])->name('berita.index');
Route::get('/berita/{slug}', [HomeController::class, 'detailBerita'])->name('berita.show');
Route::get('/clear-cache', function () {
    try {
        Artisan::call('optimize:clear');
        $output = Artisan::output();
        
        return "<h1>✅ Cache Berhasil Dibersihkan!</h1><pre>" . $output . "</pre><a href='/home'>Ke Dashboard</a>";
    } catch (\Exception $e) {
        return "<h1>❌ Gagal membersihkan cache:</h1><pre>" . $e->getMessage() . "</pre>";
    }
});
Route::get('/v/{uuid}', [App\Http\Controllers\LaporanController::class, 'verifikasi'])->name('laporan.verifikasi');
Route::get('/laporan/pdf/{siswa}/{tahun}', [LaporanController::class, 'pdf']);
use Illuminate\Support\Facades\Mail;
Route::get('/sistem-down', function() {
    \Illuminate\Support\Facades\Artisan::call('down');
    return 'SIAKAD SDN Pasiripis berhasil masuk mode pemeliharaan!';
});

Route::get('/sistem-up', function() {
    \Illuminate\Support\Facades\Artisan::call('up');
    return 'SIAKAD SDN Pasiripis kembali online!';
});