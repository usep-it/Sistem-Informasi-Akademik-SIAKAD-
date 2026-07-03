<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GuruExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    private $rowNumber = 0;

    public function collection()
    {
        // Ambil data guru beserta relasi pegawai
        return User::with('pegawai')
            ->where('role', 'Guru')
            ->orderBy('pegawai_id') // Urutkan berdasarkan data pegawai
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Guru',
            'Username',
            'Password',
        ];
    }

    public function map($user): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $user->pegawai?->nama ?? '-',
            $user->username,
            $user->plain_password ?? '(tidak tersedia)',
        ];
    }

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
                $sheet->setCellValue('A1', 'DATA AKUN GURU');
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
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // === Style Heading Tabel (A5:D5) ===
                $sheet->getStyle('A5:D5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9EAD3'], // Warna hijau muda
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // === Style Data ===
                $lastRow = $sheet->getHighestRow();
                $dataRange = 'A6:D' . $lastRow;

                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                ]);

                // Perataan teks pada kolom
                $sheet->getStyle('A6:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
                $sheet->getStyle('B6:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Nama
                $sheet->getStyle('C6:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Username & Pass

                // Atur lebar kolom
                $sheet->getColumnDimension('A')->setWidth(5);
                foreach (range('B', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Atur tinggi baris
                $sheet->getRowDimension('5')->setRowHeight(25);
                for ($i = 6; $i <= $lastRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(20);
                }
            },
        ];
    }
}
