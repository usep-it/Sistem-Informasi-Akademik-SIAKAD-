<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelases';
    protected $fillable = ['kelas', 'nama', 'pegawai_id', 'tahun_id'];

    public function jadwal_siswa()
    {
        return $this->hasMany(Jadwal_siswa::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function jadwal()
    {
        // Relasi netral tanpa filter
        return $this->hasMany(Jadwal::class, 'kelas_id');
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class, 'tahun_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }
    public static function boot()
{
    parent::boot();

    static::deleting(function ($kelas) {
        // Hapus jadwal siswa yang terhubung
        $kelas->jadwal_siswa()->delete();

        // Hapus nilai yang terkait
        $kelas->nilai()->delete();

        // Hapus jadwal yang terkait
        $kelas->jadwal()->delete();

        // Hapus siswa di kelas tersebut (kalau diinginkan)
        $kelas->siswas()->delete();
    });
}

}
