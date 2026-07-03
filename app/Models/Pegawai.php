<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Pegawai extends Model
{
    use HasFactory, Uuid;

    protected $table = 'pegawais';

    /**
     * PERBAIKAN: Menambahkan semua kolom baru ke $fillable.
     * Ini SANGAT PENTING agar data bisa disimpan.
     */
    protected $fillable = [
        'uuid',
        'nama',
        'foto', // Ditambahkan
        'jk', // Ditambahkan
        'tempat_lahir', // Ditambahkan
        'ttl', // Ditambahkan
        'nip',
        'nuptk',
        'nik',
        'email',
        'jabatan',
        'status_kepegawaian', // Ditambahkan
        'alamat', // Ditambahkan
    ];

    public function kelas()
    {
        return $this->hasMany(\App\Models\Kelas::class, 'pegawai_id');
    }

    protected static function booted()
    {
        static::deleting(function ($pegawai) {
            // Hapus jadwal yang diampu guru ini
            $pegawai->jadwal()->delete();

            // Hapus user yang terkait guru ini
            if ($pegawai->user) {
                $pegawai->user->delete();
            }
        });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'pegawai_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'pegawai_id');
    }
}
