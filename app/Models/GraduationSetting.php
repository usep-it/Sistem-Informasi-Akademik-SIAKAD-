<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduationSetting extends Model
{
    use HasFactory;

    protected $table = 'graduation_settings';
    protected $fillable = ['waktu_buka', 'waktu_tutup', 'status', 'keterangan'];
    protected $casts = [
        'waktu_buka' => 'datetime',
        'waktu_tutup' => 'datetime',
    ];

    /**
     * Get the active graduation setting
     */
    public static function getActive()
    {
        return self::where('status', 'active')->first() ?? self::first();
    }
}
