<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angkatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tahun_lulus',
        'jumlah_siswa',
    ];

    public function siswas()
{
    return $this->hasMany(Siswa::class);
}

}
