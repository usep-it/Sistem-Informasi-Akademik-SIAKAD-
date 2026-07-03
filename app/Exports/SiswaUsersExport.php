<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
// Ditambahkan: Untuk menentukan sel awal tabel
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Ditambahkan: Implementasikan WithCustomStartCell
class SiswaUsersExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithCustomStartCell
{
    private $rowNumber = 0;

    public function collection()
    {
        return User::with('siswa')
            ->where('role', 'Siswa')
            ->orderBy('username')
            ->get();
    }

    public function map($user): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,                               // No
            $user->siswa?->nama ?? '-',                     // Nama Siswa
            $user->username ?? '-',                         // Username
            $user->plain_password ?? 'Tidak tersedia',      // Password
        ];
    }

    public function headings(): array
    {
        return ['No', 'Nama Siswa', 'Username', 'Password'];
    }

    /**
     * Ditambahkan: Fungsi ini memberitahu exportir untuk memulai tabel
     * (termasuk headings) dari sel A5.
     */
    public function startCell(): string
    {
        return 'A5';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // === Header Utama ===
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'DATA AKUN SISWA');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Nama sekolah
                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('A2', 'SD NEGERI PASIRIPIS');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Tanggal export
                $sheet->mergeCells('A3:D3');
                $sheet->setCellValue('A3', 'Tanggal Export: ' . Carbon::now()->format('d-m-Y H:i'));
                $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
                // Diperbaiki: Alignment ke kanan agar lebih rapi
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Dihapus: Baris ini tidak lagi diperlukan karena kita sudah mengatur start cell
                // $sheet->insertNewRowBefore(4, 1);

                // === Style Heading Tabel (A5:D5) ===
                // Rentang sel sudah benar karena tabel dimulai dari A5
                $sheet->getStyle('A5:D5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9EAD3'], // Warna hijau muda agar lebih menarik
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // === Style Data ===
                $lastRow = $sheet->getHighestRow();
                $dataRange = 'A6:D' . $lastRow; // Data dimulai dari baris ke-6

                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                ]);

                // Rata tengah untuk kolom No, Username, dan Password
                $sheet->getStyle('A6:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C6:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // Rata kiri untuk nama
                $sheet->getStyle('B6:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);


                // Atur lebar kolom agar otomatis dan nyaman dilihat
                foreach (range('B', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getColumnDimension('A')->setWidth(5); // Atur lebar kolom 'No' secara manual

                // Atur tinggi baris untuk header dan data
                $sheet->getRowDimension('5')->setRowHeight(25); // Header lebih tinggi
                for ($i = 6; $i <= $lastRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(20);
                }
            },
        ];
    }
}
