<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanInvestasiExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithTitle,
    ShouldAutoSize,
    WithEvents  // ✅ Tambahan untuk handle tanda tangan
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
            ['PERIODE ' . strtoupper($this->laporan->periode_label) . ' TAHUN ' . $this->laporan->tahun],
            ['Kode: ' . $this->laporan->kode_laporan],
            [],
            [
                'NO.',
                'NO. COA',
                'Uraian Pekerjaan',
                'Volume',
                'RKAP 2025 - 1 Tahun (Rp)',
                'Target S.D Bulan',
                'Nomor Kontrak/PO/SP2',
                'Tanggal Kontrak/PO/SP2',
                'Pelaksana',
                'Realisasi Fisik (%)',
                'Realisasi Pembayaran (Rp)'
            ]
        ];
    }

    public function map($detail): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $detail->coa,
            $detail->uraian_pekerjaan,
            $detail->total_volume,
            $detail->nilai_rkap,
            $detail->target_sd_bulan,
            $detail->nomor_po,
            $this->formatDate($detail->tanggal_po),
            $detail->pelaksana,
            $detail->realisasi_fisik,
            $detail->realisasi_pembayaran
        ];
    }

    /**
     * Format date helper
     */
    private function formatDate($date)
    {
        if (!$date) {
            return '-';
        }

        try {
            if (is_string($date)) {
                return \Carbon\Carbon::parse($date)->format('d/m/Y');
            }
            
            if ($date instanceof \Carbon\Carbon || $date instanceof \DateTime) {
                return $date->format('d/m/Y');
            }
        } catch (\Exception $e) {
            return is_string($date) ? $date : '-';
        }

        return '-';
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells untuk header
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');

        // Style untuk header utama
        $sheet->getStyle('A1:K3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4A90E2']]
        ]);

        // Style untuk header kolom
        $sheet->getStyle('A5:K5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2E86C1']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Border untuk semua data
        $lastRow = $this->rowNumber + 5;
        $sheet->getStyle("A5:K{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Number format untuk kolom angka
        $sheet->getStyle("E6:E{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("F6:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("K6:K{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        return [];
    }

    public function title(): string
    {
        return 'Laporan ' . date('M Y', strtotime($this->laporan->tahun . '-' . $this->laporan->bulan . '-01'));
    }

    /**
     * ✅ Register events untuk menambahkan tanda tangan
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->rowNumber + 5;
                
                // ✅ Tambah baris kosong setelah data
                $signatureStartRow = $lastRow + 3;
                
                // ✅ Tambah tanggal
                $sheet->setCellValue("A{$signatureStartRow}", 'Generated on ' . now()->format('d F Y H:i:s') . ' | Status: ' . strtoupper($this->laporan->status_approval));
                $sheet->mergeCells("A{$signatureStartRow}:K{$signatureStartRow}");
                $sheet->getStyle("A{$signatureStartRow}")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                
                $signatureStartRow += 2;
                
                // ✅ Load approvals
                $approvals = $this->laporan->approvals()->orderBy('urutan')->get();
                
                if ($approvals->isEmpty()) {
                    return;
                }
                
                // ✅ Hitung jumlah kolom per approver
                $totalApprovals = $approvals->count();
                $colsPerApprover = floor(11 / $totalApprovals); // 11 kolom (A-K)
                
                $currentCol = 0;
                $columns = range('A', 'K');
                
                foreach ($approvals as $approval) {
                    $startCol = $columns[$currentCol];
                    $endCol = $columns[min($currentCol + $colsPerApprover - 1, 10)];
                    
                    // ✅ Nama role
                    $roleRow = $signatureStartRow;
                    $sheet->setCellValue("{$startCol}{$roleRow}", strtoupper($approval->role_approval));
                    $sheet->mergeCells("{$startCol}{$roleRow}:{$endCol}{$roleRow}");
                    $sheet->getStyle("{$startCol}{$roleRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);
                    
                    // ✅ Status badge
                    $statusRow = $roleRow + 1;
                    $statusText = strtoupper($approval->status);
                    $statusColor = match($approval->status) {
                        'approved' => '28A745',
                        'rejected' => 'DC3545',
                        default => 'FFC107'
                    };
                    
                    $sheet->setCellValue("{$startCol}{$statusRow}", $statusText);
                    $sheet->mergeCells("{$startCol}{$statusRow}:{$endCol}{$statusRow}");
                    $sheet->getStyle("{$startCol}{$statusRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $statusColor]]
                    ]);
                    
                    // ✅ Tanda tangan (jika approved dan ada file)
                    if ($approval->status === 'approved' && $approval->setting && $approval->setting->ttd_file) {
                        $ttdPath = storage_path('app/public/' . $approval->setting->ttd_file);
                        
                        if (file_exists($ttdPath)) {
                            $drawing = new Drawing();
                            $drawing->setName('Tanda Tangan');
                            $drawing->setDescription('Tanda Tangan ' . $approval->nama_approver);
                            $drawing->setPath($ttdPath);
                            $drawing->setHeight(60);
                            $drawing->setCoordinates("{$startCol}" . ($statusRow + 1));
                            $drawing->setOffsetX(10);
                            $drawing->setOffsetY(5);
                            $drawing->setWorksheet($sheet);
                        }
                    }
                    
                    // ✅ Nama approver
                    $nameRow = $statusRow + 5;
                    $sheet->setCellValue("{$startCol}{$nameRow}", $approval->nama_approver);
                    $sheet->mergeCells("{$startCol}{$nameRow}:{$endCol}{$nameRow}");
                    $sheet->getStyle("{$startCol}{$nameRow}")->applyFromArray([
                        'font' => ['bold' => true, 'underline' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);
                    
                    // ✅ Jabatan
                    $titleRow = $nameRow + 1;
                    $sheet->setCellValue("{$startCol}{$titleRow}", $approval->setting->jabatan ?? '-');
                    $sheet->mergeCells("{$startCol}{$titleRow}:{$endCol}{$titleRow}");
                    $sheet->getStyle("{$startCol}{$titleRow}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);
                    
                    // ✅ Tanggal approval (jika sudah approve)
                    if ($approval->tanggal_approval) {
                        $dateRow = $titleRow + 1;
                        $sheet->setCellValue("{$startCol}{$dateRow}", 
                            'Tanggal: ' . \Carbon\Carbon::parse($approval->tanggal_approval)->format('d/m/Y H:i')
                        );
                        $sheet->mergeCells("{$startCol}{$dateRow}:{$endCol}{$dateRow}");
                        $sheet->getStyle("{$startCol}{$dateRow}")->applyFromArray([
                            'font' => ['size' => 9, 'italic' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                        ]);
                    }
                    
                    $currentCol += $colsPerApprover;
                }
            },
        ];
    }
}