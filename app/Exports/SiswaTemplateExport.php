<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle; // Tambahkan ini untuk mengubah nama tab
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class SiswaTemplateExport implements FromArray, WithHeadings, WithEvents, WithCustomStartCell, WithTitle
{
    /**
     * Mengatur nama tab sheet Excel
     */
    public function title(): string
    {
        return 'Input Siswa';
    }

    /**
     * Memberikan contoh data pada baris pertama (sebagai panduan bagi user).
     */
    public function array(): array
    {
        return [
            [
                'Ahmad Fulan',
                'L',
                'Sumedang',
                '2015-10-21',
                'Jl. Raya Pasiripis No. 10, RT 01/01',
                '2024001',
                '0151234567',
                '081234567890'
            ],
        ];
    }

    /**
     * Header kolom yang akan dibaca oleh sistem import (SiswaImport).
     * (Nama ini HARUS sama dengan key array yang dibutuhkan saat import).
     */
    public function headings(): array
    {
        return ['nama', 'jk', 'tempat', 'ttl', 'alamat', 'nis', 'nisn', 'hp'];
    }

    /**
     * Memulai tabel pada baris ke-7, menyisakan ruang untuk Kop dan Petunjuk.
     */
    public function startCell(): string
    {
        return 'A7';
    }

    /**
     * Event untuk mendesain file Excel (Styling, Lebar, Dropdown, Proteksi).
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'H';
                $lastRow = 1000; // Batas area input data

                // ==========================================
                // 1. FORMATTING KOP SURAT & INSTRUKSI
                // ==========================================

                // Judul Utama
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA PESERTA DIDIK');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2C3E50']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Sub Judul (Nama Sekolah)
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'SIAKAD - SD NEGERI PASIRIPIS');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '34495E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Petunjuk Pengisian (Box Peringatan)
                $sheet->mergeCells("A3:{$lastColumn}4");
                $instruksi = "PETUNJUK PENGISIAN:\n"
                           . "1. Dilarang mengubah nama kolom pada baris ke-7.\n"
                           . "2. Kolom 'jk' (Jenis Kelamin) WAJIB diisi dengan memilih dropdown: L, P, Laki-Laki, atau Perempuan.\n"
                           . "3. Format penulisan tanggal lahir (ttl) WAJIB menggunakan YYYY-MM-DD (Contoh: 2015-10-21).";
                $sheet->setCellValue('A3', $instruksi);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'C0392B']], // Merah gelap
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FADBD8'] // Background merah pastel
                    ],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E74C3C']]
                    ]
                ]);
                $sheet->getRowDimension('3')->setRowHeight(30);
                $sheet->getRowDimension('4')->setRowHeight(30);

                // Informasi Pengunduh
                $sheet->mergeCells("A5:{$lastColumn}5");
                $now = Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm');
                $user = Auth::check() ? Auth::user()->name : 'Administrator';
                $sheet->setCellValue('A5', "Diunduh oleh: {$user} | Waktu: {$now} WIB");
                $sheet->getStyle('A5')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '7F8C8D']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // ==========================================
                // 2. STYLING HEADER TABEL (Baris 7)
                // ==========================================
                $headerRange = "A7:{$lastColumn}7";
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2980B9'], // Biru solid korporat
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1F618D']],
                    ],
                ]);
                $sheet->getRowDimension('7')->setRowHeight(25);

                // ==========================================
                // 3. STYLING AREA DATA & CONTOH DATA
                // ==========================================
                // Beri garis border tabel hingga batas baris 100 untuk estetika
                // Warna background dan font di area ini dibiarkan default (hitam standar)
                $dataRange = "A8:{$lastColumn}100";
                $sheet->getStyle($dataRange)->applyFromArray([
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDC3C7']]],
                ]);

                // ==========================================
                // 4. PENGATURAN LEBAR KOLOM
                // ==========================================
                $widths = [
                    'A' => 35, // nama
                    'B' => 15, // jk
                    'C' => 20, // tempat
                    'D' => 18, // ttl
                    'E' => 45, // alamat
                    'F' => 18, // nis
                    'G' => 18, // nisn
                    'H' => 20, // hp
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // ==========================================
                // 5. VALIDASI DATA (DROPDOWN) KOLOM B (JK)
                // ==========================================
                $validation = new DataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP); // Block input salah
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input Ditolak');
                $validation->setError('Silakan pilih Jenis Kelamin dari daftar dropdown yang tersedia!');
                $validation->setFormula1('"L,P,Laki-Laki,Perempuan"');

                // Apply dari baris 8 sampai batas baris input
                for ($i = 8; $i <= $lastRow; $i++) {
                    $sheet->getCell("B{$i}")->setDataValidation(clone $validation);
                }

                // ==========================================
                // 6. FORMATTING TEKS KHUSUS
                // ==========================================
                // Paksa kolom Tanggal (D), NIS (F), NISN (G), HP (H) agar rata tengah
                $sheet->getStyle("B8:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D8:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F8:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Format kode untuk tanggal agar aman diconvert excel
                $sheet->getStyle("D8:D{$lastRow}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
                
                // Paksa kolom nomor terbaca sebagai String (mencegah excel hilangkan angka 0 di depan HP)
                $sheet->getStyle("F8:H{$lastRow}")->getNumberFormat()->setFormatCode('@');

                // ==========================================
                // 7. SISTEM PROTEKSI EXCEL
                // ==========================================
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('sdnpasiripis2025');
                
                // Kunci seluruh Header & Instruksi (A1 - H7)
                $sheet->getStyle("A1:{$lastColumn}7")->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                
                // Buka proteksi untuk Area Pengisian Data (A8 ke bawah) agar user bisa menginput
                $sheet->getStyle("A8:{$lastColumn}{$lastRow}")->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
            },
        ];
    }
}