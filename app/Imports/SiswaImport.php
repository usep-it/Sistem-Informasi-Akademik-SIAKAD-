<?php

namespace App\Imports;

use App\Models\Siswa;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SiswaImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 8; // data mulai dari baris ke-8
    }

    public function model(array $row)
{
    if (empty($row[0])) {
        return null;
    }

    // Normalisasi JK
    $jk = strtolower(trim($row[1] ?? ''));
    if (in_array($jk, ['l', 'laki-laki'])) {
        $jk = 'L';
    } elseif (in_array($jk, ['p', 'perempuan'])) {
        $jk = 'P';
    } else {
        $jk = null;
    }

    // Perbaikan konversi tanggal lahir (bisa format teks maupun angka Excel)
    $ttl = null;
    if (!empty($row[3])) {
        if (is_numeric($row[3])) {
            // Kalau berupa angka (serial Excel)
            $ttl = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[3])->format('Y-m-d');
        } else {
            // Kalau berupa teks (misal: "12 Mei 2010" atau "2010-05-12")
            $ttl = date('Y-m-d', strtotime($row[3]));
        }
    }

    return new \App\Models\Siswa([
        'uuid'      => (string) \Illuminate\Support\Str::uuid(),
        'nama'      => trim($row[0]),
        'jk'        => $jk,
        'tempat'    => trim($row[2] ?? ''),
        'ttl'       => $ttl,
        'alamat'    => trim($row[4] ?? ''),
        'nis'       => trim($row[5] ?? ''),
        'nisn'      => trim($row[6] ?? ''),
        'hp'        => trim($row[7] ?? ''),
        'status'    => 'Aktif',
    ]);
}

}
