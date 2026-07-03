<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Uuid;

class User extends Authenticatable
{
    use Uuid;
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
    'role',
    'uuid',
    'name',
    'username',
    'email',
    'password',
    'pegawai_id',
    'siswa_id',
    'plain_password',
    'last_seen_at',
    'ip_address',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Helper: Mark user as logged out dengan update atomik
     */
    public static function markAsLoggedOut($userId, $ipAddress = null)
    {
        $now = now('Asia/Jakarta')->toDateTimeString();
        
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $userId)
            ->update([
                'last_seen_at' => $now,
                'ip_address' => $ipAddress,
                'updated_at' => $now,
            ]);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    // Mutator untuk password agar otomatis di-hash
    public function setPasswordAttribute($value)
{
    // Hanya hash jika password belum di-hash
    if (!\Illuminate\Support\Str::startsWith($value, '$2y$')) {
        $value = bcrypt($value);
    }
    $this->attributes['password'] = $value;
}

public function getRouteKeyName()
{
    return 'uuid';
}

}
