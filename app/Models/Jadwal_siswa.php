<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal_siswa extends Model
{
    use HasFactory;
    protected $guarded = [];
 public function siswa()
{
    return $this->belongsTo(Siswa::class, 'siswa_id');
}

public function kelas()
{
    return $this->belongsTo(Kelas::class, 'kelas_id');
}

public function mapel()
{
    return $this->belongsTo(Mapel::class, 'mapel_id');
}

public function nilai()
{
    return $this->hasMany(Nilai::class, 'jadwal_siswa_id');
}


}
