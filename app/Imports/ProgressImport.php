<?php

namespace App\Imports;

use App\Models\PekerjaanItem;
use App\Models\Progress;
use App\Models\ProgressDetail;
use App\Models\MasterMinggu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;

class ProgressImport implements ToCollection
{
    protected $poId;
    protected $itemMap = []; // mapping kode pekerjaan → pekerjaan_item_id

    public function __construct($poId)
    {
        $this->poId = $poId;
    }

    public function collection(Collection $rows)
    {
        // Ambil header dulu
        $header = $rows->shift()->toArray();

        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();

            // Skip baris kosong
            if (count(array_filter($rowArray, fn($val) => !is_null($val) && $val !== '')) === 0) {
                continue;
            }

            $kodePekerjaan       = trim($row[0]  ?? '');
            $jenisPekerjaanUtama = trim($row[2]  ?? '');
            $subPekerjaan        = trim($row[3]  ?? '');
            $subSubPekerjaan     = trim($row[4]  ?? '');
            $volume              = trim($row[5]  ?? '');
            $sat                 = trim($row[6]  ?? '');
            $hargaSatuan         = trim($row[7]  ?? '');
            $jumlahHargaExcel    = trim($row[8]  ?? '');
            $bobotTotal          = trim($row[9]  ?? '');

            if ($kodePekerjaan === '') {
                continue;
            }

            // cari parent_id
            $parentId = null;
            if (str_contains($kodePekerjaan, '.')) {
                $parentKode = substr($kodePekerjaan, 0, strrpos($kodePekerjaan, '.'));
                $parentId   = $this->itemMap[$parentKode] ?? null;
            }

            // hitung jumlah_harga
            $jumlahHargaFinal = is_numeric($jumlahHargaExcel)
                ? $jumlahHargaExcel
                : ((is_numeric($volume) && is_numeric($hargaSatuan)) ? $volume * $hargaSatuan : 0);

            // insert/update ke pekerjaan_items
            $item = PekerjaanItem::updateOrCreate(
                [
                    'po_id'          => $this->poId,
                    'kode_pekerjaan' => $kodePekerjaan,
                ],
                [
                    'jenis_pekerjaan_utama' => $jenisPekerjaanUtama,
                    'sub_pekerjaan'         => $subPekerjaan,
                    'sub_sub_pekerjaan'     => $subSubPekerjaan,
                    'volume'                => is_numeric($volume) ? $volume : 0,
                    'sat'                   => $sat,
                    'harga_satuan'          => is_numeric($hargaSatuan) ? $hargaSatuan : 0,
                    'jumlah_harga'          => $jumlahHargaFinal,
                    'bobot'                 => is_numeric($bobotTotal) ? $bobotTotal : 0,
                    'parent_id'             => $parentId,
                ]
            );

            $this->itemMap[$kodePekerjaan] = $item->id;

            // buat progress untuk item ini (hindari dobel)
            $progress = Progress::firstOrCreate(
                [
                    'po_id'             => $this->poId,
                    'pekerjaan_item_id' => $item->id,
                ],
                [
                    'status' => 'draft',
                ]
            );

            // Loop mulai kolom minggu (dari index 10 ke atas di Excel baru)
            foreach ($rowArray as $colIndex => $value) {
                $headerName = $header[$colIndex] ?? null;

                // hanya proses kalau headernya "M1", "M2", dst
                if ($headerName && preg_match('/^M(\d+)$/i', $headerName, $match)) {
    $mingguKode = strtoupper($headerName); // contoh: "M1"
    $bobotMingguan = trim($value ?? '');

    // hanya simpan kalau memang ada nilai di Excel
    if ($bobotMingguan !== '' && is_numeric($bobotMingguan)) {
        $minggu = MasterMinggu::where('kode_minggu', $mingguKode)
            ->where('po_id', $this->poId)
            ->first();

        if ($minggu) {
            ProgressDetail::updateOrCreate(
                [
                    'progress_id' => $progress->id,
                    'minggu_id'   => $minggu->id,
                ],
                [
                    'bobot_rencana'   => $bobotMingguan,
                    'bobot_realisasi' => 0,
                    'keterangan'      => null,
                ]
            );
        }
    }
}

            }
        }
    }
}