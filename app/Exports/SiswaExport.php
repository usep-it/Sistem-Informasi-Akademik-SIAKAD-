<?php

namespace App\Exports;

use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    private $rowNumber = 0;

   public function collection()
{
    return Siswa::with('kelas')
        ->where('status', 'Aktif') // hanya ambil siswa aktif
        ->orderBy('nama', 'asc')
        ->get();
}


    public function map($siswa): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $siswa->nama,
            $siswa->tempat,
            $siswa->ttl,
            $siswa->jk,
            $siswa->nis,
            $siswa->nisn,
            $siswa->kelas?->kelas ?? '-',
            $siswa->kelas?->nama ?? '-',
            $siswa->alamat,
            $siswa->hp,
        ];
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'NIS', 'NISN', 'Kelas', 'Fase', 'Alamat', 'No HP'
        ];
    }

    public function startCell(): string
    {
        // Memulai tabel pada baris ke-7 untuk memberi ruang bagi header
        return 'A7';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'K'; // Kolom terakhir adalah K

                // === Header Utama ===
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'DAFTAR PESERTA DIDIK');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'SD NEGERI PASIRIPIS');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->setCellValue('A3', 'Kecamatan Buahdua, Kabupaten Sumedang, Provinsi Jawa Barat');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Info Pengunduh
                $sheet->mergeCells("A4:{$lastColumn}4");
                $now = Carbon::now('Asia/Jakarta')->isoFormat('D MMMM YYYY, HH:mm');
                $user = Auth::user()->name ?? 'Administrator';
                $sheet->setCellValue('A4', "Diunduh oleh: {$user} pada {$now}");
                $sheet->getStyle('A4')->getFont()->setItalic(true)->setSize(10);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


                // === Style Header Tabel (Baris 7) ===
                $headerRange = "A7:{$lastColumn}7";
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9EAD3'], // Warna hijau muda
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                $sheet->getRowDimension('7')->setRowHeight(30);

                // === Style Data ===
                $lastRow = $sheet->getHighestRow();
                $dataRange = "A8:{$lastColumn}{$lastRow}";

                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                // Perataan teks spesifik per kolom
                $sheet->getStyle("A8:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E8:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H8:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Format kolom NIS, NISN, dan HP sebagai teks untuk mencegah masalah format angka
                $sheet->getStyle("F8:G{$lastRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                $sheet->getStyle("K8:K{$lastRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                $sheet->getStyle("F8:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                // Atur lebar kolom
                $widths = ['A' => 5, 'B' => 30, 'C' => 20, 'D' => 15, 'E' => 15, 'F' => 15, 'G' => 15, 'H' => 10, 'I' => 10, 'J' => 40, 'K' => 18];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Atur tinggi baris data
                for ($i = 8; $i <= $lastRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(20);
                }
            },
        ];
    }
}
