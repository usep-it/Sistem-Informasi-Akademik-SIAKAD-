<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id',
        'mapel_id',
        'pegawai_id',
        'tahun_id',
        'hari_id',
        'jam_mulai',
        'jam_selesai',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class, 'tahun_id');
    }

    public function hari()
    {
        return $this->belongsTo(Hari::class, 'hari_id');
    }

    // RELASI KE JADWAL_SISWA
    public function jadwal_siswa()
    {
        return $this->hasMany(Jadwal_siswa::class, 'jadwal_id', 'id');
    }
}
