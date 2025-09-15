<?php

namespace App\Imports;

use App\Models\ProgressFisik;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProgressFisikImport implements ToModel, WithHeadingRow
{
    protected $pekerjaanId;

    public function __construct($pekerjaanId)
    {
        $this->pekerjaanId = $pekerjaanId;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        $row = array_change_key_case($row, CASE_LOWER);
        Log::info('Row excel masuk:', $row);

        $bulan     = $row['bulan'] ?? null;
        $rencana   = $row['rencana'] ?? $row['rencana (%)'] ?? null;
        $realisasi = $row['realisasi'] ?? $row['realisasi (%)'] ?? null;

        // Parsing bulan
        if (is_numeric($bulan)) {
            try {
                $bulan = ExcelDate::excelToDateTimeObject($bulan)->format('Y-m-d');
            } catch (\Exception $e) {
                Log::warning('Gagal parsing Excel date: ' . $bulan, $row);
                return null;
            }
        } else {
            if (preg_match('/^\d{4}-\d{2}$/', $bulan)) {
                $bulan .= '-01';
            }
            try {
                $bulan = Carbon::parse($bulan)->format('Y-m-d');
            } catch (\Exception $e) {
                Log::warning('Gagal parse string bulan: ' . $bulan, $row);
                return null;
            }
        }

        $rencana   = $rencana !== null ? (float) str_replace('%', '', $rencana) : 0;
        $realisasi = $realisasi !== null ? (float) str_replace('%', '', $realisasi) : 0;

        if (!$bulan) {
            Log::warning('Bulan kosong, skip row', $row);
            return null;
        }

        $progress = ProgressFisik::where('pekerjaan_id', $this->pekerjaanId)
            ->where('bulan', $bulan)
            ->first();

        if ($progress) {
            $progress->update([
                'rencana'   => $rencana,
                'realisasi' => $realisasi,
                'defiasi'   => $realisasi - $rencana,
            ]);
            Log::info('Progress diperbarui untuk bulan: ' . $bulan, $row);
            return null;
        }

        Log::info('Progress baru ditambahkan untuk bulan: ' . $bulan, $row);
        return new ProgressFisik([
            'pekerjaan_id' => $this->pekerjaanId,
            'bulan'        => $bulan,
            'rencana'      => $rencana,
            'realisasi'    => $realisasi,
            'defiasi'      => $realisasi - $rencana,
        ]);
    }
}