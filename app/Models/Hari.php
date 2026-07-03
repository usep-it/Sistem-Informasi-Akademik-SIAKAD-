<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hari extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function jadwal()
    {
        // Relasi netral, tanpa filter pegawai
        return $this->hasMany(Jadwal::class, 'hari_id');
    }
}
