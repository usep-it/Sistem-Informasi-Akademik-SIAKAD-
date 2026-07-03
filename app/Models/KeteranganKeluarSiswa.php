<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeteranganKeluarSiswa extends Model
{
    use HasFactory;

    protected $table = 'keterangan_keluar_siswas';

    protected $fillable = [
        'siswa_id',
        'tanggal_keluar',
        'alasan_keluar',
        'keterangan',
    ];

    // Relasi ke tabel siswas
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
