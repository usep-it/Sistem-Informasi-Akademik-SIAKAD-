<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Siswa extends Model
{
    use Uuid, HasFactory;

    // Kolom yang bisa diisi mass assignment
    protected $guarded = [];

    /**
     * ==========================
     * 🔗 RELASI ANTAR MODEL
     * ==========================
     */

    // 1️⃣ Siswa memiliki banyak nilai
    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'siswa_id', 'id');
    }

    // 2️⃣ Siswa milik satu kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id');
    }

    // 3️⃣ Siswa memiliki satu akun user
    public function user()
    {
        return $this->hasOne(User::class, 'siswa_id', 'id');
    }

    // 4️⃣ Siswa terhubung dengan angkatan (jika ada)
    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class, 'angkatan_id', 'id');
    }
    public function keteranganKeluar()
{
    return $this->hasOne(KeteranganKeluarSiswa::class, 'siswa_id', 'id');
}


    /**
     * ==========================
     * ⚙️ EVENT OTOMATIS MODEL
     * ==========================
     */
    protected static function booted()
    {
        // 🔹 Hapus akun & nilai otomatis jika siswa dihapus
        static::deleting(function ($siswa) {
    if (in_array($siswa->status, ['Lulus', 'Mutasi', 'Berhenti'])) {
        throw new \Exception("Data siswa keluar tidak dapat dihapus secara langsung.");
    }

    try {
        if ($siswa->user) {
            $siswa->user->delete();
        }
        $siswa->nilai()->delete();
    } catch (\Exception $e) {
        \Log::error("Gagal menghapus relasi siswa: " . $e->getMessage());
    }
});


        // 🔹 Saat menyimpan data, ubah status jadi 'Aktif' jika belum lulus
        static::saving(function ($siswa) {
    // Biarkan status apapun yang dikirim dari controller
    if (!in_array($siswa->status, ['Lulus', 'Mutasi', 'Berhenti', 'Aktif'])) {
        $siswa->status = 'Aktif';
    }
});

    }

    // Di Model Siswa
public function nilaiAktif()
{
    return $this->hasMany(Nilai::class, 'siswa_id', 'id')
        ->whereHas('tahun', function($q){
            $q->where('status', 'Aktif');
        });
}

 
}
