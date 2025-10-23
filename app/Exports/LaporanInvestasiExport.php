<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanInvestasiExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithTitle,
    ShouldAutoSize
{
    protected $laporan;
    protected $rowNumber = 0;
    protected $lastCoa = '';

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function collection()
    {
        return $this->laporan->details;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN BULANAN PROGRAM PEMELIHARAAN'],
            ['PERIODE ' . strtoupper($this->laporan->nama_bulan) . ' TAHUN ' . $this->laporan->tahun],
            [],
            [
                'NO. COA',
                'Uraian Pekerjaan',
                'Volume (Satuan)',
                '1 Tahun (Rp)',
                'Target S.D Bulan Laporan',
                'Nomor Kontrak/PO/SP2',
                'Tanggal Kontrak/PO/SP2',
                'Pelaksana Kontrak/PO/SP2',
                'Realisasi Fisik (%)',
                'Realisasi Pembayaran (Rp)'
            ]
        ];
    }

    public function map($detail): array
    {
        $this->rowNumber++;
        
        return [
            $detail->coa,
            $detail->uraian_pekerjaan,
            $detail->total_volume,
            $detail->nilai_rkap,
            $detail->target_sd_bulan,
            $detail->nomor_po,
            $this->formatDate($detail->tanggal_po), // ✅ FIX: Pakai helper method
            $detail->pelaksana,
            $detail->realisasi_fisik,
            $detail->realisasi_pembayaran
        ];
    }

    /**
     * Format date helper - Handle string/Carbon/DateTime
     */
    private function formatDate($date)
    {
        if (!$date) {
            return '-';
        }

        try {
            // Jika string, parse dulu ke Carbon
            if (is_string($date)) {
                return \Carbon\Carbon::parse($date)->format('d/m/Y');
            }
            
            // Jika sudah Carbon atau DateTime object
            if ($date instanceof \Carbon\Carbon || $date instanceof \DateTime) {
                return $date->format('d/m/Y');
            }
        } catch (\Exception $e) {
            // Jika parsing gagal, return string apa adanya atau '-'
            return is_string($date) ? $date : '-';
        }

        return '-';
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells untuk header
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');

        // Style untuk header utama
        $sheet->getStyle('A1:J2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4A90E2']]
        ]);

        // Style untuk header kolom
        $sheet->getStyle('A4:J4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2E86C1']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Border untuk semua data
        $lastRow = $this->rowNumber + 4;
        $sheet->getStyle("A4:J{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Number format untuk kolom angka
        $sheet->getStyle("D5:D{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("E5:E{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("J5:J{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        return [];
    }

    public function title(): string
    {
        return 'Laporan ' . $this->laporan->nama_bulan;
    }
}